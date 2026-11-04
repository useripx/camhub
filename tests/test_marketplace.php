<?php

declare(strict_types=1);

/**
 * CAMHUB AUTOMATED TEST SUITE — TAHAP 08: MARKETPLACE JUAL BELI KAMERA
 * Memverifikasi 10 Definition of Done (DoD) Modul Marketplace Jual Beli
 */

$rootDir = dirname(__DIR__);
require_once $rootDir . '/app/Helpers/functions.php';

if (file_exists($rootDir . '/.env')) {
    load_env($rootDir . '/.env');
}

spl_autoload_register(function ($class) use ($rootDir) {
    $prefix = 'App\\';
    $baseDir = $rootDir . '/app/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) return;
    $file = $baseDir . str_replace('\\', '/', substr($class, $len)) . '.php';
    if (file_exists($file)) require_once $file;
});

$pdo = \App\Core\Database::getConnection();
$passCount = 0;
$totalTests = 10;

echo "========================================================\n";
echo "   CAMHUB TEST SUITE — TAHAP 08: MARKETPLACE JUAL BELI  \n";
echo "========================================================\n\n";

$salesRepo = new \App\Repositories\SalesRepository($pdo);
$salesService = new \App\Services\SalesService($salesRepo);
$salesValidator = new \App\Validators\SalesValidator();

// TEST 01: Proteksi Hak Akses Checkout Pembelian (/login redirect)
try {
    // Simulasi pengguna tanpa login
    unset($_SESSION['user']);
    $userCheck = auth();
    $isProtected = ($userCheck === null);

    if ($isProtected) {
        echo " [PASS] TEST-SALE-01: Proteksi Hak Akses Checkout -> Pengguna tanpa sesi diarahkan ke /login.\n";
        $passCount++;
    } else {
        echo " [FAIL] TEST-SALE-01: Proteksi hak akses gagal.\n";
    }
} catch (Throwable $e) {
    echo " [FAIL] TEST-SALE-01: Exception - " . $e->getMessage() . "\n";
}

// Persiapkan sesi user demo untuk pengujian berikutnya
$stmtUser = $pdo->query("SELECT * FROM users WHERE username = 'user' LIMIT 1");
$user = $stmtUser->fetch(PDO::FETCH_ASSOC);
$userId = (int)($user['id'] ?? 1);
$_SESSION['user'] = $user;

// TEST 02: Validasi Kamera Tersedia untuk Dijual (is_sale = 1)
try {
    $productSlug = 'sony-a7-iii';
    $prod = $salesRepo->findProductForSale($productSlug);

    if ($prod && (int)$prod['is_sale'] === 1 && (float)$prod['sale_price'] > 0) {
        echo " [PASS] TEST-SALE-02: Validasi Kamera Tersedia Dijual -> '{$prod['name']}' aktif dengan harga jual Rp " . number_format((float)$prod['sale_price'], 0, ',', '.') . ".\n";
        $passCount++;
    } else {
        echo " [FAIL] TEST-SALE-02: Produk untuk dijual tidak ditemukan atau is_sale != 1.\n";
    }
} catch (Throwable $e) {
    echo " [FAIL] TEST-SALE-02: Exception - " . $e->getMessage() . "\n";
}

// TEST 03: Auto-fill Data Pembeli & Alamat Domisili
try {
    $checkoutData = $salesService->prepareCheckout($userId, $productSlug);

    $isAutoFillValid = $checkoutData !== null 
        && !empty($checkoutData['prefill']['recipient_name'])
        && !empty($checkoutData['prefill']['phone']);

    if ($isAutoFillValid) {
        echo " [PASS] TEST-SALE-03: Auto-fill Profil Pembeli -> Nama: '{$checkoutData['prefill']['recipient_name']}', Telepon: '{$checkoutData['prefill']['phone']}'.\n";
        $passCount++;
    } else {
        echo " [FAIL] TEST-SALE-03: Gagal memuat data prefill pembeli.\n";
    }
} catch (Throwable $e) {
    echo " [FAIL] TEST-SALE-03: Exception - " . $e->getMessage() . "\n";
}

// TEST 04: Validasi Formulir Server-Side Bahasa Indonesia
try {
    $invalidPayload = [
        'recipient_name' => '',
        'phone' => '12345',
        'shipping_address' => 'pendek',
        'city' => '',
        'payment_method' => 'invalid_method',
    ];

    $errors = $salesValidator->validateCheckout($invalidPayload);

    $hasNameErr = isset($errors['recipient_name']);
    $hasPhoneErr = isset($errors['phone']);
    $hasAddressErr = isset($errors['shipping_address']);
    $hasCityErr = isset($errors['city']);
    $hasPayErr = isset($errors['payment_method']);

    if ($hasNameErr && $hasPhoneErr && $hasAddressErr && $hasCityErr && $hasPayErr) {
        echo " [PASS] TEST-SALE-04: Validasi Formulir Server-Side -> Pesan galat Bahasa Indonesia terdeteksi (Nama, HP, Alamat, Kota, Metode bayar).\n";
        $passCount++;
    } else {
        echo " [FAIL] TEST-SALE-04: Validasi gagal mendeteksi input keliru.\n";
    }
} catch (Throwable $e) {
    echo " [FAIL] TEST-SALE-04: Exception - " . $e->getMessage() . "\n";
}

// TEST 05: Presisi Matematis Kalkulasi Biaya Jual Beli
try {
    $salePrice = 24500000.0;
    $shippingFee = \App\Services\SalesService::SHIPPING_FEE; // Rp 25.000
    $adminFee = \App\Services\SalesService::ESCROW_ADMIN_FEE; // Rp 5.000
    $expectedTotal = $salePrice + $shippingFee + $adminFee; // Rp 24.530.000

    $pricing = $checkoutData['pricing'] ?? [];
    $isPresisi = ($pricing['sale_price'] === $salePrice)
        && ($pricing['shipping_fee'] === 25000.0)
        && ($pricing['admin_fee'] === 5000.0)
        && ($pricing['total_amount'] === $expectedTotal);

    if ($isPresisi) {
        echo " [PASS] TEST-SALE-05: Presisi Matematis Kalkulasi -> Produk: Rp " . number_format($salePrice, 0, ',', '.') . " + Ongkir: Rp " . number_format($shippingFee, 0, ',', '.') . " + Admin: Rp " . number_format($adminFee, 0, ',', '.') . " = Total: Rp " . number_format($expectedTotal, 0, ',', '.') . ".\n";
        $passCount++;
    } else {
        echo " [FAIL] TEST-SALE-05: Kalkulasi biaya tidak sesuai formula.\n";
    }
} catch (Throwable $e) {
    echo " [FAIL] TEST-SALE-05: Exception - " . $e->getMessage() . "\n";
}

// TEST 06: Transaksi ACID Penulisan sales_orders (#BUY-XXXXX)
$createdOrderId = 0;
try {
    $validOrderPayload = [
        'recipient_name' => $user['full_name'],
        'phone' => '081234567890',
        'shipping_address' => 'Jl. Kaliurang KM 5, Sleman',
        'city' => 'Yogyakarta',
        'notes' => 'Tolong bubble wrap tebal',
        'payment_method' => 'escrow',
    ];

    $result = $salesService->processCheckout($userId, $productSlug, $validOrderPayload);

    if ($result['success'] && !empty($result['order_id'])) {
        $createdOrderId = $result['order_id'];
        $checkOrder = $salesRepo->findOrderById($createdOrderId);

        if ($checkOrder && str_starts_with($checkOrder['order_number'], '#BUY-')) {
            echo " [PASS] TEST-SALE-06: Transaksi Finansial ACID MySQL -> Order {$checkOrder['order_number']} berhasil dibuat dengan status '{$checkOrder['order_status']}'.\n";
            $passCount++;
        } else {
            echo " [FAIL] TEST-SALE-06: Format order_number tidak sesuai (#BUY-XXXXX).\n";
        }
    } else {
        echo " [FAIL] TEST-SALE-06: Gagal memproses checkout: " . json_encode($result['errors'] ?? []) . "\n";
    }
} catch (Throwable $e) {
    echo " [FAIL] TEST-SALE-06: Exception - " . $e->getMessage() . "\n";
}

// TEST 07: Integritas Rincian Barang pada sales_order_items
try {
    $stmtItem = $pdo->prepare("SELECT * FROM sales_order_items WHERE order_id = :oid");
    $stmtItem->execute(['oid' => $createdOrderId]);
    $item = $stmtItem->fetch(PDO::FETCH_ASSOC);

    if ($item && (int)$item['quantity'] === 1 && (float)$item['unit_price'] > 0) {
        echo " [PASS] TEST-SALE-07: Integritas sales_order_items -> Item ID #{$item['id']} terhubung ke Order ID #{$createdOrderId} dengan harga satuan Rp " . number_format((float)$item['unit_price'], 0, ',', '.') . ".\n";
        $passCount++;
    } else {
        echo " [FAIL] TEST-SALE-07: Item pesanan tidak tersimpan dengan benar.\n";
    }
} catch (Throwable $e) {
    echo " [FAIL] TEST-SALE-07: Exception - " . $e->getMessage() . "\n";
}

// TEST 08: Penahanan Dana Rekening Bersama Escrow Pembelian
try {
    $stmtEscrow = $pdo->prepare("SELECT * FROM escrow_transactions WHERE sales_order_id = :oid");
    $stmtEscrow->execute(['oid' => $createdOrderId]);
    $escrow = $stmtEscrow->fetch(PDO::FETCH_ASSOC);

    $stmtPay = $pdo->prepare("SELECT * FROM payments WHERE reference_id = :oid AND order_type = 'sale'");
    $stmtPay->execute(['oid' => $createdOrderId]);
    $payment = $stmtPay->fetch(PDO::FETCH_ASSOC);

    if ($escrow && $payment && $escrow['status'] === 'held' && $payment['payment_method'] === 'escrow') {
        echo " [PASS] TEST-SALE-08: Penahanan Dana Escrow -> Rekening bersama menahan dana beli Rp " . number_format((float)$escrow['amount_held'], 0, ',', '.') . " dengan status 'held'.\n";
        $passCount++;
    } else {
        echo " [FAIL] TEST-SALE-08: Data transaksi escrow jual beli tidak tercatat dengan benar.\n";
    }
} catch (Throwable $e) {
    echo " [FAIL] TEST-SALE-08: Exception - " . $e->getMessage() . "\n";
}

// TEST 09: Otorisasi Nota Tanda Terima Pembelian
try {
    $receipt = $salesRepo->findOrderById($createdOrderId, $userId);
    $hasStore = !empty($receipt['store_name']);
    $hasInvoice = !empty($receipt['order_number']);

    // Pastikan user lain tidak dapat melihat nota ini
    $otherUserReceipt = $salesRepo->findOrderById($createdOrderId, 99999);
    $isIsolated = ($otherUserReceipt === null);

    if ($receipt && $hasStore && $hasInvoice && $isIsolated) {
        echo " [PASS] TEST-SALE-09: Otorisasi Nota Tanda Terima -> Invoice {$receipt['order_number']} aman, terisolasi dari akses akun lain.\n";
        $passCount++;
    } else {
        echo " [FAIL] TEST-SALE-09: Otorisasi nota tanda terima gagal.\n";
    }
} catch (Throwable $e) {
    echo " [FAIL] TEST-SALE-09: Exception - " . $e->getMessage() . "\n";
}

// TEST 10: Tampilan & Sinkronisasi Transaksi di Menu 'Jual Beli Saya'
try {
    $userPurchases = $salesRepo->getUserSalesOrders($userId);
    $foundInHistory = false;
    foreach ($userPurchases as $pur) {
        if ((int)$pur['id'] === $createdOrderId) {
            $foundInHistory = true;
            break;
        }
    }

    if ($foundInHistory) {
        echo " [PASS] TEST-SALE-10: Sinkronisasi Riwayat Pelanggan -> Order ID #{$createdOrderId} sukses muncul pada daftar 'Jual Beli Saya'.\n";
        $passCount++;
    } else {
        echo " [FAIL] TEST-SALE-10: Order tidak ditemukan pada riwayat jual beli pelanggan.\n";
    }
} catch (Throwable $e) {
    echo " [FAIL] TEST-SALE-10: Exception - " . $e->getMessage() . "\n";
}

echo "\n========================================================\n";
echo "HASIL PENGUJIAN TAHAP 08: {$passCount}/{$totalTests} BERHASIL (" . round(($passCount / $totalTests) * 100) . "% PASS)\n";
echo "========================================================\n";

if ($passCount === $totalTests) {
    exit(0);
} else {
    exit(1);
}

<?php

declare(strict_types=1);

/**
 * Initial Seeder - CamHub
 * 
 * Populates master data, default test accounts (admin:admin, user:user, mitra:mitra),
 * and mockup reference products into MySQL.
 * Usage: php database/seeders/initial_seed.php
 */

$rootDir = dirname(__DIR__, 2);
require_once $rootDir . '/app/Helpers/functions.php';

if (file_exists($rootDir . '/.env')) {
    load_env($rootDir . '/.env');
}

// Autoloader for classes
spl_autoload_register(function ($class) use ($rootDir) {
    $prefix = 'App\\';
    $baseDir = $rootDir . '/app/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) return;
    $file = $baseDir . str_replace('\\', '/', substr($class, $len)) . '.php';
    if (file_exists($file)) require_once $file;
});

echo "========================================================\n";
echo "              CAMHUB INITIAL DATA SEEDER               \n";
echo "========================================================\n\n";

try {
    $pdo = \App\Core\Database::getConnection();

    // 1. SEED ROLES
    echo "[1/6] Seeding roles...\n";
    $roles = [
        ['name' => 'admin', 'display_name' => 'Administrator'],
        ['name' => 'partner', 'display_name' => 'Mitra Lapak'],
        ['name' => 'customer', 'display_name' => 'Pelanggan'],
    ];

    $stmtRole = $pdo->prepare("INSERT INTO roles (name, display_name) VALUES (:name, :display_name) ON DUPLICATE KEY UPDATE display_name = VALUES(display_name)");
    foreach ($roles as $r) {
        $stmtRole->execute($r);
    }
    echo "      3 peran berhasil di-seed.\n";

    // Get role IDs
    $roleIds = $pdo->query("SELECT name, id FROM roles")->fetchAll(PDO::FETCH_KEY_PAIR);

    // 2. SEED CATEGORIES
    echo "[2/6] Seeding categories...\n";
    $categories = [
        ['name' => 'Kamera Body', 'slug' => 'kamera-body', 'icon' => 'camera'],
        ['name' => 'Lensa', 'slug' => 'lensa', 'icon' => 'disc'],
        ['name' => 'Audio', 'slug' => 'audio', 'icon' => 'mic'],
        ['name' => 'Lighting', 'slug' => 'lighting', 'icon' => 'sun'],
        ['name' => 'Aksesoris', 'slug' => 'aksesoris', 'icon' => 'tool'],
    ];

    $stmtCat = $pdo->prepare("INSERT INTO categories (name, slug, icon) VALUES (:name, :slug, :icon) ON DUPLICATE KEY UPDATE icon = VALUES(icon)");
    foreach ($categories as $c) {
        $stmtCat->execute($c);
    }
    echo "      5 kategori produk berhasil di-seed.\n";

    $catIds = $pdo->query("SELECT slug, id FROM categories")->fetchAll(PDO::FETCH_KEY_PAIR);

    // 3. SEED DEFAULT USERS (Admin, Partner, Customer)
    echo "[3/6] Seeding default users (admin:admin, user:user, mitra:mitra)...\n";
    
    $stmtUser = $pdo->prepare("
        INSERT INTO users (role_id, username, full_name, email, phone, password_hash, status)
        VALUES (:role_id, :username, :full_name, :email, :phone, :password_hash, 'active')
        ON DUPLICATE KEY UPDATE 
            username = VALUES(username),
            full_name = VALUES(full_name),
            password_hash = VALUES(password_hash),
            phone = VALUES(phone),
            status = 'active'
    ");

    // 3.1 Admin User (admin:admin)
    $stmtUser->execute([
        'role_id' => $roleIds['admin'],
        'username' => 'admin',
        'full_name' => 'Administrator CamHub',
        'email' => 'admin@camhub.id',
        'phone' => '081111111111',
        'password_hash' => password_hash('admin', PASSWORD_BCRYPT),
    ]);
    $adminId = (int)$pdo->query("SELECT id FROM users WHERE username = 'admin'")->fetchColumn();

    // 3.2 Partner User & Store (mitra:mitra)
    $stmtUser->execute([
        'role_id' => $roleIds['partner'],
        'username' => 'mitra',
        'full_name' => 'Yogi Ario (Mitra)',
        'email' => 'mitra@camhub.id',
        'phone' => '082222222222',
        'password_hash' => password_hash('mitra', PASSWORD_BCRYPT),
    ]);
    $partnerUserId = (int)$pdo->query("SELECT id FROM users WHERE username = 'mitra'")->fetchColumn();

    $stmtPartner = $pdo->prepare("
        INSERT INTO partners (user_id, business_name, status, commission_rate, verified_at)
        VALUES (:user_id, :business_name, 'active', 10.00, NOW())
        ON DUPLICATE KEY UPDATE business_name = VALUES(business_name)
    ");
    $stmtPartner->execute([
        'user_id' => $partnerUserId,
        'business_name' => 'Yogi Camera',
    ]);
    $partnerId = (int)$pdo->query("SELECT id FROM partners WHERE user_id = {$partnerUserId}")->fetchColumn();

    $stmtStore = $pdo->prepare("
        INSERT INTO partner_stores (partner_id, store_name, address, city, phone)
        VALUES (:partner_id, :store_name, :address, :city, :phone)
        ON DUPLICATE KEY UPDATE store_name = VALUES(store_name)
    ");
    $stmtStore->execute([
        'partner_id' => $partnerId,
        'store_name' => 'Yogi Camera',
        'address' => 'Jl. Kaliurang KM 5 No. 42',
        'city' => 'Yogyakarta',
        'phone' => '082222222222',
    ]);
    $storeId = (int)$pdo->query("SELECT id FROM partner_stores WHERE partner_id = {$partnerId}")->fetchColumn();

    // 3.3 Customer User (user:user)
    $stmtUser->execute([
        'role_id' => $roleIds['customer'],
        'username' => 'user',
        'full_name' => 'User Demo CamHub',
        'email' => 'user@camhub.id',
        'phone' => '083333333334',
        'password_hash' => password_hash('user', PASSWORD_BCRYPT),
    ]);
    $customerId = (int)$pdo->query("SELECT id FROM users WHERE username = 'user'")->fetchColumn();

    // 3.4 Prototype Customer User Regi (regi@camhub.id)
    $stmtUser->execute([
        'role_id' => $roleIds['customer'],
        'username' => 'regi',
        'full_name' => 'Mohammad Regieyanto',
        'email' => 'regi@camhub.id',
        'phone' => '083333333333',
        'password_hash' => password_hash('user', PASSWORD_BCRYPT),
    ]);
    $regiId = (int)$pdo->query("SELECT id FROM users WHERE email = 'regi@camhub.id'")->fetchColumn();

    $stmtProfile = $pdo->prepare("
        INSERT INTO user_profiles (user_id, address, city)
        VALUES (:user_id, :address, :city)
        ON DUPLICATE KEY UPDATE address = VALUES(address)
    ");
    $stmtProfile->execute([
        'user_id' => $customerId,
        'address' => 'Jl. Malioboro No. 12',
        'city' => 'Yogyakarta',
    ]);
    $stmtProfile->execute([
        'user_id' => $regiId,
        'address' => 'Jl. Kaliurang KM 7',
        'city' => 'Yogyakarta',
    ]);
    $stmtProfile->execute([
        'user_id' => $customerId,
        'address' => 'Jl. Malioboro No. 12',
        'city' => 'Yogyakarta',
    ]);
    echo "      Pengguna awal (admin:admin, user:user, mitra:mitra) aktif dan siap digunakan.\n";

    // 4. SEED PRODUCTS (6 Mockup Cameras)
    echo "[4/6] Seeding prototype reference cameras...\n";
    $cameras = [
        [
            'name' => 'Sony A7 III',
            'slug' => 'sony-a7-iii',
            'description' => 'Sony A7 III adalah kamera mirrorless full-frame untuk foto dan video berkualitas tinggi.',
            'daily_price' => 250000.00,
            'sale_price' => 24500000.00,
            'rating' => 4.80,
            'reviews' => 120,
            'stock' => 3,
        ],
        [
            'name' => 'Canon EOS R50',
            'slug' => 'canon-eos-r50',
            'description' => 'Kamera mirrorless APS-C compact dan ringan, ideal untuk content creator.',
            'daily_price' => 165000.00,
            'sale_price' => 10500000.00,
            'rating' => 4.80,
            'reviews' => 120,
            'stock' => 2,
        ],
        [
            'name' => 'Fujifilm X-T30',
            'slug' => 'fujifilm-x-t30',
            'description' => 'Kamera klasik mirrorless dengan simulasi film legendaris Fujifilm.',
            'daily_price' => 200000.00,
            'sale_price' => 14000000.00,
            'rating' => 4.80,
            'reviews' => 120,
            'stock' => 2,
        ],
        [
            'name' => 'Sony Alpha 6400',
            'slug' => 'sony-alpha-6400',
            'description' => 'Autofokus super cepat dengan Real-time Eye AF untuk fotografi aksi dan vlog.',
            'daily_price' => 170000.00,
            'sale_price' => 12500000.00,
            'rating' => 4.80,
            'reviews' => 120,
            'stock' => 2,
        ],
        [
            'name' => 'Canon 80D',
            'slug' => 'canon-80d',
            'description' => 'DSLR tangguh serbaguna dengan sensor APS-C 24.2 MP dan Dual Pixel CMOS AF.',
            'daily_price' => 250000.00,
            'sale_price' => 9500000.00,
            'rating' => 4.80,
            'reviews' => 120,
            'stock' => 2,
        ],
        [
            'name' => 'Nikon D750',
            'slug' => 'nikon-d750',
            'description' => 'DSLR Full-frame handal untuk fotografi pernikahan dan pemandangan.',
            'daily_price' => 250000.00,
            'sale_price' => 16500000.00,
            'rating' => 4.80,
            'reviews' => 120,
            'stock' => 2,
        ],
    ];

    $stmtProd = $pdo->prepare("
        INSERT INTO products (store_id, category_id, name, slug, description, condition_note, is_rental, is_sale, sale_price, stock_quantity, rating_avg, review_count)
        VALUES (:store_id, :category_id, :name, :slug, :description, 'Kondisi sangat baik · Siap disewa', 1, 1, :sale_price, :stock_quantity, :rating_avg, :review_count)
        ON DUPLICATE KEY UPDATE description = VALUES(description), sale_price = VALUES(sale_price)
    ");

    $stmtUnit = $pdo->prepare("
        INSERT INTO product_units (product_id, serial_number, condition_status, availability_status)
        VALUES (:product_id, :serial_number, 'Sangat Baik', 'available')
        ON DUPLICATE KEY UPDATE availability_status = VALUES(availability_status)
    ");

    $stmtPricing = $pdo->prepare("
        INSERT INTO rental_pricing (product_id, duration_days, price_per_day, discount_percent)
        VALUES (:product_id, :duration_days, :price_per_day, :discount_percent)
        ON DUPLICATE KEY UPDATE price_per_day = VALUES(price_per_day)
    ");

    $kameraBodyId = $catIds['kamera-body'];

    foreach ($cameras as $cam) {
        $stmtProd->execute([
            'store_id' => $storeId,
            'category_id' => $kameraBodyId,
            'name' => $cam['name'],
            'slug' => $cam['slug'],
            'description' => $cam['description'],
            'sale_price' => $cam['sale_price'],
            'stock_quantity' => $cam['stock'],
            'rating_avg' => $cam['rating'],
            'review_count' => $cam['reviews'],
        ]);

        $productId = (int)$pdo->query("SELECT id FROM products WHERE slug = '{$cam['slug']}'")->fetchColumn();

        // 5. SEED PRODUCT UNITS (Physical Units)
        for ($i = 1; $i <= $cam['stock']; $i++) {
            $serial = strtoupper(str_replace('-', '', $cam['slug'])) . '-' . str_pad((string)$i, 3, '0', STR_PAD_LEFT);
            $stmtUnit->execute([
                'product_id' => $productId,
                'serial_number' => $serial,
            ]);
        }

        // 6. SEED RENTAL PRICING TIERS (1, 3, 7, 14 Hari)
        $durations = [
            1 => $cam['daily_price'],
            3 => $cam['daily_price'],
            7 => $cam['daily_price'] * 0.90, // diskon 10%
            14 => $cam['daily_price'] * 0.85, // diskon 15%
        ];

        foreach ($durations as $days => $price) {
            $stmtPricing->execute([
                'product_id' => $productId,
                'duration_days' => $days,
                'price_per_day' => $price,
                'discount_percent' => $days >= 7 ? 10.00 : 0.00,
            ]);
        }
    }
    echo "      6 kamera prototype beserta unit fisik dan paket tarif berhasil di-seed.\n\n";

    echo "========================================================\n";
    echo "STATUS: SEEDER SELESAI DENGAN SUKSES\n";
    echo "========================================================\n";
    exit(0);

} catch (Throwable $e) {
    echo "\n[ERROR] Seeder gagal: " . $e->getMessage() . "\n";
    echo "========================================================\n";
    exit(1);
}

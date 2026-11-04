<?php
$activeType = $activeType ?? ($_GET['type'] ?? 'all');
$bookings = $bookings ?? [];
$purchases = $purchases ?? [];

// Jika belum ada purchases dari controller, ambil dari SalesRepository
if (empty($purchases) && ($activeType === 'sale' || $activeType === 'all')) {
    $salesRepo = new \App\Repositories\SalesRepository();
    $userId = (int)(auth()['id'] ?? 0);
    if ($userId > 0) {
        $purchases = $salesRepo->getUserSalesOrders($userId);
    }
}

// Jika belum ada bookings dari controller, ambil dari BookingRepository
if (empty($bookings) && ($activeType === 'rental' || $activeType === 'all')) {
    $bookingRepo = new \App\Repositories\BookingRepository();
    $userId = (int)(auth()['id'] ?? 0);
    if ($userId > 0) {
        $bookings = $bookingRepo->getUserBookings($userId);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Saya — CamHub</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; font-family:'Plus Jakarta Sans', sans-serif; }
        body { background:#F8FAFC; color:#0F172A; min-height:100vh; padding-bottom:60px; }
        
        .navbar { background:#FFFFFF; padding:20px 48px; border-bottom:1px solid #E2E8F0; display:flex; justify-content:space-between; align-items:center; }
        .brand-logo { font-size:24px; font-weight:800; color:#0F172A; text-decoration:none; }
        .nav-links { display:flex; gap:20px; align-items:center; }
        .nav-links a { text-decoration:none; color:#64748B; font-size:14px; font-weight:600; transition:0.2s; }
        .nav-links a:hover { color:#2563EB; }

        .main-container { max-width:1040px; margin:40px auto; padding:0 24px; }
        .page-header { margin-bottom:24px; }
        .page-title { font-size:30px; font-weight:800; color:#0F172A; letter-spacing:-0.5px; }

        /* TABS FILTER */
        .tabs-row { display:flex; gap:12px; margin-bottom:28px; border-bottom:1px solid #E2E8F0; padding-bottom:12px; }
        .tab-btn {
            text-decoration:none;
            padding:10px 18px;
            border-radius:10px;
            font-size:14px;
            font-weight:700;
            color:#64748B;
            background:transparent;
            transition:all 0.15s ease;
        }
        .tab-btn:hover { background:#F1F5F9; color:#0F172A; }
        .tab-btn.active { background:#EFF6FF; color:#2563EB; border:1px solid #BFDBFE; }

        .orders-list { display:flex; flex-direction:column; gap:16px; }
        .order-card { background:#FFFFFF; border:1px solid #E2E8F0; border-radius:16px; padding:24px; display:flex; justify-content:space-between; align-items:center; box-shadow:0 1px 3px rgba(0,0,0,0.02); transition:all 0.15s; }
        .order-card:hover { border-color:#CBD5E1; box-shadow:0 4px 12px rgba(0,0,0,0.04); }
        .order-left { display:flex; gap:20px; align-items:center; }
        .order-thumb { width:72px; height:72px; background:#F8FAFC; border:1px solid #E2E8F0; border-radius:12px; display:flex; align-items:center; justify-content:center; overflow:hidden; font-size:28px; flex-shrink:0; }
        .order-thumb img { width:100%; height:100%; object-fit:cover; }
        .order-details strong { font-size:16px; color:#0F172A; display:block; margin-bottom:4px; font-weight:800; }
        .order-meta { font-size:13px; color:#64748B; margin-bottom:4px; }
        
        .order-right { text-align:right; display:flex; flex-direction:column; align-items:flex-end; gap:8px; }
        .order-price { font-size:18px; font-weight:800; color:#0F172A; }
        
        .badge { display:inline-block; padding:4px 12px; border-radius:20px; font-size:12px; font-weight:700; text-transform:uppercase; }
        .badge-pending { background:#FEF3C7; color:#D97706; }
        .badge-confirmed { background:#E0E7FF; color:#4338CA; }
        .badge-active { background:#DCFCE7; color:#15803D; }
        .badge-shipped { background:#DBEAFE; color:#1D4ED8; }
        .badge-delivered { background:#DCFCE7; color:#15803D; }
        .badge-returned { background:#F1F5F9; color:#475569; }

        .empty-box { background:#FFFFFF; border:2px dashed #CBD5E1; border-radius:16px; padding:60px 20px; text-align:center; color:#64748B; }
        .btn-action-browse { background:#2563EB; color:#fff; text-decoration:none; padding:12px 24px; border-radius:10px; font-weight:700; display:inline-block; margin-top:16px; }

        @media (max-width: 768px) {
            .order-card { flex-direction:column; align-items:flex-start; gap:16px; }
            .order-right { align-items:flex-start; text-align:left; }
            .navbar { padding:16px 20px; }
        }
    </style>
</head>
<body>

    <header class="navbar">
        <a href="<?= base_url('/') ?>" class="brand-logo">CamHub</a>
        <div class="nav-links">
            <a href="<?= base_url('/katalog') ?>">Etalase Katalog</a>
            <a href="<?= base_url('/dashboard/pelanggan') ?>">Dashboard Pelanggan</a>
            <a href="<?= base_url('/profil') ?>">Pengaturan</a>
        </div>
    </header>

    <main class="main-container">
        <div class="page-header">
            <h1 class="page-title">Riwayat Pesanan Saya</h1>
            <p style="color:#64748B; font-size:14px; margin-top:4px;">Pantau jadwal sewa, status pembelian kamera, dan perlindungan dana Escrow Anda.</p>
        </div>

        <!-- TABS NAVIGASI -->
        <div class="tabs-row">
            <a href="<?= base_url('/pesanan-saya') ?>" class="tab-btn <?= $activeType === 'all' ? 'active' : '' ?>">Semua Transaksi</a>
            <a href="<?= base_url('/pesanan-saya?type=rental') ?>" class="tab-btn <?= $activeType === 'rental' ? 'active' : '' ?>">📷 Sewa Saya</a>
            <a href="<?= base_url('/pesanan-saya?type=sale') ?>" class="tab-btn <?= $activeType === 'sale' ? 'active' : '' ?>">🛒 Jual Beli Saya</a>
        </div>

        <div class="orders-list">
            <?php 
                $hasItems = false;
            ?>

            <!-- DAFTAR PEMBELIAN (JUAL BELI) -->
            <?php if (($activeType === 'sale' || $activeType === 'all') && !empty($purchases)): ?>
                <?php foreach ($purchases as $p): $hasItems = true; ?>
                    <div class="order-card">
                        <div class="order-left">
                            <div class="order-thumb">
                                <?php if (!empty($p['product_image'])): ?>
                                    <img src="<?= base_url('/' . ltrim($p['product_image'], '/')) ?>" alt="<?= htmlspecialchars($p['product_name'] ?? '') ?>">
                                <?php else: ?>
                                    📷
                                <?php endif; ?>
                            </div>
                            <div class="order-details">
                                <span style="font-size:12px; font-weight:700; color:#10B981; font-family:monospace;">
                                    <?= htmlspecialchars($p['order_number'] ?? '#BUY-00000') ?> · PEMBELIAN
                                </span>
                                <strong><?= htmlspecialchars($p['product_name'] ?? 'Kamera') ?></strong>
                                <div class="order-meta">
                                    Toko: <?= htmlspecialchars($p['store_name'] ?? '-') ?> (<?= htmlspecialchars($p['store_city'] ?? '-') ?>)
                                </div>
                                <div class="order-meta">
                                    Alamat Kirim: <?= htmlspecialchars($p['shipping_address'] ?? '-') ?>
                                </div>
                            </div>
                        </div>

                        <div class="order-right">
                            <div class="order-price">Rp <?= number_format((float)$p['total_amount'], 0, ',', '.') ?></div>
                            <span class="badge badge-<?= $p['order_status'] ?? 'pending' ?>">
                                <?= ucfirst($p['order_status'] ?? 'Pending') ?>
                            </span>
                            <a href="<?= base_url('/pesanan/beli/' . $p['id'] . '/sukses') ?>" style="font-size:13px; font-weight:600; color:#2563EB; text-decoration:none;">
                                Lihat Invoice & Status Escrow →
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <!-- DAFTAR PENYEWAAN -->
            <?php if (($activeType === 'rental' || $activeType === 'all') && !empty($bookings)): ?>
                <?php foreach ($bookings as $b): $hasItems = true; ?>
                    <div class="order-card">
                        <div class="order-left">
                            <div class="order-thumb">
                                <?php if (!empty($b['product_image'])): ?>
                                    <img src="<?= base_url('/' . ltrim($b['product_image'], '/')) ?>" alt="<?= htmlspecialchars($b['product_name'] ?? '') ?>">
                                <?php else: ?>
                                    📷
                                <?php endif; ?>
                            </div>
                            <div class="order-details">
                                <span style="font-size:12px; font-weight:700; color:#2563EB; font-family:monospace;">
                                    #RENT-<?= str_pad((string)$b['id'], 5, '0', STR_PAD_LEFT) ?> · SEWA
                                </span>
                                <strong><?= htmlspecialchars($b['product_name']) ?></strong>
                                <div class="order-meta">
                                    Toko: <?= htmlspecialchars($b['store_name']) ?> (<?= htmlspecialchars($b['store_city']) ?>) · SN: <?= htmlspecialchars($b['serial_number']) ?>
                                </div>
                                <div class="order-meta">
                                    Periode: <?= date('d M Y', strtotime($b['start_date'])) ?> s.d <?= date('d M Y', strtotime($b['end_date'])) ?> (<?= (int)$b['total_days'] ?> Hari)
                                </div>
                            </div>
                        </div>

                        <div class="order-right">
                            <div class="order-price">Rp <?= number_format((float)$b['total_amount'], 0, ',', '.') ?></div>
                            <span class="badge badge-<?= $b['rental_status'] ?>">
                                <?= ucfirst($b['rental_status']) ?>
                            </span>
                            <a href="<?= base_url('/booking/' . $b['id'] . '/sukses') ?>" style="font-size:13px; font-weight:600; color:#2563EB; text-decoration:none;">
                                Lihat Nota & Panduan Escrow →
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <?php if (!$hasItems): ?>
                <div class="empty-box">
                    <p style="font-size:18px; font-weight:700; color:#0F172A; margin-bottom:6px;">Belum Ada Transaksi</p>
                    <p>Temukan kamera impian untuk disewa atau dibeli langsung dengan proteksi rekening bersama Escrow.</p>
                    <a href="<?= base_url('/katalog') ?>" class="btn-action-browse">Jelajahi Katalog Kamera</a>
                </div>
            <?php endif; ?>
        </div>
    </main>

</body>
</html>

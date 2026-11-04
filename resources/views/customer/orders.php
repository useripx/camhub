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
        body { background:#F8FAFC; color:#0F172A; min-height:100vh; }
        .navbar { background:#FFFFFF; padding:20px 48px; border-bottom:1px solid #E2E8F0; display:flex; justify-content:space-between; align-items:center; }
        .brand-logo { font-size:24px; font-weight:800; color:#0F172A; text-decoration:none; }
        .nav-links { display:flex; gap:20px; align-items:center; }
        .nav-links a { text-decoration:none; color:#64748B; font-size:14px; font-weight:600; }
        .nav-links a:hover { color:#2563EB; }

        .main-container { max-width:1000px; margin:40px auto; padding:0 20px; }
        .page-header { margin-bottom:28px; }
        .page-title { font-size:28px; font-weight:800; color:#0F172A; }
        
        .orders-list { display:flex; flex-direction:column; gap:16px; }
        .order-card { background:#FFFFFF; border:1px solid #E2E8F0; border-radius:14px; padding:24px; display:flex; justify-content:space-between; align-items:center; box-shadow:0 1px 3px rgba(0,0,0,0.02); }
        .order-left { display:flex; gap:20px; align-items:center; }
        .order-thumb { width:70px; height:70px; background:#F1F5F9; border-radius:10px; display:flex; align-items:center; justify-content:center; overflow:hidden; }
        .order-thumb img { max-width:85%; max-height:85%; object-fit:contain; }
        .order-details strong { font-size:16px; color:#0F172A; display:block; margin-bottom:4px; }
        .order-meta { font-size:13px; color:#64748B; margin-bottom:4px; }
        
        .order-right { text-align:right; display:flex; flex-direction:column; align-items:flex-end; gap:8px; }
        .order-price { font-size:18px; font-weight:800; color:#0F172A; }
        
        .badge { display:inline-block; padding:4px 12px; border-radius:20px; font-size:12px; font-weight:700; text-transform:uppercase; }
        .badge-pending { background:#FEF3C7; color:#D97706; }
        .badge-confirmed { background:#E0E7FF; color:#4338CA; }
        .badge-active { background:#DCFCE7; color:#15803D; }
        .badge-returned { background:#F1F5F9; color:#475569; }

        .empty-box { background:#FFFFFF; border:2px dashed #CBD5E1; border-radius:14px; padding:60px 20px; text-align:center; color:#64748B; }
        .btn-sewa { background:#2563EB; color:#fff; text-decoration:none; padding:12px 24px; border-radius:8px; font-weight:700; display:inline-block; margin-top:16px; }
    </style>
</head>
<body>

    <header class="navbar">
        <a href="<?= base_url('/') ?>" class="brand-logo">CamHub</a>
        <div class="nav-links">
            <a href="<?= base_url('/katalog') ?>">Etalase Katalog</a>
            <a href="<?= base_url('/dashboard/pelanggan') ?>">Dashboard Saya</a>
            <a href="<?= base_url('/profil') ?>">Profil</a>
        </div>
    </header>

    <main class="main-container">
        <div class="page-header">
            <h1 class="page-title">Riwayat Pesanan Sewa Saya</h1>
            <p style="color:#64748B; font-size:14px; margin-top:4px;">Pantau jadwal dan status kamera yang Anda sewa dari toko mitra CamHub.</p>
        </div>

        <div class="orders-list">
            <?php if (empty($bookings)): ?>
                <div class="empty-box">
                    <p style="font-size:18px; font-weight:700; color:#0F172A; margin-bottom:6px;">Belum Ada Kamera yang Disewa</p>
                    <p>Temukan kamera profesional dan lensa favorit Anda di etalase sewa CamHub.</p>
                    <a href="<?= base_url('/katalog') ?>" class="btn-sewa">Jelajahi Katalog Kamera</a>
                </div>
            <?php else: ?>
                <?php foreach ($bookings as $b): ?>
                    <div class="order-card">
                        <div class="order-left">
                            <div class="order-thumb">
                                <?php if (!empty($b['product_image'])): ?>
                                    <img src="<?= base_url('/' . ltrim($b['product_image'], '/')) ?>" alt="" onerror="this.src='<?= base_url('/assets/img/canon_r50.png') ?>'">
                                <?php else: ?>
                                    <span style="font-size:26px;">📷</span>
                                <?php endif; ?>
                            </div>
                            <div class="order-details">
                                <span style="font-size:12px; font-weight:700; color:#2563EB; font-family:monospace;">
                                    #RENT-<?= str_pad((string)$b['id'], 5, '0', STR_PAD_LEFT) ?>
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
                            <div class="order-price">Rp <?= number_format($b['total_amount'], 0, ',', '.') ?></div>
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
        </div>
    </main>

</body>
</html>

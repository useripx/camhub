<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Mitra Lapak — CamHub</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; font-family:'Plus Jakarta Sans', sans-serif; }
        body { background:#F8FAFC; color:#0F172A; display:flex; min-height:100vh; }
        
        /* SIDEBAR NAVY GELAP (PERSIS MOCKUP CamHub — Dashboard Mitra Lapak.png) */
        .sidebar {
            width: 260px;
            background: #0B1320;
            color: #FFFFFF;
            display: flex;
            flex-direction: column;
            padding: 32px 24px;
            flex-shrink: 0;
            min-height: 100vh;
        }
        .brand-logo {
            font-size: 24px;
            font-weight: 800;
            color: #FFFFFF;
            text-decoration: none;
            letter-spacing: -0.5px;
            margin-bottom: 2px;
            display: block;
        }
        .brand-store {
            font-size: 14px;
            font-weight: 600;
            color: #CBD5E1;
            margin-bottom: 36px;
        }
        .nav-menu {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 8px;
            flex-grow: 1;
        }
        .nav-item a {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 12px 14px;
            color: #94A3B8;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.2s ease;
        }
        .nav-item a:hover {
            color: #FFFFFF;
            background: rgba(255, 255, 255, 0.05);
        }
        .nav-item.active a {
            color: #FFFFFF;
            background: rgba(255, 255, 255, 0.1);
        }
        .nav-icon {
            font-size: 16px;
            width: 20px;
            text-align: center;
            display: inline-block;
        }
        .sidebar-footer {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 20px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .sidebar-footer a, .sidebar-footer button {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #94A3B8;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            background: none;
            border: none;
            cursor: pointer;
            padding: 8px 12px;
            border-radius: 6px;
            transition: 0.2s;
            width: 100%;
            text-align: left;
        }
        .sidebar-footer a:hover, .sidebar-footer button:hover {
            color: #FFFFFF;
            background: rgba(255, 255, 255, 0.05);
        }
        .sidebar-footer button.btn-logout {
            color: #F87171;
        }
        .sidebar-footer button.btn-logout:hover {
            color: #EF4444;
            background: rgba(239, 68, 68, 0.1);
        }

        /* MAIN CANVAS */
        .main-content {
            flex-grow: 1;
            padding: 40px 48px;
            overflow-y: auto;
            max-width: 1200px;
        }
        .page-header {
            margin-bottom: 28px;
        }
        .page-title {
            font-size: 28px;
            font-weight: 800;
            color: #0F172A;
            letter-spacing: -0.5px;
        }
        .page-subtitle {
            font-size: 14px;
            color: #64748B;
            margin-top: 4px;
        }

        /* FLASH ALERT */
        .flash-alert {
            padding: 14px 18px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 24px;
        }
        .flash-success { background: #DCFCE7; color: #15803D; border: 1px solid #BBF7D0; }
        .flash-error { background: #FEE2E2; color: #B91C1C; border: 1px solid #FECACA; }

        /* 3 STATS SUMMARY CARDS (PERSIS MOCKUP) */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
            margin-bottom: 36px;
        }
        .stat-card {
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 14px;
            padding: 24px 28px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        }
        .stat-label {
            font-size: 14px;
            color: #64748B;
            font-weight: 500;
            margin-bottom: 12px;
        }
        .stat-value {
            font-size: 32px;
            font-weight: 800;
            color: #0F172A;
            letter-spacing: -0.5px;
        }

        /* PRODUK SAYA SECTION */
        .section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .section-title {
            font-size: 20px;
            font-weight: 800;
            color: #0F172A;
        }
        .btn-add-product {
            background: #2563EB;
            color: #FFFFFF;
            text-decoration: none;
            padding: 10px 18px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: 0.2s;
        }
        .btn-add-product:hover {
            background: #1D4ED8;
        }

        /* PRODUK CARDS LIST (HORIZONTAL LAYOUT PERSIS MOCKUP) */
        .product-list {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }
        .product-card {
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 12px;
            padding: 18px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
            transition: all 0.2s ease;
        }
        .product-card:hover {
            border-color: #CBD5E1;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        .product-left {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .product-thumb {
            width: 72px;
            height: 72px;
            background: #F1F5F9;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            flex-shrink: 0;
            border: 1px solid #E2E8F0;
        }
        .product-thumb img {
            max-width: 85%;
            max-height: 85%;
            object-fit: contain;
        }
        .product-thumb-placeholder {
            font-size: 28px;
            color: #94A3B8;
        }
        .product-details {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        .product-name {
            font-size: 16px;
            font-weight: 700;
            color: #0F172A;
        }
        .product-meta {
            font-size: 14px;
            color: #64748B;
        }
        .product-right {
            display: flex;
            align-items: center;
            gap: 24px;
        }
        .status-badge {
            font-size: 14px;
            font-weight: 700;
            color: #16A34A; /* Hijau persis mockup */
        }
        .product-actions {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .btn-action-sm {
            background: #F1F5F9;
            color: #475569;
            text-decoration: none;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            transition: 0.2s;
            border: 1px solid #E2E8F0;
        }
        .btn-action-sm:hover {
            background: #E2E8F0;
            color: #0F172A;
        }
        .btn-action-primary {
            background: #EFF6FF;
            color: #2563EB;
            border-color: #BFDBFE;
        }
        .btn-action-primary:hover {
            background: #DBEAFE;
            color: #1D4ED8;
        }

        /* EMPTY STATE */
        .empty-box {
            background: #FFFFFF;
            border: 2px dashed #CBD5E1;
            border-radius: 14px;
            padding: 48px;
            text-align: center;
            color: #64748B;
        }

        @media (max-width: 900px) {
            body { flex-direction: column; }
            .sidebar { width: 100%; min-height: auto; }
            .stats-grid { grid-template-columns: 1fr; }
            .product-card { flex-direction: column; align-items: flex-start; gap: 16px; }
            .product-right { width: 100%; justify-content: space-between; }
        }
    </style>
    <script>
        (function() {
            const theme = localStorage.getItem('camhub_theme') || 'dark';
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>
</head>
<body>

    <!-- Sidebar Navy Gelap -->
    <aside class="sidebar">
        <div>
            <a href="<?= base_url('/dashboard/mitra') ?>" class="brand-logo">CamHub</a>
            <div class="brand-store"><?= htmlspecialchars($partner['store_name'] ?? 'Mitra Lapak') ?></div>
        </div>

        <ul class="nav-menu">
            <li class="nav-item <?= ($activeMenu === 'beranda') ? 'active' : '' ?>">
                <a href="<?= base_url('/dashboard/mitra') ?>">
                    Beranda
                </a>
            </li>
            <li class="nav-item <?= ($activeMenu === 'produk') ? 'active' : '' ?>">
                <a href="<?= base_url('/dashboard/mitra/produk') ?>">
                    Kelola Produk
                </a>
            </li>
            <li class="nav-item <?= ($activeMenu === 'jadwal') ? 'active' : '' ?>">
                <a href="<?= base_url('/dashboard/mitra/jadwal') ?>">
                    Jadwal Sewa
                </a>
            </li>
            <li class="nav-item <?= ($activeMenu === 'transaksi') ? 'active' : '' ?>">
                <a href="<?= base_url('/dashboard/mitra/transaksi') ?>">
                    Transaksi
                </a>
            </li>
            <li class="nav-item <?= ($activeMenu === 'saldo') ? 'active' : '' ?>">
                <a href="<?= base_url('/dashboard/mitra/saldo') ?>">
                    Saldo & Pencairan
                </a>
            </li>
            <li class="nav-item <?= ($activeMenu === 'pengaturan') ? 'active' : '' ?>">
                <a href="<?= base_url('/dashboard/mitra/pengaturan') ?>">
                    Pengaturan
                </a>
            </li>
            <li class="nav-item <?= ($activeMenu === 'titip-jual') ? 'active' : '' ?>">
                <a href="<?= base_url('/dashboard/mitra/titip-jual') ?>">
                    Kelola Titip Jual
                </a>
            </li>
        </ul>

        <div class="sidebar-footer">
            <button type="button" class="btn-theme-toggle" onclick="toggleCamHubTheme()" title="Beralih Mode Gelap/Terang" style="width:100%; background:#16243a; color:#cbd5e1; border:1px solid #1e3a5f; padding:9px 12px; border-radius:8px; font-size:12px; font-weight:600; cursor:pointer; text-align:center; margin-bottom:8px;">
                <span id="themeToggleText">Mode Terang</span>
            </button>
            <a href="<?= base_url('/') ?>" target="_blank">
                Website Publik
            </a>
            <form action="<?= base_url('/logout') ?>" method="POST" style="margin:0;">
                <?= csrf_field() ?>
                <button type="submit" class="btn-logout" style="background:#dc2626; color:#ffffff; font-weight:700; border-radius:8px; justify-content:center; padding:10px 14px; margin-top:8px; width:100%; display:flex; align-items:center; gap:8px;">
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    <!-- Konten Utama Dashboard Mitra -->
    <main class="main-content">
        <div class="page-header">
            <h1 class="page-title">Dashboard Mitra Lapak</h1>
            <p class="page-subtitle">Kelola inventaris kamera, pantau jadwal booking sewa, dan pantau saldo toko Anda.</p>
        </div>

        <?php if ($msg = flash('success')): ?>
            <div class="flash-alert flash-success"><?= $msg ?></div>
        <?php endif; ?>
        <?php if ($msg = flash('error')): ?>
            <div class="flash-alert flash-error"><?= $msg ?></div>
        <?php endif; ?>

        <!-- 3 Kartu Metrik Utama -->
        <section class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">Total Produk</div>
                <div class="stat-value"><?= (int)($metrics['total_products'] ?? 0) ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Pesanan Aktif</div>
                <div class="stat-value"><?= (int)($metrics['active_orders'] ?? 0) ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Saldo Tersedia</div>
                <div class="stat-value"><?= $metrics['available_balance_formatted'] ?? 'Rp 0' ?></div>
            </div>
        </section>

        <!-- Seksi Produk Saya -->
        <section>
            <div class="section-header">
                <h2 class="section-title">Produk Saya</h2>
                <a href="<?= base_url('/dashboard/mitra/produk/tambah') ?>" class="btn-add-product">
                    Tambah Produk
                </a>
            </div>

            <div class="product-list">
                <?php if (empty($products)): ?>
                    <div class="empty-box">
                        <p style="font-size:16px; font-weight:700; margin-bottom:6px;">Belum Ada Kamera di Etalase Toko</p>
                        <p style="font-size:14px; margin-bottom:16px;">Mulai sewakan kamera dan lensa Anda dengan menekan tombol Tambah Produk.</p>
                        <a href="<?= base_url('/dashboard/mitra/produk/tambah') ?>" class="btn-add-product" style="display:inline-block;">+ Tambah Produk Sekarang</a>
                    </div>
                <?php else: ?>
                    <?php foreach ($products as $prod): ?>
                        <div class="product-card">
                            <div class="product-left">
                                <div class="product-thumb">
                                    <?php if (!empty($prod['primary_image'])): ?>
                                        <img src="<?= base_url('/' . ltrim($prod['primary_image'], '/')) ?>" alt="<?= htmlspecialchars($prod['name']) ?>" onerror="this.src='<?= base_url('/assets/img/canon_r50.png') ?>'">
                                    <?php else: ?>
                                        <span class="product-thumb-placeholder">📷</span>
                                    <?php endif; ?>
                                </div>
                                <div class="product-details">
                                    <div class="product-name"><?= htmlspecialchars($prod['name']) ?></div>
                                    <div class="product-meta">
                                        <?php
                                            $types = [];
                                            if ($prod['is_rental']) $types[] = 'Sewa';
                                            if ($prod['is_sale']) $types[] = 'Jual';
                                            $typeStr = !empty($types) ? implode(' & ', $types) : 'Sewa';
                                            $priceStr = 'Rp ' . number_format($prod['daily_price'], 0, ',', '.') . '/hari';
                                            echo htmlspecialchars($typeStr . ' · ' . $priceStr);
                                        ?>
                                    </div>
                                </div>
                            </div>

                            <div class="product-right">
                                <div class="status-badge">Tersedia</div>
                                <div class="product-actions">
                                    <a href="<?= base_url('/dashboard/mitra/produk/' . $prod['id'] . '/unit') ?>" class="btn-action-sm btn-action-primary" title="Kelola Serial Number Fisik">
                                        Unit Fisik (<?= (int)$prod['total_units'] ?>)
                                    </a>
                                    <a href="<?= base_url('/dashboard/mitra/produk/' . $prod['id'] . '/edit') ?>" class="btn-action-sm" title="Edit Produk">
                                        Edit
                                    </a>
                                    <a href="<?= base_url('/produk/' . $prod['slug']) ?>" target="_blank" class="btn-action-sm" title="Lihat di Katalog Publik">
                                        Lihat
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <script>
        function toggleCamHubTheme() {
            const currentTheme = document.documentElement.getAttribute('data-theme') || 'dark';
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('camhub_theme', newTheme);
            syncThemeToggleUI(newTheme);
        }

        function syncThemeToggleUI(theme) {
            const text = document.getElementById('themeToggleText');
            if (!text) return;
            text.textContent = (theme === 'dark') ? 'Mode Terang' : 'Mode Gelap';
        }

        document.addEventListener('DOMContentLoaded', function() {
            const active = document.documentElement.getAttribute('data-theme') || 'dark';
            syncThemeToggleUI(active);
        });
    </script>
</body>
</html>

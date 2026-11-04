<?php
$user = $user ?? auth();
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
    <script>
        (function() {
            const theme = localStorage.getItem('camhub_theme') || 'dark';
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>
    <style>
        :root {
            --bg-page: #0b1320;
            --bg-surface: #112240;
            --bg-card: #132644;
            --bg-subtle: rgba(255, 255, 255, 0.04);
            --bg-input: #0d1b30;
            --border-color: #1e3a5f;
            --border-subtle: #162c4a;
            --text-primary: #ffffff;
            --text-secondary: #94a3b8;
            --text-muted: #64748b;
            --accent-blue: #3b82f6;
            --accent-amber: #f59e0b;
        }

        html[data-theme="dark"], html:not([data-theme="light"]) {
            --bg-page: #0b1320;
            --bg-surface: #112240;
            --bg-card: #132644;
            --bg-subtle: rgba(255, 255, 255, 0.04);
            --bg-input: #0d1b30;
            --border-color: #1e3a5f;
            --border-subtle: #162c4a;
            --text-primary: #ffffff;
            --text-secondary: #94a3b8;
            --text-muted: #64748b;
            --accent-blue: #3b82f6;
            --accent-amber: #f59e0b;
        }

        html[data-theme="light"] {
            --bg-page: #f8fafc;
            --bg-surface: #ffffff;
            --bg-card: #ffffff;
            --bg-subtle: #f1f5f9;
            --bg-input: #ffffff;
            --border-color: #e2e8f0;
            --border-subtle: #f1f5f9;
            --text-primary: #0f172a;
            --text-secondary: #475569;
            --text-muted: #94a3b8;
            --accent-blue: #2563eb;
            --accent-amber: #d97706;
        }

        * { margin:0; padding:0; box-sizing:border-box; font-family:'Plus Jakarta Sans', system-ui, sans-serif; }
        body { background:var(--bg-page); color:var(--text-primary); min-height:100vh; display:flex; }

        /* SIDEBAR (CLEAN TEXT-ONLY, NO ICONS) */
        .sidebar {
            width:260px;
            background:#0c192c;
            color:#ffffff;
            padding:32px 24px 24px;
            display:flex;
            flex-direction:column;
            flex-shrink:0;
            height:100vh;
            position:sticky;
            top:0;
            overflow-y:auto;
        }
        .sidebar-brand {
            font-size:24px;
            font-weight:800;
            color:#ffffff;
            text-decoration:none;
            margin-bottom:24px;
            display:block;
            letter-spacing:-0.5px;
        }
        .sidebar-user {
            margin-bottom:24px;
        }
        .user-fullname {
            font-size:16px;
            font-weight:700;
            color:#ffffff;
            line-height:1.3;
            margin-bottom:4px;
        }
        .user-role-label {
            font-size:12px;
            color:#94a3b8;
            font-weight:500;
        }
        .nav-menu {
            list-style:none;
            display:flex;
            flex-direction:column;
            gap:4px;
        }
        .nav-item a {
            display:block;
            padding:10px 14px;
            border-radius:8px;
            font-size:13px;
            font-weight:600;
            color:#94a3b8;
            text-decoration:none;
            transition:all 0.15s ease;
        }
        .nav-item a:hover {
            background:#16243a;
            color:#ffffff;
        }
        .nav-item.active a {
            background:#1e293b;
            color:#ffffff;
            font-weight:700;
            border-left:3px solid #3b82f6;
        }
        
        .sidebar-bottom {
            margin-top:16px;
            padding-top:16px;
            border-top:1px solid #1e3a5f;
            display:flex;
            flex-direction:column;
            gap:8px;
        }
        .btn-theme-toggle {
            width:100%;
            background:#16243a;
            color:#cbd5e1;
            border:1px solid #1e3a5f;
            padding:10px 14px;
            border-radius:8px;
            font-size:13px;
            font-weight:600;
            cursor:pointer;
            text-align:center;
            transition:all 0.2s;
        }
        .btn-theme-toggle:hover {
            background:#1e293b;
            color:#ffffff;
            border-color:#3b82f6;
        }
        .btn-sidebar-link {
            display:block;
            padding:10px 14px;
            color:#94a3b8;
            font-size:13px;
            font-weight:600;
            text-decoration:none;
            border-radius:8px;
            transition:0.15s;
        }
        .btn-sidebar-link:hover {
            background:#16243a;
            color:#ffffff;
        }
        .btn-sidebar-logout {
            width:100%;
            background:transparent;
            border:none;
            cursor:pointer;
            padding:10px 14px;
            color:#ef4444;
            font-size:13px;
            font-weight:600;
            border-radius:8px;
            transition:0.15s;
            text-align:left;
        }
        .btn-sidebar-logout:hover {
            background:#1e293b;
            color:#f87171;
        }

        /* MAIN CONTENT */
        .main-content {
            flex:1;
            padding:40px 48px 60px;
            overflow-y:auto;
            max-width:1100px;
        }
        .breadcrumb {
            display:flex;
            align-items:center;
            gap:8px;
            font-size:13px;
            color:var(--text-muted);
            margin-bottom:20px;
        }
        .breadcrumb a {
            color:var(--accent-blue);
            text-decoration:none;
            font-weight:600;
        }
        .breadcrumb a:hover {
            text-decoration:underline;
        }

        .page-header { margin-bottom:24px; }
        .page-title {
            font-size:26px;
            font-weight:800;
            color:var(--text-primary);
            letter-spacing:-0.5px;
        }
        .page-desc {
            color:var(--text-secondary);
            font-size:14px;
            margin-top:4px;
        }

        /* TABS FILTER */
        .tabs-row {
            display:flex;
            gap:10px;
            margin-bottom:28px;
            border-bottom:1px solid var(--border-color);
            padding-bottom:12px;
        }
        .tab-btn {
            text-decoration:none;
            padding:10px 20px;
            border-radius:10px;
            font-size:14px;
            font-weight:700;
            color:var(--text-secondary);
            background:transparent;
            transition:all 0.15s ease;
        }
        .tab-btn:hover {
            background:var(--bg-subtle);
            color:var(--text-primary);
        }
        .tab-btn.active {
            background:rgba(37, 99, 235, 0.15);
            color:var(--accent-blue);
            border:1px solid rgba(37, 99, 235, 0.3);
        }

        .orders-list { display:flex; flex-direction:column; gap:16px; }
        .order-card {
            background:var(--bg-card);
            border:1px solid var(--border-color);
            border-radius:16px;
            padding:24px;
            display:flex;
            justify-content:space-between;
            align-items:center;
            box-shadow:0 2px 8px rgba(0,0,0,0.04);
            transition:all 0.15s;
        }
        .order-card:hover {
            border-color:var(--accent-blue);
            box-shadow:0 4px 16px rgba(0,0,0,0.08);
        }
        .order-left { display:flex; gap:20px; align-items:center; }
        .order-thumb {
            width:72px;
            height:72px;
            background:var(--bg-subtle);
            border:1px solid var(--border-color);
            border-radius:12px;
            display:flex;
            align-items:center;
            justify-content:center;
            overflow:hidden;
            font-size:13px;
            font-weight:700;
            color:var(--text-muted);
            flex-shrink:0;
        }
        .order-thumb img { width:100%; height:100%; object-fit:cover; }
        .order-details strong {
            font-size:16px;
            color:var(--text-primary);
            display:block;
            margin-bottom:4px;
            font-weight:800;
        }
        .order-meta { font-size:13px; color:var(--text-secondary); margin-bottom:4px; }
        
        .order-right {
            text-align:right;
            display:flex;
            flex-direction:column;
            align-items:flex-end;
            gap:8px;
        }
        .order-price {
            font-size:18px;
            font-weight:800;
            color:var(--text-primary);
        }
        
        .badge {
            display:inline-block;
            padding:5px 14px;
            border-radius:20px;
            font-size:11px;
            font-weight:800;
            text-transform:uppercase;
            letter-spacing:0.5px;
        }
        .badge-pending { background:rgba(245, 158, 11, 0.15); color:#fbbf24; border:1px solid rgba(245, 158, 11, 0.3); }
        .badge-confirmed { background:rgba(99, 102, 241, 0.15); color:#a5b4fc; border:1px solid rgba(99, 102, 241, 0.3); }
        .badge-active { background:rgba(16, 185, 129, 0.15); color:#34d399; border:1px solid rgba(16, 185, 129, 0.3); }
        .badge-completed { background:rgba(16, 185, 129, 0.15); color:#34d399; border:1px solid rgba(16, 185, 129, 0.3); }
        .badge-shipped { background:rgba(59, 130, 246, 0.15); color:#93c5fd; border:1px solid rgba(59, 130, 246, 0.3); }
        .badge-delivered { background:rgba(16, 185, 129, 0.15); color:#34d399; border:1px solid rgba(16, 185, 129, 0.3); }
        .badge-returned { background:rgba(148, 163, 184, 0.15); color:#cbd5e1; border:1px solid rgba(148, 163, 184, 0.3); }

        .empty-box {
            background:var(--bg-card);
            border:1px dashed var(--border-color);
            border-radius:16px;
            padding:60px 20px;
            text-align:center;
            color:var(--text-secondary);
        }
        .btn-action-browse {
            background:#2563eb;
            color:#fff;
            text-decoration:none;
            padding:12px 24px;
            border-radius:10px;
            font-weight:700;
            display:inline-block;
            margin-top:16px;
        }

        @media (max-width: 768px) {
            .order-card { flex-direction:column; align-items:flex-start; gap:16px; }
            .order-right { align-items:flex-start; text-align:left; }
            body { flex-direction:column; }
            .sidebar { width:100%; min-height:auto; }
        }
    </style>
</head>
<body>

    <!-- SIDEBAR KIRI VERTIKAL (CLEAN TEXT-ONLY, NO ICONS) -->
    <aside class="sidebar">
        <a href="<?= base_url() ?>" class="sidebar-brand">CamHub</a>
        
        <div class="sidebar-user">
            <div class="user-fullname"><?= htmlspecialchars($user['full_name'] ?? 'Pelanggan', ENT_QUOTES, 'UTF-8') ?></div>
            <div class="user-role-label">Pelanggan</div>
        </div>

        <ul class="nav-menu">
            <li class="nav-item">
                <a href="<?= base_url('/dashboard/pelanggan') ?>">Beranda</a>
            </li>
            <li class="nav-item <?= $activeType === 'all' ? 'active' : '' ?>">
                <a href="<?= base_url('/pesanan-saya') ?>">Pesanan Saya</a>
            </li>
            <li class="nav-item <?= $activeType === 'rental' ? 'active' : '' ?>">
                <a href="<?= base_url('/pesanan-saya?type=rental') ?>">Sewa Saya</a>
            </li>
            <li class="nav-item <?= $activeType === 'sale' ? 'active' : '' ?>">
                <a href="<?= base_url('/pesanan-saya?type=sale') ?>">Jual Beli Saya</a>
            </li>
            <li class="nav-item">
                <a href="<?= base_url('/titip-jual/saya') ?>">Titip Jual Saya</a>
            </li>
            <li class="nav-item">
                <a href="<?= base_url('/kyc/status') ?>">Dokumen KYC</a>
            </li>
            <li class="nav-item">
                <a href="<?= base_url('/profil') ?>">Pengaturan</a>
            </li>
        </ul>

        <div class="sidebar-bottom">
            <button type="button" class="btn-theme-toggle" onclick="toggleCamHubTheme()" title="Beralih Mode Gelap/Terang">
                <span id="themeToggleText">Mode Terang</span>
            </button>

            <a href="<?= base_url('/') ?>" class="btn-sidebar-link">
                Lihat Beranda Publik
            </a>

            <form action="<?= base_url('/logout') ?>" method="POST" style="margin:0;">
                <?= csrf_field() ?>
                <button type="submit" class="btn-sidebar-logout">
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    <!-- KONTEN UTAMA -->
    <main class="main-content">
        <div class="breadcrumb">
            <a href="<?= base_url('/dashboard/pelanggan') ?>">Dashboard</a>
            <span>/</span>
            <span>Pesanan Saya</span>
        </div>

        <?php if ($msg = flash('success')): ?>
            <div style="background:rgba(16, 185, 129, 0.15); border:1px solid rgba(16, 185, 129, 0.3); color:#34d399; padding:12px 18px; border-radius:10px; margin-bottom:20px; font-weight:600; font-size:14px;">
                <?= $msg ?>
            </div>
        <?php endif; ?>
        <?php if ($msg = flash('error')): ?>
            <div style="background:rgba(239, 68, 68, 0.15); border:1px solid rgba(239, 68, 68, 0.3); color:#f87171; padding:12px 18px; border-radius:10px; margin-bottom:20px; font-weight:600; font-size:14px;">
                <?= $msg ?>
            </div>
        <?php endif; ?>

        <div class="page-header">
            <h1 class="page-title">Riwayat Pesanan Saya</h1>
            <p class="page-desc">Pantau jadwal sewa, status pembelian kamera, dan perlindungan dana Escrow Anda.</p>
        </div>

        <!-- TABS NAVIGASI (TEXT ONLY) -->
        <div class="tabs-row">
            <a href="<?= base_url('/pesanan-saya') ?>" class="tab-btn <?= $activeType === 'all' ? 'active' : '' ?>">Semua Transaksi</a>
            <a href="<?= base_url('/pesanan-saya?type=rental') ?>" class="tab-btn <?= $activeType === 'rental' ? 'active' : '' ?>">Sewa Saya</a>
            <a href="<?= base_url('/pesanan-saya?type=sale') ?>" class="tab-btn <?= $activeType === 'sale' ? 'active' : '' ?>">Jual Beli Saya</a>
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
                                    Foto
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
                            <a href="<?= base_url('/pesanan/beli/' . $p['id'] . '/sukses') ?>" style="font-size:13px; font-weight:600; color:var(--accent-blue); text-decoration:none;">
                                Lihat Invoice & Status Escrow →
                            </a>
                            <?php if (($p['order_status'] ?? '') !== 'completed'): ?>
                                <form action="<?= base_url('/pesanan/beli/' . $p['id'] . '/konfirmasi-terima') ?>" method="POST" style="margin-top:6px;">
                                    <?= csrf_field() ?>
                                    <button type="submit" style="background:#16a34a; color:#fff; border:none; padding:7px 14px; border-radius:8px; font-size:12px; font-weight:700; cursor:pointer;" onclick="return confirm('Konfirmasi Anda telah menerima kamera ini dengan baik? Dana pembayaran akan diteruskan ke mitra lapak.')">
                                        Konfirmasi Terima & Lepas Escrow
                                    </button>
                                </form>
                            <?php endif; ?>
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
                                    Foto
                                <?php endif; ?>
                            </div>
                            <div class="order-details">
                                <span style="font-size:12px; font-weight:700; color:var(--accent-blue); font-family:monospace;">
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
                            <a href="<?= base_url('/booking/' . $b['id'] . '/sukses') ?>" style="font-size:13px; font-weight:600; color:var(--accent-blue); text-decoration:none;">
                                Lihat Nota & Panduan Escrow →
                            </a>
                            <?php if (($b['rental_status'] ?? '') !== 'completed'): ?>
                                <form action="<?= base_url('/pesanan/sewa/' . $b['id'] . '/konfirmasi-selesai') ?>" method="POST" style="margin-top:6px;">
                                    <?= csrf_field() ?>
                                    <button type="submit" style="background:#2563eb; color:#fff; border:none; padding:7px 14px; border-radius:8px; font-size:12px; font-weight:700; cursor:pointer;" onclick="return confirm('Selesaikan masa sewa kamera ini? Dana sewa akan diteruskan ke toko mitra.')">
                                        Selesai Sewa & Lepas Escrow
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <?php if (!$hasItems): ?>
                <div class="empty-box">
                    <p style="font-size:18px; font-weight:700; color:var(--text-primary); margin-bottom:6px;">Belum Ada Transaksi</p>
                    <p>Temukan kamera impian untuk disewa atau dibeli langsung dengan proteksi rekening bersama Escrow.</p>
                    <a href="<?= base_url('/katalog') ?>" class="btn-action-browse">Jelajahi Katalog Kamera</a>
                </div>
            <?php endif; ?>
        </div>
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

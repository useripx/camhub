<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pelanggan — CamHub</title>
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
            padding:36px 24px;
            display:flex;
            flex-direction:column;
            flex-shrink:0;
            min-height:100vh;
        }
        .sidebar-brand {
            font-size:24px;
            font-weight:800;
            color:#ffffff;
            text-decoration:none;
            margin-bottom:32px;
            display:block;
            letter-spacing:-0.5px;
        }
        .sidebar-user {
            margin-bottom:32px;
        }
        .user-fullname {
            font-size:17px;
            font-weight:700;
            color:#ffffff;
            line-height:1.3;
            margin-bottom:4px;
        }
        .user-role-label {
            font-size:13px;
            color:#94a3b8;
            font-weight:500;
        }
        .nav-menu {
            list-style:none;
            display:flex;
            flex-direction:column;
            gap:6px;
            flex:1;
        }
        .nav-item a {
            display:block;
            padding:12px 16px;
            border-radius:10px;
            font-size:14px;
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
            margin-top:auto;
            padding-top:24px;
            border-top:1px solid #1e293b;
            display:flex;
            flex-direction:column;
            gap:10px;
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

        /* MAIN CONTENT AREA */
        .main-content {
            flex:1;
            padding:40px 48px 60px;
            overflow-y:auto;
            max-width:1100px;
        }
        .page-greeting {
            font-size:30px;
            font-weight:800;
            color:var(--text-primary);
            letter-spacing:-0.5px;
            margin-bottom:6px;
        }
        .page-subtitle {
            font-size:15px;
            color:var(--text-secondary);
            margin-bottom:32px;
        }

        /* 3 KARTU STATISTIK */
        .stats-grid {
            display:grid;
            grid-template-columns:repeat(3, 1fr);
            gap:24px;
            margin-bottom:44px;
        }
        .stat-card {
            background:var(--bg-card);
            border:1px solid var(--border-color);
            border-radius:16px;
            padding:28px 24px;
            display:flex;
            flex-direction:column;
            justify-content:space-between;
            min-height:160px;
            box-shadow:0 2px 10px rgba(0,0,0,0.04);
            transition:all 0.2s ease;
        }
        .stat-card:hover {
            border-color:var(--accent-blue);
            transform:translateY(-2px);
            box-shadow:0 6px 16px rgba(0,0,0,0.08);
        }
        .stat-title {
            font-size:15px;
            font-weight:600;
            color:var(--text-secondary);
            margin-bottom:12px;
        }
        .stat-number {
            font-size:32px;
            font-weight:800;
            color:var(--text-primary);
            letter-spacing:-0.5px;
            line-height:1;
        }
        .stat-number.amount {
            color:#10b981;
        }
        .stat-link {
            font-size:13px;
            font-weight:700;
            color:var(--accent-blue);
            text-decoration:none;
            display:inline-flex;
            align-items:center;
            gap:4px;
            transition:0.15s;
            margin-top:16px;
        }
        .stat-link:hover {
            text-decoration:underline;
        }

        /* DAFTAR PESANAN TERBARU */
        .section-header {
            font-size:20px;
            font-weight:800;
            color:var(--text-primary);
            margin-bottom:20px;
            letter-spacing:-0.3px;
        }
        .orders-list {
            display:flex;
            flex-direction:column;
            gap:14px;
        }
        .order-card-row {
            background:var(--bg-card);
            border:1px solid var(--border-color);
            border-radius:14px;
            padding:20px 24px;
            display:flex;
            align-items:center;
            justify-content:space-between;
            box-shadow:0 2px 8px rgba(0,0,0,0.03);
            transition:all 0.15s ease;
            text-decoration:none;
            color:inherit;
        }
        .order-card-row:hover {
            border-color:var(--accent-blue);
            box-shadow:0 4px 14px rgba(0,0,0,0.06);
            transform:translateY(-1px);
        }
        .order-info-left {
            display:flex;
            align-items:center;
            gap:20px;
        }
        .order-thumb {
            width:64px;
            height:64px;
            background:var(--bg-subtle);
            border-radius:10px;
            display:flex;
            align-items:center;
            justify-content:center;
            font-size:13px;
            font-weight:700;
            color:var(--text-muted);
            overflow:hidden;
            flex-shrink:0;
            border:1px solid var(--border-color);
        }
        .order-thumb img {
            width:100%;
            height:100%;
            object-fit:cover;
        }
        .order-name {
            font-size:16px;
            font-weight:700;
            color:var(--text-primary);
            margin-bottom:4px;
        }
        .order-desc {
            font-size:14px;
            color:var(--text-secondary);
        }
        .order-status-badge {
            font-size:13px;
            font-weight:700;
            color:var(--accent-blue);
            text-align:right;
        }

        @media (max-width: 900px) {
            body { flex-direction:column; }
            .sidebar { width:100%; min-height:auto; }
            .main-content { padding:24px; }
            .stats-grid { grid-template-columns:1fr; }
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
            <li class="nav-item active">
                <a href="<?= base_url('/dashboard/pelanggan') ?>">Beranda</a>
            </li>
            <li class="nav-item">
                <a href="<?= base_url('/pesanan-saya') ?>">Pesanan Saya</a>
            </li>
            <li class="nav-item">
                <a href="<?= base_url('/pesanan-saya?type=rental') ?>">Sewa Saya</a>
            </li>
            <li class="nav-item">
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

    <!-- MAIN CONTENT -->
    <main class="main-content">
        <h1 class="page-greeting">Halo, <?= htmlspecialchars($firstName, ENT_QUOTES, 'UTF-8') ?>!</h1>
        <p class="page-subtitle">Nikmati pengalaman terbaik di CamHub.</p>

        <!-- 3 KARTU METRIK -->
        <section class="stats-grid">
            <div class="stat-card">
                <div>
                    <div class="stat-title">Pesanan Aktif</div>
                    <div class="stat-number"><?= (int)$pesananAktif ?></div>
                </div>
                <a href="<?= base_url('/pesanan-saya') ?>" class="stat-link">Lihat detail →</a>
            </div>

            <div class="stat-card">
                <div>
                    <div class="stat-title">Sewa Mendatang</div>
                    <div class="stat-number"><?= (int)$sewaMendatang ?></div>
                </div>
                <a href="<?= base_url('/pesanan-saya?type=rental') ?>" class="stat-link">Lihat detail →</a>
            </div>

            <div class="stat-card">
                <div>
                    <div class="stat-title">Saldo Escrow</div>
                    <div class="stat-number amount">Rp <?= number_format($saldoEscrow, 0, ',', '.') ?></div>
                </div>
                <a href="<?= base_url('/pesanan-saya') ?>" class="stat-link">Lihat detail →</a>
            </div>
        </section>

        <!-- PESANAN TERBARU -->
        <section>
            <h2 class="section-header">Pesanan Terbaru</h2>
            
            <div class="orders-list">
                <?php foreach ($latestOrders as $order): ?>
                    <a href="<?= !empty($order['id']) ? base_url('/booking/' . $order['id'] . '/sukses') : base_url('/pesanan-saya') ?>" class="order-card-row">
                        <div class="order-info-left">
                            <div class="order-thumb">
                                <?php if (!empty($order['product_image'])): ?>
                                    <img src="<?= base_url('/' . ltrim($order['product_image'], '/')) ?>" alt="<?= htmlspecialchars($order['product_name']) ?>">
                                <?php else: ?>
                                    Foto
                                <?php endif; ?>
                            </div>
                            <div>
                                <div class="order-name"><?= htmlspecialchars($order['product_name']) ?></div>
                                <div class="order-desc"><?= htmlspecialchars($order['order_desc']) ?></div>
                            </div>
                        </div>
                        <div class="order-status-badge">
                            <?= htmlspecialchars($order['status_label']) ?>
                        </div>
                    </a>
                <?php endforeach; ?>
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

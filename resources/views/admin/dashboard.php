<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin — CamHub</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root, html[data-theme="dark"] {
            --bg-page: #0b1320;
            --bg-surface: #112240;
            --bg-card: #132644;
            --bg-card-hover: #173056;
            --bg-subtle: rgba(255, 255, 255, 0.04);
            --bg-input: #0d1b30;
            --border-color: #1e3a5f;
            --border-subtle: #162c4a;
            --text-primary: #ffffff;
            --text-secondary: #94a3b8;
            --text-muted: #64748b;
            --accent-blue: #3b82f6;
            --accent-amber: #f59e0b;
            --table-th-bg: #0d1b30;
            --table-th-text: #94a3b8;
            --table-td-border: #1e3a5f;
            --table-td-text: #ffffff;
            --form-input-bg: #0d1b30;
            --form-input-border: #1e3a5f;
            --form-input-text: #ffffff;
            --stat-card-bg: #132644;
        }

        html[data-theme="light"] {
            --bg-page: #f8fafc;
            --bg-surface: #ffffff;
            --bg-card: #ffffff;
            --bg-card-hover: #f1f5f9;
            --bg-subtle: #f1f5f9;
            --bg-input: #ffffff;
            --border-color: #e2e8f0;
            --border-subtle: #cbd5e1;
            --text-primary: #0f172a;
            --text-secondary: #475569;
            --text-muted: #64748b;
            --accent-blue: #2563eb;
            --accent-amber: #d97706;
            --table-th-bg: #f8fafc;
            --table-th-text: #64748b;
            --table-td-border: #f1f5f9;
            --table-td-text: #1e293b;
            --form-input-bg: #ffffff;
            --form-input-border: #cbd5e1;
            --form-input-text: #0f172a;
            --stat-card-bg: #ffffff;
        }
        * { margin:0; padding:0; box-sizing:border-box; font-family:'Plus Jakarta Sans', sans-serif; }
        body { background:var(--bg-page); color:var(--text-primary); min-height:100vh; display:flex; }
        
        /* Sidebar Styling (Sesuai CamHub — Dashboard Admin.png) */
        .admin-sidebar {
            width:260px;
            background:#0c192c;
            min-height:100vh; height:100vh; position:sticky; top:0; overflow-y:auto; padding:32px 24px 24px;
            display:flex;
            flex-direction:column;
            justify-content:space-between;
            flex-shrink:0;
        }
        .sidebar-brand {
            font-size:24px;
            font-weight:800;
            color:#ffffff;
            text-decoration:none;
            display:block;
            margin-bottom:18px;
            letter-spacing:-0.5px;
        }
        .sidebar-nav {
            list-style:none;
            display:flex;
            flex-direction:column;
            gap:22px;
        }
        .sidebar-nav a {
            color:#94a3b8;
            text-decoration:none;
            font-size:15px;
            font-weight:500;
            display:flex;
            align-items:center;
            transition:color 0.2s;
        }
        .sidebar-nav a:hover {
            color:#ffffff;
        }
        .sidebar-nav a.active {
            color:#ffffff;
            font-weight:700;
        }

        /* Sidebar Footer / Logout */
        .sidebar-footer {
            margin-top:auto;
            padding-top:28px;
            border-top:1px solid #1e293b;
        }
        .admin-profile {
            display:flex;
            align-items:center;
            gap:10px;
            margin-bottom:14px;
        }
        .admin-avatar {
            width:34px;
            height:34px;
            border-radius:50%;
            background:#1e3a8a;
            color:#93c5fd;
            display:flex;
            align-items:center;
            justify-content:center;
            font-size:14px;
            font-weight:700;
        }
        .admin-meta {
            font-size:13px;
        }
        .admin-name {
            color:#ffffff;
            font-weight:700;
        }
        .admin-role {
            color:#38bdf8;
            font-size:11px;
            font-weight:600;
        }
        .btn-sidebar-logout {
            width:100%;
            background:#1e293b;
            color:#f87171;
            border:1px solid #334155;
            padding:9px 14px;
            border-radius:8px;
            font-size:13px;
            font-weight:600;
            cursor:pointer;
            transition:all 0.2s;
            display:flex;
            align-items:center;
            justify-content:center;
            gap:6px;
        }
        .btn-sidebar-logout:hover {
            background:#dc2626;
            color:#ffffff;
            border-color:#dc2626;
        }

        /* Main Content Styling */
        .admin-main {
            flex:1;
            padding:40px 48px;
            max-width:1200px;
        }
        .page-title {
            font-size:28px;
            font-weight:800;
            color:var(--text-primary);
            margin-bottom:28px;
            letter-spacing:-0.5px;
        }

        /* 4 Metrics Grid 2x2 */
        .metrics-grid {
            display:grid;
            grid-template-columns:repeat(2, 1fr);
            gap:24px;
            margin-bottom:36px;
        }
        .metric-card {
            background:var(--stat-card-bg);
            border:1px solid var(--border-color);
            border-radius:16px;
            padding:26px 28px;
            box-shadow:0 1px 3px rgba(0,0,0,0.02);
            transition:transform 0.2s, box-shadow 0.2s;
        }
        .metric-card:hover {
            transform:translateY(-2px);
            box-shadow:0 4px 12px rgba(0,0,0,0.04);
        }
        .metric-label {
            font-size:14px;
            font-weight:500;
            color:var(--text-secondary);
            margin-bottom:16px;
        }
        .metric-value {
            font-size:32px;
            font-weight:800;
            color:var(--text-primary);
            line-height:1;
        }

        /* Chart Section */
        .chart-section-title {
            font-size:20px;
            font-weight:700;
            color:var(--text-primary);
            margin-bottom:18px;
            letter-spacing:-0.3px;
        }
        .chart-card {
            background:var(--bg-card);
            border:1px solid var(--border-color);
            border-radius:16px;
            padding:44px 36px 28px 36px;
            box-shadow:0 1px 3px rgba(0,0,0,0.02);
        }
        .chart-bars-container {
            display:flex;
            justify-content:space-around;
            align-items:flex-end;
            height:220px;
            border-bottom:1px solid var(--border-color);
            padding-bottom:12px;
        }
        .bar-col {
            display:flex;
            flex-direction:column;
            align-items:center;
            gap:10px;
            width:48px;
            height:100%;
            justify-content:flex-end;
        }
        .bar-col .bar-fill {
            width:34px;
            background:#2563eb;
            border-radius:6px 6px 0 0;
            transition:height 0.4s ease, background 0.2s;
            cursor:pointer;
            position:relative;
        }
        .bar-col .bar-fill:hover {
            background:#1d4ed8;
        }
        .bar-col .bar-label {
            font-size:13px;
            font-weight:500;
            color:#64748b;
            margin-top:10px;
        }

        @media (max-width: 900px) {
            body { flex-direction:column; }
            .admin-sidebar { width:100%; min-height:auto; }
            .metrics-grid { grid-template-columns:1fr; }
            .admin-main { padding:24px 20px; }
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

    <!-- Sidebar 100% Mockup CamHub — Dashboard Admin.png -->
    <aside class="admin-sidebar">
        <div>
            <a href="<?= base_url('/dashboard/admin') ?>" class="sidebar-brand">CamHub</a>
            
            <div class="sidebar-user" style="padding-bottom:18px; margin-bottom:20px; border-bottom:1px solid #1e293b;">
                <div class="user-fullname" style="font-weight:700; font-size:15px; color:#ffffff; margin-bottom:3px;"><?= htmlspecialchars(auth()['full_name'] ?? 'Administrator', ENT_QUOTES, 'UTF-8') ?></div>
                <div class="user-role-label" style="font-size:12px; color:#38bdf8; font-weight:600; text-transform:uppercase; letter-spacing:0.5px;">Super Admin</div>
            </div>

            <ul class="sidebar-nav">
                <li><a href="<?= base_url('/dashboard/admin') ?>" class="<?= ($activeMenu ?? 'beranda') === 'beranda' || ($activeMenu ?? 'beranda') === 'dashboard' ? 'active' : '' ?>">Beranda</a></li>
                <li><a href="<?= base_url('/dashboard/admin/kyc') ?>" class="<?= ($activeMenu ?? '') === 'kyc' ? 'active' : '' ?>">Verifikasi KYC</a></li>
                <li><a href="<?= base_url('/dashboard/admin/jadwal') ?>" class="<?= ($activeMenu ?? '') === 'jadwal' ? 'active' : '' ?>">Jadwal Unit</a></li>
                <li><a href="<?= base_url('/dashboard/admin/transaksi') ?>" class="<?= ($activeMenu ?? '') === 'transaksi' ? 'active' : '' ?>">Transaksi</a></li>
                <li><a href="<?= base_url('/dashboard/admin/denda') ?>" class="<?= ($activeMenu ?? '') === 'denda' ? 'active' : '' ?>">Manajemen Denda</a></li>
                <li><a href="<?= base_url('/dashboard/admin/laporan') ?>" class="<?= ($activeMenu ?? '') === 'laporan' ? 'active' : '' ?>">Laporan</a></li>
                <li><a href="<?= base_url('/dashboard/admin/pengaturan') ?>" class="<?= ($activeMenu ?? '') === 'pengaturan' ? 'active' : '' ?>">Pengaturan</a></li>
            </ul>
        </div>

        <div class="sidebar-footer" style="margin-top:auto; padding-top:18px; border-top:1px solid #1e293b;">
            <button type="button" class="btn-theme-toggle" onclick="toggleCamHubTheme()" title="Beralih Mode Gelap/Terang" id="camhubThemeToggleBtn" style="width:100%; display:inline-flex; align-items:center; justify-content:center; gap:8px; background:#1e293b; color:#cbd5e1; border:1px solid #334155; padding:9px 12px; border-radius:8px; font-size:12px; font-weight:600; cursor:pointer; text-align:center; margin-bottom:10px;">
                <span class="theme-icon-wrap" id="themeIconWrap"></span>
                <span id="themeToggleText">Mode Terang</span>
            </button>
            <a href="<?= base_url('/') ?>" style="width:100%; display:inline-flex; align-items:center; justify-content:center; background:transparent; color:#94a3b8; border:1px solid #334155; padding:8px 12px; border-radius:8px; font-size:12px; font-weight:600; text-decoration:none; margin-bottom:10px; transition:all 0.2s;">
                Lihat Beranda Publik
            </a>
            <form action="<?= base_url('/logout') ?>" method="POST" style="margin:0;">
                <?= csrf_field() ?>
                <button type="submit" class="btn-sidebar-logout" style="width:100%; background:transparent; color:#ef4444; border:1px solid rgba(239,68,68,0.3); padding:8px 14px; border-radius:8px; font-size:13px; font-weight:700; cursor:pointer; transition:all 0.2s; display:flex; align-items:center; justify-content:center;">
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Area -->
    <main class="admin-main">
        <h1 class="page-title">Dashboard Admin</h1>

        <!-- 4 Metric Cards Grid 2x2 -->
        <div class="metrics-grid">
            <div class="metric-card">
                <div class="metric-label">Menunggu Verifikasi KYC</div>
                <div class="metric-value"><?= (int) ($metrics['kyc_pending'] ?? 8) ?></div>
            </div>
            <div class="metric-card">
                <div class="metric-label">Unit Inspeksi</div>
                <div class="metric-value"><?= (int) ($metrics['inspection_units'] ?? 5) ?></div>
            </div>
            <div class="metric-card">
                <div class="metric-label">Transaksi Hari Ini</div>
                <div class="metric-value"><?= (int) ($metrics['today_transactions'] ?? 12) ?></div>
            </div>
            <div class="metric-card">
                <div class="metric-label">Denda Pending</div>
                <div class="metric-value"><?= (int) ($metrics['pending_fines'] ?? 3) ?></div>
            </div>
        </div>

        <!-- Transaksi 7 Hari Terakhir -->
        <h2 class="chart-section-title">Transaksi 7 Hari Terakhir</h2>
        <div class="chart-card">
            <div class="chart-bars-container">
                <?php foreach (($chartData ?? []) as $bar): ?>
                    <div class="bar-col">
                        <div class="bar-fill" style="height: <?= (int) ($bar['height_pct'] ?? 40) ?>%;" title="<?= htmlspecialchars($bar['day']) ?>: <?= (int) $bar['count'] ?> transaksi"></div>
                        <div class="bar-label"><?= htmlspecialchars($bar['day']) ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </main>

    <script>
        const SUN_ICON = `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>`;

        const MOON_ICON = `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>`;

        function toggleCamHubTheme() {
            const currentTheme = document.documentElement.getAttribute('data-theme') || 'dark';
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('camhub_theme', newTheme);
            syncThemeToggleUI(newTheme);
        }

        function syncThemeToggleUI(theme) {
            const text = document.getElementById('themeToggleText');
            const iconWrap = document.getElementById('themeIconWrap');
            if (!text) return;
            if (theme === 'dark') {
                text.textContent = 'Mode Terang';
                if (iconWrap) iconWrap.innerHTML = SUN_ICON;
            } else {
                text.textContent = 'Mode Gelap';
                if (iconWrap) iconWrap.innerHTML = MOON_ICON;
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const active = document.documentElement.getAttribute('data-theme') || 'dark';
            syncThemeToggleUI(active);
        });
    </script>
</body>
</html>

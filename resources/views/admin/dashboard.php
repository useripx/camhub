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
        * { margin:0; padding:0; box-sizing:border-box; font-family:'Plus Jakarta Sans', sans-serif; }
        body { background:#f8fafc; color:#0f172a; min-height:100vh; display:flex; }
        
        /* Sidebar Styling (Sesuai CamHub — Dashboard Admin.png) */
        .admin-sidebar {
            width:260px;
            background:#0c192c;
            min-height:100vh;
            padding:36px 28px;
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
            margin-bottom:44px;
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
            color:#0f172a;
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
            background:#ffffff;
            border:1px solid #e2e8f0;
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
            color:#64748b;
            margin-bottom:16px;
        }
        .metric-value {
            font-size:32px;
            font-weight:800;
            color:#0f172a;
            line-height:1;
        }

        /* Chart Section */
        .chart-section-title {
            font-size:20px;
            font-weight:700;
            color:#0f172a;
            margin-bottom:18px;
            letter-spacing:-0.3px;
        }
        .chart-card {
            background:#ffffff;
            border:1px solid #e2e8f0;
            border-radius:16px;
            padding:44px 36px 28px 36px;
            box-shadow:0 1px 3px rgba(0,0,0,0.02);
        }
        .chart-bars-container {
            display:flex;
            justify-content:space-around;
            align-items:flex-end;
            height:220px;
            border-bottom:1px solid #e2e8f0;
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
</head>
<body>

    <!-- Sidebar 100% Mockup CamHub — Dashboard Admin.png -->
    <aside class="admin-sidebar">
        <div>
            <a href="<?= base_url('/dashboard/admin') ?>" class="sidebar-brand">CamHub</a>
            <ul class="sidebar-nav">
                <li><a href="<?= base_url('/dashboard/admin') ?>" class="<?= ($activeMenu ?? 'beranda') === 'beranda' ? 'active' : '' ?>">Beranda</a></li>
                <li><a href="<?= base_url('/dashboard/admin/kyc') ?>" class="<?= ($activeMenu ?? '') === 'kyc' ? 'active' : '' ?>">Verifikasi KYC</a></li>
                <li><a href="<?= base_url('/dashboard/admin/jadwal') ?>" class="<?= ($activeMenu ?? '') === 'jadwal' ? 'active' : '' ?>">Jadwal Unit</a></li>
                <li><a href="<?= base_url('/dashboard/admin/transaksi') ?>" class="<?= ($activeMenu ?? '') === 'transaksi' ? 'active' : '' ?>">Transaksi</a></li>
                <li><a href="<?= base_url('/dashboard/admin/denda') ?>" class="<?= ($activeMenu ?? '') === 'denda' ? 'active' : '' ?>">Manajemen Denda</a></li>
                <li><a href="<?= base_url('/dashboard/admin/laporan') ?>" class="<?= ($activeMenu ?? '') === 'laporan' ? 'active' : '' ?>">Laporan</a></li>
                <li><a href="<?= base_url('/dashboard/admin/pengaturan') ?>" class="<?= ($activeMenu ?? '') === 'pengaturan' ? 'active' : '' ?>">Pengaturan</a></li>
            </ul>
        </div>

        <div class="sidebar-footer">
            <div class="admin-profile">
                <div class="admin-avatar">A</div>
                <div class="admin-meta">
                    <div class="admin-name"><?= htmlspecialchars(auth()['full_name'] ?? 'Administrator', ENT_QUOTES, 'UTF-8') ?></div>
                    <div class="admin-role">Super Admin</div>
                </div>
            </div>
            <form action="<?= base_url('/logout') ?>" method="POST" style="margin:0;">
                <?= csrf_field() ?>
                <button type="submit" class="btn-sidebar-logout">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                    Keluar Sesi
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

</body>
</html>

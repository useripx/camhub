<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Pengguna & Audit Keamanan — Super Admin CamHub</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; font-family:'Plus Jakarta Sans', sans-serif; }
        body { background:#f8fafc; color:#0f172a; min-height:100vh; display:flex; }
        
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
        .sidebar-brand { font-size:24px; font-weight:800; color:#ffffff; text-decoration:none; display:block; margin-bottom:44px; letter-spacing:-0.5px; }
        .sidebar-nav { list-style:none; display:flex; flex-direction:column; gap:22px; }
        .sidebar-nav a { color:#94a3b8; text-decoration:none; font-size:15px; font-weight:500; display:flex; align-items:center; transition:color 0.2s; }
        .sidebar-nav a:hover { color:#ffffff; }
        .sidebar-nav a.active { color:#ffffff; font-weight:700; }

        .sidebar-footer { margin-top:auto; padding-top:28px; border-top:1px solid #1e293b; }
        .admin-profile { display:flex; align-items:center; gap:10px; margin-bottom:14px; }
        .admin-avatar { width:34px; height:34px; border-radius:50%; background:#1e3a8a; color:#93c5fd; display:flex; align-items:center; justify-content:center; font-size:14px; font-weight:700; }
        .admin-meta { font-size:13px; }
        .admin-name { color:#ffffff; font-weight:700; }
        .admin-role { color:#38bdf8; font-size:11px; font-weight:600; }
        .btn-sidebar-logout { width:100%; background:#1e293b; color:#f87171; border:1px solid #334155; padding:9px 14px; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer; transition:all 0.2s; display:flex; align-items:center; justify-content:center; gap:6px; }
        .btn-sidebar-logout:hover { background:#dc2626; color:#ffffff; border-color:#dc2626; }

        .admin-main { flex:1; padding:40px 48px; max-width:1300px; }
        .page-header { margin-bottom:28px; }
        .page-title { font-size:28px; font-weight:800; color:#0f172a; margin-bottom:6px; }
        .page-subtitle { font-size:14px; color:#64748b; }

        .flash-alert { padding:14px 20px; border-radius:10px; font-size:14px; font-weight:600; margin-bottom:24px; }
        .flash-success { background:#ecfdf5; border:1px solid #a7f3d0; color:#065f46; }
        .flash-error { background:#fef2f2; border:1px solid #fecaca; color:#991b1b; }

        .section-card { background:#ffffff; border:1px solid #e2e8f0; border-radius:16px; padding:28px; margin-bottom:32px; box-shadow:0 1px 3px rgba(0,0,0,0.02); }
        .section-title { font-size:18px; font-weight:800; color:#0f172a; margin-bottom:18px; }

        .table-custom { width:100%; border-collapse:collapse; font-size:13px; }
        .table-custom th { text-align:left; padding:12px 14px; background:#f8fafc; color:#64748b; font-weight:600; border-bottom:1px solid #e2e8f0; }
        .table-custom td { padding:14px; border-bottom:1px solid #f1f5f9; color:#1e293b; vertical-align:middle; }

        .badge { padding:4px 10px; border-radius:20px; font-size:11px; font-weight:700; text-transform:uppercase; display:inline-block; }
        .badge-active { background:#dcfce7; color:#15803d; }
        .badge-suspended { background:#fee2e2; color:#b91c1c; }
        .badge-role { background:#e0f2fe; color:#0369a1; }

        .btn-toggle { background:#f1f5f9; border:1px solid #cbd5e1; padding:6px 12px; border-radius:6px; font-size:12px; font-weight:700; cursor:pointer; }
        .btn-toggle.suspend { color:#dc2626; border-color:#fecaca; }
        .btn-toggle.suspend:hover { background:#fee2e2; }
        .btn-toggle.activate { color:#16a34a; border-color:#a7f3d0; }
        .btn-toggle.activate:hover { background:#dcfce7; }
    </style>
    <script>
        (function() {
            const theme = localStorage.getItem('camhub_theme') || 'dark';
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>
</head>
<body>

    <aside class="admin-sidebar">
        <div>
            <a href="<?= base_url('/dashboard/admin') ?>" class="sidebar-brand">CamHub</a>
            <ul class="sidebar-nav">
                <li><a href="<?= base_url('/dashboard/admin') ?>">Beranda</a></li>
                <li><a href="<?= base_url('/dashboard/admin/kyc') ?>">Verifikasi KYC</a></li>
                <li><a href="<?= base_url('/dashboard/admin/jadwal') ?>">Jadwal Unit</a></li>
                <li><a href="<?= base_url('/dashboard/admin/transaksi') ?>">Transaksi</a></li>
                <li><a href="<?= base_url('/dashboard/admin/denda') ?>">Manajemen Denda</a></li>
                <li><a href="<?= base_url('/dashboard/admin/laporan') ?>">Laporan</a></li>
                <li><a href="<?= base_url('/dashboard/admin/pengaturan') ?>" class="active">Pengaturan</a></li>
            </ul>
        </div>

        <div class="sidebar-footer">
            <button type="button" class="btn-theme-toggle" onclick="toggleCamHubTheme()" title="Beralih Mode Gelap/Terang" style="width:100%; background:#1e293b; color:#cbd5e1; border:1px solid #334155; padding:9px 12px; border-radius:8px; font-size:12px; font-weight:600; cursor:pointer; text-align:center; margin-bottom:12px;">
                <span id="themeToggleText">Mode Terang</span>
            </button>
            <div class="admin-profile">
                <div class="admin-avatar">A</div>
                <div class="admin-meta">
                    <div class="admin-name"><?= htmlspecialchars(auth()['full_name'] ?? 'Administrator', ENT_QUOTES, 'UTF-8') ?></div>
                    <div class="admin-role">Super Admin</div>
                </div>
            </div>
            <form action="<?= base_url('/logout') ?>" method="POST" style="margin:0;">
                <?= csrf_field() ?>
                <button type="submit" class="btn-sidebar-logout">Keluar Sesi</button>
            </form>
        </div>
    </aside>

    <main class="admin-main">
        <div class="page-header">
            <h1 class="page-title">Pengaturan Pengguna & Audit Trail Keamanan</h1>
            <p class="page-subtitle">Kontrol status operasional akun pengguna platform dan pantau rekam jejak aktivitas keamanan sistem.</p>
        </div>

        <?php if (!empty($success)): ?>
            <div class="flash-alert flash-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        <?php if (!empty($error)): ?>
            <div class="flash-alert flash-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <!-- SECTION 1: MANAJEMEN AKUN PENGGUNA -->
        <div class="section-card">
            <h2 class="section-title">👥 Manajemen Akun Pengguna Platform</h2>
            <table class="table-custom">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Pengguna</th>
                        <th>Peran (Role)</th>
                        <th>Email & Kontak</th>
                        <th>Kota / Domisili</th>
                        <th>Status</th>
                        <th style="text-align:right;">Aksi Kontrol</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                        <tr>
                            <td>#USR-<?= $u['id'] ?></td>
                            <td>
                                <strong><?= htmlspecialchars($u['full_name']) ?></strong>
                                <div style="font-size:11px; color:#64748b;">@<?= htmlspecialchars($u['username']) ?></div>
                            </td>
                            <td><span class="badge badge-role"><?= htmlspecialchars(strtoupper($u['role_name'])) ?></span></td>
                            <td>
                                <div><?= htmlspecialchars($u['email']) ?></div>
                                <div style="font-size:11px; color:#64748b;"><?= htmlspecialchars($u['phone'] ?? '-') ?></div>
                            </td>
                            <td><?= htmlspecialchars($u['city'] ?? '-') ?></td>
                            <td>
                                <span class="badge badge-<?= htmlspecialchars($u['status']) ?>">
                                    <?= htmlspecialchars(strtoupper($u['status'])) ?>
                                </span>
                            </td>
                            <td style="text-align:right;">
                                <?php if ((int)$u['id'] !== 1): ?>
                                    <form action="<?= base_url('/dashboard/admin/pengguna/' . $u['id'] . '/status') ?>" method="POST" style="margin:0;">
                                        <?= csrf_field() ?>
                                        <?php if ($u['status'] === 'active'): ?>
                                            <button type="submit" class="btn-toggle suspend" onclick="return confirm('Tangguhkan akses akun ini?')">Tangguhkan</button>
                                        <?php else: ?>
                                            <button type="submit" class="btn-toggle activate" onclick="return confirm('Aktifkan kembali akun ini?')">Aktifkan</button>
                                        <?php endif; ?>
                                    </form>
                                <?php else: ?>
                                    <span style="font-size:11px; color:#64748b; font-weight:700;">Super Admin Inti</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- SECTION 2: AUDIT TRAIL KEAMANAN REAL-TIME -->
        <div class="section-card">
            <h2 class="section-title">🛡️ Log Audit Keamanan & Finansial Terkini</h2>
            <table class="table-custom">
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>Aktor / Pengguna</th>
                        <th>Aksi Terlaksana</th>
                        <th>Target Entitas</th>
                        <th>Rincian Data Mutasi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($auditLogs as $log): ?>
                        <tr>
                            <td style="white-space:nowrap; font-size:11px; color:#64748b;">
                                <?= date('d M Y, H:i:s', strtotime($log['created_at'])) ?>
                            </td>
                            <td>
                                <?php if (!empty($log['full_name'])): ?>
                                    <strong><?= htmlspecialchars($log['full_name']) ?></strong>
                                    <div style="font-size:11px; color:#64748b;">@<?= htmlspecialchars($log['username'] ?? '') ?></div>
                                <?php else: ?>
                                    <span style="color:#64748b;">System / Tamu</span>
                                <?php endif; ?>
                            </td>
                            <td><code style="background:#f1f5f9; padding:4px 8px; border-radius:4px; font-weight:700; color:#0369a1;"><?= htmlspecialchars($log['action']) ?></code></td>
                            <td><?= htmlspecialchars($log['target_table']) ?> #<?= $log['target_id'] ?></td>
                            <td style="font-family:monospace; font-size:11px; color:#475569; max-width:320px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="<?= htmlspecialchars($log['changes_json'] ?? '') ?>">
                                <?= htmlspecialchars($log['changes_json'] ?? '-') ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
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


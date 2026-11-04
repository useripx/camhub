<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal & Pengawasan Unit Kamera — Super Admin CamHub</title>
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

        .card-table { background:#ffffff; border:1px solid #e2e8f0; border-radius:16px; padding:24px; box-shadow:0 1px 3px rgba(0,0,0,0.02); }
        .table-custom { width:100%; border-collapse:collapse; font-size:13px; }
        .table-custom th { text-align:left; padding:12px 14px; background:#f8fafc; color:#64748b; font-weight:600; border-bottom:1px solid #e2e8f0; }
        .table-custom td { padding:14px; border-bottom:1px solid #f1f5f9; color:#1e293b; vertical-align:middle; }

        .badge { padding:4px 10px; border-radius:20px; font-size:11px; font-weight:700; text-transform:uppercase; display:inline-block; }
        .badge-available { background:#dcfce7; color:#15803d; }
        .badge-rented { background:#fef3c7; color:#b45309; }
        .badge-maintenance { background:#fee2e2; color:#b91c1c; }

        @media (max-width: 900px) {
            body { flex-direction:column; }
            .admin-sidebar { width:100%; min-height:auto; }
            .admin-main { padding:24px 20px; }
            .table-custom { display:block; overflow-x:auto; }
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

    <aside class="admin-sidebar">
        <div>
            <a href="<?= base_url('/dashboard/admin') ?>" class="sidebar-brand">CamHub</a>
            <ul class="sidebar-nav">
                <li><a href="<?= base_url('/dashboard/admin') ?>">Beranda</a></li>
                <li><a href="<?= base_url('/dashboard/admin/kyc') ?>">Verifikasi KYC</a></li>
                <li><a href="<?= base_url('/dashboard/admin/jadwal') ?>" class="active">Jadwal Unit</a></li>
                <li><a href="<?= base_url('/dashboard/admin/transaksi') ?>">Transaksi</a></li>
                <li><a href="<?= base_url('/dashboard/admin/denda') ?>">Manajemen Denda</a></li>
                <li><a href="<?= base_url('/dashboard/admin/laporan') ?>">Laporan</a></li>
                <li><a href="<?= base_url('/dashboard/admin/pengaturan') ?>">Pengaturan</a></li>
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
            <h1 class="page-title">Jadwal & Pengawasan Unit Kamera</h1>
            <p class="page-subtitle">Pantau seluruh nomor seri fisik kamera di berbagai toko mitra, ketersediaan unit, dan jadwal rental aktif.</p>
        </div>

        <div class="card-table">
            <table class="table-custom">
                <thead>
                    <tr>
                        <th>Toko Lapak</th>
                        <th>Kamera & Kategori</th>
                        <th>Nomor Seri (SN)</th>
                        <th>Kondisi Fisik</th>
                        <th>Status Operasional</th>
                        <th>Penyewa & Jadwal Sewa</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($schedules as $unit): ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($unit['store_name']) ?></strong>
                                <div style="font-size:11px; color:#64748b;"><?= htmlspecialchars($unit['store_city'] ?? '-') ?></div>
                            </td>
                            <td>
                                <strong><?= htmlspecialchars($unit['product_name']) ?></strong>
                                <div style="font-size:11px; color:#64748b;"><?= htmlspecialchars($unit['category_name']) ?></div>
                            </td>
                            <td><code style="background:#f1f5f9; padding:4px 8px; border-radius:4px; font-weight:700;"><?= htmlspecialchars($unit['serial_number']) ?></code></td>
                            <td><?= htmlspecialchars($unit['condition_status'] ?? 'Baik') ?></td>
                            <td>
                                <?php if (!empty($unit['current_booking_id'])): ?>
                                    <span class="badge badge-rented">Sedang Disewa</span>
                                <?php elseif (($unit['availability_status'] ?? '') === 'available'): ?>
                                    <span class="badge badge-available">Tersedia Siap Sewa</span>
                                <?php else: ?>
                                    <span class="badge badge-maintenance"><?= htmlspecialchars(ucfirst($unit['availability_status'] ?? 'Maintenance')) ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($unit['current_booking_id'])): ?>
                                    <strong><?= htmlspecialchars($unit['renter_name'] ?? 'Penyewa') ?></strong>
                                    <div style="font-size:11px; color:#64748b;">
                                        <?= date('d M Y', strtotime($unit['start_date'])) ?> s.d <?= date('d M Y', strtotime($unit['end_date'])) ?>
                                    </div>
                                <?php else: ?>
                                    <span style="color:#64748b;">Unit siap dialokasikan</span>
                                <?php endif; ?>
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

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Denda & Inspeksi — Super Admin CamHub</title>
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

        .form-grid { display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:18px; margin-bottom:20px; }
        .form-group label { display:block; font-size:13px; font-weight:700; color:#1e293b; margin-bottom:6px; }
        .form-control { width:100%; padding:11px 14px; border:1px solid #cbd5e1; border-radius:8px; font-size:14px; color:#0f172a; }
        .form-control:focus { outline:none; border-color:#2563eb; }

        .btn-submit { background:#0c192c; color:#ffffff; font-size:14px; font-weight:700; padding:12px 24px; border:none; border-radius:8px; cursor:pointer; }
        .btn-submit:hover { background:#1e293b; }

        .table-custom { width:100%; border-collapse:collapse; font-size:13px; }
        .table-custom th { text-align:left; padding:12px 14px; background:#f8fafc; color:#64748b; font-weight:600; border-bottom:1px solid #e2e8f0; }
        .table-custom td { padding:14px; border-bottom:1px solid #f1f5f9; color:#1e293b; vertical-align:middle; }

        .badge { padding:4px 10px; border-radius:20px; font-size:11px; font-weight:700; text-transform:uppercase; display:inline-block; }
        .badge-pending { background:#fee2e2; color:#b91c1c; }
        .badge-paid { background:#dcfce7; color:#15803d; }
        .badge-waived { background:#f1f5f9; color:#64748b; }

        .btn-pay { background:#16a34a; color:#ffffff; font-size:12px; font-weight:700; padding:7px 12px; border:none; border-radius:6px; cursor:pointer; }
        .btn-pay:hover { background:#15803d; }
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
                <li><a href="<?= base_url('/dashboard/admin/denda') ?>" class="active">Manajemen Denda</a></li>
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
            <h1 class="page-title">Manajemen Denda & Inspeksi Unit</h1>
            <p class="page-subtitle">Penerbitan sanksi denda keterlambatan pengembalian atau kerusakan fisik unit sewa kamera.</p>
        </div>

        <?php if (!empty($success)): ?>
            <div class="flash-alert flash-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        <?php if (!empty($error)): ?>
            <div class="flash-alert flash-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <!-- FORM PENERBITAN DENDA -->
        <div class="section-card">
            <h2 class="section-title">➕ Terbitkan Tagihan Denda Baru</h2>
            <form action="<?= base_url('/dashboard/admin/denda/simpan') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="booking_id">ID Booking Sewa (Contoh: 1, 2, dst)</label>
                        <input type="number" id="booking_id" name="booking_id" class="form-control" placeholder="ID Pemesanan Sewa" required>
                    </div>
                    <div class="form-group">
                        <label for="fine_type">Jenis Pelanggaran</label>
                        <select id="fine_type" name="fine_type" class="form-control">
                            <option value="late_return">Keterlambatan Pengembalian (Late Return)</option>
                            <option value="damage">Kerusakan Fisik Unit (Physical Damage)</option>
                            <option value="missing_accessory">Kelengkapan Hilang (Lost Accessory)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="amount">Nominal Denda (Rp)</label>
                        <input type="number" id="amount" name="amount" class="form-control" placeholder="Contoh: 100000" min="10000" step="5000" required>
                    </div>
                    <div class="form-group">
                        <label for="reason">Catatan / Alasan Denda</label>
                        <input type="text" id="reason" name="reason" class="form-control" placeholder="Contoh: Terlambat 1 hari dari jadwal" required>
                    </div>
                </div>
                <button type="submit" class="btn-submit">Terbitkan Denda Sekarang →</button>
            </form>
        </div>

        <!-- DAFTAR DENDA -->
        <div class="section-card">
            <h2 class="section-title">📋 Riwayat Denda Operasional</h2>
            <?php if (empty($fines)): ?>
                <p style="padding:20px; text-align:center; color:#64748b; background:#f8fafc; border-radius:8px;">Belum ada catatan denda yang diterbitkan di platform.</p>
            <?php else: ?>
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>No. Denda</th>
                            <th>Pesanan Sewa</th>
                            <th>Penyewa</th>
                            <th>Jenis Denda</th>
                            <th>Nominal</th>
                            <th>Alasan</th>
                            <th>Status</th>
                            <th style="text-align:right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($fines as $fine): ?>
                            <tr>
                                <td><strong>#FINE-<?= str_pad((string)$fine['id'], 4, '0', STR_PAD_LEFT) ?></strong></td>
                                <td>#RENT-<?= str_pad((string)$fine['booking_id'], 5, '0', STR_PAD_LEFT) ?> · <?= htmlspecialchars($fine['camera_name'] ?? 'Kamera') ?></td>
                                <td>
                                    <strong><?= htmlspecialchars($fine['customer_name'] ?? 'Penyewa') ?></strong>
                                    <div style="font-size:11px; color:#64748b;"><?= htmlspecialchars($fine['customer_phone'] ?? '-') ?></div>
                                </td>
                                <td><?= htmlspecialchars(strtoupper($fine['fine_type'])) ?></td>
                                <td><strong style="color:#b91c1c;">Rp <?= number_format((float)$fine['amount'], 0, ',', '.') ?></strong></td>
                                <td><?= htmlspecialchars($fine['reason']) ?></td>
                                <td>
                                    <span class="badge badge-<?= htmlspecialchars($fine['status']) ?>">
                                        <?= htmlspecialchars(strtoupper($fine['status'])) ?>
                                    </span>
                                </td>
                                <td style="text-align:right;">
                                    <?php if ($fine['status'] === 'pending'): ?>
                                        <form action="<?= base_url('/dashboard/admin/denda/' . $fine['id'] . '/lunas') ?>" method="POST" style="margin:0;">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn-pay" onclick="return confirm('Tandai denda ini telah dilunasi?')">✓ Tandai Lunas</button>
                                        </form>
                                    <?php else: ?>
                                        <span style="font-size:12px; color:#15803d; font-weight:700;">Lunas</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
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

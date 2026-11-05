<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi KYC Pengguna — Super Admin CamHub</title>
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
        .sidebar-nav a:hover { color:#ffffff; }
        .sidebar-nav a.active { color:#ffffff; font-weight:700; }

        .sidebar-footer {
            margin-top:auto;
            padding-top:28px;
            border-top:1px solid #1e293b;
        }
        .admin-profile { display:flex; align-items:center; gap:10px; margin-bottom:14px; }
        .admin-avatar { width:34px; height:34px; border-radius:50%; background:#1e3a8a; color:#93c5fd; display:flex; align-items:center; justify-content:center; font-size:14px; font-weight:700; }
        .admin-meta { font-size:13px; }
        .admin-name { color:#ffffff; font-weight:700; }
        .admin-role { color:#38bdf8; font-size:11px; font-weight:600; }
        .btn-sidebar-logout {
            width:100%; background:#1e293b; color:#f87171; border:1px solid #334155; padding:9px 14px; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer; transition:all 0.2s; display:flex; align-items:center; justify-content:center; gap:6px;
        }
        .btn-sidebar-logout:hover { background:#dc2626; color:#ffffff; border-color:#dc2626; }

        /* Main Content */
        .admin-main { flex:1; padding:40px 48px; max-width:1300px; }
        .page-header { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:28px; }
        .page-title { font-size:28px; font-weight:800; color:var(--text-primary); margin-bottom:6px; }
        .page-subtitle { font-size:14px; color:var(--text-secondary); }

        .flash-alert { padding:14px 20px; border-radius:10px; font-size:14px; font-weight:600; margin-bottom:24px; }
        .flash-success { background:#ecfdf5; border:1px solid #a7f3d0; color:#065f46; }
        .flash-error { background:#fef2f2; border:1px solid #fecaca; color:#991b1b; }

        /* Filter Tabs */
        .filter-tabs { display:flex; gap:10px; margin-bottom:24px; }
        .filter-tab {
            padding:9px 18px; border-radius:20px; font-size:13px; font-weight:600; text-decoration:none; color:#64748b; background:#ffffff; border:1px solid #e2e8f0; transition:all 0.2s;
        }
        .filter-tab:hover { background:#f1f5f9; color:#0f172a; }
        .filter-tab.active { background:#0c192c; color:#ffffff; border-color:#0c192c; }

        /* Card Container */
        .card-table { background:#ffffff; border:1px solid #e2e8f0; border-radius:16px; padding:24px; box-shadow:0 1px 3px rgba(0,0,0,0.02); }
        .table-custom { width:100%; border-collapse:collapse; font-size:13px; }
        .table-custom th { text-align:left; padding:12px 14px; background:var(--table-th-bg); color:var(--table-th-text); font-weight:600; border-bottom:1px solid var(--border-color); }
        .table-custom td { padding:14px; border-bottom:1px solid var(--table-td-border); color:var(--table-td-text); vertical-align:middle; }
        
        .badge { padding:4px 10px; border-radius:20px; font-size:11px; font-weight:700; text-transform:uppercase; display:inline-block; }
        .badge-pending { background:#fef3c7; color:#b45309; }
        .badge-approved { background:#dcfce7; color:#15803d; }
        .badge-rejected { background:#fee2e2; color:#b91c1c; }

        .btn-action-group { display:flex; gap:8px; align-items:center; }
        .btn-approve { background:#16a34a; color:#ffffff; font-size:12px; font-weight:700; padding:8px 14px; border:none; border-radius:6px; cursor:pointer; }
        .btn-approve:hover { background:#15803d; }
        .btn-reject { background:#dc2626; color:#ffffff; font-size:12px; font-weight:700; padding:8px 14px; border:none; border-radius:6px; cursor:pointer; }
        .btn-reject:hover { background:#b91c1c; }
        .btn-view { background:#f1f5f9; color:#0f172a; font-size:12px; font-weight:600; padding:8px 12px; border:1px solid #cbd5e1; border-radius:6px; text-decoration:none; }
        .btn-view:hover { background:#e2e8f0; }

        /* Document Preview Thumbnails */
        .doc-thumb { width:44px; height:30px; object-fit:cover; border-radius:4px; border:1px solid #cbd5e1; vertical-align:middle; cursor:pointer; }

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

    <!-- Sidebar Admin -->
    <aside class="admin-sidebar">
        <div>
            <a href="<?= base_url('/dashboard/admin') ?>" class="sidebar-brand">CamHub</a>
            
            <div class="sidebar-user" style="padding-bottom:18px; margin-bottom:20px; border-bottom:1px solid #1e293b;">
                <div class="user-fullname" style="font-weight:700; font-size:15px; color:#ffffff; margin-bottom:3px;"><?= htmlspecialchars(auth()['full_name'] ?? 'Administrator', ENT_QUOTES, 'UTF-8') ?></div>
                <div class="user-role-label" style="font-size:12px; color:#38bdf8; font-weight:600; text-transform:uppercase; letter-spacing:0.5px;">Super Admin</div>
            </div>

            <ul class="sidebar-nav">
                <li><a href="<?= base_url('/dashboard/admin') ?>" class="<?= ($activeMenu ?? 'kyc') === 'beranda' || ($activeMenu ?? 'kyc') === 'dashboard' ? 'active' : '' ?>">Beranda</a></li>
                <li><a href="<?= base_url('/dashboard/admin/kyc') ?>" class="<?= ($activeMenu ?? 'kyc') === 'kyc' ? 'active' : '' ?>">Verifikasi KYC</a></li>
                <li><a href="<?= base_url('/dashboard/admin/jadwal') ?>" class="<?= ($activeMenu ?? 'kyc') === 'jadwal' ? 'active' : '' ?>">Jadwal Unit</a></li>
                <li><a href="<?= base_url('/dashboard/admin/transaksi') ?>" class="<?= ($activeMenu ?? 'kyc') === 'transaksi' ? 'active' : '' ?>">Transaksi</a></li>
                <li><a href="<?= base_url('/dashboard/admin/denda') ?>" class="<?= ($activeMenu ?? 'kyc') === 'denda' ? 'active' : '' ?>">Manajemen Denda</a></li>
                <li><a href="<?= base_url('/dashboard/admin/laporan') ?>" class="<?= ($activeMenu ?? 'kyc') === 'laporan' ? 'active' : '' ?>">Laporan</a></li>
                <li><a href="<?= base_url('/dashboard/admin/pengaturan') ?>" class="<?= ($activeMenu ?? 'kyc') === 'pengaturan' ? 'active' : '' ?>">Pengaturan</a></li>
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

    <!-- Main Content -->
    <main class="admin-main">
        <div class="page-header">
            <div>
                <h1 class="page-title">Verifikasi Identitas KYC Pelanggan</h1>
                <p class="page-subtitle">Tinjau keaslian dokumen KTP dan foto selfie wajah pengguna untuk mengizinkan transaksi sewa kamera.</p>
            </div>
        </div>

        <?php if (!empty($success)): ?>
            <div class="flash-alert flash-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        <?php if (!empty($error)): ?>
            <div class="flash-alert flash-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <!-- Filter Tabs -->
        <div class="filter-tabs">
            <a href="<?= base_url('/dashboard/admin/kyc?status=all') ?>" class="filter-tab <?= ($currentFilter === 'all') ? 'active' : '' ?>">Semua Pengajuan</a>
            <a href="<?= base_url('/dashboard/admin/kyc?status=pending') ?>" class="filter-tab <?= ($currentFilter === 'pending') ? 'active' : '' ?>">Menunggu Review</a>
            <a href="<?= base_url('/dashboard/admin/kyc?status=approved') ?>" class="filter-tab <?= ($currentFilter === 'approved') ? 'active' : '' ?>">Disetujui</a>
            <a href="<?= base_url('/dashboard/admin/kyc?status=rejected') ?>" class="filter-tab <?= ($currentFilter === 'rejected') ? 'active' : '' ?>">Ditolak</a>
        </div>

        <!-- Table KYC -->
        <div class="card-table">
            <?php if (empty($submissions)): ?>
                <p style="text-align:center; padding:40px; color:#64748b;">Tidak ada pengajuan verifikasi KYC yang cocok dengan kriteria filter.</p>
            <?php else: ?>
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>ID & Waktu</th>
                            <th>Pelanggan</th>
                            <th>NIK KTP</th>
                            <th>Dokumen KTP & Wajah</th>
                            <th>Status Verifikasi</th>
                            <th style="text-align:right;">Aksi Persetujuan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($submissions as $sub): ?>
                            <tr>
                                <td>
                                    <strong>#KYC-<?= str_pad((string)$sub['id'], 4, '0', STR_PAD_LEFT) ?></strong>
                                    <div style="font-size:11px; color:#64748b;"><?= date('d M Y, H:i', strtotime($sub['created_at'])) ?></div>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($sub['full_name']) ?></strong>
                                    <div style="font-size:12px; color:#64748b;">@<?= htmlspecialchars($sub['username']) ?> · <?= htmlspecialchars($sub['phone']) ?></div>
                                </td>
                                <td>
                                    <code style="background:#f1f5f9; padding:4px 8px; border-radius:4px; font-weight:700;"><?= htmlspecialchars($sub['nik_ktp']) ?></code>
                                    <div style="font-size:11px; color:#64748b; margin-top:2px;"><?= htmlspecialchars($sub['gender'] ?? '-') ?> · <?= htmlspecialchars($sub['birth_date'] ?? '-') ?></div>
                                </td>
                                <td>
                                    <div style="display:flex; gap:8px; align-items:center;">
                                        <?php if (!empty($sub['ktp_image_path'])): ?>
                                            <a href="<?= base_url('/' . ltrim($sub['ktp_image_path'], '/')) ?>" target="_blank" title="Buka Foto KTP">
                                                <img src="<?= base_url('/' . ltrim($sub['ktp_image_path'], '/')) ?>" alt="KTP" class="doc-thumb">
                                            </a>
                                        <?php endif; ?>
                                        <?php if (!empty($sub['selfie_image_path'])): ?>
                                            <a href="<?= base_url('/' . ltrim($sub['selfie_image_path'], '/')) ?>" target="_blank" title="Buka Foto Wajah">
                                                <img src="<?= base_url('/' . ltrim($sub['selfie_image_path'], '/')) ?>" alt="Selfie" class="doc-thumb">
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-<?= htmlspecialchars($sub['status']) ?>">
                                        <?= htmlspecialchars(strtoupper($sub['status'])) ?>
                                    </span>
                                    <?php if (!empty($sub['rejection_reason'])): ?>
                                        <div style="font-size:11px; color:#b91c1c; margin-top:4px; max-width:220px;">
                                            Alasan: <?= htmlspecialchars($sub['rejection_reason']) ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align:right;">
                                    <?php if ($sub['status'] === 'pending'): ?>
                                        <div class="btn-action-group" style="justify-content:flex-end;">
                                            <form action="<?= base_url('/dashboard/admin/kyc/' . $sub['id'] . '/approve') ?>" method="POST" style="margin:0;">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn-approve" onclick="return confirm('Apakah Anda yakin menyetujui verifikasi identitas ini?')">✓ Setujui</button>
                                            </form>

                                            <form action="<?= base_url('/dashboard/admin/kyc/' . $sub['id'] . '/reject') ?>" method="POST" style="margin:0;" onsubmit="var r = prompt('Masukkan alasan penolakan:'); if(!r) return false; this.reason.value=r; return true;">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="reason" value="">
                                                <button type="submit" class="btn-reject">✕ Tolak</button>
                                            </form>
                                        </div>
                                    <?php else: ?>
                                        <span style="font-size:12px; color:#64748b; font-weight:600;">Selesai Ditinjau</span>
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


<?php
$profile = $profile ?? [];
$user = $user ?? auth();
$kycStatus = $kycStatus ?? ($profile['kyc_status'] ?? 'unverified');
$kycRecord = $kycRecord ?? ($profile['kyc'] ?? null);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Profil Saya & Pengaturan Akun — CamHub', ENT_QUOTES, 'UTF-8') ?></title>
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

        /* MAIN CONTENT AREA */
        .main-content {
            flex:1;
            padding:40px 48px 60px;
            overflow-y:auto;
            max-width:1040px;
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
        .page-header {
            margin-bottom:28px;
        }
        .page-title {
            font-size:28px;
            font-weight:800;
            color:var(--text-primary);
            letter-spacing:-0.5px;
            margin-bottom:6px;
        }
        .page-subtitle {
            font-size:14px;
            color:var(--text-secondary);
            line-height:1.6;
        }

        /* ALERTS */
        .alert-success {
            background:rgba(16, 185, 129, 0.15);
            border:1px solid rgba(16, 185, 129, 0.3);
            color:#34d399;
            padding:14px 18px;
            border-radius:12px;
            margin-bottom:24px;
            font-size:14px;
            font-weight:600;
        }
        .alert-danger {
            background:rgba(239, 68, 68, 0.15);
            border:1px solid rgba(239, 68, 68, 0.3);
            color:#f87171;
            padding:14px 18px;
            border-radius:12px;
            margin-bottom:24px;
            font-size:14px;
            font-weight:600;
        }

        /* USER SUMMARY HEADER CARD */
        .user-header-card {
            background:var(--bg-card);
            border:1px solid var(--border-color);
            border-radius:16px;
            padding:28px 32px;
            display:flex;
            align-items:center;
            justify-content:space-between;
            margin-bottom:28px;
            flex-wrap:wrap;
            gap:20px;
            box-shadow:0 2px 10px rgba(0,0,0,0.04);
        }
        .user-info-left {
            display:flex;
            align-items:center;
            gap:20px;
        }
        .avatar-circle {
            width:68px;
            height:68px;
            border-radius:50%;
            background:linear-gradient(135deg, #2563eb, #1d4ed8);
            display:flex;
            align-items:center;
            justify-content:center;
            font-size:28px;
            font-weight:800;
            color:#ffffff;
            box-shadow:0 4px 12px rgba(37,99,235,0.25);
        }
        .user-names h2 {
            font-size:22px;
            font-weight:800;
            color:var(--text-primary);
            margin-bottom:4px;
        }
        .user-names p {
            color:var(--text-secondary);
            font-size:13px;
        }

        /* KYC BANNER */
        .kyc-banner {
            border-radius:14px;
            padding:20px 24px;
            display:flex;
            align-items:center;
            justify-content:space-between;
            margin-bottom:28px;
            flex-wrap:wrap;
            gap:16px;
        }
        .kyc-banner.unverified {
            background:rgba(245, 158, 11, 0.12);
            border:1px solid rgba(245, 158, 11, 0.3);
        }
        .kyc-banner.pending {
            background:rgba(59, 130, 246, 0.12);
            border:1px solid rgba(59, 130, 246, 0.3);
        }
        .kyc-banner.approved {
            background:rgba(16, 185, 129, 0.12);
            border:1px solid rgba(16, 185, 129, 0.3);
        }
        .kyc-btn-action {
            padding:10px 20px;
            border-radius:8px;
            font-weight:700;
            font-size:13px;
            text-decoration:none;
            transition:0.2s;
            display:inline-block;
        }

        /* EDIT FORM CARD */
        .form-card {
            background:var(--bg-card);
            border:1px solid var(--border-color);
            border-radius:16px;
            padding:32px;
            box-shadow:0 2px 10px rgba(0,0,0,0.04);
        }
        .card-header {
            margin-bottom:24px;
            border-bottom:1px solid var(--border-subtle);
            padding-bottom:14px;
        }
        .card-title {
            font-size:18px;
            font-weight:800;
            color:var(--text-primary);
        }
        
        .form-grid {
            display:grid;
            grid-template-columns:1fr 1fr;
            gap:20px;
            margin-bottom:20px;
        }
        .form-group {
            margin-bottom:20px;
        }
        .form-label {
            display:block;
            font-size:13px;
            font-weight:700;
            color:var(--text-primary);
            margin-bottom:8px;
        }
        .form-control {
            width:100%;
            height:48px;
            padding:12px 16px;
            font-size:14px;
            border:1px solid var(--border-color);
            border-radius:10px;
            background:var(--bg-input);
            color:var(--text-primary);
            transition:0.2s;
        }
        .form-control:focus {
            outline:none;
            border-color:var(--accent-blue);
            box-shadow:0 0 0 3px rgba(37,99,235,0.15);
        }
        .form-control:disabled {
            background:var(--bg-subtle);
            color:var(--text-muted);
            cursor:not-allowed;
            border-color:var(--border-subtle);
        }
        textarea.form-control {
            height:100px;
            resize:vertical;
        }

        .btn-save {
            background:#2563eb;
            color:#ffffff;
            border:none;
            padding:13px 30px;
            border-radius:10px;
            font-weight:800;
            font-size:14px;
            cursor:pointer;
            transition:background 0.2s;
        }
        .btn-save:hover {
            background:#1d4ed8;
        }

        @media (max-width: 900px) {
            body { flex-direction:column; }
            .sidebar { width:100%; min-height:auto; }
            .main-content { padding:24px; }
            .form-grid { grid-template-columns:1fr; }
        }
    </style>
</head>
<body>

    <!-- SIDEBAR KIRI VERTIKAL (CLEAN TEXT-ONLY, NO ICONS) -->
    <aside class="sidebar">
        <a href="<?= base_url() ?>" class="sidebar-brand">CamHub</a>
        
        <div class="sidebar-user">
            <div class="user-fullname"><?= htmlspecialchars($profile['full_name'] ?? $user['full_name'] ?? 'Pelanggan', ENT_QUOTES, 'UTF-8') ?></div>
            <div class="user-role-label">Pelanggan</div>
        </div>

        <ul class="nav-menu">
            <li class="nav-item">
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
            <li class="nav-item active">
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

    <!-- AREA KONTEN UTAMA PROFIL -->
    <main class="main-content">
        <div class="breadcrumb">
            <a href="<?= base_url('/dashboard/pelanggan') ?>">Dashboard</a>
            <span>/</span>
            <span>Profil Saya & Pengaturan Akun</span>
        </div>

        <div class="page-header">
            <h1 class="page-title">Pengaturan Profil Saya</h1>
            <p class="page-subtitle">Perbarui biodata domisili pengiriman kamera, nomor kontak aktif WhatsApp, dan pantau status verifikasi identitas resmi (KYC).</p>
        </div>

        <?php if (!empty($success)): ?>
            <div class="alert-success"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>
        <?php if (!empty($error)): ?>
            <div class="alert-danger"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <!-- USER SUMMARY CARD -->
        <div class="user-header-card">
            <div class="user-info-left">
                <div class="avatar-circle">
                    <?= strtoupper(substr($profile['full_name'] ?? 'U', 0, 1)) ?>
                </div>
                <div class="user-names">
                    <h2><?= htmlspecialchars($profile['full_name'] ?? 'Pelanggan', ENT_QUOTES, 'UTF-8') ?></h2>
                    <p>@<?= htmlspecialchars($profile['username'] ?? 'user', ENT_QUOTES, 'UTF-8') ?> &bull; <?= htmlspecialchars($profile['email'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
                </div>
            </div>

            <!-- KYC STATUS BADGE IN HEADER -->
            <div>
                <?php if ($kycStatus === 'approved'): ?>
                    <span style="display:inline-flex; align-items:center; gap:6px; background:#dcfce7; color:#15803d; border:1px solid #bbf7d0; padding:8px 16px; border-radius:20px; font-weight:800; font-size:13px;">
                        TERVERIFIKASI
                    </span>
                <?php elseif ($kycStatus === 'pending'): ?>
                    <span style="display:inline-flex; align-items:center; gap:6px; background:#fef3c7; color:#d97706; border:1px solid #fde68a; padding:8px 16px; border-radius:20px; font-weight:800; font-size:13px;">
                        MENUNGGU VERIFIKASI
                    </span>
                <?php elseif ($kycStatus === 'rejected'): ?>
                    <span style="display:inline-flex; align-items:center; gap:6px; background:#fee2e2; color:#b91c1c; border:1px solid #fecaca; padding:8px 16px; border-radius:20px; font-weight:800; font-size:13px;">
                        VERIFIKASI DITOLAK
                    </span>
                <?php else: ?>
                    <span style="display:inline-flex; align-items:center; gap:6px; background:#f1f5f9; color:#64748b; border:1px solid #cbd5e1; padding:8px 16px; border-radius:20px; font-weight:800; font-size:13px;">
                        BELUM VERIFIKASI
                    </span>
                <?php endif; ?>
            </div>
        </div>

        <!-- KYC STATUS BANNER -->
        <?php if ($kycStatus === 'approved'): ?>
            <div class="kyc-banner approved">
                <div>
                    <strong style="color:#15803d; font-size:15px; display:block; margin-bottom:4px;">Identitas Anda Terverifikasi Penuh</strong>
                    <p style="color:#166534; font-size:13px;">Dokumen KTP Anda telah disetujui Super Admin CamHub. Hak akses sewa kamera aktif.</p>
                </div>
                <a href="<?= base_url('/kyc/status') ?>" class="kyc-btn-action" style="background:#15803d; color:#ffffff;">Lihat Detail Status</a>
            </div>
        <?php elseif ($kycStatus === 'pending'): ?>
            <div class="kyc-banner pending">
                <div>
                    <strong style="color:#0284c7; font-size:15px; display:block; margin-bottom:4px;">Dokumen KYC Anda Sedang Ditinjau Admin</strong>
                    <p style="color:#0369a1; font-size:13px;">Estimasi verifikasi 1x24 jam kerja. Pantau pembaruan status di halaman status.</p>
                </div>
                <a href="<?= base_url('/kyc/status') ?>" class="kyc-btn-action" style="background:#0284c7; color:#ffffff;">Lihat Detail Status</a>
            </div>
        <?php elseif ($kycStatus === 'rejected'): ?>
            <div class="kyc-banner unverified" style="background:rgba(239, 68, 68, 0.12); border-color:rgba(239, 68, 68, 0.3);">
                <div>
                    <strong style="color:#b91c1c; font-size:15px; display:block; margin-bottom:4px;">Pengajuan KYC Ditolak</strong>
                    <p style="color:#991b1b; font-size:13px;">Alasan: <?= htmlspecialchars($kycRecord['rejection_reason'] ?? 'Foto KTP buram atau data tidak cocok.') ?></p>
                </div>
                <a href="<?= base_url('/kyc') ?>" class="kyc-btn-action" style="background:#b91c1c; color:#ffffff;">Ajukan Ulang Dokumen</a>
            </div>
        <?php else: ?>
            <div class="kyc-banner unverified">
                <div>
                    <strong style="color:#b45309; font-size:15px; display:block; margin-bottom:4px;">Verifikasi Identitas Anda Belum Lengkap</strong>
                    <p style="color:#92400e; font-size:13px;">Unggah foto KTP dan swafoto untuk membuka fitur penyewaan kamera bergaransi rekening bersama.</p>
                </div>
                <a href="<?= base_url('/kyc') ?>" class="kyc-btn-action" style="background:#d97706; color:#ffffff;">Unggah KTP Sekarang</a>
            </div>
        <?php endif; ?>

        <!-- EDIT PROFILE FORM -->
        <div class="form-card">
            <div class="card-header">
                <h3 class="card-title">Pembaruan Biodata Domisili & Kontak</h3>
            </div>

            <form action="<?= base_url('/profil/edit') ?>" method="POST">
                <?= csrf_field() ?>

                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label" for="full_name">Nama Lengkap</label>
                        <input type="text" id="full_name" name="full_name" class="form-control" value="<?= htmlspecialchars(old('full_name', $profile['full_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="phone">Nomor HP / WhatsApp</label>
                        <input type="tel" id="phone" name="phone" class="form-control" value="<?= htmlspecialchars(old('phone', $profile['phone'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Alamat Email (Terkunci)</label>
                        <input type="email" class="form-control" value="<?= htmlspecialchars($profile['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>" disabled>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="city">Kota Domisili</label>
                        <input type="text" id="city" name="city" class="form-control" placeholder="Contoh: Yogyakarta, Jakarta Selatan, dll." value="<?= htmlspecialchars(old('city', $profile['city'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="address">Alamat Domisili Lengkap (Untuk Pengiriman & Verifikasi)</label>
                    <textarea id="address" name="address" class="form-control" placeholder="Tuliskan jalan, nomor rumah, RT/RW, kelurahan, kecamatan" required><?= htmlspecialchars(old('address', $profile['address'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
                </div>

                <button type="submit" class="btn-save">Simpan Perubahan Profil</button>
            </form>
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

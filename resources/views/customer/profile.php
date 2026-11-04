<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Profil Saya & Pengaturan Akun — CamHub', ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; font-family:'Plus Jakarta Sans', system-ui, sans-serif; }
        body { background:#f8fafc; color:#0f172a; min-height:100vh; display:flex; }

        /* SIDEBAR IDENTIK DENGAN DASHBOARD PELANGGAN */
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
            margin-bottom:36px;
        }
        .user-fullname {
            font-size:18px;
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
            display:flex;
            align-items:center;
            gap:14px;
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
        }
        .nav-icon {
            font-size:16px;
            width:20px;
            text-align:center;
            display:inline-block;
        }
        .sidebar-bottom {
            margin-top:auto;
            padding-top:24px;
            border-top:1px solid #1e293b;
            display:flex;
            flex-direction:column;
            gap:8px;
        }
        .btn-sidebar-link {
            display:flex;
            align-items:center;
            gap:12px;
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
            display:flex;
            align-items:center;
            gap:12px;
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
            padding:48px 56px;
            overflow-y:auto;
            max-width:1040px;
        }
        .breadcrumb {
            display:flex;
            align-items:center;
            gap:8px;
            font-size:13px;
            color:#64748b;
            margin-bottom:20px;
        }
        .breadcrumb a {
            color:#2563eb;
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
            font-size:30px;
            font-weight:800;
            color:#0f172a;
            letter-spacing:-0.5px;
            margin-bottom:6px;
        }
        .page-subtitle {
            font-size:14px;
            color:#64748b;
            line-height:1.6;
        }

        /* ALERTS */
        .alert-success {
            background:#dcfce7;
            border:1px solid #bbf7d0;
            color:#15803d;
            padding:14px 18px;
            border-radius:12px;
            margin-bottom:24px;
            font-size:14px;
            font-weight:600;
        }
        .alert-danger {
            background:#fee2e2;
            border:1px solid #fecaca;
            color:#b91c1c;
            padding:14px 18px;
            border-radius:12px;
            margin-bottom:24px;
            font-size:14px;
            font-weight:600;
        }

        /* USER SUMMARY HEADER CARD */
        .user-header-card {
            background:#ffffff;
            border:1px solid #e2e8f0;
            border-radius:16px;
            padding:28px 32px;
            display:flex;
            align-items:center;
            justify-content:space-between;
            margin-bottom:28px;
            flex-wrap:wrap;
            gap:20px;
            box-shadow:0 1px 3px rgba(0,0,0,0.02);
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
            color:#0f172a;
            margin-bottom:4px;
        }
        .user-names p {
            color:#64748b;
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
            background:#fffbeb;
            border:1px solid #fde68a;
        }
        .kyc-banner.pending {
            background:#f0f9ff;
            border:1px solid #bae6fd;
        }
        .kyc-banner.approved {
            background:#f0fdf4;
            border:1px solid #bbf7d0;
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
            background:#ffffff;
            border:1px solid #e2e8f0;
            border-radius:16px;
            padding:32px;
            box-shadow:0 1px 3px rgba(0,0,0,0.02);
        }
        .card-header {
            margin-bottom:24px;
            border-bottom:1px solid #f1f5f9;
            padding-bottom:14px;
        }
        .card-title {
            font-size:18px;
            font-weight:800;
            color:#0f172a;
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
            color:#334155;
            margin-bottom:8px;
        }
        .form-control {
            width:100%;
            height:48px;
            padding:12px 16px;
            font-size:14px;
            border:1.5px solid #cbd5e1;
            border-radius:10px;
            background:#ffffff;
            color:#0f172a;
            transition:0.2s;
        }
        .form-control:focus {
            outline:none;
            border-color:#2563eb;
            box-shadow:0 0 0 3px rgba(37,99,235,0.12);
        }
        .form-control:disabled {
            background:#f1f5f9;
            color:#64748b;
            cursor:not-allowed;
            border-color:#e2e8f0;
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

    <!-- SIDEBAR KIRI VERTIKAL IDENTIK DASHBOARD PELANGGAN -->
    <aside class="sidebar">
        <a href="<?= base_url() ?>" class="sidebar-brand">CamHub</a>
        
        <div class="sidebar-user">
            <div class="user-fullname"><?= htmlspecialchars($profile['full_name'] ?? $user['full_name'] ?? 'Pelanggan', ENT_QUOTES, 'UTF-8') ?></div>
            <div class="user-role-label">Pelanggan</div>
        </div>

        <ul class="nav-menu">
            <li class="nav-item">
                <a href="<?= base_url('/dashboard/pelanggan') ?>">
                    <span class="nav-icon">⌂</span>
                    <span>Beranda</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= base_url('/pesanan-saya') ?>">
                    <span class="nav-icon">🗂</span>
                    <span>Pesanan Saya</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= base_url('/pesanan-saya?type=rental') ?>">
                    <span class="nav-icon">📷</span>
                    <span>Sewa Saya</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= base_url('/pesanan-saya?type=sale') ?>">
                    <span class="nav-icon">🛒</span>
                    <span>Jual Beli Saya</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= base_url('/titip-jual/saya') ?>">
                    <span class="nav-icon">🏷️</span>
                    <span>Titip Jual Saya</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= base_url('/kyc/status') ?>">
                    <span class="nav-icon">📄</span>
                    <span>Dokumen KYC</span>
                </a>
            </li>
            <li class="nav-item active">
                <a href="<?= base_url('/profil') ?>">
                    <span class="nav-icon">⚙️</span>
                    <span>Pengaturan</span>
                </a>
            </li>
        </ul>

        <div class="sidebar-bottom">
            <a href="<?= base_url('/') ?>" class="btn-sidebar-link">
                <span>🌐</span>
                <span>Lihat Beranda Publik</span>
            </a>
            <form action="<?= base_url('/logout') ?>" method="POST" style="margin:0;">
                <?= csrf_field() ?>
                <button type="submit" class="btn-sidebar-logout">
                    <span>🚪</span>
                    <span>Keluar Akun</span>
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
            <div class="alert-success">✓ <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></div>
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

            <div>
                <?php 
                    $kycStat = strtolower($profile['kyc_status'] ?? 'unverified');
                ?>
                <span style="display:inline-block; padding:8px 16px; border-radius:20px; font-size:12px; font-weight:800; text-transform:uppercase;
                    <?= match($kycStat) {
                        'approved' => 'background:#dcfce7; color:#15803d; border:1px solid #bbf7d0;',
                        'pending' => 'background:#fef3c7; color:#d97706; border:1px solid #fde68a;',
                        'rejected' => 'background:#fee2e2; color:#b91c1c; border:1px solid #fecaca;',
                        default => 'background:#f1f5f9; color:#475569; border:1px solid #cbd5e1;'
                    } ?>">
                    <?= match($kycStat) {
                        'approved' => '✓ Terverifikasi (KYC)',
                        'pending' => '⏳ Menunggu Verifikasi',
                        'rejected' => '✕ KYC Ditolak',
                        default => '⚠ Belum Verifikasi KYC'
                    } ?>
                </span>
            </div>
        </div>

        <!-- KYC CALLOUT BANNER -->
        <?php if ($kycStat === 'unverified' || $kycStat === 'rejected'): ?>
            <div class="kyc-banner unverified">
                <div>
                    <strong style="color:#d97706; font-size:15px;">Verifikasi Identitas Anda untuk Menyewa Kamera</strong>
                    <p style="color:#64748b; font-size:13px; margin-top:4px;">Lengkapi data diri dan foto KTP untuk membuka akses seluruh unit sewa di platform CamHub.</p>
                </div>
                <a href="<?= base_url('/kyc') ?>" class="kyc-btn-action" style="background:#f59e0b; color:#ffffff;">Mulai Verifikasi KYC Sekarang &rarr;</a>
            </div>
        <?php elseif ($kycStat === 'pending'): ?>
            <div class="kyc-banner pending">
                <div>
                    <strong style="color:#0284c7; font-size:15px;">Dokumen KYC Anda Sedang Ditinjau Admin</strong>
                    <p style="color:#64748b; font-size:13px; margin-top:4px;">Estimasi verifikasi 1x24 jam kerja. Pantau pembaruan status di halaman status dokumen.</p>
                </div>
                <a href="<?= base_url('/kyc/status') ?>" class="kyc-btn-action" style="background:#0284c7; color:#ffffff;">Lihat Detail Status</a>
            </div>
        <?php elseif ($kycStat === 'approved'): ?>
            <div class="kyc-banner approved">
                <div>
                    <strong style="color:#15803d; font-size:15px;">Akun Terverifikasi Resmi (KYC Approved)</strong>
                    <p style="color:#64748b; font-size:13px; margin-top:4px;">Identitas Anda telah diakui oleh CamHub. Anda dapat melakukan booking dan sewa kamera tanpa batasan.</p>
                </div>
                <a href="<?= base_url('/katalog') ?>" class="kyc-btn-action" style="background:#10b981; color:#ffffff;">Sewa Kamera Sekarang</a>
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

</body>
</html>

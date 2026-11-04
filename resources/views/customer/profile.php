<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Profil Saya — CamHub', ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-dark: #0b192e;
            --surface-dark: #112240;
            --card-dark: #132644;
            --accent-amber: #f59e0b;
            --accent-amber-hover: #d97706;
            --text-light: #ffffff;
            --text-muted: #94a3b8;
            --border-navy: #1e3a5f;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
        }

        * { margin:0; padding:0; box-sizing:border-box; font-family:'Plus Jakarta Sans', system-ui, sans-serif; }
        body { background-color: var(--bg-dark); color: var(--text-light); min-height: 100vh; }

        header { background: var(--surface-dark); padding: 16px 40px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid var(--border-navy); flex-wrap: wrap; gap: 16px; }
        .logo { font-size: 22px; font-weight: 800; color: #fff; text-decoration: none; }
        .logo span { color: var(--accent-amber); }
        .nav-links-dash { display: flex; align-items: center; gap: 20px; list-style: none; }
        .nav-links-dash a { color: var(--text-muted); text-decoration: none; font-size: 14px; font-weight: 600; transition: 0.2s; }
        .nav-links-dash a:hover { color: var(--accent-amber); }
        .role-badge { background: #2563eb; color: #fff; font-size: 11px; font-weight: 700; padding: 4px 10px; border-radius: 20px; text-transform: uppercase; }

        .container { max-width: 900px; margin: 36px auto; padding: 0 20px; }
        .breadcrumb { display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--text-muted); margin-bottom: 24px; }
        .breadcrumb a { color: var(--accent-amber); text-decoration: none; }

        /* USER SUMMARY HEADER CARD */
        .user-header-card { background: var(--card-dark); border: 1px solid var(--border-navy); border-radius: 16px; padding: 28px 32px; display: flex; align-items: center; justify-content: space-between; margin-bottom: 28px; flex-wrap: wrap; gap: 20px; }
        .user-info-left { display: flex; align-items: center; gap: 20px; }
        .avatar-circle { width: 68px; height: 68px; border-radius: 50%; background: linear-gradient(135deg, #2563eb, #d97706); display: flex; align-items: center; justify-content: center; font-size: 28px; font-weight: 800; color: #fff; }
        .user-names h2 { font-size: 22px; font-weight: 800; margin-bottom: 4px; }
        .user-names p { color: var(--text-muted); font-size: 13px; }

        /* KYC BANNER */
        .kyc-banner { border-radius: 12px; padding: 18px 24px; display: flex; align-items: center; justify-content: space-between; margin-bottom: 28px; flex-wrap: wrap; gap: 16px; }
        .kyc-banner.unverified { background: rgba(245, 158, 11, 0.12); border: 1px solid rgba(245, 158, 11, 0.35); }
        .kyc-banner.pending { background: rgba(56, 189, 248, 0.12); border: 1px solid rgba(56, 189, 248, 0.35); }
        .kyc-banner.approved { background: rgba(16, 185, 129, 0.12); border: 1px solid rgba(16, 185, 129, 0.35); }

        .kyc-btn-action { background: var(--accent-amber); color: #0b192e; text-decoration: none; padding: 10px 20px; border-radius: 8px; font-weight: 700; font-size: 13px; transition: 0.2s; }
        .kyc-btn-action:hover { background: #d97706; color: #fff; }

        /* EDIT FORM CARD */
        .form-card { background: var(--card-dark); border: 1px solid var(--border-navy); border-radius: 16px; padding: 32px; box-shadow: 0 10px 30px rgba(0,0,0,0.3); }
        .card-header { margin-bottom: 24px; border-bottom: 1px solid var(--border-navy); padding-bottom: 12px; }
        .card-title { font-size: 18px; font-weight: 700; }
        
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
        .form-group { margin-bottom: 20px; }
        .form-label { display: block; font-size: 13px; font-weight: 600; color: #cbd5e1; margin-bottom: 8px; }
        .form-control { width: 100%; height: 48px; padding: 12px 16px; font-size: 14px; border: 1.5px solid var(--border-navy); border-radius: 10px; background: var(--surface-dark); color: #ffffff; transition: 0.2s; }
        .form-control:focus { outline: none; border-color: var(--accent-amber); box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.15); }
        .form-control:disabled { opacity: 0.6; cursor: not-allowed; }
        textarea.form-control { height: 90px; resize: vertical; }

        .btn-save { background: var(--accent-amber); color: #0b192e; border: none; padding: 12px 28px; border-radius: 10px; font-weight: 800; font-size: 15px; cursor: pointer; transition: 0.2s; }
        .btn-save:hover { background: #d97706; color: #fff; }

        .alert-success { background: #052e16; border: 1px solid #14532d; color: #86efac; padding: 14px 18px; border-radius: 10px; margin-bottom: 24px; font-size: 14px; }
        .alert-danger { background: #450a0a; border: 1px solid #7f1d1d; color: #fca5a5; padding: 14px 18px; border-radius: 10px; margin-bottom: 24px; font-size: 14px; }
    </style>
</head>
<body>

<header>
    <a href="<?= base_url() ?>" class="logo">Cam<span>Hub</span></a>
    <ul class="nav-links-dash">
        <li><a href="<?= base_url() ?>">← Beranda Publik</a></li>
        <li><a href="<?= base_url('/dashboard/pelanggan') ?>">Dashboard</a></li>
        <li><a href="<?= base_url('/kyc/status') ?>">Status KYC</a></li>
    </ul>
    <div style="font-size: 13px;">
        <span class="role-badge">Pelanggan</span>
        <form action="<?= base_url('/logout') ?>" method="POST" style="margin:0;">
            <?= csrf_field() ?>
            <button type="submit" style="background:#dc2626; color:#ffffff; font-weight:700; border-radius:8px; padding:6px 12px; border:none; cursor:pointer; font-size:12px; display:inline-flex; align-items:center; gap:6px;">
                🚪 Keluar
            </button>
        </form>
    </div>
</header>

<main class="container">
    <div class="breadcrumb">
        <a href="<?= base_url('/dashboard/pelanggan') ?>">Dashboard</a>
        <span>/</span>
        <span>Profil Saya</span>
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
            <span style="display:inline-block; padding: 8px 16px; border-radius: 20px; font-size: 12px; font-weight: 800; text-transform: uppercase;
                <?= match($kycStat) {
                    'approved' => 'background:#052e16; color:#34d399; border:1px solid #10b981;',
                    'pending' => 'background:#451a03; color:#fbbf24; border:1px solid #f59e0b;',
                    'rejected' => 'background:#450a0a; color:#f87171; border:1px solid #ef4444;',
                    default => 'background:#1e293b; color:#94a3b8; border:1px solid #334155;'
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
                <strong style="color: #fbbf24; font-size: 15px;">Verifikasi Identitas Anda untuk Menyewa Kamera</strong>
                <p style="color: #cbd5e1; font-size: 13px; margin-top: 4px;">Lengkapi data diri dan foto KTP untuk membuka akses seluruh unit sewa di platform CamHub.</p>
            </div>
            <a href="<?= base_url('/kyc') ?>" class="kyc-btn-action">Mulai Verifikasi KYC Sekarang &rarr;</a>
        </div>
    <?php elseif ($kycStat === 'pending'): ?>
        <div class="kyc-banner pending">
            <div>
                <strong style="color: #38bdf8; font-size: 15px;">Dokumen KYC Anda Sedang Ditinjau Admin</strong>
                <p style="color: #cbd5e1; font-size: 13px; margin-top: 4px;">Estimasi verifikasi 1x24 jam kerja. Pantau pembaruan status di halaman status.</p>
            </div>
            <a href="<?= base_url('/kyc/status') ?>" class="kyc-btn-action" style="background:#0284c7; color:#fff;">Lihat Detail Status</a>
        </div>
    <?php elseif ($kycStat === 'approved'): ?>
        <div class="kyc-banner approved">
            <div>
                <strong style="color: #34d399; font-size: 15px;">Akun Terverifikasi Resmi (KYC Approved)</strong>
                <p style="color: #cbd5e1; font-size: 13px; margin-top: 4px;">Identitas Anda telah diakui oleh CamHub. Anda dapat melakukan booking dan sewa kamera tanpa batasan.</p>
            </div>
            <a href="<?= base_url() ?>" class="kyc-btn-action" style="background:#10b981; color:#fff;">Sewa Kamera Sekarang</a>
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


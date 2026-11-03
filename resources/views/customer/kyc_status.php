<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Status Verifikasi KYC — CamHub', ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-dark: #0b192e;
            --surface-dark: #112240;
            --card-dark: #132644;
            --accent-amber: #f59e0b;
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

        .container { max-width: 800px; margin: 40px auto; padding: 0 20px; }
        .breadcrumb { display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--text-muted); margin-bottom: 24px; }
        .breadcrumb a { color: var(--accent-amber); text-decoration: none; }

        .status-card { background: var(--card-dark); border: 1px solid var(--border-navy); border-radius: 16px; padding: 36px; text-align: center; margin-bottom: 28px; box-shadow: 0 10px 30px rgba(0,0,0,0.3); }
        .status-icon-circle { width: 72px; height: 72px; border-radius: 50%; margin: 0 auto 18px; display: flex; align-items: center; justify-content: center; font-size: 36px; }
        
        .status-pending .status-icon-circle { background: rgba(245, 158, 11, 0.15); border: 2px solid var(--warning); color: var(--warning); }
        .status-approved .status-icon-circle { background: rgba(16, 185, 129, 0.15); border: 2px solid var(--success); color: var(--success); }
        .status-rejected .status-icon-circle { background: rgba(239, 68, 68, 0.15); border: 2px solid var(--danger); color: var(--danger); }

        .status-title { font-size: 24px; font-weight: 800; margin-bottom: 8px; }
        .status-desc { font-size: 15px; color: var(--text-muted); max-width: 520px; margin: 0 auto 24px; line-height: 1.6; }

        .details-table { background: var(--surface-dark); border: 1px solid var(--border-navy); border-radius: 12px; padding: 20px; text-align: left; margin: 24px 0; }
        .detail-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid rgba(255,255,255,0.06); font-size: 14px; }
        .detail-row:last-child { border-bottom: none; }
        .detail-label { color: var(--text-muted); }
        .detail-val { font-weight: 700; color: #ffffff; }

        .badge-status { display: inline-block; padding: 6px 14px; border-radius: 20px; font-size: 13px; font-weight: 700; text-transform: uppercase; }
        .badge-pending { background: rgba(245, 158, 11, 0.2); color: #fbbf24; border: 1px solid #f59e0b; }
        .badge-approved { background: rgba(16, 185, 129, 0.2); color: #34d399; border: 1px solid #10b981; }
        .badge-rejected { background: rgba(239, 68, 68, 0.2); color: #f87171; border: 1px solid #ef4444; }

        .action-row { display: flex; gap: 14px; justify-content: center; margin-top: 24px; flex-wrap: wrap; }
        .btn-dash { background: var(--accent-amber); color: #0b192e; text-decoration: none; padding: 12px 24px; border-radius: 10px; font-weight: 700; font-size: 14px; transition: 0.2s; }
        .btn-dash:hover { background: #d97706; color: #fff; }
        .btn-outline { background: transparent; color: #cbd5e1; border: 1px solid var(--border-navy); text-decoration: none; padding: 12px 24px; border-radius: 10px; font-weight: 600; font-size: 14px; }
        .btn-outline:hover { background: var(--surface-dark); color: #fff; }

        .alert-success { background: #052e16; border: 1px solid #14532d; color: #86efac; padding: 14px 18px; border-radius: 10px; margin-bottom: 24px; font-size: 14px; }
    </style>
</head>
<body>

<header>
    <a href="<?= base_url() ?>" class="logo">Cam<span>Hub</span></a>
    <ul class="nav-links-dash">
        <li><a href="<?= base_url() ?>">← Beranda Publik</a></li>
        <li><a href="<?= base_url('/dashboard/pelanggan') ?>">Dashboard</a></li>
        <li><a href="<?= base_url('/profil') ?>">Profil Saya</a></li>
    </ul>
    <div style="font-size: 13px; color: var(--text-muted);">
        👤 <?= htmlspecialchars($user['full_name'] ?? 'Pelanggan', ENT_QUOTES, 'UTF-8') ?>
    </div>
</header>

<main class="container">
    <div class="breadcrumb">
        <a href="<?= base_url('/dashboard/pelanggan') ?>">Dashboard</a>
        <span>/</span>
        <span>Status Verifikasi KYC</span>
    </div>

    <?php if (!empty($success)): ?>
        <div class="alert-success">✓ <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <?php if ($kyc): ?>
        <?php $status = strtolower($kyc['status']); ?>
        <div class="status-card status-<?= $status ?>">
            <div class="status-icon-circle">
                <?php if ($status === 'approved'): ?> ✓
                <?php elseif ($status === 'rejected'): ?> ✕
                <?php else: ?> ⏳ <?php endif; ?>
            </div>

            <span class="badge-status badge-<?= $status ?>">
                <?= match($status) { 'approved' => 'Terverifikasi (Approved)', 'rejected' => 'Ditolak (Rejected)', default => 'Menunggu Tinjauan Admin (Pending)' } ?>
            </span>

            <h1 class="status-title" style="margin-top: 16px;">
                <?= match($status) {
                    'approved' => 'Selamat! Identitas Anda Telah Terverifikasi',
                    'rejected' => 'Pengajuan Verifikasi Identitas Ditolak',
                    default => 'Dokumen Anda Sedang Ditinjau Tim CamHub'
                } ?>
            </h1>

            <p class="status-desc">
                <?= match($status) {
                    'approved' => 'Akun Anda telah berstatus KYC Verified. Anda kini memiliki hak penuh untuk menyewa kamera dan bertransaksi sewa aman dengan proteksi rekening bersama.',
                    'rejected' => 'Alasan Penolakan: ' . htmlspecialchars($kyc['rejection_reason'] ?? 'Foto KTP buram atau data tidak cocok.') . '. Silakan ajukan ulang dengan dokumen yang jelas.',
                    default => 'Tim verifikasi kami sedang memeriksa keaslian foto KTP dan swafoto Anda (estimasi waktu: maksimal 1x24 jam kerja). Anda akan menerima notifikasi jika status telah disetujui.'
                } ?>
            </p>

            <div class="details-table">
                <div class="detail-row">
                    <span class="detail-label">ID Pengajuan</span>
                    <span class="detail-val">#KYC-<?= str_pad((string)$kyc['id'], 5, '0', STR_PAD_LEFT) ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Nama Sesuai KTP</span>
                    <span class="detail-val"><?= htmlspecialchars($kyc['full_name'], ENT_QUOTES, 'UTF-8') ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Nomor NIK KTP</span>
                    <span class="detail-val"><?= htmlspecialchars(substr($kyc['nik_ktp'], 0, 4) . '********' . substr($kyc['nik_ktp'], -4), ENT_QUOTES, 'UTF-8') ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Tanggal Lahir / Jenis Kelamin</span>
                    <span class="detail-val"><?= htmlspecialchars($kyc['birth_date'], ENT_QUOTES, 'UTF-8') ?> (<?= htmlspecialchars($kyc['gender'], ENT_QUOTES, 'UTF-8') ?>)</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Waktu Pengajuan</span>
                    <span class="detail-val"><?= htmlspecialchars($kyc['created_at'], ENT_QUOTES, 'UTF-8') ?></span>
                </div>
            </div>

            <div class="action-row">
                <a href="<?= base_url('/dashboard/pelanggan') ?>" class="btn-dash">Kembali ke Dashboard</a>
                <a href="<?= base_url('/profil') ?>" class="btn-outline">Lihat Profil Saya</a>
                <?php if ($status === 'rejected'): ?>
                    <a href="<?= base_url('/kyc') ?>" class="btn-dash" style="background:#ef4444; color:#fff;">Ajukan Ulang KYC</a>
                <?php endif; ?>
            </div>
        </div>
    <?php else: ?>
        <div class="status-card">
            <div class="status-icon-circle" style="background: rgba(255,255,255,0.06); border: 2px solid var(--border-navy); color: var(--text-muted);">
                🪪
            </div>
            <h1 class="status-title">Belum Ada Pengajuan KYC</h1>
            <p class="status-desc">
                Anda belum pernah mengajukan verifikasi identitas resmi. Lengkapi data KTP Anda untuk mengaktifkan fitur penyewaan kamera.
            </p>
            <div class="action-row">
                <a href="<?= base_url('/kyc') ?>" class="btn-dash">Mulai Verifikasi KYC Sekarang</a>
                <a href="<?= base_url('/dashboard/pelanggan') ?>" class="btn-outline">Kembali ke Dashboard</a>
            </div>
        </div>
    <?php endif; ?>
</main>

</body>
</html>

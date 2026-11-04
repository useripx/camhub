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
            max-width:960px;
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

        /* STATUS CARD */
        .status-card {
            background:#ffffff;
            border:1px solid #e2e8f0;
            border-radius:16px;
            padding:40px 32px;
            text-align:center;
            margin-bottom:28px;
            box-shadow:0 1px 3px rgba(0,0,0,0.02);
        }
        .status-icon-circle {
            width:72px;
            height:72px;
            border-radius:50%;
            margin:0 auto 18px;
            display:flex;
            align-items:center;
            justify-content:center;
            font-size:32px;
            font-weight:800;
        }
        .status-approved .status-icon-circle { background:#dcfce7; color:#15803d; border:2px solid #bbf7d0; }
        .status-pending .status-icon-circle { background:#fef3c7; color:#d97706; border:2px solid #fde68a; }
        .status-rejected .status-icon-circle { background:#fee2e2; color:#b91c1c; border:2px solid #fecaca; }

        .badge-status {
            display:inline-block;
            padding:6px 18px;
            border-radius:20px;
            font-size:12px;
            font-weight:800;
            letter-spacing:0.5px;
            text-transform:uppercase;
        }
        .badge-approved { background:#dcfce7; color:#15803d; border:1px solid #bbf7d0; }
        .badge-pending { background:#fef3c7; color:#d97706; border:1px solid #fde68a; }
        .badge-rejected { background:#fee2e2; color:#b91c1c; border:1px solid #fecaca; }

        .status-title {
            font-size:24px;
            font-weight:800;
            color:#0f172a;
            letter-spacing:-0.5px;
        }
        .status-desc {
            color:#64748b;
            font-size:14px;
            line-height:1.6;
            max-width:620px;
            margin:12px auto 28px;
        }

        .details-table {
            background:#f8fafc;
            border:1px solid #e2e8f0;
            border-radius:12px;
            max-width:600px;
            margin:0 auto 28px;
            overflow:hidden;
        }
        .detail-row {
            display:flex;
            justify-content:space-between;
            align-items:center;
            padding:12px 18px;
            border-bottom:1px solid #e2e8f0;
            font-size:13px;
        }
        .detail-row:last-child { border-bottom:none; }
        .detail-label { color:#64748b; font-weight:600; }
        .detail-val { color:#0f172a; font-weight:700; }

        .action-row {
            display:flex;
            justify-content:center;
            gap:14px;
            flex-wrap:wrap;
        }
        .btn-dash {
            background:#2563eb;
            color:#ffffff;
            text-decoration:none;
            padding:12px 24px;
            border-radius:10px;
            font-weight:700;
            font-size:14px;
            transition:0.2s;
        }
        .btn-dash:hover { background:#1d4ed8; }
        .btn-outline {
            background:transparent;
            color:#475569;
            border:1px solid #cbd5e1;
            text-decoration:none;
            padding:12px 24px;
            border-radius:10px;
            font-weight:600;
            font-size:14px;
            transition:0.2s;
        }
        .btn-outline:hover { background:#f1f5f9; color:#0f172a; }

        @media (max-width: 900px) {
            body { flex-direction:column; }
            .sidebar { width:100%; min-height:auto; }
            .main-content { padding:24px; }
        }
    </style>
</head>
<body>

    <!-- SIDEBAR KIRI VERTIKAL IDENTIK DASHBOARD PELANGGAN -->
    <aside class="sidebar">
        <a href="<?= base_url() ?>" class="sidebar-brand">CamHub</a>
        
        <div class="sidebar-user">
            <div class="user-fullname"><?= htmlspecialchars($user['full_name'] ?? 'Pelanggan', ENT_QUOTES, 'UTF-8') ?></div>
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
            <li class="nav-item active">
                <a href="<?= base_url('/kyc/status') ?>">
                    <span class="nav-icon">📄</span>
                    <span>Dokumen KYC</span>
                </a>
            </li>
            <li class="nav-item">
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

    <!-- AREA KONTEN UTAMA STATUS KYC -->
    <main class="main-content">
        <div class="breadcrumb">
            <a href="<?= base_url('/dashboard/pelanggan') ?>">Dashboard</a>
            <span>/</span>
            <span>Status Verifikasi KYC</span>
        </div>

        <?php if (!empty($success)): ?>
            <div class="alert-success">✓ <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <?php if (!empty($kyc)): ?>
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
                <div class="status-icon-circle" style="background:#f1f5f9; border:2px solid #cbd5e1; color:#64748b;">
                    🪪
                </div>
                <h1 class="status-title">Belum Ada Pengajuan KYC</h1>
                <p class="status-desc">
                    Anda belum pernah mengajukan verifikasi identitas resmi. Lengkapi data KTP Anda untuk mengaktifkan fitur penyewaan kamera bergaransi.
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

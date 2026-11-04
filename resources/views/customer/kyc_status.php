<?php
$user = $user ?? auth();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Status Verifikasi KYC — CamHub', ENT_QUOTES, 'UTF-8') ?></title>
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
            max-width:960px;
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

        /* STATUS CARD */
        .status-card {
            background:var(--bg-card);
            border:1px solid var(--border-color);
            border-radius:16px;
            padding:48px 40px;
            text-align:center;
            box-shadow:0 4px 20px rgba(0,0,0,0.06);
            margin-bottom:28px;
        }
        .status-icon-circle {
            width:72px;
            height:72px;
            border-radius:50%;
            margin:0 auto 20px;
            display:flex;
            align-items:center;
            justify-content:center;
            font-size:32px;
            font-weight:800;
        }
        .status-approved .status-icon-circle { background:rgba(16, 185, 129, 0.15); border:2px solid rgba(16, 185, 129, 0.4); color:#34d399; }
        .status-pending .status-icon-circle { background:rgba(245, 158, 11, 0.15); border:2px solid rgba(245, 158, 11, 0.4); color:#fbbf24; }
        .status-rejected .status-icon-circle { background:rgba(239, 68, 68, 0.15); border:2px solid rgba(239, 68, 68, 0.4); color:#f87171; }

        .badge-status {
            display:inline-block;
            padding:6px 18px;
            border-radius:20px;
            font-size:12px;
            font-weight:800;
            letter-spacing:0.5px;
            text-transform:uppercase;
        }
        .badge-approved { background:rgba(16, 185, 129, 0.15); color:#34d399; border:1px solid rgba(16, 185, 129, 0.3); }
        .badge-pending { background:rgba(245, 158, 11, 0.15); color:#fbbf24; border:1px solid rgba(245, 158, 11, 0.3); }
        .badge-rejected { background:rgba(239, 68, 68, 0.15); color:#f87171; border:1px solid rgba(239, 68, 68, 0.3); }

        .status-title {
            font-size:24px;
            font-weight:800;
            color:var(--text-primary);
            letter-spacing:-0.5px;
        }
        .status-desc {
            color:var(--text-secondary);
            font-size:14px;
            line-height:1.6;
            max-width:620px;
            margin:12px auto 28px;
        }

        .details-table {
            background:var(--bg-subtle);
            border:1px solid var(--border-subtle);
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
            border-bottom:1px solid var(--border-subtle);
            font-size:13px;
        }
        .detail-row:last-child { border-bottom:none; }
        .detail-label { color:var(--text-muted); font-weight:600; }
        .detail-val { color:var(--text-primary); font-weight:700; }

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
            color:var(--text-primary);
            border:1px solid var(--border-color);
            text-decoration:none;
            padding:12px 24px;
            border-radius:10px;
            font-weight:700;
            font-size:14px;
            transition:0.2s;
        }
        .btn-outline:hover { background:var(--bg-subtle); border-color:var(--accent-blue); }

        @media (max-width: 768px) {
            body { flex-direction:column; }
            .sidebar { width:100%; min-height:auto; }
            .main-content { padding:24px 20px; }
        }
    </style>
</head>
<body>

    <!-- SIDEBAR KIRI VERTIKAL (CLEAN TEXT-ONLY, NO ICONS) -->
    <aside class="sidebar">
        <a href="<?= base_url() ?>" class="sidebar-brand">CamHub</a>
        
        <div class="sidebar-user">
            <div class="user-fullname"><?= htmlspecialchars($user['full_name'] ?? 'Pelanggan', ENT_QUOTES, 'UTF-8') ?></div>
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
            <li class="nav-item active">
                <a href="<?= base_url('/kyc/status') ?>">Dokumen KYC</a>
            </li>
            <li class="nav-item">
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

    <!-- AREA KONTEN UTAMA STATUS KYC -->
    <main class="main-content">
        <div class="breadcrumb">
            <a href="<?= base_url('/dashboard/pelanggan') ?>">Dashboard</a>
            <span>/</span>
            <span>Status Verifikasi KYC</span>
        </div>

        <?php if (!empty($success)): ?>
            <div class="alert-success"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></div>
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
                <div class="status-icon-circle" style="background:var(--bg-subtle); border:2px solid var(--border-color); color:var(--text-muted);">
                    KTP
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

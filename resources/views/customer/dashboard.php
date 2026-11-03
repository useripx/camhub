<?php
$user = auth();
$userId = (int)($user['id'] ?? 0);
$kycRepo = new \App\Repositories\KycRepository();
$latestKyc = $userId > 0 ? $kycRepo->findLatestByUserId($userId) : null;
$kycStatus = strtolower($latestKyc['status'] ?? 'unverified');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pelanggan — CamHub</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; font-family:'Plus Jakarta Sans', sans-serif; }
        body { background:#0b192e; color:#ffffff; min-height:100vh; }
        header { background:#112240; padding:16px 40px; display:flex; align-items:center; justify-content:space-between; border-bottom:1px solid #1e3a5f; flex-wrap:wrap; gap:16px; }
        .logo { font-size:20px; font-weight:800; color:#fff; text-decoration:none; }
        .logo span { color:#fbbf24; }
        .nav-links-dash { display:flex; align-items:center; gap:20px; list-style:none; }
        .nav-links-dash a { color:#94a3b8; text-decoration:none; font-size:14px; font-weight:600; transition:0.2s; }
        .nav-links-dash a:hover { color:#fbbf24; }
        .user-nav { display:flex; align-items:center; gap:12px; }
        .role-badge { background:#2563eb; color:#fff; font-size:12px; font-weight:700; padding:4px 10px; border-radius:20px; text-transform:uppercase; }
        .btn-nav-auth { background:#1e293b; color:#cbd5e1; text-decoration:none; padding:7px 14px; border-radius:8px; font-size:13px; font-weight:600; border:1px solid #334155; transition:0.2s; }
        .btn-nav-auth:hover { background:#334155; color:#fff; }
        .btn-logout { background:#ef4444; color:#fff; text-decoration:none; padding:7px 14px; border-radius:8px; font-size:13px; font-weight:600; transition:0.2s; border:none; cursor:pointer; }
        .btn-logout:hover { background:#dc2626; }
        main { max-width:1100px; margin:40px auto; padding:0 20px; }
        .welcome-card { background:#132644; border:1px solid #1e3a5f; border-radius:16px; padding:32px; margin-bottom:28px; }
        .welcome-card h1 { font-size:28px; margin-bottom:8px; }
        .welcome-card h1 span { color:#fbbf24; }
        .welcome-card p { color:#94a3b8; font-size:15px; }
        .grid-stats { display:grid; grid-template-columns:repeat(auto-fit, minmax(240px, 1fr)); gap:20px; margin-bottom:30px; }
        .stat-card { background:#112240; border:1px solid #1e3a5f; border-radius:12px; padding:20px; text-decoration:none; color:inherit; display:block; transition:0.2s; }
        .stat-card:hover { border-color:#fbbf24; transform:translateY(-2px); }
        .stat-label { font-size:13px; color:#94a3b8; margin-bottom:6px; }
        .stat-value { font-size:22px; font-weight:800; color:#fbbf24; }
        .action-row { display:flex; gap:14px; flex-wrap:wrap; }
        .btn-action { background:#d97706; color:#fff; text-decoration:none; padding:12px 24px; border-radius:10px; font-weight:700; font-size:14px; transition:0.2s; }
        .btn-action:hover { background:#b45309; }
        .btn-secondary { background:#1e293b; color:#cbd5e1; text-decoration:none; padding:12px 24px; border-radius:10px; font-weight:600; font-size:14px; border:1px solid #334155; }
        .btn-secondary:hover { background:#334155; color:#fff; }
    </style>
</head>
<body>

<header>
    <div style="display:flex; align-items:center; gap:30px;">
        <a href="<?= base_url() ?>" class="logo">Cam<span>Hub</span></a>
        <ul class="nav-links-dash">
            <li><a href="<?= base_url() ?>">← Beranda Publik</a></li>
            <li><a href="<?= base_url('/profil') ?>">👤 Profil Saya</a></li>
            <li><a href="<?= base_url('/kyc/status') ?>">🪪 Status KYC</a></li>
        </ul>
    </div>
    <div class="user-nav">
        <span>👤 <?= htmlspecialchars($user['full_name'] ?? 'Pelanggan', ENT_QUOTES, 'UTF-8') ?></span>
        <span class="role-badge">Pelanggan</span>
        <a href="<?= base_url('/profil') ?>" class="btn-nav-auth">Edit Profil</a>
        <form action="<?= base_url('/logout') ?>" method="POST" style="margin:0;">
            <?= csrf_field() ?>
            <button type="submit" class="btn-logout">Keluar (Logout)</button>
        </form>
    </div>
</header>

<main>
    <div class="welcome-card">
        <h1>Selamat Datang, <span><?= htmlspecialchars($user['full_name'] ?? 'Pelanggan', ENT_QUOTES, 'UTF-8') ?></span>!</h1>
        <p>Anda terautentikasi dengan peran <strong>Customer / Pelanggan</strong> (Username: <code><?= htmlspecialchars($user['username'] ?? 'user', ENT_QUOTES, 'UTF-8') ?></code>). Kelola identitas KYC, biodata profil, dan riwayat pesanan sewa kamera Anda.</p>
    </div>

    <div class="grid-stats">
        <a href="<?= base_url() ?>" class="stat-card">
            <div class="stat-label">Sewa Berjalan</div>
            <div class="stat-value">0 Unit Aktif</div>
        </a>
        <a href="<?= base_url('/kyc/status') ?>" class="stat-card">
            <div class="stat-label">Status Verifikasi KYC</div>
            <div class="stat-value" style="color: <?= match($kycStatus) {
                'approved' => '#10b981',
                'pending' => '#f59e0b',
                'rejected' => '#ef4444',
                default => '#38bdf8'
            } ?>;">
                <?= match($kycStatus) {
                    'approved' => '✓ Terverifikasi',
                    'pending' => '⏳ Menunggu Review',
                    'rejected' => '✕ Perlu Perbaikan',
                    default => '⚠ Belum Diajukan'
                } ?>
            </div>
        </a>
        <div class="stat-card">
            <div class="stat-label">Saldo Escrow Aman</div>
            <div class="stat-value">Rp 0</div>
        </div>
    </div>

    <div class="action-row">
        <?php if ($kycStatus === 'unverified' || $kycStatus === 'rejected'): ?>
            <a href="<?= base_url('/kyc') ?>" class="btn-action" style="background:#2563eb;">🪪 Mulai Verifikasi KYC</a>
        <?php else: ?>
            <a href="<?= base_url('/kyc/status') ?>" class="btn-action" style="background:#0284c7;">🪪 Periksa Status KYC</a>
        <?php endif; ?>
        <a href="<?= base_url('/profil') ?>" class="btn-secondary">👤 Pengaturan Profil</a>
        <a href="<?= base_url() ?>" class="btn-secondary">Jelajahi Beranda Publik</a>
        <a href="<?= base_url('/logout') ?>" class="btn-secondary" style="color:#ef4444;">Keluar Sesi</a>
    </div>
</main>

</body>
</html>

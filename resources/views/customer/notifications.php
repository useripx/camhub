<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pusat Notifikasi — CamHub</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; font-family:'Plus Jakarta Sans', sans-serif; }
        body { background:#f8fafc; color:#0f172a; min-height:100vh; padding-bottom:60px; }

        .navbar { background:#ffffff; padding:18px 48px; border-bottom:1px solid #e2e8f0; display:flex; justify-content:space-between; align-items:center; }
        .brand-logo { font-size:24px; font-weight:800; color:#0f172a; text-decoration:none; }
        .nav-links { display:flex; gap:20px; align-items:center; }
        .nav-links a { text-decoration:none; color:#64748b; font-size:14px; font-weight:600; transition:0.2s; }
        .nav-links a:hover { color:#2563eb; }

        .container { max-width:800px; margin:40px auto; padding:0 20px; }
        .page-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; }
        .title-box { display:flex; align-items:center; gap:12px; }
        .page-title { font-size:26px; font-weight:800; color:#0f172a; letter-spacing:-0.5px; }
        .badge-count { background:#ef4444; color:#ffffff; font-size:12px; font-weight:700; padding:3px 9px; border-radius:20px; }

        .btn-mark-all { background:#eff6ff; color:#2563eb; border:1px solid #bfdbfe; padding:8px 16px; border-radius:8px; font-size:13px; font-weight:700; cursor:pointer; transition:0.2s; }
        .btn-mark-all:hover { background:#dbeafe; }

        .flash-alert { padding:14px 18px; border-radius:10px; font-size:14px; font-weight:600; margin-bottom:20px; }
        .flash-success { background:#dcfce7; color:#15803d; border:1px solid #bbf7d0; }

        .notif-list { display:flex; flex-direction:column; gap:12px; }
        .notif-card { background:#ffffff; border:1px solid #e2e8f0; border-radius:12px; padding:20px; display:flex; gap:16px; align-items:flex-start; transition:all 0.2s; position:relative; }
        .notif-card.unread { background:#f0f9ff; border-color:#bae6fd; }
        .notif-card:hover { border-color:#93c5fd; box-shadow:0 2px 4px rgba(0,0,0,0.03); }

        .notif-icon { width:42px; height:42px; border-radius:10px; background:#eff6ff; color:#2563eb; display:flex; align-items:center; justify-content:center; font-size:20px; flex-shrink:0; }
        .notif-card.unread .notif-icon { background:#2563eb; color:#ffffff; }

        .notif-content { flex:1; }
        .notif-header { display:flex; justify-content:space-between; align-items:baseline; margin-bottom:4px; }
        .notif-title { font-size:15px; font-weight:700; color:#0f172a; }
        .notif-time { font-size:12px; color:#94a3b8; }
        .notif-msg { font-size:13px; color:#475569; line-height:1.5; margin-bottom:8px; }

        .notif-actions { display:flex; gap:12px; align-items:center; }
        .notif-link { font-size:13px; font-weight:700; color:#2563eb; text-decoration:none; display:inline-flex; align-items:center; gap:4px; }
        .notif-link:hover { text-decoration:underline; }

        .btn-read-check { background:none; border:none; color:#94a3b8; font-size:12px; font-weight:600; cursor:pointer; padding:0; }
        .btn-read-check:hover { color:#2563eb; }

        .empty-state { background:#ffffff; border:1px solid #e2e8f0; border-radius:14px; padding:48px; text-align:center; color:#64748b; }
        .empty-icon { font-size:48px; margin-bottom:12px; }
        .empty-title { font-size:18px; font-weight:800; color:#0f172a; margin-bottom:6px; }
    </style>
</head>
<body>

    <header class="navbar">
        <a href="<?= base_url('/') ?>" class="brand-logo">CamHub</a>
        <div class="nav-links">
            <?php 
                $role = auth()['role'] ?? 'customer';
                $dashUrl = $role === 'admin' ? '/dashboard/admin' : ($role === 'partner' ? '/dashboard/mitra' : '/dashboard/pelanggan');
            ?>
            <a href="<?= base_url($dashUrl) ?>">Dashboard Saya</a>
            <a href="<?= base_url('/katalog') ?>">Katalog Kamera</a>
            <a href="<?= base_url('/logout') ?>" style="color:#ef4444;">Keluar</a>
        </div>
    </header>

    <main class="container">
        <div class="page-header">
            <div class="title-box">
                <h1 class="page-title">Pusat Notifikasi</h1>
                <?php if ($unreadCount > 0): ?>
                    <span class="badge-count"><?= $unreadCount ?> Belum Dibaca</span>
                <?php endif; ?>
            </div>
            <?php if ($unreadCount > 0): ?>
                <form action="<?= base_url('/notifikasi/baca-semua') ?>" method="POST" style="margin:0;">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn-mark-all">✓ Tandai Semua Sudah Dibaca</button>
                </form>
            <?php endif; ?>
        </div>

        <?php if ($msg = flash('success')): ?>
            <div class="flash-alert flash-success"><?= $msg ?></div>
        <?php endif; ?>

        <div class="notif-list">
            <?php if (empty($notifications)): ?>
                <div class="empty-state">
                    <div class="empty-icon">🔔</div>
                    <div class="empty-title">Belum Ada Notifikasi</div>
                    <p style="font-size:14px;">Pemberitahuan terkait sewa kamera, pencairan saldo, dan verifikasi dokumen KYC akan muncul di sini.</p>
                </div>
            <?php else: ?>
                <?php foreach ($notifications as $n): ?>
                    <div class="notif-card <?= empty($n['is_read']) ? 'unread' : '' ?>">
                        <div class="notif-icon">
                            <?php 
                                $t = strtolower($n['title']);
                                if (strpos($t, 'sewa') !== false || strpos($t, 'pesanan') !== false) {
                                    echo '📷';
                                } elseif (strpos($t, 'kyc') !== false) {
                                    echo '📄';
                                } elseif (strpos($t, 'saldo') !== false || strpos($t, 'rekber') !== false || strpos($t, 'dana') !== false) {
                                    echo '💳';
                                } elseif (strpos($t, 'titip') !== false) {
                                    echo '📦';
                                } else {
                                    echo '🔔';
                                }
                            ?>
                        </div>
                        <div class="notif-content">
                            <div class="notif-header">
                                <span class="notif-title"><?= htmlspecialchars($n['title']) ?></span>
                                <span class="notif-time"><?= date('d M Y, H:i', strtotime($n['created_at'])) ?></span>
                            </div>
                            <div class="notif-msg"><?= htmlspecialchars($n['message']) ?></div>
                            <div class="notif-actions">
                                <?php if (!empty($n['action_url'])): ?>
                                    <a href="<?= base_url($n['action_url']) ?>" class="notif-link">
                                        Lihat Rincian →
                                    </a>
                                <?php endif; ?>
                                
                                <?php if (empty($n['is_read'])): ?>
                                    <form action="<?= base_url('/notifikasi/' . $n['id'] . '/baca') ?>" method="POST" style="margin:0;">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn-read-check">Tandai sudah dibaca</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>

</body>
</html>


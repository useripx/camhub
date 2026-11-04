<?php
$user = $user ?? auth();
$role = $user['role'] ?? 'customer';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pusat Notifikasi — CamHub</title>
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
            margin-bottom:32px;
        }
        .user-fullname {
            font-size:17px;
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
            display:block;
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
            border-left:3px solid #3b82f6;
        }
        
        .sidebar-bottom {
            margin-top:auto;
            padding-top:24px;
            border-top:1px solid #1e293b;
            display:flex;
            flex-direction:column;
            gap:10px;
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

        .page-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; flex-wrap:wrap; gap:16px; }
        .title-box { display:flex; align-items:center; gap:12px; }
        .page-title { font-size:26px; font-weight:800; color:var(--text-primary); letter-spacing:-0.5px; }
        .badge-count { background:#ef4444; color:#ffffff; font-size:12px; font-weight:700; padding:4px 10px; border-radius:20px; }

        .btn-mark-all {
            background:rgba(37, 99, 235, 0.15);
            color:var(--accent-blue);
            border:1px solid rgba(37, 99, 235, 0.3);
            padding:8px 16px;
            border-radius:8px;
            font-size:13px;
            font-weight:700;
            cursor:pointer;
            transition:0.2s;
        }
        .btn-mark-all:hover { background:rgba(37, 99, 235, 0.25); }

        .flash-alert { padding:14px 18px; border-radius:10px; font-size:14px; font-weight:600; margin-bottom:20px; }
        .flash-success { background:rgba(16, 185, 129, 0.15); border:1px solid rgba(16, 185, 129, 0.3); color:#34d399; }

        .notif-list { display:flex; flex-direction:column; gap:12px; }
        .notif-card {
            background:var(--bg-card);
            border:1px solid var(--border-color);
            border-radius:12px;
            padding:20px;
            display:flex;
            gap:16px;
            align-items:flex-start;
            transition:all 0.2s;
            position:relative;
        }
        .notif-card.unread {
            border-color:var(--accent-blue);
            background:var(--bg-subtle);
        }
        .notif-card:hover {
            box-shadow:0 4px 12px rgba(0,0,0,0.06);
        }

        .notif-content { flex:1; }
        .notif-header { display:flex; justify-content:space-between; align-items:baseline; margin-bottom:6px; }
        .notif-title { font-size:15px; font-weight:700; color:var(--text-primary); }
        .notif-time { font-size:12px; color:var(--text-muted); }
        .notif-msg { font-size:13px; color:var(--text-secondary); line-height:1.5; margin-bottom:10px; }

        .notif-actions { display:flex; gap:12px; align-items:center; }
        .notif-link { font-size:13px; font-weight:700; color:var(--accent-blue); text-decoration:none; display:inline-flex; align-items:center; gap:4px; }
        .notif-link:hover { text-decoration:underline; }

        .btn-read-check { background:none; border:none; color:var(--text-muted); font-size:12px; font-weight:600; cursor:pointer; padding:0; }
        .btn-read-check:hover { color:var(--accent-blue); }

        .empty-state {
            background:var(--bg-card);
            border:1px solid var(--border-color);
            border-radius:14px;
            padding:48px;
            text-align:center;
            color:var(--text-secondary);
        }
        .empty-title { font-size:18px; font-weight:800; color:var(--text-primary); margin-bottom:6px; }

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
            <div class="user-role-label"><?= ucfirst($role) ?></div>
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

    <!-- AREA KONTEN NOTIFIKASI -->
    <main class="main-content">
        <div class="breadcrumb">
            <a href="<?= base_url('/dashboard/pelanggan') ?>">Dashboard</a>
            <span>/</span>
            <span>Pusat Notifikasi</span>
        </div>

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
                    <button type="submit" class="btn-mark-all">Tandai Semua Sudah Dibaca</button>
                </form>
            <?php endif; ?>
        </div>

        <?php if ($msg = flash('success')): ?>
            <div class="flash-alert flash-success"><?= $msg ?></div>
        <?php endif; ?>

        <div class="notif-list">
            <?php if (empty($notifications)): ?>
                <div class="empty-state">
                    <div class="empty-title">Belum Ada Notifikasi</div>
                    <p style="font-size:14px;">Pemberitahuan terkait sewa kamera, pencairan saldo, dan verifikasi dokumen KYC akan muncul di sini.</p>
                </div>
            <?php else: ?>
                <?php foreach ($notifications as $n): ?>
                    <div class="notif-card <?= empty($n['is_read']) ? 'unread' : '' ?>">
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

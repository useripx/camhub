<?php
$user = $user ?? auth();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Titip Jual Saya — CamHub</title>
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
            --table-hover: rgba(255, 255, 255, 0.02);
            --stepper-bg: #1e293b;
            --stepper-line: #1e3a5f;
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
            --table-hover: rgba(255, 255, 255, 0.02);
            --stepper-bg: #1e293b;
            --stepper-line: #1e3a5f;
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
            --table-hover: #f8fafc;
            --stepper-bg: #e2e8f0;
            --stepper-line: #cbd5e1;
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

        /* MAIN CONTENT */
        .main-content {
            flex:1;
            padding:40px 48px 60px;
            overflow-y:auto;
            max-width:1100px;
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
            display:flex;
            justify-content:space-between;
            align-items:center;
            margin-bottom:28px;
            flex-wrap:wrap;
            gap:16px;
        }
        .page-title {
            font-size:26px;
            font-weight:800;
            color:var(--text-primary);
            letter-spacing:-0.5px;
        }
        .page-desc {
            color:var(--text-secondary);
            font-size:14px;
            margin-top:4px;
        }
        
        .btn-add {
            background:#2563eb;
            color:#ffffff;
            text-decoration:none;
            font-weight:700;
            font-size:14px;
            padding:12px 20px;
            border-radius:10px;
            display:inline-flex;
            align-items:center;
            transition:background 0.2s;
        }
        .btn-add:hover { background:#1d4ed8; }

        .alert-success {
            background:rgba(16, 185, 129, 0.15);
            border:1px solid rgba(16, 185, 129, 0.3);
            color:#34d399;
            padding:14px 18px;
            border-radius:10px;
            font-size:14px;
            margin-bottom:24px;
            font-weight:600;
        }
        html[data-theme="light"] .alert-success {
            background:#ecfdf5;
            border-color:#a7f3d0;
            color:#065f46;
        }

        .empty-state {
            background:var(--bg-card);
            border:1px solid var(--border-color);
            border-radius:16px;
            padding:60px 24px;
            text-align:center;
        }
        .empty-title {
            font-size:18px;
            font-weight:700;
            color:var(--text-primary);
            margin-bottom:6px;
        }
        .empty-desc {
            font-size:14px;
            color:var(--text-secondary);
            max-width:460px;
            margin:0 auto 20px;
            line-height:1.6;
        }

        .consign-card {
            background:var(--bg-card);
            border:1px solid var(--border-color);
            border-radius:16px;
            padding:24px 28px;
            margin-bottom:20px;
            box-shadow:0 2px 8px rgba(0,0,0,0.04);
        }
        .consign-header {
            display:flex;
            justify-content:space-between;
            align-items:flex-start;
            margin-bottom:16px;
            flex-wrap:wrap;
            gap:12px;
        }
        .item-name {
            font-size:18px;
            font-weight:800;
            color:var(--text-primary);
        }
        .store-badge {
            font-size:13px;
            color:var(--text-muted);
            margin-top:4px;
            font-weight:500;
        }
        
        .status-pill {
            font-size:12px;
            font-weight:700;
            padding:6px 14px;
            border-radius:20px;
            text-transform:uppercase;
            letter-spacing:0.5px;
        }

        .grid-details {
            display:grid;
            grid-template-columns:repeat(auto-fit, minmax(180px, 1fr));
            gap:16px;
            background:var(--bg-subtle);
            border:1px solid var(--border-subtle);
            border-radius:12px;
            padding:16px 20px;
            margin-bottom:20px;
        }
        .detail-item .label {
            font-size:12px;
            color:var(--text-muted);
            margin-bottom:4px;
            font-weight:600;
            text-transform:uppercase;
        }
        .detail-item .val {
            font-size:15px;
            font-weight:700;
            color:var(--text-primary);
        }

        /* Stepper Lifecycle Progress */
        .lifecycle-stepper {
            display:flex;
            justify-content:space-between;
            margin-top:20px;
            position:relative;
            padding:0 10px;
        }
        .lifecycle-stepper::before {
            content:'';
            position:absolute;
            top:12px;
            left:30px;
            right:30px;
            height:3px;
            background:var(--stepper-line);
            z-index:1;
        }
        .step-node {
            position:relative;
            z-index:2;
            text-align:center;
            flex:1;
        }
        .step-dot {
            width:26px;
            height:26px;
            border-radius:50%;
            background:var(--stepper-bg);
            color:var(--text-muted);
            display:inline-flex;
            align-items:center;
            justify-content:center;
            font-size:12px;
            font-weight:700;
            margin-bottom:6px;
            border:2px solid var(--border-color);
        }
        .step-node.active .step-dot {
            background:#2563eb;
            color:#ffffff;
            border-color:#3b82f6;
        }
        .step-node.completed .step-dot {
            background:#10b981;
            color:#ffffff;
            border-color:#34d399;
        }
        .step-text {
            font-size:12px;
            font-weight:600;
            color:var(--text-muted);
        }
        .step-node.active .step-text {
            color:var(--accent-blue);
            font-weight:700;
        }
        .step-node.completed .step-text {
            color:#10b981;
        }

        .item-desc-box {
            font-size:13px;
            color:var(--text-secondary);
            line-height:1.6;
            border-left:3px solid var(--accent-blue);
            padding-left:12px;
            margin-top:14px;
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
            <li class="nav-item active">
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

    <!-- KONTEN UTAMA -->
    <main class="main-content">
        <div class="breadcrumb">
            <a href="<?= base_url('/dashboard/pelanggan') ?>">Dashboard</a>
            <span>/</span>
            <span>Titip Jual Saya</span>
        </div>

        <div class="page-header">
            <div>
                <h1 class="page-title">Titip Jual Saya</h1>
                <p class="page-desc">Pantau status pengajuan unit kamera yang Anda titipkan ke Toko Mitra CamHub.</p>
            </div>
            <a href="<?= base_url('/titip-jual/ajukan') ?>" class="btn-add">
                Ajukan Titip Jual Baru
            </a>
        </div>

        <?php if (!empty($success)): ?>
            <div class="alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <?php if (empty($consignments)): ?>
            <div class="empty-state">
                <h3 class="empty-title">Belum Ada Kamera yang Dititipkan</h3>
                <p class="empty-desc">Punya kamera tidak terpakai? Titipkan sekarang ke Toko Mitra CamHub terdekat dan dapatkan hasil penjualan secara aman via Escrow.</p>
                <a href="<?= base_url('/titip-jual/ajukan') ?>" class="btn-add">Mulai Titip Jual Sekarang</a>
            </div>
        <?php else: ?>
            <?php foreach ($consignments as $c): 
                $consignObj = new \App\Models\Consignment($c);
                $badge = $consignObj->getStatusBadge();
                $st = $c['status'];
            ?>
                <div class="consign-card">
                    <div class="consign-header">
                        <div>
                            <div class="item-name"><?= htmlspecialchars($c['item_name']) ?></div>
                            <div class="store-badge">Toko Mitra: <strong><?= htmlspecialchars($c['store_name'] ?? 'Mitra Lapak') ?></strong> (<?= htmlspecialchars($c['store_city'] ?? '') ?>) · Diajukan pada <?= date('d M Y, H:i', strtotime($c['created_at'])) ?></div>
                        </div>
                        <span class="status-pill" style="background:<?= $badge['bg'] ?>; color:<?= $badge['color'] ?>;">
                            <?= $badge['label'] ?>
                        </span>
                    </div>

                    <div class="grid-details">
                        <div class="detail-item">
                            <div class="label">Harga Diminta (User)</div>
                            <div class="val"><?= $consignObj->getFormattedRequestedPrice() ?></div>
                        </div>
                        <div class="detail-item">
                            <div class="label">Harga Kesepakatan</div>
                            <div class="val"><?= $consignObj->getFormattedAgreedPrice() ?></div>
                        </div>
                        <div class="detail-item">
                            <div class="label">Komisi Toko Mitra</div>
                            <div class="val"><?= number_format((float)$c['commission_percent'], 0) ?>%</div>
                        </div>
                        <div class="detail-item">
                            <div class="label">Estimasi Dana Cair</div>
                            <div class="val" style="color:#10b981;"><?= $consignObj->getFormattedEstimatedPayout() ?></div>
                        </div>
                    </div>

                    <!-- Progress Lifecycle Stepper -->
                    <div class="lifecycle-stepper">
                        <div class="step-node <?= in_array($st, ['submitted', 'inspected', 'approved', 'listed', 'sold']) ? 'completed' : '' ?>">
                            <div class="step-dot">1</div>
                            <div class="step-text">Pengajuan</div>
                        </div>
                        <div class="step-node <?= in_array($st, ['inspected', 'approved', 'listed', 'sold']) ? 'completed' : ($st === 'submitted' ? 'active' : '') ?>">
                            <div class="step-dot">2</div>
                            <div class="step-text">Inspeksi Mitra</div>
                        </div>
                        <div class="step-node <?= in_array($st, ['approved', 'listed', 'sold']) ? 'completed' : ($st === 'inspected' ? 'active' : '') ?>">
                            <div class="step-dot">3</div>
                            <div class="step-text">Kesepakatan</div>
                        </div>
                        <div class="step-node <?= in_array($st, ['listed', 'sold']) ? 'completed' : ($st === 'approved' ? 'active' : '') ?>">
                            <div class="step-dot">4</div>
                            <div class="step-text">Tayang di Katalog</div>
                        </div>
                        <div class="step-node <?= ($st === 'sold') ? 'completed' : ($st === 'listed' ? 'active' : '') ?>">
                            <div class="step-dot">5</div>
                            <div class="step-text">Terjual & Cair</div>
                        </div>
                    </div>

                    <div class="item-desc-box">
                        <strong>Catatan Kondisi Unit:</strong> <?= htmlspecialchars($c['item_description']) ?>
                    </div>
                </div>
            <?php endforeach; ?>
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

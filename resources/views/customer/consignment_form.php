<?php
$user = $user ?? auth();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajukan Titip Jual Kamera — CamHub</title>
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

        /* MAIN CONTENT */
        .main-content {
            flex:1;
            padding:40px 48px 60px;
            overflow-y:auto;
            max-width:920px;
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

        .form-card {
            background:var(--bg-card);
            border:1px solid var(--border-color);
            border-radius:16px;
            padding:36px;
            box-shadow:0 2px 10px rgba(0,0,0,0.05);
        }
        .header-title {
            font-size:26px;
            font-weight:800;
            color:var(--text-primary);
            margin-bottom:8px;
            letter-spacing:-0.5px;
        }
        .header-subtitle {
            color:var(--text-secondary);
            font-size:14px;
            line-height:1.6;
            margin-bottom:28px;
        }
        
        .alert-error {
            background:rgba(239, 68, 68, 0.15);
            border:1px solid rgba(239, 68, 68, 0.3);
            color:#f87171;
            padding:14px 18px;
            border-radius:10px;
            font-size:14px;
            margin-bottom:24px;
        }
        html[data-theme="light"] .alert-error {
            background:#fee2e2;
            border-color:#fca5a5;
            color:#991b1b;
        }
        .alert-error ul { margin-left:20px; margin-top:6px; }
        
        .form-group { margin-bottom:22px; }
        .form-label {
            display:block;
            font-size:14px;
            font-weight:700;
            color:var(--text-primary);
            margin-bottom:8px;
        }
        .form-control {
            width:100%;
            padding:12px 16px;
            background:var(--bg-input);
            border:1px solid var(--border-color);
            border-radius:10px;
            font-size:14px;
            color:var(--text-primary);
            transition:border 0.2s;
        }
        .form-control:focus {
            outline:none;
            border-color:var(--accent-blue);
            box-shadow:0 0 0 3px rgba(37,99,235,0.15);
        }
        .form-hint {
            font-size:12px;
            color:var(--text-muted);
            margin-top:6px;
        }

        .info-box {
            background:var(--bg-subtle);
            border:1px solid var(--border-subtle);
            border-radius:12px;
            padding:18px;
            margin-bottom:28px;
            font-size:13px;
            color:var(--text-secondary);
            line-height:1.6;
        }
        .info-box strong { color:var(--text-primary); }

        .btn-submit {
            width:100%;
            background:#2563eb;
            color:#ffffff;
            font-weight:700;
            font-size:15px;
            padding:14px;
            border:none;
            border-radius:10px;
            cursor:pointer;
            transition:background 0.2s;
        }
        .btn-submit:hover { background:#1d4ed8; }
        .btn-back {
            display:inline-block;
            text-align:center;
            width:100%;
            color:var(--text-muted);
            font-size:14px;
            font-weight:600;
            text-decoration:none;
            margin-top:14px;
            transition:color 0.2s;
        }
        .btn-back:hover { color:var(--text-primary); }
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
            <a href="<?= base_url('/titip-jual/saya') ?>">Titip Jual Saya</a>
            <span>/</span>
            <span>Pengajuan Baru</span>
        </div>

        <div class="form-card">
            <h1 class="header-title">Pengajuan Titip Jual Kamera</h1>
            <p class="header-subtitle">Titipkan kamera Anda ke Toko Mitra terverifikasi CamHub. Unit akan melalui inspeksi fisik, dipasarkan ke katalog publik, dan pembayaran diamankan dengan rekening bersama Escrow.</p>

            <?php if (!empty($errors)): ?>
                <div class="alert-error">
                    <strong>Mohon perbaiki kesalahan berikut:</strong>
                    <ul>
                        <?php foreach ($errors as $err): ?>
                            <li><?= htmlspecialchars($err) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('/titip-jual/ajukan') ?>" method="POST">
                <?= csrf_field() ?>

                <div class="form-group">
                    <label class="form-label" for="item_name">Nama Kamera & Varian Lengkap</label>
                    <input type="text" id="item_name" name="item_name" class="form-control" placeholder="Contoh: Sony Alpha A7 III Body Only (Ex Garansi Resmi)" value="<?= htmlspecialchars($old['item_name'] ?? '') ?>" required>
                    <div class="form-hint">Sebutkan merk, tipe, dan varian secara jelas untuk mempermudah identifikasi unit.</div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="store_id">Pilih Toko Mitra Penampung</label>
                    <select id="store_id" name="store_id" class="form-control" required>
                        <option value="">-- Pilih Toko Mitra Terdekat --</option>
                        <?php foreach (($stores ?? []) as $s): ?>
                            <option value="<?= (int) $s['id'] ?>" <?= ((int)($old['store_id'] ?? 0) === (int)$s['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($s['store_name']) ?> (<?= htmlspecialchars($s['city']) ?>) — <?= htmlspecialchars($s['address']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="form-hint">Unit fisik akan diserahkan/dikirimkan ke toko mitra yang Anda pilih untuk proses inspeksi kondisi.</div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="requested_price">Ekspektasi Harga Jual Bersih (Rp)</label>
                    <input type="number" id="requested_price" name="requested_price" class="form-control" placeholder="Contoh: 18500000" min="100000" step="50000" value="<?= htmlspecialchars((string)($old['requested_price'] ?? '')) ?>" required>
                    <div class="form-hint">Harga estimasi yang Anda harapkan sebelum dipotong komisi standar toko mitra (10%).</div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="item_description">Deskripsi Kondisi Fisik & Kelengkapan Unit</label>
                    <textarea id="item_description" name="item_description" class="form-control" rows="5" placeholder="Contoh: Shutter Count ~12.000, sensor bersih bebas jamur, fisik mulus 95%, karet rapat. Kelengkapan: Unit kamera, baterai original 2 pcs, charger, strap, boks buku lengkap..." required><?= htmlspecialchars($old['item_description'] ?? '') ?></textarea>
                    <div class="form-hint">Jelaskan kondisi seakurat mungkin agar mempercepat proses inspeksi dan persetujuan harga toko mitra.</div>
                </div>

                <div class="info-box">
                    <strong>Ketentuan Konsinyasi CamHub:</strong>
                    <ol style="margin-left:20px; margin-top:6px;">
                        <li>Toko Mitra berhak melakukan inspeksi teknis (sensor, lensa, shutter) sebelum menyetujui penayangan.</li>
                        <li>Komisi standar bagi toko mitra adalah <strong>10%</strong> dari harga jual final yang disepakati.</li>
                        <li>Dana hasil penjualan akan otomatis dialokasikan ke rekening Anda setelah pembeli mengonfirmasi penerimaan barang.</li>
                    </ol>
                </div>

                <button type="submit" class="btn-submit">Kirim Pengajuan Titip Jual</button>
                <a href="<?= base_url('/titip-jual/saya') ?>" class="btn-back">Batal & Kembali ke Titip Jual Saya</a>
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

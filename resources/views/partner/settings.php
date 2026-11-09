<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Profil Lapak — CamHub Mitra</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; font-family:'Plus Jakarta Sans', sans-serif; }
        body { background:#F8FAFC; color:#0F172A; display:flex; min-height:100vh; }
        .sidebar { width: 260px; background: #0B1320; color: #FFFFFF; display: flex; flex-direction: column; padding: 32px 24px; flex-shrink: 0; min-height: 100vh; }
        .brand-logo { font-size: 24px; font-weight: 800; color: #FFFFFF; text-decoration: none; margin-bottom: 2px; display: block; }
        .brand-store { font-size: 14px; font-weight: 600; color: #CBD5E1; margin-bottom: 36px; }
        .nav-menu { list-style: none; display: flex; flex-direction: column; gap: 8px; flex-grow: 1; }
        .nav-item a { display: flex; align-items: center; gap: 14px; padding: 12px 14px; color: #94A3B8; text-decoration: none; font-size: 14px; font-weight: 600; border-radius: 8px; transition: all 0.2s; }
        .nav-item a:hover { color: #FFFFFF; background: rgba(255, 255, 255, 0.05); }
        .nav-item.active a { color: #FFFFFF; background: rgba(255, 255, 255, 0.1); }
        .nav-icon { font-size: 16px; width: 20px; text-align: center; }
        .sidebar-footer { border-top: 1px solid rgba(255, 255, 255, 0.1); padding-top: 20px; display: flex; flex-direction: column; gap: 10px; }
        .sidebar-footer a { color: #94A3B8; text-decoration: none; font-size: 13px; font-weight: 600; padding: 4px 0; }
        
        .main-content { flex-grow: 1; padding: 40px 48px; overflow-y: auto; max-width: 800px; }
        .page-header { margin-bottom: 28px; }
        .page-title { font-size: 28px; font-weight: 800; color: #0F172A; }
        
        .flash-alert { padding: 14px 18px; border-radius: 10px; font-size: 14px; font-weight: 600; margin-bottom: 24px; }
        .flash-success { background: #DCFCE7; color: #15803D; border: 1px solid #BBF7D0; }
        .flash-error { background: #FEE2E2; color: #B91C1C; border: 1px solid #FECACA; }

        .card { background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 14px; padding: 32px; box-shadow: 0 1px 3px rgba(0,0,0,0.02); }
        .form-group { margin-bottom: 20px; }
        .form-label { display: block; font-size: 14px; font-weight: 700; color: #1E293B; margin-bottom: 8px; }
        .form-input { width: 100%; padding: 12px 16px; border: 1px solid #CBD5E1; border-radius: 8px; font-size: 14px; }
        .form-input:focus { border-color: #2563EB; outline: none; }
        .btn-submit { background: #2563EB; color: #FFFFFF; border: none; padding: 12px 24px; border-radius: 8px; font-weight: 700; cursor: pointer; }
        .btn-submit:hover { background: #1D4ED8; }
    </style>
    <script>
        (function() {
            const theme = localStorage.getItem('camhub_theme') || 'dark';
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>
</head>
<body>

    <aside class="sidebar">
        <div>
            <a href="<?= base_url('/dashboard/mitra') ?>" class="brand-logo">CamHub</a>
            <div class="brand-store"><?= htmlspecialchars($partner['store_name'] ?? 'Mitra Lapak') ?></div>
        </div>
        <ul class="nav-menu">
            <li class="nav-item"><a href="<?= base_url('/dashboard/mitra') ?>">Beranda</a></li>
            <li class="nav-item"><a href="<?= base_url('/dashboard/mitra/produk') ?>">Kelola Produk</a></li>
            <li class="nav-item"><a href="<?= base_url('/dashboard/mitra/jadwal') ?>">Jadwal Sewa</a></li>
            <li class="nav-item"><a href="<?= base_url('/dashboard/mitra/transaksi') ?>">Transaksi</a></li>
            <li class="nav-item"><a href="<?= base_url('/dashboard/mitra/saldo') ?>">Saldo & Pencairan</a></li>
            <li class="nav-item active"><a href="<?= base_url('/dashboard/mitra/pengaturan') ?>">Pengaturan</a></li>
            <li class="nav-item"><a href="<?= base_url('/dashboard/mitra/titip-jual') ?>">Kelola Titip Jual</a></li>
        </ul>
        <div class="sidebar-footer">
            <button type="button" class="btn-theme-toggle" onclick="toggleCamHubTheme()" title="Beralih Mode Gelap/Terang" style="width:100%; background:#16243a; color:#cbd5e1; border:1px solid #1e3a5f; padding:9px 12px; border-radius:8px; font-size:12px; font-weight:600; cursor:pointer; text-align:center; margin-bottom:8px;">
                <span id="themeToggleText">Mode Terang</span>
            </button>
            <a href="<?= base_url('/') ?>" target="_blank">Website Publik</a>
            <form action="<?= base_url('/logout') ?>" method="POST" style="margin:0; width:100%;">
                <?= csrf_field() ?>
                <button type="submit" style="background:#dc2626; color:#ffffff; font-weight:700; border-radius:8px; justify-content:center; padding:10px 14px; margin-top:8px; width:100%; display:flex; align-items:center; gap:8px; border:none; cursor:pointer;">
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    <main class="main-content">
        <div class="page-header">
            <h1 class="page-title">Pengaturan Profil Lapak Toko</h1>
            <p style="color:#64748B; font-size:14px; margin-top:4px;">Perbarui data fisik toko, lokasi kota penjemputan kamera, dan nomor kontak yang tertera pada etalase sewa.</p>
        </div>

        <?php if ($msg = flash('success')): ?>
            <div class="flash-alert flash-success"><?= $msg ?></div>
        <?php endif; ?>
        <?php if ($msg = flash('error')): ?>
            <div class="flash-alert flash-error"><?= $msg ?></div>
        <?php endif; ?>

        <div class="card">
            <form action="<?= base_url('/dashboard/mitra/pengaturan/update') ?>" method="POST">
                <?= csrf_field() ?>

                <div class="form-group">
                    <label class="form-label" for="store_name">Nama Toko Lapak</label>
                    <input type="text" id="store_name" name="store_name" class="form-input" value="<?= htmlspecialchars($partner['store_name'] ?? 'Yogi Camera') ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="city">Kota Operasional</label>
                    <input type="text" id="city" name="city" class="form-input" value="<?= htmlspecialchars($partner['store_city'] ?? 'Yogyakarta') ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="address">Alamat Lengkap Toko</label>
                    <input type="text" id="address" name="address" class="form-input" value="<?= htmlspecialchars($partner['store_address'] ?? 'Jl. Kaliurang KM 5 No. 42') ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="phone">Nomor Telepon / WhatsApp Lapak</label>
                    <input type="text" id="phone" name="phone" class="form-input" value="<?= htmlspecialchars($partner['store_phone'] ?? '082222222222') ?>" required>
                </div>

                <button type="submit" class="btn-submit">Simpan Pembaruan Profil Toko</button>
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

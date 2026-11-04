<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Unit Fisik: <?= htmlspecialchars($product['name']) ?> — CamHub</title>
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
        
        .main-content { flex-grow: 1; padding: 40px 48px; overflow-y: auto; max-width: 1100px; }
        .page-header { margin-bottom: 28px; }
        .page-title { font-size: 28px; font-weight: 800; color: #0F172A; }
        
        .flash-alert { padding: 14px 18px; border-radius: 10px; font-size: 14px; font-weight: 600; margin-bottom: 24px; }
        .flash-success { background: #DCFCE7; color: #15803D; border: 1px solid #BBF7D0; }
        .flash-error { background: #FEE2E2; color: #B91C1C; border: 1px solid #FECACA; }

        .layout-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 24px; }
        .card { background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 14px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.02); }
        .card-title { font-size: 18px; font-weight: 800; color: #0F172A; margin-bottom: 16px; }
        
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th { background: #F8FAFC; padding: 12px 16px; font-size: 13px; font-weight: 700; color: #475569; border-bottom: 1px solid #E2E8F0; }
        td { padding: 14px 16px; font-size: 14px; border-bottom: 1px solid #F1F5F9; }
        
        .badge { display: inline-block; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 700; }
        .badge-available { background: #DCFCE7; color: #15803D; }
        .badge-rented { background: #FEF3C7; color: #D97706; }
        .badge-maintenance { background: #FEE2E2; color: #B91C1C; }
        
        .form-group { margin-bottom: 16px; }
        .form-label { display: block; font-size: 13px; font-weight: 700; color: #334155; margin-bottom: 6px; }
        .form-input, .form-select { width: 100%; padding: 10px 14px; border: 1px solid #CBD5E1; border-radius: 8px; font-size: 14px; }
        .btn-submit { background: #2563EB; color: #FFFFFF; border: none; padding: 12px; width: 100%; border-radius: 8px; font-weight: 700; cursor: pointer; }
        .btn-submit:hover { background: #1D4ED8; }
    </style>
</head>
<body>

    <aside class="sidebar">
        <div>
            <a href="<?= base_url('/dashboard/mitra') ?>" class="brand-logo">CamHub</a>
            <div class="brand-store"><?= htmlspecialchars($partner['store_name'] ?? 'Mitra Lapak') ?></div>
        </div>
        <ul class="nav-menu">
            <li class="nav-item"><a href="<?= base_url('/dashboard/mitra') ?>"><span class="nav-icon">⌂</span> Beranda</a></li>
            <li class="nav-item active"><a href="<?= base_url('/dashboard/mitra/produk') ?>"><span class="nav-icon">▣</span> Kelola Produk</a></li>
            <li class="nav-item"><a href="<?= base_url('/dashboard/mitra/jadwal') ?>"><span class="nav-icon">▦</span> Jadwal Sewa</a></li>
            <li class="nav-item"><a href="<?= base_url('/dashboard/mitra/transaksi') ?>"><span class="nav-icon">⧉</span> Transaksi</a></li>
            <li class="nav-item"><a href="<?= base_url('/dashboard/mitra/saldo') ?>"><span class="nav-icon">Rp</span> Saldo & Pencairan</a></li>
            <li class="nav-item"><a href="<?= base_url('/dashboard/mitra/pengaturan') ?>"><span class="nav-icon">⚙</span> Pengaturan</a></li>
            <li class="nav-item"><a href="<?= base_url('/dashboard/mitra/titip-jual') ?>"><span class="nav-icon">📦</span> Kelola Titip Jual</a></li>
        </ul>
        <div class="sidebar-footer">
            <a href="<?= base_url('/') ?>" target="_blank"><span>🌐</span> Website Publik</a>
            <form action="<?= base_url('/logout') ?>" method="POST" style="margin:0; width:100%;">
                <?= csrf_field() ?>
                <button type="submit" style="background:#dc2626; color:#ffffff; font-weight:700; border-radius:8px; justify-content:center; padding:10px 14px; margin-top:8px; width:100%; display:flex; align-items:center; gap:8px; border:none; cursor:pointer;">
                    <span>🚪</span> Keluar Akun
                </button>
            </form>
        </div>
    </aside>

    <main class="main-content">
        <div class="page-header">
            <a href="<?= base_url('/dashboard/mitra/produk') ?>" style="color:#64748B; text-decoration:none; font-size:13px; font-weight:600; display:inline-block; margin-bottom:8px;">← Kembali ke Kelola Produk</a>
            <h1 class="page-title">Unit Fisik & Serial Number</h1>
            <p style="color:#64748B; font-size:14px; margin-top:4px;">Kelola inventaris nomor seri kamera untuk: <strong><?= htmlspecialchars($product['name']) ?></strong></p>
        </div>

        <?php if ($msg = flash('success')): ?>
            <div class="flash-alert flash-success"><?= $msg ?></div>
        <?php endif; ?>
        <?php if ($msg = flash('error')): ?>
            <div class="flash-alert flash-error"><?= $msg ?></div>
        <?php endif; ?>

        <div class="layout-grid">
            <div class="card">
                <h2 class="card-title">Daftar Unit Fisik Terdaftar (<?= count($units) ?>)</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Nomor Seri (Serial Number)</th>
                            <th>Kondisi Fisik</th>
                            <th>Status Ketersediaan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($units)): ?>
                            <tr>
                                <td colspan="3" style="text-align:center; padding:30px; color:#64748B;">Belum ada nomor seri fisik yang didaftarkan untuk kamera ini.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($units as $u): ?>
                                <tr>
                                    <td><strong style="color:#0F172A;"><?= htmlspecialchars($u['serial_number']) ?></strong></td>
                                    <td style="color:#475569;"><?= htmlspecialchars($u['condition_status']) ?></td>
                                    <td>
                                        <?php if ($u['availability_status'] === 'available'): ?>
                                            <span class="badge badge-available">Tersedia</span>
                                        <?php elseif ($u['availability_status'] === 'rented'): ?>
                                            <span class="badge badge-rented">Sedang Disewa</span>
                                        <?php else: ?>
                                            <span class="badge badge-maintenance">Maintenance</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="card">
                <h2 class="card-title">+ Tambah Unit Fisik</h2>
                <form action="<?= base_url('/dashboard/mitra/produk/' . $product['id'] . '/unit/simpan') ?>" method="POST">
                    <?= csrf_field() ?>
                    <div class="form-group">
                        <label class="form-label" for="serial_number">Nomor Seri Kamera (SN)</label>
                        <input type="text" id="serial_number" name="serial_number" class="form-input" placeholder="Contoh: SN-882941-R50" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="condition_status">Catatan Kondisi Fisik</label>
                        <input type="text" id="condition_status" name="condition_status" class="form-input" placeholder="Contoh: Sangat Baik / Mulus 99%" value="Sangat Baik">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="availability_status">Status Awal</label>
                        <select id="availability_status" name="availability_status" class="form-select">
                            <option value="available">Tersedia (Siap Disewa)</option>
                            <option value="maintenance">Maintenance / Perawatan</option>
                        </select>
                    </div>

                    <button type="submit" class="btn-submit">+ Daftarkan Unit Fisik</button>
                </form>
            </div>
        </div>
    </main>

</body>
</html>

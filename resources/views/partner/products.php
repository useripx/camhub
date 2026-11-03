<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Produk — CamHub Mitra Lapak</title>
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
        .sidebar-footer a, .sidebar-footer button { display: flex; align-items: center; gap: 10px; color: #94A3B8; text-decoration: none; font-size: 13px; font-weight: 600; background: none; border: none; cursor: pointer; padding: 8px 12px; border-radius: 6px; transition: 0.2s; width: 100%; text-align: left; }
        .sidebar-footer button.btn-logout { color: #F87171; }
        
        .main-content { flex-grow: 1; padding: 40px 48px; overflow-y: auto; max-width: 1200px; }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 28px; }
        .page-title { font-size: 28px; font-weight: 800; color: #0F172A; }
        .btn-add { background: #2563EB; color: #FFFFFF; text-decoration: none; padding: 12px 20px; border-radius: 8px; font-weight: 700; font-size: 14px; display: inline-flex; align-items: center; gap: 8px; }
        .btn-add:hover { background: #1D4ED8; }
        
        .flash-alert { padding: 14px 18px; border-radius: 10px; font-size: 14px; font-weight: 600; margin-bottom: 24px; }
        .flash-success { background: #DCFCE7; color: #15803D; border: 1px solid #BBF7D0; }
        .flash-error { background: #FEE2E2; color: #B91C1C; border: 1px solid #FECACA; }

        .table-card { background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 14px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.02); }
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th { background: #F8FAFC; padding: 16px 20px; font-size: 13px; font-weight: 700; color: #475569; border-bottom: 1px solid #E2E8F0; }
        td { padding: 16px 20px; font-size: 14px; border-bottom: 1px solid #F1F5F9; vertical-align: middle; }
        tr:last-child td { border-bottom: none; }
        
        .item-info { display: flex; align-items: center; gap: 14px; }
        .item-thumb { width: 50px; height: 50px; background: #F1F5F9; border-radius: 8px; display: flex; align-items: center; justify-content: center; overflow: hidden; }
        .item-thumb img { max-width: 80%; max-height: 80%; object-fit: contain; }
        .badge-kategori { background: #E0E7FF; color: #4338CA; font-size: 12px; font-weight: 700; padding: 3px 8px; border-radius: 6px; }
        
        .actions-group { display: flex; gap: 6px; }
        .btn-action { padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600; text-decoration: none; border: 1px solid #CBD5E1; color: #334155; }
        .btn-action:hover { background: #F1F5F9; }
        .btn-action-primary { background: #EFF6FF; color: #2563EB; border-color: #BFDBFE; }
        .btn-action-danger { color: #DC2626; border-color: #FECACA; background: #FEF2F2; cursor: pointer; }
    </style>
</head>
<body>

    <aside class="sidebar">
        <div>
            <a href="<?= base_url('/dashboard/mitra') ?>" class="brand-logo">CamHub</a>
            <div class="brand-store"><?= htmlspecialchars($partner['store_name'] ?? 'Mitra Lapak') ?></div>
        </div>

        <ul class="nav-menu">
            <li class="nav-item <?= ($activeMenu === 'beranda') ? 'active' : '' ?>">
                <a href="<?= base_url('/dashboard/mitra') ?>"><span class="nav-icon">⌂</span> Beranda</a>
            </li>
            <li class="nav-item <?= ($activeMenu === 'produk') ? 'active' : '' ?>">
                <a href="<?= base_url('/dashboard/mitra/produk') ?>"><span class="nav-icon">▣</span> Kelola Produk</a>
            </li>
            <li class="nav-item <?= ($activeMenu === 'jadwal') ? 'active' : '' ?>">
                <a href="<?= base_url('/dashboard/mitra/jadwal') ?>"><span class="nav-icon">▦</span> Jadwal Sewa</a>
            </li>
            <li class="nav-item <?= ($activeMenu === 'transaksi') ? 'active' : '' ?>">
                <a href="<?= base_url('/dashboard/mitra/transaksi') ?>"><span class="nav-icon">⧉</span> Transaksi</a>
            </li>
            <li class="nav-item <?= ($activeMenu === 'saldo') ? 'active' : '' ?>">
                <a href="<?= base_url('/dashboard/mitra/saldo') ?>"><span class="nav-icon">Rp</span> Saldo & Pencairan</a>
            </li>
            <li class="nav-item <?= ($activeMenu === 'pengaturan') ? 'active' : '' ?>">
                <a href="<?= base_url('/dashboard/mitra/pengaturan') ?>"><span class="nav-icon">⚙</span> Pengaturan</a>
            </li>
        </ul>

        <div class="sidebar-footer">
            <a href="<?= base_url('/') ?>" target="_blank"><span>🌐</span> Website Publik</a>
            <form action="<?= base_url('/logout') ?>" method="POST" style="margin:0;">
                <?= csrf_field() ?>
                <button type="submit" class="btn-logout"><span>🚪</span> Keluar Akun</button>
            </form>
        </div>
    </aside>

    <main class="main-content">
        <div class="page-header">
            <div>
                <h1 class="page-title">Kelola Inventaris Produk</h1>
                <p style="color:#64748B; font-size:14px; margin-top:4px;">Daftar seluruh kamera, lensa, dan aksesoris yang terdaftar di etalase toko Anda.</p>
            </div>
            <a href="<?= base_url('/dashboard/mitra/produk/tambah') ?>" class="btn-add">
                <span>+</span> Tambah Produk Baru
            </a>
        </div>

        <?php if ($msg = flash('success')): ?>
            <div class="flash-alert flash-success"><?= $msg ?></div>
        <?php endif; ?>
        <?php if ($msg = flash('error')): ?>
            <div class="flash-alert flash-error"><?= $msg ?></div>
        <?php endif; ?>

        <div class="table-card">
            <table>
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>Kategori</th>
                        <th>Opsi</th>
                        <th>Tarif Sewa Harian</th>
                        <th>Unit Fisik</th>
                        <th style="text-align:right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($products)): ?>
                        <tr>
                            <td colspan="6" style="text-align:center; padding:40px; color:#64748B;">Belum ada produk yang didaftarkan.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($products as $p): ?>
                            <tr>
                                <td>
                                    <div class="item-info">
                                        <div class="item-thumb">
                                            <?php if (!empty($p['primary_image'])): ?>
                                                <img src="<?= base_url('/' . ltrim($p['primary_image'], '/')) ?>" alt="" onerror="this.src='<?= base_url('/assets/img/canon_r50.png') ?>'">
                                            <?php else: ?>
                                                <span>📷</span>
                                            <?php endif; ?>
                                        </div>
                                        <div>
                                            <strong style="color:#0F172A; display:block;"><?= htmlspecialchars($p['name']) ?></strong>
                                            <span style="font-size:12px; color:#64748B;"><?= htmlspecialchars($p['condition_note']) ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge-kategori"><?= htmlspecialchars($p['category_name']) ?></span>
                                </td>
                                <td>
                                    <span style="font-size:13px; font-weight:600; color:#475569;">
                                        <?= ($p['is_rental'] ? 'Sewa' : '') . ($p['is_rental'] && $p['is_sale'] ? ' & ' : '') . ($p['is_sale'] ? 'Jual' : '') ?>
                                    </span>
                                </td>
                                <td>
                                    <strong style="color:#0F172A;">Rp <?= number_format($p['daily_price'], 0, ',', '.') ?></strong>
                                    <span style="font-size:12px; color:#64748B;">/hari</span>
                                </td>
                                <td>
                                    <span style="font-weight:700; color:<?= $p['available_units'] > 0 ? '#16A34A' : '#DC2626' ?>;">
                                        <?= (int)$p['available_units'] ?> / <?= (int)$p['total_units'] ?> Tersedia
                                    </span>
                                </td>
                                <td style="text-align:right;">
                                    <div class="actions-group" style="justify-content: flex-end;">
                                        <a href="<?= base_url('/dashboard/mitra/produk/' . $p['id'] . '/unit') ?>" class="btn-action btn-action-primary" title="Kelola Serial Number Unit Fisik">
                                            Unit (<?= (int)$p['total_units'] ?>)
                                        </a>
                                        <a href="<?= base_url('/dashboard/mitra/produk/' . $p['id'] . '/edit') ?>" class="btn-action">
                                            Edit
                                        </a>
                                        <form action="<?= base_url('/dashboard/mitra/produk/' . $p['id'] . '/hapus') ?>" method="POST" style="margin:0;" onsubmit="return confirm('Hapus produk ini dari toko?');">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn-action btn-action-danger">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

</body>
</html>

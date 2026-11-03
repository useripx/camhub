<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $isEdit ? 'Edit Produk' : 'Tambah Produk Baru' ?> — CamHub Mitra Lapak</title>
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
        
        .main-content { flex-grow: 1; padding: 40px 48px; overflow-y: auto; max-width: 900px; }
        .page-header { margin-bottom: 28px; }
        .page-title { font-size: 28px; font-weight: 800; color: #0F172A; }
        
        .flash-alert { padding: 14px 18px; border-radius: 10px; font-size: 14px; font-weight: 600; margin-bottom: 24px; }
        .flash-error { background: #FEE2E2; color: #B91C1C; border: 1px solid #FECACA; }

        .form-card { background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 14px; padding: 32px; box-shadow: 0 1px 3px rgba(0,0,0,0.02); }
        .form-group { margin-bottom: 22px; }
        .form-label { display: block; font-size: 14px; font-weight: 700; color: #1E293B; margin-bottom: 8px; }
        .form-input, .form-select, .form-textarea { width: 100%; padding: 12px 16px; border: 1px solid #CBD5E1; border-radius: 8px; font-size: 14px; color: #0F172A; outline: none; transition: 0.2s; }
        .form-input:focus, .form-select:focus, .form-textarea:focus { border-color: #2563EB; box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1); }
        .form-textarea { min-height: 100px; resize: vertical; }
        
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .checkbox-label { display: flex; align-items: center; gap: 10px; font-size: 14px; font-weight: 600; color: #1E293B; cursor: pointer; }
        
        .form-actions { display: flex; gap: 12px; margin-top: 32px; padding-top: 20px; border-top: 1px solid #F1F5F9; }
        .btn-submit { background: #2563EB; color: #FFFFFF; border: none; padding: 12px 24px; border-radius: 8px; font-size: 14px; font-weight: 700; cursor: pointer; transition: 0.2s; }
        .btn-submit:hover { background: #1D4ED8; }
        .btn-cancel { background: #F1F5F9; color: #475569; text-decoration: none; padding: 12px 20px; border-radius: 8px; font-size: 14px; font-weight: 600; }
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
        </ul>

        <div class="sidebar-footer">
            <a href="<?= base_url('/') ?>" target="_blank"><span>🌐</span> Website Publik</a>
        </div>
    </aside>

    <main class="main-content">
        <div class="page-header">
            <a href="<?= base_url('/dashboard/mitra/produk') ?>" style="color:#64748B; text-decoration:none; font-size:13px; font-weight:600; display:inline-block; margin-bottom:8px;">← Kembali ke Kelola Produk</a>
            <h1 class="page-title"><?= $isEdit ? 'Edit Data Produk Kamera' : 'Tambah Kamera / Lensa Baru' ?></h1>
        </div>

        <?php if ($msg = flash('error')): ?>
            <div class="flash-alert flash-error"><?= $msg ?></div>
        <?php endif; ?>

        <div class="form-card">
            <form action="<?= $isEdit ? base_url('/dashboard/mitra/produk/' . $product['id'] . '/update') : base_url('/dashboard/mitra/produk/simpan') ?>" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>

                <div class="form-group">
                    <label class="form-label" for="name">Nama Produk Kamera / Lensa</label>
                    <input type="text" id="name" name="name" class="form-input" placeholder="Contoh: Sony Alpha A7 III Kit 28-70mm" value="<?= htmlspecialchars($product['name'] ?? old('name')) ?>" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="category_id">Kategori Produk</label>
                        <select id="category_id" name="category_id" class="form-select" required>
                            <option value="">-- Pilih Kategori --</option>
                            <?php foreach ($categories as $cat): 
                                $catId = is_object($cat) ? $cat->id : $cat['id'];
                                $catName = is_object($cat) ? $cat->name : $cat['name'];
                            ?>
                                <option value="<?= (int)$catId ?>" <?= (isset($product['category_id']) && $product['category_id'] == $catId) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($catName) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="condition_note">Kondisi Barang</label>
                        <input type="text" id="condition_note" name="condition_note" class="form-input" placeholder="Contoh: Kondisi 98% mulus · Sensor bersih" value="<?= htmlspecialchars($product['condition_note'] ?? 'Kondisi sangat baik · Siap disewa') ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="checkbox-label" style="margin-top:10px;">
                            <input type="checkbox" name="is_rental" value="1" <?= (!isset($product) || !empty($product['is_rental'])) ? 'checked' : '' ?>>
                            Aktifkan Opsi Disewakan (Rental)
                        </label>
                        <label class="form-label" for="daily_price" style="margin-top:14px;">Tarif Sewa Harian (Rp/hari)</label>
                        <input type="number" id="daily_price" name="daily_price" class="form-input" placeholder="250000" value="<?= htmlspecialchars($product['daily_price'] ?? old('daily_price', '250000')) ?>">
                    </div>

                    <div class="form-group">
                        <label class="checkbox-label" style="margin-top:10px;">
                            <input type="checkbox" name="is_sale" value="1" <?= (!empty($product['is_sale'])) ? 'checked' : '' ?>>
                            Aktifkan Opsi Dijual (Marketplace)
                        </label>
                        <label class="form-label" for="sale_price" style="margin-top:14px;">Harga Jual Produk (Rp)</label>
                        <input type="number" id="sale_price" name="sale_price" class="form-input" placeholder="15000000" value="<?= htmlspecialchars($product['sale_price'] ?? old('sale_price', '0')) ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="description">Deskripsi & Kelengkapan Paket</label>
                    <textarea id="description" name="description" class="form-textarea" placeholder="Tuliskan spesifikasi, kelengkapan sewa (baterai, charger, strap, tas, memori), dan catatan untuk penyewa..."><?= htmlspecialchars($product['description'] ?? old('description')) ?></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label" for="product_image">Foto Utama Kamera</label>
                    <input type="file" id="product_image" name="product_image" class="form-input" accept="image/png, image/jpeg, image/webp">
                    <small style="color:#64748B; font-size:12px; margin-top:4px; display:block;">Format JPG, PNG, atau WEBP. Maksimal 3MB.</small>
                </div>

                <?php if (!$isEdit): ?>
                    <div class="form-group" style="background:#F8FAFC; border:1px dashed #CBD5E1; padding:16px; border-radius:10px;">
                        <label class="form-label" for="serial_number">Nomor Seri Unit Fisik Pertama (Opsional)</label>
                        <input type="text" id="serial_number" name="serial_number" class="form-input" placeholder="Contoh: SN-SONY-A7M3-001">
                        <small style="color:#64748B; font-size:12px; margin-top:4px; display:block;">Nomor seri fisik kamera untuk pelacakan unit saat disewa penyewa.</small>
                    </div>
                <?php endif; ?>

                <div class="form-actions">
                    <button type="submit" class="btn-submit">
                        <?= $isEdit ? 'Simpan Perubahan' : 'Terbitkan Produk Sekarang' ?>
                    </button>
                    <a href="<?= base_url('/dashboard/mitra/produk') ?>" class="btn-cancel">Batal</a>
                </div>
            </form>
        </div>
    </main>

</body>
</html>

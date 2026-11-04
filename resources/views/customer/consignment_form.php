<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajukan Titip Jual Kamera — CamHub</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; font-family:'Plus Jakarta Sans', sans-serif; }
        body { background:#f8fafc; color:#0f172a; min-height:100vh; }
        .topbar { background:#0c192c; padding:18px 48px; display:flex; justify-content:space-between; align-items:center; }
        .topbar a.brand { font-size:22px; font-weight:800; color:#fff; text-decoration:none; }
        .topbar .nav-links a { color:#94a3b8; text-decoration:none; font-size:14px; font-weight:600; transition:color 0.2s; }
        .topbar .nav-links a:hover { color:#fff; }
        
        .container { max-width:800px; margin:40px auto; padding:0 24px 60px; }
        .form-card { background:#ffffff; border:1px solid #e2e8f0; border-radius:16px; padding:36px; box-shadow:0 1px 3px rgba(0,0,0,0.03); }
        .header-title { font-size:26px; font-weight:800; color:#0f172a; margin-bottom:8px; }
        .header-subtitle { color:#64748b; font-size:14px; line-height:1.6; margin-bottom:28px; }
        
        .alert-error { background:#fee2e2; border:1px solid #fca5a5; color:#991b1b; padding:14px 18px; border-radius:10px; font-size:14px; margin-bottom:24px; }
        .alert-error ul { margin-left:20px; margin-top:6px; }
        
        .form-group { margin-bottom:22px; }
        .form-label { display:block; font-size:14px; font-weight:700; color:#1e293b; margin-bottom:8px; }
        .form-control { width:100%; padding:12px 16px; border:1px solid #cbd5e1; border-radius:10px; font-size:14px; color:#0f172a; transition:border 0.2s; }
        .form-control:focus { outline:none; border-color:#2563eb; box-shadow:0 0 0 3px rgba(37,99,235,0.1); }
        .form-hint { font-size:12px; color:#64748b; margin-top:6px; }

        .info-box { background:#eff6ff; border:1px solid #bfdbfe; border-radius:12px; padding:18px; margin-bottom:28px; font-size:13px; color:#1e40af; line-height:1.6; }
        .info-box strong { color:#1e3a8a; }

        .btn-submit { width:100%; background:#2563eb; color:#ffffff; font-weight:700; font-size:15px; padding:14px; border:none; border-radius:10px; cursor:pointer; transition:background 0.2s; }
        .btn-submit:hover { background:#1d4ed8; }
        .btn-back { display:inline-block; text-align:center; width:100%; color:#64748b; font-size:14px; font-weight:600; text-decoration:none; margin-top:14px; }
        .btn-back:hover { color:#0f172a; }
    </style>
</head>
<body>

    <header class="topbar">
        <a href="<?= base_url() ?>" class="brand">CamHub</a>
        <div class="nav-links" style="display:flex; align-items:center; gap:16px;">
            <a href="<?= base_url('/dashboard/pelanggan') ?>">← Kembali ke Dashboard</a>
            <form action="<?= base_url('/logout') ?>" method="POST" style="margin:0;">
                <?= csrf_field() ?>
                <button type="submit" style="background:#dc2626; color:#ffffff; font-weight:700; border-radius:8px; padding:6px 12px; border:none; cursor:pointer; font-size:12px;">
                    🚪 Keluar
                </button>
            </form>
        </div>
    </header>

    <main class="container">
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
                <a href="<?= base_url('/dashboard/pelanggan') ?>" class="btn-back">Batal & Kembali ke Dashboard</a>
            </form>
        </div>
    </main>

</body>
</html>


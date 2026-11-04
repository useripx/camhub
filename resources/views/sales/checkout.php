<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout Pembelian — <?= htmlspecialchars($product['name'] ?? 'Kamera') ?> — CamHub</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; font-family:'Plus Jakarta Sans', sans-serif; }
        body { background:#F8FAFC; color:#0F172A; min-height:100vh; padding-bottom:60px; }

        /* HEADER */
        .checkout-header {
            background:#FFFFFF;
            padding:20px 48px;
            border-bottom:1px solid #E2E8F0;
            display:flex;
            justify-content:space-between;
            align-items:center;
        }
        .brand-logo { font-size:24px; font-weight:800; color:#0F172A; text-decoration:none; }
        .back-link {
            text-decoration:none;
            color:#64748B;
            font-size:14px;
            font-weight:600;
            display:flex;
            align-items:center;
            gap:6px;
            transition:0.2s;
        }
        .back-link:hover { color:#2563EB; }

        /* CONTAINER 2 KOLOM */
        .checkout-container {
            max-width:1120px;
            margin:40px auto;
            padding:0 24px;
        }
        .page-title-row {
            margin-bottom:32px;
        }
        .checkout-main-title {
            font-size:32px;
            font-weight:800;
            color:#0F172A;
            margin-bottom:6px;
            letter-spacing:-0.5px;
        }
        .checkout-subtitle {
            font-size:15px;
            color:#64748B;
        }

        .checkout-layout {
            display:grid;
            grid-template-columns:1.4fr 1fr;
            gap:36px;
            align-items:start;
        }

        /* CARD KIRI: INFORMASI PENGIRIMAN */
        .form-card {
            background:#FFFFFF;
            border:1px solid #E2E8F0;
            border-radius:18px;
            padding:36px;
            box-shadow:0 1px 3px rgba(0,0,0,0.02);
        }
        .form-section-title {
            font-size:20px;
            font-weight:800;
            color:#0F172A;
            margin-bottom:24px;
        }
        .form-group {
            margin-bottom:20px;
        }
        .form-label {
            display:block;
            font-size:14px;
            font-weight:600;
            color:#334155;
            margin-bottom:8px;
        }
        .form-input, .form-textarea {
            width:100%;
            padding:12px 16px;
            border:1px solid #CBD5E1;
            border-radius:10px;
            font-size:14px;
            color:#0F172A;
            background:#FFFFFF;
            transition:0.2s;
        }
        .form-input:focus, .form-textarea:focus {
            outline:none;
            border-color:#2563EB;
            box-shadow:0 0 0 3px rgba(37,99,235,0.12);
        }
        .form-textarea {
            resize:vertical;
            min-height:85px;
        }
        .form-error {
            color:#EF4444;
            font-size:12px;
            margin-top:6px;
            font-weight:600;
        }

        /* METODE PEMBAYARAN PERSIS PROTOTYPE */
        .payment-methods {
            display:flex;
            flex-direction:column;
            gap:12px;
            margin-top:10px;
        }
        .payment-radio-label {
            display:flex;
            align-items:center;
            justify-content:space-between;
            padding:16px 20px;
            border:1px solid #E2E8F0;
            border-radius:12px;
            cursor:pointer;
            transition:all 0.15s ease;
            background:#FFFFFF;
        }
        .payment-radio-label:hover {
            border-color:#94A3B8;
        }
        .payment-radio-label.selected {
            border-color:#2563EB;
            background:#EFF6FF;
        }
        .payment-radio-left {
            display:flex;
            align-items:center;
            gap:14px;
            font-size:14px;
            font-weight:600;
            color:#0F172A;
        }
        .payment-radio-label input[type="radio"] {
            width:18px;
            height:18px;
            accent-color:#2563EB;
            cursor:pointer;
        }
        .badge-secure {
            background:#DCFCE7;
            color:#166534;
            font-size:11px;
            font-weight:700;
            padding:3px 8px;
            border-radius:6px;
        }

        /* CARD KANAN STICKY: RINGKASAN PESANAN */
        .summary-card {
            background:#FFFFFF;
            border:1px solid #E2E8F0;
            border-radius:18px;
            padding:36px;
            box-shadow:0 1px 3px rgba(0,0,0,0.02);
            position:sticky;
            top:32px;
        }
        .summary-title {
            font-size:20px;
            font-weight:800;
            color:#0F172A;
            margin-bottom:20px;
        }
        .summary-product-item {
            display:flex;
            gap:16px;
            align-items:center;
            padding-bottom:20px;
            border-bottom:1px solid #F1F5F9;
            margin-bottom:20px;
        }
        .summary-product-thumb {
            width:64px;
            height:64px;
            background:#F8FAFC;
            border:1px solid #E2E8F0;
            border-radius:10px;
            display:flex;
            align-items:center;
            justify-content:center;
            font-size:28px;
            overflow:hidden;
            flex-shrink:0;
        }
        .summary-product-thumb img {
            width:100%;
            height:100%;
            object-fit:cover;
        }
        .summary-product-name {
            font-size:15px;
            font-weight:700;
            color:#0F172A;
            margin-bottom:4px;
        }
        .summary-product-store {
            font-size:13px;
            color:#64748B;
        }

        .summary-row {
            display:flex;
            justify-content:space-between;
            align-items:center;
            font-size:14px;
            margin-bottom:12px;
            color:#64748B;
        }
        .summary-label { font-weight:500; }
        .summary-val { font-weight:700; color:#0F172A; }
        .summary-divider {
            height:1px;
            background:#E2E8F0;
            margin:18px 0;
        }

        .summary-total-label {
            font-size:14px;
            font-weight:700;
            color:#0F172A;
            margin-bottom:4px;
        }
        .summary-total-val {
            font-size:32px;
            font-weight:800;
            color:#2563EB;
            margin-bottom:24px;
            letter-spacing:-0.5px;
        }

        .btn-checkout-submit {
            width:100%;
            background:#2563EB;
            color:#FFFFFF;
            border:none;
            padding:16px;
            border-radius:12px;
            font-size:16px;
            font-weight:800;
            cursor:pointer;
            transition:all 0.2s ease;
            box-shadow:0 4px 12px rgba(37,99,235,0.25);
        }
        .btn-checkout-submit:hover {
            background:#1D4ED8;
            transform:translateY(-1px);
            box-shadow:0 6px 16px rgba(37,99,235,0.35);
        }

        .escrow-pill-note {
            background:#F8FAFC;
            border:1px solid #E2E8F0;
            border-radius:12px;
            padding:14px 16px;
            margin-top:20px;
            display:flex;
            align-items:center;
            gap:12px;
            font-size:12px;
            color:#475569;
            line-height:1.5;
        }

        @media (max-width: 900px) {
            .checkout-layout { grid-template-columns:1fr; }
            .checkout-header { padding:16px 20px; }
            .summary-card { position:static; }
        }
    </style>
</head>
<body>

    <header class="checkout-header">
        <a href="<?= base_url() ?>" class="brand-logo">CamHub</a>
        <a href="<?= base_url('/produk/' . htmlspecialchars($product['slug'])) ?>" class="back-link">
            ← Kembali ke Detail Produk
        </a>
    </header>

    <main class="checkout-container">
        <div class="page-title-row">
            <h1 class="checkout-main-title">Checkout Pembelian Kamera</h1>
            <p class="checkout-subtitle">Pastikan alamat pengiriman dan detail barang yang Anda beli sudah benar.</p>
        </div>

        <form action="<?= base_url('/beli/' . htmlspecialchars($product['slug']) . '/proses') ?>" method="POST">
            <?= csrf_field() ?>

            <div class="checkout-layout">
                <!-- KOLOM KIRI: INFORMASI PENGIRIMAN -->
                <section class="form-card">
                    <h2 class="form-section-title">Informasi Pengiriman Barang</h2>

                    <div class="form-group">
                        <label class="form-label" for="recipient_name">Nama Penerima</label>
                        <input type="text" id="recipient_name" name="recipient_name" class="form-input" 
                               value="<?= htmlspecialchars($old['recipient_name'] ?? $prefill['recipient_name'] ?? '') ?>" required>
                        <?php if (isset($errors['recipient_name'])): ?>
                            <div class="form-error"><?= htmlspecialchars($errors['recipient_name']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="phone">Nomor HP / WhatsApp Penerima</label>
                        <input type="tel" id="phone" name="phone" class="form-input" 
                               value="<?= htmlspecialchars($old['phone'] ?? $prefill['phone'] ?? '') ?>" required>
                        <?php if (isset($errors['phone'])): ?>
                            <div class="form-error"><?= htmlspecialchars($errors['phone']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="shipping_address">Alamat Lengkap Pengiriman</label>
                        <textarea id="shipping_address" name="shipping_address" class="form-textarea" 
                                  placeholder="Nama jalan, nomor rumah, RT/RW, kelurahan, kecamatan" required><?= htmlspecialchars($old['shipping_address'] ?? $prefill['shipping_address'] ?? '') ?></textarea>
                        <?php if (isset($errors['shipping_address'])): ?>
                            <div class="form-error"><?= htmlspecialchars($errors['shipping_address']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="city">Kota / Kabupaten</label>
                        <input type="text" id="city" name="city" class="form-input" 
                               value="<?= htmlspecialchars($old['city'] ?? $prefill['city'] ?? 'Yogyakarta') ?>" required>
                        <?php if (isset($errors['city'])): ?>
                            <div class="form-error"><?= htmlspecialchars($errors['city']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="notes">Catatan Tambahan untuk Penjual (Opsional)</label>
                        <input type="text" id="notes" name="notes" class="form-input" 
                               placeholder="Contoh: Titipkan di pos satpam / packing kayu ekstra"
                               value="<?= htmlspecialchars($old['notes'] ?? '') ?>">
                    </div>

                    <div class="form-group" style="margin-top:28px;">
                        <label class="form-label">Metode Pembayaran</label>
                        <div class="payment-methods">
                            <label class="payment-radio-label selected">
                                <div class="payment-radio-left">
                                    <input type="radio" name="payment_method" value="escrow" checked onclick="selectPayment(this)">
                                    <span>Rekening Bersama (Escrow CamHub)</span>
                                </div>
                                <span class="badge-secure">🛡️ Paling Direkomendasikan</span>
                            </label>

                            <label class="payment-radio-label">
                                <div class="payment-radio-left">
                                    <input type="radio" name="payment_method" value="bank_transfer" onclick="selectPayment(this)">
                                    <span>Transfer Bank (Virtual Account)</span>
                                </div>
                            </label>

                            <label class="payment-radio-label">
                                <div class="payment-radio-left">
                                    <input type="radio" name="payment_method" value="ewallet" onclick="selectPayment(this)">
                                    <span>E-Wallet (GoPay / OVO / Dana)</span>
                                </div>
                            </label>
                        </div>
                    </div>
                </section>

                <!-- KOLOM KANAN: RINGKASAN PESANAN STICKY -->
                <aside class="summary-card">
                    <h2 class="summary-title">Ringkasan Pesanan</h2>

                    <div class="summary-product-item">
                        <div class="summary-product-thumb">
                            <?php if (!empty($product['primary_image'])): ?>
                                <img src="<?= base_url('/' . ltrim($product['primary_image'], '/')) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                            <?php else: ?>
                                📷
                            <?php endif; ?>
                        </div>
                        <div>
                            <div class="summary-product-name"><?= htmlspecialchars($product['name']) ?></div>
                            <div class="summary-product-store">Toko: <?= htmlspecialchars($product['store_name']) ?> (<?= htmlspecialchars($product['store_city']) ?>)</div>
                        </div>
                    </div>

                    <div class="summary-row">
                        <span class="summary-label">Harga Kamera</span>
                        <span class="summary-val"><?= $pricing['formatted_sale_price'] ?></span>
                    </div>

                    <div class="summary-row">
                        <span class="summary-label">Ongkos Kirim (Berasuransi)</span>
                        <span class="summary-val"><?= $pricing['formatted_shipping_fee'] ?></span>
                    </div>

                    <div class="summary-row">
                        <span class="summary-label">Biaya Layanan Rekber</span>
                        <span class="summary-val"><?= $pricing['formatted_admin_fee'] ?></span>
                    </div>

                    <div class="summary-divider"></div>

                    <div class="summary-total-label">Total Pembayaran</div>
                    <div class="summary-total-val"><?= $pricing['formatted_total'] ?></div>

                    <button type="submit" class="btn-checkout-submit">
                        Lanjutkan Pembayaran
                    </button>

                    <div class="escrow-pill-note">
                        <span style="font-size:20px;">🛡️</span>
                        <span>Dana Anda diamankan di Rekening Bersama Escrow CamHub dan baru diteruskan ke toko penjual setelah kamera diterima dan Anda periksa.</span>
                    </div>
                </aside>
            </div>
        </form>
    </main>

    <script>
        function selectPayment(radio) {
            document.querySelectorAll('.payment-radio-label').forEach(el => el.classList.remove('selected'));
            radio.closest('.payment-radio-label').classList.add('selected');
        }
    </script>
</body>
</html>

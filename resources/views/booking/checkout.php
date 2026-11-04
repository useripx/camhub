<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking & Checkout — CamHub</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; font-family:'Plus Jakarta Sans', sans-serif; }
        body { background:#F1F5F9; color:#0F172A; min-height:100vh; display:flex; flex-direction:column; }
        
        /* NAVBAR HEADER BERSIH (PERSIS MOCKUP CamHub — Booking & Checkout.png) */
        .navbar {
            background: #FFFFFF;
            padding: 20px 48px;
            border-bottom: 1px solid #E2E8F0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .brand-logo {
            font-size: 26px;
            font-weight: 800;
            color: #0F172A;
            text-decoration: none;
            letter-spacing: -0.5px;
        }
        .nav-back {
            color: #64748B;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: 0.2s;
        }
        .nav-back:hover {
            color: #2563EB;
        }

        /* CONTAINER UTAMA */
        .main-container {
            max-width: 1140px;
            width: 100%;
            margin: 40px auto;
            padding: 0 24px;
            flex-grow: 1;
        }
        .checkout-header {
            margin-bottom: 32px;
        }
        .page-title {
            font-size: 32px;
            font-weight: 800;
            color: #0F172A;
            letter-spacing: -0.5px;
            margin-bottom: 6px;
        }
        .page-subtitle {
            font-size: 15px;
            color: #64748B;
        }

        /* FLASH ALERT */
        .flash-alert {
            padding: 14px 20px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 24px;
        }
        .flash-error { background: #FEE2E2; color: #B91C1C; border: 1px solid #FECACA; }
        .flash-success { background: #DCFCE7; color: #15803D; border: 1px solid #BBF7D0; }

        /* GRID DUA KOLOM (PERSIS MOCKUP) */
        .checkout-grid {
            display: grid;
            grid-template-columns: 1fr 380px;
            gap: 32px;
            align-items: start;
        }

        /* KARTU KIRI: INFORMASI PENYEWA */
        .card-buyer {
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 16px;
            padding: 36px 40px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        }
        .card-title {
            font-size: 20px;
            font-weight: 800;
            color: #0F172A;
            margin-bottom: 24px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-label {
            display: block;
            font-size: 14px;
            font-weight: 700;
            color: #334155;
            margin-bottom: 8px;
        }
        .form-input {
            width: 100%;
            padding: 14px 18px;
            background: #FFFFFF;
            border: 1px solid #CBD5E1;
            border-radius: 10px;
            font-size: 14px;
            color: #0F172A;
            outline: none;
            transition: all 0.2s ease;
        }
        .form-input:focus {
            border-color: #2563EB;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }
        .form-input::placeholder {
            color: #94A3B8;
        }

        .date-grid {
            display: grid;
            grid-template-columns: 1fr 140px;
            gap: 16px;
        }

        /* METODE PEMBAYARAN (RADIO PILIHAN) */
        .payment-section-title {
            font-size: 16px;
            font-weight: 800;
            color: #0F172A;
            margin-top: 28px;
            margin-bottom: 16px;
        }
        .payment-options {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .payment-radio-label {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 18px;
            border: 1px solid #E2E8F0;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 14px;
            font-weight: 600;
            color: #1E293B;
        }
        .payment-radio-label:hover {
            border-color: #CBD5E1;
            background: #F8FAFC;
        }
        .payment-radio-label.selected {
            border-color: #2563EB;
            background: #EFF6FF;
            color: #1D4ED8;
        }
        .payment-radio-label input[type="radio"] {
            accent-color: #2563EB;
            width: 18px;
            height: 18px;
        }

        /* KARTU KANAN: RINGKASAN PESANAN (STICKY CARD PERSIS MOCKUP) */
        .card-summary {
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 16px;
            padding: 32px 28px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
            position: sticky;
            top: 32px;
        }
        .summary-title {
            font-size: 18px;
            font-weight: 800;
            color: #0F172A;
            margin-bottom: 24px;
        }
        .summary-item-header {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 15px;
            font-weight: 700;
            color: #0F172A;
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 1px solid #F1F5F9;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 14px;
            font-size: 14px;
        }
        .summary-label {
            color: #64748B;
        }
        .summary-val {
            font-weight: 600;
            color: #1E293B;
        }
        .summary-divider {
            border-top: 1px solid #E2E8F0;
            margin: 20px 0;
        }
        .summary-total-label {
            font-size: 14px;
            font-weight: 700;
            color: #0F172A;
            margin-bottom: 6px;
        }
        .summary-total-val {
            font-size: 32px;
            font-weight: 800;
            color: #2563EB; /* Biru tebal persis mockup */
            letter-spacing: -0.5px;
            margin-bottom: 24px;
        }
        .btn-checkout-submit {
            width: 100%;
            background: #2563EB;
            color: #FFFFFF;
            border: none;
            padding: 16px 20px;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
        }
        .btn-checkout-submit:hover {
            background: #1D4ED8;
            box-shadow: 0 6px 16px rgba(37, 99, 235, 0.3);
        }

        .escrow-pill-note {
            display: flex;
            align-items: center;
            gap: 8px;
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 8px;
            padding: 10px 12px;
            font-size: 12px;
            color: #64748B;
            margin-top: 16px;
            line-height: 1.4;
        }

        @media (max-width: 860px) {
            .checkout-grid { grid-template-columns: 1fr; }
            .navbar { padding: 16px 20px; }
            .main-container { margin: 20px auto; }
            .card-buyer { padding: 24px; }
        }
    </style>
</head>
<body>

    <!-- NAVBAR HEADER -->
    <header class="navbar">
        <a href="<?= base_url('/') ?>" class="brand-logo">CamHub</a>
        <a href="<?= base_url('/produk/' . $product['slug']) ?>" class="nav-back">
            <span>←</span> Kembali ke Detail Produk
        </a>
    </header>

    <!-- MAIN CONTAINER -->
    <main class="main-container">
        <div class="checkout-header">
            <h1 class="page-title">Booking & Checkout</h1>
            <p class="page-subtitle">Pastikan detail pesanan sudah benar.</p>
        </div>

        <?php if ($msg = flash('error')): ?>
            <div class="flash-alert flash-error"><?= $msg ?></div>
        <?php endif; ?>

        <form action="<?= base_url('/checkout/' . $product['slug'] . '/proses') ?>" method="POST" id="checkoutForm">
            <?= csrf_field() ?>

            <div class="checkout-grid">
                <!-- KARTU KIRI: INFORMASI PENYEWA (PERSIS MOCKUP) -->
                <section class="card-buyer">
                    <h2 class="card-title">Informasi Penyewa</h2>

                    <!-- Nama Lengkap -->
                    <div class="form-group">
                        <label class="form-label" for="full_name">Nama lengkap</label>
                        <input type="text" id="full_name" name="full_name" class="form-input" placeholder="Nama lengkap Anda" value="<?= htmlspecialchars($user['full_name'] ?? 'Mohammad Regieyanto') ?>" required>
                    </div>

                    <!-- Nomor HP -->
                    <div class="form-group">
                        <label class="form-label" for="phone">Nomor HP</label>
                        <input type="text" id="phone" name="phone" class="form-input" placeholder="08xxxxxxxxxx" value="<?= htmlspecialchars($user['phone'] ?? '081234567890') ?>" required>
                    </div>

                    <!-- Alamat Pengambilan -->
                    <div class="form-group">
                        <label class="form-label" for="pickup_address">Alamat pengambilan</label>
                        <input type="text" id="pickup_address" name="pickup_address" class="form-input" placeholder="Alamat kamu" value="<?= htmlspecialchars($profile['address'] ?? 'Jl. Kaliurang KM 5, Sleman, Yogyakarta') ?>" required>
                    </div>

                    <!-- Tanggal Sewa & Durasi -->
                    <div class="form-group">
                        <label class="form-label">Tanggal sewa</label>
                        <div class="date-grid">
                            <input type="date" id="start_date" name="start_date" class="form-input" value="<?= htmlspecialchars($startDate) ?>" min="<?= date('Y-m-d') ?>" required>
                            <select id="duration_days" name="duration_days" class="form-input" style="cursor:pointer;" required>
                                <option value="1" <?= $durationDays == 1 ? 'selected' : '' ?>>1 Hari</option>
                                <option value="3" <?= $durationDays == 3 ? 'selected' : '' ?>>3 Hari (Diskon 5%)</option>
                                <option value="7" <?= $durationDays == 7 ? 'selected' : '' ?>>7 Hari (Diskon 10%)</option>
                                <option value="14" <?= $durationDays == 14 ? 'selected' : '' ?>>14 Hari (Diskon 15%)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Metode Pembayaran -->
                    <div class="payment-section-title">Metode pembayaran</div>
                    <div class="payment-options">
                        <label class="payment-radio-label selected">
                            <input type="radio" name="payment_method" value="escrow" checked onclick="selectPayment(this)">
                            <span>Rekening Bersama (Escrow)</span>
                        </label>
                        <label class="payment-radio-label">
                            <input type="radio" name="payment_method" value="bank_transfer" onclick="selectPayment(this)">
                            <span>Transfer Bank</span>
                        </label>
                        <label class="payment-radio-label">
                            <input type="radio" name="payment_method" value="ewallet" onclick="selectPayment(this)">
                            <span>E-Wallet</span>
                        </label>
                    </div>
                </section>

                <!-- KARTU KANAN: RINGKASAN PESANAN (PERSIS MOCKUP) -->
                <aside class="card-summary">
                    <h2 class="summary-title">Ringkasan Pesanan</h2>

                    <div class="summary-item-header">
                        <span>📷</span>
                        <span id="summaryItemText"><?= htmlspecialchars($product['name']) ?> · <?= (int)$durationDays ?> hari</span>
                    </div>

                    <div class="summary-row">
                        <span class="summary-label">Subtotal</span>
                        <span class="summary-val" id="summarySubtotal">Rp <?= number_format($calculation['subtotal'], 0, ',', '.') ?></span>
                    </div>

                    <?php if ($calculation['discount_amount'] > 0): ?>
                        <div class="summary-row" id="rowDiscount" style="color:#16A34A;">
                            <span>Diskon Durasi</span>
                            <span id="summaryDiscount">-Rp <?= number_format($calculation['discount_amount'], 0, ',', '.') ?></span>
                        </div>
                    <?php endif; ?>

                    <div class="summary-row">
                        <span class="summary-label">Biaya admin</span>
                        <span class="summary-val">Rp 5.000</span>
                    </div>

                    <div class="summary-divider"></div>

                    <div class="summary-total-label">Total pembayaran</div>
                    <div class="summary-total-val" id="summaryTotal">Rp <?= number_format($calculation['total_price'] ?? ($calculation['subtotal'] + 5000), 0, ',', '.') ?></div>

                    <button type="submit" class="btn-checkout-submit" id="btnSubmit">
                        Lanjutkan Pembayaran
                    </button>

                    <div class="escrow-pill-note">
                        <span>🛡️</span>
                        <span>Dana Anda ditahan aman di Rekening Bersama Escrow CamHub hingga kamera selesai disewa & dicek.</span>
                    </div>
                </aside>
            </div>
        </form>
    </main>

    <script>
        const dailyPrice = <?= (float)$dailyPrice ?>;
        const productName = <?= json_encode($product['name']) ?>;

        function selectPayment(radio) {
            document.querySelectorAll('.payment-radio-label').forEach(el => el.classList.remove('selected'));
            radio.closest('.payment-radio-label').classList.add('selected');
        }

        // Live calculation upon changing duration or start date
        const durationSelect = document.getElementById('duration_days');
        durationSelect.addEventListener('change', function() {
            const days = parseInt(this.value, 10);
            let discountPercent = 0;
            if (days >= 14) discountPercent = 15;
            else if (days >= 7) discountPercent = 10;
            else if (days >= 3) discountPercent = 5;

            const gross = dailyPrice * days;
            const discountAmount = Math.round(gross * (discountPercent / 100));
            const subtotal = gross - discountAmount;
            const adminFee = 5000;
            const total = subtotal + adminFee;

            document.getElementById('summaryItemText').textContent = productName + ' · ' + days + ' hari';
            document.getElementById('summarySubtotal').textContent = 'Rp ' + subtotal.toLocaleString('id-ID');
            document.getElementById('summaryTotal').textContent = 'Rp ' + total.toLocaleString('id-ID');
        });
    </script>
</body>
</html>

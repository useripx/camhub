<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemesanan Berhasil — CamHub</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; font-family:'Plus Jakarta Sans', sans-serif; }
        body { background:#F8FAFC; color:#0F172A; min-height:100vh; display:flex; flex-direction:column; }
        .navbar { background:#FFFFFF; padding:20px 48px; border-bottom:1px solid #E2E8F0; display:flex; justify-content:space-between; align-items:center; }
        .brand-logo { font-size:24px; font-weight:800; color:#0F172A; text-decoration:none; }
        .nav-link { color:#64748B; font-size:14px; text-decoration:none; font-weight:600; }
        .nav-link:hover { color:#2563EB; }
        
        .main-container { max-width:720px; width:100%; margin:40px auto; padding:0 20px; flex-grow:1; }
        .receipt-card { background:#FFFFFF; border:1px solid #E2E8F0; border-radius:20px; padding:40px; box-shadow:0 4px 16px rgba(0,0,0,0.03); }
        .success-icon-box { width:64px; height:64px; background:#DCFCE7; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 20px; font-size:30px; color:#16A34A; }
        .receipt-title { font-size:26px; font-weight:800; text-align:center; color:#0F172A; margin-bottom:8px; }
        .receipt-subtitle { font-size:14px; text-align:center; color:#64748B; margin-bottom:32px; }
        
        .booking-code-badge { background:#EFF6FF; border:1px solid #BFDBFE; border-radius:12px; padding:16px; text-align:center; margin-bottom:28px; }
        .code-label { font-size:12px; font-weight:700; color:#1D4ED8; text-transform:uppercase; letter-spacing:0.5px; }
        .code-value { font-size:28px; font-weight:800; color:#1E40AF; margin-top:4px; font-family:monospace; }
        
        .section-title { font-size:15px; font-weight:700; color:#334155; margin-bottom:14px; text-transform:uppercase; letter-spacing:0.5px; }
        .info-table { width:100%; border-collapse:collapse; margin-bottom:28px; }
        .info-table td { padding:10px 0; font-size:14px; border-bottom:1px solid #F1F5F9; }
        .info-table td.label { color:#64748B; width:45%; }
        .info-table td.val { font-weight:700; color:#0F172A; text-align:right; }
        
        .escrow-box { background:#F8FAFC; border:1px dashed #CBD5E1; border-radius:12px; padding:18px; margin-bottom:32px; }
        .escrow-box strong { color:#0F172A; display:block; font-size:14px; margin-bottom:6px; }
        .escrow-box p { color:#64748B; font-size:13px; line-height:1.5; margin:0; }
        
        .actions-row { display:flex; gap:12px; }
        .btn { flex:1; text-align:center; padding:14px; border-radius:10px; font-size:14px; font-weight:700; text-decoration:none; transition:0.2s; }
        .btn-primary { background:#2563EB; color:#FFFFFF; }
        .btn-primary:hover { background:#1D4ED8; }
        .btn-outline { background:#FFFFFF; border:1px solid #CBD5E1; color:#334155; }
        .btn-outline:hover { background:#F1F5F9; }
    </style>
</head>
<body>

    <header class="navbar">
        <a href="<?= base_url('/') ?>" class="brand-logo">CamHub</a>
        <a href="<?= base_url('/pesanan-saya') ?>" class="nav-link">Lihat Riwayat Sewa →</a>
    </header>

    <main class="main-container">
        <div class="receipt-card">
            <div class="success-icon-box">✓</div>
            <h1 class="receipt-title">Pemesanan Sewa Berhasil Dibuat!</h1>
            <p class="receipt-subtitle">Unit kamera telah dialokasikan untuk Anda dengan sistem rekening bersama Escrow CamHub.</p>

            <div class="booking-code-badge">
                <div class="code-label">Kode Booking Resmi</div>
                <div class="code-value">#RENT-<?= str_pad((string)$booking['id'], 5, '0', STR_PAD_LEFT) ?></div>
            </div>

            <div class="section-title">Detail Unit & Durasi Sewa</div>
            <table class="info-table">
                <tr>
                    <td class="label">Kamera</td>
                    <td class="val"><?= htmlspecialchars($booking['product_name']) ?></td>
                </tr>
                <tr>
                    <td class="label">Nomor Seri Unit Fisik</td>
                    <td class="val"><span style="background:#F1F5F9; padding:2px 8px; border-radius:4px;"><?= htmlspecialchars($booking['serial_number']) ?></span></td>
                </tr>
                <tr>
                    <td class="label">Toko Penyedia (Mitra)</td>
                    <td class="val"><?= htmlspecialchars($booking['store_name']) ?> (<?= htmlspecialchars($booking['store_city']) ?>)</td>
                </tr>
                <tr>
                    <td class="label">Periode Sewa</td>
                    <td class="val"><?= date('d M Y', strtotime($booking['start_date'])) ?> s.d <?= date('d M Y', strtotime($booking['end_date'])) ?> (<?= (int)$booking['total_days'] ?> Hari)</td>
                </tr>
                <tr>
                    <td class="label">Metode Pembayaran</td>
                    <td class="val"><?= ucfirst($booking['payment_method'] ?? 'Escrow') ?> Rekening Bersama</td>
                </tr>
                <tr>
                    <td class="label" style="font-size:16px;">Total Pembayaran</td>
                    <td class="val" style="font-size:20px; color:#2563EB;">Rp <?= number_format($booking['total_amount'], 0, ',', '.') ?></td>
                </tr>
            </table>

            <div class="escrow-box">
                <strong>🛡️ Garansi Perlindungan CamHub Escrow</strong>
                <p>Uang sewa Anda aman ditahan di rekening penampung resmi CamHub dan <strong>hanya akan diteruskan ke Toko Mitra</strong> setelah Anda menerima kamera, menggunakan masa sewa, dan mengembalikan kamera dalam keadaan aman.</p>
            </div>

            <div class="actions-row">
                <a href="<?= base_url('/pesanan-saya') ?>" class="btn btn-primary">Lihat Pesanan Saya</a>
                <a href="<?= base_url('/katalog') ?>" class="btn btn-outline">Lanjut Sewa Lainnya</a>
            </div>
        </div>
    </main>

</body>
</html>


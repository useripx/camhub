<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembelian Berhasil — CamHub</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; font-family:'Plus Jakarta Sans', sans-serif; }
        body { background:#F8FAFC; color:#0F172A; min-height:100vh; padding-bottom:60px; }
        
        .header { background:#FFFFFF; padding:20px 48px; border-bottom:1px solid #E2E8F0; display:flex; justify-content:space-between; align-items:center; }
        .logo { font-size:24px; font-weight:800; color:#0F172A; text-decoration:none; }
        .nav-link { color:#2563EB; text-decoration:none; font-size:14px; font-weight:700; }
        
        .container { max-width:680px; margin:48px auto; padding:0 20px; }
        .success-card { background:#FFFFFF; border:1px solid #E2E8F0; border-radius:20px; padding:44px 36px; text-align:center; box-shadow:0 4px 20px rgba(0,0,0,0.03); }
        
        .check-icon { width:72px; height:72px; background:#DCFCE7; color:#15803D; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:32px; margin:0 auto 20px auto; }
        .title { font-size:26px; font-weight:800; color:#0F172A; margin-bottom:8px; }
        .subtitle { font-size:14px; color:#64748B; margin-bottom:32px; line-height:1.6; }
        
        .order-code-box { background:#F1F5F9; border:1px dashed #CBD5E1; border-radius:12px; padding:18px; margin-bottom:32px; }
        .order-code-label { font-size:12px; font-weight:700; color:#64748B; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:4px; }
        .order-code { font-size:28px; font-weight:800; color:#2563EB; letter-spacing:1px; }

        .details-table { width:100%; border-collapse:collapse; margin-bottom:32px; text-align:left; font-size:14px; }
        .details-table tr { border-bottom:1px solid #F1F5F9; }
        .details-table td { padding:14px 6px; }
        .details-table td:first-child { color:#64748B; font-weight:500; width:45%; }
        .details-table td:last-child { color:#0F172A; font-weight:700; text-align:right; }

        .escrow-banner { background:#EFF6FF; border:1px solid #BFDBFE; border-radius:12px; padding:18px 20px; text-align:left; margin-bottom:36px; display:flex; gap:14px; }
        .escrow-banner h4 { font-size:14px; font-weight:800; color:#1E40AF; margin-bottom:4px; }
        .escrow-banner p { font-size:13px; color:#3B82F6; line-height:1.5; margin:0; }

        .btn-group { display:flex; gap:16px; justify-content:center; }
        .btn-primary { background:#2563EB; color:#fff; text-decoration:none; padding:14px 28px; border-radius:10px; font-weight:700; font-size:14px; transition:0.2s; display:inline-block; }
        .btn-primary:hover { background:#1D4ED8; }
        .btn-secondary { background:#F1F5F9; color:#334155; text-decoration:none; padding:14px 28px; border-radius:10px; font-weight:700; font-size:14px; transition:0.2s; display:inline-block; }
        .btn-secondary:hover { background:#E2E8F0; }
    </style>
</head>
<body>

    <header class="header">
        <a href="<?= base_url() ?>" class="logo">CamHub</a>
        <a href="<?= base_url('/pesanan-saya?type=sale') ?>" class="nav-link">Lihat Riwayat Pembelian →</a>
    </header>

    <main class="container">
        <div class="success-card">
            <div class="check-icon">✓</div>
            <h1 class="title">Pesanan Pembelian Berhasil Dibuat!</h1>
            <p class="subtitle">Kamera yang Anda beli telah dicatat dalam sistem dan pembayaran Anda telah diamankan di Rekening Bersama Escrow CamHub.</p>

            <div class="order-code-box">
                <div class="order-code-label">Nomor Invoice Resmi</div>
                <div class="order-code"><?= htmlspecialchars($order['order_number'] ?? '#BUY-00000') ?></div>
            </div>

            <table class="details-table">
                <tr>
                    <td>Barang yang Dibeli</td>
                    <td><?= htmlspecialchars($order['product_name'] ?? 'Kamera') ?></td>
                </tr>
                <tr>
                    <td>Toko Penjual (Mitra)</td>
                    <td><?= htmlspecialchars($order['store_name'] ?? '-') ?> (<?= htmlspecialchars($order['store_city'] ?? '-') ?>)</td>
                </tr>
                <tr>
                    <td>Alamat Tujuan Kirim</td>
                    <td><?= htmlspecialchars($order['shipping_address'] ?? '-') ?></td>
                </tr>
                <tr>
                    <td>Metode Pembayaran</td>
                    <td><?= strtoupper(htmlspecialchars($order['payment_method'] ?? 'ESCROW')) ?> (Rekber)</td>
                </tr>
                <tr>
                    <td>Total Pembayaran</td>
                    <td style="color:#2563EB; font-size:18px;"><?= 'Rp ' . number_format((float)($order['total_amount'] ?? 0), 0, ',', '.') ?></td>
                </tr>
            </table>

            <div class="escrow-banner">
                <div style="font-size:24px;">🛡️</div>
                <div>
                    <h4>Garansi Perlindungan CamHub Escrow</h4>
                    <p>Uang pembayaran Anda ditahan aman di rekening bersama CamHub dan baru akan diteruskan ke penjual setelah paket sampai di tangan Anda dan Anda mengonfirmasi bahwa kondisi kamera sesuai deskripsi.</p>
                </div>
            </div>

            <div class="btn-group">
                <a href="<?= base_url('/pesanan-saya?type=sale') ?>" class="btn-primary">Lihat Jual Beli Saya</a>
                <a href="<?= base_url('/katalog') ?>" class="btn-secondary">Lanjut Belanja</a>
            </div>
        </div>
    </main>

</body>
</html>

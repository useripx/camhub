<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Pembelian Kamera #BUY-<?= str_pad((string)$order['id'], 5, '0', STR_PAD_LEFT) ?> — CamHub</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; font-family:'Plus Jakarta Sans', sans-serif; }
        body { background:#f1f5f9; color:#0f172a; padding:40px 20px; }
        .receipt-container { max-width:760px; margin:0 auto; background:#ffffff; border-radius:14px; padding:48px; border:1px solid #e2e8f0; box-shadow:0 4px 6px -1px rgba(0,0,0,0.05); }
        
        .receipt-header { display:flex; justify-content:space-between; align-items:flex-start; border-bottom:2px solid #0f172a; padding-bottom:24px; margin-bottom:28px; }
        .brand { font-size:28px; font-weight:800; color:#0f172a; letter-spacing:-0.5px; }
        .brand-sub { font-size:13px; color:#64748b; font-weight:500; margin-top:2px; }
        .receipt-title-box { text-align:right; }
        .receipt-title { font-size:20px; font-weight:800; color:#7c3aed; letter-spacing:0.5px; }
        .receipt-number { font-size:16px; font-weight:700; color:#0f172a; margin-top:4px; }
        .receipt-date { font-size:12px; color:#64748b; margin-top:2px; }

        .meta-grid { display:grid; grid-template-columns:1fr 1fr; gap:28px; margin-bottom:32px; background:#f8fafc; border-radius:10px; padding:20px; border:1px solid #e2e8f0; }
        .meta-col h3 { font-size:12px; font-weight:800; color:#64748b; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:8px; }
        .meta-name { font-size:15px; font-weight:700; color:#0f172a; }
        .meta-detail { font-size:13px; color:#475569; margin-top:3px; line-height:1.4; }

        .item-table { width:100%; border-collapse:collapse; margin-bottom:28px; }
        .item-table th { background:#f8fafc; padding:12px 14px; text-align:left; font-size:12px; font-weight:800; color:#475569; border-bottom:1px solid #cbd5e1; text-transform:uppercase; }
        .item-table td { padding:14px; font-size:14px; border-bottom:1px solid #f1f5f9; vertical-align:top; }

        .pricing-box { display:flex; justify-content:flex-end; margin-bottom:32px; }
        .pricing-table { width:320px; }
        .pricing-row { display:flex; justify-content:space-between; padding:8px 0; font-size:14px; color:#475569; }
        .pricing-total { display:flex; justify-content:space-between; padding:12px 0; font-size:18px; font-weight:800; color:#0f172a; border-top:2px solid #0f172a; margin-top:6px; }

        .escrow-guarantee { background:#f5f3ff; border:1px solid #ddd6fe; border-radius:10px; padding:16px 20px; display:flex; align-items:center; gap:14px; margin-bottom:32px; }
        .escrow-icon { font-size:24px; color:#7c3aed; }
        .escrow-text { font-size:12px; color:#5b21b6; line-height:1.4; }

        .receipt-footer { text-align:center; padding-top:24px; border-top:1px dashed #cbd5e1; color:#94a3b8; font-size:12px; }

        .action-bar { max-width:760px; margin:0 auto 20px auto; display:flex; justify-content:space-between; align-items:center; }
        .btn-print { background:#7c3aed; color:#ffffff; padding:10px 20px; border-radius:8px; font-weight:700; font-size:14px; border:none; cursor:pointer; display:inline-flex; align-items:center; gap:8px; transition:0.2s; }
        .btn-print:hover { background:#6d28d9; }
        .btn-back { color:#64748b; text-decoration:none; font-size:14px; font-weight:600; }
        .btn-back:hover { color:#0f172a; }

        @media print {
            body { background:#ffffff; padding:0; }
            .action-bar { display:none !important; }
            .receipt-container { border:none; box-shadow:none; padding:0; width:100%; max-width:100%; }
        }
    </style>
</head>
<body>

    <div class="action-bar no-print">
        <a href="javascript:window.history.back();" class="btn-back">← Kembali</a>
        <button onclick="window.print()" class="btn-print">
            <span>🖨</span> Cetak Invoice / Simpan PDF
        </button>
    </div>

    <div class="receipt-container">
        <header class="receipt-header">
            <div>
                <div class="brand">CamHub</div>
                <div class="brand-sub">Platform Marketplace & Rental Kamera Terintegrasi</div>
            </div>
            <div class="receipt-title-box">
                <div class="receipt-title">INVOICE PEMBELIAN KAMERA</div>
                <div class="receipt-number">#BUY-<?= str_pad((string)$order['id'], 5, '0', STR_PAD_LEFT) ?></div>
                <div class="receipt-date"><?= date('d F Y, H:i', strtotime($order['created_at'])) ?> WIB</div>
            </div>
        </header>

        <section class="meta-grid">
            <div class="meta-col">
                <h3>Tujuan Pengiriman</h3>
                <div class="meta-name"><?= htmlspecialchars($order['recipient_name'] ?? 'Pembeli') ?></div>
                <div class="meta-detail">Kontak: <?= htmlspecialchars($order['recipient_phone'] ?? '-') ?></div>
                <div class="meta-detail">Alamat: <?= htmlspecialchars($order['shipping_address'] ?? 'Alamat Terdaftar') ?>, <?= htmlspecialchars($order['city'] ?? '') ?></div>
            </div>
            <div class="meta-col">
                <h3>Toko Mitra Penjual</h3>
                <div class="meta-name"><?= htmlspecialchars($order['store_name'] ?? 'Mitra CamHub') ?></div>
                <div class="meta-detail">Lokasi Asal: <?= htmlspecialchars($order['store_city'] ?? 'Yogyakarta') ?></div>
                <div class="meta-detail">Metode Bayar: Rekening Bersama Escrow</div>
            </div>
        </section>

        <table class="item-table">
            <thead>
                <tr>
                    <th>Deskripsi Produk Kamera</th>
                    <th>Jumlah</th>
                    <th style="text-align:right;">Harga Satuan</th>
                    <th style="text-align:right;">Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <strong style="color:#0f172a; display:block;"><?= htmlspecialchars($order['product_name'] ?? 'Kamera Marketplace') ?></strong>
                        <span style="font-size:12px; color:#64748b;">
                            Kategori: <?= htmlspecialchars($order['category_name'] ?? 'Kamera') ?> · Kondisi: <?= htmlspecialchars($order['condition_note'] ?? 'Sangat Baik') ?>
                        </span>
                    </td>
                    <td>1 Unit</td>
                    <td style="text-align:right;">Rp <?= number_format((float)($order['item_price'] ?? ($order['total_amount'] - 30000)), 0, ',', '.') ?></td>
                    <td style="text-align:right;"><strong>Rp <?= number_format((float)($order['item_price'] ?? ($order['total_amount'] - 30000)), 0, ',', '.') ?></strong></td>
                </tr>
            </tbody>
        </table>

        <div class="pricing-box">
            <div class="pricing-table">
                <div class="pricing-row">
                    <span>Harga Kamera</span>
                    <span>Rp <?= number_format((float)($order['item_price'] ?? ($order['total_amount'] - 30000)), 0, ',', '.') ?></span>
                </div>
                <div class="pricing-row">
                    <span>Ongkos Kirim Kurir Ekspedisi</span>
                    <span>Rp 25.000</span>
                </div>
                <div class="pricing-row">
                    <span>Biaya Layanan Rekber Escrow</span>
                    <span>Rp 5.000</span>
                </div>
                <div class="pricing-total">
                    <span>Total Pembayaran</span>
                    <span style="color:#7c3aed;">Rp <?= number_format((float)$order['total_amount'], 0, ',', '.') ?></span>
                </div>
            </div>
        </div>

        <div class="escrow-guarantee">
            <div class="escrow-icon">🛡</div>
            <div class="escrow-text">
                <strong>Garansi Dana Aman (CamHub Escrow Protection):</strong><br>
                Uang Anda aman di rekening bersama CamHub. Dana hanya akan dicairkan ke toko penjual setelah Anda mengonfirmasi bahwa unit kamera telah sampai dan sesuai dengan deskripsi.
            </div>
        </div>

        <footer class="receipt-footer">
            Invoice ini diterbitkan secara elektronik dan sah tanpa tanda tangan basah.<br>
            Untuk pelacakan nomor resi pengiriman kurir ekspedisi, periksa status di akun CamHub Anda.
        </footer>
    </div>

</body>
</html>

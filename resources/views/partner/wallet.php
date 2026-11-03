<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Saldo & Pencairan Dana — CamHub Mitra Lapak</title>
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
        .sidebar-footer { border-top: 1px solid rgba(255, 255, 255, 0.1); padding-top: 20px; }
        .sidebar-footer a { color: #94A3B8; text-decoration: none; font-size: 13px; font-weight: 600; }
        
        .main-content { flex-grow: 1; padding: 40px 48px; overflow-y: auto; max-width: 1000px; }
        .page-header { margin-bottom: 28px; }
        .page-title { font-size: 28px; font-weight: 800; color: #0F172A; }
        
        .balance-card { background: linear-gradient(135deg, #0F172A 0%, #1E293B 100%); color: #FFFFFF; border-radius: 16px; padding: 32px; margin-bottom: 32px; display: flex; justify-content: space-between; align-items: center; }
        .balance-label { font-size: 14px; color: #94A3B8; margin-bottom: 8px; }
        .balance-value { font-size: 38px; font-weight: 800; color: #F59E0B; }
        .btn-payout { background: #2563EB; color: #FFFFFF; border: none; padding: 14px 28px; border-radius: 10px; font-size: 15px; font-weight: 700; cursor: pointer; }
        .btn-payout:hover { background: #1D4ED8; }
        
        .card { background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 14px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.02); }
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
            <li class="nav-item"><a href="<?= base_url('/dashboard/mitra/produk') ?>"><span class="nav-icon">▣</span> Kelola Produk</a></li>
            <li class="nav-item"><a href="<?= base_url('/dashboard/mitra/jadwal') ?>"><span class="nav-icon">▦</span> Jadwal Sewa</a></li>
            <li class="nav-item"><a href="<?= base_url('/dashboard/mitra/transaksi') ?>"><span class="nav-icon">⧉</span> Transaksi</a></li>
            <li class="nav-item active"><a href="<?= base_url('/dashboard/mitra/saldo') ?>"><span class="nav-icon">Rp</span> Saldo & Pencairan</a></li>
            <li class="nav-item"><a href="<?= base_url('/dashboard/mitra/pengaturan') ?>"><span class="nav-icon">⚙</span> Pengaturan</a></li>
        </ul>
        <div class="sidebar-footer">
            <a href="<?= base_url('/') ?>" target="_blank"><span>🌐</span> Website Publik</a>
        </div>
    </aside>

    <main class="main-content">
        <div class="page-header">
            <h1 class="page-title">Saldo Dompet & Pencairan</h1>
            <p style="color:#64748B; font-size:14px; margin-top:4px;">Kelola dana pendapatan sewa dan penjualan yang telah selesai diverifikasi rekening bersama.</p>
        </div>

        <div class="balance-card">
            <div>
                <div class="balance-label">Total Saldo Dompet Tersedia</div>
                <div class="balance-value"><?= $metrics['available_balance_formatted'] ?? 'Rp 2.450.000' ?></div>
                <div style="color:#94A3B8; font-size:13px; margin-top:8px;">* Dana hasil sewa aman dilepas otomatis setelah unit dikembalikan utuh.</div>
            </div>
            <div>
                <button type="button" class="btn-payout" onclick="alert('Permintaan pencairan dana sedang diproses oleh tim keuangan CamHub.');">
                    Tarik Saldo ke Rekening
                </button>
            </div>
        </div>

        <div class="card">
            <h2 style="font-size:18px; font-weight:800; color:#0F172A; margin-bottom:12px;">Informasi Rekening Pencairan</h2>
            <p style="color:#64748B; font-size:14px; line-height:1.6;">
                Pencairan dana dikirimkan ke rekening pemilik toko terdaftar pada hari kerja (maksimal 1x24 jam). Bebas biaya admin transfer antar-bank dengan sistem rekening bersama CamHub Escrow Guard.
            </p>
        </div>
    </main>

</body>
</html>

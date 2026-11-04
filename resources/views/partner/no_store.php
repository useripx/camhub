<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Lapak Mitra — CamHub</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; font-family:'Plus Jakarta Sans', sans-serif; }
        body { background:#0F172A; color:#FFFFFF; display:flex; align-items:center; justify-content:center; min-height:100vh; padding:20px; }
        .card { background:#1E293B; border:1px solid #334155; border-radius:16px; padding:40px; max-width:500px; text-align:center; }
        h1 { font-size:24px; font-weight:800; margin-bottom:12px; }
        p { color:#94A3B8; font-size:14px; margin-bottom:24px; line-height:1.6; }
        .btn { background:#2563EB; color:#fff; text-decoration:none; padding:12px 24px; border-radius:8px; font-weight:700; display:inline-block; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Lapak Mitra Belum Aktif</h1>
        <p>Akun Anda terdaftar sebagai Mitra tetapi toko Anda belum terhubung. Hubungi administrator CamHub atau kembali ke beranda.</p>
        <div style="display:flex; gap:12px; justify-content:center; align-items:center; flex-wrap:wrap;">
            <a href="<?= base_url() ?>" class="btn">Kembali ke Beranda Publik</a>
            <form action="<?= base_url('/logout') ?>" method="POST" style="margin:0;">
                <?= csrf_field() ?>
                <button type="submit" style="background:#dc2626; color:#ffffff; font-weight:700; border-radius:8px; padding:12px 20px; border:none; cursor:pointer;">
                    Keluar Akun
                </button>
            </form>
        </div>
    </div>
</body>
</html>

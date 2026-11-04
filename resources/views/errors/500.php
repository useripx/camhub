<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 — Terjadi Kendala Sistem | CamHub</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; font-family:'Plus Jakarta Sans', sans-serif; }
        body { background:#F8FAFC; color:#0F172A; min-height:100vh; display:flex; align-items:center; justify-content:center; padding:24px; }
        .error-card { background:#FFFFFF; border:1px solid #E2E8F0; border-radius:16px; padding:48px 40px; max-width:520px; width:100%; text-align:center; box-shadow:0 10px 25px -5px rgba(0,0,0,0.05); }
        .error-code { font-size:64px; font-weight:800; color:#EF4444; letter-spacing:-1px; line-height:1; margin-bottom:12px; }
        .error-title { font-size:22px; font-weight:800; color:#0F172A; margin-bottom:12px; }
        .error-desc { font-size:15px; color:#64748B; line-height:1.6; margin-bottom:32px; }
        .btn-home { display:inline-flex; align-items:center; gap:8px; background:#2563EB; color:#FFFFFF; text-decoration:none; padding:12px 24px; border-radius:10px; font-size:14px; font-weight:700; transition:0.2s; }
        .btn-home:hover { background:#1D4ED8; }
    </style>
</head>
<body>
    <div class="error-card">
        <div class="error-code">500</div>
        <h1 class="error-title">Terjadi Kendala pada Sistem</h1>
        <p class="error-desc">Maaf, sistem CamHub sedang mengalami kendala teknis sementara. Tim kami telah mencatat peristiwa ini untuk penanganan lebih lanjut.</p>
        <a href="<?= function_exists('base_url') ? base_url('/') : '/' ?>" class="btn-home">
            <span>⌂</span> Kembali ke Beranda
        </a>
    </div>
</body>
</html>


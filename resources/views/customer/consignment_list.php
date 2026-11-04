<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Titip Jual Saya — CamHub</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; font-family:'Plus Jakarta Sans', sans-serif; }
        body { background:#f8fafc; color:#0f172a; min-height:100vh; }
        
        .topbar { background:#0c192c; padding:18px 48px; display:flex; justify-content:space-between; align-items:center; }
        .topbar a.brand { font-size:22px; font-weight:800; color:#fff; text-decoration:none; }
        .topbar .nav-links a { color:#94a3b8; text-decoration:none; font-size:14px; font-weight:600; margin-left:20px; transition:color 0.2s; }
        .topbar .nav-links a:hover { color:#fff; }

        .container { max-width:1050px; margin:40px auto; padding:0 24px 60px; }
        .page-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:28px; flex-wrap:wrap; gap:16px; }
        .page-title { font-size:26px; font-weight:800; color:#0f172a; }
        .page-desc { color:#64748b; font-size:14px; margin-top:4px; }
        
        .btn-add { background:#2563eb; color:#ffffff; text-decoration:none; font-weight:700; font-size:14px; padding:12px 20px; border-radius:10px; display:inline-flex; align-items:center; gap:8px; transition:background 0.2s; }
        .btn-add:hover { background:#1d4ed8; }

        .alert-success { background:#ecfdf5; border:1px solid #a7f3d0; color:#065f46; padding:14px 18px; border-radius:10px; font-size:14px; margin-bottom:24px; font-weight:600; }

        .empty-state { background:#ffffff; border:1px solid #e2e8f0; border-radius:16px; padding:60px 24px; text-align:center; }
        .empty-icon { font-size:48px; margin-bottom:12px; }
        .empty-title { font-size:18px; font-weight:700; color:#1e293b; margin-bottom:6px; }
        .empty-desc { font-size:14px; color:#64748b; max-width:440px; margin:0 auto 20px; }

        .consign-card { background:#ffffff; border:1px solid #e2e8f0; border-radius:16px; padding:24px 28px; margin-bottom:20px; box-shadow:0 1px 3px rgba(0,0,0,0.02); }
        .consign-header { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:16px; flex-wrap:wrap; gap:12px; }
        .item-name { font-size:18px; font-weight:800; color:#0f172a; }
        .store-badge { font-size:13px; color:#64748b; margin-top:4px; font-weight:500; }
        
        .status-pill { font-size:12px; font-weight:700; padding:6px 14px; border-radius:20px; text-transform:uppercase; letter-spacing:0.5px; }

        .grid-details { display:grid; grid-template-columns:repeat(auto-fit, minmax(180px, 1fr)); gap:16px; background:#f8fafc; border-radius:12px; padding:16px 20px; margin-bottom:20px; }
        .detail-item .label { font-size:12px; color:#64748b; margin-bottom:4px; font-weight:600; text-transform:uppercase; }
        .detail-item .val { font-size:15px; font-weight:700; color:#0f172a; }

        /* Stepper Lifecycle Progress */
        .lifecycle-stepper { display:flex; justify-content:space-between; margin-top:20px; position:relative; padding:0 10px; }
        .lifecycle-stepper::before { content:''; position:absolute; top:12px; left:30px; right:30px; height:3px; background:#e2e8f0; z-index:1; }
        .step-node { position:relative; z-index:2; text-align:center; flex:1; }
        .step-dot { width:26px; height:26px; border-radius:50%; background:#e2e8f0; color:#64748b; display:inline-flex; align-items:center; justify-content:center; font-size:12px; font-weight:700; margin-bottom:6px; border:2px solid #ffffff; }
        .step-node.active .step-dot { background:#2563eb; color:#ffffff; }
        .step-node.completed .step-dot { background:#10b981; color:#ffffff; }
        .step-text { font-size:12px; font-weight:600; color:#64748b; }
        .step-node.active .step-text { color:#2563eb; font-weight:700; }
        .step-node.completed .step-text { color:#10b981; }

        .item-desc-box { font-size:13px; color:#475569; line-height:1.6; border-left:3px solid #cbd5e1; padding-left:12px; margin-top:14px; }
    </style>
</head>
<body>

    <header class="topbar">
        <a href="<?= base_url() ?>" class="brand">CamHub</a>
        <div class="nav-links">
            <a href="<?= base_url('/dashboard/pelanggan') ?>">Dashboard</a>
            <a href="<?= base_url('/pesanan-saya') ?>">Sewa Saya</a>
            <a href="<?= base_url('/jual-beli-saya') ?>">Jual Beli Saya</a>
            <a href="<?= base_url('/titip-jual/saya') ?>" style="color:#fff; font-weight:700;">Titip Jual Saya</a>
            <a href="<?= base_url('/logout') ?>" style="color:#ef4444;">Keluar</a>
        </div>
    </header>

    <main class="container">
        <div class="page-header">
            <div>
                <h1 class="page-title">Titip Jual Saya</h1>
                <p class="page-desc">Pantau status pengajuan unit kamera yang Anda titipkan ke Toko Mitra CamHub.</p>
            </div>
            <a href="<?= base_url('/titip-jual/ajukan') ?>" class="btn-add">
                <span>+</span> Ajukan Titip Jual Baru
            </a>
        </div>

        <?php if (!empty($success)): ?>
            <div class="alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <?php if (empty($consignments)): ?>
            <div class="empty-state">
                <div class="empty-icon">📦</div>
                <h3 class="empty-title">Belum Ada Kamera yang Dititipkan</h3>
                <p class="empty-desc">Punya kamera tidak terpakai? Titipkan sekarang ke Toko Mitra CamHub terdekat dan dapatkan hasil penjualan secara aman via Escrow.</p>
                <a href="<?= base_url('/titip-jual/ajukan') ?>" class="btn-add">Mulai Titip Jual Sekarang</a>
            </div>
        <?php else: ?>
            <?php foreach ($consignments as $c): 
                $consignObj = new \App\Models\Consignment($c);
                $badge = $consignObj->getStatusBadge();
                $st = $c['status'];
            ?>
                <div class="consign-card">
                    <div class="consign-header">
                        <div>
                            <div class="item-name"><?= htmlspecialchars($c['item_name']) ?></div>
                            <div class="store-badge">Toko Mitra: <strong><?= htmlspecialchars($c['store_name'] ?? 'Mitra Lapak') ?></strong> (<?= htmlspecialchars($c['store_city'] ?? '') ?>) · Diajukan pada <?= date('d M Y, H:i', strtotime($c['created_at'])) ?></div>
                        </div>
                        <span class="status-pill" style="background:<?= $badge['bg'] ?>; color:<?= $badge['color'] ?>;">
                            <?= $badge['label'] ?>
                        </span>
                    </div>

                    <div class="grid-details">
                        <div class="detail-item">
                            <div class="label">Harga Diminta (User)</div>
                            <div class="val"><?= $consignObj->getFormattedRequestedPrice() ?></div>
                        </div>
                        <div class="detail-item">
                            <div class="label">Harga Kesepakatan</div>
                            <div class="val"><?= $consignObj->getFormattedAgreedPrice() ?></div>
                        </div>
                        <div class="detail-item">
                            <div class="label">Komisi Toko Mitra</div>
                            <div class="val"><?= number_format((float)$c['commission_percent'], 0) ?>%</div>
                        </div>
                        <div class="detail-item">
                            <div class="label">Estimasi Dana Cair</div>
                            <div class="val" style="color:#16a34a;"><?= $consignObj->getFormattedEstimatedPayout() ?></div>
                        </div>
                    </div>

                    <!-- Progress Lifecycle Stepper -->
                    <div class="lifecycle-stepper">
                        <div class="step-node <?= in_array($st, ['submitted', 'inspected', 'approved', 'listed', 'sold']) ? 'completed' : '' ?>">
                            <div class="step-dot">1</div>
                            <div class="step-text">Pengajuan</div>
                        </div>
                        <div class="step-node <?= in_array($st, ['inspected', 'approved', 'listed', 'sold']) ? 'completed' : ($st === 'submitted' ? 'active' : '') ?>">
                            <div class="step-dot">2</div>
                            <div class="step-text">Inspeksi Mitra</div>
                        </div>
                        <div class="step-node <?= in_array($st, ['approved', 'listed', 'sold']) ? 'completed' : ($st === 'inspected' ? 'active' : '') ?>">
                            <div class="step-dot">3</div>
                            <div class="step-text">Kesepakatan</div>
                        </div>
                        <div class="step-node <?= in_array($st, ['listed', 'sold']) ? 'completed' : ($st === 'approved' ? 'active' : '') ?>">
                            <div class="step-dot">4</div>
                            <div class="step-text">Tayang di Katalog</div>
                        </div>
                        <div class="step-node <?= ($st === 'sold') ? 'completed' : ($st === 'listed' ? 'active' : '') ?>">
                            <div class="step-dot">5</div>
                            <div class="step-text">Terjual & Cair</div>
                        </div>
                    </div>

                    <div class="item-desc-box">
                        <strong>Catatan Kondisi Unit:</strong> <?= htmlspecialchars($c['item_description']) ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </main>

</body>
</html>

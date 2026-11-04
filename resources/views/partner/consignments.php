<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Titip Jual — Dashboard Mitra CamHub</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; font-family:'Plus Jakarta Sans', sans-serif; }
        body { background:#F8FAFC; color:#0F172A; min-height:100vh; display:flex; }

        /* SIDEBAR GELAP NAVY (PERSIS MOCKUP) */
        .sidebar {
            width:260px;
            background:#0c192c;
            min-height:100vh;
            padding:32px 24px;
            display:flex;
            flex-direction:column;
            justify-content:space-between;
            flex-shrink:0;
        }
        .brand-logo { font-size:24px; font-weight:800; color:#FFFFFF; text-decoration:none; display:block; margin-bottom:4px; }
        .brand-store { font-size:14px; font-weight:600; color:#CBD5E1; margin-bottom:36px; }
        .nav-menu { list-style:none; display:flex; flex-direction:column; gap:8px; flex-grow:1; }
        .nav-item a { display:flex; align-items:center; gap:14px; padding:12px 14px; color:#94A3B8; text-decoration:none; font-size:14px; font-weight:600; border-radius:8px; transition:all 0.2s; }
        .nav-item a:hover { color:#FFFFFF; background:rgba(255, 255, 255, 0.05); }
        .nav-item.active a { color:#FFFFFF; background:rgba(255, 255, 255, 0.1); }
        .sidebar-footer { border-top:1px solid rgba(255, 255, 255, 0.1); padding-top:20px; display:flex; flex-direction:column; gap:10px; }
        .sidebar-footer a { color:#94A3B8; text-decoration:none; font-size:13px; font-weight:600; padding:8px 12px; }

        /* MAIN CONTENT */
        .main-content { flex-grow:1; padding:40px 48px; overflow-y:auto; max-width:1200px; }
        .page-header { margin-bottom:28px; }
        .page-title { font-size:28px; font-weight:800; color:#0F172A; margin-bottom:6px; }
        .page-subtitle { font-size:14px; color:#64748B; }

        .flash-alert { padding:14px 20px; border-radius:10px; font-size:14px; font-weight:600; margin-bottom:24px; }
        .flash-success { background:#ECFDF5; border:1px solid #A7F3D0; color:#065F46; }

        .card-consign { background:#FFFFFF; border:1px solid #E2E8F0; border-radius:14px; padding:24px; margin-bottom:20px; box-shadow:0 1px 3px rgba(0,0,0,0.02); }
        .card-header { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:14px; flex-wrap:wrap; gap:12px; }
        .unit-name { font-size:18px; font-weight:800; color:#0F172A; }
        .owner-info { font-size:13px; color:#64748B; margin-top:4px; }
        
        .badge { font-size:11px; font-weight:700; padding:5px 12px; border-radius:20px; text-transform:uppercase; }

        .desc-box { background:#F8FAFC; border-radius:10px; padding:14px 18px; font-size:13px; color:#475569; line-height:1.6; margin-bottom:18px; border-left:3px solid #CBD5E1; }

        .price-row { display:flex; gap:24px; margin-bottom:18px; flex-wrap:wrap; }
        .price-col .plabel { font-size:12px; font-weight:600; color:#64748B; text-transform:uppercase; margin-bottom:4px; }
        .price-col .pval { font-size:16px; font-weight:800; color:#0F172A; }

        .action-box { background:#F1F5F9; border-radius:12px; padding:18px 20px; margin-top:14px; }
        .form-inline { display:flex; gap:14px; align-items:flex-end; flex-wrap:wrap; }
        .form-group-sm { flex:1; min-width:180px; }
        .form-label-sm { font-size:12px; font-weight:700; color:#1E293B; margin-bottom:6px; display:block; }
        .form-input-sm { width:100%; padding:10px 14px; border:1px solid #CBD5E1; border-radius:8px; font-size:13px; }

        .btn-approve { background:#16A34A; color:#FFFFFF; font-size:13px; font-weight:700; padding:11px 20px; border:none; border-radius:8px; cursor:pointer; transition:0.2s; }
        .btn-approve:hover { background:#15803D; }
        .btn-publish { background:#2563EB; color:#FFFFFF; font-size:13px; font-weight:700; padding:11px 20px; border:none; border-radius:8px; cursor:pointer; transition:0.2s; }
        .btn-publish:hover { background:#1D4ED8; }
        .btn-reject { background:#EF4444; color:#FFFFFF; font-size:13px; font-weight:700; padding:11px 18px; border:none; border-radius:8px; cursor:pointer; }
        .btn-reject:hover { background:#DC2626; }
    </style>
    <script>
        (function() {
            const theme = localStorage.getItem('camhub_theme') || 'dark';
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>
</head>
<body>

    <aside class="sidebar">
        <div>
            <a href="<?= base_url('/dashboard/mitra') ?>" class="brand-logo">CamHub</a>
            <div class="brand-store"><?= htmlspecialchars($partner['store_name'] ?? 'Mitra Lapak') ?></div>
        </div>

        <ul class="nav-menu">
            <li class="nav-item">
                <a href="<?= base_url('/dashboard/mitra') ?>">
                    Beranda
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= base_url('/dashboard/mitra/produk') ?>">
                    Kelola Produk
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= base_url('/dashboard/mitra/jadwal') ?>">
                    Jadwal Sewa
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= base_url('/dashboard/mitra/transaksi') ?>">
                    Transaksi
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= base_url('/dashboard/mitra/saldo') ?>">
                    Saldo & Pencairan
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= base_url('/dashboard/mitra/pengaturan') ?>">
                    Pengaturan
                </a>
            </li>
            <li class="nav-item active">
                <a href="<?= base_url('/dashboard/mitra/titip-jual') ?>">
                    Kelola Titip Jual
                </a>
            </li>
        </ul>

        <div class="sidebar-footer">
            <button type="button" class="btn-theme-toggle" onclick="toggleCamHubTheme()" title="Beralih Mode Gelap/Terang" style="width:100%; background:#16243a; color:#cbd5e1; border:1px solid #1e3a5f; padding:9px 12px; border-radius:8px; font-size:12px; font-weight:600; cursor:pointer; text-align:center; margin-bottom:8px;">
                <span id="themeToggleText">Mode Terang</span>
            </button>
            <a href="<?= base_url('/') ?>" target="_blank">Beranda Publik</a>
            <form action="<?= base_url('/logout') ?>" method="POST" style="margin:0; width:100%;">
                <?= csrf_field() ?>
                <button type="submit" style="background:#dc2626; color:#ffffff; font-weight:700; border-radius:8px; justify-content:center; padding:10px 14px; margin-top:8px; width:100%; display:flex; align-items:center; gap:8px; border:none; cursor:pointer;">
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    <main class="main-content">
        <div class="page-header">
            <h1 class="page-title">Kelola Titip Jual Kamera</h1>
            <p class="page-subtitle">Verifikasi fisik unit kamera titipan pelanggan, tetapkan harga kesepakatan, dan tayangkan ke etalase publik.</p>
        </div>

        <?php if (!empty($success)): ?>
            <div class="flash-alert flash-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <?php if (empty($consignments)): ?>
            <div style="background:#fff; border:1px solid #E2E8F0; border-radius:14px; padding:48px 24px; text-align:center; color:#64748B;">
                <div style="font-size:40px; margin-bottom:12px;">📦</div>
                <h3 style="color:#0F172A; margin-bottom:6px;">Belum Ada Pengajuan Titip Jual Masuk</h3>
                <p>Ketika pelanggan memilih toko Anda untuk konsinyasi, permohonan akan muncul di halaman ini.</p>
            </div>
        <?php else: ?>
            <?php foreach ($consignments as $c): 
                $obj = new \App\Models\Consignment($c);
                $badge = $obj->getStatusBadge();
                $st = $c['status'];
            ?>
                <div class="card-consign">
                    <div class="card-header">
                        <div>
                            <div class="unit-name"><?= htmlspecialchars($c['item_name']) ?></div>
                            <div class="owner-info">
                                Pemilik: <strong><?= htmlspecialchars($c['user_name'] ?? 'Pelanggan') ?></strong> (<?= htmlspecialchars($c['user_phone'] ?? '-') ?>) · Diajukan <?= date('d M Y, H:i', strtotime($c['created_at'])) ?>
                            </div>
                        </div>
                        <span class="badge" style="background:<?= $badge['bg'] ?>; color:<?= $badge['color'] ?>;">
                            <?= $badge['label'] ?>
                        </span>
                    </div>

                    <div class="desc-box">
                        <strong>Kondisi & Kelengkapan Fisik:</strong><br>
                        <?= nl2br(htmlspecialchars($c['item_description'])) ?>
                    </div>

                    <div class="price-row">
                        <div class="price-col">
                            <div class="plabel">Harga Diminta Pemilik</div>
                            <div class="pval"><?= $obj->getFormattedRequestedPrice() ?></div>
                        </div>
                        <div class="price-col">
                            <div class="plabel">Harga Kesepakatan Final</div>
                            <div class="pval" style="color:#2563EB;"><?= $obj->getFormattedAgreedPrice() ?></div>
                        </div>
                        <div class="price-col">
                            <div class="plabel">Komisi Toko (10%)</div>
                            <div class="pval"><?= number_format((float)$c['commission_percent'], 0) ?>%</div>
                        </div>
                    </div>

                    <?php if ($st === 'submitted' || $st === 'inspected'): ?>
                        <div class="action-box">
                            <form action="<?= base_url('/dashboard/mitra/titip-jual/' . (int)$c['id'] . '/inspeksi') ?>" method="POST" class="form-inline">
                                <?= csrf_field() ?>
                                <div class="form-group-sm">
                                    <label class="form-label-sm">Harga Kesepakatan (Rp)</label>
                                    <input type="number" name="agreed_price" class="form-input-sm" value="<?= htmlspecialchars((string)($c['agreed_price'] ?? $c['requested_price'])) ?>" min="100000" step="50000" required>
                                </div>
                                <div class="form-group-sm" style="max-width:140px;">
                                    <label class="form-label-sm">Komisi Toko (%)</label>
                                    <input type="number" name="commission_percent" class="form-input-sm" value="10" min="5" max="30" required>
                                </div>
                                <button type="submit" name="action" value="approve" class="btn-approve">✓ Lolos Inspeksi & Setujui Harga</button>
                                <button type="submit" name="action" value="reject" class="btn-reject" onclick="return confirm('Tolak permohonan titip jual ini?')">✕ Tolak</button>
                            </form>
                        </div>
                    <?php elseif ($st === 'approved'): ?>
                        <div class="action-box" style="display:flex; justify-content:space-between; align-items:center;">
                            <div>
                                <strong style="color:#15803D;">✓ Harga Disetujui: <?= $obj->getFormattedAgreedPrice() ?></strong>
                                <div style="font-size:12px; color:#64748B;">Unit siap diterbitkan ke etalase publik CamHub.</div>
                            </div>
                            <form action="<?= base_url('/dashboard/mitra/titip-jual/' . (int)$c['id'] . '/setujui') ?>" method="POST" style="margin:0;">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn-publish">📢 Terbitkan ke Katalog Publik</button>
                            </form>
                        </div>
                    <?php elseif ($st === 'listed'): ?>
                        <div style="font-size:13px; color:#2563EB; font-weight:700; margin-top:10px;">
                            🌐 Sedang Tayang di Etalase Publik dengan badge "Titip Jual"
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </main>

    <script>
        function toggleCamHubTheme() {
            const currentTheme = document.documentElement.getAttribute('data-theme') || 'dark';
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('camhub_theme', newTheme);
            syncThemeToggleUI(newTheme);
        }

        function syncThemeToggleUI(theme) {
            const text = document.getElementById('themeToggleText');
            if (!text) return;
            text.textContent = (theme === 'dark') ? 'Mode Terang' : 'Mode Gelap';
        }

        document.addEventListener('DOMContentLoaded', function() {
            const active = document.documentElement.getAttribute('data-theme') || 'dark';
            syncThemeToggleUI(active);
        });
    </script>
</body>
</html>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Saldo & Pencairan Dana — Dashboard Mitra CamHub</title>
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

        /* MAIN CANVAS */
        .main-content { flex-grow:1; padding:40px 48px; overflow-y:auto; max-width:1200px; }
        .page-header { margin-bottom:28px; }
        .page-title { font-size:28px; font-weight:800; color:#0F172A; margin-bottom:6px; }
        .page-subtitle { font-size:14px; color:#64748B; }

        .flash-alert { padding:14px 20px; border-radius:10px; font-size:14px; font-weight:600; margin-bottom:24px; }
        .flash-success { background:#ECFDF5; border:1px solid #A7F3D0; color:#065F46; }
        .flash-error { background:#FEF2F2; border:1px solid #FECACA; color:#991B1B; }

        /* STATS GRID */
        .stats-grid { display:grid; grid-template-columns:repeat(auto-fit, minmax(260px, 1fr)); gap:20px; margin-bottom:36px; }
        .stat-card { background:#FFFFFF; border:1px solid #E2E8F0; border-radius:14px; padding:24px; box-shadow:0 1px 3px rgba(0,0,0,0.02); }
        .stat-label { font-size:13px; font-weight:600; color:#64748B; margin-bottom:12px; }
        .stat-value { font-size:28px; font-weight:800; color:#0F172A; }
        .stat-value.green { color:#16A34A; }
        .stat-value.amber { color:#D97706; }

        /* SECTION & TABLES */
        .section-card { background:#FFFFFF; border:1px solid #E2E8F0; border-radius:16px; padding:28px; margin-bottom:30px; box-shadow:0 1px 3px rgba(0,0,0,0.02); }
        .section-title { font-size:18px; font-weight:800; color:#0F172A; margin-bottom:18px; display:flex; justify-content:space-between; align-items:center; }

        .form-grid { display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:18px; margin-bottom:20px; }
        .form-group label { display:block; font-size:13px; font-weight:700; color:#1E293B; margin-bottom:6px; }
        .form-control { width:100%; padding:11px 14px; border:1px solid #CBD5E1; border-radius:8px; font-size:14px; color:#0F172A; }
        .form-control:focus { outline:none; border-color:#2563EB; }

        .btn-withdraw { background:#16A34A; color:#FFFFFF; font-size:14px; font-weight:700; padding:12px 24px; border:none; border-radius:8px; cursor:pointer; transition:0.2s; }
        .btn-withdraw:hover { background:#15803D; }

        .table-custom { width:100%; border-collapse:collapse; font-size:13px; }
        .table-custom th { text-align:left; padding:12px 14px; background:#F8FAFC; color:#64748B; font-weight:600; border-bottom:1px solid #E2E8F0; }
        .table-custom td { padding:14px; border-bottom:1px solid #F1F5F9; color:#1E293B; }
        .badge { padding:4px 10px; border-radius:20px; font-size:11px; font-weight:700; text-transform:uppercase; }
        .badge-held { background:#FEF3C7; color:#B45309; }
        .badge-released { background:#DCFCE7; color:#15803D; }
        .badge-refunded { background:#FEE2E2; color:#B91C1C; }
        .badge-disputed { background:#EDE9FE; color:#6D28D9; }
    </style>
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
                    <span class="nav-icon">⌂</span> Beranda
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= base_url('/dashboard/mitra/produk') ?>">
                    <span class="nav-icon">▣</span> Kelola Produk
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= base_url('/dashboard/mitra/jadwal') ?>">
                    <span class="nav-icon">▦</span> Jadwal Sewa
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= base_url('/dashboard/mitra/transaksi') ?>">
                    <span class="nav-icon">⧉</span> Transaksi
                </a>
            </li>
            <li class="nav-item active">
                <a href="<?= base_url('/dashboard/mitra/saldo') ?>">
                    <span class="nav-icon">Rp</span> Saldo & Pencairan
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= base_url('/dashboard/mitra/pengaturan') ?>">
                    <span class="nav-icon">⚙</span> Pengaturan
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= base_url('/dashboard/mitra/titip-jual') ?>">
                    <span class="nav-icon">📦</span> Kelola Titip Jual
                </a>
            </li>
        </ul>

        <div class="sidebar-footer">
            <a href="<?= base_url('/') ?>" target="_blank"><span>🌐</span> Website Publik</a>
            <form action="<?= base_url('/logout') ?>" method="POST" style="margin:0;">
                <?= csrf_field() ?>
                <button type="submit" style="background:#dc2626; color:#ffffff; font-weight:700; border-radius:8px; justify-content:center; padding:10px 14px; margin-top:8px; width:100%; display:flex; align-items:center; gap:8px; border:none; cursor:pointer;">
                    <span>🚪</span> Keluar Akun
                </button>
            </form>
        </div>
    </aside>

    <main class="main-content">
        <div class="page-header">
            <h1 class="page-title">Saldo & Pencairan Dana</h1>
            <p class="page-subtitle">Kelola Saldo Dompet toko, pantau pergerakan dana rekening bersama Escrow, dan ajukan Tarik Saldo ke rekening bank Anda.</p>
        </div>

        <?php if (!empty($success)): ?>
            <div class="flash-alert flash-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        <?php if (!empty($error)): ?>
            <div class="flash-alert flash-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <!-- 3 KARTU METRIK SALDO -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">Saldo Tersedia (Siap Ditarik)</div>
                <div class="stat-value green">Rp <?= number_format((float)($metrics['available_balance'] ?? 0), 0, ',', '.') ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Saldo Tertahan di Escrow</div>
                <div class="stat-value amber">Rp <?= number_format((float)($metrics['held_balance'] ?? 0), 0, ',', '.') ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Total Dana Pernah Ditarik</div>
                <div class="stat-value">Rp <?= number_format((float)($metrics['total_withdrawn'] ?? 0), 0, ',', '.') ?></div>
            </div>
        </div>

        <!-- FORMULIR PENARIKAN SALDO -->
        <div class="section-card">
            <h2 class="section-title">Ajukan Penarikan Dana (Withdrawal)</h2>
            <form action="<?= base_url('/dashboard/mitra/saldo/tarik') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="amount">Nominal Penarikan (Rp)</label>
                        <input type="number" id="amount" name="amount" class="form-control" placeholder="Min. Rp 50.000" min="50000" max="<?= (float)($metrics['available_balance'] ?? 0) ?>" step="10000" required>
                    </div>
                    <div class="form-group">
                        <label for="bank_name">Nama Bank Tujuan</label>
                        <select id="bank_name" name="bank_name" class="form-control" required>
                            <option value="BCA">Bank BCA</option>
                            <option value="Mandiri">Bank Mandiri</option>
                            <option value="BRI">Bank BRI</option>
                            <option value="BNI">Bank BNI</option>
                            <option value="BSI">Bank Syariah Indonesia (BSI)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="account_number">Nomor Rekening</label>
                        <input type="text" id="account_number" name="account_number" class="form-control" placeholder="Contoh: 8820391823" required>
                    </div>
                    <div class="form-group">
                        <label for="account_holder">Nama Pemilik Rekening</label>
                        <input type="text" id="account_holder" name="account_holder" class="form-control" placeholder="Nama sesuai buku tabungan" required>
                    </div>
                </div>
                <button type="submit" class="btn-withdraw">Ajukan Pencairan Sekarang →</button>
            </form>
        </div>

        <!-- RIWAYAT PENARIKAN DANA -->
        <div class="section-card">
            <h2 class="section-title">Riwayat Penarikan Dana ke Bank</h2>
            <?php if (empty($withdrawals)): ?>
                <p style="color:#64748B; font-size:14px;">Belum ada riwayat pengajuan penarikan dana.</p>
            <?php else: ?>
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tanggal Pengajuan</th>
                            <th>Bank & No. Rekening</th>
                            <th>Pemilik Rekening</th>
                            <th>Nominal</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($withdrawals as $w): ?>
                            <tr>
                                <td>#WD-<?= str_pad((string)$w['id'], 4, '0', STR_PAD_LEFT) ?></td>
                                <td><?= date('d M Y, H:i', strtotime($w['created_at'])) ?></td>
                                <td><strong><?= htmlspecialchars($w['bank_name']) ?></strong> — <?= htmlspecialchars($w['account_number']) ?></td>
                                <td><?= htmlspecialchars($w['account_holder']) ?></td>
                                <td style="font-weight:700; color:#16A34A;">Rp <?= number_format((float)$w['amount'], 0, ',', '.') ?></td>
                                <td>
                                    <span class="badge" style="background:#e0e7ff; color:#4338ca;">
                                        <?= htmlspecialchars(strtoupper($w['status'])) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <!-- MUTASI TRANSAKSI ESCROW MASUK -->
        <div class="section-card">
            <h2 class="section-title">Mutasi Dana Masuk Rekening Bersama Escrow</h2>
            <?php if (empty($transactions)): ?>
                <p style="color:#64748B; font-size:14px;">Belum ada mutasi transaksi escrow terkait toko Anda.</p>
            <?php else: ?>
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>No. Referensi</th>
                            <th>Jenis Transaksi</th>
                            <th>Unit Kamera</th>
                            <th>Nominal Escrow</th>
                            <th>Status Escrow</th>
                            <th>Waktu Tertahan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($transactions as $t): 
                            $badgeCls = match($t['escrow_status']) {
                                'released' => 'badge-released',
                                'refunded' => 'badge-refunded',
                                'disputed' => 'badge-disputed',
                                default => 'badge-held'
                            };
                        ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($t['reference_code'] ?? '-') ?></strong></td>
                                <td><?= htmlspecialchars($t['transaction_type']) ?></td>
                                <td><?= htmlspecialchars($t['item_name'] ?? 'Kamera') ?></td>
                                <td style="font-weight:700;">Rp <?= number_format((float)$t['amount_held'], 0, ',', '.') ?></td>
                                <td><span class="badge <?= $badgeCls ?>"><?= htmlspecialchars(strtoupper($t['escrow_status'])) ?></span></td>
                                <td><?= date('d M Y, H:i', strtotime($t['held_at'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </main>

</body>
</html>

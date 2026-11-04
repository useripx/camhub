<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan & Operasional — CamHub Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; font-family:'Plus Jakarta Sans', sans-serif; }
        body { background:#f8fafc; color:#0f172a; display:flex; min-height:100vh; }
        .admin-sidebar { width:260px; background:#0c192c; color:#ffffff; padding:32px 24px; display:flex; flex-direction:column; flex-shrink:0; min-height:100vh; }
        .sidebar-brand { font-size:24px; font-weight:800; color:#ffffff; text-decoration:none; display:block; margin-bottom:44px; letter-spacing:-0.5px; }
        .sidebar-nav { list-style:none; display:flex; flex-direction:column; gap:22px; }
        .sidebar-nav a { color:#94a3b8; text-decoration:none; font-size:15px; font-weight:500; display:flex; align-items:center; transition:color 0.2s; }
        .sidebar-nav a:hover { color:#ffffff; }
        .sidebar-nav a.active { color:#ffffff; font-weight:700; }
        .sidebar-footer { margin-top:auto; padding-top:28px; border-top:1px solid #1e293b; }
        .admin-profile { display:flex; align-items:center; gap:12px; margin-bottom:16px; }
        .admin-avatar { width:38px; height:38px; background:#2563eb; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:14px; color:#ffffff; }
        .admin-info-name { font-size:14px; font-weight:700; color:#ffffff; line-height:1.2; }
        .admin-info-role { font-size:12px; color:#64748b; }
        .btn-sidebar-logout { width:100%; background:#1e293b; color:#f87171; border:1px solid #334155; padding:9px 14px; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer; transition:all 0.2s; display:flex; align-items:center; justify-content:center; gap:6px; }
        .btn-sidebar-logout:hover { background:#dc2626; color:#ffffff; border-color:#dc2626; }

        .admin-main { flex:1; padding:40px 48px; overflow-y:auto; }
        .page-header { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:28px; }
        .page-title { font-size:28px; font-weight:800; color:#0f172a; letter-spacing:-0.5px; }
        .page-desc { font-size:14px; color:#64748b; margin-top:4px; }
        
        .header-actions { display:flex; gap:12px; align-items:center; }
        .btn-export { background:#16a34a; color:#ffffff; text-decoration:none; padding:10px 18px; border-radius:8px; font-weight:700; font-size:14px; display:inline-flex; align-items:center; gap:8px; transition:0.2s; border:none; cursor:pointer; }
        .btn-export:hover { background:#15803d; }

        .filter-card { background:#ffffff; border:1px solid #e2e8f0; border-radius:12px; padding:16px 20px; margin-bottom:28px; display:flex; flex-wrap:wrap; gap:14px; align-items:center; justify-content:space-between; }
        .filter-form { display:flex; flex-wrap:wrap; gap:12px; align-items:center; }
        .filter-group { display:flex; align-items:center; gap:8px; }
        .filter-label { font-size:13px; font-weight:700; color:#475569; }
        .filter-select, .filter-input { padding:8px 12px; border:1px solid #cbd5e1; border-radius:6px; font-size:13px; color:#0f172a; outline:none; }
        .btn-filter { background:#2563eb; color:#ffffff; border:none; padding:8px 16px; border-radius:6px; font-size:13px; font-weight:700; cursor:pointer; }
        .btn-filter:hover { background:#1d4ed8; }

        .metrics-grid { display:grid; grid-template-columns:repeat(4, 1fr); gap:20px; margin-bottom:32px; }
        .metric-card { background:#ffffff; border:1px solid #e2e8f0; border-radius:14px; padding:24px; position:relative; }
        .metric-label { font-size:13px; font-weight:600; color:#64748b; margin-bottom:8px; }
        .metric-val { font-size:26px; font-weight:800; color:#0f172a; line-height:1.2; }
        .metric-sub { font-size:12px; color:#94a3b8; margin-top:6px; }

        .section-title { font-size:18px; font-weight:800; color:#0f172a; margin-bottom:16px; }
        
        .layout-two-col { display:grid; grid-template-columns:1fr 2fr; gap:24px; margin-bottom:32px; }
        .category-card { background:#ffffff; border:1px solid #e2e8f0; border-radius:14px; padding:24px; }
        .cat-item { display:flex; justify-content:space-between; align-items:center; padding:12px 0; border-bottom:1px solid #f1f5f9; }
        .cat-item:last-child { border-bottom:none; }
        .cat-name { font-size:14px; font-weight:700; color:#1e293b; }
        .cat-count { font-size:12px; color:#64748b; }
        .cat-rev { font-size:14px; font-weight:800; color:#2563eb; }

        .table-card { background:#ffffff; border:1px solid #e2e8f0; border-radius:14px; overflow:hidden; }
        table { width:100%; border-collapse:collapse; text-align:left; }
        th { background:#f8fafc; padding:14px 18px; font-size:13px; font-weight:700; color:#475569; border-bottom:1px solid #e2e8f0; }
        td { padding:14px 18px; font-size:13px; border-bottom:1px solid #f1f5f9; vertical-align:middle; }
        tr:last-child td { border-bottom:none; }

        .badge { display:inline-block; padding:4px 10px; border-radius:20px; font-size:11px; font-weight:700; }
        .badge-rental { background:#eff6ff; color:#2563eb; }
        .badge-sale { background:#f5f3ff; color:#7c3aed; }
        .badge-held { background:#fef3c7; color:#d97706; }
        .badge-released { background:#dcfce7; color:#15803d; }
        .badge-disputed { background:#fee2e2; color:#b91c1c; }
        .badge-refunded { background:#f1f5f9; color:#475569; }

        @media(max-width:1100px) {
            .metrics-grid { grid-template-columns:1fr 1fr; }
            .layout-two-col { grid-template-columns:1fr; }
        }
    </style>
</head>
<body>

    <aside class="admin-sidebar">
        <div>
            <a href="<?= base_url('/dashboard/admin') ?>" class="sidebar-brand">CamHub</a>
            <ul class="sidebar-nav">
                <li><a href="<?= base_url('/dashboard/admin') ?>">Beranda</a></li>
                <li><a href="<?= base_url('/dashboard/admin/kyc') ?>">Verifikasi KYC</a></li>
                <li><a href="<?= base_url('/dashboard/admin/jadwal') ?>">Jadwal Unit</a></li>
                <li><a href="<?= base_url('/dashboard/admin/transaksi') ?>">Transaksi</a></li>
                <li><a href="<?= base_url('/dashboard/admin/denda') ?>">Manajemen Denda</a></li>
                <li><a href="<?= base_url('/dashboard/admin/laporan') ?>" class="active">Laporan</a></li>
                <li><a href="<?= base_url('/dashboard/admin/pengaturan') ?>">Pengaturan</a></li>
            </ul>
        </div>

        <div class="sidebar-footer">
            <div class="admin-profile">
                <div class="admin-avatar">A</div>
                <div>
                    <div class="admin-info-name">Administrator</div>
                    <div class="admin-info-role">Super Admin</div>
                </div>
            </div>
            <form action="<?= base_url('/logout') ?>" method="POST" style="margin:0;">
                <?= csrf_field() ?>
                <button type="submit" class="btn-sidebar-logout">Keluar Sesi</button>
            </form>
        </div>
    </aside>

    <main class="admin-main">
        <div class="page-header">
            <div>
                <h1 class="page-title">Laporan Keuangan & Operasional</h1>
                <p class="page-desc">Rekapitulasi perputaran kas platform, pendapatan bersih fee rekening bersama, komisi titip jual, dan ekspor data transaksi.</p>
            </div>
            <div class="header-actions">
                <?php 
                    $exportQuery = http_build_query([
                        'period' => $period,
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                    ]);
                ?>
                <a href="<?= base_url('/dashboard/admin/laporan/export?' . $exportQuery) ?>" class="btn-export">
                    <span>📊</span> Unduh Rekap CSV / Excel
                </a>
            </div>
        </div>

        <!-- Filter Periode Transaksi -->
        <div class="filter-card">
            <form action="<?= base_url('/dashboard/admin/laporan') ?>" method="GET" class="filter-form">
                <div class="filter-group">
                    <label class="filter-label" for="period">Periode:</label>
                    <select name="period" id="period" class="filter-select" onchange="toggleCustomDates(this.value)">
                        <option value="all" <?= ($period === 'all') ? 'selected' : '' ?>>Semua Waktu</option>
                        <option value="today" <?= ($period === 'today') ? 'selected' : '' ?>>Hari Ini</option>
                        <option value="7days" <?= ($period === '7days') ? 'selected' : '' ?>>7 Hari Terakhir</option>
                        <option value="month" <?= ($period === 'month') ? 'selected' : '' ?>>Bulan Ini</option>
                        <option value="custom" <?= ($period === 'custom') ? 'selected' : '' ?>>Rentang Tanggal Kustom</option>
                    </select>
                </div>

                <div class="filter-group" id="custom-dates" style="display: <?= ($period === 'custom') ? 'flex' : 'none' ?>; gap:8px;">
                    <input type="date" name="start_date" class="filter-input" value="<?= htmlspecialchars($startDate ?? '') ?>">
                    <span style="font-size:13px; color:#64748b;">s/d</span>
                    <input type="date" name="end_date" class="filter-input" value="<?= htmlspecialchars($endDate ?? '') ?>">
                </div>

                <button type="submit" class="btn-filter">Terapkan Filter</button>
            </form>
            <div style="font-size:12px; color:#64748b;">
                Menampilkan data untuk: <strong><?= strtoupper(htmlspecialchars($period)) ?></strong>
            </div>
        </div>

        <!-- 4 Kartu Metrik Keuangan -->
        <div class="metrics-grid">
            <div class="metric-card">
                <div class="metric-label">Total Omzet Gross (GTV)</div>
                <div class="metric-val">Rp <?= number_format($summary['gross_turnover'], 0, ',', '.') ?></div>
                <div class="metric-sub"><?= (int)$summary['total_transactions'] ?> total transaksi sewa & jual</div>
            </div>
            <div class="metric-card">
                <div class="metric-label">Pendapatan Bersih Platform</div>
                <div class="metric-val" style="color:#16a34a;">Rp <?= number_format($summary['platform_net_revenue'], 0, ',', '.') ?></div>
                <div class="metric-sub">Fee rekber + komisi titip + denda</div>
            </div>
            <div class="metric-card">
                <div class="metric-label">Fee Rekber Escrow</div>
                <div class="metric-val" style="color:#2563eb;">Rp <?= number_format($summary['escrow_admin_fee'], 0, ',', '.') ?></div>
                <div class="metric-sub">Rp 5.000 / transaksi terproteksi</div>
            </div>
            <div class="metric-card">
                <div class="metric-label">Pencairan Dana Mitra Selesai</div>
                <div class="metric-val" style="color:#7c3aed;">Rp <?= number_format($summary['withdrawn_total'], 0, ',', '.') ?></div>
                <div class="metric-sub">Dana ditransfer ke bank mitra</div>
            </div>
        </div>

        <div class="layout-two-col">
            <!-- Breakdown Omzet per Kategori -->
            <div class="category-card">
                <h2 class="section-title">Omzet per Kategori</h2>
                <?php if (empty($categoryBreakdown)): ?>
                    <p style="color:#64748b; font-size:13px;">Belum ada data kategori.</p>
                <?php else: ?>
                    <?php foreach ($categoryBreakdown as $cat): ?>
                        <div class="cat-item">
                            <div>
                                <div class="cat-name"><?= htmlspecialchars($cat['name']) ?></div>
                                <div class="cat-count"><?= (int)$cat['booking_count'] ?> kali disewa</div>
                            </div>
                            <div class="cat-rev">Rp <?= number_format((float)$cat['total_revenue'], 0, ',', '.') ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Tabel Transaksi Terkini -->
            <div>
                <h2 class="section-title">Transaksi Terkini (Maks. 50 Data)</h2>
                <div class="table-card">
                    <table>
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Tanggal</th>
                                <th>Jenis</th>
                                <th>Pelanggan</th>
                                <th>Unit Kamera</th>
                                <th>Nominal</th>
                                <th>Escrow</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($transactions)): ?>
                                <tr>
                                    <td colspan="8" style="text-align:center; padding:32px; color:#64748b;">
                                        Tidak ada transaksi pada periode ini.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($transactions as $tx): ?>
                                    <tr>
                                        <td><strong><?= htmlspecialchars($tx['code']) ?></strong></td>
                                        <td><?= date('d/m/y H:i', strtotime($tx['tx_date'])) ?></td>
                                        <td>
                                            <span class="badge <?= $tx['tx_type'] === 'Sewa' ? 'badge-rental' : 'badge-sale' ?>">
                                                <?= htmlspecialchars($tx['tx_type']) ?>
                                            </span>
                                        </td>
                                        <td><?= htmlspecialchars($tx['customer_name']) ?></td>
                                        <td><?= htmlspecialchars($tx['item_name']) ?></td>
                                        <td><strong>Rp <?= number_format((float)$tx['amount'], 0, ',', '.') ?></strong></td>
                                        <td>
                                            <span class="badge badge-<?= htmlspecialchars($tx['escrow_status']) ?>">
                                                <?= strtoupper(htmlspecialchars($tx['escrow_status'])) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($tx['tx_type'] === 'Sewa'): ?>
                                                <a href="<?= base_url('/pesanan/sewa/' . $tx['id'] . '/cetak') ?>" target="_blank" style="color:#2563eb; font-weight:700; text-decoration:none; font-size:12px;">Cetak</a>
                                            <?php else: ?>
                                                <a href="<?= base_url('/pesanan/beli/' . $tx['id'] . '/cetak') ?>" target="_blank" style="color:#7c3aed; font-weight:700; text-decoration:none; font-size:12px;">Cetak</a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <script>
        function toggleCustomDates(val) {
            const el = document.getElementById('custom-dates');
            if (val === 'custom') {
                el.style.display = 'flex';
            } else {
                el.style.display = 'none';
            }
        }
    </script>
</body>
</html>


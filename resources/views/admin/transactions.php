<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Transaksi & Sengketa Escrow — Super Admin CamHub</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        <style>
        :root, html[data-theme="dark"] {
            --bg-page: #0b1320;
            --bg-surface: #112240;
            --bg-card: #132644;
            --bg-card-hover: #173056;
            --bg-subtle: rgba(255, 255, 255, 0.04);
            --bg-input: #0d1b30;
            --border-color: #1e3a5f;
            --border-subtle: #162c4a;
            --text-primary: #ffffff;
            --text-secondary: #94a3b8;
            --text-muted: #64748b;
            --accent-blue: #3b82f6;
            --accent-amber: #f59e0b;
            --table-th-bg: #0d1b30;
            --table-th-text: #94a3b8;
            --table-td-border: #1e3a5f;
            --table-td-text: #ffffff;
            --form-input-bg: #0d1b30;
            --form-input-border: #1e3a5f;
            --form-input-text: #ffffff;
            --stat-card-bg: #132644;
        }

        html[data-theme="light"] {
            --bg-page: #f8fafc;
            --bg-surface: #ffffff;
            --bg-card: #ffffff;
            --bg-card-hover: #f1f5f9;
            --bg-subtle: #f1f5f9;
            --bg-input: #ffffff;
            --border-color: #e2e8f0;
            --border-subtle: #cbd5e1;
            --text-primary: #0f172a;
            --text-secondary: #475569;
            --text-muted: #64748b;
            --accent-blue: #2563eb;
            --accent-amber: #d97706;
            --table-th-bg: #f8fafc;
            --table-th-text: #64748b;
            --table-td-border: #f1f5f9;
            --table-td-text: #1e293b;
            --form-input-bg: #ffffff;
            --form-input-border: #cbd5e1;
            --form-input-text: #0f172a;
            --stat-card-bg: #ffffff;
        }
        * { margin:0; padding:0; box-sizing:border-box; font-family:'Plus Jakarta Sans', sans-serif; }
        body { background:var(--bg-page); color:var(--text-primary); min-height:100vh; display:flex; }
        
        .admin-sidebar {
            width:260px;
            background:#0c192c;
            min-height:100vh; height:100vh; position:sticky; top:0; overflow-y:auto; padding:32px 24px 24px;
            display:flex;
            flex-direction:column;
            justify-content:space-between;
            flex-shrink:0;
        }
        .sidebar-brand { font-size:24px; font-weight:800; color:#ffffff; text-decoration:none; display:block; margin-bottom:18px; letter-spacing:-0.5px; }
        .sidebar-nav { list-style:none; display:flex; flex-direction:column; gap:22px; }
        .sidebar-nav a { color:#94a3b8; text-decoration:none; font-size:15px; font-weight:500; display:flex; align-items:center; transition:color 0.2s; }
        .sidebar-nav a:hover { color:#ffffff; }
        .sidebar-nav a.active { color:#ffffff; font-weight:700; }

        .sidebar-footer { margin-top:auto; padding-top:28px; border-top:1px solid #1e293b; }
        .admin-profile { display:flex; align-items:center; gap:10px; margin-bottom:14px; }
        .admin-avatar { width:34px; height:34px; border-radius:50%; background:#1e3a8a; color:#93c5fd; display:flex; align-items:center; justify-content:center; font-size:14px; font-weight:700; }
        .admin-meta { font-size:13px; }
        .admin-name { color:#ffffff; font-weight:700; }
        .admin-role { color:#38bdf8; font-size:11px; font-weight:600; }
        .btn-sidebar-logout { width:100%; background:#1e293b; color:#f87171; border:1px solid #334155; padding:9px 14px; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer; transition:all 0.2s; display:flex; align-items:center; justify-content:center; gap:6px; }
        .btn-sidebar-logout:hover { background:#dc2626; color:#ffffff; border-color:#dc2626; }

        .admin-main { flex:1; padding:40px 48px; max-width:1300px; }
        .page-header { margin-bottom:28px; }
        .page-title { font-size:28px; font-weight:800; color:var(--text-primary); margin-bottom:6px; }
        .page-subtitle { font-size:14px; color:var(--text-secondary); }

        .flash-alert { padding:14px 20px; border-radius:10px; font-size:14px; font-weight:600; margin-bottom:24px; }
        .flash-success { background:#ecfdf5; border:1px solid #a7f3d0; color:#065f46; }
        .flash-error { background:#fef2f2; border:1px solid #fecaca; color:#991b1b; }

        .section-card { background:var(--bg-card); border:1px solid var(--border-color); color:var(--text-primary); border-radius:16px; padding:28px; margin-bottom:32px; box-shadow:0 1px 3px rgba(0,0,0,0.02); }
        .section-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; }
        .section-title { font-size:18px; font-weight:800; color:var(--text-primary); }

        .table-custom { width:100%; border-collapse:collapse; font-size:13px; }
        .table-custom th { text-align:left; padding:12px 14px; background:var(--table-th-bg); color:var(--table-th-text); font-weight:600; border-bottom:1px solid var(--border-color); }
        .table-custom td { padding:14px; border-bottom:1px solid var(--table-td-border); color:var(--table-td-text); vertical-align:middle; }

        .badge { padding:4px 10px; border-radius:20px; font-size:11px; font-weight:700; text-transform:uppercase; display:inline-block; }
        .badge-held { background:#fef3c7; color:#b45309; }
        .badge-released { background:#dcfce7; color:#15803d; }
        .badge-disputed { background:#ede9fe; color:#6d28d9; }
        .badge-refunded { background:#fee2e2; color:#b91c1c; }
        .badge-pending { background:#fef3c7; color:#b45309; }
        .badge-processed { background:#dcfce7; color:#15803d; }
        .badge-rejected { background:#fee2e2; color:#b91c1c; }

        .btn-action-group { display:flex; gap:8px; align-items:center; }
        .btn-primary { background:#2563eb; color:#ffffff; font-size:12px; font-weight:700; padding:8px 14px; border:none; border-radius:6px; cursor:pointer; }
        .btn-primary:hover { background:#1d4ed8; }
        .btn-success { background:#16a34a; color:#ffffff; font-size:12px; font-weight:700; padding:8px 14px; border:none; border-radius:6px; cursor:pointer; }
        .btn-success:hover { background:#15803d; }
        .btn-danger { background:#dc2626; color:#ffffff; font-size:12px; font-weight:700; padding:8px 14px; border:none; border-radius:6px; cursor:pointer; }
        .btn-danger:hover { background:#b91c1c; }
    </style>
    <script>
        (function() {
            const theme = localStorage.getItem('camhub_theme') || 'dark';
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>
</head>
<body>

    <!-- Sidebar Admin -->
    <aside class="admin-sidebar">
        <div>
            <a href="<?= base_url('/dashboard/admin') ?>" class="sidebar-brand">CamHub</a>
            
            <div class="sidebar-user" style="padding-bottom:18px; margin-bottom:20px; border-bottom:1px solid #1e293b;">
                <div class="user-fullname" style="font-weight:700; font-size:15px; color:#ffffff; margin-bottom:3px;"><?= htmlspecialchars(auth()['full_name'] ?? 'Administrator', ENT_QUOTES, 'UTF-8') ?></div>
                <div class="user-role-label" style="font-size:12px; color:#38bdf8; font-weight:600; text-transform:uppercase; letter-spacing:0.5px;">Super Admin</div>
            </div>

            <ul class="sidebar-nav">
                <li><a href="<?= base_url('/dashboard/admin') ?>" class="<?= ($activeMenu ?? 'transaksi') === 'beranda' || ($activeMenu ?? 'transaksi') === 'dashboard' ? 'active' : '' ?>">Beranda</a></li>
                <li><a href="<?= base_url('/dashboard/admin/kyc') ?>" class="<?= ($activeMenu ?? 'transaksi') === 'kyc' ? 'active' : '' ?>">Verifikasi KYC</a></li>
                <li><a href="<?= base_url('/dashboard/admin/jadwal') ?>" class="<?= ($activeMenu ?? 'transaksi') === 'jadwal' ? 'active' : '' ?>">Jadwal Unit</a></li>
                <li><a href="<?= base_url('/dashboard/admin/transaksi') ?>" class="<?= ($activeMenu ?? 'transaksi') === 'transaksi' ? 'active' : '' ?>">Transaksi</a></li>
                <li><a href="<?= base_url('/dashboard/admin/denda') ?>" class="<?= ($activeMenu ?? 'transaksi') === 'denda' ? 'active' : '' ?>">Manajemen Denda</a></li>
                <li><a href="<?= base_url('/dashboard/admin/laporan') ?>" class="<?= ($activeMenu ?? 'transaksi') === 'laporan' ? 'active' : '' ?>">Laporan</a></li>
                <li><a href="<?= base_url('/dashboard/admin/pengaturan') ?>" class="<?= ($activeMenu ?? 'transaksi') === 'pengaturan' ? 'active' : '' ?>">Pengaturan</a></li>
            </ul>
        </div>

        <div class="sidebar-footer" style="margin-top:24px; padding-top:18px; border-top:1px solid #1e293b;">
            <button type="button" class="btn-theme-toggle" onclick="toggleCamHubTheme()" title="Beralih Mode Gelap/Terang" id="camhubThemeToggleBtn" style="width:100%; display:inline-flex; align-items:center; justify-content:center; gap:8px; background:#1e293b; color:#cbd5e1; border:1px solid #334155; padding:9px 12px; border-radius:8px; font-size:12px; font-weight:600; cursor:pointer; text-align:center; margin-bottom:10px;">
                <span class="theme-icon-wrap" id="themeIconWrap"></span>
                <span id="themeToggleText">Mode Terang</span>
            </button>
            <a href="<?= base_url('/') ?>" style="width:100%; display:inline-flex; align-items:center; justify-content:center; background:transparent; color:#94a3b8; border:1px solid #334155; padding:8px 12px; border-radius:8px; font-size:12px; font-weight:600; text-decoration:none; margin-bottom:10px; transition:all 0.2s;">
                Lihat Beranda Publik
            </a>
            <form action="<?= base_url('/logout') ?>" method="POST" style="margin:0;">
                <?= csrf_field() ?>
                <button type="submit" class="btn-sidebar-logout" style="width:100%; background:transparent; color:#ef4444; border:1px solid rgba(239,68,68,0.3); padding:8px 14px; border-radius:8px; font-size:13px; font-weight:700; cursor:pointer; transition:all 0.2s; display:flex; align-items:center; justify-content:center;">
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    <main class="admin-main">
        <div class="page-header">
            <h1 class="page-title">Pusat Transaksi & Rekening Bersama Escrow</h1>
            <p class="page-subtitle">Pantau seluruh perputaran dana transaksi, tangani sengketa komplain, dan verifikasi pencairan saldo toko mitra.</p>
        </div>

        <?php if (!empty($success)): ?>
            <div class="flash-alert flash-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        <?php if (!empty($error)): ?>
            <div class="flash-alert flash-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <!-- SECTION 1: MODERASI SENGKETA ESCROW (DISPUTED) -->
        <div class="section-card" style="border-left: 4px solid #6d28d9;">
            <div class="section-header">
                <div>
                    <h2 class="section-title">Moderasi Sengketa Transaksi (Disputed Escrows)</h2>
                    <p style="font-size:13px; color:#64748b;">Daftar transaksi yang dilaporkan bermasalah oleh pelanggan dan memerlukan putusan resmi Super Admin.</p>
                </div>
                <span class="badge badge-disputed"><?= count($disputes) ?> Menunggu Putusan</span>
            </div>

            <?php if (empty($disputes)): ?>
                <p style="padding:20px; text-align:center; color:#64748b; background:#f8fafc; border-radius:8px;">Saat ini tidak ada sengketa transaksi yang aktif. Seluruh transaksi berjalan kondusif.</p>
            <?php else: ?>
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>No. Referensi</th>
                            <th>Jenis Transaksi</th>
                            <th>Pelanggan</th>
                            <th>Toko Mitra</th>
                            <th>Nominal Sengketa</th>
                            <th style="text-align:right;">Putusan Super Admin</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($disputes as $disp): ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($disp['reference_code']) ?></strong>
                                    <div style="font-size:11px; color:#64748b;">Escrow ID #<?= $disp['escrow_id'] ?></div>
                                </td>
                                <td><?= htmlspecialchars($disp['transaction_type']) ?> · <?= htmlspecialchars($disp['product_name'] ?? 'Unit Kamera') ?></td>
                                <td>
                                    <strong><?= htmlspecialchars($disp['customer_name'] ?? 'Pelanggan') ?></strong>
                                    <div style="font-size:11px; color:#64748b;"><?= htmlspecialchars($disp['customer_phone'] ?? '-') ?></div>
                                </td>
                                <td><?= htmlspecialchars($disp['store_name'] ?? 'Mitra Lapak') ?></td>
                                <td><strong style="color:#6d28d9;">Rp <?= number_format((float)$disp['amount_held'], 0, ',', '.') ?></strong></td>
                                <td style="text-align:right;">
                                    <div class="btn-action-group" style="justify-content:flex-end;">
                                        <!-- Opsi Force Release ke Mitra -->
                                        <form action="<?= base_url('/dashboard/admin/transaksi/sengketa/' . $disp['escrow_id'] . '/resolusi') ?>" method="POST" style="margin:0;">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="resolution" value="release">
                                            <button type="submit" class="btn-success" onclick="return confirm('Apakah Anda yakin memutuskan dana dicairkan ke toko mitra?')">✓ Cairkan ke Mitra</button>
                                        </form>

                                        <!-- Opsi Force Refund ke Pelanggan -->
                                        <form action="<?= base_url('/dashboard/admin/transaksi/sengketa/' . $disp['escrow_id'] . '/resolusi') ?>" method="POST" style="margin:0;">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="resolution" value="refund">
                                            <button type="submit" class="btn-danger" onclick="return confirm('Apakah Anda yakin membatalkan transaksi dan mengembalikan (refund) dana ke pelanggan?')">↩ Refund ke Pelanggan</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <!-- SECTION 2: PERSETUJUAN PENCAIRAN SALDO MITRA -->
        <div class="section-card">
            <div class="section-header">
                <div>
                    <h2 class="section-title">Persetujuan Pencairan Saldo Mitra (Withdrawals)</h2>
                    <p style="font-size:13px; color:#64748b;">Pengajuan transfer saldo lapak mitra ke rekening bank terdaftar.</p>
                </div>
            </div>

            <?php if (empty($withdrawals)): ?>
                <p style="padding:20px; text-align:center; color:#64748b; background:#f8fafc; border-radius:8px;">Belum ada riwayat pengajuan penarikan dana dari toko mitra.</p>
            <?php else: ?>
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>ID & Waktu</th>
                            <th>Toko Mitra</th>
                            <th>Rekening Tujuan</th>
                            <th>Nominal Penarikan</th>
                            <th>Status</th>
                            <th style="text-align:right;">Aksi Persetujuan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($withdrawals as $wd): ?>
                            <tr>
                                <td>
                                    <strong>#WD-<?= str_pad((string)$wd['id'], 4, '0', STR_PAD_LEFT) ?></strong>
                                    <div style="font-size:11px; color:#64748b;"><?= date('d M Y, H:i', strtotime($wd['created_at'])) ?></div>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($wd['store_name'] ?? 'Mitra Lapak') ?></strong>
                                    <div style="font-size:11px; color:#64748b;"><?= htmlspecialchars($wd['partner_name']) ?></div>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($wd['bank_name']) ?></strong>
                                    <div><?= htmlspecialchars($wd['account_number']) ?> a/n <?= htmlspecialchars($wd['account_holder']) ?></div>
                                </td>
                                <td><strong>Rp <?= number_format((float)$wd['amount'], 0, ',', '.') ?></strong></td>
                                <td>
                                    <span class="badge badge-<?= htmlspecialchars($wd['status']) ?>">
                                        <?= htmlspecialchars(strtoupper($wd['status'])) ?>
                                    </span>
                                </td>
                                <td style="text-align:right;">
                                    <?php if ($wd['status'] === 'pending'): ?>
                                        <div class="btn-action-group" style="justify-content:flex-end;">
                                            <form action="<?= base_url('/dashboard/admin/transaksi/pencairan/' . $wd['id'] . '/proses') ?>" method="POST" style="margin:0;">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="action" value="processed">
                                                <button type="submit" class="btn-success" onclick="return confirm('Setujui dan tandai transfer pencairan dana ini telah diproses?')">✓ Setujui Transfer</button>
                                            </form>
                                            <form action="<?= base_url('/dashboard/admin/transaksi/pencairan/' . $wd['id'] . '/proses') ?>" method="POST" style="margin:0;">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="action" value="rejected">
                                                <button type="submit" class="btn-danger" onclick="return confirm('Tolak pengajuan pencairan dana mitra ini?')">✕ Tolak</button>
                                            </form>
                                        </div>
                                    <?php else: ?>
                                        <span style="font-size:12px; color:#64748b; font-weight:600;">Selesai</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <!-- SECTION 3: DAFTAR TRANSAKSI SELURUH PLATFORM -->
        <div class="section-card">
            <div class="section-header">
                <div>
                    <h2 class="section-title">Seluruh Transaksi Masuk Platform</h2>
                    <p style="font-size:13px; color:#64748b;">Agregasi transaksi sewa kamera dan transaksi jual beli marketplace.</p>
                </div>
            </div>

            <table class="table-custom">
                <thead>
                    <tr>
                        <th>No. Referensi</th>
                        <th>Kategori</th>
                        <th>Kamera / Produk</th>
                        <th>Pelanggan</th>
                        <th>Toko Mitra</th>
                        <th>Total Bayar</th>
                        <th>Status Order</th>
                        <th>Status Escrow</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transactions as $tx): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($tx['reference_code']) ?></strong></td>
                            <td><?= htmlspecialchars($tx['trx_type']) ?></td>
                            <td><?= htmlspecialchars($tx['item_name']) ?></td>
                            <td><?= htmlspecialchars($tx['customer_name']) ?></td>
                            <td><?= htmlspecialchars($tx['store_name']) ?></td>
                            <td>Rp <?= number_format((float)$tx['total_amount'], 0, ',', '.') ?></td>
                            <td><span style="font-weight:700; font-size:11px; text-transform:uppercase;"><?= htmlspecialchars($tx['order_status']) ?></span></td>
                            <td>
                                <?php if (!empty($tx['escrow_status'])): ?>
                                    <span class="badge badge-<?= htmlspecialchars($tx['escrow_status']) ?>">
                                        <?= htmlspecialchars(strtoupper($tx['escrow_status'])) ?>
                                    </span>
                                <?php else: ?>
                                    <span style="color:#64748b;">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>

    <script>
        const SUN_ICON = `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>`;

        const MOON_ICON = `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>`;

        function toggleCamHubTheme() {
            const currentTheme = document.documentElement.getAttribute('data-theme') || 'dark';
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('camhub_theme', newTheme);
            syncThemeToggleUI(newTheme);
        }

        function syncThemeToggleUI(theme) {
            const text = document.getElementById('themeToggleText');
            const iconWrap = document.getElementById('themeIconWrap');
            if (!text) return;
            if (theme === 'dark') {
                text.textContent = 'Mode Terang';
                if (iconWrap) iconWrap.innerHTML = SUN_ICON;
            } else {
                text.textContent = 'Mode Gelap';
                if (iconWrap) iconWrap.innerHTML = MOON_ICON;
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const active = document.documentElement.getAttribute('data-theme') || 'dark';
            syncThemeToggleUI(active);
        });
    </script>
</body>
</html>


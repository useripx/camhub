<?php
$user = $user ?? auth();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Verifikasi Identitas KYC — CamHub', ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        (function() {
            const theme = localStorage.getItem('camhub_theme') || 'dark';
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>
    <style>
        :root {
            --bg-page: #0b1320;
            --bg-surface: #112240;
            --bg-card: #132644;
            --bg-subtle: rgba(255, 255, 255, 0.04);
            --bg-input: #0d1b30;
            --border-color: #1e3a5f;
            --border-subtle: #162c4a;
            --text-primary: #ffffff;
            --text-secondary: #94a3b8;
            --text-muted: #64748b;
            --accent-blue: #3b82f6;
            --accent-amber: #f59e0b;
        }

        html[data-theme="dark"], html:not([data-theme="light"]) {
            --bg-page: #0b1320;
            --bg-surface: #112240;
            --bg-card: #132644;
            --bg-subtle: rgba(255, 255, 255, 0.04);
            --bg-input: #0d1b30;
            --border-color: #1e3a5f;
            --border-subtle: #162c4a;
            --text-primary: #ffffff;
            --text-secondary: #94a3b8;
            --text-muted: #64748b;
            --accent-blue: #3b82f6;
            --accent-amber: #f59e0b;
        }

        html[data-theme="light"] {
            --bg-page: #f8fafc;
            --bg-surface: #ffffff;
            --bg-card: #ffffff;
            --bg-subtle: #f1f5f9;
            --bg-input: #ffffff;
            --border-color: #e2e8f0;
            --border-subtle: #f1f5f9;
            --text-primary: #0f172a;
            --text-secondary: #475569;
            --text-muted: #94a3b8;
            --accent-blue: #2563eb;
            --accent-amber: #d97706;
        }

        * { margin:0; padding:0; box-sizing:border-box; font-family:'Plus Jakarta Sans', system-ui, sans-serif; }
        body { background:var(--bg-page); color:var(--text-primary); min-height:100vh; display:flex; line-height:1.5; }

        /* SIDEBAR (CLEAN TEXT-ONLY, NO ICONS) */
        .sidebar {
            width:260px;
            background:#0c192c;
            color:#ffffff;
            padding:32px 24px 24px;
            display:flex;
            flex-direction:column;
            flex-shrink:0;
            height:100vh;
            position:sticky;
            top:0;
            overflow-y:auto;
        }
        .sidebar-brand {
            font-size:24px;
            font-weight:800;
            color:#ffffff;
            text-decoration:none;
            margin-bottom:24px;
            display:block;
            letter-spacing:-0.5px;
        }
        .sidebar-user {
            margin-bottom:24px;
        }
        .user-fullname {
            font-size:16px;
            font-weight:700;
            color:#ffffff;
            line-height:1.3;
            margin-bottom:4px;
        }
        .user-role-label {
            font-size:12px;
            color:#94a3b8;
            font-weight:500;
        }
        .nav-menu {
            list-style:none;
            display:flex;
            flex-direction:column;
            gap:4px;
        }
        .nav-item a {
            display:block;
            padding:10px 14px;
            border-radius:8px;
            font-size:13px;
            font-weight:600;
            color:#94a3b8;
            text-decoration:none;
            transition:all 0.15s ease;
        }
        .nav-item a:hover {
            background:#16243a;
            color:#ffffff;
        }
        .nav-item.active a {
            background:#1e293b;
            color:#ffffff;
            font-weight:700;
            border-left:3px solid #3b82f6;
        }
        
        .sidebar-bottom {
            margin-top:16px;
            padding-top:16px;
            border-top:1px solid #1e3a5f;
            display:flex;
            flex-direction:column;
            gap:8px;
        }
        .btn-theme-toggle {
            width:100%;
            background:#16243a;
            color:#cbd5e1;
            border:1px solid #1e3a5f;
            padding:10px 14px;
            border-radius:8px;
            font-size:13px;
            font-weight:600;
            cursor:pointer;
            text-align:center;
            transition:all 0.2s;
        }
        .btn-theme-toggle:hover {
            background:#1e293b;
            color:#ffffff;
            border-color:#3b82f6;
        }
        .btn-sidebar-link {
            display:block;
            padding:10px 14px;
            color:#94a3b8;
            font-size:13px;
            font-weight:600;
            text-decoration:none;
            border-radius:8px;
            transition:0.15s;
        }
        .btn-sidebar-link:hover {
            background:#16243a;
            color:#ffffff;
        }
        .btn-sidebar-logout {
            width:100%;
            background:transparent;
            border:none;
            cursor:pointer;
            padding:10px 14px;
            color:#ef4444;
            font-size:13px;
            font-weight:600;
            border-radius:8px;
            transition:0.15s;
            text-align:left;
        }
        .btn-sidebar-logout:hover {
            background:#1e293b;
            color:#f87171;
        }

        /* MAIN CONTENT */
        .main-content {
            flex:1;
            padding:40px 48px 60px;
            overflow-y:auto;
            max-width:960px;
        }
        .breadcrumb {
            display:flex;
            align-items:center;
            gap:8px;
            font-size:13px;
            color:var(--text-muted);
            margin-bottom:20px;
        }
        .breadcrumb a {
            color:var(--accent-blue);
            text-decoration:none;
            font-weight:600;
        }
        .breadcrumb a:hover {
            text-decoration:underline;
        }

        .page-header { margin-bottom:28px; text-align:center; }
        .page-title {
            font-size:28px;
            font-weight:800;
            color:var(--text-primary);
            margin-bottom:8px;
            letter-spacing:-0.5px;
        }
        .page-desc {
            color:var(--text-secondary);
            font-size:14px;
            max-width:620px;
            margin:0 auto;
            line-height:1.6;
        }

        /* STEPPER INDICATOR */
        .stepper-container { display:flex; justify-content:center; align-items:center; margin-bottom:32px; gap:24px; }
        .step-item { display:flex; align-items:center; gap:8px; opacity:0.6; font-size:13px; font-weight:600; color:var(--text-secondary); }
        .step-item.active { opacity:1; color:var(--accent-blue); }
        .step-circle {
            width:30px;
            height:30px;
            border-radius:50%;
            background:var(--bg-subtle);
            border:2px solid var(--border-color);
            display:flex;
            align-items:center;
            justify-content:center;
            font-weight:700;
            color:var(--text-primary);
        }
        .step-item.active .step-circle { border-color:var(--accent-blue); background:rgba(37, 99, 235, 0.15); color:var(--accent-blue); }
        .step-line { width:36px; height:2px; background:var(--border-color); }

        /* CARD FORM */
        .kyc-card {
            background:var(--bg-card);
            border:1px solid var(--border-color);
            border-radius:16px;
            padding:36px;
            box-shadow:0 4px 20px rgba(0,0,0,0.06);
            margin-bottom:30px;
        }
        .section-title {
            font-size:17px;
            font-weight:700;
            margin-bottom:18px;
            color:var(--text-primary);
            display:flex;
            align-items:center;
            gap:10px;
            border-bottom:1px solid var(--border-subtle);
            padding-bottom:10px;
        }

        /* PANDUAN BOX */
        .guide-box {
            background:rgba(245, 158, 11, 0.08);
            border:1px dashed rgba(245, 158, 11, 0.35);
            border-radius:12px;
            padding:18px;
            margin-bottom:24px;
        }
        .guide-title {
            font-size:14px;
            font-weight:700;
            color:var(--accent-amber);
            margin-bottom:8px;
        }
        .guide-list {
            list-style:none;
            font-size:13px;
            color:var(--text-secondary);
            display:grid;
            grid-template-columns:1fr 1fr;
            gap:8px;
        }
        .guide-list li { display:flex; align-items:center; gap:8px; }
        .guide-list li::before { content:"•"; color:var(--accent-amber); font-weight:bold; }

        /* FORM ELEMENTS */
        .form-grid { display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:20px; }
        .form-group { margin-bottom:20px; }
        .form-label { display:block; font-size:13px; font-weight:600; color:var(--text-primary); margin-bottom:8px; }
        .form-control {
            width:100%;
            height:46px;
            padding:12px 16px;
            font-size:14px;
            border:1px solid var(--border-color);
            border-radius:10px;
            background:var(--bg-input);
            color:var(--text-primary);
            transition:0.2s;
        }
        .form-control:focus { outline:none; border-color:var(--accent-blue); box-shadow:0 0 0 3px rgba(37,99,235,0.15); }
        .form-control.is-invalid { border-color:#ef4444; }
        .field-error { color:#f87171; font-size:12px; font-weight:500; margin-top:6px; }

        /* UPLOAD DROPZONES */
        .upload-grid { display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:24px; }
        .upload-card {
            border:2px dashed var(--border-color);
            border-radius:12px;
            padding:24px 16px;
            text-align:center;
            background:var(--bg-subtle);
            cursor:pointer;
            transition:0.2s;
            position:relative;
        }
        .upload-card:hover { border-color:var(--accent-blue); background:rgba(37, 99, 235, 0.05); }
        .upload-title { font-size:14px; font-weight:700; color:var(--text-primary); margin-bottom:4px; }
        .upload-hint { font-size:12px; color:var(--text-muted); }
        .upload-input { position:absolute; top:0; left:0; width:100%; height:100%; opacity:0; cursor:pointer; }
        .file-chosen-name { font-size:12px; color:var(--accent-blue); margin-top:8px; font-weight:600; word-break:break-all; }

        /* AGREEMENT CHECKBOX */
        .agreement-box {
            background:var(--bg-subtle);
            border:1px solid var(--border-subtle);
            border-radius:10px;
            padding:16px;
            margin-bottom:28px;
            display:flex;
            align-items:flex-start;
            gap:12px;
        }
        .agreement-box input[type="checkbox"] { width:18px; height:18px; accent-color:#2563eb; cursor:pointer; margin-top:3px; }
        .agreement-text { font-size:13px; color:var(--text-secondary); line-height:1.5; cursor:pointer; }

        .btn-submit {
            width:100%;
            height:50px;
            background:#2563eb;
            color:#ffffff;
            border:none;
            border-radius:10px;
            font-size:15px;
            font-weight:700;
            cursor:pointer;
            transition:0.2s;
        }
        .btn-submit:hover { background:#1d4ed8; }

        .alert-danger {
            background:rgba(239, 68, 68, 0.15);
            border:1px solid rgba(239, 68, 68, 0.3);
            color:#f87171;
            padding:14px 16px;
            border-radius:10px;
            margin-bottom:24px;
            font-size:14px;
        }

        @media (max-width: 768px) {
            .form-grid, .upload-grid { grid-template-columns:1fr; }
            body { flex-direction:column; }
            .sidebar { width:100%; min-height:auto; }
        }
    </style>
</head>
<body>

    <!-- SIDEBAR KIRI VERTIKAL (CLEAN TEXT-ONLY, NO ICONS) -->
    <aside class="sidebar">
        <a href="<?= base_url() ?>" class="sidebar-brand">CamHub</a>
        
        <div class="sidebar-user">
            <div class="user-fullname"><?= htmlspecialchars($user['full_name'] ?? 'Pelanggan', ENT_QUOTES, 'UTF-8') ?></div>
            <div class="user-role-label">Pelanggan</div>
        </div>

        <ul class="nav-menu">
            <li class="nav-item">
                <a href="<?= base_url('/dashboard/pelanggan') ?>">Beranda</a>
            </li>
            <li class="nav-item">
                <a href="<?= base_url('/pesanan-saya') ?>">Pesanan Saya</a>
            </li>
            <li class="nav-item">
                <a href="<?= base_url('/pesanan-saya?type=rental') ?>">Sewa Saya</a>
            </li>
            <li class="nav-item">
                <a href="<?= base_url('/pesanan-saya?type=sale') ?>">Jual Beli Saya</a>
            </li>
            <li class="nav-item">
                <a href="<?= base_url('/titip-jual/saya') ?>">Titip Jual Saya</a>
            </li>
            <li class="nav-item active">
                <a href="<?= base_url('/kyc/status') ?>">Dokumen KYC</a>
            </li>
            <li class="nav-item">
                <a href="<?= base_url('/profil') ?>">Pengaturan</a>
            </li>
        </ul>

        <div class="sidebar-bottom">
            <button type="button" class="btn-theme-toggle" onclick="toggleCamHubTheme()" title="Beralih Mode Gelap/Terang">
                <span id="themeToggleText">Mode Terang</span>
            </button>

            <a href="<?= base_url('/') ?>" class="btn-sidebar-link">
                Lihat Beranda Publik
            </a>

            <form action="<?= base_url('/logout') ?>" method="POST" style="margin:0;">
                <?= csrf_field() ?>
                <button type="submit" class="btn-sidebar-logout">
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    <!-- KONTEN UTAMA -->
    <main class="main-content">
        <div class="breadcrumb">
            <a href="<?= base_url('/dashboard/pelanggan') ?>">Dashboard</a>
            <span>/</span>
            <span>Verifikasi Identitas Resmi (KYC)</span>
        </div>

        <div class="page-header">
            <h1 class="page-title">Verifikasi Identitas Resmi (KYC)</h1>
            <p class="page-desc">
                Sesuai regulasi platform CamHub, setiap transaksi penyewaan kamera wajib melalui verifikasi KTP untuk menjamin keamanan unit dan mengaktifkan proteksi rekening bersama (escrow).
            </p>
        </div>

        <!-- STEPPER -->
        <div class="stepper-container">
            <div class="step-item active">
                <div class="step-circle">1</div>
                <span>Data Identitas KTP</span>
            </div>
            <div class="step-line"></div>
            <div class="step-item active">
                <div class="step-circle">2</div>
                <span>Unggah Berkas</span>
            </div>
            <div class="step-line"></div>
            <div class="step-item active">
                <div class="step-circle">3</div>
                <span>Pernyataan & Kirim</span>
            </div>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert-danger"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <div class="kyc-card">
            <!-- PANDUAN FOTO KTP -->
            <div class="guide-box">
                <div class="guide-title">
                    Panduan Mengambil Foto KTP yang Benar
                </div>
                <ul class="guide-list">
                    <li>Gunakan KTP asli (bukan fotokopi buram).</li>
                    <li>Seluruh 4 sudut KTP terlihat jelas dalam frame.</li>
                    <li>Teks NIK, nama, dan foto wajah terbaca tajam.</li>
                    <li>Hindari pantulan lampu flash atau bayangan gelap.</li>
                </ul>
            </div>

            <form action="<?= base_url('/kyc/submit') ?>" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>

                <!-- BAGIAN 1: DATA IDENTITAS -->
                <div class="section-title">
                    01. Data Identitas Diri (Sesuai KTP)
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label" for="full_name">Nama Lengkap (Sesuai KTP) *</label>
                        <input type="text" id="full_name" name="full_name" class="form-control <?= isset($errors['full_name']) ? 'is-invalid' : '' ?>" placeholder="Contoh: Yogi Ario Pratama" value="<?= htmlspecialchars(old('full_name', $user['full_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required>
                        <?php if (isset($errors['full_name'])): ?>
                            <div class="field-error"><?= htmlspecialchars($errors['full_name'], ENT_QUOTES, 'UTF-8') ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="nik_ktp">Nomor NIK KTP (16 Digit) *</label>
                        <input type="text" id="nik_ktp" name="nik_ktp" maxlength="16" pattern="[0-9]{16}" class="form-control <?= isset($errors['nik_ktp']) ? 'is-invalid' : '' ?>" placeholder="3271xxxxxxxxxxxx" value="<?= htmlspecialchars(old('nik_ktp'), ENT_QUOTES, 'UTF-8') ?>" required>
                        <?php if (isset($errors['nik_ktp'])): ?>
                            <div class="field-error"><?= htmlspecialchars($errors['nik_ktp'], ENT_QUOTES, 'UTF-8') ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label" for="birth_date">Tanggal Lahir (Minimal 17 Tahun) *</label>
                        <input type="date" id="birth_date" name="birth_date" class="form-control <?= isset($errors['birth_date']) ? 'is-invalid' : '' ?>" value="<?= htmlspecialchars(old('birth_date'), ENT_QUOTES, 'UTF-8') ?>" required>
                        <?php if (isset($errors['birth_date'])): ?>
                            <div class="field-error"><?= htmlspecialchars($errors['birth_date'], ENT_QUOTES, 'UTF-8') ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="gender">Jenis Kelamin *</label>
                        <select id="gender" name="gender" class="form-control <?= isset($errors['gender']) ? 'is-invalid' : '' ?>" required>
                            <option value="">-- Pilih Jenis Kelamin --</option>
                            <option value="Laki-laki" <?= old('gender') === 'Laki-laki' ? 'selected' : '' ?>>Laki-laki</option>
                            <option value="Perempuan" <?= old('gender') === 'Perempuan' ? 'selected' : '' ?>>Perempuan</option>
                        </select>
                        <?php if (isset($errors['gender'])): ?>
                            <div class="field-error"><?= htmlspecialchars($errors['gender'], ENT_QUOTES, 'UTF-8') ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- BAGIAN 2: UNGGAH DOKUMEN -->
                <div class="section-title" style="margin-top: 10px;">
                    02. Unggah Berkas Dokumen (Format: JPG, PNG, WEBP — Maks. 2MB)
                </div>

                <div class="upload-grid">
                    <!-- DROPZONE KTP -->
                    <div>
                        <label class="form-label">Foto KTP Asli *</label>
                        <div class="upload-card">
                            <div class="upload-title">Pilih atau Seret Foto KTP</div>
                            <div class="upload-hint">Pastikan tulisan NIK dan wajah jelas</div>
                            <input type="file" id="ktp_file" name="ktp_file" accept="image/jpeg,image/png,image/webp" class="upload-input" onchange="previewFileName(this, 'name-ktp')" required>
                            <div id="name-ktp" class="file-chosen-name"></div>
                        </div>
                        <?php if (isset($errors['ktp_file'])): ?>
                            <div class="field-error"><?= htmlspecialchars($errors['ktp_file'], ENT_QUOTES, 'UTF-8') ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- DROPZONE SELFIE -->
                    <div>
                        <label class="form-label">Swafoto Memegang KTP (Selfie) *</label>
                        <div class="upload-card">
                            <div class="upload-title">Pilih atau Seret Foto Selfie</div>
                            <div class="upload-hint">Pegang KTP di samping wajah dengan tegak</div>
                            <input type="file" id="selfie_file" name="selfie_file" accept="image/jpeg,image/png,image/webp" class="upload-input" onchange="previewFileName(this, 'name-selfie')" required>
                            <div id="name-selfie" class="file-chosen-name"></div>
                        </div>
                        <?php if (isset($errors['selfie_file'])): ?>
                            <div class="field-error"><?= htmlspecialchars($errors['selfie_file'], ENT_QUOTES, 'UTF-8') ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- BAGIAN 3: PERNYATAAN & SUBMIT -->
                <div class="agreement-box">
                    <input type="checkbox" id="agreement" name="agreement" value="1" required>
                    <label for="agreement" class="agreement-text">
                        Saya menyatakan dengan sesungguhnya bahwa seluruh data dan dokumen KTP yang saya lampirkan adalah <strong>asli, benar, sah, dan milik saya pribadi</strong>. Saya memahami bahwa pemalsuan data identitas merupakan tindak pidana dan akan diproses hukum serta diblokir permanen dari ekosistem CamHub.
                    </label>
                </div>
                <?php if (isset($errors['agreement'])): ?>
                    <div class="field-error" style="margin-top: -20px; margin-bottom: 20px;"><?= htmlspecialchars($errors['agreement'], ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>

                <button type="submit" class="btn-submit">
                    Kirim Pengajuan Verifikasi KYC
                </button>
            </form>
        </div>
    </main>

    <script>
        function previewFileName(input, targetId) {
            if (input.files && input.files[0]) {
                const file = input.files[0];
                const sizeMb = (file.size / (1024 * 1024)).toFixed(2);
                document.getElementById(targetId).textContent = file.name + ' (' + sizeMb + ' MB)';
            }
        }

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

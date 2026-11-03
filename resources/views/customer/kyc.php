<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Verifikasi Identitas KYC — CamHub', ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-dark: #0b192e;
            --surface-dark: #112240;
            --card-dark: #132644;
            --accent-amber: #f59e0b;
            --accent-amber-hover: #d97706;
            --text-light: #ffffff;
            --text-muted: #94a3b8;
            --border-navy: #1e3a5f;
            --danger-bg: #450a0a;
            --danger-text: #fca5a5;
            --danger-border: #7f1d1d;
            --success-bg: #052e16;
            --success-text: #86efac;
            --success-border: #14532d;
        }

        * { margin:0; padding:0; box-sizing:border-box; font-family:'Plus Jakarta Sans', system-ui, sans-serif; }
        body { background-color: var(--bg-dark); color: var(--text-light); min-height: 100vh; line-height: 1.5; }

        header { background: var(--surface-dark); padding: 16px 40px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid var(--border-navy); flex-wrap: wrap; gap: 16px; }
        .logo { font-size: 22px; font-weight: 800; color: #fff; text-decoration: none; display: flex; align-items: center; gap: 8px; }
        .logo span { color: var(--accent-amber); }
        .nav-links-dash { display: flex; align-items: center; gap: 20px; list-style: none; }
        .nav-links-dash a { color: var(--text-muted); text-decoration: none; font-size: 14px; font-weight: 600; transition: 0.2s; }
        .nav-links-dash a:hover { color: var(--accent-amber); }
        .user-nav { display: flex; align-items: center; gap: 12px; }
        .role-badge { background: #2563eb; color: #fff; font-size: 11px; font-weight: 700; padding: 4px 10px; border-radius: 20px; text-transform: uppercase; }

        .container { max-width: 900px; margin: 36px auto; padding: 0 20px; }

        .breadcrumb { display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--text-muted); margin-bottom: 24px; }
        .breadcrumb a { color: var(--accent-amber); text-decoration: none; }

        .page-header { margin-bottom: 30px; text-align: center; }
        .page-title { font-size: 30px; font-weight: 800; margin-bottom: 10px; letter-spacing: -0.5px; }
        .page-title span { color: var(--accent-amber); }
        .page-desc { color: var(--text-muted); font-size: 15px; max-width: 600px; margin: 0 auto; }

        /* STEPPER INDICATOR */
        .stepper-container { display: flex; justify-content: center; align-items: center; margin-bottom: 36px; gap: 30px; }
        .step-item { display: flex; align-items: center; gap: 10px; opacity: 0.5; font-size: 14px; font-weight: 600; }
        .step-item.active { opacity: 1; color: var(--accent-amber); }
        .step-circle { width: 32px; height: 32px; border-radius: 50%; background: var(--surface-dark); border: 2px solid var(--border-navy); display: flex; align-items: center; justify-content: center; font-weight: 700; }
        .step-item.active .step-circle { border-color: var(--accent-amber); background: rgba(245, 158, 11, 0.15); color: var(--accent-amber); }
        .step-line { width: 40px; height: 2px; background: var(--border-navy); }

        /* CARD FORM */
        .kyc-card { background: var(--card-dark); border: 1px solid var(--border-navy); border-radius: 16px; padding: 36px; box-shadow: 0 10px 30px rgba(0,0,0,0.3); margin-bottom: 30px; }
        .section-title { font-size: 18px; font-weight: 700; margin-bottom: 18px; color: #ffffff; display: flex; align-items: center; gap: 10px; border-bottom: 1px solid var(--border-navy); padding-bottom: 10px; }
        .section-title span { color: var(--accent-amber); }

        /* PHOTO GUIDELINE BOX */
        .guide-box { background: rgba(245, 158, 11, 0.08); border: 1px dashed rgba(245, 158, 11, 0.35); border-radius: 12px; padding: 18px; margin-bottom: 24px; }
        .guide-title { font-size: 14px; font-weight: 700; color: var(--accent-amber); margin-bottom: 8px; display: flex; align-items: center; gap: 6px; }
        .guide-list { list-style: none; font-size: 13px; color: #cbd5e1; display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
        .guide-list li { display: flex; align-items: center; gap: 8px; }
        .guide-list li::before { content: "✓"; color: #10b981; font-weight: bold; }

        /* FORM ELEMENTS */
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
        .form-group { margin-bottom: 20px; }
        .form-label { display: block; font-size: 13px; font-weight: 600; color: #cbd5e1; margin-bottom: 8px; }
        .form-control { width: 100%; height: 48px; padding: 12px 16px; font-size: 14px; border: 1.5px solid var(--border-navy); border-radius: 10px; background: var(--surface-dark); color: #ffffff; transition: 0.2s; }
        .form-control:focus { outline: none; border-color: var(--accent-amber); box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.15); }
        .form-control.is-invalid { border-color: #ef4444; background: #281216; }
        .field-error { color: #f87171; font-size: 12px; font-weight: 500; margin-top: 6px; }

        /* UPLOAD DROPZONES */
        .upload-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 24px; }
        .upload-card { border: 2px dashed var(--border-navy); border-radius: 12px; padding: 24px 16px; text-align: center; background: rgba(17, 34, 64, 0.5); cursor: pointer; transition: 0.2s; position: relative; }
        .upload-card:hover { border-color: var(--accent-amber); background: rgba(245, 158, 11, 0.05); }
        .upload-icon { font-size: 32px; margin-bottom: 8px; }
        .upload-title { font-size: 14px; font-weight: 700; color: #fff; margin-bottom: 4px; }
        .upload-hint { font-size: 12px; color: var(--text-muted); }
        .upload-input { position: absolute; top:0; left:0; width:100%; height:100%; opacity:0; cursor:pointer; }
        .file-chosen-name { font-size: 12px; color: var(--accent-amber); margin-top: 8px; font-weight: 600; word-break: break-all; }

        /* AGREEMENT CHECKBOX */
        .agreement-box { background: rgba(17, 34, 64, 0.7); border: 1px solid var(--border-navy); border-radius: 10px; padding: 16px; margin-bottom: 28px; display: flex; align-items: flex-start; gap: 12px; }
        .agreement-box input[type="checkbox"] { width: 18px; height: 18px; accent-color: var(--accent-amber); cursor: pointer; margin-top: 3px; }
        .agreement-text { font-size: 13px; color: #cbd5e1; line-height: 1.5; cursor: pointer; }

        .btn-submit { width: 100%; height: 52px; background: var(--accent-amber); color: #0b192e; border: none; border-radius: 10px; font-size: 16px; font-weight: 800; cursor: pointer; transition: 0.2s; box-shadow: 0 4px 14px rgba(245, 158, 11, 0.35); display: flex; align-items: center; justify-content: center; gap: 8px; }
        .btn-submit:hover { background: var(--accent-amber-hover); color: #fff; transform: translateY(-1px); }

        .alert-danger { background: var(--danger-bg); border: 1px solid var(--danger-border); color: var(--danger-text); padding: 14px 16px; border-radius: 10px; margin-bottom: 24px; font-size: 14px; }

        @media (max-width: 768px) {
            header { padding: 16px 20px; }
            .form-grid, .upload-grid, .guide-list { grid-template-columns: 1fr; }
            .stepper-container { display: none; }
            .kyc-card { padding: 24px 18px; }
        }
    </style>
</head>
<body>

<header>
    <a href="<?= base_url() ?>" class="logo">Cam<span>Hub</span></a>
    <ul class="nav-links-dash">
        <li><a href="<?= base_url() ?>">← Beranda Publik</a></li>
        <li><a href="<?= base_url('/dashboard/pelanggan') ?>">Dashboard</a></li>
        <li><a href="<?= base_url('/profil') ?>">Profil Saya</a></li>
        <li><a href="<?= base_url('/kyc/status') ?>">Status KYC</a></li>
    </ul>
    <div class="user-nav">
        <span>👤 <?= htmlspecialchars($user['full_name'] ?? 'Pelanggan', ENT_QUOTES, 'UTF-8') ?></span>
        <span class="role-badge">Pelanggan</span>
    </div>
</header>

<main class="container">
    <div class="breadcrumb">
        <a href="<?= base_url('/dashboard/pelanggan') ?>">Dashboard</a>
        <span>/</span>
        <span>Verifikasi Identitas Resmi (KYC)</span>
    </div>

    <div class="page-header">
        <h1 class="page-title">Verifikasi Identitas <span>Resmi (KYC)</span></h1>
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
        <!-- PANDUAN FOTO KTP (MOCKUP ACCORDANCE) -->
        <div class="guide-box">
            <div class="guide-title">
                <span>📷</span> Panduan Mengambil Foto KTP yang Benar
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
                <span>01.</span> Data Identitas Diri (Sesuai KTP)
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
                <span>02.</span> Unggah Berkas Dokumen (Format: JPG, PNG, WEBP — Maks. 2MB)
            </div>

            <div class="upload-grid">
                <!-- DROPZONE KTP -->
                <div>
                    <label class="form-label">Foto KTP Asli *</label>
                    <div class="upload-card">
                        <div class="upload-icon">🪪</div>
                        <div class="upload-title">Klik atau Seret Foto KTP</div>
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
                        <div class="upload-icon">🤳</div>
                        <div class="upload-title">Klik atau Seret Foto Selfie</div>
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
                <span>Kirim Pengajuan Verifikasi KYC</span>
                <svg style="width:20px;height:20px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
            </button>
        </form>
    </div>
</main>

<script>
    function previewFileName(input, targetId) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            const sizeMb = (file.size / (1024 * 1024)).toFixed(2);
            document.getElementById(targetId).textContent = '✓ ' + file.name + ' (' + sizeMb + ' MB)';
        }
    }
</script>

</body>
</html>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Masuk ke CamHub', ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-navy: #0b192e;
            --primary-navy-card: #13243d;
            --accent-amber: #d97706;
            --accent-amber-hover: #b45309;
            --accent-amber-light: #fef3c7;
            --text-dark: #0f172a;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --bg-light: #f8fafc;
            --danger-bg: #fef2f2;
            --danger-text: #991b1b;
            --danger-border: #fecaca;
            --success-bg: #f0fdf4;
            --success-text: #166534;
            --success-border: #bbf7d0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
        }

        body {
            background-color: var(--bg-light);
            min-height: 100vh;
            display: flex;
            align-items: stretch;
            color: var(--text-dark);
        }

        .auth-container {
            display: flex;
            width: 100%;
            min-height: 100vh;
        }

        /* LEFT SIDEBAR (Dark Navy Brand Panel) */
        .auth-brand-panel {
            flex: 1.1;
            background: linear-gradient(145deg, #07101e 0%, #0b192e 60%, #152c4e 100%);
            color: #ffffff;
            padding: 60px 80px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }

        .auth-brand-panel::before {
            content: '';
            position: absolute;
            top: -100px;
            right: -100px;
            width: 400px;
            height: 400px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(217, 119, 6, 0.15) 0%, rgba(11, 25, 46, 0) 70%);
            pointer-events: none;
        }

        .brand-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: #ffffff;
        }

        .brand-icon-box {
            width: 44px;
            height: 44px;
            background: var(--accent-amber);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 16px rgba(217, 119, 6, 0.35);
        }

        .brand-icon-box svg {
            width: 24px;
            height: 24px;
            stroke: #ffffff;
            stroke-width: 2.2;
            fill: none;
        }

        .brand-name {
            font-size: 24px;
            font-weight: 800;
            letter-spacing: -0.5px;
        }

        .brand-name span {
            color: #fbbf24;
        }

        .brand-content {
            margin: 40px 0;
            max-width: 520px;
        }

        .badge-tagline {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 14px;
            border-radius: 9999px;
            background: rgba(251, 191, 36, 0.12);
            border: 1px solid rgba(251, 191, 36, 0.3);
            color: #fbbf24;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 24px;
        }

        .brand-headline {
            font-size: 38px;
            font-weight: 800;
            line-height: 1.25;
            letter-spacing: -1px;
            margin-bottom: 16px;
        }

        .brand-headline em {
            color: #fbbf24;
            font-style: normal;
        }

        .brand-subline {
            font-size: 16px;
            line-height: 1.6;
            color: #94a3b8;
            margin-bottom: 36px;
        }

        .features-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .feature-item {
            display: flex;
            align-items: flex-start;
            gap: 16px;
        }

        .feature-icon-wrapper {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: rgba(34, 197, 94, 0.15);
            border: 1px solid rgba(34, 197, 94, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .feature-icon-wrapper svg {
            width: 18px;
            height: 18px;
            stroke: #4ade80;
            stroke-width: 2.5;
            fill: none;
        }

        .feature-title {
            font-size: 15px;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 4px;
        }

        .feature-desc {
            font-size: 13px;
            line-height: 1.5;
            color: #94a3b8;
        }

        .brand-footer {
            font-size: 13px;
            color: #64748b;
        }

        /* RIGHT FORM PANEL */
        .auth-form-panel {
            flex: 1;
            background: #ffffff;
            padding: 60px 80px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            overflow-y: auto;
        }

        .form-wrapper {
            max-width: 440px;
            width: 100%;
            margin: 0 auto;
        }

        .form-header {
            margin-bottom: 24px;
        }

        .form-title {
            font-size: 28px;
            font-weight: 800;
            color: var(--primary-navy);
            letter-spacing: -0.5px;
            margin-bottom: 8px;
        }

        .form-subtitle {
            font-size: 14px;
            color: var(--text-muted);
        }

        /* DEMO CREDENTIALS HELPER */
        .demo-credentials-box {
            background: #f1f5f9;
            border: 1px dashed #cbd5e1;
            border-radius: 12px;
            padding: 14px 16px;
            margin-bottom: 24px;
        }

        .demo-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 12px;
            font-weight: 700;
            color: #475569;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .demo-pills {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .demo-pill {
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 6px 12px;
            font-size: 12px;
            font-weight: 600;
            color: #1e293b;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .demo-pill:hover {
            border-color: var(--accent-amber);
            background: #fffbeb;
            color: var(--accent-amber);
            transform: translateY(-1px);
        }

        .demo-pill .role-tag {
            font-size: 10px;
            padding: 2px 6px;
            border-radius: 4px;
            background: #e2e8f0;
            color: #475569;
            text-transform: uppercase;
        }

        /* ALERTS */
        .alert {
            padding: 14px 16px;
            border-radius: 10px;
            font-size: 14px;
            line-height: 1.5;
            margin-bottom: 20px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }

        .alert-danger {
            background-color: var(--danger-bg);
            color: var(--danger-text);
            border: 1px solid var(--danger-border);
        }

        .alert-success {
            background-color: var(--success-bg);
            color: var(--success-text);
            border: 1px solid var(--success-border);
        }

        /* FORM ELEMENTS */
        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 8px;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon {
            position: absolute;
            left: 14px;
            color: #94a3b8;
            pointer-events: none;
            display: flex;
            align-items: center;
        }

        .input-icon svg {
            width: 18px;
            height: 18px;
        }

        .form-control {
            width: 100%;
            height: 48px;
            padding: 12px 16px 12px 44px;
            font-size: 14px;
            border: 1.5px solid var(--border-color);
            border-radius: 10px;
            background-color: #ffffff;
            color: var(--text-dark);
            transition: all 0.2s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--accent-amber);
            box-shadow: 0 0 0 3px rgba(217, 119, 6, 0.15);
        }

        .form-control.is-invalid {
            border-color: #ef4444;
            background-color: #fffaf0;
        }

        .field-error {
            color: #dc2626;
            font-size: 12px;
            font-weight: 500;
            margin-top: 6px;
        }

        .form-actions-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
            font-size: 13px;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #475569;
            cursor: pointer;
        }

        .remember-me input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: var(--accent-amber);
            cursor: pointer;
        }

        .forgot-link {
            color: var(--accent-amber);
            text-decoration: none;
            font-weight: 600;
        }

        .forgot-link:hover {
            text-decoration: underline;
        }

        .btn-submit {
            width: 100%;
            height: 50px;
            background-color: var(--accent-amber);
            color: #ffffff;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(217, 119, 6, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-submit:hover {
            background-color: var(--accent-amber-hover);
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(217, 119, 6, 0.4);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .form-footer {
            margin-top: 28px;
            text-align: center;
            font-size: 14px;
            color: var(--text-muted);
        }

        .form-footer a {
            color: var(--accent-amber);
            font-weight: 700;
            text-decoration: none;
        }

        .form-footer a:hover {
            text-decoration: underline;
        }

        /* RESPONSIVE */
        @media (max-width: 960px) {
            .auth-container {
                flex-direction: column;
            }
            .auth-brand-panel {
                padding: 40px 30px;
                min-height: auto;
            }
            .brand-headline {
                font-size: 28px;
            }
            .auth-form-panel {
                padding: 40px 30px;
            }
        }
    </style>
</head>
<body>

<div class="auth-container">
    <!-- LEFT PANEL: Brand & Features (Dark Navy) -->
    <div class="auth-brand-panel">
        <div>
            <a href="<?= base_url() ?>" class="brand-logo">
                <div class="brand-icon-box">
                    <svg viewBox="0 0 24 24">
                        <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path>
                        <circle cx="12" cy="13" r="4"></circle>
                    </svg>
                </div>
                <div class="brand-name">Cam<span>Hub</span></div>
            </a>
        </div>

        <div class="brand-content">
            <div class="badge-tagline">
                <span>✦</span> Platform Ekosistem Kamera #1
            </div>
            <h1 class="brand-headline">
                Sewa, Jual, Beli, Titip.<br>
                <em>Semua Kebutuhan</em> Fotografi.
            </h1>
            <p class="brand-subline">
                Satu platform terintegrasi untuk penyewaan kamera bergaransi, jual beli unit terverifikasi, dan pengelolaan toko mitra di seluruh Indonesia.
            </p>

            <ul class="features-list">
                <li class="feature-item">
                    <div class="feature-icon-wrapper">
                        <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    </div>
                    <div>
                        <div class="feature-title">Transaksi Aman (Escrow)</div>
                        <div class="feature-desc">Dana Anda diamankan di rekening bersama CamHub hingga unit kamera diterima dan diverifikasi sesuai deskripsi.</div>
                    </div>
                </li>
                <li class="feature-item">
                    <div class="feature-icon-wrapper">
                        <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    </div>
                    <div>
                        <div class="feature-title">Verifikasi Identitas Resmi (KYC)</div>
                        <div class="feature-desc">Seluruh penyewa dan pemilik lapak mitra melalui tahap verifikasi KTP untuk menjamin keamanan unit.</div>
                    </div>
                </li>
                <li class="feature-item">
                    <div class="feature-icon-wrapper">
                        <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    </div>
                    <div>
                        <div class="feature-title">Pilihan Kamera & Lensa Terlengkap</div>
                        <div class="feature-desc">Mulai dari kamera mirrorless, lensa premium, lighting hingga audio dari toko mitra terpercaya.</div>
                    </div>
                </li>
            </ul>
        </div>

        <div class="brand-footer">
            &copy; <?= date('Y') ?> CamHub Indonesia. Hak Cipta Dilindungi Undang-Undang.
        </div>
    </div>

    <!-- RIGHT PANEL: Form Login -->
    <div class="auth-form-panel">
        <div class="form-wrapper">
            <div class="form-header">
                <h2 class="form-title">Masuk ke CamHub</h2>
                <p class="form-subtitle">Silakan masukkan akun Anda untuk melanjutkan aktivitas</p>
            </div>

            <!-- QUICK DEMO LOGIN PILLS (Requested by Project Owner) -->
            <div class="demo-credentials-box">
                <div class="demo-header">
                    <span>⚡ Akses Cepat Akun Demo (1-Klik)</span>
                </div>
                <div class="demo-pills">
                    <button type="button" class="demo-pill" onclick="fillCredentials('admin', 'admin')">
                        <span class="role-tag">Admin</span>
                        <strong>admin</strong> : admin
                    </button>
                    <button type="button" class="demo-pill" onclick="fillCredentials('user', 'user')">
                        <span class="role-tag">Customer</span>
                        <strong>user</strong> : user
                    </button>
                    <button type="button" class="demo-pill" onclick="fillCredentials('mitra', 'mitra')">
                        <span class="role-tag">Mitra</span>
                        <strong>mitra</strong> : mitra
                    </button>
                </div>
            </div>

            <!-- FLASH ALERTS -->
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger">
                    <svg style="width:20px;height:20px;flex-shrink:0;fill:currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                    <div><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
                </div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="alert alert-success">
                    <svg style="width:20px;height:20px;flex-shrink:0;fill:currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <div><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></div>
                </div>
            <?php endif; ?>

            <!-- LOGIN FORM -->
            <form action="<?= base_url('/login') ?>" method="POST" autocomplete="off">
                <?= csrf_field() ?>

                <div class="form-group">
                    <label class="form-label" for="identifier">Email, No. HP, atau Username</label>
                    <div class="input-wrapper">
                        <span class="input-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                        </span>
                        <input 
                            type="text" 
                            id="identifier" 
                            name="identifier" 
                            class="form-control <?= isset($errors['identifier']) ? 'is-invalid' : '' ?>" 
                            placeholder="Contoh: admin, user, atau nama@email.com" 
                            value="<?= htmlspecialchars(old('identifier'), ENT_QUOTES, 'UTF-8') ?>"
                            required 
                            autofocus
                        >
                    </div>
                    <?php if (isset($errors['identifier'])): ?>
                        <div class="field-error"><?= htmlspecialchars($errors['identifier'], ENT_QUOTES, 'UTF-8') ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <div class="input-wrapper">
                        <span class="input-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                            </svg>
                        </span>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>" 
                            placeholder="Masukkan password akun Anda" 
                            required
                        >
                    </div>
                    <?php if (isset($errors['password'])): ?>
                        <div class="field-error"><?= htmlspecialchars($errors['password'], ENT_QUOTES, 'UTF-8') ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-actions-row">
                    <label class="remember-me">
                        <input type="checkbox" name="remember" value="1">
                        <span>Ingat saya di perangkat ini</span>
                    </label>
                    <a href="#" class="forgot-link" onclick="alert('Untuk keperluan tugas & demo, silakan gunakan akun admin/user/mitra.'); return false;">Lupa Password?</a>
                </div>

                <button type="submit" class="btn-submit">
                    <span>Masuk ke Akun</span>
                    <svg style="width:18px;height:18px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                        <polyline points="12 5 19 12 12 19"></polyline>
                    </svg>
                </button>
            </form>

            <div class="form-footer">
                Belum memiliki akun CamHub? <a href="<?= base_url('/register') ?>">Daftar Sekarang</a>
            </div>
        </div>
    </div>
</div>

<script>
    function fillCredentials(id, pass) {
        document.getElementById('identifier').value = id;
        document.getElementById('password').value = pass;
    }
</script>

</body>
</html>


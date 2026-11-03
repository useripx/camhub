<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Daftar Akun — CamHub', ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-navy: #0b192e;
            --accent-amber: #d97706;
            --accent-amber-hover: #b45309;
            --text-dark: #0f172a;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --bg-light: #f8fafc;
            --danger-bg: #fef2f2;
            --danger-text: #991b1b;
            --danger-border: #fecaca;
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

        /* LEFT BRAND PANEL */
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
        }

        .brand-name span {
            color: #fbbf24;
        }

        .brand-content {
            margin: 30px 0;
            max-width: 520px;
        }

        .brand-headline {
            font-size: 34px;
            font-weight: 800;
            line-height: 1.3;
            margin-bottom: 16px;
        }

        .brand-headline em {
            color: #fbbf24;
            font-style: normal;
        }

        .brand-subline {
            font-size: 15px;
            line-height: 1.6;
            color: #94a3b8;
            margin-bottom: 30px;
        }

        .brand-footer {
            font-size: 13px;
            color: #64748b;
        }

        /* RIGHT FORM PANEL */
        .auth-form-panel {
            flex: 1.2;
            background: #ffffff;
            padding: 50px 80px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            overflow-y: auto;
        }

        .form-wrapper {
            max-width: 480px;
            width: 100%;
            margin: 0 auto;
        }

        .form-header {
            margin-bottom: 24px;
        }

        .form-title {
            font-size: 26px;
            font-weight: 800;
            color: var(--primary-navy);
            letter-spacing: -0.5px;
            margin-bottom: 6px;
        }

        .form-subtitle {
            font-size: 14px;
            color: var(--text-muted);
        }

        /* ALERTS */
        .alert {
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 14px;
            line-height: 1.5;
            margin-bottom: 20px;
        }

        .alert-danger {
            background-color: var(--danger-bg);
            color: var(--danger-text);
            border: 1px solid var(--danger-border);
        }

        /* FORM ELEMENTS */
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 6px;
        }

        .form-control {
            width: 100%;
            height: 46px;
            padding: 10px 14px;
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
            margin-top: 4px;
        }

        /* ROLE SELECTOR */
        .role-selector {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 20px;
        }

        .role-card {
            border: 2px solid var(--border-color);
            border-radius: 10px;
            padding: 12px;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .role-card:hover {
            border-color: var(--accent-amber);
        }

        .role-card.active {
            border-color: var(--accent-amber);
            background-color: #fffbeb;
        }

        .role-card input[type="radio"] {
            accent-color: var(--accent-amber);
            cursor: pointer;
        }

        .role-text-title {
            font-size: 13px;
            font-weight: 700;
            color: #1e293b;
        }

        .role-text-desc {
            font-size: 11px;
            color: #64748b;
        }

        .btn-submit {
            width: 100%;
            height: 48px;
            background-color: var(--accent-amber);
            color: #ffffff;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
            margin-top: 8px;
            box-shadow: 0 4px 12px rgba(217, 119, 6, 0.3);
        }

        .btn-submit:hover {
            background-color: var(--accent-amber-hover);
        }

        .form-footer {
            margin-top: 24px;
            text-align: center;
            font-size: 14px;
            color: var(--text-muted);
        }

        .form-footer a {
            color: var(--accent-amber);
            font-weight: 700;
            text-decoration: none;
        }

        @media (max-width: 960px) {
            .auth-container { flex-direction: column; }
            .auth-brand-panel, .auth-form-panel { padding: 40px 24px; }
            .form-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<div class="auth-container">
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
            <h1 class="brand-headline">
                Bergabung dengan Komunitas<br>
                <em>Fotografi Terbesar</em>.
            </h1>
            <p class="brand-subline">
                Daftar sekarang untuk menyewa kamera impian, menjual peralatan foto Anda, atau buka lapak rental resmi dengan sistem keamanan terjamin.
            </p>
        </div>

        <div class="brand-footer">
            &copy; <?= date('Y') ?> CamHub Indonesia. Hak Cipta Dilindungi Undang-Undang.
        </div>
    </div>

    <div class="auth-form-panel">
        <div class="form-wrapper">
            <div class="form-header">
                <h2 class="form-title">Daftar Akun Baru</h2>
                <p class="form-subtitle">Lengkapi formulir di bawah ini untuk memulai di CamHub</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>

            <form action="<?= base_url('/register') ?>" method="POST" autocomplete="off">
                <?= csrf_field() ?>

                <div class="form-group">
                    <label class="form-label">Daftar Sebagai:</label>
                    <div class="role-selector">
                        <label class="role-card active" id="label-role-customer">
                            <input type="radio" name="role" value="customer" checked onchange="toggleRoleCard(this)">
                            <div>
                                <div class="role-text-title">Pelanggan</div>
                                <div class="role-text-desc">Sewa & beli kamera</div>
                            </div>
                        </label>
                        <label class="role-card" id="label-role-partner">
                            <input type="radio" name="role" value="partner" onchange="toggleRoleCard(this)">
                            <div>
                                <div class="role-text-title">Mitra Lapak</div>
                                <div class="role-text-desc">Buka rental & toko</div>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label" for="full_name">Nama Lengkap</label>
                        <input type="text" id="full_name" name="full_name" class="form-control <?= isset($errors['full_name']) ? 'is-invalid' : '' ?>" placeholder="Contoh: Yogi Ario" value="<?= htmlspecialchars(old('full_name'), ENT_QUOTES, 'UTF-8') ?>" required>
                        <?php if (isset($errors['full_name'])): ?>
                            <div class="field-error"><?= htmlspecialchars($errors['full_name'], ENT_QUOTES, 'UTF-8') ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="username">Username (Opsional)</label>
                        <input type="text" id="username" name="username" class="form-control <?= isset($errors['username']) ? 'is-invalid' : '' ?>" placeholder="Contoh: yogiario" value="<?= htmlspecialchars(old('username'), ENT_QUOTES, 'UTF-8') ?>">
                        <?php if (isset($errors['username'])): ?>
                            <div class="field-error"><?= htmlspecialchars($errors['username'], ENT_QUOTES, 'UTF-8') ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label" for="email">Alamat Email</label>
                        <input type="email" id="email" name="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" placeholder="nama@email.com" value="<?= htmlspecialchars(old('email'), ENT_QUOTES, 'UTF-8') ?>" required>
                        <?php if (isset($errors['email'])): ?>
                            <div class="field-error"><?= htmlspecialchars($errors['email'], ENT_QUOTES, 'UTF-8') ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="phone">Nomor HP / WhatsApp</label>
                        <input type="tel" id="phone" name="phone" class="form-control <?= isset($errors['phone']) ? 'is-invalid' : '' ?>" placeholder="08123456789" value="<?= htmlspecialchars(old('phone'), ENT_QUOTES, 'UTF-8') ?>" required>
                        <?php if (isset($errors['phone'])): ?>
                            <div class="field-error"><?= htmlspecialchars($errors['phone'], ENT_QUOTES, 'UTF-8') ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label" for="password">Password</label>
                        <input type="password" id="password" name="password" class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>" placeholder="Minimal 4 karakter" required>
                        <?php if (isset($errors['password'])): ?>
                            <div class="field-error"><?= htmlspecialchars($errors['password'], ENT_QUOTES, 'UTF-8') ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="password_confirmation">Ulangi Password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control <?= isset($errors['password_confirmation']) ? 'is-invalid' : '' ?>" placeholder="Ketik ulang password" required>
                        <?php if (isset($errors['password_confirmation'])): ?>
                            <div class="field-error"><?= htmlspecialchars($errors['password_confirmation'], ENT_QUOTES, 'UTF-8') ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <button type="submit" class="btn-submit">Daftar Akun Sekarang</button>
            </form>

            <div class="form-footer">
                Sudah memiliki akun? <a href="<?= base_url('/login') ?>">Masuk ke Akun</a>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleRoleCard(radio) {
        document.getElementById('label-role-customer').classList.toggle('active', radio.value === 'customer');
        document.getElementById('label-role-partner').classList.toggle('active', radio.value === 'partner');
    }
</script>

</body>
</html>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'CamHub') ?></title>
    <link rel="stylesheet" href="<?= asset_url('css/app.css') ?>">
    <script>
        (function() {
            const theme = localStorage.getItem('camhub_theme') || 'dark';
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>
</head>
<body>

    <!-- Header Navigation Bar (Mockup Beranda) -->
    <header class="navbar">
        <div class="container nav-wrapper">
            <a href="<?= base_url() ?>" class="brand-logo">
                📷 <span><?= htmlspecialchars($appName ?? 'CamHub') ?></span>
            </a>
            <nav>
                <ul class="nav-links">
                    <li><a href="<?= base_url() ?>" class="active">Beranda</a></li>
                    <li><a href="#kategori">Kategori</a></li>
                    <li><a href="#sewa">Sewa</a></li>
                    <li><a href="#jual-beli">Jual Beli</a></li>
                    <li><a href="#titip-jual">Titip Jual</a></li>
                </ul>
            </nav>

            <!-- AUTH BUTTONS (Login / Register / Dashboard) -->
            <div class="nav-auth-buttons" style="display: flex; align-items: center; gap: 10px;">
                <button type="button" onclick="toggleCamHubTheme()" style="display:inline-flex; align-items:center; gap:6px; padding:8px 14px; border-radius:8px; border:1px solid #334155; background:#1e293b; color:#ffffff; font-size:14px; font-weight:600; cursor:pointer;" title="Beralih Mode Gelap/Terang">
                    <span id="themeIconWrap"></span>
                    <span id="themeToggleText">Mode Terang</span>
                </button>
                <?php if (auth_check()): ?>
                    <?php 
                        $currentUser = auth();
                        $dashUrl = match(strtolower($currentUser['role_name'] ?? '')) {
                            'admin' => base_url('/dashboard/admin'),
                            'partner' => base_url('/dashboard/mitra'),
                            default => base_url('/dashboard/pelanggan')
                        };
                        $displayName = $currentUser['full_name'] ?? $currentUser['username'] ?? 'Akun Saya';
                    ?>
                    <a href="<?= $dashUrl ?>" style="background: #2563eb; color: #ffffff; padding: 8px 16px; border-radius: 8px; text-decoration: none; font-size: 14px; font-weight: 700; display: inline-flex; align-items: center;">
                        <?= htmlspecialchars($displayName, ENT_QUOTES, 'UTF-8') ?>
                    </a>
                    <a href="<?= base_url('/logout') ?>" style="background: #fee2e2; color: #dc2626; padding: 8px 14px; border-radius: 8px; text-decoration: none; font-size: 14px; font-weight: 600;">
                        Keluar
                    </a>
                <?php else: ?>
                    <a href="<?= base_url('/login') ?>" style="background: transparent; color: #0b192e; border: 1.5px solid #cbd5e1; padding: 8px 18px; border-radius: 8px; text-decoration: none; font-size: 14px; font-weight: 700; transition: 0.2s;">
                        Masuk (Login)
                    </a>
                    <a href="<?= base_url('/register') ?>" style="background: #d97706; color: #ffffff; padding: 8px 18px; border-radius: 8px; text-decoration: none; font-size: 14px; font-weight: 700; box-shadow: 0 2px 8px rgba(217, 119, 6, 0.3); transition: 0.2s;">
                        Daftar Akun
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <main class="container">
        
        <!-- Hero Banner (Mockup Beranda) -->
        <section class="hero-card">
            <span class="hero-badge">CAMERA MARKETPLACE</span>
            <h1 class="hero-title">Sewa kamera, abadikan cerita.</h1>
            <p class="hero-desc">Sewa atau beli perlengkapan fotografi dari komunitas terpercaya di Indonesia.</p>
            <div style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
                <?php if (!auth_check()): ?>
                    <a href="<?= base_url('/login') ?>" style="background: #d97706; color: #ffffff; padding: 12px 24px; border-radius: 10px; text-decoration: none; font-weight: 700; font-size: 15px; box-shadow: 0 4px 14px rgba(217,119,6,0.4);">
                        Masuk ke Akun
                    </a>
                    <a href="<?= base_url('/register') ?>" style="background: rgba(255,255,255,0.15); color: #ffffff; border: 1px solid rgba(255,255,255,0.3); padding: 12px 24px; border-radius: 10px; text-decoration: none; font-weight: 700; font-size: 15px;">
                        Daftar Pelanggan / Mitra
                    </a>
                <?php else: ?>
                    <a href="<?= $dashUrl ?>" style="background: #d97706; color: #ffffff; padding: 12px 24px; border-radius: 10px; text-decoration: none; font-weight: 700; font-size: 15px;">
                        Buka Dashboard Anda &rarr;
                    </a>
                <?php endif; ?>
                <a href="<?= base_url('api/health') ?>" target="_blank" style="color: #94a3b8; font-size: 14px; text-decoration: underline; margin-left: 8px;">
                    Cek Status API
                </a>
            </div>
        </section>

        <!-- Foundation & Security Verification Cards -->
        <section style="margin-top: 48px; margin-bottom: 64px;">
            <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 20px;">
                <div>
                    <h2 style="font-size: 24px; font-weight: 700; color: var(--text-primary);">Status Sistem CamHub</h2>
                    <p style="color: var(--text-secondary); font-size: 15px;">Tahap 01 Fondasi, Tahap 02 Database 22 Tabel, dan Tahap 03 Autentikasi Pengguna Aktif</p>
                </div>
                <span class="badge badge-sewa">PHP Native & RBAC</span>
            </div>

            <div class="grid-cards">
                <div class="card">
                    <h3 style="font-size: 17px; font-weight: 700; margin-bottom: 8px;">Autentikasi Multi-Peran</h3>
                    <p style="color: var(--text-secondary); font-size: 14px; margin-bottom: 16px;">
                        Mendukung login fleksibel dengan akun siap pakai <code>admin:admin</code>, <code>user:user</code>, dan <code>mitra:mitra</code>.
                    </p>
                    <a href="<?= base_url('/login') ?>" style="color: #2563eb; font-weight: 700; font-size: 14px; text-decoration: none;">Uji Login Sekarang &rarr;</a>
                </div>

                <div class="card">
                    <h3 style="font-size: 17px; font-weight: 700; margin-bottom: 8px;">Basis Data 22 Tabel</h3>
                    <p style="color: var(--text-secondary); font-size: 14px; margin-bottom: 16px;">
                        Relasional InnoDB dengan transaksi ACID, proteksi bentrokan sewa, dan 27 foreign keys di <code>camhub_db</code>.
                    </p>
                    <span style="color: #10b981; font-weight: 600; font-size: 14px;">Status: 22 Tabel Terverifikasi</span>
                </div>

                <div class="card">
                    <h3 style="font-size: 17px; font-weight: 700; margin-bottom: 8px;">Keamanan & Enkripsi</h3>
                    <p style="color: var(--text-secondary); font-size: 14px; margin-bottom: 16px;">
                        BCRYPT password hashing, token CSRF guard, dan regenerasi session ID anti-pembajakan.
                    </p>
                    <span style="color: #10b981; font-weight: 600; font-size: 14px;">Status: 100% PASS Gate</span>
                </div>
            </div>
        </section>

    </main>

    <!-- Footer -->
    <footer style="border-top: 1px solid var(--border-color); background-color: #ffffff; padding: 24px 0; text-align: center; color: var(--text-secondary); font-size: 14px;">
        <div class="container">
            <p>&copy; <?= date('Y') ?> CamHub – Camera Marketplace Platform. Kelompok 1 (Yogi Ario, Muhammad Regieyanto, Ivan Farrel, Krisna Mukti).</p>
        </div>
    </footer>

    <script>
        const SUN_ICON = `<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>`;
        const MOON_ICON = `<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>`;
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

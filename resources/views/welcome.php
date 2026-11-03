<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'CamHub') ?></title>
    <link rel="stylesheet" href="<?= asset_url('css/app.css') ?>">
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
        </div>
    </header>

    <!-- Main Container -->
    <main class="container">
        
        <!-- Hero Banner (Mockup Beranda) -->
        <section class="hero-card">
            <span class="hero-badge">CAMERA MARKETPLACE</span>
            <h1 class="hero-title">Sewa kamera, abadikan cerita.</h1>
            <p class="hero-desc">Sewa atau beli perlengkapan fotografi dari komunitas terpercaya di Indonesia.</p>
            <div style="display: flex; gap: 12px; align-items: center;">
                <span class="badge badge-success" style="padding: 8px 16px; font-size: 14px;">
                    ✓ Tahap 01: Persiapan Fondasi Aktif
                </span>
                <a href="<?= base_url('api/health') ?>" target="_blank" class="btn btn-primary" style="background-color: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.3);">
                    Cek Status API
                </a>
            </div>
        </section>

        <!-- Foundation Verification Cards -->
        <section style="margin-top: 48px; margin-bottom: 64px;">
            <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 20px;">
                <div>
                    <h2 style="font-size: 24px; font-weight: 700; color: var(--text-primary);">Status Fondasi Sistem</h2>
                    <p style="color: var(--text-secondary); font-size: 15px;">Integritas arsitektur Modular PHP Native terverifikasi di Laragon</p>
                </div>
                <span class="badge badge-sewa">PHP Native & PDO</span>
            </div>

            <div class="grid-cards">
                <div class="card">
                    <h3 style="font-size: 17px; font-weight: 700; margin-bottom: 8px;">Arsitektur Modular</h3>
                    <p style="color: var(--text-secondary); font-size: 14px; margin-bottom: 16px;">
                        Pemisahan jelas antara Presentation, Controller, Service, dan Repository.
                    </p>
                    <span style="color: var(--primary); font-weight: 600; font-size: 14px;">Status: Siap (Layering Aktif)</span>
                </div>

                <div class="card">
                    <h3 style="font-size: 17px; font-weight: 700; margin-bottom: 8px;">Front Controller & Router</h3>
                    <p style="color: var(--text-secondary); font-size: 14px; margin-bottom: 16px;">
                        URL Rewrite bersih via <code>public/index.php</code> dan <code>app/Core/Router.php</code>.
                    </p>
                    <span style="color: var(--primary); font-weight: 600; font-size: 14px;">Status: 200 OK Dispatched</span>
                </div>

                <div class="card">
                    <h3 style="font-size: 17px; font-weight: 700; margin-bottom: 8px;">Keamanan & Lingkungan</h3>
                    <p style="color: var(--text-secondary); font-size: 14px; margin-bottom: 16px;">
                        Proteksi kunci rahasia lewat <code>.gitignore</code> dan isolasi document root <code>public/</code>.
                    </p>
                    <span style="color: var(--success); font-weight: 600; font-size: 14px;">Status: Terproteksi Aman</span>
                </div>
            </div>
        </section>

    </main>

    <!-- Footer -->
    <footer style="border-top: 1px solid var(--border-color); background-color: #ffffff; padding: 24px 0; text-align: center; color: var(--text-secondary); font-size: 14px;">
        <div class="container">
            <p>&copy; <?= date('Y') ?> CamHub – Camera Marketplace Platform. Project Owner: Yogi Ario.</p>
        </div>
    </footer>

</body>
</html>


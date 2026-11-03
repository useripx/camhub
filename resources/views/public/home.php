<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'CamHub — Platform Marketplace Kamera', ENT_QUOTES, 'UTF-8') ?></title>
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
            --badge-green: #16a34a;
            --badge-green-bg: #dcfce7;
            --blue-accent: #2563eb;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
        }

        body {
            background-color: var(--bg-light);
            color: var(--text-dark);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* HEADER / NAVBAR */
        .navbar {
            background: #ffffff;
            border-bottom: 1px solid var(--border-color);
            position: sticky;
            top: 0;
            z-index: 50;
            padding: 0.85rem 1.5rem;
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1.5rem;
        }

        .brand-logo {
            font-size: 1.35rem;
            font-weight: 800;
            color: var(--primary-navy);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .brand-logo span {
            color: var(--accent-amber);
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            list-style: none;
        }

        .nav-links a {
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.92rem;
            font-weight: 600;
            transition: color 0.15s;
        }

        .nav-links a:hover, .nav-links a.active {
            color: var(--accent-amber);
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.55rem 1.15rem;
            font-size: 0.88rem;
            font-weight: 600;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.2s;
            cursor: pointer;
            border: 1px solid transparent;
        }

        .btn-outline {
            border-color: var(--border-color);
            color: var(--text-dark);
            background: #ffffff;
        }

        .btn-outline:hover {
            border-color: var(--accent-amber);
            color: var(--accent-amber);
        }

        .btn-primary {
            background: var(--accent-amber);
            color: #ffffff;
        }

        .btn-primary:hover {
            background: var(--accent-amber-hover);
        }

        /* HERO BANNER */
        .hero {
            background: linear-gradient(135deg, var(--primary-navy) 0%, #15294a 100%);
            color: #ffffff;
            padding: 4.5rem 1.5rem 4rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: -50px;
            right: -50px;
            width: 350px;
            height: 350px;
            background: radial-gradient(circle, rgba(217,119,6,0.15) 0%, rgba(11,25,46,0) 70%);
            border-radius: 50%;
        }

        .hero-container {
            max-width: 850px;
            margin: 0 auto;
            position: relative;
            z-index: 2;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.35rem 0.85rem;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            color: var(--accent-amber-light);
            margin-bottom: 1.25rem;
        }

        .hero-title {
            font-size: 2.75rem;
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 1rem;
            letter-spacing: -0.5px;
        }

        .hero-subtitle {
            font-size: 1.1rem;
            color: #94a3b8;
            max-width: 620px;
            margin: 0 auto 2rem;
            line-height: 1.6;
        }

        /* HERO SEARCH BOX */
        .hero-search-box {
            background: #ffffff;
            padding: 0.5rem;
            border-radius: 12px;
            display: flex;
            gap: 0.5rem;
            max-width: 700px;
            margin: 0 auto;
            box-shadow: 0 15px 35px rgba(0,0,0,0.25);
        }

        .hero-search-box input {
            flex: 1;
            border: none;
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
            outline: none;
            color: var(--text-dark);
        }

        .hero-search-box select {
            border: none;
            border-left: 1px solid var(--border-color);
            padding: 0.75rem 1rem;
            font-size: 0.9rem;
            outline: none;
            color: var(--text-muted);
            background: transparent;
            cursor: pointer;
        }

        .hero-search-box button {
            background: var(--accent-amber);
            color: #ffffff;
            border: none;
            padding: 0.75rem 1.75rem;
            font-size: 0.92rem;
            font-weight: 700;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.2s;
        }

        .hero-search-box button:hover {
            background: var(--accent-amber-hover);
        }

        /* MAIN CONTENT SECTION */
        .section-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 3.5rem 1.5rem;
            width: 100%;
        }

        .section-header {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            margin-bottom: 2rem;
        }

        .section-title {
            font-size: 1.6rem;
            font-weight: 800;
            color: var(--primary-navy);
            letter-spacing: -0.3px;
        }

        .section-subtitle {
            color: var(--text-muted);
            font-size: 0.92rem;
            margin-top: 0.25rem;
        }

        .section-link {
            color: var(--blue-accent);
            text-decoration: none;
            font-size: 0.92rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            transition: color 0.15s;
        }

        .section-link:hover {
            color: var(--accent-amber);
        }

        /* CATEGORIES CHIPS / GRID */
        .categories-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 1rem;
            margin-bottom: 3.5rem;
        }

        .category-card {
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 1.25rem;
            text-decoration: none;
            color: var(--text-dark);
            text-align: center;
            transition: all 0.2s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
        }

        .category-card:hover {
            border-color: var(--accent-amber);
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.05);
        }

        .category-icon {
            font-size: 2rem;
            margin-bottom: 0.25rem;
        }

        .category-name {
            font-weight: 700;
            font-size: 0.95rem;
        }

        .category-count {
            font-size: 0.78rem;
            color: var(--text-muted);
        }

        /* PRODUCT CARDS GRID */
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 1.75rem;
        }

        .product-card {
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 14px;
            overflow: hidden;
            text-decoration: none;
            color: var(--text-dark);
            transition: all 0.2s ease;
            display: flex;
            flex-direction: column;
        }

        .product-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 28px rgba(0,0,0,0.08);
            border-color: #cbd5e1;
        }

        .product-image-wrap {
            height: 200px;
            background: #f1f5f9;
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .product-image-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .product-card:hover .product-image-wrap img {
            transform: scale(1.04);
        }

        .badge-status {
            position: absolute;
            top: 12px;
            left: 12px;
            background: var(--badge-green-bg);
            color: var(--badge-green);
            padding: 0.25rem 0.65rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 700;
        }

        .badge-category {
            position: absolute;
            top: 12px;
            right: 12px;
            background: rgba(11,25,46,0.85);
            color: #ffffff;
            padding: 0.25rem 0.65rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .product-body {
            padding: 1.25rem;
            display: flex;
            flex-direction: column;
            flex: 1;
        }

        .product-store {
            font-size: 0.8rem;
            color: var(--text-muted);
            margin-bottom: 0.35rem;
            display: flex;
            align-items: center;
            gap: 0.35rem;
        }

        .product-title {
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--primary-navy);
            margin-bottom: 0.5rem;
            line-height: 1.3;
        }

        .product-desc {
            font-size: 0.85rem;
            color: var(--text-muted);
            margin-bottom: 1rem;
            line-height: 1.5;
            flex: 1;
        }

        .product-rating {
            display: flex;
            align-items: center;
            gap: 0.35rem;
            font-size: 0.85rem;
            margin-bottom: 1rem;
        }

        .stars {
            color: #f59e0b;
        }

        .rating-num {
            font-weight: 700;
            color: var(--text-dark);
        }

        .reviews-count {
            color: var(--text-muted);
            font-size: 0.8rem;
        }

        .product-footer {
            border-top: 1px solid var(--border-color);
            padding-top: 0.85rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .price-label {
            font-size: 0.75rem;
            color: var(--text-muted);
            text-transform: uppercase;
            font-weight: 600;
        }

        .price-value {
            font-size: 1.2rem;
            font-weight: 800;
            color: var(--accent-amber);
        }

        .price-unit {
            font-size: 0.82rem;
            font-weight: 500;
            color: var(--text-muted);
        }

        .btn-view-detail {
            padding: 0.45rem 0.95rem;
            font-size: 0.82rem;
            font-weight: 700;
            border-radius: 8px;
            background: var(--primary-navy);
            color: #ffffff;
            text-decoration: none;
            transition: background 0.15s;
        }

        .btn-view-detail:hover {
            background: var(--blue-accent);
        }

        /* VALUE PROPOSITION / FEATURES */
        .features-banner {
            background: #ffffff;
            border-top: 1px solid var(--border-color);
            border-bottom: 1px solid var(--border-color);
            padding: 3rem 1.5rem;
        }

        .features-grid {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 2rem;
        }

        .feature-item {
            display: flex;
            gap: 1rem;
            align-items: flex-start;
        }

        .feature-icon-wrap {
            width: 46px;
            height: 46px;
            border-radius: 10px;
            background: var(--accent-amber-light);
            color: var(--accent-amber);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
            flex-shrink: 0;
        }

        .feature-title {
            font-size: 1rem;
            font-weight: 700;
            color: var(--primary-navy);
            margin-bottom: 0.25rem;
        }

        .feature-desc {
            font-size: 0.85rem;
            color: var(--text-muted);
            line-height: 1.5;
        }

        /* MITRA CTA BANNER */
        .mitra-cta {
            background: linear-gradient(135deg, #1e293b 0%, var(--primary-navy) 100%);
            border-radius: 16px;
            padding: 3rem 2.5rem;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 2rem;
            margin-top: 2rem;
        }

        .mitra-cta-title {
            font-size: 1.6rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
        }

        .mitra-cta-desc {
            color: #94a3b8;
            font-size: 0.95rem;
            max-width: 550px;
            line-height: 1.6;
        }

        /* FOOTER */
        .footer {
            background: var(--primary-navy);
            color: #cbd5e1;
            padding: 3.5rem 1.5rem 1.5rem;
            margin-top: auto;
        }

        .footer-grid {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1.5fr;
            gap: 3rem;
            margin-bottom: 2.5rem;
        }

        .footer-brand h3 {
            color: #ffffff;
            font-size: 1.4rem;
            font-weight: 800;
            margin-bottom: 0.75rem;
        }

        .footer-brand p {
            font-size: 0.88rem;
            line-height: 1.6;
            color: #94a3b8;
            margin-bottom: 1rem;
        }

        .footer-col h4 {
            color: #ffffff;
            font-size: 0.95rem;
            font-weight: 700;
            margin-bottom: 1rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .footer-col ul {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 0.55rem;
        }

        .footer-col a {
            color: #94a3b8;
            text-decoration: none;
            font-size: 0.88rem;
            transition: color 0.15s;
        }

        .footer-col a:hover {
            color: var(--accent-amber);
        }

        .footer-bottom {
            max-width: 1200px;
            margin: 0 auto;
            border-top: 1px solid rgba(255,255,255,0.1);
            padding-top: 1.5rem;
            text-align: center;
            font-size: 0.82rem;
            color: #64748b;
        }

        @media (max-width: 768px) {
            .hero-title { font-size: 2rem; }
            .hero-search-box { flex-direction: column; }
            .hero-search-box select { border-left: none; border-top: 1px solid var(--border-color); }
            .footer-grid { grid-template-columns: 1fr; gap: 2rem; }
            .mitra-cta { flex-direction: column; text-align: center; }
            .nav-links { display: none; }
        }
    </style>
</head>
<body>

    <!-- NAVBAR -->
    <header class="navbar">
        <div class="nav-container">
            <a href="<?= base_url() ?>" class="brand-logo">
                📷 Cam<span>Hub</span>
            </a>

            <nav>
                <ul class="nav-links">
                    <li><a href="<?= base_url() ?>" class="active">Beranda</a></li>
                    <li><a href="<?= base_url('katalog') ?>">Katalog Produk</a></li>
                    <li><a href="<?= base_url('katalog?type=rental') ?>">Sewa Kamera</a></li>
                    <li><a href="<?= base_url('katalog?type=sale') ?>">Jual Beli</a></li>
                    <li><a href="<?= base_url('katalog?type=consignment') ?>">Titip Jual</a></li>
                </ul>
            </nav>

            <div class="nav-actions">
                <?php if (auth_check()): ?>
                    <a href="<?= base_url('dashboard/' . (auth()['role_name'] === 'admin' ? 'admin' : (auth()['role_name'] === 'partner' ? 'mitra' : 'pelanggan'))) ?>" class="btn btn-outline">
                        👤 Dashboard (<?= htmlspecialchars(auth()['username'] ?? 'Akun', ENT_QUOTES, 'UTF-8') ?>)
                    </a>
                    <a href="<?= base_url('logout') ?>" class="btn btn-outline" style="color:#ef4444;">
                        Keluar
                    </a>
                <?php else: ?>
                    <a href="<?= base_url('login') ?>" class="btn btn-outline">Masuk (Login)</a>
                    <a href="<?= base_url('register') ?>" class="btn btn-primary">Daftar Akun</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- HERO SECTION -->
    <section class="hero">
        <div class="hero-container">
            <div class="hero-badge">
                ✦ PLATFORM EKOSISTEM KAMERA #1 DI INDONESIA
            </div>
            <h1 class="hero-title">Sewa kamera, abadikan cerita.</h1>
            <p class="hero-subtitle">
                Sewa atau beli perlengkapan fotografi dari komunitas terpercaya di Indonesia. Dilindungi garansi rekening bersama escrow dan verifikasi identitas resmi.
            </p>

            <!-- SEARCH BAR -->
            <form action="<?= base_url('katalog') ?>" method="GET" class="hero-search-box">
                <input type="text" name="q" placeholder="Cari kamera, lensa, atau aksesoris..." autocomplete="off">
                <select name="category">
                    <option value="">Semua Kategori</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= htmlspecialchars($cat->slug, ENT_QUOTES, 'UTF-8') ?>">
                            <?= htmlspecialchars($cat->name, ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit">Cari Kamera</button>
            </form>
        </div>
    </section>

    <!-- MAIN BODY -->
    <main class="section-container">

        <!-- KATEGORI GRID -->
        <div class="section-header">
            <div>
                <h2 class="section-title">Kategori Perlengkapan</h2>
                <p class="section-subtitle">Pilih kategori peralatan sesuai kebutuhan sesi foto atau produksi video Anda</p>
            </div>
            <a href="<?= base_url('katalog') ?>" class="section-link">
                Lihat Semua Kategori →
            </a>
        </div>

        <div class="categories-grid">
            <?php foreach ($categories as $cat): ?>
                <a href="<?= base_url('katalog?category=' . urlencode($cat->slug)) ?>" class="category-card">
                    <div class="category-icon">
                        <?php 
                            $iconMap = [
                                'kamera-mirrorless' => '📷',
                                'kamera-dslr'       => '📸',
                                'lensa'             => '🔍',
                                'action-cam'        => '📹',
                                'lighting'          => '💡',
                                'aksesoris'         => '🎒',
                            ];
                            echo $iconMap[$cat->slug] ?? '🎥';
                        ?>
                    </div>
                    <div class="category-name"><?= htmlspecialchars($cat->name, ENT_QUOTES, 'UTF-8') ?></div>
                    <div class="category-count"><?= $cat->product_count ?> Produk Tersedia</div>
                </a>
            <?php endforeach; ?>
        </div>

        <!-- POPULER DISEWA SECTION -->
        <div class="section-header">
            <div>
                <h2 class="section-title">Paling Populer Disewa</h2>
                <p class="section-subtitle">Unit kamera pilihan dengan rating kepuasan penyewa tertinggi di CamHub</p>
            </div>
            <a href="<?= base_url('katalog?sort=popular') ?>" class="section-link">
                Jelajahi Semua Produk (<?= count($featuredProducts) ?>+) →
            </a>
        </div>

        <div class="product-grid">
            <?php foreach ($featuredProducts as $product): ?>
                <div class="product-card">
                    <div class="product-image-wrap">
                        <img src="<?= htmlspecialchars($product->getPrimaryImageUrl(), ENT_QUOTES, 'UTF-8') ?>" 
                             alt="<?= htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8') ?>"
                             loading="lazy">
                        <span class="badge-status">Tersedia</span>
                        <span class="badge-category"><?= htmlspecialchars($product->category_name ?? 'Kamera', ENT_QUOTES, 'UTF-8') ?></span>
                    </div>

                    <div class="product-body">
                        <div class="product-store">
                            🏪 <?= htmlspecialchars($product->store_name ?? 'Mitra CamHub', ENT_QUOTES, 'UTF-8') ?>
                            • 📍 <?= htmlspecialchars($product->store_city ?? 'Indonesia', ENT_QUOTES, 'UTF-8') ?>
                        </div>

                        <h3 class="product-title"><?= htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8') ?></h3>
                        <p class="product-desc"><?= htmlspecialchars($product->description ?? '', ENT_QUOTES, 'UTF-8') ?></p>

                        <div class="product-rating">
                            <span class="stars">★★★★★</span>
                            <span class="rating-num"><?= $product->getStarRating() ?></span>
                            <span class="reviews-count">(<?= $product->review_count ?> ulasan)</span>
                        </div>

                        <div class="product-footer">
                            <div>
                                <div class="price-label">Tarif Sewa</div>
                                <div class="price-value">
                                    <?= $product->getFormattedDailyPrice() ?>
                                    <span class="price-unit">/ hari</span>
                                </div>
                            </div>
                            <a href="<?= base_url('produk/' . urlencode($product->slug)) ?>" class="btn-view-detail">
                                Lihat Detail
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- BUKA LAPAK MITRA BANNER -->
        <div class="mitra-cta">
            <div>
                <h3 class="mitra-cta-title">Punya Kamera Menganggur? Hasilkan Pendapatan di CamHub!</h3>
                <p class="mitra-cta-desc">
                    Buka lapak rental resmi atau titip jual kamera Anda dengan proteksi verifikasi identitas penyewa dan jaminan saldo rekening bersama.
                </p>
            </div>
            <div>
                <a href="<?= base_url('register') ?>" class="btn btn-primary" style="padding:0.85rem 1.75rem; font-size:1rem;">
                    Buka Lapak Sekarang →
                </a>
            </div>
        </div>

    </main>

    <!-- VALUE PROPOSITIONS -->
    <section class="features-banner">
        <div class="features-grid">
            <div class="feature-item">
                <div class="feature-icon-wrap">🔒</div>
                <div>
                    <h4 class="feature-title">Transaksi Aman Escrow</h4>
                    <p class="feature-desc">Dana Anda diamankan di rekening bersama CamHub hingga unit diterima dan dicek kondisinya.</p>
                </div>
            </div>

            <div class="feature-item">
                <div class="feature-icon-wrap">🪪</div>
                <div>
                    <h4 class="feature-title">Verifikasi Identitas Resmi (KYC)</h4>
                    <p class="feature-desc">Seluruh penyewa diverifikasi menggunakan KTP valid untuk menjaga keamanan aset mitra rental.</p>
                </div>
            </div>

            <div class="feature-item">
                <div class="feature-icon-wrap">🏪</div>
                <div>
                    <h4 class="feature-title">Jaringan Mitra Terpercaya</h4>
                    <p class="feature-desc">Pilihan lapak rental kamera di berbagai kota di Indonesia dengan reputasi dan ulasan nyata.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="footer">
        <div class="footer-grid">
            <div class="footer-brand">
                <h3>📷 CamHub</h3>
                <p>
                    Platform ekosistem terpadu untuk sewa kamera bergaransi, jual beli unit terverifikasi, dan pengelolaan lapak mitra fotografi di seluruh Indonesia.
                </p>
                <div style="font-size:0.85rem; color:#94a3b8;">
                    <strong>Dikembangkan oleh Kelompok 1:</strong><br>
                    Yogi Ario Pratama • Muhammad Regieyanto • Ivan Farrel Nanda Kristanto • Krisna Mukti
                </div>
            </div>

            <div class="footer-col">
                <h4>Navigasi</h4>
                <ul>
                    <li><a href="<?= base_url() ?>">Beranda</a></li>
                    <li><a href="<?= base_url('katalog') ?>">Katalog Produk</a></li>
                    <li><a href="<?= base_url('login') ?>">Masuk Akun</a></li>
                    <li><a href="<?= base_url('register') ?>">Daftar Pelanggan</a></li>
                    <li><a href="<?= base_url('api/health') ?>">Status API</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4>Kategori</h4>
                <ul>
                    <?php foreach ($categories as $cat): ?>
                        <li><a href="<?= base_url('katalog?category=' . urlencode($cat->slug)) ?>"><?= htmlspecialchars($cat->name, ENT_QUOTES, 'UTF-8') ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="footer-col">
                <h4>Keamanan & Jaminan</h4>
                <ul>
                    <li><a href="#">Sistem Escrow CamHub</a></li>
                    <li><a href="#">Verifikasi Identitas KYC</a></li>
                    <li><a href="#">Kebijakan Kerusakan & Denda</a></li>
                    <li><a href="#">Panduan Sewa Aman</a></li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            © 2026 CamHub – Camera Marketplace Platform. Kelompok 1 (Yogi Ario, Muhammad Regieyanto, Ivan Farrel, Krisna Mukti). Hak Cipta Dilindungi Undang-Undang.
        </div>
    </footer>

</body>
</html>

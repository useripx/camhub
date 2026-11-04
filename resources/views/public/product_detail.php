<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Detail Produk — CamHub', ENT_QUOTES, 'UTF-8') ?></title>
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

        /* NAVBAR */
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

        .brand-logo span { color: var(--accent-amber); }

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

        .nav-links a:hover { color: var(--accent-amber); }

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

        /* BREADCRUMB */
        .breadcrumb-bar {
            background: #ffffff;
            border-bottom: 1px solid var(--border-color);
            padding: 0.85rem 1.5rem;
        }

        .breadcrumb {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        .breadcrumb a {
            color: var(--text-muted);
            text-decoration: none;
        }

        .breadcrumb a:hover { color: var(--accent-amber); }
        .breadcrumb span.current { color: var(--text-dark); font-weight: 600; }

        /* DETAIL LAYOUT */
        .detail-container {
            max-width: 1200px;
            margin: 2rem auto 4rem;
            padding: 0 1.5rem;
            display: grid;
            grid-template-columns: 1fr 380px;
            gap: 2.5rem;
            width: 100%;
        }

        /* LEFT COLUMN */
        .detail-left {
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        /* IMAGE GALLERY */
        .gallery-wrap {
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 14px;
            padding: 1.25rem;
        }

        .main-img-box {
            width: 100%;
            height: 420px;
            background: #f1f5f9;
            border-radius: 10px;
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
        }

        .main-img-box img {
            max-width: 100%;
            max-height: 100%;
            object-fit: cover;
            transition: opacity 0.2s ease;
        }

        .status-pill {
            position: absolute;
            top: 14px;
            left: 14px;
            background: var(--badge-green-bg);
            color: var(--badge-green);
            padding: 0.35rem 0.85rem;
            border-radius: 6px;
            font-size: 0.82rem;
            font-weight: 700;
        }

        .thumbs-row {
            display: flex;
            gap: 0.75rem;
            overflow-x: auto;
            padding-bottom: 0.5rem;
        }

        .thumb-item {
            width: 80px;
            height: 80px;
            border-radius: 8px;
            border: 2px solid var(--border-color);
            background: #f8fafc;
            cursor: pointer;
            overflow: hidden;
            transition: border-color 0.15s;
            flex-shrink: 0;
        }

        .thumb-item.active, .thumb-item:hover {
            border-color: var(--accent-amber);
        }

        .thumb-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* HEADER INFO */
        .product-meta-header {
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 14px;
            padding: 1.75rem;
        }

        .cat-tag {
            font-size: 0.82rem;
            font-weight: 700;
            color: var(--blue-accent);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.5rem;
        }

        .product-main-title {
            font-size: 1.85rem;
            font-weight: 800;
            color: var(--primary-navy);
            margin-bottom: 0.75rem;
            line-height: 1.25;
        }

        .product-rating-row {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.92rem;
            color: var(--text-dark);
            margin-bottom: 1rem;
        }

        .stars { color: #f59e0b; }
        .rev-link { color: var(--text-muted); text-decoration: none; }
        .rev-link:hover { color: var(--accent-amber); }

        /* TABS SECTION */
        .tabs-card {
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 14px;
            overflow: hidden;
        }

        .tabs-nav {
            display: flex;
            border-bottom: 1px solid var(--border-color);
            background: #f8fafc;
        }

        .tab-btn {
            padding: 1rem 1.5rem;
            font-size: 0.92rem;
            font-weight: 700;
            color: var(--text-muted);
            background: transparent;
            border: none;
            border-bottom: 3px solid transparent;
            cursor: pointer;
            transition: all 0.15s;
        }

        .tab-btn:hover { color: var(--text-dark); }

        .tab-btn.active {
            color: var(--accent-amber);
            border-bottom-color: var(--accent-amber);
            background: #ffffff;
        }

        .tab-content {
            padding: 1.75rem;
            line-height: 1.7;
            color: #334155;
            font-size: 0.95rem;
        }

        .tab-pane { display: none; }
        .tab-pane.active { display: block; }

        .spec-table {
            width: 100%;
            border-collapse: collapse;
        }

        .spec-table tr:nth-child(even) { background: #f8fafc; }
        .spec-table td {
            padding: 0.75rem 1rem;
            border: 1px solid var(--border-color);
            font-size: 0.88rem;
        }
        .spec-table td.spec-name {
            font-weight: 600;
            color: var(--primary-navy);
            width: 35%;
        }

        /* REVIEWS LIST */
        .review-card {
            border-bottom: 1px solid var(--border-color);
            padding: 1rem 0;
        }
        .review-card:last-child { border-bottom: none; }
        .review-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.35rem;
        }
        .reviewer-name { font-weight: 700; font-size: 0.9rem; color: var(--primary-navy); }
        .review-date { font-size: 0.78rem; color: var(--text-muted); }
        .review-text { font-size: 0.88rem; color: #475569; }

        /* STORE / MITRA CARD */
        .store-card {
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 14px;
            padding: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1.5rem;
        }

        .store-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .store-avatar {
            width: 52px;
            height: 52px;
            background: #1e293b;
            color: #ffffff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: 700;
        }

        .store-name {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--primary-navy);
            margin-bottom: 0.2rem;
        }

        .store-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            font-size: 0.75rem;
            font-weight: 700;
            color: #0284c7;
            background: #e0f2fe;
            padding: 0.15rem 0.5rem;
            border-radius: 4px;
        }

        .store-loc {
            font-size: 0.82rem;
            color: var(--text-muted);
            margin-top: 0.25rem;
        }

        /* RIGHT COLUMN (STICKY PRICING CARD) */
        .pricing-card-wrap {
            position: sticky;
            top: 5rem;
            height: fit-content;
        }

        .pricing-card {
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 1.75rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.06);
        }

        .price-hero {
            display: flex;
            align-items: baseline;
            gap: 0.35rem;
            margin-bottom: 0.5rem;
        }

        .price-big {
            font-size: 2rem;
            font-weight: 800;
            color: var(--accent-amber);
            letter-spacing: -0.5px;
        }

        .price-period {
            font-size: 0.95rem;
            color: var(--text-muted);
            font-weight: 500;
        }

        .sale-tag {
            font-size: 0.82rem;
            color: var(--text-muted);
            margin-bottom: 1.25rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #f1f5f9;
        }

        .sale-tag strong { color: var(--primary-navy); }

        .unit-stock-banner {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #166534;
            padding: 0.65rem 0.85rem;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.4rem;
            margin-bottom: 1.25rem;
        }

        /* DURATION SELECTOR (PILLS) */
        .duration-label {
            font-size: 0.88rem;
            font-weight: 700;
            color: var(--primary-navy);
            margin-bottom: 0.5rem;
            display: flex;
            justify-content: space-between;
        }

        .duration-chips {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 0.5rem;
            margin-bottom: 1.25rem;
        }

        .chip-btn {
            background: #f8fafc;
            border: 1px solid var(--border-color);
            padding: 0.6rem 0.35rem;
            border-radius: 8px;
            font-size: 0.82rem;
            font-weight: 700;
            text-align: center;
            cursor: pointer;
            transition: all 0.15s;
            color: var(--text-dark);
        }

        .chip-btn:hover { border-color: var(--accent-amber); }

        .chip-btn.active {
            background: var(--accent-amber);
            border-color: var(--accent-amber);
            color: #ffffff;
        }

        .chip-discount {
            display: block;
            font-size: 0.68rem;
            font-weight: 500;
            color: #16a34a;
        }

        .chip-btn.active .chip-discount { color: #fef3c7; }

        /* DATE INPUTS */
        .dates-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.75rem;
            margin-bottom: 1.25rem;
        }

        .date-box label {
            display: block;
            font-size: 0.78rem;
            font-weight: 600;
            color: var(--text-muted);
            margin-bottom: 0.35rem;
        }

        .date-box input {
            width: 100%;
            padding: 0.6rem;
            font-size: 0.85rem;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            outline: none;
        }

        .date-box input:focus { border-color: var(--accent-amber); }

        /* CALCULATION BREAKDOWN BOX */
        .calc-box {
            background: #f8fafc;
            border: 1px solid var(--border-color);
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1.25rem;
        }

        .calc-row {
            display: flex;
            justify-content: space-between;
            font-size: 0.85rem;
            margin-bottom: 0.4rem;
            color: var(--text-muted);
        }

        .calc-row strong { color: var(--text-dark); }

        .calc-row.discount { color: #16a34a; font-weight: 600; }

        .calc-divider {
            height: 1px;
            background: #e2e8f0;
            margin: 0.65rem 0;
        }

        .calc-row.total {
            font-size: 1.05rem;
            font-weight: 800;
            color: var(--primary-navy);
            margin-bottom: 0;
        }

        .calc-row.total .total-amount { color: var(--accent-amber); }

        /* CTA BUTTONS */
        .btn-booking {
            width: 100%;
            padding: 0.9rem;
            font-size: 1rem;
            font-weight: 800;
            background: var(--accent-amber);
            color: #ffffff;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            display: block;
            margin-bottom: 0.75rem;
            transition: background 0.15s;
        }

        .btn-booking:hover { background: var(--accent-amber-hover); }

        .btn-buy-marketplace {
            width: 100%;
            padding: 0.75rem;
            font-size: 0.88rem;
            font-weight: 700;
            background: #ffffff;
            color: var(--primary-navy);
            border: 1px solid var(--primary-navy);
            border-radius: 10px;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            display: block;
            margin-bottom: 1.25rem;
            transition: all 0.15s;
        }

        .btn-buy-marketplace:hover {
            background: #f1f5f9;
        }

        /* SECURITY GUARANTEES */
        .trust-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            font-size: 0.8rem;
            color: var(--text-muted);
            border-top: 1px solid #f1f5f9;
            padding-top: 1rem;
        }

        .trust-item {
            display: flex;
            align-items: flex-start;
            gap: 0.4rem;
            line-height: 1.4;
        }

        /* RELATED PRODUCTS SECTION */
        .related-section {
            margin-top: 2rem;
        }
        .related-title {
            font-size: 1.35rem;
            font-weight: 800;
            color: var(--primary-navy);
            margin-bottom: 1.25rem;
        }
        .related-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1.25rem;
        }
        .related-card {
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 10px;
            overflow: hidden;
            text-decoration: none;
            color: var(--text-dark);
            transition: transform 0.2s;
        }
        .related-card:hover { transform: translateY(-3px); }
        .related-img { height: 130px; background: #f1f5f9; overflow: hidden; }
        .related-img img { width: 100%; height: 100%; object-fit: cover; }
        .related-body { padding: 0.85rem; }
        .related-name { font-size: 0.9rem; font-weight: 700; color: var(--primary-navy); margin-bottom: 0.35rem; }
        .related-price { font-size: 0.95rem; font-weight: 800; color: var(--accent-amber); }

        /* FOOTER */
        .footer {
            background: var(--primary-navy);
            color: #cbd5e1;
            padding: 3rem 1.5rem 1.5rem;
            margin-top: auto;
        }
        .footer-bottom {
            max-width: 1200px;
            margin: 0 auto;
            text-align: center;
            font-size: 0.82rem;
            color: #64748b;
        }

        @media (max-width: 900px) {
            .detail-container { grid-template-columns: 1fr; }
            .pricing-card-wrap { position: static; }
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
                    <li><a href="<?= base_url() ?>">Beranda</a></li>
                    <li><a href="<?= base_url('katalog') ?>" class="active">Katalog Produk</a></li>
                    <li><a href="<?= base_url('katalog?type=rental') ?>">Sewa Kamera</a></li>
                    <li><a href="<?= base_url('katalog?type=sale') ?>">Jual Beli</a></li>
                </ul>
            </nav>

            <div class="nav-actions">
                <?php if (auth_check()): ?>
                    <a href="<?= base_url('dashboard/' . (auth()['role_name'] === 'admin' ? 'admin' : (auth()['role_name'] === 'partner' ? 'mitra' : 'pelanggan'))) ?>" class="btn btn-outline">
                        👤 Dashboard
                    </a>
                <?php else: ?>
                    <a href="<?= base_url('login') ?>" class="btn btn-outline">Masuk (Login)</a>
                    <a href="<?= base_url('register') ?>" class="btn btn-primary">Daftar Akun</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- BREADCRUMB -->
    <div class="breadcrumb-bar">
        <div class="breadcrumb">
            <a href="<?= base_url() ?>">Beranda</a>
            <span>/</span>
            <a href="<?= base_url('katalog') ?>">Katalog</a>
            <span>/</span>
            <a href="<?= base_url('katalog?category=' . urlencode($product->category_slug ?? '')) ?>"><?= htmlspecialchars($product->category_name ?? 'Kategori', ENT_QUOTES, 'UTF-8') ?></a>
            <span>/</span>
            <span class="current"><?= htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8') ?></span>
        </div>
    </div>

    <!-- DETAIL CONTAINER -->
    <div class="detail-container">

        <!-- LEFT COLUMN: PRESENTATION & SPECS -->
        <div class="detail-left">

            <!-- IMAGE GALLERY -->
            <div class="gallery-wrap">
                <div class="main-img-box">
                    <img id="mainImage" src="<?= htmlspecialchars($product->getPrimaryImageUrl(), ENT_QUOTES, 'UTF-8') ?>" 
                         alt="<?= htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8') ?>">
                    <span class="status-pill">
                        <?= $availableUnitsCount > 0 ? "✓ {$availableUnitsCount} Unit Tersedia" : "⏳ Sedang Disewa" ?>
                    </span>
                </div>

                <div class="thumbs-row">
                    <?php foreach ($product->images as $idx => $img): ?>
                        <?php 
                            $imgSrc = !empty($img['image_path']) ? (str_starts_with($img['image_path'], 'http') ? $img['image_path'] : base_url($img['image_path'])) : $product->getPrimaryImageUrl();
                        ?>
                        <div class="thumb-item <?= $idx === 0 ? 'active' : '' ?>" onclick="switchThumb(this, '<?= htmlspecialchars($imgSrc, ENT_QUOTES, 'UTF-8') ?>')">
                            <img src="<?= htmlspecialchars($imgSrc, ENT_QUOTES, 'UTF-8') ?>" alt="Thumbnail">
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- PRODUCT HEADER META -->
            <div class="product-meta-header">
                <div class="cat-tag"><?= htmlspecialchars($product->category_name ?? 'Kamera', ENT_QUOTES, 'UTF-8') ?></div>
                <h1 class="product-main-title"><?= htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8') ?></h1>

                <div class="product-rating-row">
                    <span class="stars">★★★★★</span>
                    <strong><?= $product->getStarRating() ?></strong>
                    <span style="color:var(--text-muted);">•</span>
                    <a href="#tab-reviews" onclick="activateTab('tab-reviews')" class="rev-link">
                        <?= $product->review_count ?> Ulasan Penyewa
                    </a>
                    <span style="color:var(--text-muted);">•</span>
                    <span style="color:var(--text-muted);">Kondisi: <strong><?= htmlspecialchars($product->condition_note ?? 'Sangat Baik (Terawat)', ENT_QUOTES, 'UTF-8') ?></strong></span>
                </div>
            </div>

            <!-- TABS (DESKRIPSI, SPESIFIKASI, SYARAT, ULASAN) -->
            <div class="tabs-card">
                <div class="tabs-nav">
                    <button class="tab-btn active" onclick="switchTab(this, 'tab-desc')">Deskripsi & Fitur</button>
                    <button class="tab-btn" onclick="switchTab(this, 'tab-specs')">Spesifikasi Unit</button>
                    <button class="tab-btn" onclick="switchTab(this, 'tab-items')">Kelengkapan Sewa</button>
                    <button class="tab-btn" id="btn-tab-reviews" onclick="switchTab(this, 'tab-reviews')">Ulasan (<?= count($product->reviews) ?>)</button>
                </div>

                <div class="tab-content">
                    <!-- TAB 1: DESKRIPSI -->
                    <div id="tab-desc" class="tab-pane active">
                        <p style="margin-bottom:1rem; font-size:1rem; line-height:1.8;">
                            <?= nl2br(htmlspecialchars($product->description ?? 'Deskripsi produk belum ditambahkan.', ENT_QUOTES, 'UTF-8')) ?>
                        </p>
                        <h4 style="font-size:1rem; font-weight:700; color:var(--primary-navy); margin:1.25rem 0 0.5rem;">Kelebihan Utama:</h4>
                        <ul style="padding-left:1.25rem; line-height:1.8;">
                            <li>Sensor CMOS performa tinggi dengan reproduksi warna natural.</li>
                            <li>Sistem autofokus pintar dengan pendeteksi mata (*Eye-AF*).</li>
                            <li>Bodi ergonomis, ringan, dan siap pakai untuk indoor maupun outdoor.</li>
                            <li>Telah melalui inspeksi kebersihan sensor dan kalibrasi fokus sebelum diserahkan.</li>
                        </ul>
                    </div>

                    <!-- TAB 2: SPESIFIKASI -->
                    <div id="tab-specs" class="tab-pane">
                        <table class="spec-table">
                            <tr>
                                <td class="spec-name">Brand & Tipe</td>
                                <td><?= htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8') ?></td>
                            </tr>
                            <tr>
                                <td class="spec-name">Kategori</td>
                                <td><?= htmlspecialchars($product->category_name ?? 'Kamera', ENT_QUOTES, 'UTF-8') ?></td>
                            </tr>
                            <tr>
                                <td class="spec-name">Kondisi Fisik</td>
                                <td><?= htmlspecialchars($product->condition_note ?? 'Grade A (Mulus & Normal)', ENT_QUOTES, 'UTF-8') ?></td>
                            </tr>
                            <tr>
                                <td class="spec-name">Layanan Transaksi</td>
                                <td>
                                    <?= $product->is_rental ? '✓ Sewa Harian' : '' ?>
                                    <?= $product->is_sale ? ' • ✓ Beli Unit Permanen' : '' ?>
                                    <?= $product->is_consignment ? ' • ✓ Titip Jual' : '' ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="spec-name">Nomor Seri Unit</td>
                                <td>
                                    <?php if (!empty($product->units)): ?>
                                        <?= implode(', ', array_map(fn($u) => htmlspecialchars($u['serial_number'], ENT_QUOTES, 'UTF-8'), $product->units)) ?>
                                    <?php else: ?>
                                        Tersedia unit fisik terkalibrasi
                                    <?php endif; ?>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <!-- TAB 3: KELENGKAPAN SEWA -->
                    <div id="tab-items" class="tab-pane">
                        <p style="margin-bottom:0.75rem;">Setiap paket sewa unit ini sudah termasuk perlengkapan siap pakai:</p>
                        <ul style="padding-left:1.25rem; line-height:1.8;">
                            <li>1x Body Kamera <?= htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8') ?></li>
                            <li>2x Baterai Original Rechargeable</li>
                            <li>1x Charger Baterai + Kabel Power</li>
                            <li>1x Kartu Memori High Speed (SD Card 64GB)</li>
                            <li>1x Strap Leher Original CamHub</li>
                            <li>1x Tas Kamera Padded Anti-Guncangan</li>
                        </ul>
                    </div>

                    <!-- TAB 4: ULASAN -->
                    <div id="tab-reviews" class="tab-pane">
                        <?php if (!empty($product->reviews)): ?>
                            <?php foreach ($product->reviews as $rev): ?>
                                <div class="review-card">
                                    <div class="review-header">
                                        <span class="reviewer-name">👤 <?= htmlspecialchars($rev['user_name'] ?? $rev['username'], ENT_QUOTES, 'UTF-8') ?></span>
                                        <span class="review-date"><?= date('d M Y', strtotime($rev['created_at'])) ?></span>
                                    </div>
                                    <div style="color:#f59e0b; font-size:0.8rem; margin-bottom:0.35rem;">
                                        <?= str_repeat('★', (int)$rev['rating']) ?> (<?= $rev['rating'] ?>/5)
                                    </div>
                                    <p class="review-text"><?= htmlspecialchars($rev['comment'] ?? 'Unit sangat terawat dan pengambilan lancar.', ENT_QUOTES, 'UTF-8') ?></p>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div style="text-align:center; padding:2rem; color:var(--text-muted);">
                                🌟 Belum ada ulasan untuk kamera ini. Jadilah penyewa pertama yang memberikan ulasan!
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- TOKO MITRA LAPAK -->
            <div class="store-card">
                <div class="store-info">
                    <div class="store-avatar">🏪</div>
                    <div>
                        <h4 class="store-name"><?= htmlspecialchars($product->store_name ?? 'Lapak Mitra CamHub', ENT_QUOTES, 'UTF-8') ?></h4>
                        <span class="store-badge">✓ Mitra Lapak Terverifikasi</span>
                        <div class="store-loc">
                            📍 <?= htmlspecialchars($product->store_city ?? 'Indonesia', ENT_QUOTES, 'UTF-8') ?>
                            • Rating Lapak: ★ <?= number_format($product->store_rating ?? 5.0, 1) ?>
                        </div>
                    </div>
                </div>

                <div>
                    <a href="<?= base_url('katalog?store=' . $product->store_id) ?>" class="btn btn-outline" style="font-size:0.82rem;">
                        Lihat Lapak →
                    </a>
                </div>
            </div>

            <!-- RELATED PRODUCTS -->
            <?php if (!empty($relatedProducts)): ?>
                <div class="related-section">
                    <h3 class="related-title">Peralatan Terkait Lainnya</h3>
                    <div class="related-grid">
                        <?php foreach ($relatedProducts as $rel): ?>
                            <a href="<?= base_url('produk/' . urlencode($rel->slug)) ?>" class="related-card">
                                <div class="related-img">
                                    <img src="<?= htmlspecialchars($rel->getPrimaryImageUrl(), ENT_QUOTES, 'UTF-8') ?>" 
                                         alt="<?= htmlspecialchars($rel->name, ENT_QUOTES, 'UTF-8') ?>" loading="lazy">
                                </div>
                                <div class="related-body">
                                    <div class="related-name"><?= htmlspecialchars($rel->name, ENT_QUOTES, 'UTF-8') ?></div>
                                    <div class="related-price"><?= $rel->getFormattedDailyPrice() ?> <span style="font-size:0.75rem; color:var(--text-muted); font-weight:normal;">/ hari</span></div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

        </div>

        <!-- RIGHT COLUMN: STICKY BOOKING & PRICING CARD -->
        <aside class="pricing-card-wrap">
            <div class="pricing-card">

                <!-- TARIF SEWA -->
                <div class="price-hero">
                    <span class="price-big" id="displayDailyRate"><?= $product->getFormattedDailyPrice() ?></span>
                    <span class="price-period">/ hari</span>
                </div>

                <?php if ($product->is_sale && $product->sale_price > 0): ?>
                    <div class="sale-tag">
                        Beli unit baru/bekas ini: <strong><?= $product->getFormattedSalePrice() ?></strong>
                    </div>
                <?php endif; ?>

                <!-- UNIT AVAILABILITY -->
                <div class="unit-stock-banner">
                    <?php if ($availableUnitsCount > 0): ?>
                        <span>✓</span>
                        <span><strong><?= $availableUnitsCount ?> Unit Fisik</strong> siap disewa saat ini</span>
                    <?php else: ?>
                        <span style="color:#dc2626;">⏳</span>
                        <span style="color:#dc2626;">Semua unit sedang dalam masa sewa</span>
                    <?php endif; ?>
                </div>

                <!-- DURASI SEWA (PILLS) -->
                <div class="duration-label">
                    <span>Pilihan Durasi Sewa</span>
                    <span style="color:var(--blue-accent); font-size:0.78rem;">Makin lama, makin hemat</span>
                </div>

                <div class="duration-chips">
                    <div class="chip-btn active" onclick="selectDuration(1, this)">
                        1 Hari
                        <span class="chip-discount">Normal</span>
                    </div>
                    <div class="chip-btn" onclick="selectDuration(3, this)">
                        3 Hari
                        <span class="chip-discount">-5%</span>
                    </div>
                    <div class="chip-btn" onclick="selectDuration(7, this)">
                        7 Hari
                        <span class="chip-discount">-10%</span>
                    </div>
                    <div class="chip-btn" onclick="selectDuration(14, this)">
                        14 Hari
                        <span class="chip-discount">-15%</span>
                    </div>
                </div>

                <!-- DATE PICKERS -->
                <div class="dates-grid">
                    <div class="date-box">
                        <label for="startDate">Tanggal Ambil</label>
                        <input type="date" id="startDate" value="<?= date('Y-m-d') ?>" min="<?= date('Y-m-d') ?>" onchange="recalculateDates()">
                    </div>
                    <div class="date-box">
                        <label for="endDate">Tanggal Kembali</label>
                        <input type="date" id="endDate" value="<?= date('Y-m-d', strtotime('+1 day')) ?>" min="<?= date('Y-m-d') ?>" onchange="recalculateDates()">
                    </div>
                </div>

                <!-- BREAKDOWN BOX -->
                <div class="calc-box">
                    <div class="calc-row">
                        <span id="breakdownDurationLabel">Tarif Sewa (1 Hari)</span>
                        <strong id="breakdownBaseFee"><?= $product->getFormattedDailyPrice() ?></strong>
                    </div>
                    <div class="calc-row discount" id="rowDiscount" style="display:none;">
                        <span id="labelDiscountPercent">Diskon Durasi</span>
                        <span id="breakdownDiscount">- Rp 0</span>
                    </div>
                    <div class="calc-row">
                        <span>Biaya Escrow Rekber</span>
                        <strong>Rp 5.000</strong>
                    </div>

                    <div class="calc-divider"></div>

                    <div class="calc-row total">
                        <span>Total Estimasi</span>
                        <span class="total-amount" id="breakdownTotal">
                            <?= 'Rp ' . number_format(($product->daily_price ?? 150000) + 5000, 0, ',', '.') ?>
                        </span>
                    </div>
                </div>

                <!-- CTA BUTTONS -->
                <a href="<?= base_url('checkout/' . $product->slug . '?duration=1') ?>" id="btnBookingAction" class="btn-booking">
                    Sewa Sekarang (Booking)
                </a>

                <?php if ($product->is_sale && $product->sale_price > 0): ?>
                    <a href="<?= base_url('marketplace/buy?product_id=' . $product->id) ?>" class="btn-buy-marketplace">
                        Beli Unit Ini (Rp <?= number_format($product->sale_price, 0, ',', '.') ?>)
                    </a>
                <?php endif; ?>

                <!-- TRUST BADGES -->
                <ul class="trust-list">
                    <li class="trust-item">
                        <span>🔒</span>
                        <span><strong>Transaksi Rekening Bersama:</strong> Dana aman di escrow CamHub hingga unit diperiksa.</span>
                    </li>
                    <li class="trust-item">
                        <span>🪪</span>
                        <span><strong>Wajib Verifikasi KYC:</strong> Identitas penyewa terverifikasi demi keamanan bersama.</span>
                    </li>
                </ul>

            </div>
        </aside>

    </div>

    <!-- FOOTER -->
    <footer class="footer">
        <div class="footer-bottom">
            © 2026 CamHub – Camera Marketplace Platform. Kelompok 1 (Yogi Ario, Muhammad Regieyanto, Ivan Farrel, Krisna Mukti).
        </div>
    </footer>

    <!-- INTERACTIVE JAVASCRIPT LOGIC -->
    <script>
        const DAILY_PRICE = <?= (float)($product->daily_price ?? 150000.0) ?>;
        const ESCROW_FEE = 5000;
        let currentDuration = 1;

        function switchThumb(elem, imgSrc) {
            document.querySelectorAll('.thumb-item').forEach(el => el.classList.remove('active'));
            elem.classList.add('active');
            document.getElementById('mainImage').src = imgSrc;
        }

        function switchTab(btn, tabId) {
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
            btn.classList.add('active');
            document.getElementById(tabId).classList.add('active');
        }

        function activateTab(tabId) {
            const btn = document.getElementById('btn-' + tabId);
            if (btn) {
                switchTab(btn, tabId);
            }
        }

        function formatRupiah(num) {
            return 'Rp ' + Math.round(num).toLocaleString('id-ID');
        }

        function selectDuration(days, chipElem) {
            currentDuration = days;
            document.querySelectorAll('.chip-btn').forEach(c => c.classList.remove('active'));
            if (chipElem) chipElem.classList.add('active');

            // Adjust end date input
            const startInput = document.getElementById('startDate');
            const endInput = document.getElementById('endDate');
            if (startInput.value) {
                const s = new Date(startInput.value);
                s.setDate(s.getDate() + days);
                endInput.value = s.toISOString().split('T')[0];
            }

            calculateBreakdown(days);
        }

        function recalculateDates() {
            const startVal = document.getElementById('startDate').value;
            const endVal = document.getElementById('endDate').value;
            if (!startVal || !endVal) return;

            const s = new Date(startVal);
            const e = new Date(endVal);
            const diffTime = e - s;
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            const days = Math.max(1, diffDays);

            currentDuration = days;

            // Update chip active state if matching standard days
            document.querySelectorAll('.chip-btn').forEach(c => c.classList.remove('active'));
            const matchingChip = Array.from(document.querySelectorAll('.chip-btn')).find(c => c.innerText.includes(days + ' Hari'));
            if (matchingChip) matchingChip.classList.add('active');

            calculateBreakdown(days);
        }

        function calculateBreakdown(days) {
            const baseFee = DAILY_PRICE * days;
            let discountRate = 0;

            if (days >= 14) discountRate = 0.15;
            else if (days >= 7) discountRate = 0.10;
            else if (days >= 3) discountRate = 0.05;

            const discountAmount = Math.round(baseFee * discountRate);
            const subtotal = baseFee - discountAmount;
            const total = subtotal + ESCROW_FEE;

            document.getElementById('breakdownDurationLabel').innerText = `Tarif Sewa (${days} Hari)`;
            document.getElementById('breakdownBaseFee').innerText = formatRupiah(baseFee);

            const rowDiscount = document.getElementById('rowDiscount');
            if (discountRate > 0) {
                rowDiscount.style.display = 'flex';
                document.getElementById('labelDiscountPercent').innerText = `Diskon Durasi (${Math.round(discountRate * 100)}%)`;
                document.getElementById('breakdownDiscount').innerText = `- ${formatRupiah(discountAmount)}`;
            } else {
                rowDiscount.style.display = 'none';
            }

            document.getElementById('breakdownTotal').innerText = formatRupiah(total);

            // Update booking button URL
            const btn = document.getElementById('btnBookingAction');
            const startVal = document.getElementById('startDate').value;
            const endVal = document.getElementById('endDate').value;
            btn.href = `<?= base_url('checkout/' . $product->slug) ?>?duration=${days}&start_date=${startVal}&end_date=${endVal}`;
        }
    </script>
</body>
</html>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Katalog Produk — CamHub', ENT_QUOTES, 'UTF-8') ?></title>
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

        /* BREADCRUMB & PAGE HEADER */
        .page-header-wrap {
            background: #ffffff;
            border-bottom: 1px solid var(--border-color);
            padding: 1.75rem 1.5rem;
        }

        .header-content {
            max-width: 1200px;
            margin: 0 auto;
        }

        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.82rem;
            color: var(--text-muted);
            margin-bottom: 0.5rem;
        }

        .breadcrumb a {
            color: var(--text-muted);
            text-decoration: none;
        }

        .breadcrumb a:hover { color: var(--accent-amber); }

        .catalog-title {
            font-size: 1.85rem;
            font-weight: 800;
            color: var(--primary-navy);
            letter-spacing: -0.3px;
        }

        .catalog-subtitle {
            color: var(--text-muted);
            font-size: 0.95rem;
            margin-top: 0.25rem;
        }

        /* CATALOG LAYOUT */
        .catalog-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem 1.5rem 4rem;
            display: grid;
            grid-template-columns: 280px 1fr;
            gap: 2rem;
            width: 100%;
        }

        /* SIDEBAR FILTER */
        .filter-sidebar {
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 1.5rem;
            height: fit-content;
            position: sticky;
            top: 5rem;
        }

        .filter-group {
            margin-bottom: 1.5rem;
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: 1.25rem;
        }

        .filter-group:last-child {
            margin-bottom: 0;
            border-bottom: none;
            padding-bottom: 0;
        }

        .filter-title {
            font-size: 0.92rem;
            font-weight: 700;
            color: var(--primary-navy);
            margin-bottom: 0.85rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .search-input-wrap input {
            width: 100%;
            padding: 0.65rem 0.85rem;
            font-size: 0.88rem;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            outline: none;
        }

        .search-input-wrap input:focus {
            border-color: var(--accent-amber);
        }

        .filter-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .filter-item label {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 0.88rem;
            cursor: pointer;
            color: var(--text-dark);
        }

        .filter-item input[type="radio"],
        .filter-item input[type="checkbox"] {
            margin-right: 0.5rem;
            accent-color: var(--accent-amber);
        }

        .item-count {
            font-size: 0.75rem;
            color: var(--text-muted);
            background: #f1f5f9;
            padding: 0.15rem 0.5rem;
            border-radius: 10px;
        }

        .price-inputs {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.5rem;
            margin-top: 0.5rem;
        }

        .price-inputs input {
            width: 100%;
            padding: 0.5rem;
            font-size: 0.82rem;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            outline: none;
        }

        .price-inputs input:focus {
            border-color: var(--accent-amber);
        }

        .btn-apply-filter {
            width: 100%;
            background: var(--accent-amber);
            color: #ffffff;
            border: none;
            padding: 0.65rem;
            font-size: 0.9rem;
            font-weight: 700;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.15s;
        }

        .btn-apply-filter:hover { background: var(--accent-amber-hover); }

        .btn-reset-filter {
            width: 100%;
            background: #f1f5f9;
            color: var(--text-muted);
            border: none;
            padding: 0.55rem;
            font-size: 0.85rem;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            margin-top: 0.5rem;
            text-align: center;
            text-decoration: none;
            display: block;
        }

        .btn-reset-filter:hover {
            color: var(--text-dark);
            background: #e2e8f0;
        }

        /* MAIN CONTENT & TOOLBAR */
        .catalog-main {
            display: flex;
            flex-direction: column;
        }

        .catalog-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
            background: #ffffff;
            padding: 0.85rem 1.25rem;
            border-radius: 10px;
            border: 1px solid var(--border-color);
        }

        .results-count {
            font-size: 0.88rem;
            color: var(--text-muted);
        }

        .results-count strong {
            color: var(--primary-navy);
        }

        .sort-control {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.88rem;
            color: var(--text-muted);
        }

        .sort-control select {
            padding: 0.45rem 0.85rem;
            font-size: 0.88rem;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            outline: none;
            color: var(--text-dark);
            background: #ffffff;
            cursor: pointer;
        }

        /* PRODUCTS GRID */
        .catalog-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2.5rem;
        }

        .product-card {
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: all 0.2s ease;
        }

        .product-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 24px rgba(0,0,0,0.06);
            border-color: #cbd5e1;
        }

        .product-img-wrap {
            height: 180px;
            background: #f1f5f9;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .product-img-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .product-card:hover .product-img-wrap img {
            transform: scale(1.04);
        }

        .badge-status {
            position: absolute;
            top: 10px;
            left: 10px;
            background: var(--badge-green-bg);
            color: var(--badge-green);
            padding: 0.2rem 0.55rem;
            border-radius: 5px;
            font-size: 0.72rem;
            font-weight: 700;
        }

        .badge-cat {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(11,25,46,0.85);
            color: #ffffff;
            padding: 0.2rem 0.55rem;
            border-radius: 5px;
            font-size: 0.72rem;
            font-weight: 600;
        }

        .product-info {
            padding: 1.15rem;
            display: flex;
            flex-direction: column;
            flex: 1;
        }

        .product-store {
            font-size: 0.78rem;
            color: var(--text-muted);
            margin-bottom: 0.35rem;
        }

        .product-name {
            font-size: 1.05rem;
            font-weight: 700;
            color: var(--primary-navy);
            margin-bottom: 0.4rem;
            line-height: 1.3;
        }

        .product-brief {
            font-size: 0.82rem;
            color: var(--text-muted);
            margin-bottom: 0.85rem;
            line-height: 1.5;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            flex: 1;
        }

        .rating-row {
            display: flex;
            align-items: center;
            gap: 0.35rem;
            font-size: 0.82rem;
            margin-bottom: 0.85rem;
        }

        .rating-stars { color: #f59e0b; }
        .rating-score { font-weight: 700; }
        .rating-revs { color: var(--text-muted); font-size: 0.78rem; }

        .product-action-bar {
            border-top: 1px solid var(--border-color);
            padding-top: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .price-block {
            display: flex;
            flex-direction: column;
        }

        .price-prefix {
            font-size: 0.7rem;
            color: var(--text-muted);
            font-weight: 600;
            text-transform: uppercase;
        }

        .price-main {
            font-size: 1.15rem;
            font-weight: 800;
            color: var(--accent-amber);
        }

        .price-unit {
            font-size: 0.78rem;
            font-weight: 500;
            color: var(--text-muted);
        }

        .btn-detail {
            padding: 0.45rem 0.9rem;
            font-size: 0.82rem;
            font-weight: 700;
            background: var(--primary-navy);
            color: #ffffff;
            border-radius: 7px;
            text-decoration: none;
            transition: background 0.15s;
        }

        .btn-detail:hover { background: var(--blue-accent); }

        /* PAGINATION */
        .pagination-wrap {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
            margin-top: 1rem;
        }

        .page-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 36px;
            height: 36px;
            padding: 0 0.5rem;
            border-radius: 8px;
            border: 1px solid var(--border-color);
            background: #ffffff;
            color: var(--text-dark);
            text-decoration: none;
            font-size: 0.88rem;
            font-weight: 600;
            transition: all 0.15s;
        }

        .page-link:hover {
            border-color: var(--accent-amber);
            color: var(--accent-amber);
        }

        .page-link.active {
            background: var(--accent-amber);
            border-color: var(--accent-amber);
            color: #ffffff;
        }

        .page-link.disabled {
            opacity: 0.4;
            pointer-events: none;
        }

        /* EMPTY STATE */
        .empty-state {
            background: #ffffff;
            border: 1px dashed var(--border-color);
            border-radius: 12px;
            padding: 4rem 2rem;
            text-align: center;
            color: var(--text-muted);
        }

        .empty-icon { font-size: 3rem; margin-bottom: 1rem; }
        .empty-title { font-size: 1.25rem; font-weight: 700; color: var(--primary-navy); margin-bottom: 0.5rem; }
        .empty-desc { font-size: 0.9rem; max-width: 450px; margin: 0 auto 1.5rem; line-height: 1.5; }

        /* FOOTER */
        .footer {
            background: var(--primary-navy);
            color: #cbd5e1;
            padding: 3rem 1.5rem 1.5rem;
            margin-top: auto;
        }

        .footer-grid {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 3rem;
            margin-bottom: 2rem;
        }

        .footer-brand h3 { color: #ffffff; font-size: 1.35rem; font-weight: 800; margin-bottom: 0.5rem; }
        .footer-brand p { font-size: 0.88rem; line-height: 1.6; color: #94a3b8; }
        .footer-col h4 { color: #ffffff; font-size: 0.92rem; font-weight: 700; margin-bottom: 1rem; text-transform: uppercase; }
        .footer-col ul { list-style: none; display: flex; flex-direction: column; gap: 0.5rem; }
        .footer-col a { color: #94a3b8; text-decoration: none; font-size: 0.85rem; }
        .footer-col a:hover { color: var(--accent-amber); }
        .footer-bottom {
            max-width: 1200px;
            margin: 0 auto;
            border-top: 1px solid rgba(255,255,255,0.1);
            padding-top: 1.5rem;
            text-align: center;
            font-size: 0.82rem;
            color: #64748b;
        }

        @media (max-width: 900px) {
            .catalog-container { grid-template-columns: 1fr; }
            .filter-sidebar { position: static; }
            .footer-grid { grid-template-columns: 1fr; }
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
                    <li><a href="<?= base_url('katalog?type=consignment') ?>">Titip Jual</a></li>
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

    <!-- PAGE HEADER -->
    <section class="page-header-wrap">
        <div class="header-content">
            <div class="breadcrumb">
                <a href="<?= base_url() ?>">Beranda</a>
                <span>/</span>
                <span>Katalog Produk</span>
                <?php if ($selectedCategory): ?>
                    <span>/</span>
                    <span style="color:var(--text-dark); font-weight:600;"><?= htmlspecialchars($selectedCategory->name, ENT_QUOTES, 'UTF-8') ?></span>
                <?php endif; ?>
            </div>
            <h1 class="catalog-title">Katalog Peralatan Kamera & Fotografi</h1>
            <p class="catalog-subtitle">
                Temukan kamera mirrorless, DSLR, lensa prima, dan perlengkapan studio dari lapak mitra terpercaya di seluruh Indonesia.
            </p>
        </div>
    </section>

    <!-- CATALOG MAIN -->
    <div class="catalog-container">

        <!-- FILTER SIDEBAR -->
        <aside class="filter-sidebar">
            <form action="<?= base_url('katalog') ?>" method="GET" id="filterForm">

                <!-- 1. PENCARIAN -->
                <div class="filter-group">
                    <div class="filter-title">Kata Kunci</div>
                    <div class="search-input-wrap">
                        <input type="text" name="q" placeholder="Cari nama kamera..." 
                               value="<?= htmlspecialchars($filters['q'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    </div>
                </div>

                <!-- 2. KATEGORI -->
                <div class="filter-group">
                    <div class="filter-title">Kategori</div>
                    <ul class="filter-list">
                        <li class="filter-item">
                            <label>
                                <span>
                                    <input type="radio" name="category" value="" <?= empty($filters['category']) ? 'checked' : '' ?>>
                                    Semua Kategori
                                </span>
                            </label>
                        </li>
                        <?php foreach ($categories as $cat): ?>
                            <li class="filter-item">
                                <label>
                                    <span>
                                        <input type="radio" name="category" value="<?= htmlspecialchars($cat->slug, ENT_QUOTES, 'UTF-8') ?>" 
                                            <?= ($filters['category'] === $cat->slug || $filters['category'] == $cat->id) ? 'checked' : '' ?>>
                                        <?= htmlspecialchars($cat->name, ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                    <span class="item-count"><?= $cat->product_count ?></span>
                                </label>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- 3. TIPE TRANSAKSI -->
                <div class="filter-group">
                    <div class="filter-title">Tipe Layanan</div>
                    <ul class="filter-list">
                        <li class="filter-item">
                            <label>
                                <span>
                                    <input type="radio" name="type" value="" <?= empty($filters['type']) ? 'checked' : '' ?>>
                                    Semua Layanan
                                </span>
                            </label>
                        </li>
                        <li class="filter-item">
                            <label>
                                <span>
                                    <input type="radio" name="type" value="rental" <?= ($filters['type'] === 'rental') ? 'checked' : '' ?>>
                                    Sewa Kamera
                                </span>
                            </label>
                        </li>
                        <li class="filter-item">
                            <label>
                                <span>
                                    <input type="radio" name="type" value="sale" <?= ($filters['type'] === 'sale') ? 'checked' : '' ?>>
                                    Beli Unit Kamera
                                </span>
                            </label>
                        </li>
                        <li class="filter-item">
                            <label>
                                <span>
                                    <input type="radio" name="type" value="consignment" <?= ($filters['type'] === 'consignment') ? 'checked' : '' ?>>
                                    Titip Jual
                                </span>
                            </label>
                        </li>
                    </ul>
                </div>

                <!-- 4. RENTANG HARGA SEWA HARIAN -->
                <div class="filter-group">
                    <div class="filter-title">Tarif Sewa Harian (Rp)</div>
                    <div class="price-inputs">
                        <input type="number" name="min_price" placeholder="Min (50rb)" step="10000"
                               value="<?= htmlspecialchars((string)($filters['min_price'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                        <input type="number" name="max_price" placeholder="Maks (500rb)" step="10000"
                               value="<?= htmlspecialchars((string)($filters['max_price'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                    </div>
                </div>

                <!-- 5. KETERSEDIAAN -->
                <div class="filter-group">
                    <div class="filter-title">Ketersediaan Unit</div>
                    <ul class="filter-list">
                        <li class="filter-item">
                            <label>
                                <span>
                                    <input type="checkbox" name="available_only" value="1" <?= !empty($filters['available_only']) ? 'checked' : '' ?>>
                                    Hanya Unit Tersedia
                                </span>
                            </label>
                        </li>
                    </ul>
                </div>

                <!-- SUBMIT & RESET -->
                <div style="margin-top:1.25rem;">
                    <button type="submit" class="btn-apply-filter">Terapkan Filter</button>
                    <a href="<?= base_url('katalog') ?>" class="btn-reset-filter">Reset Filter</a>
                </div>

            </form>
        </aside>

        <!-- MAIN CATALOG LIST -->
        <main class="catalog-main">

            <!-- TOOLBAR -->
            <div class="catalog-toolbar">
                <div class="results-count">
                    Menampilkan <strong><?= count($products) ?></strong> dari <strong><?= $total ?></strong> kamera
                    <?php if (!empty($filters['q'])): ?>
                        untuk kata kunci "<em><?= htmlspecialchars($filters['q'], ENT_QUOTES, 'UTF-8') ?></em>"
                    <?php endif; ?>
                </div>

                <div class="sort-control">
                    <label for="sortSelect">Urutkan:</label>
                    <select id="sortSelect" onchange="applySort(this.value)">
                        <option value="popular" <?= ($filters['sort'] === 'popular') ? 'selected' : '' ?>>Paling Populer</option>
                        <option value="rating_desc" <?= ($filters['sort'] === 'rating_desc') ? 'selected' : '' ?>>Rating Tertinggi</option>
                        <option value="price_asc" <?= ($filters['sort'] === 'price_asc') ? 'selected' : '' ?>>Harga Sewa Termurah</option>
                        <option value="price_desc" <?= ($filters['sort'] === 'price_desc') ? 'selected' : '' ?>>Harga Sewa Termahal</option>
                        <option value="newest" <?= ($filters['sort'] === 'newest') ? 'selected' : '' ?>>Terbaru Ditambahkan</option>
                    </select>
                </div>
            </div>

            <!-- PRODUCT GRID -->
            <?php if (!empty($products)): ?>
                <div class="catalog-grid">
                    <?php foreach ($products as $product): ?>
                        <div class="product-card">
                            <div class="product-img-wrap">
                                <img src="<?= htmlspecialchars($product->getPrimaryImageUrl(), ENT_QUOTES, 'UTF-8') ?>" 
                                     alt="<?= htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8') ?>"
                                     loading="lazy">
                                <span class="badge-status"><?= $product->stock_quantity > 0 ? 'Tersedia' : 'Disewa' ?></span>
                                <span class="badge-cat"><?= htmlspecialchars($product->category_name ?? 'Kamera', ENT_QUOTES, 'UTF-8') ?></span>
                                <?php if (!empty($product->is_consignment)): ?>
                                    <span style="position:absolute; bottom:10px; left:10px; background:#fef3c7; color:#b45309; border:1px solid #fde68a; font-size:11px; font-weight:700; padding:3px 8px; border-radius:6px; z-index:2;">🏷️ Titip Jual</span>
                                <?php endif; ?>
                            </div>

                            <div class="product-info">
                                <div class="product-store">
                                    🏪 <?= htmlspecialchars($product->store_name ?? 'Mitra CamHub', ENT_QUOTES, 'UTF-8') ?>
                                    • 📍 <?= htmlspecialchars($product->store_city ?? 'Indonesia', ENT_QUOTES, 'UTF-8') ?>
                                </div>

                                <h3 class="product-name"><?= htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8') ?></h3>
                                <p class="product-brief"><?= htmlspecialchars($product->description ?? '', ENT_QUOTES, 'UTF-8') ?></p>

                                <div class="rating-row">
                                    <span class="rating-stars">★★★★★</span>
                                    <span class="rating-score"><?= $product->getStarRating() ?></span>
                                    <span class="rating-revs">(<?= $product->review_count ?> ulasan)</span>
                                </div>

                                <div class="product-action-bar">
                                    <div class="price-block">
                                        <span class="price-prefix">Sewa Harian</span>
                                        <span class="price-main">
                                            <?= $product->getFormattedDailyPrice() ?>
                                            <span class="price-unit">/ hari</span>
                                        </span>
                                    </div>
                                    <a href="<?= base_url('produk/' . urlencode($product->slug)) ?>" class="btn-detail">
                                        Lihat Detail
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- PAGINATION CONTROLS -->
                <?php if ($lastPage > 1): ?>
                    <div class="pagination-wrap">
                        <?php 
                            $queryBase = $_GET;
                            $buildPageUrl = function($pageNum) use ($queryBase) {
                                $queryBase['page'] = $pageNum;
                                return base_url('katalog?' . http_build_query($queryBase));
                            };
                        ?>

                        <!-- PREV -->
                        <a href="<?= $buildPageUrl($page - 1) ?>" 
                           class="page-link <?= ($page <= 1) ? 'disabled' : '' ?>">
                            ‹ Sebelumnya
                        </a>

                        <!-- PAGE NUMBERS -->
                        <?php for ($p = 1; $p <= $lastPage; $p++): ?>
                            <a href="<?= $buildPageUrl($p) ?>" 
                               class="page-link <?= ($p === $page) ? 'active' : '' ?>">
                                <?= $p ?>
                            </a>
                        <?php endfor; ?>

                        <!-- NEXT -->
                        <a href="<?= $buildPageUrl($page + 1) ?>" 
                           class="page-link <?= ($page >= $lastPage) ? 'disabled' : '' ?>">
                            Selanjutnya ›
                        </a>
                    </div>
                <?php endif; ?>

            <?php else: ?>
                <!-- EMPTY STATE -->
                <div class="empty-state">
                    <div class="empty-icon">🔍</div>
                    <h3 class="empty-title">Tidak Ada Kamera yang Sesuai</h3>
                    <p class="empty-desc">
                        Kami tidak menemukan peralatan kamera yang cocok dengan kriteria filter atau kata kunci Anda. Silakan coba atur ulang filter pencarian.
                    </p>
                    <a href="<?= base_url('katalog') ?>" class="btn btn-primary">
                        Reset Semua Filter
                    </a>
                </div>
            <?php endif; ?>

        </main>

    </div>

    <!-- FOOTER -->
    <footer class="footer">
        <div class="footer-grid">
            <div class="footer-brand">
                <h3>📷 CamHub</h3>
                <p>Platform marketplace kamera terpercaya di Indonesia dengan garansi rekening bersama escrow dan verifikasi identitas resmi.</p>
            </div>
            <div class="footer-col">
                <h4>Navigasi</h4>
                <ul>
                    <li><a href="<?= base_url() ?>">Beranda</a></li>
                    <li><a href="<?= base_url('katalog') ?>">Katalog Produk</a></li>
                    <li><a href="<?= base_url('login') ?>">Masuk Akun</a></li>
                    <li><a href="<?= base_url('register') ?>">Daftar Baru</a></li>
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
                <h4>Tim Pengembang</h4>
                <p style="font-size:0.85rem; color:#94a3b8; line-height:1.6;">
                    Kelompok 1:<br>
                    Yogi Ario Pratama<br>
                    Muhammad Regieyanto<br>
                    Ivan Farrel Nanda Kristanto<br>
                    Krisna Mukti
                </p>
            </div>
        </div>
        <div class="footer-bottom">
            © 2026 CamHub – Camera Marketplace Platform. Hak Cipta Dilindungi Undang-Undang.
        </div>
    </footer>

    <script>
        function applySort(sortVal) {
            const url = new URL(window.location.href);
            url.searchParams.set('sort', sortVal);
            url.searchParams.set('page', '1');
            window.location.href = url.toString();
        }
    </script>
</body>
</html>

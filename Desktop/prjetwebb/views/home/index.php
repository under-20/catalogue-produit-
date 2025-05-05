<?php
// Include header
$activePage = 'home';
require_once __DIR__ . '/../layouts/header.php';
?>

<style>
    /* Hero section styles */
    .hero-section {
        background: linear-gradient(rgba(39, 68, 93, 0.8), rgba(39, 68, 93, 0.8)), url('../public/uploads/hero-bookshop.jpg');
        background-size: cover;
        background-position: center;
        color: #fff;
        padding: 5rem 2rem;
        text-align: center;
        margin-bottom: 3rem;
    }

    .hero-content {
        max-width: 800px;
        margin: 0 auto;
    }

    .hero-title {
        font-size: 3rem;
        margin-bottom: 1rem;
        font-weight: 700;
    }

    .hero-subtitle {
        font-size: 1.5rem;
        margin-bottom: 2rem;
        opacity: 0.9;
    }

    .hero-button {
        display: inline-block;
        background-color: var(--primary-light);
        color: #fff;
        padding: 0.8rem 2rem;
        border-radius: 30px;
        font-size: 1.1rem;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .hero-button:hover {
        background-color: var(--primary-medium);
        transform: translateY(-3px);
    }

    /* Category Filter */
    .category-filter-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 2rem;
        margin-bottom: 2rem;
        text-align: center;
    }

    .category-filter-form {
        display: inline-flex;
        align-items: center;
        background-color: #fff;
        padding: 0.8rem 1.5rem;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
    }

    .category-filter-form label {
        font-weight: 600;
        margin-right: 1rem;
        color: var(--primary-dark);
    }

    .category-filter-form select {
        padding: 0.6rem 1rem;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 1rem;
        min-width: 240px;
        color: #333;
        background-color: #f9f9f9;
        cursor: pointer;
        transition: border-color 0.3s;
    }

    .category-filter-form select:focus {
        outline: none;
        border-color: var(--primary-medium);
    }

    .no-books-message {
        grid-column: 1 / -1;
        text-align: center;
        padding: 2rem;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        font-size: 1.1rem;
        color: #555;
    }

    /* Featured books section */
    .featured-section {
        padding: 2rem;
        max-width: 1200px;
        margin: 0 auto 3rem;
    }

    .section-title {
        font-size: 2rem;
        text-align: center;
        margin-bottom: 2rem;
        color: var(--primary-dark);
        position: relative;
    }

    .section-title::after {
        content: '';
        width: 80px;
        height: 3px;
        background-color: var(--primary-light);
        position: absolute;
        bottom: -10px;
        left: 50%;
        transform: translateX(-50%);
    }

    .book-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 2rem;
    }

    .book-card {
        background-color: #fff;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease;
    }

    .book-card:hover {
        transform: translateY(-8px);
    }

    .book-image {
        width: 100%;
        height: 280px;
        object-fit: cover;
    }

    .book-info {
        padding: 1.2rem;
    }

    .book-title {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: var(--primary-dark);
    }

    .book-category {
        color: var(--primary-medium);
        font-size: 0.9rem;
        margin-bottom: 0.5rem;
    }

    .book-price {
        font-weight: 700;
        font-size: 1.2rem;
        color: #333;
        margin: 0.5rem 0;
    }

    .book-link {
        display: inline-block;
        background-color: var(--primary-medium);
        color: #fff;
        padding: 0.5rem 1rem;
        border-radius: 4px;
        text-decoration: none;
        font-size: 0.9rem;
        margin-top: 0.5rem;
        transition: background-color 0.3s ease;
    }

    .book-link:hover {
        background-color: var(--primary-dark);
    }

    .admin-button {
        background-color: #e74c3c;
        color: white;
        padding: 0.5rem 1.2rem;
        border-radius: 4px;
        font-weight: bold;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .admin-button i {
        font-size: 1.1rem;
    }

    .admin-button:hover {
        background-color: #c0392b;
        color: white;
    }

    @media (max-width: 768px) {
        .hero-title {
            font-size: 2rem;
        }

        .hero-subtitle {
            font-size: 1.2rem;
        }

        .book-grid {
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        }

        .category-filter-form {
            flex-direction: column;
            gap: 0.8rem;
            width: 100%;
        }

        .category-filter-form select {
            width: 100%;
        }
    }
</style>

<!-- Hero Section -->
<section class="hero-section">
    <div class="hero-content">
        <h1 class="hero-title">Bienvenue à la Librairie Bookshop</h1>
        <p class="hero-subtitle">Découvrez notre collection de livres
            exceptionnels pour tous les lecteurs passionnés</p>
        <a href="index.php?action=index&controller=product"
            class="hero-button">Parcourir les Livres</a>
    </div>
</section>

<!-- Category Filter -->
<div class="category-filter-container">
    <form method="GET" action="index.php" class="category-filter-form">
        <label for="category-select">Filtrer par catégorie:</label>
        <select id="category-select" name="category"
            onchange="this.form.submit()">
            <option value="">Toutes les catégories</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?= $category['id_cat'] ?>"
                    <?= ($categoryId == $category['id_cat']) ? 'selected' : '' ?>>
                    <?= str_repeat('— ', $category['level']) . htmlspecialchars($category['nom_cat']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>
</div>

<!-- Suggested Books Section -->
<section class="featured-section">
    <h2 class="section-title">
        <?php if ($selectedCategory): ?>
            Livres de la catégorie:
            <?= htmlspecialchars($selectedCategory['nom_cat']) ?>
        <?php else: ?>
            Livres Recommandés
        <?php endif; ?>
    </h2>

    <div class="book-grid">
        <?php if (!empty($featuredBooks)): ?>
            <?php foreach ($featuredBooks as $book): ?>
                <div class="book-card">
                    <img src="<?= BASE_URL ?>public/uploads/products/<?= htmlspecialchars($book['image']) ?>"
                        alt="<?= htmlspecialchars($book['titre']) ?>" class="book-image"
                        onerror="this.src='<?= BASE_URL ?>public/uploads/products/default.jpg'">
                    <div class="book-info">
                        <h3 class="book-title"><?= htmlspecialchars($book['titre']) ?>
                        </h3>
                        <?php if (!empty($book['category_name'])): ?>
                            <div class="book-category">
                                <i class="fas fa-bookmark"></i>
                                <?= htmlspecialchars($book['category_name']) ?>
                            </div>
                        <?php endif; ?>
                        <div class="book-price"><?= number_format($book['prix'], 2) ?> dt
                        </div>
                        <a href="index.php?action=view&controller=product&id=<?= $book['id_prod'] ?>"
                            class="book-link">Voir détails</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="no-books-message">Aucun livre trouvé dans cette catégorie.</p>
        <?php endif; ?>
    </div>
</section>

<?php
// Include footer
require_once __DIR__ . '/../layouts/footer.php';
?>
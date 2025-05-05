<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container product-detail-container">
  <div class="breadcrumb">
    <a href="/bookshop/public/index.php">Accueil</a>
    <span class="separator">/</span>
    <?php if (isset($category)): ?>
      <a href="/bookshop/public/index.php?category=<?= $category['id_cat'] ?>"><?= htmlspecialchars($category['nom_cat']) ?></a>
      <span class="separator">/</span>
    <?php endif; ?>
    <span class="current"><?= htmlspecialchars($product['titre']) ?></span>
  </div>
  
  <div class="page-header">
    <h1><?= htmlspecialchars($product['titre']) ?></h1>
    <a href="javascript:history.back();" class="btn btn-back">
      <i class="fas fa-arrow-left"></i>
      Retour
    </a>
  </div>

  <?php if (isset($_SESSION['message'])): ?>
    <div
      class="alert alert-success"><?= htmlspecialchars($_SESSION['message']) ?>
    </div>
    <?php unset($_SESSION['message']); ?>
  <?php endif; ?>

  <?php if (isset($_SESSION['error'])): ?><div
      class="alert alert-error"> <?= htmlspecialchars($_SESSION['error']) ?>
    </div>
    <?php unset($_SESSION['error']); ?>
  <?php endif; ?>

  <div class="product-details">
    <div
      class="product-image-container">
      <?php
      $imagePath = "/bookshop/public/uploads/products/" . htmlspecialchars($product['image']);
      if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $imagePath)) {
        $imagePath = "/bookshop/public/uploads/products/default.jpg";
      }
      ?>
      <img src="<?= $imagePath ?>" alt="<?= htmlspecialchars($product['titre']) ?>" class="product-image">
    </div>

    <div class="product-info">
      <div class="book-category">
        <?php if (isset($category)): ?>
          <span class="category-badge">
            <i class="fas fa-bookmark"></i>
            <?= htmlspecialchars($category['nom_cat']) ?>
          </span>
        <?php endif; ?>
      </div>

      <div class="book-price">
        <span class="price-label">Prix:</span>
        <span class="price-value"><?= number_format($product['prix'], 2) ?> dt</span>
      </div>

      <div class="book-status">
        <span class="status-label">Disponibilité:</span>
        <span class="status-badge <?= $product['etat'] ?>">
          <?php
          switch ($product['etat']) {
            case 'stock':
              echo 'En stock';
              if ($product['quantite'] > 0) {
                echo ' <span class="stock-quantity">('.$product['quantite'].' disponible'.($product['quantite'] > 1 ? 's' : '').')</span>';
              }
              break;
            case 'rupture':
              echo 'En rupture';
              break;
            case 'commande':
              echo 'Sur commande';
              break;
            default:
              echo htmlspecialchars($product['etat']);
          }
          ?>
        </span>
      </div>

      <?php if (!empty($product['descrip'])): ?>
        <div class="book-description">
          <h2>Description</h2>
          <p><?= nl2br(htmlspecialchars($product['descrip'])) ?></p>
        </div>
      <?php endif; ?>
      
      <!-- Book Meta Information -->
      <div class="book-meta-info">
        <h2>Détails du produit</h2>
        <div class="meta-grid">
          <div class="meta-item">
            <span class="meta-label">Référence</span>
            <span class="meta-value"><?= htmlspecialchars($product['ref']) ?></span>
          </div>
          <?php if (isset($category)): ?>
          <div class="meta-item">
            <span class="meta-label">Catégorie</span>
            <span class="meta-value"><?= htmlspecialchars($category['nom_cat']) ?></span>
          </div>
          <?php endif; ?>
          <div class="meta-item">
            <span class="meta-label">Disponibilité</span>
            <span class="meta-value">
              <?php if ($product['etat'] === 'rupture'): ?>
                <span class="stock-status out">Non disponible</span>
              <?php elseif ($product['etat'] === 'commande'): ?>
                <span class="stock-status order">Sur commande</span>
              <?php else: ?>
                <span class="stock-status <?= $product['quantite'] > 0 ? 'in' : 'out' ?>">
                  <?= $product['quantite'] ?> en stock
                </span>
              <?php endif; ?>
            </span>
          </div>
          <div class="meta-item">
            <span class="meta-label">Type de produit</span>
            <span class="meta-value">Livre</span>
          </div>
        </div>
      </div>

      <div class="action-buttons">
        <?php
        // Get wishlist controller to check if product is in wishlist
        require_once __DIR__ . '/../../controllers/WishlistController.php';
        $wishlistController = new WishlistController();
        $isInWishlist = $wishlistController->isInWishlist($product['id_prod']);
        ?>
        
        <?php if ($product['etat'] === 'rupture'): ?>
          <button class="btn btn-primary disabled" disabled>
            <i class="fas fa-shopping-cart"></i> Produit indisponible
          </button>
        <?php else: ?>
          <a href="#" class="btn btn-primary">
            <i class="fas fa-shopping-cart"></i> Ajouter au panier
          </a>
        <?php endif; ?>
        
        <?php if ($isInWishlist): ?>
          <form method="post" action="/bookshop/public/index.php?action=remove_from_wishlist">
            <input type="hidden" name="product_id" value="<?= $product['id_prod']; ?>">
            <button type="submit" class="btn btn-wishlist active">
              <i class="fas fa-heart"></i> Dans ma liste de souhaits
            </button>
          </form>
        <?php else: ?>
          <form method="post" action="/bookshop/public/index.php?action=add_to_wishlist">
            <input type="hidden" name="product_id" value="<?= $product['id_prod']; ?>">
            <button type="submit" class="btn btn-wishlist">
              <i class="far fa-heart"></i> Ajouter à ma liste de souhaits
            </button>
          </form>
        <?php endif; ?>
      </div>
      
      <!-- Social Sharing -->
      <div class="social-sharing">
        <span class="share-label">Partager:</span>
        <div class="share-buttons">
          <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) ?>" target="_blank" class="share-btn facebook">
            <i class="fab fa-facebook-f"></i>
          </a>
          <a href="https://twitter.com/intent/tweet?url=<?= urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) ?>&text=<?= urlencode($product['titre']) ?>" target="_blank" class="share-btn twitter">
            <i class="fab fa-twitter"></i>
          </a>
          <a href="https://api.whatsapp.com/send?text=<?= urlencode($product['titre'] . ' - ' . 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) ?>" target="_blank" class="share-btn whatsapp">
            <i class="fab fa-whatsapp"></i>
          </a>
          <a href="mailto:?subject=<?= urlencode($product['titre']) ?>&body=<?= urlencode('Découvrez ce livre: ' . 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) ?>" class="share-btn email">
            <i class="fas fa-envelope"></i>
          </a>
          <button class="share-btn copy-link" id="copyLinkBtn" data-url="<?= 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ?>">
            <i class="fas fa-link"></i>
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

<style>:root
{
  --primary-dark: #27445D;
  --primary-medium: #497D74;
  --primary-light: #71BBB2;
  --background-light: #EFE9D5;
  --background-white: #FBFBFB;
  --error-color: #dc3545;
  --success-color: #28a745;
}

.product-detail-container {
  max-width: 1200px;
  margin: 2rem auto;
  padding: 2rem;
  background-color: var(--background-white);
  border-radius: 8px;
  box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
}

.breadcrumb {
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  gap: 0.5rem;
  margin-bottom: 2rem;
  font-size: 0.9rem;
  color: #666;
}

.breadcrumb a {
  color: var(--primary-medium);
  text-decoration: none;
  transition: color 0.2s ease;
}

.breadcrumb a:hover {
  color: var(--primary-dark);
  text-decoration: underline;
}

.breadcrumb .separator {
  color: #ccc;
}

.breadcrumb .current {
  font-weight: 600;
  color: var(--primary-dark);
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
  padding-bottom: 1rem;
  border-bottom: 2px solid var(--primary-light);
}

.page-header h1 {
  color: var(--primary-dark);
  margin: 0;
  font-size: 2.2rem;
}

.btn-back {
  background-color: var(--background-light);
  color: var(--primary-dark);
  padding: 0.6rem 1.2rem;
  border-radius: 4px;
  text-decoration: none;
  font-weight: 600;
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  transition: all 0.3s ease;
  border: 1px solid var(--primary-medium);
}

.btn-back:hover {
  background-color: var(--primary-light);
  color: white;
  transform: translateY(-2px);
}

.product-details {
  display: flex;
  gap: 2rem;
  margin-bottom: 2rem;
  flex-wrap: wrap;
}

.product-image-container {
  min-width: 300px;
  max-width: 500px;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
  line-height: 0;
  aspect-ratio: 2/3; /* Better book cover ratio */
}

.product-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  object-position: center;
  display: block;
  transition: transform 0.3s ease;
}

.product-image:hover {
  transform: scale(1.03);
}

.product-info {
  flex: 2;
  min-width: 300px;
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.book-category {
  margin-bottom: 0.5rem;
}

.category-badge {
  display: inline-block;
  padding: 0.5rem 1rem;
  background-color: rgba(73, 125, 116, 0.1);
  color: var(--primary-medium);
  border-radius: 30px;
  font-size: 0.9rem;
  font-weight: 600;
}

.category-badge i {
  margin-right: 0.5rem;
}

.book-price {
  font-size: 1.8rem;
  color: var(--primary-dark);
  font-weight: 700;
}

.price-label {
  font-size: 1rem;
  color: #666;
  font-weight: normal;
  margin-right: 0.5rem;
}

.book-status,
.book-ref {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.status-label,
.ref-label {
  font-weight: 600;
  color: var(--primary-dark);
  min-width: 100px;
}

.status-badge {
  display: inline-block;
  padding: 0.5rem 1rem;
  border-radius: 4px;
  font-weight: 600;
  font-size: 0.9rem;
  text-align: center;
}

.stock {
  background-color: rgba(40, 167, 69, 0.2);
  color: #155724;
}

.rupture {
  background-color: rgba(220, 53, 69, 0.2);
  color: #721c24;
}

.commande {
  background-color: rgba(255, 193, 7, 0.2);
  color: #856404;
}

.book-description {
  margin-top: 1rem;
  border-top: 1px solid #eee;
  padding-top: 1rem;
}

.book-description h2 {
  color: var(--primary-dark);
  font-size: 1.4rem;
  margin-bottom: 1rem;
}

.book-description p {
  line-height: 1.6;
  color: #333;
}

.book-meta-info {
  margin-top: 1.5rem;
  padding: 1.5rem;
  background-color: rgba(239, 233, 213, 0.5);
  border-radius: 8px;
  border: 1px solid #e5e0d5;
}

.book-meta-info h2 {
  color: var(--primary-dark);
  font-size: 1.4rem;
  margin-bottom: 1.5rem;
  padding-bottom: 0.5rem;
  border-bottom: 1px solid #e5e0d5;
}

.meta-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 1.5rem;
}

.meta-item {
  display: flex;
  flex-direction: column;
}

.meta-label {
  font-weight: 600;
  color: var(--primary-dark);
  margin-bottom: 0.5rem;
  font-size: 0.9rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.meta-value {
  font-size: 1rem;
  color: #333;
}

.meta-value .stock-status {
  display: inline-block;
  padding: 0.3rem 0.8rem;
  border-radius: 4px;
  font-size: 0.9rem;
  font-weight: 600;
}

.meta-value .stock-status.in {
  background-color: rgba(40, 167, 69, 0.2);
  color: #155724;
}

.meta-value .stock-status.out {
  background-color: rgba(220, 53, 69, 0.2);
  color: #721c24;
}

.meta-value .stock-status.order {
  background-color: rgba(255, 193, 7, 0.2);
  color: #856404;
}

.action-buttons {
  margin-top: 1.5rem;
  display: flex;
  gap: 1rem;
}

.btn-primary {
  background-color: var(--primary-light);
  color: white;
  padding: 0.8rem 1.5rem;
  border-radius: 4px;
  text-decoration: none;
  font-weight: 600;
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  transition: all 0.3s ease;
}

.btn-primary:hover {
  background-color: var(--primary-medium);
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.btn-primary.disabled {
  background-color: #ccc;
  color: #666;
  cursor: not-allowed;
  transform: none;
  box-shadow: none;
}

.btn-primary.disabled:hover {
  background-color: #ccc;
  transform: none;
  box-shadow: none;
}

.alert {
  padding: 1rem;
  border-radius: 4px;
  margin-bottom: 1.5rem;
}

.alert-success {
  background-color: rgba(40, 167, 69, 0.2);
  border: 1px solid rgba(40, 167, 69, 0.3);
  color: #155724;
}

.alert-error {
  background-color: rgba(220, 53, 69, 0.2);
  border: 1px solid rgba(220, 53, 69, 0.3);
  color: #721c24;
}

.stock-quantity {
  font-size: 0.85rem;
  opacity: 0.8;
  font-weight: normal;
  margin-left: 5px;
}

.social-sharing {
  margin-top: 1.5rem;
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  gap: 1rem;
}

.share-label {
  font-weight: 600;
  color: var(--primary-dark);
}

.share-buttons {
  display: flex;
  gap: 0.8rem;
}

.share-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 2.5rem;
  height: 2.5rem;
  border-radius: 50%;
  color: white;
  font-size: 1rem;
  transition: all 0.3s ease;
}

.share-btn:hover {
  transform: translateY(-3px);
  box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
}

.share-btn.facebook {
  background-color: #3b5998;
}

.share-btn.twitter {
  background-color: #1da1f2;
}

.share-btn.whatsapp {
  background-color: #25D366;
}

.share-btn.email {
  background-color: #777;
}

.copy-link {
  background-color: #007bff;
  border: none;
  cursor: pointer;
  position: relative;
}

.copy-tooltip {
  position: absolute;
  background-color: rgba(0, 123, 255, 0.9);
  color: white;
  padding: 0.3rem 0.6rem;
  border-radius: 4px;
  font-size: 0.8rem;
  top: -30px;
  left: 50%;
  transform: translateX(-50%);
  white-space: nowrap;
  z-index: 10;
}

/* Wishlist Button Styles */
.btn-wishlist {
  background-color: #f8f9fa;
  color: var(--primary-dark);
  border: 1px solid var(--primary-medium);
  padding: 0.8rem 1.5rem;
  border-radius: 4px;
  text-decoration: none;
  font-weight: 600;
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  transition: all 0.3s ease;
}

.btn-wishlist:hover {
  background-color: rgba(113, 187, 178, 0.1);
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.btn-wishlist.active {
  background-color: rgba(113, 187, 178, 0.2);
  color: var(--primary-dark);
}

.btn-wishlist.active i {
  color: #e74c3c;
}

@media(max-width: 992px) {
  .product-details {
    flex-direction: column;
  }

  .product-image-container {
    max-width: 100%;
  }
}

@media(max-width: 768px) {
  .product-detail-container {
    padding: 1rem;
    margin: 1rem;
  }

  .page-header {
    flex-direction: column;
    gap: 1rem;
    text-align: center;
  }

  .btn-back {
    width: 100%;
    justify-content: center;
  }

  .action-buttons {
    flex-direction: column;
  }

  .btn-primary {
    width: 100%;
    justify-content: center;
  }

  .btn-wishlist {
    width: 100%;
    justify-content: center;
  }
}

/* Related Books Section */
.related-books-section {
  max-width: 1200px;
  margin: 0 auto 2rem auto;
  padding: 2rem;
  background-color: var(--background-white);
  border-radius: 8px;
  box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
}

.section-title {
  color: var(--primary-dark);
  font-size: 1.8rem;
  text-align: center;
  margin-bottom: 1.5rem;
  position: relative;
}

.section-title::after {
  content: '';
  display: block;
  width: 60px;
  height: 3px;
  background-color: var(--primary-light);
  margin: 0.5rem auto;
}

.placeholder-text {
  text-align: center;
  color: #666;
  font-style: italic;
  padding: 2rem;
  background-color: var(--background-light);
  border-radius: 8px;
}</style>

<!-- Related Books Section -->
<section class="related-books-section">
  <h2 class="section-title">Vous pourriez également aimer</h2>
  <p class="placeholder-text">Les recommandations de livres similaires seront bientôt disponibles.</p>
</section>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

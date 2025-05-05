<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="container" style="max-width: 1200px; margin: 2rem auto; padding: 1rem;">
  <div class="page-header" style="margin-bottom: 2rem; border-bottom: 2px solid var(--primary-medium); padding-bottom: 0.5rem;">
    <h1 style="color: var(--primary-dark); font-size: 2rem; font-weight: 600;">Ma liste de souhaits</h1>
  </div>

  <?php if (isset($_SESSION['message'])): ?>
    <div
      class="alert" style="padding: 1rem; background-color: <?= $_SESSION['message_type'] == 'success' ? '#d4edda' : '#f8d7da' ?>;
            color: <?= $_SESSION['message_type'] == 'success' ? '#155724' : '#721c24' ?>;
            border: 1px solid <?= $_SESSION['message_type'] == 'success' ? '#c3e6cb' : '#f5c6cb' ?>;
            border-radius: 4px; margin-bottom: 1rem;">
      <?= $_SESSION['message']; ?>
      <button type="button" style="float: right; font-weight: 700; border: none; background: transparent; cursor: pointer;" onclick="this.parentElement.style.display='none'">&times;</button>
    </div>
    <?php
    unset($_SESSION['message']);
    unset($_SESSION['message_type']);
    ?>
  <?php endif; ?>

  <?php if (empty($items)): ?>
      <div class="empty-wishlist" style="background-color: var(--background-light); border-radius: 8px; padding: 2rem; text-align: center; margin: 2rem 0; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);"> <i class="fas fa-heart" style="font-size: 3rem; color: var(--primary-medium); margin-bottom: 1rem; display: block;"></i>
      <h3 style="color: var(--primary-dark); margin-bottom: 1rem;">Votre liste de souhaits est vide</h3>
      <p style="margin-bottom: 1.5rem; color: #666;">Parcourez notre catalogue et ajoutez des livres à votre liste de souhaits!</p>
      <a href="index.php" class="browse-btn" style="display: inline-block; background-color: var(--primary-medium); color: white; padding: 0.7rem 1.5rem; text-decoration: none; border-radius: 4px; font-weight: 500; transition: all 0.3s ease;">Parcourir les produits</a>
    </div>
  <?php else: ?>
    <div
      class="wishlist-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 2rem; margin-top: 2rem;">
      <?php foreach ($items as $item): ?>
        <div class="book-card" style="background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1); transition: transform 0.3s ease, box-shadow 0.3s ease;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 15px rgba(0, 0, 0, 0.15)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 3px 10px rgba(0, 0, 0, 0.1)';">
          <div
            class="book-image" style="height: 250px; overflow: hidden; position: relative;">
            <?php if (!empty($item['image'])): ?>
              <img
              src="<?= !strstr($item['image'], 'uploads/') ? 'public/uploads/products/' . $item['image'] : 'public/' . $item['image']; ?>" alt="<?= $item['titre']; ?>" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease;" onmouseover="this.style.transform='scale(1.05)';" onmouseout="this.style.transform='scale(1)';">
            <?php else: ?>
              <div style="height: 100%; background-color: #f5f5f5; display: flex; align-items: center; justify-content: center;">
                <span style="color: #aaa; font-size: 1.2rem;">Pas d'image</span>
              </div>
            <?php endif; ?>
          </div>

          <div class="book-info" style="padding: 1.2rem;">
            <h3 style="color: var(--primary-dark); font-size: 1.2rem; margin-bottom: 0.5rem; font-weight: 600; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;"><?= $item['titre']; ?></h3>
            <p style="color: #666; font-size: 0.95rem; margin-bottom: 1rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; height: 2.8em;"><?= $item['descrip']; ?></p>
            <p
              style="color: var(--primary-medium); font-size: 1.25rem; font-weight: 700; margin-bottom: 1rem;">
              <?= number_format($item['prix'], 2); ?>
              dt</p>

            <div style="display: flex; justify-content: space-between; align-items: center;">
              <a href="index.php?action=view&controller=product&id=<?= $item['id_prod']; ?>" style="background-color: var(--primary-dark); color: white; padding: 0.5rem 1rem; text-decoration: none; border-radius: 4px; font-size: 0.9rem; transition: all 0.3s ease;" onmouseover="this.style.backgroundColor='#1c3346'; this.style.transform='scale(1.05)';" onmouseout="this.style.backgroundColor='var(--primary-dark)'; this.style.transform='scale(1)';">
                Détails
              </a>

              <form method="post" action="index.php?action=remove_from_wishlist">
                <input type="hidden" name="product_id" value="<?= $item['id_prod']; ?>">
                <button type="submit" style="background-color: #dc3545; color: white; border: none; padding: 0.5rem 1rem; border-radius: 4px; cursor: pointer; font-size: 0.9rem; transition: all 0.3s ease;" onmouseover="this.style.backgroundColor='#c82333'; this.style.transform='scale(1.05)';" onmouseout="this.style.backgroundColor='#dc3545'; this.style.transform='scale(1)';">
                  Supprimer
                </button>
              </form>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <style>
      /* Responsive adjustments for the wishlist grid */
      @media(max-width: 992px) {
        .wishlist-grid {
          grid-template-columns: repeat(auto-fill, minmax(230px, 1fr));
          gap: 1.5rem;
        }
      }

      @media(max-width: 576px) {
        .wishlist-grid {
          grid-template-columns: 1fr;
          gap: 1.5rem;
        }

        .book-card .book-info div {
          flex-direction: column;
          gap: 0.8rem;
        }

        .book-card .book-info div a,
        .book-card .book-info div button {
          width: 100%;
          text-align: center;
        }
      }
    </style>
  <?php endif; ?>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>


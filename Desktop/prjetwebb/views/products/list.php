<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container">
    <h1>Gestion des Produits</h1>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($_SESSION['message']) ?>
        </div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error"><?= htmlspecialchars($_SESSION['error']) ?>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <a href="/bookshop/public/index.php?action=create&controller=product"
        class="btn">Ajouter un produit</a>

    <table>
        <thead>
            <tr>
                <th>Image</th>
                <th>Référence</th>
                <th>Titre</th>
                <th>Prix</th>
                <th>Quantité</th>
                <th>Catégorie</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $product): ?>
                <tr>
                    <td>
                        <?php
                        // Corrected image path
                        $imagePath = "/bookshop/public/uploads/" . htmlspecialchars($product['image']);
                        ?>
                        <img src="<?= $imagePath ?>"
                            alt="<?= htmlspecialchars($product['titre']) ?>"
                            style="max-width: 50px;">
                    </td>
                    <td><?= htmlspecialchars($product['ref']) ?></td>
                    <td><?= htmlspecialchars($product['titre']) ?></td>
                    <td><?= number_format($product['prix'], 2) ?> dt</td>
                    <td><?= $product['quantite'] ?></td>
                    <td><?= htmlspecialchars($product['category_name'] ?? '--') ?>
                    </td>
                    <td class="action-buttons">
                        <a
                            href="/bookshop/public/index.php?action=edit&controller=product&id=<?= $product['id_prod'] ?>">Modifier</a>
                        <a href="/bookshop/public/index.php?action=delete&controller=product&id=<?= $product['id_prod'] ?>"
                            onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce produit?')">Supprimer</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($products)): ?>
                <tr>
                    <td colspan="7" class="empty-table">Aucun produit disponible
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <?php if (isset($pagination) && $pagination['totalPages'] > 1): ?>
        <div class="pagination">
            <?php if ($pagination['currentPage'] > 1): ?>
                <a href="/bookshop/public/index.php?action=index&controller=product&page=1"
                    class="page-link">&laquo; First</a>
                <a href="/bookshop/public/index.php?action=index&controller=product&page=<?= $pagination['currentPage'] - 1 ?>"
                    class="page-link">&lsaquo; Prev</a>
            <?php endif; ?>

            <?php
            // Display page numbers with current page highlighted
            $startPage = max(1, $pagination['currentPage'] - 2);
            $endPage = min($pagination['totalPages'], $pagination['currentPage'] + 2);

            for ($i = $startPage; $i <= $endPage; $i++): ?>
                <a href="/bookshop/public/index.php?action=index&controller=product&page=<?= $i ?>"
                    class="page-link <?= ($i == $pagination['currentPage']) ? 'active' : '' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>

            <?php if ($pagination['currentPage'] < $pagination['totalPages']): ?>
                <a href="/bookshop/public/index.php?action=index&controller=product&page=<?= $pagination['currentPage'] + 1 ?>"
                    class="page-link">Next &rsaquo;</a>
                <a href="/bookshop/public/index.php?action=index&controller=product&page=<?= $pagination['totalPages'] ?>"
                    class="page-link">Last &raquo;</a>
            <?php endif; ?>

            <span class="pagination-info">
                Page <?= $pagination['currentPage'] ?> of
                <?= $pagination['totalPages'] ?>
                (<?= $pagination['totalCount'] ?> items)
            </span>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
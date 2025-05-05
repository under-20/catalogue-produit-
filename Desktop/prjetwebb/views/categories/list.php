<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container">
    <h1>Gestion des Catégories</h1>

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

    <a href="/bookshop/public/index.php?action=create&controller=category"
        class="btn">Ajouter une catégorie</a>

    <?php if (!empty($categories)): ?>
        <table>
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Slug</th>
                    <th>Parent</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $cat): ?>
                    <tr class="level-<?= $cat['level'] ?? 0 ?>">
                        <td><?= htmlspecialchars($cat['nom_cat'] ?? '') ?></td>
                        <td><?= htmlspecialchars($cat['slug'] ?? '') ?></td>
                        <td>
                            <?php if (!empty($cat['parentid'])): ?>
                                <?php
                                $parentName = '';
                                foreach ($categories as $parentCat) {
                                    if ($parentCat['id_cat'] == $cat['parentid']) {
                                        $parentName = htmlspecialchars($parentCat['nom_cat']);
                                        break;
                                    }
                                }
                                echo $parentName ?: '--';
                                ?>
                            <?php else: ?>
                                --
                            <?php endif; ?>
                        </td>
                        <td class="action-buttons">
                            <a
                                href="/bookshop/public/index.php?action=edit&controller=category&id=<?= $cat['id_cat'] ?>">Modifier</a>
                            <a href="/bookshop/public/index.php?action=delete&controller=category&id=<?= $cat['id_cat'] ?>"
                                onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette catégorie?')">Supprimer</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if (isset($pagination) && $pagination['totalPages'] > 1): ?>
            <div class="pagination">
                <?php if ($pagination['currentPage'] > 1): ?>
                    <a href="/bookshop/public/index.php?action=index&controller=category&page=1"
                        class="page-link">&laquo; First</a>
                    <a href="/bookshop/public/index.php?action=index&controller=category&page=<?= $pagination['currentPage'] - 1 ?>"
                        class="page-link">&lsaquo; Prev</a>
                <?php endif; ?>

                <?php
                // Display page numbers with current page highlighted
                $startPage = max(1, $pagination['currentPage'] - 2);
                $endPage = min($pagination['totalPages'], $pagination['currentPage'] + 2);

                for ($i = $startPage; $i <= $endPage; $i++): ?>
                    <a href="/bookshop/public/index.php?action=index&controller=category&page=<?= $i ?>"
                        class="page-link <?= ($i == $pagination['currentPage']) ? 'active' : '' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>

                <?php if ($pagination['currentPage'] < $pagination['totalPages']): ?>
                    <a href="/bookshop/public/index.php?action=index&controller=category&page=<?= $pagination['currentPage'] + 1 ?>"
                        class="page-link">Next &rsaquo;</a>
                    <a href="/bookshop/public/index.php?action=index&controller=category&page=<?= $pagination['totalPages'] ?>"
                        class="page-link">Last &raquo;</a>
                <?php endif; ?>

                <span class="pagination-info">
                    Page <?= $pagination['currentPage'] ?> of
                    <?= $pagination['totalPages'] ?>
                    (<?= $pagination['totalCount'] ?> items)
                </span>
            </div>
        <?php endif; ?>

    <?php else: ?>
        <div class="alert alert-info">Aucune catégorie trouvée</div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
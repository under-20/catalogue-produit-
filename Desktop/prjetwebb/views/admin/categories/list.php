<?php
$activePage = 'categories';
require_once __DIR__ . '/../../layouts/admin_header.php';
?>

<div class="admin-container">
    <div class="page-header">
        <h1>Gestion des Catégories</h1>
        <a href="/bookshop/public/index.php?action=create&controller=admin_category"
            class="btn btn-primary">
            <i class="fas fa-plus-circle"></i> Ajouter une catégorie
        </a>
    </div>

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

    <div class="search-box">
        <input type="text" id="categorySearch"
            placeholder="Rechercher une catégorie..." class="search-input">
        <i class="fas fa-search search-icon"></i>
    </div>

    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Slug</th>
                    <th>Parent</th>
                    <th>Produits</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $cat): ?>
                    <tr class="level-<?= $cat['level'] ?? 0 ?>">
                        <td>
                            <?php if (!empty($cat['level']) && $cat['level'] > 0): ?>
                                <span class="indentation"
                                    style="padding-left: <?= $cat['level'] * 20 ?>px;">└─
                                </span>
                            <?php endif; ?>
                            <?= htmlspecialchars($cat['nom_cat'] ?? '') ?>
                        </td>
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
                                <span class="badge badge-primary">Principale</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (isset($cat['product_count'])): ?>
                                <span
                                    class="badge badge-info"><?= $cat['product_count'] ?></span>
                            <?php else: ?>
                                <span class="badge">0</span>
                            <?php endif; ?>
                        </td>
                        <td class="action-buttons">
                            <a href="/bookshop/public/index.php?action=edit&controller=admin_category&id=<?= $cat['id_cat'] ?>"
                                class="action-btn edit-btn" title="Modifier">
                                <i class="fas fa-edit"></i>
                            </a>
                            
                            <a href="/bookshop/public/index.php?action=delete&controller=admin_category&id=<?= $cat['id_cat'] ?>"
                                class="action-btn delete-btn"
                                onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette catégorie? <?= isset($cat['product_count']) && $cat['product_count'] > 0 ? 'Cette action va décatégoriser ' . $cat['product_count'] . ' produit(s).' : '' ?>')"
                                title="Supprimer">
                                <i class="fas fa-trash-alt"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($categories)): ?>
                    <tr>
                        <td colspan="5" class="empty-table">Aucune catégorie
                            disponible</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if (isset($pagination) && $pagination['totalPages'] > 1): ?>
        <div class="pagination">
            <?php if ($pagination['currentPage'] > 1): ?>
                <a href="/bookshop/public/index.php?action=list&controller=admin_category&page=1"
                    class="page-link">&laquo; First</a>
                <a href="/bookshop/public/index.php?action=list&controller=admin_category&page=<?= $pagination['currentPage'] - 1 ?>"
                    class="page-link">&lsaquo; Prev</a>
            <?php endif; ?>

            <?php
            // Display page numbers with current page highlighted
            $startPage = max(1, $pagination['currentPage'] - 2);
            $endPage = min($pagination['totalPages'], $pagination['currentPage'] + 2);

            for ($i = $startPage; $i <= $endPage; $i++): ?>
                <a href="/bookshop/public/index.php?action=list&controller=admin_category&page=<?= $i ?>"
                    class="page-link <?= ($i == $pagination['currentPage']) ? 'active' : '' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>

            <?php if ($pagination['currentPage'] < $pagination['totalPages']): ?>
                <a href="/bookshop/public/index.php?action=list&controller=admin_category&page=<?= $pagination['currentPage'] + 1 ?>"
                    class="page-link">Next &rsaquo;</a>
                <a href="/bookshop/public/index.php?action=list&controller=admin_category&page=<?= $pagination['totalPages'] ?>"
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

<style>
    .admin-container {
        max-width: 1200px;
        margin: 2rem auto;
        padding: 2rem;
        background-color: var(--background-white);
        border-radius: 8px;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
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
        border: none;
    }

    .btn {
        padding: 0.8rem 1.5rem;
        border-radius: 4px;
        text-decoration: none;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
    }

    .btn-primary {
        background-color: var(--primary-light);
        color: white;
    }

    .btn-primary:hover {
        background-color: var(--primary-medium);
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .search-box {
        position: relative;
        margin-bottom: 1.5rem;
    }

    .search-input {
        width: 100%;
        padding: 0.8rem 1rem 0.8rem 2.5rem;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 1rem;
    }

    .search-icon {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: #777;
    }

    .table-responsive {
        overflow-x: auto;
    }

    .admin-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 1rem;
    }

    .admin-table th,
    .admin-table td {
        padding: 1rem;
        text-align: left;
        border-bottom: 1px solid #eee;
    }

    .admin-table th {
        background-color: var(--background-light);
        color: var(--primary-dark);
        font-weight: 600;
    }

    .admin-table tbody tr:hover {
        background-color: rgba(113, 187, 178, 0.05);
    }

    .indentation {
        color: var(--primary-medium);
        font-family: monospace;
    }

    .badge {
        display: inline-block;
        padding: 0.3rem 0.6rem;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.85rem;
        background-color: #f0f0f0;
        color: #666;
    }

    .badge-primary {
        background-color: rgba(113, 187, 178, 0.2);
        color: var(--primary-dark);
    }

    .badge-info {
        background-color: rgba(13, 202, 240, 0.2);
        color: #055160;
    }

    .action-buttons {
        display: flex;
        gap: 0.5rem;
    }

    .action-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 2.2rem;
        height: 2.2rem;
        border-radius: 50%;
        color: white;
        transition: all 0.3s ease;
    }

    .edit-btn {
        background-color: var(--primary-light);
    }

    .edit-btn:hover {
        background-color: var(--primary-medium);
        transform: translateY(-2px);
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .view-btn {
        background-color: var(--primary-dark);
    }

    .view-btn:hover {
        background-color: #1e3a4f;
        transform: translateY(-2px);
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .delete-btn {
        background-color: var(--error-color);
    }

    .delete-btn:hover {
        background-color: #c82333;
        transform: translateY(-2px);
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .empty-table {
        text-align: center;
        padding: 2rem;
        font-style: italic;
        color: #777;
    }

    /* Pagination Styles */
    .pagination {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        align-items: center;
        margin: 2rem 0;
        gap: 0.5rem;
    }

    .page-link {
        display: inline-block;
        padding: 0.5rem 1rem;
        background: var(--background-light);
        color: var(--primary-dark);
        text-decoration: none;
        border: 1px solid var(--primary-medium);
        border-radius: 4px;
        min-width: 2.5rem;
        text-align: center;
        transition: all 0.2s ease;
    }

    .page-link:hover {
        background: var(--primary-light);
        color: var(--text-light);
    }

    .page-link.active {
        background: var(--primary-dark);
        color: var(--text-light);
        font-weight: bold;
    }

    .pagination-info {
        margin-left: 1rem;
        color: #666;
        font-size: 0.9rem;
    }

    @media (max-width: 992px) {
        .action-buttons {
            flex-direction: column;
            gap: 0.3rem;
        }

        .action-btn {
            width: 2rem;
            height: 2rem;
        }
    }

    @media (max-width: 768px) {
        .admin-container {
            padding: 1rem;
            margin: 1rem;
        }

        .page-header {
            flex-direction: column;
            gap: 1rem;
            text-align: center;
        }

        .btn {
            width: 100%;
            justify-content: center;
        }

        .admin-table th:nth-child(3),
        .admin-table td:nth-child(3),
        .admin-table th:nth-child(4),
        .admin-table td:nth-child(4) {
            display: none;
        }

        .pagination {
            flex-direction: column;
        }
    }
</style>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const searchInput = document.getElementById('categorySearch');
        const tableRows = document.querySelectorAll('.admin-table tbody tr');

        searchInput.addEventListener('input', function () {
            const searchQuery = this.value.toLowerCase();

            tableRows.forEach(row => {
                const categoryName = row.children[0].textContent.toLowerCase();
                const categorySlug = row.children[1].textContent.toLowerCase();

                if (categoryName.includes(searchQuery) || categorySlug.includes(searchQuery)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    });
</script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
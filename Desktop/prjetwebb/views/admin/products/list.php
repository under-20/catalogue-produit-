<?php
$activePage = 'products';
require_once __DIR__ . '/../../layouts/admin_header.php';
?>

<div class="admin-container">
    <div class="page-header">
        <h1>Gestion des Produits</h1>
        <a href="/bookshop/public/index.php?action=create&controller=admin_product" class="btn btn-primary">
            <i class="fas fa-plus-circle"></i>
            Ajouter un produit
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

    <div class="filter-controls">
        <div class="search-box">
            <input type="text" id="productSearch" placeholder="Rechercher un produit..." class="search-input">
            <i class="fas fa-search search-icon"></i>
        </div>
        <div class="filter-dropdown">
            <select id="categoryFilter" class="filter-select">
                <option value="">Toutes les catégories</option>
                <?php foreach ($categories as $category): ?>
                    <option
                        value="<?= $category['id_cat'] ?>"><?= htmlspecialchars($category['nom_cat']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Référence</th>
                    <th>Titre</th>
                    <th>Prix</th>
                    <th>Quantité</th>
                    <th>Catégorie</th>
                    <th>État</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td
                            class="product-image">
                            <?php
                            $imagePath = "/bookshop/public/uploads/" . htmlspecialchars($product['image']);
                            ?>
                            <img src="<?= $imagePath ?>" alt="<?= htmlspecialchars($product['titre']) ?>" class="thumbnail">
                        </td>
                        <td><?= htmlspecialchars($product['ref']) ?></td>
                        <td><?= htmlspecialchars($product['titre']) ?></td>
                        <td>
                            <?= number_format($product['prix'], 2) ?>
                            dt</td>
                        <td>
                            <div class="quantity-control">
                                <span
                                    class="quantity-badge <?= $product['quantite'] > 0 ? 'in-stock' : 'out-of-stock' ?>"><?= $product['quantite'] ?>
                                </span>
                            </div>
                        </td>
                        <td
                            data-category-id="<?= $product['id_cat'] ?? '' ?>"><?= htmlspecialchars($product['category_name'] ?? 'Non catégorisé') ?>
                        </td>
                        <td>
                            <span
                                class="status-badge <?= $product['etat'] ?>"><?php
                                  switch ($product['etat']) {
                                      case 'stock':
                                          echo 'En stock';
                                          break;
                                      case 'rupture':
                                          echo 'En rupture';
                                          break;
                                      case 'commande':
                                          echo 'Sur commande';
                                          break;
                                      default:
                                          echo $product['etat'];
                                  }
                                  ?>
                            </span>
                        </td>
                        <td class="action-buttons">
                            <a href="/bookshop/public/index.php?action=edit&controller=admin_product&id=<?= $product['id_prod'] ?>" class="action-btn edit-btn" title="Modifier">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="/bookshop/public/index.php?action=view&controller=admin_product&id=<?= $product['id_prod'] ?>" class="action-btn view-btn" title="Voir">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="/bookshop/public/index.php?action=delete&controller=admin_product&id=<?= $product['id_prod'] ?>" class="action-btn delete-btn" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce produit?')" title="Supprimer">
                                <i class="fas fa-trash-alt"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($products)): ?>
                    <tr>
                        <td colspan="8" class="empty-table">Aucun produit disponible
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if (isset($pagination) && $pagination['totalPages'] > 1): ?>
        <div
            class="pagination">
            <?php if ($pagination['currentPage'] > 1): ?>
                <a href="/bookshop/public/index.php?action=list&controller=admin_product&page=1" class="page-link">&laquo; First</a>
                <a href="/bookshop/public/index.php?action=list&controller=admin_product&page=<?= $pagination['currentPage'] - 1 ?>" class="page-link">&lsaquo; Prev</a>
            <?php endif; ?>

            <?php
            // Display page numbers with current page highlighted
            $startPage = max(1, $pagination['currentPage'] - 2);
            $endPage = min($pagination['totalPages'], $pagination['currentPage'] + 2);

            for ($i = $startPage; $i <= $endPage; $i++): ?>
                <a
                    href="/bookshop/public/index.php?action=list&controller=admin_product&page=<?= $i ?>" class="page-link <?= ($i == $pagination['currentPage']) ? 'active' : '' ?>"><?= $i ?>
                </a>
            <?php endfor; ?>

            <?php if ($pagination['currentPage'] < $pagination['totalPages']): ?>
                <a href="/bookshop/public/index.php?action=list&controller=admin_product&page=<?= $pagination['currentPage'] + 1 ?>" class="page-link">Next &rsaquo;</a>
                <a href="/bookshop/public/index.php?action=list&controller=admin_product&page=<?= $pagination['totalPages'] ?>" class="page-link">Last &raquo;</a>
            <?php endif; ?>

            <span class="pagination-info">
                Page
                <?= $pagination['currentPage'] ?>
                of
                <?= $pagination['totalPages'] ?>
                (
                <?= $pagination['totalCount'] ?>
                items)
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

    .filter-controls {
        display: flex;
        gap: 1rem;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
    }

    .search-box {
        position: relative;
        flex-grow: 1;
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

    .filter-dropdown {
        min-width: 200px;
    }

    .filter-select {
        width: 100%;
        padding: 0.8rem 1rem;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 1rem;
        appearance: none;
        background-image: url("data:image/svg+xml, %3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23777'%3E%3Cpath d='M7 10l5 5 5-5z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 1rem center;
        background-size: 1.5rem;
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

    .product-image {
        width: 80px;
    }

    .thumbnail {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 4px;
        border: 1px solid #ddd;
    }

    .quantity-badge {
        display: inline-block;
        padding: 0.3rem 0.6rem;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.85rem;
    }

    .in-stock {
        background-color: rgba(40, 167, 69, 0.2);
        color: #155724;
    }

    .out-of-stock {
        background-color: rgba(220, 53, 69, 0.2);
        color: #721c24;
    }

    .status-badge {
        display: inline-block;
        padding: 0.3rem 0.6rem;
        border-radius: 4px;
        font-weight: 600;
        font-size: 0.85rem;
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

    @media(max-width: 992px) {
        .action-buttons {
            flex-direction: column;
            gap: 0.3rem;
        }

        .action-btn {
            width: 2rem;
            height: 2rem;
        }
    }

    @media(max-width: 768px) {
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

        .filter-controls {
            flex-direction: column;
        }

        .admin-table th:nth-child(5),
        .admin-table td:nth-child(5),
        .admin-table th:nth-child(6),
        .admin-table td:nth-child(6) {
            display: none;
        }

        .pagination {
            flex-direction: column;
        }
    }
</style>

<script>
    document.addEventListener("DOMContentLoaded", function () {
const searchInput = document.getElementById('productSearch');
const categoryFilter = document.getElementById('categoryFilter');
const tableRows = document.querySelectorAll('.admin-table tbody tr');

// Search functionality
searchInput.addEventListener('input', filterProducts);

// Category filter functionality
categoryFilter.addEventListener('change', filterProducts);

function filterProducts() {
const searchQuery = searchInput.value.toLowerCase();
const selectedCategory = categoryFilter.value;

tableRows.forEach(row => { // For empty rows (no products)
if (row.children.length <= 3) {
return;
}

const title = row.children[2].textContent.toLowerCase();
const reference = row.children[1].textContent.toLowerCase();
const categoryCell = row.querySelector('td:nth-child(6)');
const categoryId = categoryCell ? categoryCell.getAttribute('data-category-id') : '';

const matchesSearch = title.includes(searchQuery) || reference.includes(searchQuery);
const matchesCategory = ! selectedCategory || (categoryId === selectedCategory);

if (matchesSearch && matchesCategory) {
row.style.display = '';
} else {
row.style.display = 'none';
}
});
}
});
</script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>

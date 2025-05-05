<?php
$activePage = 'products';
require_once __DIR__ . '/../../layouts/admin_header.php';
?>

<div class="admin-container">
    <div class="page-header">
        <h1>Détails du Produit</h1>
        <div class="action-buttons-header">
            <a href="/bookshop/public/index.php?action=edit&controller=admin_product&id=<?= $product['id_prod'] ?>"
                class="btn btn-primary">
                <i class="fas fa-edit"></i> Modifier
            </a>
            <a href="/bookshop/public/index.php?action=list&controller=admin_product"
                class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour à la liste
            </a>
        </div>
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

    <div class="product-details">
        <div class="product-image-container">
            <?php
            $imagePath = "/bookshop/public/uploads/products/" . htmlspecialchars($product['image']);
            if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $imagePath)) {
                $imagePath = "/bookshop/public/uploads/products/default.jpg";
            }
            ?>
            <img src="<?= $imagePath ?>"
                alt="<?= htmlspecialchars($product['titre']) ?>"
                class="product-image">
        </div>

        <div class="product-info">
            <div class="info-row">
                <div class="info-label">Référence</div>
                <div class="info-value"><?= htmlspecialchars($product['ref']) ?>
                </div>
            </div>

            <div class="info-row">
                <div class="info-label">Titre</div>
                <div class="info-value">
                    <?= htmlspecialchars($product['titre']) ?></div>
            </div>

            <div class="info-row">
                <div class="info-label">Prix</div>
                <div class="info-value">
                    <?= number_format($product['prix'], 2) ?> dt</div>
            </div>

            <div class="info-row">
                <div class="info-label">Quantité en stock</div>
                <div class="info-value">
                    <span
                        class="quantity-badge <?= $product['quantite'] > 0 ? 'in-stock' : 'out-of-stock' ?>">
                        <?= $product['quantite'] ?>
                    </span>
                </div>
            </div>

            <div class="info-row">
                <div class="info-label">Catégorie</div>
                <div class="info-value">
                    <?php if (isset($category)): ?>
                        <a
                            href="/bookshop/public/index.php?action=view&controller=admin_category&id=<?= $category['id_cat'] ?>">
                            <?= htmlspecialchars($category['nom_cat']) ?>
                        </a>
                    <?php else: ?>
                        <span class="text-muted">Non catégorisé</span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="info-row">
                <div class="info-label">État</div>
                <div class="info-value">
                    <span class="status-badge <?= $product['etat'] ?>">
                        <?php
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
                                echo htmlspecialchars($product['etat']);
                        }
                        ?>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="product-description">
        <h2>Description</h2>
        <div class="description-content">
            <?php if (!empty($product['descrip'])): ?>
                <p><?= nl2br(htmlspecialchars($product['descrip'])) ?></p>
            <?php else: ?>
                <p class="text-muted">Aucune description disponible.</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="action-buttons-footer">
        <a href="/bookshop/public/index.php?action=edit&controller=admin_product&id=<?= $product['id_prod'] ?>"
            class="btn btn-primary">
            <i class="fas fa-edit"></i> Modifier
        </a>
        <a href="/bookshop/public/index.php?action=delete&controller=admin_product&id=<?= $product['id_prod'] ?>"
            class="btn btn-danger"
            onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce produit?')">
            <i class="fas fa-trash-alt"></i> Supprimer
        </a>
        <a href="/bookshop/public/index.php?action=list&controller=admin_product"
            class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour à la liste
        </a>
    </div>
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

    .action-buttons-header {
        display: flex;
        gap: 1rem;
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

    .btn-secondary {
        background-color: var(--background-light);
        color: var(--primary-dark);
        border: 1px solid var(--primary-medium);
    }

    .btn-secondary:hover {
        background-color: #eaeaea;
        transform: translateY(-2px);
    }

    .btn-danger {
        background-color: var(--error-color);
        color: white;
    }

    .btn-danger:hover {
        background-color: #c82333;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .product-details {
        display: flex;
        gap: 2rem;
        margin-bottom: 2rem;
        flex-wrap: wrap;
    }

    .product-image-container {
        flex: 1;
        min-width: 300px;
        max-width: 500px;
        background-color: var(--background-light);
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
    }

    .product-image {
        width: 100%;
        height: auto;
        object-fit: contain;
        display: block;
    }

    .product-info {
        flex: 2;
        min-width: 300px;
    }

    .info-row {
        display: flex;
        margin-bottom: 1.5rem;
        border-bottom: 1px solid #eee;
        padding-bottom: 0.5rem;
    }

    .info-label {
        width: 150px;
        font-weight: 600;
        color: var(--primary-dark);
    }

    .info-value {
        flex: 1;
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

    .product-description {
        margin-bottom: 2rem;
    }

    .product-description h2 {
        color: var(--primary-dark);
        font-size: 1.5rem;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid #eee;
    }

    .description-content {
        line-height: 1.6;
        color: #333;
    }

    .text-muted {
        color: #6c757d;
        font-style: italic;
    }

    .action-buttons-footer {
        display: flex;
        gap: 1rem;
        margin-top: 2rem;
        border-top: 1px solid #eee;
        padding-top: 2rem;
    }

    @media (max-width: 992px) {
        .product-details {
            flex-direction: column;
        }

        .product-image-container {
            max-width: 100%;
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
        }

        .action-buttons-header,
        .action-buttons-footer {
            flex-direction: column;
            width: 100%;
        }

        .btn {
            width: 100%;
            justify-content: center;
        }

        .info-row {
            flex-direction: column;
        }

        .info-label {
            width: 100%;
            margin-bottom: 0.5rem;
        }
    }
</style>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
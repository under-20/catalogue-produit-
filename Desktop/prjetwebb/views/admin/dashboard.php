<?php
$activePage = 'dashboard';
require_once __DIR__ . '/../layouts/admin_header.php';
?>

<div class="admin-container">
    <h1>Tableau de Bord Administration</h1>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($_SESSION['message']) ?></div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <div class="dashboard-stats">
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-book"></i></div>
            <div class="stat-content">
                <h2>Produits</h2>
                <p class="stat-number"><?= $productCount ?? 0 ?></p>
                <a href="/bookshop/public/index.php?action=list&controller=admin_product"
                    class="stat-link">Gérer les produits</a>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-tags"></i></div>
            <div class="stat-content">
                <h2>Catégories</h2>
                <p class="stat-number"><?= $categoryCount ?? 0 ?></p>
                <a href="/bookshop/public/index.php?action=list&controller=admin_category"
                    class="stat-link">Gérer les catégories</a>
            </div>
        </div>
    </div>

    <div class="quick-actions">
        <h2>Actions Rapides</h2>
        <div class="action-buttons">
            <a href="/bookshop/public/index.php?action=create&controller=admin_product"
                class="btn btn-primary">
                <i class="fas fa-plus-circle"></i> Ajouter un produit
            </a>
            <a href="/bookshop/public/index.php?action=create&controller=admin_category"
                class="btn btn-secondary">
                <i class="fas fa-plus-circle"></i> Ajouter une catégorie
            </a>
        </div>
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

    h1 {
        color: var(--primary-dark);
        margin-bottom: 1.5rem;
        text-align: center;
        border-bottom: 2px solid var(--primary-light);
        padding-bottom: 0.5rem;
    }

    .dashboard-stats {
        display: flex;
        flex-wrap: wrap;
        gap: 2rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        flex: 1 1 300px;
        background-color: #fff;
        border-radius: 8px;
        padding: 1.5rem;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        display: flex;
        align-items: center;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
    }

    .stat-icon {
        background-color: var(--primary-light);
        color: white;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
    }

    .stat-icon i {
        font-size: 1.5rem;
    }

    .stat-content {
        flex-grow: 1;
    }

    .stat-content h2 {
        color: var(--primary-dark);
        margin-bottom: 0.5rem;
        font-size: 1.2rem;
    }

    .stat-number {
        font-size: 2rem;
        font-weight: bold;
        color: var(--primary-medium);
        margin-bottom: 0.5rem;
    }

    .stat-link {
        color: var(--primary-light);
        text-decoration: none;
        font-weight: 500;
    }

    .stat-link:hover {
        text-decoration: underline;
    }

    .quick-actions {
        background-color: #fff;
        padding: 1.5rem;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
    }

    .quick-actions h2 {
        color: var(--primary-dark);
        margin-bottom: 1rem;
        font-size: 1.5rem;
    }

    .action-buttons {
        display: flex;
        flex-wrap: wrap;
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
        background-color: var(--primary-dark);
        color: white;
    }

    .btn-secondary:hover {
        background-color: #1e3a4f;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    @media (max-width: 768px) {
        .admin-container {
            padding: 1rem;
            margin: 1rem;
        }

        .dashboard-stats {
            flex-direction: column;
        }

        .action-buttons {
            flex-direction: column;
        }

        .btn {
            width: 100%;
            text-align: center;
            justify-content: center;
        }
    }
</style>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
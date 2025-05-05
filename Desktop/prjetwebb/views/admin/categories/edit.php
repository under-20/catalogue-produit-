<?php
$activePage = 'categories';
require_once __DIR__ . '/../../layouts/admin_header.php';
?>

<div class="admin-container">
    <div class="page-header">
        <h1>Modifier la Catégorie</h1>
        <a href="/bookshop/public/index.php?action=list&controller=admin_category"
            class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour à la liste
        </a>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <?php foreach ($errors as $error): ?>
                <p><?= htmlspecialchars($error) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="admin-form-container">
        <form method="POST"
            action="/bookshop/public/index.php?action=update&controller=admin_category&id=<?= $category['id_cat'] ?>">
            <div class="form-section">
                <div class="form-group">
                    <label for="nom_cat">Nom de la catégorie <span
                            class="required">*</span></label>
                    <input type="text" class="form-control" id="nom_cat"
                        name="nom_cat"
                        value="<?= htmlspecialchars($category['nom_cat']) ?>"
                        required>
                    <p class="form-text">Le nom sera visible pour les
                        utilisateurs</p>
                </div>

                <div class="form-group">
                    <label for="slug">Slug</label>
                    <div class="input-group">
                        <span class="input-group-text">/</span>
                        <input type="text" class="form-control" id="slug"
                            name="slug"
                            value="<?= htmlspecialchars($category['slug'] ?? '') ?>">
                    </div>
                    <p class="form-text">Le slug sera utilisé dans les URLs.
                        Laisser vide pour générer automatiquement.</p>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea class="form-control" id="description"
                        name="description"
                        rows="4"><?= htmlspecialchars($category['description'] ?? '') ?></textarea>
                    <p class="form-text">Une brève description de la catégorie
                        (optionnel)</p>
                </div>

                <div class="form-group">
                    <label for="parentid">Catégorie Parent</label>
                    <select class="form-control" id="parentid" name="parentid">
                        <option value="">-- Catégorie principale --</option>
                        <?php foreach ($categories as $cat): ?>
                            <?php if ($cat['id_cat'] != $category['id_cat']): // Prevent selecting itself as parent ?>
                                <option value="<?= $cat['id_cat'] ?>"
                                    <?= $category['parentid'] == $cat['id_cat'] ? 'selected' : '' ?>>
                                    <?= str_repeat('— ', $cat['level'] ?? 0) ?>        <?= htmlspecialchars($cat['nom_cat']) ?>
                                </option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                    <p class="form-text">Sélectionnez une catégorie parent pour
                        créer une hiérarchie</p>
                </div>
            </div>

            <div class="category-stats">
                <div class="stat-box">
                    <div class="stat-icon"><i class="fas fa-book"></i></div>
                    <div class="stat-content">
                        <h3>Produits associés</h3>
                        <p class="stat-number"><?= $productCount ?? 0 ?></p>
                    </div>
                </div>
                <?php if (!empty($category['parentid'])): ?>
                    <div class="stat-box">
                        <div class="stat-icon parent-icon"><i
                                class="fas fa-sitemap"></i></div>
                        <div class="stat-content">
                            <h3>Catégorie Parent</h3>
                            <p class="stat-text">
                                <?= htmlspecialchars($parentName ?? 'Aucune') ?></p>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if (!empty($childCount) && $childCount > 0): ?>
                    <div class="stat-box">
                        <div class="stat-icon children-icon"><i
                                class="fas fa-stream"></i></div>
                        <div class="stat-content">
                            <h3>Sous-catégories</h3>
                            <p class="stat-number"><?= $childCount ?></p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Enregistrer les modifications
                </button>
                <button type="button" id="generateSlug" class="btn btn-outline">
                    <i class="fas fa-sync-alt"></i> Générer le slug
                </button>
                <a href="/bookshop/public/index.php?action=delete&controller=admin_category&id=<?= $category['id_cat'] ?>"
                    class="btn btn-danger"
                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette catégorie?<?= !empty($productCount) && $productCount > 0 ? ' Cette action va décatégoriser ' . $productCount . ' produit(s).' : '' ?><?= !empty($childCount) && $childCount > 0 ? ' Cette action va aussi supprimer la relation avec ' . $childCount . ' sous-catégorie(s).' : '' ?>')">
                    <i class="fas fa-trash-alt"></i> Supprimer
                </a>
            </div>
        </form>
    </div>
</div>

<style>
    .admin-container {
        max-width: 800px;
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

    .admin-form-container {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        padding: 2rem;
    }

    .form-section {
        margin-bottom: 2rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 600;
        color: var(--primary-medium);
    }

    .required {
        color: var(--error-color);
    }

    .form-control {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 1rem;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        border-color: var(--primary-light);
        outline: none;
        box-shadow: 0 0 0 3px rgba(113, 187, 178, 0.2);
    }

    textarea.form-control {
        min-height: 100px;
        resize: vertical;
    }

    .form-text {
        margin-top: 0.5rem;
        font-size: 0.85rem;
        color: #6c757d;
    }

    .input-group {
        display: flex;
        align-items: center;
    }

    .input-group-text {
        padding: 0.75rem 1rem;
        background-color: #f8f9fa;
        border: 1px solid #ddd;
        border-right: none;
        border-radius: 4px 0 0 4px;
    }

    .input-group .form-control {
        border-radius: 0 4px 4px 0;
    }

    .category-stats {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .stat-box {
        flex: 1 1 200px;
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 1.2rem;
        display: flex;
        align-items: center;
        border: 1px solid #eee;
    }

    .stat-icon {
        width: 50px;
        height: 50px;
        background-color: var(--primary-light);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        margin-right: 1rem;
    }

    .parent-icon {
        background-color: var(--primary-medium);
    }

    .children-icon {
        background-color: var(--primary-dark);
    }

    .stat-content h3 {
        color: var(--primary-dark);
        font-size: 1rem;
        margin-bottom: 0.3rem;
    }

    .stat-number {
        font-size: 1.5rem;
        font-weight: bold;
        color: var(--primary-medium);
        margin: 0;
    }

    .stat-text {
        font-size: 1rem;
        color: #555;
        margin: 0;
    }

    .form-actions {
        display: flex;
        justify-content: space-between;
        gap: 1rem;
        margin-top: 2rem;
        padding-top: 1.5rem;
        border-top: 1px solid #eee;
    }

    .btn {
        padding: 0.8rem 1.5rem;
        border-radius: 4px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
        cursor: pointer;
        border: none;
        text-decoration: none;
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

    .btn-outline {
        background-color: transparent;
        color: var(--primary-medium);
        border: 1px solid var(--primary-light);
    }

    .btn-outline:hover {
        background-color: rgba(113, 187, 178, 0.1);
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

        .form-actions {
            flex-direction: column;
        }

        .btn {
            width: 100%;
            justify-content: center;
        }

        .btn-danger {
            order: -1;
            /* Move the delete button to the top on mobile */
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const categoryNameInput = document.getElementById('nom_cat');
        const slugInput = document.getElementById('slug');
        const generateSlugButton = document.getElementById('generateSlug');

        // Function to convert category name to slug
        function convertToSlug(text) {
            return text
                .toLowerCase()
                .normalize('NFD').replace(/[\u0300-\u036f]/g, '') // Remove accents
                .replace(/[^\w-]+/g, '-') // Replace non-word chars with dash
                .replace(/--+/g, '-') // Replace multiple dashes with single dash
                .replace(/^-+|-+$/g, ''); // Trim dashes from start and end
        }

        // Generate slug button click handler
        generateSlugButton.addEventListener('click', function () {
            if (categoryNameInput.value !== '') {
                slugInput.value = convertToSlug(categoryNameInput.value);
            }
        });
    });
</script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
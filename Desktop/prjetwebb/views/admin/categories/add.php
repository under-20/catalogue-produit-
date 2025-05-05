<?php
$activePage = 'categories';
require_once __DIR__ . '/../../layouts/admin_header.php';
?>

<div class="admin-container">
    <div class="page-header">
        <h1>Ajouter une Catégorie</h1>
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
            action="/bookshop/public/index.php?action=store&controller=admin_category">
            <div class="form-section">
                <div class="form-group">
                    <label for="nom_cat">Nom de la catégorie <span
                            class="required">*</span></label>
                    <input type="text" class="form-control" id="nom_cat"
                        name="nom_cat"
                        value="<?= htmlspecialchars($_SESSION['old_input']['nom_cat'] ?? '') ?>"
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
                            value="<?= htmlspecialchars($_SESSION['old_input']['slug'] ?? '') ?>">
                    </div>
                    <p class="form-text">Le slug sera utilisé dans les URLs.
                        Laisser vide pour générer automatiquement.</p>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea class="form-control" id="description"
                        name="description"
                        rows="4"><?= htmlspecialchars($_SESSION['old_input']['description'] ?? '') ?></textarea>
                    <p class="form-text">Une brève description de la catégorie
                        (optionnel)</p>
                </div>

                <div class="form-group">
                    <label for="parentid">Catégorie Parent</label>
                    <select class="form-control" id="parentid" name="parentid">
                        <option value="">-- Catégorie principale --</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= $category['id_cat'] ?>"
                                <?= ($_SESSION['old_input']['parentid'] ?? '') == $category['id_cat'] ? 'selected' : '' ?>>
                                <?= str_repeat('— ', $category['level'] ?? 0) ?>    <?= htmlspecialchars($category['nom_cat']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <p class="form-text">Sélectionnez une catégorie parent pour
                        créer une hiérarchie</p>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Enregistrer la catégorie
                </button>
                <button type="button" id="generateSlug" class="btn btn-outline">
                    <i class="fas fa-sync-alt"></i> Générer le slug
                </button>
                <a href="/bookshop/public/index.php?action=list&controller=admin_category"
                    class="btn btn-secondary">
                    Annuler
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

        // Auto-generate slug when category name changes
        categoryNameInput.addEventListener('blur', function () {
            if (slugInput.value === '' && this.value !== '') {
                slugInput.value = convertToSlug(this.value);
            }
        });

        // Generate slug button click handler
        generateSlugButton.addEventListener('click', function () {
            if (categoryNameInput.value !== '') {
                slugInput.value = convertToSlug(categoryNameInput.value);
            }
        });
    });
</script>

<?php
// Clear old input data after displaying the form
unset($_SESSION['old_input']);
?>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
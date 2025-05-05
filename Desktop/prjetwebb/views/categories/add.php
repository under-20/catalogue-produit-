<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<style>
    /* === CSS FORMULAIRE AJOUT CATEGORIE === */
    :root {
        --primary-dark: #27445D;
        --primary-medium: #497D74;
        --primary-light: #71BBB2;
        --background-light: #EFE9D5;
        --background-white: #FBFBFB;
        --error-color: #dc3545;
        --success-color: #28a745;
    }

    .container {
        max-width: 800px;
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
        padding-bottom: 10px;
    }

    .alert {
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 4px;
    }

    .alert-error {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    .alert-success {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .form-container {
        background-color: var(--background-white);
        padding: 2rem;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        border: 1px solid #eee;
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

    select.form-control {
        height: auto;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23497D74'%3E%3Cpath d='M7 10l5 5 5-5z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 1rem center;
        background-size: 1.5rem;
    }

    .slug-preview {
        margin-top: 0.5rem;
        font-size: 0.9rem;
        color: var(--primary-medium);
    }

    .slug-preview span {
        font-weight: 600;
        color: var(--primary-dark);
    }

    .btn {
        background-color: var(--primary-light);
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 4px;
        cursor: pointer;
        font-size: 1rem;
        font-weight: 600;
        transition: all 0.3s ease;
        width: 100%;
        margin-top: 1rem;
    }

    .btn:hover {
        background-color: var(--primary-medium);
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    @media (max-width: 768px) {
        .container {
            padding: 1rem;
            margin: 1rem;
        }

        .form-container {
            padding: 1.5rem;
        }
    }
</style>

<div class="container">
    <h1>Ajouter une nouvelle catégorie</h1>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($_SESSION['message']) ?>
        </div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <?php foreach ($errors as $error): ?>
                <p><?= htmlspecialchars($error) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="form-container">
        <form method="POST"
            action="/bookshop/public/index.php?action=store&controller=category">
            <div class="form-group">
                <label for="nom_cat">Nom de la catégorie *</label>
                <input type="text" class="form-control" id="nom_cat"
                    name="nom_cat"
                    value="<?= htmlspecialchars($_SESSION['old_input']['nom_cat'] ?? '') ?>"
                    required>
            </div>

            <div class="form-group">
                <label for="slug">Slug (URL) *</label>
                <input type="text" class="form-control" id="slug" name="slug"
                    value="<?= htmlspecialchars($_SESSION['old_input']['slug'] ?? '') ?>"
                    required>
                <div class="slug-preview">Prévisualisation: <span
                        id="slugPreview">/categorie/<?= htmlspecialchars($_SESSION['old_input']['slug'] ?? '') ?></span>
                </div>
            </div>

            <div class="form-group">
                <label for="parent_id">Catégorie parente</label>
                <select class="form-control" id="parent_id" name="parent_id">
                    <option value="">-- Aucune (catégorie principale) --
                    </option>
                    <?php foreach ($parentCategories as $category): ?>
                        <option value="<?= $category['id_cat'] ?>"
                            <?= ($_SESSION['old_input']['parent_id'] ?? '') == $category['id_cat'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($category['nom_cat']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn">Ajouter la catégorie</button>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const nomCatInput = document.getElementById('nom_cat');
        const slugInput = document.getElementById('slug');
        const slugPreview = document.getElementById('slugPreview');

        // Flag pour suivre les modifications manuelles du slug
        let isSlugManuallyChanged = false;

        // Génération automatique du slug
        nomCatInput.addEventListener('input', function () {
            if (!isSlugManuallyChanged) {
                generateSlugFromName();
            }
        });

        // Marquer le slug comme modifié manuellement
        slugInput.addEventListener('input', function () {
            isSlugManuallyChanged = this.value !== '';
            updateSlugPreview();
        });

        // Fonction pour générer le slug à partir du nom
        function generateSlugFromName() {
            const nameValue = nomCatInput.value;
            const generatedSlug = nameValue
                .toLowerCase()
                .normalize('NFD')  // Décompose les accents
                .replace(/[\u0300-\u036f]/g, '')  // Supprime les accents
                .replace(/[^\w\s-]/g, '')  // Supprime les caractères spéciaux
                .replace(/[\s_-]+/g, '-')  // Remplace espaces et _ par -
                .replace(/^-+|-+$/g, '');  // Supprime les - en début et fin

            slugInput.value = generatedSlug;
            updateSlugPreview();
        }

        // Mettre à jour l'affichage du slug
        function updateSlugPreview() {
            slugPreview.textContent = '/categorie/' + slugInput.value;
        }

        // Initialiser l'affichage
        updateSlugPreview();
    });
</script>

<?php
// Clear old input data after displaying the form
unset($_SESSION['old_input']);
?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
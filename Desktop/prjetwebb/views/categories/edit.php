<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<style>
    /* === CSS FORMULAIRE MODIFICATION CATEGORIE === */
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
        padding-bottom: 0.5rem;
    }

    .alert {
        padding: 1rem;
        margin-bottom: 1.5rem;
        border-radius: 4px;
    }

    .alert-error {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    .form-container {
        background-color: var(--background-white);
        padding: 2rem;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
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
        display: inline-block;
        margin-right: 1rem;
        text-align: center;
        text-decoration: none;
    }

    .btn:hover {
        background-color: var(--primary-medium);
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .btn-cancel {
        background-color: #6c757d;
    }

    .btn-cancel:hover {
        background-color: #5a6268;
    }

    .btn-danger {
        background-color: var(--error-color);
    }

    .btn-danger:hover {
        background-color: #c82333;
    }

    .button-group {
        margin-top: 1.5rem;
        display: flex;
        justify-content: flex-start;
        gap: 1rem;
    }

    @media (max-width: 768px) {
        .container {
            padding: 1rem;
            margin: 1rem;
        }

        .form-container {
            padding: 1.5rem;
        }

        .button-group {
            flex-direction: column;
            gap: 0.5rem;
        }

        .btn {
            width: 100%;
            margin-right: 0;
        }
    }
</style>

<div class="container">
    <h1>Modifier la catégorie</h1>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <?php foreach ($errors as $error): ?>
                <p><?= htmlspecialchars($error) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="form-container">
        <form method="POST"
            action="/bookshop/public/index.php?action=update&controller=category&id=<?= $category['id_cat'] ?>">
            <div class="form-group">
                <label for="nom_cat">Nom de la catégorie *</label>
                <input type="text" class="form-control" id="nom_cat"
                    name="nom_cat"
                    value="<?= htmlspecialchars($category['nom_cat'] ?? '') ?>"
                    required>
            </div>

            <div class="form-group">
                <label for="slug">Slug (URL) *</label>
                <input type="text" class="form-control" id="slug" name="slug"
                    value="<?= htmlspecialchars($category['slug'] ?? '') ?>"
                    required>
                <div class="slug-preview">Prévisualisation: <span
                        id="slugPreview">/categorie/<?= htmlspecialchars($category['slug'] ?? '') ?></span>
                </div>
            </div>

            <div class="form-group">
                <label for="parent_id">Catégorie parente</label>
                <select class="form-control" id="parent_id" name="parent_id">
                    <option value="">-- Aucune (catégorie principale) --
                    </option>
                    <?php foreach ($parentCategories as $cat): ?>
                        <?php if ($cat['id_cat'] != $category['id_cat']): ?>
                            <option value="<?= $cat['id_cat'] ?>"
                                <?= ($category['parentid'] ?? null) == $cat['id_cat'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['nom_cat']) ?>
                            </option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="button-group">
                <button type="submit" class="btn">Enregistrer les
                    modifications</button>
                <a href="/bookshop/public/index.php?action=index&controller=category"
                    class="btn btn-cancel">Annuler</a>
                <a href="/bookshop/public/index.php?action=delete&controller=category&id=<?= $category['id_cat'] ?>"
                    class="btn btn-danger"
                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette catégorie?')">Supprimer</a>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('nom_cat').addEventListener('input', function () {
        if (!document.getElementById('slug')._changed) {
            const slug = this.value
                .toLowerCase()
                .replace(/[^\w\s-]/g, '')
                .replace(/[\s_-]+/g, '-')
                .replace(/^-+|-+$/g, '');
            document.getElementById('slug').value = slug;
            document.getElementById('slugPreview').textContent = '/categorie/' + slug;
        }
    });

    document.getElementById('slug').addEventListener('input', function () {
        this._changed = true;
        document.getElementById('slugPreview').textContent = '/categorie/' + this.value;
    });
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
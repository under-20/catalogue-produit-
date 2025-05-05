<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<style>
    /* === GLOBAL STYLES === */
    :root {
        --primary-dark: #27445D;
        --primary-medium: #497D74;
        --primary-light: #71BBB2;
        --background-light: #EFE9D5;
        --background-white: #FBFBFB;
        --error-color: #dc3545;
        --success-color: #28a745;
    }

    /* === LAYOUT STYLES === */
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

    /* === ALERT MESSAGES === */
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

    /* === FORM STYLES === */
    .form-container {
        background-color: var(--background-white);
        padding: 2rem;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        border: 1px solid var(--background-light);
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
        background-color: var(--background-white);
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

    textarea.form-control {
        min-height: 100px;
        resize: vertical;
    }

    /* === BUTTON STYLES === */
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

    /* === FILE INPUT STYLES === */
    input[type="file"].form-control {
        padding: 0.5rem;
        border: 1px dashed var(--primary-medium);
    }

    /* === RESPONSIVE ADJUSTMENTS === */
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
    <h1>Ajouter un nouveau produit</h1>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <?php foreach ($errors as $error): ?>
                <p><?= htmlspecialchars($error) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="form-container">
        <form method="POST"
            action="/bookshop/public/index.php?action=store&controller=product"
            enctype="multipart/form-data">
            <div class="form-group">
                <label for="ref">Référence *</label>
                <input type="text" class="form-control" id="ref" name="ref"
                    required>
            </div>

            <div class="form-group">
                <label for="titre">Titre *</label>
                <input type="text" class="form-control" id="titre" name="titre"
                    required>
            </div>

            <div class="form-group">
                <label for="descrip">Description</label>
                <textarea class="form-control" id="descrip" name="descrip"
                    rows="3"></textarea>
            </div>

            <div class="form-group">
                <label for="prix">Prix *</label>
                <input type="number" step="0.01" class="form-control" id="prix"
                    name="prix" required>
            </div>

            <div class="form-group">
                <label for="quantite">Quantité</label>
                <input type="number" class="form-control" id="quantite"
                    name="quantite" value="0">
            </div>

            <div class="form-group">
                <label for="etat">État</label>
                <select class="form-control" id="etat" name="etat">
                    <option value="stock">En stock</option>
                    <option value="rupture">En rupture</option>
                    <option value="commande">Sur commande</option>
                </select>
            </div>

            <div class="form-group">
                <label for="id_cat">Catégorie</label>
                <select class="form-control" id="id_cat" name="id_cat">
                    <option value="">-- Aucune catégorie --</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= $category['id_cat'] ?>">
                            <?= htmlspecialchars($category['nom_cat']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="image">Image</label>
                <input type="file" class="form-control" id="image" name="image">
            </div>

            <button type="submit" class="btn">Ajouter le produit</button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
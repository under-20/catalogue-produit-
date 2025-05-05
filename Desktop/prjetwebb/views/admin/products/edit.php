<?php
$activePage = 'products';
require_once __DIR__ . '/../../layouts/admin_header.php';
?>

<div class="admin-container">
    <div class="page-header">
        <h1>Modifier le Produit</h1>
        <a href="/bookshop/public/index.php?action=list&controller=admin_product"
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
            action="/bookshop/public/index.php?action=update&controller=admin_product&id=<?= $product['id_prod'] ?>"
            enctype="multipart/form-data">
            <div class="form-grid">
                <div class="form-section">
                    <h2>Informations Générales</h2>

                    <div class="form-group">
                        <label for="ref">Référence <span
                                class="required">*</span></label>
                        <input type="text" class="form-control" id="ref"
                            name="ref"
                            value="<?= htmlspecialchars($product['ref']) ?>"
                            required>
                    </div>

                    <div class="form-group">
                        <label for="titre">Titre <span
                                class="required">*</span></label>
                        <input type="text" class="form-control" id="titre"
                            name="titre"
                            value="<?= htmlspecialchars($product['titre']) ?>"
                            required>
                    </div>

                    <div class="form-group">
                        <label for="descrip">Description</label>
                        <textarea class="form-control" id="descrip"
                            name="descrip"
                            rows="5"><?= htmlspecialchars($product['descrip']) ?></textarea>
                    </div>
                </div>

                <div class="form-section">
                    <h2>Prix et Stock</h2>

                    <div class="form-group">
                        <label for="prix">Prix <span
                                class="required">*</span></label>
                        <div class="input-group">
                            <input type="number" step="0.01"
                                class="form-control" id="prix" name="prix"
                                value="<?= htmlspecialchars($product['prix']) ?>"
                                required>
                            <span class="input-group-text">dt</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="quantite">Quantité en stock</label>
                        <input type="number" class="form-control" id="quantite"
                            name="quantite"
                            value="<?= htmlspecialchars($product['quantite']) ?>">
                    </div>

                    <div class="form-group">
                        <label for="etat">État du stock</label>
                        <select class="form-control" id="etat" name="etat">
                            <option value="stock" <?= $product['etat'] === 'stock' ? 'selected' : '' ?>>En stock</option>
                            <option value="rupture"
                                <?= $product['etat'] === 'rupture' ? 'selected' : '' ?>>En rupture</option>
                            <option value="commande"
                                <?= $product['etat'] === 'commande' ? 'selected' : '' ?>>Sur commande</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="id_cat">Catégorie</label>
                        <select class="form-control" id="id_cat" name="id_cat">
                            <option value="">-- Aucune catégorie --</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= $category['id_cat'] ?>"
                                    <?= $product['id_cat'] == $category['id_cat'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($category['nom_cat']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-section full-width">
                    <h2>Image du Produit</h2>

                    <div class="form-group image-upload-container">
                        <label>Image actuelle</label>
                        <div class="current-image-container">
                            <?php
                            $imagePath = "/bookshop/public/uploads/" . htmlspecialchars($product['image']);
                            ?>
                            <img src="<?= $imagePath ?>"
                                alt="<?= htmlspecialchars($product['titre']) ?>"
                                class="current-image">
                            <p class="image-filename">
                                <?= htmlspecialchars($product['image']) ?></p>
                        </div>

                        <label for="image" class="mt-3">Nouvelle image</label>
                        <div class="image-preview-container">
                            <div id="imagePreview" class="image-preview">
                                <div class="upload-placeholder">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <p>Cliquez ou déposez une nouvelle image ici
                                    </p>
                                </div>
                            </div>
                            <input type="file" class="form-control image-upload"
                                id="image" name="image" accept="image/*">
                        </div>
                        <p class="form-text">Formats acceptés: JPG, PNG, GIF.
                            Taille max: 2 Mo. Laissez vide pour conserver
                            l'image actuelle.</p>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Enregistrer les modifications
                </button>
                <a href="/bookshop/public/index.php?action=list&controller=admin_product"
                    class="btn btn-secondary">
                    Annuler
                </a>
                <a href="/bookshop/public/index.php?action=delete&controller=admin_product&id=<?= $product['id_prod'] ?>"
                    class="btn btn-danger"
                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce produit?')">
                    <i class="fas fa-trash-alt"></i> Supprimer
                </a>
            </div>
        </form>
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

    .admin-form-container {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        padding: 2rem;
    }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2rem;
    }

    .form-section {
        margin-bottom: 2rem;
    }

    .form-section h2 {
        color: var(--primary-medium);
        font-size: 1.3rem;
        margin-bottom: 1.5rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid #eee;
    }

    .full-width {
        grid-column: 1 / -1;
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

    .mt-3 {
        margin-top: 1.5rem;
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

    .required {
        color: var(--error-color);
    }

    textarea.form-control {
        min-height: 150px;
        resize: vertical;
    }

    .input-group {
        display: flex;
        align-items: center;
    }

    .input-group-text {
        padding: 0.75rem 1rem;
        background-color: #f8f9fa;
        border: 1px solid #ddd;
        border-left: none;
        border-radius: 0 4px 4px 0;
    }

    .input-group .form-control {
        border-radius: 4px 0 0 4px;
    }

    .form-text {
        margin-top: 0.5rem;
        font-size: 0.85rem;
        color: #6c757d;
    }

    .current-image-container {
        margin-bottom: 1.5rem;
        padding: 1rem;
        background-color: #f8f9fa;
        border-radius: 8px;
        border: 1px solid #eee;
        text-align: center;
    }

    .current-image {
        max-width: 100%;
        max-height: 200px;
        object-fit: contain;
        margin-bottom: 0.5rem;
    }

    .image-filename {
        font-size: 0.9rem;
        color: #6c757d;
        margin: 0.5rem 0 0 0;
    }

    .image-upload-container {
        position: relative;
    }

    .image-preview-container {
        margin-bottom: 1rem;
    }

    .image-preview {
        width: 100%;
        height: 200px;
        border: 2px dashed #ddd;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        background-color: #f8f9fa;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .image-preview:hover {
        border-color: var(--primary-light);
    }

    .image-preview img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }

    .upload-placeholder {
        text-align: center;
        color: #6c757d;
    }

    .upload-placeholder i {
        font-size: 2rem;
        margin-bottom: 0.5rem;
    }

    .image-upload {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        cursor: pointer;
        z-index: -1;
    }

    .form-actions {
        display: flex;
        justify-content: flex-end;
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

    .btn-danger {
        background-color: var(--error-color);
        color: white;
        margin-left: auto;
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

        .form-grid {
            grid-template-columns: 1fr;
            gap: 1rem;
        }

        .form-actions {
            flex-direction: column;
        }

        .btn {
            width: 100%;
            justify-content: center;
        }

        .btn-danger {
            margin-left: 0;
            order: -1;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const imageInput = document.getElementById('image');
        const imagePreview = document.getElementById('imagePreview');

        // Display image preview when a file is selected
        imageInput.addEventListener('change', function () {
            if (this.files && this.files[0]) {
                const reader = new FileReader();

                reader.onload = function (e) {
                    imagePreview.innerHTML = `<img src="${e.target.result}" alt="Aperçu de l'image">`;
                }

                reader.readAsDataURL(this.files[0]);
            }
        });

        // Clicking on the preview area triggers the file input
        imagePreview.addEventListener('click', function () {
            imageInput.click();
        });

        // Handle drag and drop
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            imagePreview.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            imagePreview.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            imagePreview.addEventListener(eventName, unhighlight, false);
        });

        function highlight() {
            imagePreview.style.borderColor = "var(--primary - light)";
            imagePreview.style.backgroundColor = 'rgba(113, 187, 178, 0.1)';
        }

        function unhighlight() {
            imagePreview.style.borderColor = '#ddd';
            imagePreview.style.backgroundColor = '#f8f9fa';
        }

        imagePreview.addEventListener('drop', handleDrop, false);

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;

            if (files && files[0]) {
                imageInput.files = files;
                const reader = new FileReader();

                reader.onload = function (e) {
                    imagePreview.innerHTML = `<img src="${e.target.result}" alt="Aperçu de l'image">`;
                }

                reader.readAsDataURL(files[0]);
            }
        }
    });
</script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
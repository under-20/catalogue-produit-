<?php
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Category.php';

class AdminProductController
{
    private $productModel;
    private $categoryModel;
    private $activePage = 'products'; // For active menu item highlighting
    private $uploadDir;

    public function __construct()
    {
        require_once __DIR__ . '/../config/database.php';
        try {
            $database = new Database();
            $pdo = $database->getConnection();
            $this->productModel = new Product($pdo);
            $this->categoryModel = new Category($pdo);
            $this->uploadDir = __DIR__ . '/../public/uploads/products/';

            // Make sure the upload directory exists
            if (!file_exists($this->uploadDir)) {
                mkdir($this->uploadDir, 0755, true);
            }
        } catch (PDOException $e) {
            die("Erreur de connexion à la base de données: " . $e->getMessage());
        }
    }

    /**
     * List all products for administration
     */
    public function list()
    {
        $activePage = $this->activePage;

        // Get pagination parameters
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $page = max(1, $page); // Ensure page is at least 1
        $perPage = 5; // Show 5 items per page

        // Get paginated data
        $data = $this->productModel->getPaginated($page, $perPage);

        // Extract data for the view
        $products = $data['products'];
        $pagination = [
            'currentPage' => $data['currentPage'],
            'totalPages' => $data['totalPages'],
            'totalCount' => $data['totalCount'],
            'perPage' => $data['perPage']
        ];

        // Get categories for the filter dropdown
        $categories = $this->categoryModel->getAll();

        // Load the admin products list view
        require __DIR__ . '/../views/admin/products/list.php';
    }

    /**
     * Show the form to add a new product
     */
    public function create()
    {
        $activePage = $this->activePage;

        // Get categories for dropdown
        $categories = $this->categoryModel->getAll();

        // Pass any validation errors that might exist
        $errors = $_SESSION['errors'] ?? [];
        unset($_SESSION['errors']);

        // Load the add product view
        require __DIR__ . '/../views/admin/products/add.php';
    }

    /**
     * Store a new product in the database
     */
    public function store()
    {
        $errors = [];

        // Get and validate data
        $data = [
            'ref' => trim($_POST['ref'] ?? ''),
            'titre' => trim($_POST['titre'] ?? ''),
            'descrip' => trim($_POST['descrip'] ?? ''),
            'prix' => filter_var($_POST['prix'] ?? 0, FILTER_VALIDATE_FLOAT),
            'quantite' => filter_var($_POST['quantite'] ?? 0, FILTER_VALIDATE_INT),
            'etat' => trim($_POST['etat'] ?? ''),
            'id_cat' => !empty($_POST['id_cat']) ? (int) $_POST['id_cat'] : null,
            'image' => 'default.jpg' // Default image
        ];

        // Validation
        if (empty($data['ref'])) {
            $errors[] = "La référence du produit est obligatoire";
        } else if ($this->productModel->refExists($data['ref'])) {
            $errors[] = "Cette référence existe déjà";
        }

        if (empty($data['titre'])) {
            $errors[] = "Le titre du produit est obligatoire";
        }

        if ($data['prix'] === false || $data['prix'] < 0) {
            $errors[] = "Le prix doit être un nombre positif";
        }

        if ($data['quantite'] === false || $data['quantite'] < 0) {
            $errors[] = "La quantité doit être un nombre entier positif";
        }

        // Check if category exists if provided
        if (!empty($data['id_cat']) && !$this->categoryModel->exists($data['id_cat'])) {//premier jointure clé etrange
            $errors[] = "La catégorie sélectionnée n'existe pas";
        }

        // Handle image upload if present
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            $maxSize = 2 * 1024 * 1024; // 2MB

            // Validate image
            if (!in_array($_FILES['image']['type'], $allowedTypes)) {
                $errors[] = "Le type de fichier n'est pas autorisé (JPG, PNG, GIF uniquement)";
            } else if ($_FILES['image']['size'] > $maxSize) {
                $errors[] = "L'image est trop volumineuse (max 2Mo)";
            } else {
                // Generate unique filename
                $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $filename = uniqid() . '.' . $extension;
                $targetPath = $this->uploadDir . $filename;

                // Move the uploaded file
                if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                    $data['image'] = $filename;
                } else {
                    $errors[] = "Problème lors de l'upload de l'image";
                }
            }
        }

        if (empty($errors)) {
            if ($this->productModel->create($data)) {
                $_SESSION['message'] = "Produit ajouté avec succès";
                header('Location: index.php?action=list&controller=admin_product');
                exit;
            } else {
                $errors[] = "Erreur lors de la création du produit";
            }
        }

        // If errors occurred, store them and redirect back to form
        $_SESSION['errors'] = $errors;
        $_SESSION['old_input'] = $_POST;
        header('Location: index.php?action=create&controller=admin_product');
        exit;
    }

    /**
     * Show the form to edit an existing product
     */
    public function edit($id)
    {
        $activePage = $this->activePage;

        // Get the product
        $product = $this->productModel->getById($id);
        if (!$product) {
            $_SESSION['error'] = "Produit introuvable";
            header('Location: index.php?action=list&controller=admin_product');
            exit;
        }

        // Get categories for dropdown
        $categories = $this->categoryModel->getAll();

        // Pass any validation errors that might exist
        $errors = $_SESSION['errors'] ?? [];
        unset($_SESSION['errors']);

        // Load the edit product view
        require __DIR__ . '/../views/admin/products/edit.php';
    }

    /**
     * Update an existing product in the database
     */
    public function update($id)
    {
        $errors = [];

        // Verify product exists
        $product = $this->productModel->getById($id);
        if (!$product) {
            $_SESSION['error'] = "Produit introuvable";
            header('Location: index.php?action=list&controller=admin_product');
            exit;
        }

        // Get and validate data
        $data = [
            'ref' => trim($_POST['ref'] ?? ''),
            'titre' => trim($_POST['titre'] ?? ''),
            'descrip' => trim($_POST['descrip'] ?? ''),
            'prix' => filter_var($_POST['prix'] ?? 0, FILTER_VALIDATE_FLOAT),
            'quantite' => filter_var($_POST['quantite'] ?? 0, FILTER_VALIDATE_INT),
            'etat' => trim($_POST['etat'] ?? ''),
            'id_cat' => !empty($_POST['id_cat']) ? (int) $_POST['id_cat'] : null,
            'image' => $product['image'] // Keep existing image by default
        ];

        // Validation
        if (empty($data['ref'])) {
            $errors[] = "La référence du produit est obligatoire";
        } else if ($this->productModel->refExists($data['ref'], $id)) {
            $errors[] = "Cette référence existe déjà pour un autre produit";
        }

        if (empty($data['titre'])) {
            $errors[] = "Le titre du produit est obligatoire";
        }

        if ($data['prix'] === false || $data['prix'] < 0) {
            $errors[] = "Le prix doit être un nombre positif";
        }

        if ($data['quantite'] === false || $data['quantite'] < 0) {
            $errors[] = "La quantité doit être un nombre entier positif";
        }

        // Check if category exists if provided
        if (!empty($data['id_cat']) && !$this->categoryModel->exists($data['id_cat'])) {
            $errors[] = "La catégorie sélectionnée n'existe pas";
        }

        // Handle image upload if present
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            $maxSize = 2 * 1024 * 1024; // 2MB

            // Validate image
            if (!in_array($_FILES['image']['type'], $allowedTypes)) {
                $errors[] = "Le type de fichier n'est pas autorisé (JPG, PNG, GIF uniquement)";
            } else if ($_FILES['image']['size'] > $maxSize) {
                $errors[] = "L'image est trop volumineuse (max 2Mo)";
            } else {
                // Generate unique filename
                $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $filename = uniqid() . '.' . $extension;
                $targetPath = $this->uploadDir . $filename;

                // Move the uploaded file
                if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                    $data['image'] = $filename;

                    // Delete old image if not default
                    if ($product['image'] !== 'default.jpg' && file_exists($this->uploadDir . $product['image'])) {
                        @unlink($this->uploadDir . $product['image']);
                    }
                } else {
                    $errors[] = "Problème lors de l'upload de l'image";
                }
            }
        }

        if (empty($errors)) {
            if ($this->productModel->update($id, $data)) {
                $_SESSION['message'] = "Produit modifié avec succès";
                header('Location: index.php?action=list&controller=admin_product');
                exit;
            } else {
                $errors[] = "Erreur lors de la mise à jour du produit";
            }
        }

        // If errors occurred, store them and redirect back to form
        $_SESSION['errors'] = $errors;
        header('Location: index.php?action=edit&controller=admin_product&id=' . $id);
        exit;
    }

    /**
     * Display product details
     */
    public function view($id)
    {
        $activePage = $this->activePage;

        // Get the product
        $product = $this->productModel->getById($id);
        if (!$product) {
            $_SESSION['error'] = "Produit introuvable";
            header('Location: index.php?action=list&controller=admin_product');
            exit;
        }

        // Get the category if set
        if (!empty($product['id_cat'])) {
            $category = $this->categoryModel->getById($product['id_cat']);//2eme jouinture
        }

        // Load the view product view
        require __DIR__ . '/../views/admin/products/view.php';
    }

    /**
     * Delete a product
     */
    public function delete($id)
    {
        // Verify product exists
        $product = $this->productModel->getById($id);
        if (!$product) {
            $_SESSION['error'] = "Produit introuvable";
            header('Location: index.php?action=list&controller=admin_product');
            exit;
        }

        if ($this->productModel->delete($id)) {
            // Delete product image if not default
            if ($product['image'] !== 'default.jpg' && file_exists($this->uploadDir . $product['image'])) {
                @unlink($this->uploadDir . $product['image']);
            }

            $_SESSION['message'] = "Produit supprimé avec succès";
        } else {
            $_SESSION['error'] = "Erreur lors de la suppression du produit";
        }

        header('Location: index.php?action=list&controller=admin_product');
        exit;
    }
}
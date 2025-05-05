<?php
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Category.php';

class ProductController
{
    private $productModel;
    private $categoryModel;
    private $activePage = 'products'; // Add this to ensure proper navigation

    public function __construct()
    {
        require_once __DIR__ . '/../config/database.php';
        try {
            $database = new Database();
            $pdo = $database->getConnection();
            $this->productModel = new Product($pdo);
            $this->categoryModel = new Category($pdo);
        } catch (PDOException $e) {
            die("Erreur de connexion à la base de données: " . $e->getMessage());
        }
    }

    public function index()
    {
        // Set active page for navigation
        $activePage = 'home';

        // Get all categories for the filter
        $categories = $this->categoryModel->getAll();

        // Check if category filter is applied
        $categoryId = isset($_GET['category']) ? (int) $_GET['category'] : null;

        // Get books based on category filter
        if ($categoryId && $this->categoryModel->exists($categoryId)) {
            $featuredBooks = $this->productModel->getByCategory($categoryId);
            $selectedCategory = $this->categoryModel->getById($categoryId);
        } else {
            // No filter or invalid category - show recent books
            $featuredBooks = $this->productModel->getRecent(8);
            $selectedCategory = null;
        }

        // Load the homepage view
        require __DIR__ . '/../views/home/index.php';
    }

    public function create()
    {
        $activePage = $this->activePage; // Set active page for navigation
        $categories = $this->categoryModel->getAll();
        $errors = $_SESSION['errors'] ?? []; // Use consistent error session variable
        unset($_SESSION['errors']);
        require __DIR__ . '/../views/products/add.php';
    }

    public function store()
    {
        $errors = [];

        // Validation des données
        $data = [
            'ref' => trim($_POST['ref'] ?? ''),
            'titre' => trim($_POST['titre'] ?? ''),
            'descrip' => trim($_POST['descrip'] ?? ''),
            'prix' => (float) $_POST['prix'] ?? 0,
            'quantite' => (int) $_POST['quantite'] ?? 0,
            'etat' => $_POST['etat'] ?? 'stock',
            'id_cat' => !empty($_POST['id_cat']) ? (int) $_POST['id_cat'] : null
        ];

        // Gestion de l'upload d'image
        $data['image'] = $this->handleImageUpload($_FILES['image'] ?? null, $errors);

        // Validation
        if (empty($data['ref']))
            $errors[] = "La référence est obligatoire";
        if (empty($data['titre']))
            $errors[] = "Le titre est obligatoire";
        if ($this->productModel->refExists($data['ref']))
            $errors[] = "Cette référence existe déjà";
        if ($data['prix'] <= 0)
            $errors[] = "Le prix doit être positif";
        if ($data['quantite'] < 0)
            $errors[] = "La quantité ne peut pas être négative";

        if (empty($errors)) {
            if ($this->productModel->create($data)) {
                $_SESSION['message'] = "Produit ajouté avec succès!";
                header('Location: index.php?action=index&controller=product');
                exit;
            } else {
                $errors[] = "Erreur lors de l'ajout du produit";
            }
        }

        // If we have errors, store them in session and redirect back to the form
        $_SESSION['errors'] = $errors;
        $_SESSION['old_input'] = $_POST;
        header('Location: index.php?action=create&controller=product');
        exit;
    }

    public function edit($id)
    {
        $activePage = $this->activePage; // Set active page for navigation
        $product = $this->productModel->getById($id);
        if (!$product) {
            $_SESSION['error'] = "Produit introuvable";
            header('Location: index.php?action=index&controller=product');
            exit;
        }

        $categories = $this->categoryModel->getAll();
        $errors = $_SESSION['errors'] ?? []; // Use consistent error session variable
        unset($_SESSION['errors']);
        require __DIR__ . '/../views/products/edit.php';
    }

    public function update($id)
    {
        $errors = [];
        $product = $this->productModel->getById($id);

        if (!$product) {
            $_SESSION['error'] = "Produit introuvable";
            header('Location: index.php?action=index&controller=product');
            exit;
        }

        $data = [
            'ref' => trim($_POST['ref'] ?? ''),
            'titre' => trim($_POST['titre'] ?? ''),
            'descrip' => trim($_POST['descrip'] ?? ''),
            'prix' => (float) $_POST['prix'] ?? 0,
            'quantite' => (int) $_POST['quantite'] ?? 0,
            'etat' => $_POST['etat'] ?? 'stock',
            'id_cat' => !empty($_POST['id_cat']) ? (int) $_POST['id_cat'] : null,
            'image' => $product['image'] // Conserver l'image actuelle par défaut
        ];

        // Gestion de l'upload si nouvelle image fournie
        if (!empty($_FILES['image']['name'])) {
            $data['image'] = $this->handleImageUpload($_FILES['image'], $errors);
        }

        // Validation
        if (empty($data['ref']))
            $errors[] = "La référence est obligatoire";
        if (empty($data['titre']))
            $errors[] = "Le titre est obligatoire";
        if ($this->productModel->refExists($data['ref'], $id))
            $errors[] = "Cette référence existe déjà";
        if ($data['prix'] <= 0)
            $errors[] = "Le prix doit être positif";
        if ($data['quantite'] < 0)
            $errors[] = "La quantité ne peut pas être négative";

        if (empty($errors)) {
            if ($this->productModel->update($id, $data)) {
                $_SESSION['message'] = "Produit modifié avec succès!";
                header('Location: index.php?action=index&controller=product');
                exit;
            } else {
                $errors[] = "Erreur lors de la modification du produit";
            }
        }

        // If we have errors, store them in session and redirect back to the form
        $_SESSION['errors'] = $errors;
        $_SESSION['old_input'] = $_POST;
        header('Location: index.php?action=edit&controller=product&id=' . $id);
        exit;
    }

    public function delete($id)
    {
        $product = $this->productModel->getById($id);
        if ($product && $this->productModel->delete($id)) {
            // Supprimer l'image associée si ce n'est pas l'image par défaut
            if ($product['image'] !== 'default_product.png') {
                $imagePath = __DIR__ . '/../public/uploads/' . $product['image'];
                if (file_exists($imagePath)) {
                    @unlink($imagePath);
                }
            }
            $_SESSION['message'] = "Produit supprimé avec succès!";
        } else {
            $_SESSION['error'] = "Erreur lors de la suppression du produit";
        }
        header('Location: index.php?action=index&controller=product');
        exit;
    }

    public function view($id)
    {
        $activePage = 'products'; // Set active page for navigation

        // Get the product
        $product = $this->productModel->getById($id);
        if (!$product) {
            $_SESSION['error'] = "Produit introuvable";
            header('Location: index.php?action=index&controller=product');
            exit;
        }

        // Get the category if set
        if (!empty($product['id_cat'])) {
            $category = $this->categoryModel->getById($product['id_cat']);
        }

        // Load the user product view
        require __DIR__ . '/../views/products/view.php';
    }

    private function handleImageUpload($file, &$errors)
    {
        if (!$file || $file['error'] === UPLOAD_ERR_NO_FILE) {
            return 'default_product.png';
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = "Erreur lors de l'upload de l'image";
            return 'default_product.png';
        }

        // Validation du type de fichier
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($file['type'], $allowedTypes)) {
            $errors[] = "Type de fichier non autorisé (seuls JPEG, PNG et GIF sont acceptés)";
            return 'default_product.png';
        }

        // Validation de la taille
        $maxSize = 2 * 1024 * 1024; // 2MB
        if ($file['size'] > $maxSize) {
            $errors[] = "L'image est trop volumineuse (max 2MB)";
            return 'default_product.png';
        }

        // Ensure upload directory exists
        $uploadDir = __DIR__ . '/../public/uploads/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Génération d'un nom de fichier unique
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $extension;
        $destination = $uploadDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $destination)) {
            return $filename;
        } else {
            $errors[] = "Erreur lors de l'enregistrement de l'image";
            return 'default_product.png';
        }
    }
}
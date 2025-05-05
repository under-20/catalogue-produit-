<?php
require_once __DIR__ . '/../models/Category.php';

class CategoryController
{
    private $categoryModel;
    private $activePage = 'categories'; // Add this property to control active navigation

    public function __construct()
    {
        $this->initializeDatabaseConnection();
    }

    private function initializeDatabaseConnection(): void
    {
        try {
            require_once __DIR__ . '/../config/database.php';
            $database = new Database();
            $pdo = $database->getConnection();
            $this->categoryModel = new Category($pdo);
        } catch (PDOException $e) {
            error_log("Database connection error: " . $e->getMessage());
            throw new RuntimeException("Service temporairement indisponible");
        }
    }

    public function index(): void
    {
        try {
            $activePage = $this->activePage; // Set activePage for navigation

            // Get pagination parameters
            $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
            $page = max(1, $page); // Ensure page is at least 1
            $perPage = 5; // Show 5 items per page

            // Get paginated data
            $data = $this->categoryModel->getPaginated($page, $perPage);

            // Extract data for the view
            $categories = $data['categories'];
            $pagination = [
                'currentPage' => $data['currentPage'],
                'totalPages' => $data['totalPages'],
                'totalCount' => $data['totalCount'],
                'perPage' => $data['perPage']
            ];

            require __DIR__ . '/../views/categories/list.php';
        } catch (PDOException $e) {
            error_log("Error fetching categories: " . $e->getMessage());
            $_SESSION['error'] = "Could not load categories. Please try again later.";
            $activePage = $this->activePage;
            require __DIR__ . '/../views/categories/list.php';
        }
    }

    public function create(): void
    {
        try {
            $activePage = $this->activePage; // Set activePage for navigation
            $parentCategories = $this->categoryModel->getParentCategories();
            $errors = $_SESSION['errors'] ?? []; // Use consistent error session variable
            unset($_SESSION['errors']);
            require __DIR__ . '/../views/categories/add.php';
        } catch (PDOException $e) {
            error_log("Error loading create form: " . $e->getMessage());
            $_SESSION['error'] = "Could not load the form. Please try again.";
            $this->redirectToIndex();
        }
    }

    public function store(): void
    {
        // No need to validate HTTP method anymore since form sends POST

        $data = $this->validateAndSanitizeInput();
        $errors = $data['errors'];

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors; // Use consistent error session variable
            $_SESSION['old_input'] = $_POST;
            $this->redirect('create');
            return;
        }

        try {
            if ($this->categoryModel->create($data['clean_data'])) {
                $_SESSION['message'] = "Catégorie ajoutée avec succès!"; // Using consistent message variable
                $this->redirectToIndex();
                return;
            }
            throw new RuntimeException("Échec de la création de la catégorie");
        } catch (Exception $e) {
            error_log("Error creating category: " . $e->getMessage());
            $_SESSION['error'] = "Erreur lors de l'ajout de la catégorie"; // Using consistent error variable
            $_SESSION['old_input'] = $_POST;
            $this->redirect('create');
        }
    }

    public function edit(int $id): void
    {
        try {
            $activePage = $this->activePage; // Set activePage for navigation
            $category = $this->categoryModel->getById($id);
            if (!$category) {
                throw new RuntimeException("Category not found");
            }

            $parentCategories = $this->categoryModel->getParentCategories();
            $errors = $_SESSION['errors'] ?? []; // Use consistent error session variable
            unset($_SESSION['errors']);

            require __DIR__ . '/../views/categories/edit.php';
        } catch (Exception $e) {
            error_log("Error loading edit form: " . $e->getMessage());
            $_SESSION['error'] = "Could not load the category for editing.";
            $this->redirectToIndex();
        }
    }

    public function update(int $id): void
    {
        // No need to validate HTTP method anymore since form sends POST

        $data = $this->validateAndSanitizeInput($id);
        $errors = $data['errors'];

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors; // Use consistent error session variable
            $_SESSION['old_input'] = $_POST;
            $this->redirect('edit', $id);
            return;
        }

        try {
            if ($this->categoryModel->update($id, $data['clean_data'])) {
                $_SESSION['message'] = "Catégorie modifiée avec succès!"; // Using consistent message variable
                $this->redirectToIndex();
                return;
            }
            throw new RuntimeException("Failed to update category");
        } catch (Exception $e) {
            error_log("Error updating category: " . $e->getMessage());
            $_SESSION['error'] = "Could not update category. Please try again.";
            $this->redirect('edit', $id);
        }
    }

    public function delete(int $id): void
    {
        // Allow both GET and POST for delete - originally it expected POST only
        try {
            if ($this->categoryModel->hasChildren($id)) {
                throw new RuntimeException("Cette catégorie contient des sous-catégories et ne peut pas être supprimée.");
            }

            if (!$this->categoryModel->delete($id)) {
                throw new RuntimeException("Échec de la suppression de la catégorie");
            }

            $_SESSION['message'] = "Catégorie supprimée avec succès!"; // Using consistent message variable
        } catch (Exception $e) {
            error_log("Error deleting category: " . $e->getMessage());
            $_SESSION['error'] = $e->getMessage(); // Using consistent error variable
        }

        $this->redirectToIndex();
    }

    private function validateAndSanitizeInput(?int $excludeId = null): array
    {
        $errors = [];
        $cleanData = [
            'nom_cat' => trim($_POST['nom_cat'] ?? ''),
            'slug' => trim($_POST['slug'] ?? ''),
            'parentid' => !empty($_POST['parent_id']) ? (int) $_POST['parent_id'] : null
        ];

        // Validate name
        if (empty($cleanData['nom_cat'])) {
            $errors[] = "Le nom de la catégorie est obligatoire";
        } elseif (strlen($cleanData['nom_cat']) > 100) {
            $errors[] = "Le nom de la catégorie ne peut pas dépasser 100 caractères";
        }

        // Validate slug
        if (empty($cleanData['slug'])) {
            $errors[] = "Le slug est obligatoire";
        } elseif (!preg_match('/^[a-z0-9-]+$/', $cleanData['slug'])) {
            $errors[] = "Le slug ne peut contenir que des lettres minuscules, des chiffres et des tirets";
        } elseif (strlen($cleanData['slug']) > 100) {
            $errors[] = "Le slug ne peut pas dépasser 100 caractères";
        } elseif ($this->categoryModel->slugExists($cleanData['slug'], $excludeId)) {
            $errors[] = "Ce slug est déjà utilisé";
        }

        return ['errors' => $errors, 'clean_data' => $cleanData];
    }

    private function redirect(string $action, ?int $id = null): void
    {
        $location = "index.php?action=$action&controller=category";
        if ($id !== null) {
            $location .= "&id=$id";
        }
        header("Location: $location");
        exit;
    }

    private function redirectToIndex(): void
    {
        $this->redirect('index');
    }
}
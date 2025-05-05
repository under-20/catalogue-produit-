<?php
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../models/Product.php';

class AdminController
{
    private $categoryModel;
    private $productModel;

    public function __construct()
    {
        require_once __DIR__ . '/../config/database.php';
        try {
            $database = new Database();
            $pdo = $database->getConnection();
            $this->categoryModel = new Category($pdo);
            $this->productModel = new Product($pdo);
        } catch (PDOException $e) {
            die("Erreur de connexion à la base de données: " . $e->getMessage());
        }
    }

    /**
     * Display the admin dashboard
     */
    public function dashboard()
    {
        $activePage = 'dashboard'; // For active menu item highlighting

        // Get summary counts for the dashboard
        $totalCategories = $this->categoryModel->countAll();
        $totalProducts = $this->productModel->countAll();

        // Get recent products
        $recentProducts = $this->productModel->getRecent(5);

        // Get categories
        $categories = $this->categoryModel->getAll();

        // Load the dashboard view
        require __DIR__ . '/../views/admin/dashboard.php';
    }

    /**
     * Handle admin login
     */
    public function login()
    {
        // In a real application, you would implement proper authentication here
        // This is just a placeholder for demonstration purposes

        // Load the login view
        require __DIR__ . '/../views/admin/login.php';
    }

    /**
     * Process admin login form
     */
    public function authenticate()
    {
        // In a real application, you would implement proper authentication here
        // This is just a placeholder for demonstration purposes

        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        // Simple authentication (replace with proper authentication in a real app)
        if ($username === 'admin' && $password === 'admin') {
            $_SESSION['admin'] = true;
            $_SESSION['message'] = 'Connexion réussie';
            header('Location: index.php?controller=admin&action=dashboard');
            exit;
        }

        $_SESSION['error'] = 'Nom d\'utilisateur ou mot de passe incorrect';
        header('Location: index.php?controller=admin&action=login');
        exit;
    }

    /**
     * Log out admin
     */
    public function logout()
    {
        // Destroy the admin session
        unset($_SESSION['admin']);
        session_destroy();

        // Redirect to login
        header('Location: index.php?controller=admin&action=login');
        exit;
    }
}
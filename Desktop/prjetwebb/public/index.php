<?php
session_start();

// Configuration de base
define('BASE_URL', '/bookshop/'); // Adjust according to your structure

// Autoload controllers
spl_autoload_register(function ($className) {
    $file = __DIR__ . '/../controllers/' . $className . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// Get parameters safely
$controllerName = isset($_GET['controller']) ? strtolower(filter_var($_GET['controller'], FILTER_SANITIZE_STRING)) : 'product';
$action = isset($_GET['action']) ? strtolower(filter_var($_GET['action'], FILTER_SANITIZE_STRING)) : 'index';
$id = isset($_GET['id']) ? filter_var($_GET['id'], FILTER_VALIDATE_INT) : null;

// List of allowed controllers
$allowedControllers = [
    'category' => 'CategoryController',
    'product' => 'ProductController',
    'wishlist' => 'WishlistController',  // Add wishlist controller
    // Add admin controllers
    'admin' => 'AdminController',
    'admin_category' => 'AdminCategoryController',
    'admin_product' => 'AdminProductController'
];

// List of allowed actions
$allowedActions = [
    'index',
    'create',
    'store',
    'edit',
    'update',
    'delete',
    'show',
    'view',
    'list',
    'dashboard',
    // Wishlist-specific actions
    'wishlist',
    'add_to_wishlist',
    'remove_from_wishlist',
    'view_wishlist'
];

// Special handling for wishlist actions
if ($action === 'wishlist') {
    $wishlistController = new WishlistController();
    $wishlistController->viewWishlist();
    exit;
} elseif ($action === 'add_to_wishlist') {
    $wishlistController = new WishlistController();
    $wishlistController->addToWishlist();
    exit;
} elseif ($action === 'remove_from_wishlist') {
    $wishlistController = new WishlistController();
    $wishlistController->removeFromWishlist();
    exit;
}

// Check if controller exists
if (!array_key_exists($controllerName, $allowedControllers)) {
    http_response_code(404);
    echo "Controller not found";
    exit;
}

// Instantiate controller
$controllerClass = $allowedControllers[$controllerName];
$controller = new $controllerClass();

// Check if action exists
if (!in_array($action, $allowedActions) || !method_exists($controller, $action)) {
    http_response_code(404);
    echo "Action not found";
    exit;
}

// Call the action with or without parameter
try {
    if ($id !== null && $id !== false) {
        $controller->$action($id);
    } else {
        $controller->$action();
    }
} catch (Exception $e) {
    // Log the error
    error_log("Error in router: " . $e->getMessage());

    // Show error message
    http_response_code(500);
    echo "Internal Server Error: " . $e->getMessage();
    exit;
}
<?php
require_once __DIR__ . '/../models/Wishlist.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../config/database.php';

class WishlistController
{
  private $wishlist;
  private $product;
  private $db;

  public function __construct()
  {
    $database = new Database();
    $this->db = $database->getConnection();
    $this->wishlist = new Wishlist($this->db);
    $this->product = new Product($this->db);
  }

  public function viewWishlist()
  {
    // For simplicity, use hardcoded user ID = 1
    $userId = 1;

    // Get all wishlist items for the user
    $items = $this->wishlist->getUserWishlist($userId);

    // Include wishlist view
    include_once __DIR__ . '/../views/wishlist/list.php';
  }

  public function addToWishlist()
  {
    // For simplicity, use hardcoded user ID = 1
    $userId = 1;

    if (isset($_POST['product_id']) && !empty($_POST['product_id'])) {
      $productId = $_POST['product_id'];

      // Check if product exists
      $product = $this->product->getById($productId);
      if (!$product) {
        $_SESSION['message'] = "Product not found!";
        $_SESSION['message_type'] = "danger";
        header("Location: index.php?action=index&controller=product");
        exit;
      }

      // Add product to wishlist
      if ($this->wishlist->addItem($userId, $productId)) {
        $_SESSION['message'] = "Product added to wishlist successfully!";
        $_SESSION['message_type'] = "success";
      } else {
        $_SESSION['message'] = "Failed to add product to wishlist!";
        $_SESSION['message_type'] = "danger";
      }
    } else {
      $_SESSION['message'] = "Product ID is required!";
      $_SESSION['message_type'] = "danger";
    }

    // Redirect back to referring page or product list
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'index.php?action=index&controller=product';
    header("Location: " . $referer);
    exit;
  }

  public function removeFromWishlist()
  {
    // For simplicity, use hardcoded user ID = 1
    $userId = 1;

    if (isset($_POST['product_id']) && !empty($_POST['product_id'])) {
      $productId = $_POST['product_id'];

      // Remove product from wishlist
      if ($this->wishlist->removeItem($userId, $productId)) {
        $_SESSION['message'] = "Product removed from wishlist successfully!";
        $_SESSION['message_type'] = "success";
      } else {
        $_SESSION['message'] = "Failed to remove product from wishlist!";
        $_SESSION['message_type'] = "danger";
      }
    } else {
      $_SESSION['message'] = "Product ID is required!";
      $_SESSION['message_type'] = "danger";
    }

    // Redirect back to referring page or wishlist page
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'index.php?action=wishlist';
    header("Location: " . $referer);
    exit;
  }

  public function getWishlistCount()
  {
    // For simplicity, use hardcoded user ID = 1
    $userId = 1;
    return $this->wishlist->countItems($userId);
  }

  public function isInWishlist($productId)
  {
    // For simplicity, use hardcoded user ID = 1
    $userId = 1;
    return $this->wishlist->isInWishlist($userId, $productId);
  }
}
?>


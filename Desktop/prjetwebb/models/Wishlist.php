<?php

class Wishlist
{
  private $conn;

  public function __construct($db)
  {
    $this->conn = $db;
  }

  // Add a product to user's wishlist
  public function addItem($userId, $productId)
  {
    // Check if item already exists in wishlist
    if ($this->isInWishlist($userId, $productId)) {
      return true; // Item already in wishlist
    }

    $query = "INSERT INTO wishlist (id_user, id_prod) 
                  VALUES (:userId, :productId)";

    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':userId', $userId);
    $stmt->bindParam(':productId', $productId);

    return $stmt->execute();
  }

  // Remove a product from user's wishlist
  public function removeItem($userId, $productId)
  {
    $query = "DELETE FROM wishlist 
                  WHERE id_user = :userId AND id_prod = :productId";

    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':userId', $userId);
    $stmt->bindParam(':productId', $productId);

    return $stmt->execute();
  }

  // Get all items in a user's wishlist with product details
  public function getUserWishlist($userId)
  {
    $query = "SELECT w.id_wishlist, w.date_ajout, p.* 
                  FROM wishlist w
                  JOIN produit p ON w.id_prod = p.id_prod
                  WHERE w.id_user = :userId
                  ORDER BY w.date_ajout DESC";

    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':userId', $userId);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  // Check if a product is already in user's wishlist
  public function isInWishlist($userId, $productId)
  {
    $query = "SELECT COUNT(*) FROM wishlist 
                  WHERE id_user = :userId AND id_prod = :productId";

    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':userId', $userId);
    $stmt->bindParam(':productId', $productId);
    $stmt->execute();

    return ($stmt->fetchColumn() > 0);
  }

  // Count items in user's wishlist
  public function countItems($userId)
  {
    $query = "SELECT COUNT(*) FROM wishlist WHERE id_user = :userId";

    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':userId', $userId);
    $stmt->execute();

    return $stmt->fetchColumn();
  }
}
?>

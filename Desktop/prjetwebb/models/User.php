<?php

class User
{
  private $conn;

  public function __construct($db)
  {
    $this->conn = $db;
  }

  public function getById($userId)
  {
    $query = "SELECT * FROM utilisateurs WHERE id_user = :id_user";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':id_user', $userId);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  public function create($username, $email, $password)
  {
    $query = "INSERT INTO utilisateurs (username, email, password) 
                  VALUES (:username, :email, :password)";

    $stmt = $this->conn->prepare($query);

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $hashedPassword);

    if ($stmt->execute()) {
      return $this->conn->lastInsertId();
    }

    return false;
  }

  public function update($id, $username, $email)
  {
    $query = "UPDATE utilisateurs 
                  SET username = :username, email = :email 
                  WHERE id_user = :id";

    $stmt = $this->conn->prepare($query);

    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':id', $id);

    return $stmt->execute();
  }
}
?>

<?php
// Connexion DB
$host = "localhost";
$user = "isslem";
$pass = "123123456aya";
$dbname = "bookshop";

try {
  $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  die("Erreur de connexion : " . $e->getMessage());
}

// Suppression si "delete" est présent
if (isset($_GET['delete'])) {
  $id = $_GET['delete'];
  $pdo->prepare("DELETE FROM livres WHERE id = ?")->execute([$id]);
  header("Location: index.php");
  exit;
}

// Récupération des livres
$livres = $pdo->query("SELECT * FROM livres")->fetchAll();
?>

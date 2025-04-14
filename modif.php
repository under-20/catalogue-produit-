<?php
$livre = null;
$error = null;
$success = null;

// Connexion à la base de données
$conn = mysqli_connect("localhost", "isslem", "123123456aya", "bookshop");
if (!$conn) {
    die("Connexion échouée : " . mysqli_connect_error());
}

// 1. Recherche d'un livre via GET
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['search-ref'])) {
    $searchRef = trim($_GET['search-ref']);

    // Validation de la référence
    if (empty($searchRef)) {
        $error = "Veuillez entrer une référence valide";
    } else {
        $sql = "SELECT * FROM produit WHERE ref = ?";
        $stmt = mysqli_prepare($conn, $sql);

        if (!$stmt) {
            $error = "Erreur de préparation de la requête: " . mysqli_error($conn);
        } else {
            mysqli_stmt_bind_param($stmt, "s", $searchRef);

            if (!mysqli_stmt_execute($stmt)) {
                $error = "Erreur d'exécution de la requête: " . mysqli_stmt_error($stmt);
            } else {
                $result = mysqli_stmt_get_result($stmt);

                if ($row = mysqli_fetch_assoc($result)) {
                    $livre = $row;
                    $success = "Livre trouvé avec succès!";
                } else {
                    $error = "Aucun livre trouvé avec la référence: " . htmlspecialchars($searchRef);
                }
            }
            mysqli_stmt_close($stmt);
        }
    }
}

// 2. Modification d'un livre via POST
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ref'])) {
    // Validation des données
    $ref = trim($_POST['ref']);
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $price = (float)$_POST['price'];
    $quantity = (int)$_POST['quantity'];
    $etat = isset($_POST['etat']) ? trim($_POST['etat']) : 'stock';

    // Vérification des champs obligatoires
    if (empty($ref) || empty($title) || empty($description)) {
        $error = "Tous les champs obligatoires doivent être remplis";
    } elseif ($price <= 0) {
        $error = "Le prix doit être un nombre positif";
    } elseif ($quantity < 0) {
        $error = "La quantité ne peut pas être négative";
    } else {
        // Traitement de l'image
        $image = isset($livre['image']) ? $livre['image'] : null;
        
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = 'uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            // Validation du fichier image
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            $file_info = finfo_open(FILEINFO_MIME_TYPE);
            $mime_type = finfo_file($file_info, $_FILES['image']['tmp_name']);
            finfo_close($file_info);

            if (!in_array($mime_type, $allowed_types)) {
                $error = "Type de fichier non autorisé. Seuls JPEG, PNG et GIF sont acceptés.";
            } elseif ($_FILES['image']['size'] > 5000000) {
                $error = "Fichier trop volumineux (max 5MB).";
            } else {
                $file_ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $file_name = uniqid() . '.' . $file_ext;
                $target_file = $upload_dir . $file_name;

                if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                    // Supprimer l'ancienne image si elle existe
                    if (!empty($livre['image']) && file_exists($livre['image'])) {
                        unlink($livre['image']);
                    }
                    $image = $target_file;
                } else {
                    $error = "Erreur lors de l'upload de l'image.";
                }
            }
        }

        if (!$error) {
            // Préparation de la requête SQL
            $sql = $image !== null 
                ? "UPDATE produit SET titre=?, descrip=?, prix=?, quantite=?, etat=?, image=? WHERE ref=?" 
                : "UPDATE produit SET titre=?, descrip=?, prix=?, quantite=?, etat=? WHERE ref=?";

            $stmt = mysqli_prepare($conn, $sql);

            if (!$stmt) {
                $error = "Erreur de préparation: " . mysqli_error($conn);
            } else {
                if ($image !== null) {
                    mysqli_stmt_bind_param($stmt, "ssdiss", $title, $description, $price, $quantity, $etat, $image, $ref);
                } else {
                    mysqli_stmt_bind_param($stmt, "ssdis", $title, $description, $price, $quantity, $etat, $ref);
                }

                if (mysqli_stmt_execute($stmt)) {
                    $success = "Livre modifié avec succès!";
                    // Mettre à jour les données affichées
                    $livre = [
                        'ref' => $ref,
                        'titre' => $title,
                        'descrip' => $description,
                        'prix' => $price,
                        'quantite' => $quantity,
                        'etat' => $etat,
                        'image' => $image
                    ];
                } else {
                    $error = "Erreur de modification: " . mysqli_stmt_error($stmt);
                }
                mysqli_stmt_close($stmt);
            }
        }
    }
}

mysqli_close($conn);
?>
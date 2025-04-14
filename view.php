<?php
// Connexion à la base de données
$host = "localhost";
$user = "isslem";
$password = "123123456aya";
$dbname = "bookshop"; // Remplace par le nom réel de ta base

$conn = new mysqli($host, $user, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

// Suppression si bouton cliqué
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    $conn->query("DELETE FROM produit WHERE id_prod = $id");
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Récupération des données
$result = $conn->query("SELECT * FROM produit");
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des Produits</title>
    <style>
        /* Général */
body {
    font-family: 'Segoe UI', sans-serif;
    margin: 0;
    padding: 0;
    background-color: #FBFBFB;
    color: #1e1e1e;
    transition: background-color 0.5s, color 0.5s;
}

/* En-tête */
header {
    text-align: center;
    padding: 30px 10px 10px;
    background-color: #EFE9D5;
    font-size: 28px;
    color: #27445D;
    font-weight: bold;
}

/* Conteneur principal */
.container {
    max-width: 600px;
    margin: 40px auto;
    background-color: #ffffff;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    padding: 30px;
}

/* Titre */
h2 {
    text-align: center;
    color: #27445D;
    margin-bottom: 25px;
}

/* Table */
table {
    width: 90%;
    margin: 20px auto;
    border-collapse: collapse;
}

th, td {
    border: 1px solid #EFE9D5;
    padding: 8px 12px;
    text-align: center;
}

th {
    background-color: #EFE9D5;
    color: #27445D;
}

td {
    background-color: #FBFBFB;
    color: #27445D;
}

/* Bouton de suppression */
.btn-supprimer {
    color: white;
    background-color: red;
    padding: 6px 10px;
    text-decoration: none;
    border-radius: 4px;
}

/* Formulaires */
label {
    display: block;
    margin-bottom: 8px;
    font-weight: bold;
    color: #497D74;
}

input[type="text"],
input[type="number"],
input[type="file"] {
    width: 100%;
    padding: 12px 15px;
    border-radius: 10px;
    border: 1px solid #ccc;
    margin-bottom: 20px;
    font-size: 15px;
    box-sizing: border-box;
    background-color: #FBFBFB;
}

/* Bouton de soumission */
button {
    background-color: #497D74;
    color: white;
    border: none;
    padding: 12px 20px;
    border-radius: 10px;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
    width: 100%;
    transition: background-color 0.3s;
}

button:hover {
    background-color: #71BBB2;
}

/* Prévisualisation des images */
.preview {
    text-align: center;
    margin-top: 20px;
}

.preview img {
    max-width: 200px;
    border-radius: 10px;
    margin-top: 10px;
}

/* Mode sombre */
body.dark-mode {
    background-color: #121212;
    color: #ffffff;
}

/* Mode sombre pour la table */
body.dark-mode table, 
body.dark-mode th, 
body.dark-mode td {
    border: 1px solid #EFE9D5;
}

body.dark-mode th {
    background-color: #EFE9D5;
    color: #27445D;
}

body.dark-mode td {
    background-color: #FBFBFB;
    color: #27445D;
}

/* Bouton de suppression en mode sombre */
body.dark-mode .btn-supprimer {
    background-color: darkred;
}

/* Switch pour mode sombre/claire */
.ui-switch {
    --switch-bg: rgb(135, 150, 165);
    --switch-width: 48px;
    --switch-height: 20px;
    --circle-diameter: 32px;
    --circle-bg: rgb(0, 56, 146);
    --circle-inset: calc((var(--circle-diameter) - var(--switch-height)) / 2);
}

.ui-switch input {
    display: none;
}

.slider {
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    width: var(--switch-width);
    height: var(--switch-height);
    background: var(--switch-bg);
    border-radius: 999px;
    position: relative;
    cursor: pointer;
}

.slider .circle {
    top: calc(var(--circle-inset) * -1);
    left: 0;
    width: var(--circle-diameter);
    height: var(--circle-diameter);
    position: absolute;
    background: var(--circle-bg);
    border-radius: inherit;
    background-image: url("data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIGhlaWdodD0iMjAiIHdpZHRoPSIyMCIgdmlld0JveD0iMCAwIDIwIDIwIj4KICAgIDxwYXRoIGZpbGw9IiNmZmYiCiAgICAgICAgZD0iTTkuMzA1IDEuNjY3VjMuNzVoMS4zODlWMS42NjdoLTEuMzl6bS00LjcwNyAxLjk1bC0uOTgyLjk4Mkw1LjA5IDYuMDcybC45ODItLjk4Mi0xLjQ3My0xLjQ3My0uOTgyLS45ODJ6bT...
}

.ui-switch input:checked + .slider {
    background-color: rgb(40, 140, 255);
}

.ui-switch input:active + .slider .circle::before {
    opacity: 1;
    width: 0;
    height: 0;
}

.ui-switch input:checked + .slider .circle {
    left: calc(100% - var(--circle-diameter));
    background-image: url("data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIGhlaWdodD0iMjAiIHdpZHRoPSIyMCIgdmlld0JveD0iMCAwIDIwIDIwIj4KICAgIDxwYXRoIGZpbGw9IiNmZmYiCiAgICAgICAgZD0iTTQuMiAyLjVsLS43IDEuOC0xLjguNyAxLjguNy43IDEuOC42LTEuOEw2LjcgNWwtMS45LS43LS42LTEuOHptMTUgOC4zYTYuNyA2LjcgMCAxMS02LjYtNi42IDUuOCA1LjggMCAwMDYuNiA2LjZ6IiAvPgo8L3N2Zz4=");
}

    </style>
</head>
<body>

<h2 style="text-align:center;">Liste des Produits</h2>

<table>
    <tr>
        <th>ID</th>
        <th>Référence</th>
        <th>Titre</th>
        <th>Description</th>
        <th>Prix</th>
        <th>Quantité</th>
        <th>État</th>
        <th>Image</th>
        <th>Action</th>
    </tr>

    <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?= $row['id_prod'] ?></td>
        <td><?= $row['ref'] ?></td>
        <td><?= $row['titre'] ?></td>
        <td><?= $row['descrip'] ?></td>
        <td><?= $row['prix'] ?></td>
        <td><?= $row['quantite'] ?></td>
        <td><?= $row['etat'] ?></td>
        <td><?= $row['image'] ?></td>
        <td>
            <a class="btn-supprimer" href="?delete_id=<?= $row['id_prod'] ?>" onclick="return confirm('Supprimer ce produit ?')">Supprimer</a>
        </td>
    </tr>
    <?php endwhile; ?>

</table>

</body>
</html>

<?php $conn->close(); ?>

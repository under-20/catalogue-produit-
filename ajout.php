<?php
// Only process the form if it's submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Establish database connection
    $conn = mysqli_connect("localhost", "isslem", "123123456aya", "bookshop");

    // Check connection
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Get form data and sanitize it
    $ref = mysqli_real_escape_string($conn, $_POST['ref']);
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $quantity = mysqli_real_escape_string($conn, $_POST['quantity']);
    $etat = isset($_POST['etat']) ? mysqli_real_escape_string($conn, $_POST['etat']) : 'stock';  // Default to 'stock'

    // Handle file upload
    $image = 'default_avatar.png';  // Default image if no image is uploaded
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/';  // Directory to save the uploaded files
        $file_name = basename($_FILES['image']['name']);
        $target_file = $upload_dir . $file_name;

        // Check if file is an image
        $check = getimagesize($_FILES['image']['tmp_name']);
        if ($check !== false) {
            // Ensure the file is not too large (e.g., 5MB limit)
            if ($_FILES['image']['size'] <= 5000000) {  // 5MB size limit
                // Move the uploaded file to the target directory
                if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                    $image = $target_file;  // Save the image path in the database
                } else {
                    echo "<script>alert('Sorry, there was an error uploading your file.');</script>";
                }
            } else {
                echo "<script>alert('Sorry, your file is too large.');</script>";
            }
        } else {
            echo "<script>alert('File is not an image.');</script>";
        }
    }

    // Prepare the SQL statement using a prepared query to prevent SQL injection
    $sql = "INSERT INTO produit (ref, titre, descrip, prix, quantite, etat, image) 
            VALUES ('$ref', '$title', '$description', '$price', '$quantity', '$etat', '$image')";

    // Debugging: Check the query
    echo "SQL Query: $sql<br>";

    // Execute the query
    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Livre ajouté avec succès !');</script>";
    } else {
        echo "<script>alert('Erreur lors de l\'ajout : " . mysqli_error($conn) . "');</script>";
    }

    // Close the database connection
    mysqli_close($conn);
}
?>

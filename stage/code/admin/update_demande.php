<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['loggedin'])) {
    header('Location: ../index.html'); // Redirect if not logged in
    exit;
}

// Database connection parameters
$servername = "localhost";
$username = "root"; // Replace with your database username
$password = ""; // Replace with your database password
$dbname = "st"; // Replace with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables for form data
$id = $_POST['id'];
$titre = $_POST['titre'];
$description = $_POST['description'];
$fichierPDF = null; // Initialize to null initially

// File upload handling
if ($_FILES['newPDF']['error'] === UPLOAD_ERR_OK) {
    $pdfFile = $_FILES['newPDF'];
    
    // Check if it's a PDF file
    $fileType = pathinfo($pdfFile['name'], PATHINFO_EXTENSION);
    if ($fileType !== 'pdf') {
        die("Erreur: Le fichier doit être au format PDF.");
    }
    
    // Generate unique filename to avoid overwriting existing files
    $targetDir = "uploads/";
    $targetFilename = uniqid('pdf_') . '.' . $fileType;
    $targetPath = $targetDir . $targetFilename;
    
    // Move uploaded file to target directory
    if (!move_uploaded_file($pdfFile['tmp_name'], $targetPath)) {
        die("Erreur lors du téléchargement du fichier.");
    }
    
    // Set the filename to be stored in database
    $fichierPDF = $targetFilename;
}

// Prepare SQL statement to update data in the database
if ($fichierPDF) {
    $sql = "UPDATE besoin SET titre = ?, description = ?, fichierPDF = ? WHERE idBesoin = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $titre, $description, $fichierPDF, $id);
} else {
    $sql = "UPDATE besoin SET titre = ?, description = ? WHERE idBesoin = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $titre, $description, $id);
}

// Execute SQL statement
if ($stmt->execute()) {
    echo "<script>alert('Demande d\'offre mise à jour avec succès.'); window.location.href='demande.php';</script>";
} else {
    echo "Erreur lors de la mise à jour de la demande d'offre: " . $conn->error;
}

// Close statement and database connection
$stmt->close();
$conn->close();
?>

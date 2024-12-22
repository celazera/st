<?php
session_start();


if (!isset($_SESSION['loggedin'])) {
    header('Location: ../index.html'); // Redirect if not logged in
    exit;
}


$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "st"; 

$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$titre = $_POST['titre'];
$description = $_POST['description'];
$fichierPDF = null; 

// File upload handling
if ($_FILES['pdfFile']['error'] === UPLOAD_ERR_OK) {
    $pdfFile = $_FILES['pdfFile'];
    
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

// Prepare SQL statement to insert data into database
$sql = "INSERT INTO besoin (titre, description, fichierPDF) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $titre, $description, $fichierPDF);

// Execute SQL statement
if ($stmt->execute()) {
    echo "Demande d'offre ajoutée avec succès.";
    header('Location: demande.php');
} else {
    echo "Erreur lors de l'ajout de la demande d'offre: " . $conn->error;
}

// Close statement and database connection
$stmt->close();
$conn->close();
?>

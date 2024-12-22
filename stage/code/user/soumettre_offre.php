<?php
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['loggedin'])) {
    header('Location: ../index.html');
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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Handle form submission
    $idBesoin = $_POST['idBesoin'];
    $userid = $_SESSION['id']; // Assuming you store user ID in session
    $etat = "en attente"; // Default etat for new offre
    $titre = $_POST['titre'];
    $description = $_POST['description'];
    $pdfFile = $_FILES['pdfFile']['name'];
    $pdfFile_tmp = $_FILES['pdfFile']['tmp_name'];
    $uploads_directory = "../admin/uploads/"; // Path to store uploaded PDF files

    // Move uploaded file to uploads directory
    move_uploaded_file($pdfFile_tmp, $uploads_directory . $pdfFile);

    // Insert new offre into database
    $insert_sql = "INSERT INTO offre (idBesoin, userid, etat, pdfFile, titre, description) 
                   VALUES ('$idBesoin', '$userid', '$etat', '$pdfFile', '$titre', '$description')";

    if ($conn->query($insert_sql) === TRUE) {
        echo '<script>alert("Offre soumise avec succès !");</script>';
        header('Location:Avis.php');

    } else {
        echo "Error: " . $insert_sql . "<br>" . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Soumettre Offre</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            font-weight: bold;
            display: block;
            margin-bottom: 8px;
        }

        input[type="text"], textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .file-upload {
            margin-bottom: 20px;
        }

        .file-upload label {
            display: block;
            margin-bottom: 8px;
        }

        .submit-button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .submit-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Soumettre une Offre</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
            <input type="hidden" name="idBesoin" value="<?php echo $_GET['idBesoin']; ?>">
            <div class="form-group">
                <label for="titre">Titre</label>
                <input type="text" id="titre" name="titre" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="4" required></textarea>
            </div>
            <div class="file-upload">
                <label for="pdfFile">Uploader PDF</label>
                <input type="file" id="pdfFile" name="pdfFile" required>
            </div>
            <button type="submit" class="submit-button">Soumettre Offre</button>
        </form>
    </div>
</body>
</html>

<?php
// Close connection
$conn->close();
?>

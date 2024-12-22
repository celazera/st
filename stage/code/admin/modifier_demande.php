<?php
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['loggedin'])) {
    header('Location: ../index.html');
    exit;
}

// Database connection parameters
$servername = "localhost";
$username = "root"; // Change this if you have a different database user
$password = ""; // Change this if you have a database password
$dbname = "st"; // Replace with your database name

// Initialize variables
$id = $titre = $description = $fichierPDF = "";

// Check if ID parameter exists
if (isset($_POST['id'])) {
    $id = $_POST['id'];

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare a select statement
    $sql = "SELECT idBesoin, titre, description, fichierPDF FROM besoin WHERE idBesoin = ?";
    
    if ($stmt = $conn->prepare($sql)) {
        // Bind variables to the prepared statement as parameters
        $stmt->bind_param("i", $id);

        // Attempt to execute the prepared statement
        if ($stmt->execute()) {
            // Store result
            $stmt->store_result();

            // Check if a record exists
            if ($stmt->num_rows == 1) {
                // Bind result variables
                $stmt->bind_result($id, $titre, $description, $fichierPDF);

                // Fetch values
                $stmt->fetch();
            } else {
                echo "Aucun besoin trouvé.";
                exit;
            }
        } else {
            echo "Erreur lors de la récupération du besoin.";
            exit;
        }

        // Close statement
        $stmt->close();
    }

    // Close connection
    $conn->close();
} else {
    // If ID parameter is not set, redirect to user_home.php
    header("Location: user_home.php");
    exit;
}

// Function to display existing PDF file if available
function displayExistingPDF($fichierPDF) {
    if (!empty($fichierPDF)) {
        return "<p><strong>Fichier PDF actuel:</strong> <a href='uploads/{$fichierPDF}' target='_blank'>Voir PDF</a></p>";
    }
    return "";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Modifier Demande d'Offre</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 20px;
        }

        .navtop {
            background-color: #333;
            height: 60px;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            box-sizing: border-box;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1;
        }

        .navtop a {
            color: #fff;
            text-decoration: none;
            font-size: 18px;
            margin-right: 20px;
        }

        .navtop a:hover {
            color: #ccc;
        }

        .content {
            padding: 20px;
            margin-top: 80px; /* Adjusted margin top to accommodate the navtop */
            box-sizing: border-box;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        form {
            max-width: 600px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .form-group input[type=text], 
        .form-group textarea,
        .form-group input[type=file] {
            width: 100%;
            padding: 8px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .form-group textarea {
            height: 100px;
            resize: vertical;
        }

        .form-group button {
            padding: 10px 20px;
            background-color: #032B44;
            color: #ffffff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .form-group button:hover {
            background-color: #021f30;
        }
    </style>
</head>
<body>
    <nav class="navtop">
        <a href="user.php">Gestion des utilisateurs</a>
        <a href="demande.php">Gestion des demandes d'offres</a>
        <a href="offre.php">Gestion des offres</a>
        <a href="user_home.php">Votre espace</a>
    </nav>
    <div class="content">
        <h2>Modifier Demande d'Offre</h2>
        <form action="update_demande.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <div class="form-group">
                <label for="titre">Titre</label>
                <input type="text" id="titre" name="titre" value="<?php echo $titre; ?>" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" required><?php echo $description; ?></textarea>
            </div>
            <?php echo displayExistingPDF($fichierPDF); ?>
            <div class="form-group">
                <label for="newPDF">Télécharger un nouveau fichier PDF</label>
                <input type="file" id="newPDF" name="newPDF">
            </div>
            <div class="form-group">
                <button type="submit">Enregistrer les modifications</button>
            </div>
        </form>
    </div>
</body>
</html>

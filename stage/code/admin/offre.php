<?php
session_start();

// Redirect to login if not logged in or not admin
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
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

// Handle accept and reject actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $offre_id = $_POST['offre_id'];
    $action = $_POST['action'];

    if ($action == 'accepter') {
        $sql = "UPDATE offre SET etat = 'acceptée' WHERE idOffre = ?";
    } elseif ($action == 'refuser') {
        $sql = "UPDATE offre SET etat = 'refusée' WHERE idOffre = ?";
    } elseif ($action == 'supprimer') {
        $sql = "DELETE FROM offre WHERE idOffre = ?";
    }

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $offre_id);
    $stmt->execute();
    $stmt->close();
}

// Initialize array to store non-treated offers
$non_traite_offres = [];

// Query to fetch non-treated offers
$search = isset($_GET['search']) ? $_GET['search'] : '';
if (!empty($search)) {
    $sql = "SELECT o.idOffre, o.titre AS offre_titre, o.pdfFile, a.company_name, b.titre AS besoin_titre
            FROM offre o
            INNER JOIN besoin b ON o.idBesoin = b.idBesoin
            INNER JOIN accounts a ON o.userid = a.id
            WHERE o.etat = 'en attente'
            AND (o.titre LIKE '%$search%' OR a.company_name LIKE '%$search%' OR b.titre LIKE '%$search%')";
} else {
    $sql = "SELECT o.idOffre, o.titre AS offre_titre, o.pdfFile, a.company_name, b.titre AS besoin_titre
            FROM offre o
            INNER JOIN besoin b ON o.idBesoin = b.idBesoin
            INNER JOIN accounts a ON o.userid = a.id
            WHERE o.etat = 'en attente'";
}

$result = $conn->query($sql);

// Fetch non-treated offers
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $offre_id = $row['idOffre'];
        $offre_titre = htmlspecialchars($row['offre_titre']);
        $pdf_file = $row['pdfFile'];
        $company_name = htmlspecialchars($row['company_name']);
        $besoin_titre = htmlspecialchars($row['besoin_titre']);

        // Create PDF link
        $pdf_link = "uploads/$pdf_file";

        // Create a list item for each non-treated offer
        $non_traite_offres[] = "<tr>
                                    <td>$company_name</td>
                                    <td>$offre_titre</td>
                                    <td><a href='$pdf_link' target='_blank'>Télécharger PDF</a></td>
                                    <td>$besoin_titre</td>
                                    <td>
                                        <form action='' method='post' style='display:inline-block;'>
                                            <input type='hidden' name='offre_id' value='$offre_id'>
                                            <button type='submit' name='action' value='accepter' class='accept-button'>Accepter</button>
                                        </form>
                                        <form action='' method='post' style='display:inline-block;'>
                                            <input type='hidden' name='offre_id' value='$offre_id'>
                                            <button type='submit' name='action' value='refuser' class='reject-button'>Refuser</button>
                                        </form>
                                        <form action='' method='post' style='display:inline-block;'>
                                            <input type='hidden' name='offre_id' value='$offre_id'>
                                            <button type='submit' name='action' value='supprimer' class='delete-button'>Supprimer</button>
                                        </form>
                                    </td>
                                </tr>";
    }
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Offres Non Traitées</title>
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

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .accept-button, .reject-button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .accept-button {
            background-color: #4CAF50;
            color: white;
        }

        .accept-button:hover {
            background-color: #45a049;
        }

        .reject-button {
            background-color: #f44336;
            color: white;
        }

        .reject-button:hover {
            background-color: #d32f2f;
        }

        .search-container {
            position: absolute;
            top: 80px;
            left: 20px;
            z-index: 1;
        }

        .search-container input[type=text] {
            padding: 10px;
            margin-top: 10px;
            width: 200px;
            border: none;
            border-radius: 5px;
            font-size: 14px;
        }

        .search-container button {
            padding: 10px;
            margin-top: 10px;
            margin-left: 5px;
            background: #ccc;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }

    </style>
</head>
<body>
    <nav class="navtop">
        <a href="accept.php">offres acceptés </a>
        <a href="rejet.php">offres rejetés</a>
        <a href="offre.php">offres non traités</a>
        <a href="admin_dashboard.php">Votre espace</a>
    </nav>
    <div class="search-container">
        <form action="" method="get">
            <input type="text" placeholder="Rechercher..." name="search">
            <button type="submit"><i class="fa fa-search"></i></button>
        </form>
    </div>
    <div class="content">
        <h2>Liste des Offres Non Traitées</h2>
        <table>
            <thead>
                <tr>
                    <th>Entreprise</th>
                    <th>Titre de l'Offre</th>
                    <th>Fichier PDF</th>
                    <th>Besoin Associé</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (!empty($non_traite_offres)) {
                    foreach ($non_traite_offres as $offre) {
                        echo $offre;
                    }
                } else {
                    echo "<tr><td colspan='5'>Aucune offre non traitée trouvée</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>

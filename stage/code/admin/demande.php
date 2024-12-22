<?php
session_start();


if (!isset($_SESSION['loggedin'])) {
    header('Location: ../index.html');
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


$search_query = "";
$where_clause = "";


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $search_query = trim($_POST["search"]);
    if (!empty($search_query)) {
        $where_clause = " WHERE titre LIKE '%$search_query%' OR description LIKE '%$search_query%'";
    }
}


$sql = "SELECT idBesoin, titre, description, fichierPDF, datePublication FROM besoin $where_clause";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Besoins</title>
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
            margin-top: 80px; 
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

        .action-buttons {
            display: flex;
            gap: 10px;
        }

        .action-buttons button {
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .modify-button {
            background-color: #4CAF50;
            color: white;
        }

        .modify-button:hover {
            background-color: #45a049;
        }

        .delete-button {
            background-color: #f44336;
            color: white;
        }

        .delete-button:hover {
            background-color: #d32f2f;
        }

        .search-container {
            display: inline-block;
            vertical-align: middle;
            margin-bottom: 20px;
        }

        .search-container input[type=text] {
            padding: 10px;
            margin-top: 8px;
            font-size: 17px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .search-container button {
            padding: 10px 15px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .search-container button:hover {
            background-color: #45a049;
        }

        .add-button-container {
            display: inline-block;
            vertical-align: middle;
            margin-bottom: 10px;
            margin-top: -20px; 
        }

        .add-button {
            background-color: #032B44;
            color: #ffffff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            margin-left: 10px; 
        }

        .add-button:hover {
            background-color: #021f30;
        }
        .ajout{
            margin-left:1000px;
            margin-top: -60px;
        }
    </style>
</head>
<body>
    <nav class="navtop">
        <a href="user.php">Gestion des utilisateurs</a>
        <a href="demande.php">Gestion des demandes d'offres</a>
        <a href="offre.php">Gestion des offres</a>
        <a href="admin_dashboard.php">Votre espace</a>
    </nav>
    <div class="content">
        <div class="add-button-container">
            <div class="search-container">
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <input type="text" placeholder="Rechercher par titre ou description" name="search" value="<?php echo htmlspecialchars($search_query); ?>">
                    <button type="submit">Rechercher</button>
                </form>
            </div>
            <div class="ajout"><a href="ajouter_besoin.php" class="add-button">Ajouter un demande d'offre</a></div>
        </div>
        <h2>Liste des demandes d'offre</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Titre</th>
                    <th>Description</th>
                    <th>Fichier PDF</th>
                    <th>Date de Publication</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["idBesoin"] . "</td>";
                        echo "<td>" . $row["titre"] . "</td>";
                        echo "<td>" . $row["description"] . "</td>";
                        echo "<td>";
                        if (!empty($row["fichierPDF"])) {
                            echo "<a href='uploads/" . $row["fichierPDF"] . "' target='_blank'>Télécharger PDF</a>";
                        } else {
                            echo "Aucun fichier";
                        }
                        echo "</td>";
                        echo "<td>" . $row["datePublication"] . "</td>";
                        echo "<td class='action-buttons'>";
                        echo "<form action='modifier_demande.php' method='post' style='display: inline-block;'>";
                        echo "<input type='hidden' name='id' value='" . $row["idBesoin"] . "'>";
                        echo "<button type='submit' class='modify-button'>Modifier</button>";
                        echo "</form>";
                        echo "<form action='delete_demande.php' method='post' style='display: inline-block;'>";
                        echo "<input type='hidden' name='id' value='" . $row["idBesoin"] . "'>";
                        echo "<button type='submit' class='delete-button'>Supprimer</button>";
                        echo "</form>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>Aucun besoin trouvé</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>

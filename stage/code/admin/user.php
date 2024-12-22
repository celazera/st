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

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize search query variable
$search_query = "";

// Handle search form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["search"])) {
        $search_query = trim($_POST["search"]);
    }
}

// Fetch users from the accounts table, excluding admins
$sql = "SELECT id, username, email, role, company_name, contact_person, phone_number, address FROM accounts WHERE role != 'admin'";
if (!empty($search_query)) {
    $sql .= " AND (username LIKE '%$search_query%' OR email LIKE '%$search_query%')";
}
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Management</title>
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
            margin-bottom: 20px;
            text-align: left; /* Align left for search container */
            display: flex;
            align-items: center;
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
            margin-left: 10px; /* Add space between input and button */
        }

        .search-container button:hover {
            background-color: #45a049;
        }

        .add-button {
            margin-left: auto; /* Pushes the "Ajouter utilisateur" button to the right */
            background-color: #032B44;
            color: #ffffff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .add-button:hover {
            background-color: #025C80;
        }
    </style>
</head>

<body>
    <nav class="navtop">
        <a href="user.php">Gestion des utilisateurs</a>
        <a href="demande.php">Gestion des demandes d'offre</a>
        <a href="offre.php">Gestion des offres</a>
        <a href="admin_dashboard.php">Votre espace</a>
    </nav>
    
    <div class="content">
        <div class="search-container">
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <input type="text" placeholder="Rechercher par nom d'utilisateur ou email" name="search" value="<?php echo isset($search_query) ? htmlspecialchars($search_query) : ''; ?>">
                <button type="submit">Rechercher</button>
            </form>
            <a href="ajouter fournisseur.php" class="add-button">Ajouter Fournisseur</a>
            <a href="ajouter_admin.php" class="add-button">Ajouter admin</a>
        </div>
        
        <h2>Liste des utilisateurs</h2>
        
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom d'utilisateur</th>
                    <th>Email</th>
                    <th>Rôle</th>
                    <th>Nom de l'entreprise</th>
                    <th>Personne de contact</th>
                    <th>Numéro de téléphone</th>
                    <th>Adresse</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    // Output data of each row
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["id"] . "</td>";
                        echo "<td>" . $row["username"] . "</td>";
                        echo "<td>" . $row["email"] . "</td>";
                        if ($row["role"] == "user") {
                            echo "<td>fournisseur</td>";
                        } else {
                            echo "<td>" . $row["role"] . "</td>"; // Handle any other roles if they exist
                        }
                        echo "<td>" . $row["company_name"] . "</td>";
                        echo "<td>" . $row["contact_person"] . "</td>";
                        echo "<td>" . $row["phone_number"] . "</td>";
                        echo "<td>" . $row["address"] . "</td>";
                        echo "<td class='action-buttons'>
                                <form action='modify.php' method='post' style='display:inline-block;'>
                                    <input type='hidden' name='id' value='" . $row["id"] . "'>
                                    <button type='submit' class='modify-button'>Modifier</button>
                                </form>
                                <form action='delete.php' method='post' style='display:inline-block;'>
                                    <input type='hidden' name='id' value='" . $row["id"] . "'>
                                    <button type='submit' class='delete-button'>Supprimer</button>
                                </form>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='9'>Aucun utilisateur trouvé</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>

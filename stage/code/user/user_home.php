<?php
// Start session
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

// Fetch offers for the logged-in user
$user_id = $_SESSION['id'];

// Query to fetch offers and related besoin titles
$sql = "SELECT o.idOffre, o.titre AS offre_titre, b.titre AS besoin_titre, o.etat
        FROM offre o
        INNER JOIN besoin b ON o.idBesoin = b.idBesoin
        WHERE o.userid = $user_id";

$result = $conn->query($sql);

// Initialize arrays to categorize offers
$accepted_offres = [];
$refused_offres = [];
$not_treated_offres = [];

// Categorize offers based on their etat
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $offre_titre = htmlspecialchars($row['offre_titre']);
        $besoin_titre = htmlspecialchars($row['besoin_titre']);
        $offre_id = $row['idOffre'];

        switch ($row['etat']) {
            case 'acceptée':
                $accepted_offres[] = "<tr><td><strong>$offre_titre</strong></td><td>$besoin_titre</td></tr>";
                break;
            case 'refusée':
                $refused_offres[] = "<tr><td><strong>$offre_titre</strong></td><td>$besoin_titre</td></tr>";
                break;
            case 'en attente':
                $not_treated_offres[] = "<tr><td><strong>$offre_titre</strong></td><td>$besoin_titre</td></tr>";
                break;
            default:
                break;
        }
    }
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Vertical Icon Bar and Navtop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }
        /* Top Navigation Bar */
        .navtop {
            background-color: #333;
            height: 60px;
            width: calc(100% - 80px); /* Full width minus the width of the icon bar */
            display: flex;
            align-items: center;
			justify-content: space-between; /* Align items to the left */
            padding: 0 20px;
            box-sizing: border-box;
            position: fixed; /* Make the navtop fixed */
            top: 0;
            left: 60px; /* Shift it to the right by the width of the icon bar */
            z-index: 1; /* Ensure it stays on top */
        }
        /* Links in the navtop */
        .navtop a {
            color: #fff;
            text-decoration: none;
            font-size: 18px;
            margin-right: 20px;
        }
        .navtop a:hover {
            color: #ccc;
        }
        /* Icon Bar Styles */
        .icon-bar {
            display: flex;
            flex-direction: column;
            align-items: center;
            background-color: #333;
            height: 100%;
            width: 60px;
            padding: 20px 0; /* Add some padding to the top and bottom */
            box-sizing: border-box;
            position: fixed; /* Make the icon bar fixed */
            top: 0;
            left: 0;
            z-index: 2; /* Ensure it stays above the content but below the navtop */
        }
        /* Links in the icon bar */
        .icon-bar a {
            color: #fff;
            text-decoration: none;
            font-size: 25px;
            margin-bottom: 20px;
        }
        .icon-bar a:hover {
            color: #ccc;
        }
        /* Content Styles */
        .content {
            background-color: #f7f7f7;
            padding: 20px;
            margin: 65px 20px 20px 65px; 
            box-sizing: border-box;
        }
		
        .offre-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .offre-table th, .offre-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .offre-table th {
            background-color:#ADD8E6;
            color: white;
        }

        .offre-table td {
            background-color: #f7f7f7;
        }
    </style>
</head>
<body>
    <nav class="navtop">
        <a href="Avis.php">Voir Nos demandes d'offre</a>
        <a href="user_home.php">Votre espace</a>
    </nav>
    <div class="icon-bar">
        <a href="../profile.php"><i class="fas fa-user-circle"></i></a>
        <a href="../logout.php"><i class="fas fa-sign-out-alt"></i></a>
    </div>
    <div class="content">
        <p>Welcome back, <?=htmlspecialchars($_SESSION['name'], ENT_QUOTES)?>!</p>

        <!-- Accepted Offers -->
        <h3>Offres Acceptées</h3>
        <table class="offre-table">
            <thead>
                <tr>
                    <th>Titre de l'Offre</th>
                    <th>Titre de la demande</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($accepted_offres as $offre) {
                    echo $offre;
                }
                ?>
            </tbody>
        </table>

        <!-- Refused Offers -->
        <h3>Offres Refusées</h3>
        <table class="offre-table">
            <thead>
                <tr>
                    <th>Titre de l'Offre</th>
                    <th>Titre de la demande</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($refused_offres as $offre) {
                    echo $offre;
                }
                ?>
            </tbody>
        </table>

        <!-- Not Treated Offers -->
        <h3>Offres Non Traitées</h3>
        <table class="offre-table">
            <thead>
                <tr>
                    <th>Titre de l'Offre</th>
                    <th>Titre de la demande</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($not_treated_offres as $offre) {
                    echo $offre;
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>

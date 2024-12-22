<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
	header('Location: ../index.html');
	exit;
}

// Database connection
$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'st'; // Change this to your database name

$con = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
if (mysqli_connect_errno()) {
	exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}

// Fetch data from database
$query_offres_non_traitees = "SELECT COUNT(*) AS count FROM offre WHERE etat = 'en attente'";
$query_demandes_sans_offres = "SELECT COUNT(b.idBesoin) AS count FROM besoin b LEFT JOIN offre o ON b.idBesoin = o.idBesoin WHERE o.idBesoin IS NULL";
$query_offres_refusees = "SELECT COUNT(*) AS count FROM offre WHERE etat = 'refusée'";
$query_offres_acceptees = "SELECT COUNT(*) AS count FROM offre WHERE etat = 'acceptée'";

$result_offres_non_traitees = mysqli_query($con, $query_offres_non_traitees);
$result_demandes_sans_offres = mysqli_query($con, $query_demandes_sans_offres);
$result_offres_refusees = mysqli_query($con, $query_offres_refusees);
$result_offres_acceptees = mysqli_query($con, $query_offres_acceptees);

$count_offres_non_traitees = mysqli_fetch_assoc($result_offres_non_traitees)['count'];
$count_demandes_sans_offres = mysqli_fetch_assoc($result_demandes_sans_offres)['count'];
$count_offres_refusees = mysqli_fetch_assoc($result_offres_refusees)['count'];
$count_offres_acceptees = mysqli_fetch_assoc($result_offres_acceptees)['count'];

mysqli_close($con);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Vertical Icon Bar and Navtop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
        }
        .navtop {
            background-color: #333;
            height: 60px;
            width: calc(100% - 80px);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            box-sizing: border-box;
            position: fixed;
            top: 0;
            left: 60px;
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
        .icon-bar {
            display: flex;
            flex-direction: column;
            align-items: center;
            background-color: #333;
            height: 100%;
            width: 60px;
            padding: 20px 0;
            box-sizing: border-box;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 2;
        }
        .icon-bar a {
            color: #fff;
            text-decoration: none;
            font-size: 25px;
            margin-bottom: 20px;
        }
        .icon-bar a:hover {
            color: #ccc;
        }
        .content {
            padding: 20px;
            margin: 80px 20px 20px 80px;
            box-sizing: border-box;
        }
        .stats {
            display: flex;
            justify-content: space-around;
            margin-top: 20px;
        }
        .stat-box {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            flex: 1;
            margin: 0 10px;
        }
        .stat-box i {
            font-size: 50px;
            margin-bottom: 10px;
            color: #333;
        }
        .stat-box p {
            font-size: 18px;
            color: #333;
        }
        .stat-box .count {
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }
    </style>
</head>
<body>
    <nav class="navtop">
        <a href="user.php">Gestion des utilisateurs </a>
        <a href="demande.php">Gestion des demandes d'offres </a>
        <a href="offre.php">Gestion des offres </a>
        <a href="admin_dashboard.php">Votre espace</a>
    </nav>
    <div class="icon-bar">
        <a href="../profile.php"><i class="fas fa-user-circle"></i></a>
        <a href="../logout.php"><i class="fas fa-sign-out-alt"></i></a>
    </div>
    <div class="content">
        <h2 style="margin-top: 0;">Home Page</h2>
        <p>Welcome back, <?=htmlspecialchars($_SESSION['name'], ENT_QUOTES)?>!</p>
        <div class="stats">
            <div class="stat-box">
                <i class="fas fa-tasks"></i>
                <p>Offres non traitées</p>
                <div class="count"><?=$count_offres_non_traitees?></div>
            </div>
            <div class="stat-box">
                <i class="fas fa-envelope-open"></i>
                <p>Demandes sans offres</p>
                <div class="count"><?=$count_demandes_sans_offres?></div>
            </div>
            <div class="stat-box">
                <i class="fas fa-times-circle"></i>
                <p>Offres refusées</p>
                <div class="count"><?=$count_offres_refusees?></div>
            </div>
            <div class="stat-box">
                <i class="fas fa-check-circle"></i>
                <p>Offres acceptées</p>
                <div class="count"><?=$count_offres_acceptees?></div>
            </div>
        </div>
    </div>
</body>
</html>

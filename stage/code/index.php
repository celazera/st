<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EACORPORATION - Accueil</title>
    <style>
        /* Global Styles */
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-image: url('images/company_image.jpg'); /* Change path to your background image */
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            color: #fff;
        }
        .navtop {
            background-color: rgba(0, 0, 0, 0.5);
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
            z-index: 1000;
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
            margin-top: 50px; /* Adjusted margin-top for navigation bar */
            box-sizing: border-box;
            text-align: center;
        }
        .intro {
            background-color: rgba(255, 255, 255, 0.8);
            padding: 20px;
            border-radius: 10px;
            margin: auto;
            max-width: 800px;
        }
        .intro h2 {
            color: #032B44;
            font-size: 32px;
            margin-bottom: 10px;
        }
        .intro p {
            color: #333;
            font-size: 18px;
            line-height: 1.6;
            margin-bottom: 20px;
        }
        .intro img {
            max-width: 100%;
            height: auto;
            border-radius: 10px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <nav class="navtop">
        <a href="#home">Contactez-nous</a>
        <a href="login.html">Login</a>
        <a href="registration.html">Devenez notre fournisseur</a>
    </nav>
    <div class="content">
        <div class="intro" id="home">
            <h2>Bienvenue chez EACORPORATION</h2>
            <p>EACORPORATION est spécialisée dans les travaux de construction, rénovation et aménagement intérieur et extérieur.</p>
            <p>Notre site facilite le contact avec les fournisseurs, assurant une meilleure collaboration pour vos projets.</p>
            <!-- Remove the image tag here -->
        </div>
    </div>
</body>
</html>

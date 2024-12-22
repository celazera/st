<?php
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['loggedin'])) {
    header('Location: ../index.html');
    exit;
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Include the database connection file
    $DATABASE_HOST = 'localhost';
    $DATABASE_USER = 'root';
    $DATABASE_PASS = '';
    $DATABASE_NAME = 'st';

    // Create connection
    $con = new mysqli($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);

    // Check connection
    if ($con->connect_error) {
        die("Connection failed: " . $con->connect_error);
    }

    // Get the form data
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password

    // Check if the account with that username already exists
    if ($stmt = $con->prepare('SELECT id FROM accounts WHERE username = ?')) {
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            // Username already exists
            echo "<script>alert('Username exists, please choose another!'); window.location.href='registeradmin.php';</script>";
            $stmt->close();
            $con->close();
            exit();
        }
        $stmt->close();
    }

    // Prepare the INSERT statement for admin role
    $stmt = $con->prepare('INSERT INTO accounts (username, password, email, role, company_name, contact_person, phone_number, address) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
    $role = 'admin'; // Set role to admin
    $empty = ''; // Empty value for other fields
    $stmt->bind_param('ssssssss', $username, $password, $email, $role, $empty, $empty, $empty, $empty);

    // Execute the statement and check for success
    if ($stmt->execute()) {
        echo "<script>alert('Admin registered successfully.'); window.location.href='user.php';</script>";
    } else {
        echo "<script>alert('Error registering admin.'); window.location.href='registeradmin.php';</script>";
    }

    // Close statement and connection
    $stmt->close();
    $con->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 20px;
            background-image: url('../images/f2.jpg');
        }

        .container {
            max-width: 600px;
            margin: 50px auto;
            background-color: rgba(255, 255, 255, 0.5);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            animation: fade 0.5s ease forwards;
        }

        @keyframes fade {
            0% {
                opacity: 0;
                transform: translateY(-20px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: bold;
        }

        .form-group input {
            width: calc(100% - 20px);
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }

        .form-group input[type="password"] {
            background-color: #f9f9f9;
        }

        .form-actions {
            text-align: center;
            margin-top: 20px;
        }

        .form-actions button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .form-actions button:hover {
            background-color: #45a049;
        }

        .form-actions a {
            text-decoration: none;
            color: #333;
            margin-left: 20px;
            font-size: 14px;
            transition: color 0.3s ease;
        }

        .form-actions a:hover {
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Ajouter Admin</h2>
        <form action="ajouter_admin.php" method="post">
            <div class="form-group">
                <label for="username">Nom d'utilisateur</label>
                <input type="text" id="username" name="username" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <div class="form-actions">
                <button type="submit">Enregistrer</button>
                <a href="user.php">Annuler</a>
            </div>
        </form>
    </div>
</body>
</html>

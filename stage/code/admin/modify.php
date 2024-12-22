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

// Get user ID from POST request
if (isset($_POST['id'])) {
    $user_id = $_POST['id'];

    // Fetch user details
    $sql = "SELECT * FROM accounts WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (!$user) {
        die("User not found");
    }
} else {
    die("User ID not provided");
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $role = trim($_POST['role']);
    $company_name = trim($_POST['company_name']);
    $contact_person = trim($_POST['contact_person']);
    $phone_number = trim($_POST['phone_number']);
    $address = trim($_POST['address']);
    $password = trim($_POST['password']);

    // Update password only if provided
    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "UPDATE accounts SET username = ?, email = ?, role = ?, company_name = ?, contact_person = ?, phone_number = ?, address = ?, password = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssssi", $username, $email, $role, $company_name, $contact_person, $phone_number, $address, $hashed_password, $user_id);
    } else {
        $sql = "UPDATE accounts SET username = ?, email = ?, role = ?, company_name = ?, contact_person = ?, phone_number = ?, address = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssi", $username, $email, $role, $company_name, $contact_person, $phone_number, $address, $user_id);
    }

    if ($stmt->execute()) {
        header("Location: user.php");
        exit();
    } else {
        echo "Error updating record: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier l'utilisateur</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 20px;
            font-family: Arial, sans-serif;
            background-image: url('../images/f2.jpg'); /* Change path to your background image */
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            color: #fff;
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

        .form-group input, .form-group select {
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
        <h2>Modifier l'utilisateur</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <input type="hidden" name="id" value="<?php echo $user_id; ?>">
            
            <div class="form-group">
                <label for="username">Nom d'utilisateur</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="role">Rôle</label>
                <select id="role" name="role" required>
                    <option value="user" <?php echo $user['role'] == 'user' ? 'selected' : ''; ?>>Fournisseur</option>
                    <option value="admin" <?php echo $user['role'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="company_name">Nom de l'entreprise</label>
                <input type="text" id="company_name" name="company_name" value="<?php echo htmlspecialchars($user['company_name']); ?>">
            </div>
            
            <div class="form-group">
                <label for="contact_person">Personne de contact</label>
                <input type="text" id="contact_person" name="contact_person" value="<?php echo htmlspecialchars($user['contact_person']); ?>">
            </div>
            
            <div class="form-group">
                <label for="phone_number">Numéro de téléphone</label>
                <input type="text" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($user['phone_number']); ?>">
            </div>
            
            <div class="form-group">
                <label for="address">Adresse</label>
                <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($user['address']); ?>">
            </div>
            
            <div class="form-group">
                <label for="password">Mot de passe (laisser vide pour ne pas changer)</label>
                <input type="password" id="password" name="password">
            </div>
            
            <div class="form-actions">
                <button type="submit" name="update">Modifier</button>
                <a href="user.php">Annuler</a>
            </div>
        </form>
    </div>
</body>
</html>


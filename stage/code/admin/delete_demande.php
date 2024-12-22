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

// Check if ID parameter exists
if (isset($_POST['id'])) {
    $id = $_POST['id'];

    // Prepare a delete statement
    $sql = "DELETE FROM besoin WHERE idBesoin = ?";

    if ($stmt = $conn->prepare($sql)) {
        // Bind variables to the prepared statement as parameters
        $stmt->bind_param("i", $id);

        // Attempt to execute the prepared statement
        if ($stmt->execute()) {
            // Redirect to user_home.php after deletion
            header("Location: user_home.php");
            exit;
        } else {
            echo "Erreur lors de la suppression du besoin.";
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
?>

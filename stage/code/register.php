<?php

$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'st';

$con = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
if (mysqli_connect_errno()) {
    
    exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}


if (!isset($_POST['username'], $_POST['password'], $_POST['email'])) {
    
    exit('Please complete the registration form!');
}


if (empty($_POST['username']) || empty($_POST['password']) || empty($_POST['email'])) {
   
    exit('Please complete the registration form');
}


$password = password_hash($_POST['password'], PASSWORD_DEFAULT);


if ($stmt = $con->prepare('SELECT id FROM accounts WHERE username = ?')) {
    $stmt->bind_param('s', $_POST['username']);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
       
        exit('Username exists, please choose another!');
    }
    $stmt->close();
}


$stmt = $con->prepare('INSERT INTO accounts (username, password, email, role, company_name, contact_person, phone_number, address) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
$role = 'user'; 
$stmt->bind_param('ssssssss', $_POST['username'], $password, $_POST['email'], $role, $_POST['company_name'], $_POST['contact_person'], $_POST['phone_number'], $_POST['address']);


if ($stmt->execute()) {
    echo 'You have successfully registered! You can now login!';
    header('Location:login.html');

} else {
    echo 'Could not execute statement: ' . $stmt->error;
}

$stmt->close();
$con->close();
?>

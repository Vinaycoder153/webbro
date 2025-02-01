<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $phoneno = $_POST['phone'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $users = file_exists('users.json') ? json_decode(file_get_contents('users.json'), true) : [];
    if (isset($users[$username])) {
        echo "Username already exists!";
        exit;
    }

    $users[$username] = ['password' => $password];
    file_put_contents('users.json', json_encode($users));
    echo "Registration successful!";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="log.css">
</head>

<body>
    <form method="POST" class="register-form">
        <h2 class="register-header">Register</h2>
        <input type="text" name="first_name" placeholder="First Name" required><br>
        <input type="text" name="last_name" placeholder="Last Name" required><br>
        <input type="text" name="email" placeholder="Email" required><br>
        <input type="text" name="phone" placeholder="Phone" required><br>
        <input type="text" name="username" placeholder="Username" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <button type="submit" class="register-button">Register</button>
    </form>
    <a href="login.php">Already have an account? Login here</a>
</body>

</html>
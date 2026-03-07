<?php
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username   = trim($_POST['username'] ?? '');
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name  = trim($_POST['last_name'] ?? '');
    $email      = trim($_POST['email'] ?? '');
    $phone      = trim($_POST['phone'] ?? '');
    $password   = $_POST['password'] ?? '';
    $confirm_pw = $_POST['confirm_password'] ?? '';

    if (empty($username) || empty($first_name) || empty($last_name) || empty($email) || empty($phone) || empty($password)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email address.";
    } elseif (!preg_match('/^\+?[0-9]{7,15}$/', $phone)) {
        $error = "Invalid phone number.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } elseif ($password !== $confirm_pw) {
        $error = "Passwords do not match.";
    } else {
        $users = file_exists('users.json') ? json_decode(file_get_contents('users.json'), true) : [];
        if (isset($users[$username])) {
            $error = "Username already exists!";
        } else {
            $users[$username] = [
                'password'   => password_hash($password, PASSWORD_DEFAULT),
                'first_name' => $first_name,
                'last_name'  => $last_name,
                'email'      => $email,
                'phone'      => $phone,
                'created_at' => date('Y-m-d H:i:s'),
            ];
            file_put_contents('users.json', json_encode($users, JSON_PRETTY_PRINT));
            $success = "Registration successful! You can now log in.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register – WebBro Shop</title>
    <link rel="stylesheet" href="log.css">
</head>

<body>
    <form method="POST" class="register-form">
        <h2 class="register-header">Register</h2>
        <?php if ($error): ?>
            <p class="form-msg error"><?= htmlspecialchars($error) ?></p>
        <?php elseif ($success): ?>
            <p class="form-msg success"><?= htmlspecialchars($success) ?></p>
        <?php endif; ?>
        <input type="text" name="first_name" placeholder="First Name" value="<?= htmlspecialchars($_POST['first_name'] ?? '') ?>" required><br>
        <input type="text" name="last_name" placeholder="Last Name" value="<?= htmlspecialchars($_POST['last_name'] ?? '') ?>" required><br>
        <input type="email" name="email" placeholder="Email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required><br>
        <input type="tel" name="phone" placeholder="Phone" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>" required><br>
        <input type="text" name="username" placeholder="Username" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required><br>
        <input type="password" name="password" placeholder="Password (min 6 chars)" required><br>
        <input type="password" name="confirm_password" placeholder="Confirm Password" required><br>
        <button type="submit" class="register-button">Register</button>
    </form>
    <a href="login.php">Already have an account? Login here</a>
</body>

</html>
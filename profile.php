<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];
$users    = file_exists('users.json') ? json_decode(file_get_contents('users.json'), true) : [];
$user     = $users[$username] ?? [];

$orders   = $_SESSION['orders'] ?? [];

$success = '';
$error   = '';

/* ── Handle profile update ──────────────────────────────────── */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'update_profile') {
    $new_first = trim($_POST['first_name'] ?? '');
    $new_last  = trim($_POST['last_name']  ?? '');
    $new_email = trim($_POST['email']      ?? '');
    $new_phone = trim($_POST['phone']      ?? '');

    if (empty($new_first) || empty($new_last) || empty($new_email) || empty($new_phone)) {
        $error = "All fields are required.";
    } elseif (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email address.";
    } else {
        $users[$username]['first_name'] = $new_first;
        $users[$username]['last_name']  = $new_last;
        $users[$username]['email']      = $new_email;
        $users[$username]['phone']      = $new_phone;
        file_put_contents('users.json', json_encode($users, JSON_PRETTY_PRINT));
        $user    = $users[$username];
        $success = "Profile updated successfully!";
    }
}

/* ── Handle password change ─────────────────────────────────── */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'change_password') {
    $current = $_POST['current_password'] ?? '';
    $new_pw  = $_POST['new_password']     ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if (!password_verify($current, $user['password'])) {
        $error = "Current password is incorrect.";
    } elseif (strlen($new_pw) < 6) {
        $error = "New password must be at least 6 characters.";
    } elseif ($new_pw !== $confirm) {
        $error = "Passwords do not match.";
    } else {
        $users[$username]['password'] = password_hash($new_pw, PASSWORD_DEFAULT);
        file_put_contents('users.json', json_encode($users, JSON_PRETTY_PRINT));
        $success = "Password changed successfully!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile – WebBro Shop</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .profile-container {
            max-width: 640px;
            margin: 30px auto;
            padding: 0 20px 40px;
        }
        .profile-card {
            background: white;
            border-radius: 12px;
            padding: 28px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
            margin-bottom: 24px;
        }
        .profile-card h2 {
            margin-top: 0;
            color: #333;
            font-size: 1.2em;
            border-bottom: 2px solid #ff6347;
            padding-bottom: 8px;
        }
        .profile-card label {
            display: block;
            font-size: 0.85em;
            color: #666;
            margin: 12px 0 4px;
        }
        .profile-card input[type="text"],
        .profile-card input[type="email"],
        .profile-card input[type="tel"],
        .profile-card input[type="password"] {
            width: 100%;
            padding: 9px 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 0.95em;
            box-sizing: border-box;
        }
        .profile-card button {
            margin-top: 16px;
        }
        .msg { padding: 8px 12px; border-radius: 4px; margin-bottom: 14px; font-size: 0.9em; }
        .msg.success { background: #e0ffe0; color: #006600; border: 1px solid #c3e6cb; }
        .msg.error   { background: #ffe0e0; color: #cc0000; border: 1px solid #f5c6cb; }
        .order-card {
            background: #fafafa;
            border: 1px solid #eee;
            border-radius: 8px;
            padding: 14px;
            margin-bottom: 14px;
            font-size: 0.9em;
        }
        .order-card .order-date { color: #888; font-size: 0.82em; margin-bottom: 6px; }
        .order-card .order-item { display: flex; justify-content: space-between; padding: 3px 0; }
        .order-card .order-total { font-weight: bold; margin-top: 8px; color: #ff6347; text-align: right; }
        .no-orders { color: #aaa; text-align: center; padding: 20px 0; }
    </style>
</head>
<body>
    <header>
        <div class="header-left">
            <h1>🛍️ WebBro Shop</h1>
            <p class="header-subtitle">My Profile</p>
        </div>
        <div class="header-right">
            <a href="index.php" class="nav-link">🏠 Shop</a>
            <a href="wishlist.php" class="nav-link">❤️ Wishlist</a>
            <a href="logout.php" class="nav-link logout-link">Logout</a>
        </div>
    </header>

    <div class="profile-container">

        <?php if ($success): ?>
            <p class="msg success"><?= htmlspecialchars($success) ?></p>
        <?php elseif ($error): ?>
            <p class="msg error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <!-- Profile Info -->
        <div class="profile-card">
            <h2>👤 Account Details</h2>
            <p><strong>Username:</strong> <?= htmlspecialchars($username) ?></p>
            <?php if (!empty($user['created_at'])): ?>
                <p><strong>Member since:</strong> <?= htmlspecialchars($user['created_at']) ?></p>
            <?php endif; ?>
        </div>

        <!-- Edit Profile -->
        <div class="profile-card">
            <h2>✏️ Edit Profile</h2>
            <form method="POST">
                <input type="hidden" name="action" value="update_profile">
                <label>First Name</label>
                <input type="text" name="first_name" value="<?= htmlspecialchars($user['first_name'] ?? '') ?>" required>
                <label>Last Name</label>
                <input type="text" name="last_name" value="<?= htmlspecialchars($user['last_name'] ?? '') ?>" required>
                <label>Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
                <label>Phone</label>
                <input type="tel" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" required>
                <button type="submit">💾 Save Changes</button>
            </form>
        </div>

        <!-- Change Password -->
        <div class="profile-card">
            <h2>🔒 Change Password</h2>
            <form method="POST">
                <input type="hidden" name="action" value="change_password">
                <label>Current Password</label>
                <input type="password" name="current_password" required>
                <label>New Password (min 6 chars)</label>
                <input type="password" name="new_password" required>
                <label>Confirm New Password</label>
                <input type="password" name="confirm_password" required>
                <button type="submit">🔑 Change Password</button>
            </form>
        </div>

        <!-- Order History -->
        <div class="profile-card">
            <h2>📦 Order History</h2>
            <?php if (empty($orders)): ?>
                <p class="no-orders">No orders yet. Start shopping!</p>
            <?php else: ?>
                <?php foreach (array_reverse($orders) as $i => $order): ?>
                    <div class="order-card">
                        <p class="order-date">🕐 <?= htmlspecialchars($order['date']) ?></p>
                        <?php foreach ($order['items'] as $name => $data): ?>
                            <div class="order-item">
                                <span><?= htmlspecialchars($name) ?> × <?= (int)$data['qty'] ?></span>
                                <span>₹<?= number_format($data['price'] * $data['qty']) ?></span>
                            </div>
                        <?php endforeach; ?>
                        <p class="order-total">Total: ₹<?= number_format($order['total']) ?></p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

    </div>
</body>
</html>

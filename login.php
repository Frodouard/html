<?php
session_start();
require 'config.php';
$message = '';

if (isset($_SESSION['user_id'])) {
    header('Location: expenses.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        $message = 'Please enter both username and password.';
    } else {
        $stmt = $mysqli->prepare('SELECT id, password_hash FROM users WHERE username = ?');
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $stmt->bind_result($id, $hash);

        if ($stmt->fetch()) {
            $loggedIn = false;

            if (password_verify($password, $hash)) {
                $loggedIn = true;
            } elseif (hash_equals($hash, hash('sha256', $password))) {
                $loggedIn = true;
                $newHash = password_hash($password, PASSWORD_DEFAULT);
                $update = $mysqli->prepare('UPDATE users SET password_hash = ? WHERE id = ?');
                if ($update) {
                    $update->bind_param('si', $newHash, $id);
                    $update->execute();
                    $update->close();
                }
            }

            if ($loggedIn) {
                $_SESSION['user_id'] = $id;
                $_SESSION['username'] = $username;
                header('Location: expenses.php');
                exit;
            }
        }

        $message = 'Invalid login credentials. Please try again.';
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Customer Daily Expense Management</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f8fb; color: #333; margin: 0; padding: 0; display: flex; align-items: center; justify-content: center; height: 100vh; }
        .login-box { background: #ffffff; padding: 28px; border-radius: 12px; box-shadow: 0 18px 48px rgba(0,0,0,0.08); width: 320px; }
        h1 { margin-top: 0; font-size: 24px; color: #044d9b; }
        .form-group { margin-bottom: 16px; }
        label { display: block; margin-bottom: 6px; font-weight: 600; }
        input[type="text"], input[type="password"] { width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; }
        button { width: 100%; padding: 12px; border: none; border-radius: 8px; background: #1f7fe2; color: white; cursor: pointer; font-size: 16px; }
        .message { margin-top: 16px; color: #b91c1c; }
    </style>
</head>
<body>
    <div class="login-box">
        <h1>Expense Management Login</h1>
        <form method="post" novalidate>
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Login</button>
            <?php if ($message): ?>
                <p class="message"><?= htmlspecialchars($message) ?></p>
            <?php endif; ?>
            <p style="margin-top: 14px; font-size: 13px; color: #555;">Use <strong>admin</strong> / <strong>admin123</strong> or configure your own credentials in <code>config.php</code>.</p>
        </form>
    </div>
</body>
</html>

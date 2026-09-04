<?php
// config.php - Database connection and auto schema setup
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'customer_expense_management';

$mysqli = new mysqli($host, $user, $pass);
if ($mysqli->connect_error) {
    die('Database connection failed: ' . $mysqli->connect_error);
}

$mysqli->query("CREATE DATABASE IF NOT EXISTS `{$db}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
$mysqli->select_db($db);

$mysqli->query("CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(100) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

$mysqli->query("CREATE TABLE IF NOT EXISTS `expenses` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `customer_name` VARCHAR(100) NOT NULL,
    `customer_phone` VARCHAR(20) NOT NULL,
    `expense_date` DATE NOT NULL,
    `expense_description` TEXT NOT NULL,
    `amount_spent` DECIMAL(10,2) NOT NULL,
    `payment_method` ENUM('Cash','Mobile Money','Bank') NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

$defaultPassword = 'admin123';
$result = $mysqli->query("SELECT COUNT(*) AS total FROM users WHERE username='admin'");
if ($result) {
    $row = $result->fetch_assoc();
    if ($row['total'] == 0) {
        $passwordHash = password_hash($defaultPassword, PASSWORD_DEFAULT);
        $stmt = $mysqli->prepare("INSERT INTO users (username, password_hash) VALUES (?, ?)");
        if ($stmt) {
            $userName = 'admin';
            $stmt->bind_param('ss', $userName, $passwordHash);
            $stmt->execute();
            $stmt->close();
        }
    }
}

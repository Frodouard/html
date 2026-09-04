-- Database schema for Customer Daily Expense Management
CREATE DATABASE IF NOT EXISTS `customer_expense_management` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `customer_expense_management`;

CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(100) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `expenses` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `customer_name` VARCHAR(100) NOT NULL,
  `customer_phone` VARCHAR(20) NOT NULL,
  `expense_date` DATE NOT NULL,
  `expense_description` TEXT NOT NULL,
  `amount_spent` DECIMAL(10,2) NOT NULL,
  `payment_method` ENUM('Cash','Mobile Money','Bank') NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO `users` (`username`, `password_hash`) VALUES ('admin', '$2y$12$r.SMonltoXO5PxFQZWk6gOC9DqzD5E5RM.Hk2T.uFFtfAKOvYKZVO');

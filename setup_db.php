<?php
// setup_db.php
require_once 'config.php';

try {
    $sql = "
    CREATE TABLE IF NOT EXISTS `users` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `username` VARCHAR(50) NOT NULL UNIQUE,
        `password` VARCHAR(255) NOT NULL,
        `fullname` VARCHAR(100) NOT NULL,
        `role` ENUM('admin', 'officer') NOT NULL DEFAULT 'officer',
        `station_name` VARCHAR(100) DEFAULT 'ស្រុកពញាក្រែក',
        `failed_attempts` INT DEFAULT 0,
        `is_blocked` TINYINT(1) DEFAULT 0,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS `login_logs` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `user_id` INT NULL,
        `username_attempted` VARCHAR(50) NOT NULL,
        `ip_address` VARCHAR(45) NOT NULL,
        `user_agent` TEXT NOT NULL,
        `status` ENUM('success', 'failed', 'blocked_attempt') NOT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS `unblock_requests` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `user_id` INT NOT NULL,
        `reason` TEXT NOT NULL,
        `status` ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
    );

    INSERT INTO `users` (`username`, `password`, `fullname`, `role`, `station_name`) VALUES
    ('admin', '$2y$10$7pagvtQGMT9Z2IUzl2DAZ.79YJKg5m8.Qte7L/2fiVHpTjmg9ekgK', 'អ្នកគ្រប់គ្រងប្រព័ន្ធ', 'admin', 'ស្នងការដ្ឋាន'),
    ('officer_krek', '$2y$10$7pagvtQGMT9Z2IUzl2DAZ.79YJKg5m8.Qte7L/2fiVHpTjmg9ekgK', 'មន្ត្រីប៉ុស្តិ៍ពញាក្រែក', 'officer', 'ប៉ុស្តិ៍នគរបាលរដ្ឋបាលពញាក្រែក')
    ON DUPLICATE KEY UPDATE `username`=`username`;
    ";

    $pdo->exec($sql);
    echo "<h1>✅ បានបង្កើតតារាង SQL និងទិន្នន័យគំរូដោយជោគជ័យ ១០០%!</h1>";
    echo "<p><a href='index.php'>ចុចទីនេះដើម្បីត្រឡប់ទៅទំព័រ Login</a></p>";
} catch (PDOException $e) {
    echo "<h1>❌ មានបញ្ហា៖ " . $e->getMessage() . "</h1>";
}

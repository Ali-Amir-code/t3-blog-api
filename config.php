<?php
header("Content-Type: application/json; charset=UTF-8");
// These options are for XAMPP local server.
define('DB_HOST', 'localhost');
define('DB_NAME', 'blog_api');
define('DB_USER', 'root');
define('DB_PASS', '');

try {
    // Step 1: Connect without database
    $pdo = new PDO(
        "mysql:host=".DB_HOST.";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    // Step 2: Create database if not exists
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `".DB_NAME."` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

    // Step 3: Connect to the database
    $pdo->exec("USE `".DB_NAME."`");
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Database connection failed"]);
    exit;
}

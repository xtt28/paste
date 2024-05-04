<?php

$hostname = "localhost";
$database = "paste";
$username = "root";
$password = "";

try {
    $conn = new PDO("mysql:host=$hostname;dbname=$database", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    error_log("Could not connect to database: $e");
    die("Could not create a database connection.");
}

$schema = "CREATE TABLE IF NOT EXISTS `pastes` (
    `id` CHAR(8) NOT NULL DEFAULT (LEFT(REPLACE(UUID(), '-', ''), 8)),
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `expires_at` DATETIME,
    `delete_token` CHAR(16) NOT NULL DEFAULT (LEFT(REPLACE(UUID(), '-', ''), 16)),
    `content` MEDIUMTEXT NOT NULL CHECK(LENGTH(`content`) <= 400000),
    PRIMARY KEY(`id`)
)";

try {
    $conn->exec($schema);
} catch (PDOException $e) {
    error_log("Could not initialize database schema: $e");
    die("Could not initialize the database.");
}
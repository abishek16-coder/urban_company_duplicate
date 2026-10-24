<?php
require 'd:/ABISHEK/urban/vendor/autoload.php';
require 'd:/ABISHEK/urban/config/PostgresDatabase.php';
require 'd:/ABISHEK/urban/config/Database.php';

try {
    $pdo = Config\Database::getPdo();
    echo "Neon DB connected! Driver: " . $pdo->getAttribute(PDO::ATTR_DRIVER_NAME) . "\n";
    // Test a simple query
    $stmt = $pdo->query("SELECT version()");
    $row = $stmt->fetch();
    echo "PG Version: " . $row['version'] . "\n";
} catch(Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

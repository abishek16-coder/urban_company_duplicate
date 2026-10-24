<?php
$neonUrl = 'postgresql://neondb_owner:npg_JRz64yKTkteD@ep-withered-mud-b4pm71nk-pooler.c-6.us-east-2.aws.neon.tech/neondb?sslmode=require&channel_binding=require';

$parsed = parse_url($neonUrl);
$host = $parsed['host'];
$port = $parsed['port'] ?? 5432;
$db = ltrim($parsed['path'], '/');
$user = $parsed['user'];
$pass = $parsed['pass'];

$dsn = "pgsql:host={$host};port={$port};dbname={$db};sslmode=require";
echo "Attempting to connect to Neon PostgreSQL at {$host}...\n";

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    $version = $pdo->query('SELECT version()')->fetchColumn();
    echo "SUCCESS: Connected to Neon PostgreSQL!\n";
    echo "Database: {$db}\n";
    echo "Version: {$version}\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

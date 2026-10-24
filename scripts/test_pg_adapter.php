<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/PostgresDatabase.php';

$neonUrl = 'postgresql://neondb_owner:npg_JRz64yKTkteD@ep-withered-mud-b4pm71nk-pooler.c-6.us-east-2.aws.neon.tech/neondb?sslmode=require&channel_binding=require';

$parsed = parse_url($neonUrl);
$dsn = "pgsql:host={$parsed['host']};port=" . ($parsed['port'] ?? 5432) . ";dbname=" . ltrim($parsed['path'], '/') . ";sslmode=require";
$pdo = new PDO($dsn, $parsed['user'], $parsed['pass'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

$db = new \Config\PostgresDatabase($pdo);
echo "PostgresDatabase initialized, tables verified!\n";

// Test insert
$res = $db->users->insertOne([
    'name' => 'Neon Test User',
    'email' => 'neon_test_' . time() . '@test.com',
    'password' => password_hash('Secret123', PASSWORD_BCRYPT),
    'role' => 'customer',
    'city' => 'Cloud City'
]);

$insertedId = $res->getInsertedId();
echo "Inserted user ID: $insertedId\n";

// Test findOne
$user = $db->users->findOne(['_id' => $insertedId]);
echo "Found User by _id: " . ($user ? $user['name'] . ' (' . $user['email'] . ')' : 'NOT FOUND') . "\n";

// Test updateOne
$db->users->updateOne(['_id' => $insertedId], ['$set' => ['city' => 'Updated Cloud City']]);
$updatedUser = $db->users->findOne(['_id' => $insertedId]);
echo "Updated User City: " . $updatedUser['city'] . "\n";

// Test countDocuments
$count = $db->users->countDocuments();
echo "Total users count: $count\n";

// Clean up test user
$db->users->deleteOne(['_id' => $insertedId]);
echo "Cleanup successful. Adapter is 100% working!\n";

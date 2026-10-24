<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Config\Database;

$db = Database::getDb();

// Drop existing data
$db->drop();

// Create Admin
$adminId = new \MongoDB\BSON\ObjectId();
$db->users->insertOne([
    '_id' => $adminId,
    'name' => 'System Admin',
    'email' => 'admin@serviceconnect.com',
    'password' => password_hash('Admin@123', PASSWORD_BCRYPT),
    'role' => 'admin',
    'phone' => '9876543210',
    'address' => 'HQ',
    'city' => 'New York',
    'created_at' => new \MongoDB\BSON\UTCDateTime()
]);

// Create Categories
$cat1 = new \MongoDB\BSON\ObjectId();
$cat2 = new \MongoDB\BSON\ObjectId();
$db->categories->insertMany([
    ['_id' => $cat1, 'name' => 'Electrician', 'slug' => 'electrician', 'icon' => 'bi-lightning'],
    ['_id' => $cat2, 'name' => 'Plumber', 'slug' => 'plumber', 'icon' => 'bi-wrench']
]);

// Create Provider User & Profile
$provUserId = new \MongoDB\BSON\ObjectId();
$db->users->insertOne([
    '_id' => $provUserId,
    'name' => 'John Electric',
    'email' => 'provider@serviceconnect.com',
    'password' => password_hash('Provider@123', PASSWORD_BCRYPT),
    'role' => 'provider',
    'phone' => '9123456789',
    'address' => '45 Main St',
    'city' => 'New York',
    'created_at' => new \MongoDB\BSON\UTCDateTime()
]);

$provId = new \MongoDB\BSON\ObjectId();
$db->providers->insertOne([
    '_id' => $provId,
    'user_id' => $provUserId,
    'business_name' => 'John Pro Electricals',
    'category_ids' => [$cat1],
    'description' => 'Expert residential and commercial electrical services.',
    'city' => 'New York',
    'is_verified' => true,
    'rating_avg' => 4.5,
    'rating_count' => 2,
    'created_at' => new \MongoDB\BSON\UTCDateTime()
]);

// Create Service
$db->services->insertOne([
    '_id' => new \MongoDB\BSON\ObjectId(),
    'provider_id' => $provId,
    'name' => 'Wiring Repair',
    'description' => 'Fix short circuits and faulty wiring safely.',
    'price' => 50.00,
    'duration' => '1 hour',
    'is_active' => true,
    'created_at' => new \MongoDB\BSON\UTCDateTime()
]);

// Create Customer
$custUserId = new \MongoDB\BSON\ObjectId();
$db->users->insertOne([
    '_id' => $custUserId,
    'name' => 'Alice Customer',
    'email' => 'customer@serviceconnect.com',
    'password' => password_hash('Customer@123', PASSWORD_BCRYPT),
    'role' => 'customer',
    'phone' => '9988776655',
    'address' => '12 Park Ave',
    'city' => 'New York',
    'created_at' => new \MongoDB\BSON\UTCDateTime()
]);

echo "Database seeded successfully!\n";

<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/PostgresDatabase.php';
require_once __DIR__ . '/../config/Database.php';

use Config\Database;

$db = Database::getDb();

echo "Connected to Neon PostgreSQL. Seeding...\n";

// Clear existing data
$db->drop();
echo "Tables cleared.\n";

// Create Admin
$db->users->insertOne([
    'name'       => 'System Admin',
    'email'      => 'admin@serviceconnect.com',
    'password'   => password_hash('Admin@123', PASSWORD_BCRYPT),
    'role'       => 'admin',
    'phone'      => '9876543210',
    'address'    => 'HQ Building',
    'city'       => 'Mumbai',
]);
echo "Admin created.\n";

// Create Categories
$db->categories->insertOne(['name' => 'Electrician',   'slug' => 'electrician',   'icon' => 'bi-lightning-charge']);
$db->categories->insertOne(['name' => 'Plumber',       'slug' => 'plumber',       'icon' => 'bi-wrench-adjustable']);
$db->categories->insertOne(['name' => 'Carpenter',     'slug' => 'carpenter',     'icon' => 'bi-tools']);
$db->categories->insertOne(['name' => 'Painter',       'slug' => 'painter',       'icon' => 'bi-brush']);
$db->categories->insertOne(['name' => 'Cleaning',      'slug' => 'cleaning',      'icon' => 'bi-bucket']);
$db->categories->insertOne(['name' => 'AC Repair',     'slug' => 'ac-repair',     'icon' => 'bi-wind']);
echo "Categories created.\n";

// Look up category IDs
$elec = $db->categories->findOne(['slug' => 'electrician']);
$plumb = $db->categories->findOne(['slug' => 'plumber']);

// Create Provider User
$provResult = $db->users->insertOne([
    'name'     => 'John Electric',
    'email'    => 'provider@serviceconnect.com',
    'password' => password_hash('Provider@123', PASSWORD_BCRYPT),
    'role'     => 'provider',
    'phone'    => '9123456789',
    'address'  => '45 Main St',
    'city'     => 'Mumbai',
]);
$provUserId = $provResult->getInsertedId();

$provProfileResult = $db->providers->insertOne([
    'user_id'       => $provUserId,
    'business_name' => 'John Pro Electricals',
    'category_ids'  => [$elec['id']],
    'description'   => 'Expert residential and commercial electrical services.',
    'city'          => 'Mumbai',
    'is_verified'   => true,
    'rating_avg'    => 4.5,
    'rating_count'  => 12,
]);
$provId = $provProfileResult->getInsertedId();

// Create Services
$db->services->insertOne([
    'provider_id' => $provId,
    'name'        => 'Wiring Repair',
    'description' => 'Fix short circuits and faulty wiring safely.',
    'price'       => 799.00,
    'duration'    => '2 hours',
    'is_active'   => true,
]);
$db->services->insertOne([
    'provider_id' => $provId,
    'name'        => 'Fan Installation',
    'description' => 'Professional ceiling fan installation and balancing.',
    'price'       => 499.00,
    'duration'    => '1 hour',
    'is_active'   => true,
]);
echo "Provider and services created.\n";

// Create Customer
$custResult = $db->users->insertOne([
    'name'     => 'Alice Customer',
    'email'    => 'customer@serviceconnect.com',
    'password' => password_hash('Customer@123', PASSWORD_BCRYPT),
    'role'     => 'customer',
    'phone'    => '9988776655',
    'address'  => '12 Park Avenue',
    'city'     => 'Mumbai',
]);
$custId = $custResult->getInsertedId();
echo "Customer created.\n";

// Create a sample booking
$svc = $db->services->findOne(['name' => 'Wiring Repair']);
$db->bookings->insertOne([
    'customer_id' => $custId,
    'provider_id' => $provId,
    'service_id'  => $svc['id'],
    'date'        => date('Y-m-d', strtotime('+3 days')),
    'time_slot'   => '10:00 AM - 12:00 PM',
    'status'      => 'Pending',
]);
echo "Sample booking created.\n";

echo "\n=== Seed Complete! ===\n";
echo "Admin:    admin@serviceconnect.com / Admin@123\n";
echo "Provider: provider@serviceconnect.com / Provider@123\n";
echo "Customer: customer@serviceconnect.com / Customer@123\n";

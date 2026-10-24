<?php
namespace Config;

use MongoDB\Client;

class Database {
    private static ?Client $client = null;
    private static ?string $dbName = null;

    public static function getConnection(): Client {
        if (self::$client === null) {
            $uri = $_ENV['MONGO_URI'] ?? 'mongodb://localhost:27017';
            self::$dbName = $_ENV['MONGO_DB'] ?? 'service_connect';
            self::$client = new Client($uri);
            self::initIndexes(self::$client->selectDatabase(self::$dbName));
        }
        return self::$client;
    }

    public static function getDb(): \MongoDB\Database {
        $client = self::getConnection();
        $dbName = $_ENV['MONGO_DB'] ?? 'service_connect';
        return $client->selectDatabase($dbName);
    }

    private static function initIndexes(\MongoDB\Database $db): void {
        try {
            $db->users->createIndex(['email' => 1], ['unique' => true]);
            $db->services->createIndex(['name' => 'text', 'description' => 'text']);
            $db->providers->createIndex(['category_ids' => 1, 'city' => 1]);
            $db->bookings->createIndex(['provider_id' => 1, 'date' => 1]);
        } catch (\Exception $e) {
            // Index creation failure handled or logged
        }
    }
}

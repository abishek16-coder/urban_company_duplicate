<?php
namespace Config;

require_once __DIR__ . '/PostgresDatabase.php';

use PDO;
use Exception;

class Database {
    private static $instance = null;
    private static ?string $driver = null;
    private static ?PDO $pdo = null;

    private static function loadEnv(): void {
        $envFile = __DIR__ . '/../.env';
        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line) || str_starts_with($line, '#')) continue;
                if (str_contains($line, '=')) {
                    [$k, $v] = explode('=', $line, 2);
                    $k = trim($k);
                    $v = trim($v, " \t\n\r\0\x0B\"'");
                    $_ENV[$k] = $v;
                    putenv("$k=$v");
                }
            }
        }
    }

    public static function getDriver(): string {
        self::loadEnv();
        $dbUrl = $_ENV['DATABASE_URL'] ?? getenv('DATABASE_URL') ?? '';
        $driver = $_ENV['DB_DRIVER'] ?? getenv('DB_DRIVER') ?? '';
        
        if ($driver === 'mongodb' || (!empty($_ENV['MONGO_URI']) && empty($dbUrl))) {
            return 'mongodb';
        }
        return 'postgres';
    }

    public static function getPdo(): PDO {
        self::loadEnv();
        if (self::$pdo === null) {
            $dbUrl = $_ENV['DATABASE_URL'] ?? getenv('DATABASE_URL') ?? 'postgresql://neondb_owner:npg_JRz64yKTkteD@ep-withered-mud-b4pm71nk-pooler.c-6.us-east-2.aws.neon.tech/neondb?sslmode=require&channel_binding=require';
            $parsed = parse_url($dbUrl);
            $host = $parsed['host'] ?? 'localhost';
            $port = $parsed['port'] ?? 5432;
            $db = ltrim($parsed['path'] ?? 'neondb', '/');
            $user = $parsed['user'] ?? '';
            $pass = $parsed['pass'] ?? '';

            $dsn = "pgsql:host={$host};port={$port};dbname={$db};sslmode=require";
            self::$pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);
        }
        return self::$pdo;
    }

    public static function getDb() {
        if (self::$instance === null) {
            self::$driver = self::getDriver();

            if (self::$driver === 'postgres') {
                $pdo = self::getPdo();
                self::$instance = new PostgresDatabase($pdo);
            } else {
                // MongoDB Driver
                $uri = $_ENV['MONGO_URI'] ?? 'mongodb://localhost:27017';
                $dbName = $_ENV['MONGO_DB'] ?? 'service_connect';
                $client = new \MongoDB\Client($uri);
                self::$instance = $client->selectDatabase($dbName);
                self::initMongoIndexes(self::$instance);
            }
        }
        return self::$instance;
    }

    public static function getConnection() {
        return self::getDb();
    }

    private static function initMongoIndexes($db): void {
        try {
            $db->users->createIndex(['email' => 1], ['unique' => true]);
            $db->services->createIndex(['name' => 'text', 'description' => 'text']);
            $db->providers->createIndex(['category_ids' => 1, 'city' => 1]);
            $db->bookings->createIndex(['provider_id' => 1, 'date' => 1]);
        } catch (Exception $e) {
            // Handled
        }
    }
}

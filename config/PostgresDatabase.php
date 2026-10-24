<?php
namespace Config;

use PDO;
use Exception;

class PostgresCursor implements \IteratorAggregate, \Countable {
    private array $items;

    public function __construct(array $items) {
        $this->items = $items;
    }

    public function toArray(): array {
        return $this->items;
    }

    public function getIterator(): \Traversable {
        return new \ArrayIterator($this->items);
    }

    public function count(): int {
        return count($this->items);
    }
}

class PostgresInsertResult {
    private string $id;

    public function __construct(string $id) {
        $this->id = $id;
    }

    public function getInsertedId(): string {
        return $this->id;
    }

    public function __toString(): string {
        return $this->id;
    }
}

class PostgresUpdateResult {
    private int $count;

    public function __construct(int $count = 1) {
        $this->count = $count;
    }

    public function getModifiedCount(): int {
        return $this->count;
    }
}

class PostgresDeleteResult {
    private int $count;

    public function __construct(int $count = 1) {
        $this->count = $count;
    }

    public function getDeletedCount(): int {
        return $this->count;
    }
}

class PostgresCollection {
    private PDO $pdo;
    private string $table;

    public function __construct(PDO $pdo, string $table) {
        $this->pdo = $pdo;
        $this->table = $table;
    }

    private function cleanVal($val) {
        if ($val === null) return null;
        if (is_object($val)) {
            if (method_exists($val, '__toString')) {
                return (string)$val;
            }
            if ($val instanceof \DateTimeInterface) {
                return $val->format('Y-m-d H:i:s');
            }
            return (string)$val;
        }
        return $val;
    }

    private function normalizeDoc(array $row): array {
        $data = !empty($row['data']) ? json_decode($row['data'], true) : [];
        if (!is_array($data)) $data = [];
        
        $merged = array_merge($row, $data);
        $merged['_id'] = $row['id'] ?? ($data['_id'] ?? '');
        $merged['id'] = $merged['_id'];
        return $merged;
    }

    public function insertOne(array $doc): PostgresInsertResult {
        $id = isset($doc['_id']) ? (string)$this->cleanVal($doc['_id']) : bin2hex(random_bytes(12));
        $doc['_id'] = $id;
        $doc['id'] = $id;

        // Clean values for json encoding
        $cleanedDoc = [];
        foreach ($doc as $k => $v) {
            $cleanedDoc[$k] = $this->cleanVal($v);
        }

        $json = json_encode($cleanedDoc, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        // Extract primary columns if available
        $columns = ['id' => $id, 'data' => $json];
        $knownCols = [
            'users' => ['name', 'email', 'password', 'role', 'phone', 'address', 'city'],
            'categories' => ['name', 'slug', 'icon'],
            'providers' => ['user_id', 'business_name', 'description', 'city', 'is_verified', 'rating_avg', 'rating_count'],
            'services' => ['provider_id', 'name', 'description', 'price', 'duration', 'is_active'],
            'bookings' => ['customer_id', 'provider_id', 'service_id', 'date', 'time_slot', 'status'],
            'payments' => ['booking_id', 'customer_id', 'amount', 'method', 'status'],
            'invoices' => ['booking_id', 'invoice_number', 'amount'],
            'reviews' => ['booking_id', 'customer_id', 'provider_id', 'rating', 'comment'],
            'reports' => ['booking_id', 'customer_id', 'provider_id', 'reason', 'status']
        ];

        if (isset($knownCols[$this->table])) {
            foreach ($knownCols[$this->table] as $col) {
                if (isset($cleanedDoc[$col])) {
                    $columns[$col] = $cleanedDoc[$col];
                }
            }
        }

        $colNames = implode(', ', array_keys($columns));
        $placeholders = ':' . implode(', :', array_keys($columns));

        $stmt = $this->pdo->prepare("INSERT INTO {$this->table} ({$colNames}) VALUES ({$placeholders}) ON CONFLICT (id) DO UPDATE SET data = EXCLUDED.data");
        $stmt->execute($columns);

        return new PostgresInsertResult($id);
    }

    public function insertMany(array $docs): array {
        $results = [];
        foreach ($docs as $doc) {
            $results[] = $this->insertOne($doc);
        }
        return $results;
    }

    private function buildWhere(array $filter, array &$params): string {
        if (empty($filter)) {
            return '1=1';
        }

        $clauses = [];
        $i = 0;

        foreach ($filter as $key => $val) {
            if ($key === '$or' && is_array($val)) {
                $orClauses = [];
                foreach ($val as $subFilter) {
                    $subParams = [];
                    $orClauses[] = $this->buildWhere($subFilter, $subParams);
                    $params = array_merge($params, $subParams);
                }
                if (!empty($orClauses)) {
                    $clauses[] = '(' . implode(' OR ', $orClauses) . ')';
                }
                continue;
            }

            if ($key === '$text' && isset($val['$search'])) {
                $p = 'p_search_' . (++$i);
                $clauses[] = "(data::text ILIKE :{$p})";
                $params[$p] = '%' . $val['$search'] . '%';
                continue;
            }

            $col = $key;
            if ($col === '_id') {
                $col = 'id';
            }

            if (is_array($val) && isset($val['$regex'])) {
                $p = 'p_reg_' . (++$i);
                $clauses[] = "(data->>'{$key}' ILIKE :{$p} OR {$col}::text ILIKE :{$p})";
                $params[$p] = '%' . $val['$regex'] . '%';
                continue;
            }

            $cleanVal = $this->cleanVal($val);
            $p = 'p_' . preg_replace('/[^a-zA-Z0-9_]/', '_', $col) . '_' . (++$i);

            // Check if column exists or query JSON
            $clauses[] = "({$col} = :{$p} OR data->>'{$key}' = :{$p} OR id = :{$p})";
            $params[$p] = (string)$cleanVal;
        }

        return !empty($clauses) ? implode(' AND ', $clauses) : '1=1';
    }

    public function findOne(array $filter = []): ?array {
        $params = [];
        $where = $this->buildWhere($filter, $params);
        $sql = "SELECT * FROM {$this->table} WHERE {$where} LIMIT 1";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) return null;
        return $this->normalizeDoc($row);
    }

    public function find(array $filter = [], array $options = []): PostgresCursor {
        $params = [];
        $where = $this->buildWhere($filter, $params);
        $sql = "SELECT * FROM {$this->table} WHERE {$where}";

        if (!empty($options['sort'])) {
            $orderParts = [];
            foreach ($options['sort'] as $k => $dir) {
                $col = ($k === '_id') ? 'id' : $k;
                $direction = ($dir < 0) ? 'DESC' : 'ASC';
                $orderParts[] = "{$col} {$direction}";
            }
            if (!empty($orderParts)) {
                $sql .= ' ORDER BY ' . implode(', ', $orderParts);
            }
        } else {
            $sql .= ' ORDER BY created_at DESC';
        }

        if (!empty($options['limit'])) {
            $sql .= ' LIMIT ' . (int)$options['limit'];
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $results = [];
        foreach ($rows as $row) {
            $results[] = $this->normalizeDoc($row);
        }

        return new PostgresCursor($results);
    }

    public function updateOne(array $filter, array $update): PostgresUpdateResult {
        $doc = $this->findOne($filter);
        if (!$doc) {
            return new PostgresUpdateResult(0);
        }

        $id = $doc['id'];

        if (isset($update['$set'])) {
            foreach ($update['$set'] as $k => $v) {
                $doc[$k] = $this->cleanVal($v);
            }
        }

        if (isset($update['$push'])) {
            foreach ($update['$push'] as $k => $v) {
                if (!isset($doc[$k]) || !is_array($doc[$k])) {
                    $doc[$k] = [];
                }
                $doc[$k][] = $this->cleanVal($v);
            }
        }

        $json = json_encode($doc, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        // Update known columns as well
        $setClauses = ['data = :data'];
        $params = ['id' => $id, 'data' => $json];

        $knownCols = [
            'users' => ['name', 'email', 'role', 'phone', 'address', 'city'],
            'providers' => ['business_name', 'description', 'city', 'is_verified', 'rating_avg', 'rating_count'],
            'services' => ['name', 'description', 'price', 'duration', 'is_active'],
            'bookings' => ['status'],
            'payments' => ['status', 'amount'],
            'reports' => ['status']
        ];

        if (isset($knownCols[$this->table])) {
            foreach ($knownCols[$this->table] as $col) {
                if (isset($doc[$col])) {
                    $setClauses[] = "{$col} = :{$col}";
                    $params[$col] = $doc[$col];
                }
            }
        }

        $sql = "UPDATE {$this->table} SET " . implode(', ', $setClauses) . " WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return new PostgresUpdateResult(1);
    }

    public function deleteOne(array $filter): PostgresDeleteResult {
        $params = [];
        $where = $this->buildWhere($filter, $params);
        $sql = "DELETE FROM {$this->table} WHERE id IN (SELECT id FROM {$this->table} WHERE {$where} LIMIT 1)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return new PostgresDeleteResult($stmt->rowCount());
    }

    public function countDocuments(array $filter = []): int {
        $params = [];
        $where = $this->buildWhere($filter, $params);
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE {$where}";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    public function createIndex($keys, $options = []): void {
        // Handled automatically by PostgreSQL schema indexes
    }
}

class PostgresDatabase {
    private PDO $pdo;
    private array $collections = [];

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
        $this->initSchema();
    }

    public function getPdo(): PDO {
        return $this->pdo;
    }

    public function __get(string $name): PostgresCollection {
        if (!isset($this->collections[$name])) {
            $this->collections[$name] = new PostgresCollection($this->pdo, $name);
        }
        return $this->collections[$name];
    }

    public function drop(): void {
        $tables = ['reviews', 'reports', 'invoices', 'payments', 'bookings', 'services', 'providers', 'categories', 'users'];
        foreach ($tables as $t) {
            $this->pdo->exec("TRUNCATE TABLE {$t} RESTART IDENTITY CASCADE");
        }
    }

    public function selectDatabase(string $name): self {
        return $this;
    }

    private function initSchema(): void {
        $schema = "
        CREATE TABLE IF NOT EXISTS users (
            id VARCHAR(64) PRIMARY KEY,
            name VARCHAR(255),
            email VARCHAR(255) UNIQUE,
            password VARCHAR(255),
            role VARCHAR(50) DEFAULT 'customer',
            phone VARCHAR(50),
            address TEXT,
            city VARCHAR(100),
            created_at TIMESTAMPTZ DEFAULT NOW(),
            data JSONB NOT NULL DEFAULT '{}'
        );

        CREATE TABLE IF NOT EXISTS categories (
            id VARCHAR(64) PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            slug VARCHAR(255) UNIQUE NOT NULL,
            icon VARCHAR(100) DEFAULT 'bi-folder',
            created_at TIMESTAMPTZ DEFAULT NOW(),
            data JSONB NOT NULL DEFAULT '{}'
        );

        CREATE TABLE IF NOT EXISTS providers (
            id VARCHAR(64) PRIMARY KEY,
            user_id VARCHAR(64),
            business_name VARCHAR(255),
            category_ids JSONB DEFAULT '[]',
            description TEXT,
            city VARCHAR(100),
            is_verified BOOLEAN DEFAULT TRUE,
            rating_avg NUMERIC(3, 2) DEFAULT 5.0,
            rating_count INT DEFAULT 0,
            created_at TIMESTAMPTZ DEFAULT NOW(),
            data JSONB NOT NULL DEFAULT '{}'
        );

        CREATE TABLE IF NOT EXISTS services (
            id VARCHAR(64) PRIMARY KEY,
            provider_id VARCHAR(64),
            name VARCHAR(255) NOT NULL,
            description TEXT,
            price NUMERIC(10, 2) DEFAULT 0.00,
            duration VARCHAR(100) DEFAULT '1 hour',
            is_active BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMPTZ DEFAULT NOW(),
            data JSONB NOT NULL DEFAULT '{}'
        );

        CREATE TABLE IF NOT EXISTS bookings (
            id VARCHAR(64) PRIMARY KEY,
            customer_id VARCHAR(64),
            provider_id VARCHAR(64),
            service_id VARCHAR(64),
            date VARCHAR(50),
            time_slot VARCHAR(100),
            status VARCHAR(50) DEFAULT 'Pending',
            status_history JSONB DEFAULT '[]',
            created_at TIMESTAMPTZ DEFAULT NOW(),
            data JSONB NOT NULL DEFAULT '{}'
        );

        CREATE TABLE IF NOT EXISTS payments (
            id VARCHAR(64) PRIMARY KEY,
            booking_id VARCHAR(64),
            customer_id VARCHAR(64),
            amount NUMERIC(10, 2) DEFAULT 0.00,
            method VARCHAR(50) DEFAULT 'cash',
            status VARCHAR(50) DEFAULT 'pending',
            created_at TIMESTAMPTZ DEFAULT NOW(),
            data JSONB NOT NULL DEFAULT '{}'
        );

        CREATE TABLE IF NOT EXISTS invoices (
            id VARCHAR(64) PRIMARY KEY,
            booking_id VARCHAR(64),
            invoice_number VARCHAR(100),
            amount NUMERIC(10, 2) DEFAULT 0.00,
            created_at TIMESTAMPTZ DEFAULT NOW(),
            data JSONB NOT NULL DEFAULT '{}'
        );

        CREATE TABLE IF NOT EXISTS reviews (
            id VARCHAR(64) PRIMARY KEY,
            booking_id VARCHAR(64),
            customer_id VARCHAR(64),
            provider_id VARCHAR(64),
            rating INT DEFAULT 5,
            comment TEXT,
            created_at TIMESTAMPTZ DEFAULT NOW(),
            data JSONB NOT NULL DEFAULT '{}'
        );

        CREATE TABLE IF NOT EXISTS reports (
            id VARCHAR(64) PRIMARY KEY,
            booking_id VARCHAR(64),
            customer_id VARCHAR(64),
            provider_id VARCHAR(64),
            reason TEXT,
            status VARCHAR(50) DEFAULT 'open',
            created_at TIMESTAMPTZ DEFAULT NOW(),
            data JSONB NOT NULL DEFAULT '{}'
        );

        CREATE INDEX IF NOT EXISTS idx_users_email ON users(email);
        CREATE INDEX IF NOT EXISTS idx_categories_slug ON categories(slug);
        CREATE INDEX IF NOT EXISTS idx_services_provider ON services(provider_id);
        CREATE INDEX IF NOT EXISTS idx_bookings_cust ON bookings(customer_id);
        CREATE INDEX IF NOT EXISTS idx_bookings_prov ON bookings(provider_id);
        ";

        $this->pdo->exec($schema);
    }
}

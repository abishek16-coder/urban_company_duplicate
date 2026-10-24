<?php
namespace App\Models;

use Config\Database;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;

class Invoice {
    public static function create(array $data) {
        $db = Database::getDb();
        $data['issued_at'] = new UTCDateTime();
        return $db->invoices->insertOne($data);
    }
}

<?php
namespace App\Models;

use Config\Database;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;

class Report {
    public static function create(array $data) {
        $db = Database::getDb();
        $data['created_at'] = new UTCDateTime();
        return $db->reports->insertOne($data);
    }
}

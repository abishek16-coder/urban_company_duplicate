<?php
namespace App\Models;

use Config\Database;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;

class Service {
    public static function findByProviderId($providerId) {
        $db = Database::getDb();
        return $db->services->find(['provider_id' => new ObjectId($providerId)])->toArray();
    }

    public static function create(array $data) {
        $db = Database::getDb();
        $data['created_at'] = new UTCDateTime();
        return $db->services->insertOne($data);
    }
}

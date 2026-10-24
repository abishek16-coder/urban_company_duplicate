<?php
namespace App\Models;

use Config\Database;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;

class Provider {
    public static function findByUserId($userId) {
        $db = Database::getDb();
        return $db->providers->findOne(['user_id' => new ObjectId($userId)]);
    }

    public static function update($id, array $data) {
        $db = Database::getDb();
        return $db->providers->updateOne(['_id' => new ObjectId($id)], ['$set' => $data]);
    }
}

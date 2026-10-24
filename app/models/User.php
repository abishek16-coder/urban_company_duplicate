<?php
namespace App\Models;

use Config\Database;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;

class User {
    public static function findByEmail(string $email) {
        $db = Database::getDb();
        return $db->users->findOne(['email' => $email]);
    }

    public static function findById($id) {
        $db = Database::getDb();
        return $db->users->findOne(['_id' => new ObjectId($id)]);
    }

    public static function create(array $data) {
        $db = Database::getDb();
        $data['created_at'] = new UTCDateTime();
        return $db->users->insertOne($data);
    }

    public static function update($id, array $data) {
        $db = Database::getDb();
        return $db->users->updateOne(['_id' => new ObjectId($id)], ['$set' => $data]);
    }
}

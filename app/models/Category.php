<?php
namespace App\Models;

use Config\Database;
use MongoDB\BSON\ObjectId;

class Category {
    public static function all() {
        $db = Database::getDb();
        return $db->categories->find()->toArray();
    }

    public static function create(array $data) {
        $db = Database::getDb();
        return $db->categories->insertOne($data);
    }

    public static function delete($id) {
        $db = Database::getDb();
        return $db->categories->deleteOne(['_id' => new ObjectId($id)]);
    }
}

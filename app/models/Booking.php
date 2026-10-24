<?php
namespace App\Models;

use Config\Database;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;

class Booking {
    public static function create(array $data) {
        $db = Database::getDb();
        $data['created_at'] = new UTCDateTime();
        return $db->bookings->insertOne($data);
    }

    public static function findByCustomer($customerId) {
        $db = Database::getDb();
        return $db->bookings->find(['customer_id' => new ObjectId($customerId)])->toArray();
    }

    public static function findByProvider($providerId) {
        $db = Database::getDb();
        return $db->bookings->find(['provider_id' => new ObjectId($providerId)])->toArray();
    }
}

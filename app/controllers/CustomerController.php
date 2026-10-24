<?php
namespace App\Controllers;

use App\Helpers\Middleware;
use Config\Database;
use MongoDB\BSON\ObjectId;

class CustomerController {
    public function home() {
        require_once __DIR__ . '/../views/customer/home.php';
    }

    public function dashboard() {
        Middleware::role('customer');
        $db = Database::getDb();
        $categories = $db->categories->find()->toArray();
        $recentBookings = $db->bookings->find(
            ['customer_id' => new ObjectId($_SESSION['user_id'])],
            ['limit' => 3, 'sort' => ['created_at' => -1]]
        )->toArray();
        foreach ($recentBookings as &$b) {
            $b['service'] = $db->services->findOne(['_id' => $b['service_id']]);
            $b['provider'] = $db->providers->findOne(['_id' => $b['provider_id']]);
        }
        require_once __DIR__ . '/../views/customer/dashboard.php';
    }

    public function search() {
        Middleware::role('customer');
        $db = Database::getDb();
        $query = trim($_GET['q'] ?? '');
        $catFilter = trim($_GET['category'] ?? '');
        $filter = [];

        if (!empty($query)) {
            // Regex search allows partial matching across name, description
            $filter['$or'] = [
                ['name' => ['$regex' => $query, '$options' => 'i']],
                ['description' => ['$regex' => $query, '$options' => 'i']]
            ];
        }

        $services = $db->services->find($filter)->toArray();

        // Attach provider info and filter by category if requested
        $filteredServices = [];
        foreach ($services as $srv) {
            $provider = $db->providers->findOne(['_id' => $srv['provider_id']]);
            $srv['provider'] = $provider;
            if (!empty($catFilter) && $provider) {
                $catDoc = $db->categories->findOne(['slug' => $catFilter]);
                if ($catDoc) {
                    $catIds = array_map(function($id) { return (string)$id; }, (array)($provider['category_ids'] ?? []));
                    if (!in_array((string)$catDoc['_id'], $catIds)) {
                        continue;
                    }
                }
            }
            $filteredServices[] = $srv;
        }
        $services = $filteredServices;
        $categories = $db->categories->find()->toArray();
        require_once __DIR__ . '/../views/customer/search.php';
    }

    public function bookings() {
        Middleware::role('customer');
        $db = Database::getDb();
        $customerId = new ObjectId($_SESSION['user_id']);
        $bookings = $db->bookings->find(['customer_id' => $customerId], ['sort' => ['created_at' => -1]])->toArray();

        foreach ($bookings as &$booking) {
            $booking['service'] = $db->services->findOne(['_id' => $booking['service_id']]);
            $booking['provider'] = $db->providers->findOne(['_id' => $booking['provider_id']]);
            $booking['payment'] = $db->payments->findOne(['booking_id' => $booking['_id']]);
            $booking['invoice'] = $db->invoices->findOne(['booking_id' => $booking['_id']]);
            $booking['review'] = $db->reviews->findOne(['booking_id' => $booking['_id']]);
            $booking['report'] = $db->reports->findOne(['booking_id' => $booking['_id']]);
        }
        require_once __DIR__ . '/../views/customer/bookings.php';
    }
}

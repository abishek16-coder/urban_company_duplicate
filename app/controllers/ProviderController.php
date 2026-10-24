<?php
namespace App\Controllers;

use App\Helpers\Middleware;
use App\Models\Provider;
use App\Models\Service;
use App\Models\Booking;

class ProviderController {
    public function dashboard() {
        Middleware::role('provider');
        $db = \Config\Database::getDb();
        $provider = Provider::findByUserId($_SESSION['user_id']);
        if (!$provider) {
            $user = $db->users->findOne(['_id' => new \MongoDB\BSON\ObjectId($_SESSION['user_id'])]);
            $res = $db->providers->insertOne([
                'user_id' => new \MongoDB\BSON\ObjectId($_SESSION['user_id']),
                'business_name' => ($user['name'] ?? 'Provider') . ' Services',
                'category_ids' => [],
                'description' => '',
                'city' => $user['city'] ?? '',
                'is_verified' => true,
                'rating_avg' => 5.0,
                'rating_count' => 0,
                'created_at' => new \MongoDB\BSON\UTCDateTime()
            ]);
            $provider = $db->providers->findOne(['_id' => $res->getInsertedId()]);
        }
        $services = Service::findByProviderId($provider['_id']);
        $bookings = Booking::findByProvider($provider['_id']);
        $categories = $db->categories->find()->toArray();
        require_once __DIR__ . '/../views/provider/dashboard.php';
    }

    public function updateProfile() {
        Middleware::role('provider');
        $db = \Config\Database::getDb();
        $provider = Provider::findByUserId($_SESSION['user_id']);
        if ($provider) {
            $catIds = [];
            if (!empty($_POST['category_id'])) {
                $catIds[] = new \MongoDB\BSON\ObjectId($_POST['category_id']);
            }
            Provider::update($provider['_id'], [
                'business_name' => trim($_POST['business_name'] ?? $provider['business_name']),
                'description' => trim($_POST['description'] ?? ''),
                'city' => trim($_POST['city'] ?? $provider['city']),
                'category_ids' => !empty($catIds) ? $catIds : ($provider['category_ids'] ?? [])
            ]);
            \App\Helpers\Session::set('success', 'Profile updated successfully!');
        }
        header('Location: /provider/dashboard');
        exit;
    }

    public function storeService() {
        Middleware::role('provider');
        $provider = Provider::findByUserId($_SESSION['user_id']);
        if ($provider) {
            Service::create([
                'provider_id' => $provider['_id'],
                'name' => trim($_POST['name'] ?? ''),
                'description' => trim($_POST['description'] ?? ''),
                'price' => (float)($_POST['price'] ?? 0),
                'duration' => trim($_POST['duration'] ?? '1 hour'),
                'is_active' => true
            ]);
            \App\Helpers\Session::set('success', 'Service added successfully!');
        }
        header('Location: /provider/dashboard');
        exit;
    }
}

<?php
namespace App\Controllers;

use App\Helpers\Middleware;
use App\Models\Provider;
use App\Models\Booking;

class ProviderBookingController {
    public function index() {
        Middleware::role('provider');
        $db = \Config\Database::getDb();
        $provider = Provider::findByUserId($_SESSION['user_id']);
        $bookings = [];
        if ($provider) {
            $bookings = $db->bookings->find(
                ['provider_id' => $provider['_id']],
                ['sort' => ['created_at' => -1]]
            )->toArray();
            foreach ($bookings as &$b) {
                $b['customer'] = $db->users->findOne(['_id' => $b['customer_id']]);
                $b['service'] = $db->services->findOne(['_id' => $b['service_id']]);
                $b['payment'] = $db->payments->findOne(['booking_id' => $b['_id']]);
                $b['invoice'] = $db->invoices->findOne(['booking_id' => $b['_id']]);
                $b['review'] = $db->reviews->findOne(['booking_id' => $b['_id']]);
                $b['report'] = $db->reports->findOne(['booking_id' => $b['_id']]);
            }
        }
        require_once __DIR__ . '/../views/provider/bookings.php';
    }

    public function updateStatus() {
        Middleware::role('provider');
        $db = \Config\Database::getDb();
        $bookingId = $_POST['booking_id'] ?? '';
        $newStatus = $_POST['status'] ?? 'Confirmed';
        
        if (!empty($bookingId)) {
            $bId = new \MongoDB\BSON\ObjectId($bookingId);
            $db->bookings->updateOne(
                ['_id' => $bId],
                [
                    '$set' => ['status' => $newStatus],
                    '$push' => [
                        'status_history' => [
                            'status' => $newStatus,
                            'timestamp' => new \MongoDB\BSON\UTCDateTime(),
                            'updated_by' => $_SESSION['user_id']
                        ]
                    ]
                ]
            );

            if ($newStatus === 'Completed') {
                $db->payments->updateOne(
                    ['booking_id' => $bId],
                    ['$set' => ['status' => 'paid']]
                );
            }
            \App\Helpers\Session::set('success', "Booking status updated to $newStatus!");
        }
        header('Location: /provider/bookings');
        exit;
    }
}

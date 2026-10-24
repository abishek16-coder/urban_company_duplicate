<?php
namespace App\Controllers;

use App\Helpers\Middleware;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Invoice;
use MongoDB\BSON\ObjectId;

class BookingController {
    public function store() {
        Middleware::role('customer');
        $serviceId = $_POST['service_id'] ?? '';
        $providerId = $_POST['provider_id'] ?? '';
        $date = $_POST['date'] ?? '';
        $timeSlot = $_POST['time_slot'] ?? '';

        $db = \Config\Database::getDb();
        $srv = $db->services->findOne(['_id' => new ObjectId($serviceId)]);
        $price = $srv ? (float)($srv['price'] ?? 50.00) : 50.00;

        $bookingId = new ObjectId();
        Booking::create([
            '_id' => $bookingId,
            'customer_id' => new ObjectId($_SESSION['user_id']),
            'provider_id' => new ObjectId($providerId),
            'service_id' => new ObjectId($serviceId),
            'date' => $date,
            'time_slot' => $timeSlot,
            'status' => 'Pending',
            'status_history' => [
                ['status' => 'Pending', 'timestamp' => new \MongoDB\BSON\UTCDateTime(), 'updated_by' => $_SESSION['user_id']]
            ]
        ]);

        Payment::create([
            'booking_id' => $bookingId,
            'customer_id' => new ObjectId($_SESSION['user_id']),
            'amount' => $price,
            'method' => $_POST['payment_method'] ?? 'cash',
            'status' => 'pending'
        ]);

        Invoice::create([
            'booking_id' => $bookingId,
            'invoice_number' => 'INV-' . strtoupper(substr(uniqid(), 7)),
            'amount' => $price
        ]);

        \App\Helpers\Session::set('success', 'Booking requested successfully! You can track it here.');
        header('Location: /customer/bookings');
        exit;
    }
}

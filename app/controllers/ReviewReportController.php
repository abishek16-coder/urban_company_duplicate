<?php
namespace App\Controllers;

use App\Helpers\Middleware;
use App\Models\Review;
use App\Models\Report;
use MongoDB\BSON\ObjectId;

class ReviewReportController {
    public function storeReview() {
        Middleware::role('customer');
        $db = \Config\Database::getDb();
        $bookingId = new ObjectId($_POST['booking_id']);
        $providerId = new ObjectId($_POST['provider_id']);
        $rating = max(1, min(5, (int)($_POST['rating'] ?? 5)));
        $comment = trim($_POST['comment'] ?? '');

        Review::create([
            'booking_id' => $bookingId,
            'customer_id' => new ObjectId($_SESSION['user_id']),
            'provider_id' => $providerId,
            'rating' => $rating,
            'comment' => $comment
        ]);

        // Recalculate provider average rating
        $reviews = $db->reviews->find(['provider_id' => $providerId])->toArray();
        if (count($reviews) > 0) {
            $sum = 0;
            foreach ($reviews as $r) {
                $sum += (int)($r['rating'] ?? 5);
            }
            $avg = round($sum / count($reviews), 1);
            $db->providers->updateOne(
                ['_id' => $providerId],
                ['$set' => ['rating_avg' => $avg, 'rating_count' => count($reviews)]]
            );
        }

        \App\Helpers\Session::set('success', 'Thank you! Your review has been submitted.');
        header('Location: /customer/bookings');
        exit;
    }

    public function storeReport() {
        Middleware::role('customer');
        $bookingId = new ObjectId($_POST['booking_id']);
        $providerId = new ObjectId($_POST['provider_id']);
        $reason = trim($_POST['reason'] ?? '');

        Report::create([
            'booking_id' => $bookingId,
            'customer_id' => new ObjectId($_SESSION['user_id']),
            'provider_id' => $providerId,
            'reason' => $reason,
            'status' => 'open'
        ]);

        \App\Helpers\Session::set('success', 'Your complaint has been submitted to admin support.');
        header('Location: /customer/bookings');
        exit;
    }
}

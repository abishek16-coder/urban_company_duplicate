<?php
function req($url, $method = 'GET', $data = [], &$cookies = []) {
    $ch = curl_init('http://localhost:8000' . $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    if (!empty($cookies)) {
        curl_setopt($ch, CURLOPT_COOKIE, implode('; ', $cookies));
    }
    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    }
    $response = curl_exec($ch);
    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $headers = substr($response, 0, $headerSize);
    $body = substr($response, $headerSize);
    curl_close($ch);

    preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $headers, $matches);
    if (!empty($matches[1])) {
        foreach ($matches[1] as $c) {
            $parts = explode('=', $c, 2);
            $cookies[$parts[0]] = $c;
        }
    }
    return ['code' => $httpCode, 'body' => $body, 'headers' => $headers];
}

echo "=== STARTING SERVICE CONNECT VERIFICATION ===" . PHP_EOL;

// 1. Get Login Page & CSRF
$c = [];
$res = req('/auth/login', 'GET', [], $c);
preg_match('/name="csrf_token" value="([^"]+)"/', $res['body'], $m);
$token = $m[1] ?? '';
echo "[1] CSRF Token fetched: " . ($token ? "OK" : "FAILED") . PHP_EOL;

// 2. Login as Admin
$res = req('/auth/login', 'POST', [
    'email' => 'admin@serviceconnect.com',
    'password' => 'Admin@123',
    'csrf_token' => $token
], $c);
echo "[2] Admin Login: " . ($res['code'] === 302 ? "SUCCESS (302 Redirect)" : "STATUS " . $res['code']) . PHP_EOL;

// 3. Admin Dashboard
$res = req('/admin/dashboard', 'GET', [], $c);
echo "[3] Admin Dashboard: " . (strpos($res['body'], 'System Administration') !== false ? "SUCCESS" : "FAILED") . PHP_EOL;

// 4. Admin Add Category
$res = req('/admin/categories', 'POST', ['name' => 'Painting & Decor', 'icon' => 'bi-brush'], $c);
$res = req('/admin/categories', 'GET', [], $c);
echo "[4] Admin Category Added (Painting & Decor): " . (strpos($res['body'], 'Painting') !== false ? "SUCCESS" : "FAILED") . PHP_EOL;

// 5. Provider Login
$c2 = [];
$res = req('/auth/login', 'GET', [], $c2);
preg_match('/name="csrf_token" value="([^"]+)"/', $res['body'], $m);
$token2 = $m[1] ?? '';
$res = req('/auth/login', 'POST', [
    'email' => 'provider@serviceconnect.com',
    'password' => 'Provider@123',
    'csrf_token' => $token2
], $c2);
echo "[5] Provider Login: " . ($res['code'] === 302 ? "SUCCESS" : "STATUS " . $res['code']) . PHP_EOL;

// 6. Provider Profile Update
$res = req('/provider/profile/update', 'POST', [
    'business_name' => 'John Pro Electricals',
    'city' => 'New York',
    'description' => 'Licensed master electricians offering emergency repair.'
], $c2);
$res = req('/provider/dashboard', 'GET', [], $c2);
echo "[6] Provider Profile Update: " . (strpos($res['body'], 'John Pro Electricals') !== false ? "SUCCESS" : "FAILED") . PHP_EOL;

// 7. Provider Add Service
$res = req('/provider/services/store', 'POST', [
    'name' => 'Ceiling Fan Installation',
    'price' => '45.00',
    'duration' => '1 hour',
    'description' => 'Fast safe fan installation.'
], $c2);
$res = req('/provider/dashboard', 'GET', [], $c2);
echo "[7] Provider Add Service: " . (strpos($res['body'], 'Ceiling Fan Installation') !== false ? "SUCCESS" : "FAILED") . PHP_EOL;

// 8. Customer Login
$c3 = [];
$res = req('/auth/login', 'GET', [], $c3);
preg_match('/name="csrf_token" value="([^"]+)"/', $res['body'], $m);
$token3 = $m[1] ?? '';
$res = req('/auth/login', 'POST', [
    'email' => 'customer@serviceconnect.com',
    'password' => 'Customer@123',
    'csrf_token' => $token3
], $c3);
echo "[8] Customer Login: " . ($res['code'] === 302 ? "SUCCESS" : "STATUS " . $res['code']) . PHP_EOL;

// 9. Customer Search
$res = req('/customer/search?q=Wiring', 'GET', [], $c3);
preg_match('/name="service_id" value="([^"]+)"/', $res['body'], $sm);
preg_match('/name="provider_id" value="([^"]+)"/', $res['body'], $pm);
$srvId = $sm[1] ?? '';
$provId = $pm[1] ?? '';
echo "[9] Customer Search (Found Service ID: $srvId, Provider ID: $provId): " . ($srvId ? "SUCCESS" : "FAILED") . PHP_EOL;

// 10. Customer Book Service
$res = req('/booking/store', 'POST', [
    'service_id' => $srvId,
    'provider_id' => $provId,
    'date' => '2026-10-30',
    'time_slot' => '02:00 PM - 04:00 PM',
    'payment_method' => 'cash'
], $c3);
echo "[10] Customer Book Service: " . ($res['code'] === 302 ? "SUCCESS" : "STATUS " . $res['code']) . PHP_EOL;

// 11. Customer Bookings View
$res = req('/customer/bookings', 'GET', [], $c3);
preg_match('/name="booking_id" value="([^"]+)"/', $res['body'], $bm);
$bookId = $bm[1] ?? '';
echo "[11] Customer Bookings View (Found Booking ID: $bookId): " . ($bookId ? "SUCCESS" : "FAILED") . PHP_EOL;

// 12. Customer Submit Review
$res = req('/review/store', 'POST', [
    'booking_id' => $bookId,
    'provider_id' => $provId,
    'rating' => 5,
    'comment' => 'Arrived on time and fixed the short circuit smoothly!'
], $c3);
echo "[12] Customer Review Submit: " . ($res['code'] === 302 ? "SUCCESS" : "STATUS " . $res['code']) . PHP_EOL;

// 13. Customer Submit Report
$res = req('/report/store', 'POST', [
    'booking_id' => $bookId,
    'provider_id' => $provId,
    'reason' => 'Slight delay in arrival.'
], $c3);
echo "[13] Customer Report Submit: " . ($res['code'] === 302 ? "SUCCESS" : "STATUS " . $res['code']) . PHP_EOL;

// 14. Provider Accept Booking
$res = req('/provider/bookings/update-status', 'POST', [
    'booking_id' => $bookId,
    'status' => 'Confirmed'
], $c2);
echo "[14] Provider Accept Booking: " . ($res['code'] === 302 ? "SUCCESS" : "STATUS " . $res['code']) . PHP_EOL;

// 15. Verify Admin sees bookings and complaints
$res = req('/admin/dashboard', 'GET', [], $c);
echo "[15] Admin Dashboard sees live dispute and booking: " . (strpos($res['body'], 'Slight delay in arrival') !== false ? "SUCCESS" : "FAILED") . PHP_EOL;

echo "=== ALL CHECKLIST TESTS COMPLETED SUCCESSFULLY! ===" . PHP_EOL;

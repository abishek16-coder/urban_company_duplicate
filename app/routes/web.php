<?php
use App\Controllers\AuthController;
use App\Controllers\AdminController;
use App\Controllers\ProviderController;
use App\Controllers\ProviderBookingController;
use App\Controllers\CustomerController;
use App\Controllers\BookingController;
use App\Controllers\ReviewReportController;

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$auth = new AuthController();
$admin = new AdminController();
$provider = new ProviderController();
$providerBooking = new ProviderBookingController();
$customer = new CustomerController();
$booking = new BookingController();
$reviewReport = new ReviewReportController();

switch ($uri) {
    case '/':
        $customer->home();
        break;

    // Authentication routes
    case '/auth/login':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') $auth->login();
        else $auth->showLogin();
        break;
    case '/auth/register':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') $auth->register();
        else $auth->showRegister();
        break;
    case '/auth/logout':
        $auth->logout();
        break;

    // Admin routes
    case '/admin/dashboard':
        $admin->dashboard();
        break;
    case '/admin/categories':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') $admin->storeCategory();
        else $admin->categories();
        break;
    case '/admin/categories/delete':
        $admin->deleteCategory();
        break;

    // Provider routes
    case '/provider/dashboard':
        $provider->dashboard();
        break;
    case '/provider/profile/update':
        $provider->updateProfile();
        break;
    case '/provider/services/store':
        $provider->storeService();
        break;
    case '/provider/bookings':
        $providerBooking->index();
        break;
    case '/provider/bookings/update-status':
        $providerBooking->updateStatus();
        break;

    // Customer routes
    case '/customer/dashboard':
        $customer->dashboard();
        break;
    case '/customer/search':
        $customer->search();
        break;
    case '/customer/bookings':
        $customer->bookings();
        break;
    case '/booking/store':
        $booking->store();
        break;

    // Reviews & Reports
    case '/review/store':
        $reviewReport->storeReview();
        break;
    case '/report/store':
        $reviewReport->storeReport();
        break;

    default:
        http_response_code(404);
        require_once __DIR__ . '/../views/layouts/header.php';
        echo '<div class="text-center py-5"><h2>404 Not Found</h2><p class="text-muted">The requested page does not exist.</p><a href="/" class="btn btn-primary mt-3">Return to Home</a></div>';
        require_once __DIR__ . '/../views/layouts/footer.php';
        break;
}

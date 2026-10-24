<?php
namespace App\Controllers;

use App\Helpers\Middleware;
use App\Models\Category;
use Config\Database;

class AdminController {
    public function dashboard() {
        Middleware::role('admin');
        $db = Database::getDb();
        $counts = [
            'users' => $db->users->countDocuments(),
            'providers' => $db->providers->countDocuments(),
            'bookings' => $db->bookings->countDocuments(),
            'reports' => $db->reports->countDocuments()
        ];
        $recentBookings = $db->bookings->find([], ['limit' => 6, 'sort' => ['created_at' => -1]])->toArray();
        $recentReports = $db->reports->find([], ['limit' => 5, 'sort' => ['created_at' => -1]])->toArray();
        $recentUsers = $db->users->find([], ['limit' => 6, 'sort' => ['created_at' => -1]])->toArray();
        require_once __DIR__ . '/../views/admin/dashboard.php';
    }

    public function categories() {
        Middleware::role('admin');
        $categories = Category::all();
        require_once __DIR__ . '/../views/admin/categories.php';
    }

    public function storeCategory() {
        Middleware::role('admin');
        $name = trim($_POST['name'] ?? '');
        if (!empty($name)) {
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
            Category::create(['name' => $name, 'slug' => $slug, 'icon' => $_POST['icon'] ?? 'bi-folder']);
        }
        header('Location: /admin/categories');
        exit;
    }

    public function deleteCategory($id = null) {
        Middleware::role('admin');
        $catId = $id ?? $_GET['id'] ?? $_POST['id'] ?? '';
        if ($catId) {
            Category::delete($catId);
        }
        header('Location: /admin/categories');
        exit;
    }
}

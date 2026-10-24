<?php
namespace App\Controllers;

use App\Models\User;
use App\Models\Provider;
use App\Helpers\Session;
use App\Helpers\CSRF;
use App\Helpers\Validator;
use Config\Database;

class AuthController {
    public function showLogin() {
        require_once __DIR__ . '/../views/auth/login.php';
    }

    public function login() {
        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            die('Invalid CSRF Token');
        }

        $email = Validator::sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $user = User::findByEmail($email);
        if ($user && password_verify($password, $user['password'])) {
            Session::set('user_id', (string)$user['_id']);
            Session::set('name', $user['name']);
            Session::set('role', $user['role']);

            if ($user['role'] === 'admin') {
                header('Location: /admin/dashboard');
            } elseif ($user['role'] === 'provider') {
                header('Location: /provider/dashboard');
            } else {
                header('Location: /customer/dashboard');
            }
            exit;
        }

        Session::set('error', 'Invalid email or password');
        header('Location: /auth/login');
        exit;
    }

    public function showRegister() {
        require_once __DIR__ . '/../views/auth/register.php';
    }

    public function register() {
        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            die('Invalid CSRF Token');
        }

        $name = Validator::sanitize($_POST['name'] ?? '');
        $email = Validator::sanitize($_POST['email'] ?? '');
        $password = password_hash($_POST['password'] ?? '', PASSWORD_BCRYPT);
        $role = $_POST['role'] === 'provider' ? 'provider' : 'customer';
        $phone = Validator::sanitize($_POST['phone'] ?? '');
        $address = Validator::sanitize($_POST['address'] ?? '');
        $city = Validator::sanitize($_POST['city'] ?? '');

        if (User::findByEmail($email)) {
            Session::set('error', 'Email already registered');
            header('Location: /auth/register');
            exit;
        }

        $result = User::create([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'role' => $role,
            'phone' => $phone,
            'address' => $address,
            'city' => $city
        ]);

        if ($role === 'provider') {
            $db = Database::getDb();
            $db->providers->insertOne([
                'user_id' => $result->getInsertedId(),
                'business_name' => $name . ' Services',
                'category_ids' => [],
                'description' => '',
                'city' => $city,
                'is_verified' => false,
                'rating_avg' => 0.0,
                'rating_count' => 0,
                'created_at' => new \MongoDB\BSON\UTCDateTime()
            ]);
        }

        Session::set('success', 'Registration successful! Please login.');
        header('Location: /auth/login');
        exit;
    }

    public function logout() {
        Session::destroy();
        header('Location: /auth/login');
        exit;
    }
}

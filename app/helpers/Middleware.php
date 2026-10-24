<?php
namespace App\Helpers;

class Middleware {
    public static function auth(): void {
        Session::start();
        if (!Session::get('user_id')) {
            header('Location: /auth/login');
            exit;
        }
    }

    public static function role(string $role): void {
        self::auth();
        if (Session::get('role') !== $role) {
            http_response_code(403);
            echo "Access Denied";
            exit;
        }
    }
}

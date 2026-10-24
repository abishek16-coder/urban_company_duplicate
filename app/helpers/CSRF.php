<?php
namespace App\Helpers;

class CSRF {
    public static function generateToken(): string {
        Session::start();
        if (!Session::get('csrf_token')) {
            Session::set('csrf_token', bin2hex(random_bytes(32)));
        }
        return Session::get('csrf_token');
    }

    public static function validateToken(?string $token): bool {
        Session::start();
        $sessionToken = Session::get('csrf_token');
        return $token && $sessionToken && hash_equals($sessionToken, $token);
    }
}

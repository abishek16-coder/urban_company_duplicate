<?php
namespace App\Helpers;

class Validator {
    public static function sanitize(string $data): string {
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }
}

<?php
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
if ($uri !== '/' && file_exists(__DIR__ . $uri)) {
    return false;
}

require_once __DIR__ . '/../vendor/autoload.php';
use App\Helpers\Session;

Session::start();
require_once __DIR__ . '/../app/routes/web.php';

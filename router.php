<?php
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ($path === '/' || $path === '') {
    require __DIR__ . '/api/php/index.php';
    return true;
}

$file = __DIR__ . $path;
if (is_file($file)) {
    return false;
}

require __DIR__ . '/api/php/index.php';
return true;
<?php
/**
 * CORS Middleware
 */

$config = require __DIR__ . '/../config/app.php';
$cors = $config['cors'];

// Set CORS headers
header('Access-Control-Allow-Origin: ' . implode(', ', $cors['allowed_origins']));
header('Access-Control-Allow-Methods: ' . implode(', ', $cors['allowed_methods']));
header('Access-Control-Allow-Headers: ' . implode(', ', $cors['allowed_headers']));
header('Access-Control-Max-Age: ' . $cors['max_age']);

if ($cors['supports_credentials']) {
    header('Access-Control-Allow-Credentials: true');
}

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

<?php
/**
 * CORS Middleware
 */
$config = require __DIR__ . '/../../config/app.php';
$cors = $config['cors'];

// Get origin from request
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';

// Check if origin is allowed
$allowedOrigin = '*';
if (in_array($origin, $cors['allowed_origins'])) {
    $allowedOrigin = $origin;
} elseif (!empty($cors['allowed_origins'])) {
    $allowedOrigin = $cors['allowed_origins'][0];
}

// Set CORS headers
header('Access-Control-Allow-Origin: ' . $allowedOrigin);
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

<?php
/**
 * Main entry point
 */

require_once __DIR__ . '/../vendor/autoload.php';

// Enable error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Load CORS middleware
require_once __DIR__ . '/../src/Middleware/Cors.php';

// Load configuration
$config = require __DIR__ . '/../config/app.php';

// Import core classes
use App\Core\Router;
use App\Core\Response;

// Initialize router
$router = new Router();

// Health check route
$router->get('/api/health', function() {
    Response::success(['status' => 'OK'], 'API is running');
});

// Sample route
$router->get('/api/test', function() {
    Response::success(['message' => 'Test route works!']);
});

// Load routes
require_once __DIR__ . '/../routes/api.php';

// Run the router
$router->run();

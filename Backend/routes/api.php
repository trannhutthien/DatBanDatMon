<?php
/**
 * API Routes
 */

use App\Core\Router;
use App\Core\Database;
use App\Core\Response;
use App\Controllers\AuthController;

// Auth routes
$router->post('/api/auth/google', [AuthController::class, 'googleLogin']);
$router->post('/api/auth/register', [AuthController::class, 'register']);
$router->post('/api/auth/login', [AuthController::class, 'login']);

// Simple DB connectivity test
$router->get('/api/db-test', function() {
    try {
        $db = Database::getInstance();
        // Run a lightweight query to verify connection
        $row = $db->fetch('SELECT DATABASE() as db, NOW() as now');
        Response::success($row, 'Database connection OK');
    } catch (\Exception $e) {
        Response::serverError('Database connection failed: ' . $e->getMessage());
    }
});

// Test khachhang table structure
$router->get('/api/test-khachhang', function() {
    try {
        $db = Database::getInstance();
        $columns = $db->fetchAll('DESCRIBE khachhang');
        Response::success($columns, 'Table structure');
    } catch (\Exception $e) {
        Response::serverError('Error: ' . $e->getMessage());
    }
});
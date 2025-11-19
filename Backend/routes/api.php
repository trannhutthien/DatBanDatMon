<?php
/**
 * API Routes
 */

use App\Core\Router;
use App\Controllers\AuthController;

// Auth routes
$router->post('/api/auth/google', [AuthController::class, 'googleLogin']);
$router->post('/api/auth/register', [AuthController::class, 'register']);
$router->post('/api/auth/login', [AuthController::class, 'login']);

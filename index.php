<?php
define('APP_INIT', true);
require_once __DIR__ . '/controllers/LoginController.php';
require_once __DIR__ . '/controllers/DashboardController.php';
require_once __DIR__ . '/controllers/UserController.php';
require_once __DIR__ . '/controllers/BahiaController.php';
require_once __DIR__ . '/controllers/LogoutController.php';

$action = $_GET['action'] ?? 'login';
$action = preg_match('/^[a-zA-Z0-9_]+$/', $action) ? $action : 'login';
$routes = [
    'login' => LoginController::class,
    'dashboard' => DashboardController::class,
    'dashboard_data' => DashboardController::class,
    'alta_usuario' => UserController::class,
    'alta_bahias' => BahiaController::class,
    'logout' => LogoutController::class,
];

$publicActions = ['login', 'logout'];

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (!isset($routes[$action])) {
    header('Location: index.php?action=login');
    exit();
}

if (!in_array($action, $publicActions, true) && empty($_SESSION['ususario'])) {
    header('Location: index.php?action=login');
    exit();
}

$controllerName = $routes[$action];
$controller = new $controllerName();
$controller->index();

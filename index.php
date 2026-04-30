<?php
require_once __DIR__ . '/controllers/LoginController.php';
require_once __DIR__ . '/controllers/DashboardController.php';
require_once __DIR__ . '/controllers/UserController.php';
require_once __DIR__ . '/controllers/BahiaController.php';
require_once __DIR__ . '/controllers/LogoutController.php';

$action = $_GET['action'] ?? 'login';
$routes = [
    'login' => LoginController::class,
    'dashboard' => DashboardController::class,
    'alta_usuario' => UserController::class,
    'alta_bahias' => BahiaController::class,
    'logout' => LogoutController::class,
];

if (!isset($routes[$action])) {
    http_response_code(404);
    echo 'Página no encontrada';
    exit();
}

$controllerName = $routes[$action];
$controller = new $controllerName();
$controller->index();

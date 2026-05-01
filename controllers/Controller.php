<?php
if (!defined('APP_INIT')) {
    http_response_code(403);
    exit('Acceso denegado.');
}

require_once __DIR__ . '/../core/View.php';

abstract class Controller
{
    protected function render(string $view, array $data = []): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        $data['csrfToken'] = $_SESSION['csrf_token'];
        View::render($view, $data);
    }

    protected function validateCsrfToken(?string $token): bool
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        return isset($_SESSION['csrf_token']) && is_string($token) && hash_equals($_SESSION['csrf_token'], $token);
    }

    protected function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit();
    }

    protected function requireLogin(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (!isset($_SESSION['ususario'])) {
            $this->redirect('index.php?action=login');
        }
    }
}

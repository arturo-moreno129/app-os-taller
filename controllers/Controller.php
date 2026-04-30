<?php
require_once __DIR__ . '/../core/View.php';

abstract class Controller
{
    protected function render(string $view, array $data = []): void
    {
        View::render($view, $data);
    }

    protected function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit();
    }

    protected function requireLogin(): void
    {
        session_start();

        if (!isset($_SESSION['ususario'])) {
            $this->redirect('index.php?action=login');
        }
    }
}

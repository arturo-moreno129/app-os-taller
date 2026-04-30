<?php
require_once __DIR__ . '/Controller.php';

class DashboardController extends Controller
{
    public function index(): void
    {
        $this->requireLogin();

        $nombreUsuario = trim(($_SESSION['nombre'] ?? '') . ' ' . ($_SESSION['apellidoP'] ?? ''));
        if ($nombreUsuario === '') {
            $nombreUsuario = $_SESSION['ususario'];
        }

        $this->render('dashboard.php', ['nombreUsuario' => $nombreUsuario]);
    }
}

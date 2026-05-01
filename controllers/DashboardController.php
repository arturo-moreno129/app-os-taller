<?php
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/Bahia.php';

class DashboardController extends Controller
{
    public function index(): void
    {
        $this->requireLogin();

        $nombreUsuario = trim(($_SESSION['nombre'] ?? '') . ' ' . ($_SESSION['apellidoP'] ?? ''));
        if ($nombreUsuario === '') {
            $nombreUsuario = $_SESSION['ususario'];
        }

        $bahiaModel = new Bahia();
        $bays = $bahiaModel->getDashboardBays();

        $bayColors = [
            'BAHIA 1' => 'blue',
            'BAHIA 2' => 'green',
            'BAHIA 3' => 'orange',
            'BAHIA 4' => 'purple',
        ];

        if (($_GET['action'] ?? '') === 'dashboard_data') {
            header('Content-Type: text/html; charset=UTF-8');
            header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
            header('Pragma: no-cache');
            View::render('dashboard-bays.php', [
                'bays' => $bays,
                'bayColors' => $bayColors,
            ]);
            return;
        }

        $this->render('dashboard.php', [
            'nombreUsuario' => $nombreUsuario,
            'bays' => $bays,
            'bayColors' => $bayColors,
        ]);
    }
}

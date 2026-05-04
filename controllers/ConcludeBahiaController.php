<?php
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/Bahia.php';

class ConcludeBahiaController extends Controller
{
    public function index(): void
    {
        $this->requireLogin();

        $bahiaModel = new Bahia();
        $successMessage = $_SESSION['conclude_success'] ?? '';
        $errorMessage = $_SESSION['conclude_error'] ?? '';
        unset($_SESSION['conclude_success'], $_SESSION['conclude_error']);

        if (($_GET['action'] ?? '') === 'concluir_bahia' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->validateCsrfToken($_POST['csrf_token'] ?? null)) {
                $_SESSION['conclude_error'] = 'Token de seguridad inválido.';
            } else {
                $nombreBahia = trim($_POST['nombre_bahia'] ?? '');
                if ($nombreBahia === '') {
                    $_SESSION['conclude_error'] = 'Nombre de bahía inválido.';
                } else {
                    [$success, $message] = $bahiaModel->concludeBay($nombreBahia);
                    if ($success) {
                        $_SESSION['conclude_success'] = $message;
                    } else {
                        $_SESSION['conclude_error'] = $message;
                    }
                }
            }

            $this->redirect('index.php?action=concluir_bahias');
            return;
        }

        $bays = $bahiaModel->findActiveBays();
        $csrfToken = $_SESSION['csrf_token'] ?? '';

        View::render('conclude_bahias.php', [
            'bays' => $bays,
            'csrfToken' => $csrfToken,
            'successMessage' => $successMessage,
            'errorMessage' => $errorMessage,
        ]);
    }
}

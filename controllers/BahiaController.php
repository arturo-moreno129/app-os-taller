<?php
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/Technician.php';
require_once __DIR__ . '/../models/Bahia.php';

class BahiaController extends Controller
{
    public function index(): void
    {
        $this->requireLogin();

        $successMessage = '';
        $errorMessage = '';

        $formValues = [
            'nombre_bahia' => '',
            'os' => '',
            'estatus' => 'En operación',
            'fecha' => '',
            'hora' => '',
            'cliente' => '',
            'motivo' => '',
            'id_tecnico' => ''
        ];

        $technicianModel = new Technician();
        $bahiaModel = new Bahia();
        $tecnicos = $technicianModel->findAll();
        $ultimosRegistros = $bahiaModel->getRecent(5);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->validateCsrfToken($_POST['csrf_token'] ?? null)) {
                $errorMessage = 'Token de seguridad inválido.';
            } else {
                foreach ($formValues as $field => $value) {
                    $formValues[$field] = trim($_POST[$field] ?? '');
                }

                if ($formValues['nombre_bahia'] === '' || $formValues['os'] === '' || $formValues['fecha'] === '' || $formValues['hora'] === '' || $formValues['cliente'] === '' || $formValues['id_tecnico'] === '') {
                    $errorMessage = 'Completa todos los campos obligatorios.';
                } else {
                    $createdBy = isset($_SESSION['id_usuario']) ? (int) $_SESSION['id_usuario'] : 0;

                    [$created, $message] = $bahiaModel->create([
                        'nombre_bahia' => $formValues['nombre_bahia'],
                        'os' => $formValues['os'],
                        'estatus' => $formValues['estatus'],
                        'fecha' => $formValues['fecha'],
                        'hora' => $formValues['hora'],
                        'cliente' => $formValues['cliente'],
                        'motivo' => $formValues['motivo'],
                        'id_tecnico' => (int) $formValues['id_tecnico'],
                        'creado_por' => $createdBy
                    ]);

                    if ($created) {
                        $successMessage = $message;
                        $formValues = [
                            'nombre_bahia' => '',
                            'os' => '',
                            'estatus' => 'En operación',
                            'fecha' => '',
                            'hora' => '',
                            'cliente' => '',
                            'motivo' => '',
                            'id_tecnico' => ''
                        ];
                        $ultimosRegistros = $bahiaModel->getRecent(5);
                    } else {
                        $errorMessage = $message;
                    }
                }
            }
        }

        $nombreUsuario = trim(($_SESSION['nombre'] ?? '') . ' ' . ($_SESSION['apellidoP'] ?? ''));
        if ($nombreUsuario === '') {
            $nombreUsuario = $_SESSION['ususario'];
        }

        $this->render('bahia_form.php', [
            'successMessage' => $successMessage,
            'errorMessage' => $errorMessage,
            'formValues' => $formValues,
            'tecnicos' => $tecnicos,
            'ultimosRegistros' => $ultimosRegistros,
            'nombreUsuario' => $nombreUsuario
        ]);
    }
}

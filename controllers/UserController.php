<?php
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/User.php';

class UserController extends Controller
{
    public function index(): void
    {
        $this->requireLogin();

        $successMessage = '';
        $errorMessage = '';

        $formValues = [
            'usuario' => '',
            'contrasena' => '',
            'nombre' => '',
            'apellidoP' => '',
            'apellidoM' => '',
            'sexo' => 'Masculino',
            'puesto' => '',
            'departamento' => '',
            'rol' => 'Usuario'
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->validateCsrfToken($_POST['csrf_token'] ?? null)) {
                $errorMessage = 'Token de seguridad inválido.';
            } else {
                foreach ($formValues as $field => $value) {
                    $formValues[$field] = trim($_POST[$field] ?? '');
                }

                if ($formValues['usuario'] === '' || $formValues['contrasena'] === '' || $formValues['nombre'] === '' || $formValues['apellidoP'] === '' || $formValues['puesto'] === '' || $formValues['departamento'] === '' || $formValues['rol'] === '') {
                    $errorMessage = 'Completa todos los campos obligatorios.';
                } else {
                    $userModel = new User();

                    if ($userModel->exists($formValues['usuario'])) {
                        $errorMessage = 'El nombre de usuario ya existe. Elige otro diferente.';
                    } elseif ($userModel->create($formValues)) {
                        $successMessage = 'Usuario creado correctamente.';
                        $formValues = [
                            'usuario' => '',
                            'contrasena' => '',
                            'nombre' => '',
                            'apellidoP' => '',
                            'apellidoM' => '',
                            'sexo' => 'Masculino',
                            'puesto' => '',
                            'departamento' => '',
                            'rol' => 'Usuario'
                        ];
                    } else {
                        $errorMessage = 'No se pudo guardar el usuario en la base de datos.';
                    }
                }
            }
        }

        $nombreUsuario = trim(($_SESSION['nombre'] ?? '') . ' ' . ($_SESSION['apellidoP'] ?? ''));
        if ($nombreUsuario === '') {
            $nombreUsuario = $_SESSION['ususario'];
        }

        $this->render('user_form.php', [
            'successMessage' => $successMessage,
            'errorMessage' => $errorMessage,
            'formValues' => $formValues,
            'nombreUsuario' => $nombreUsuario
        ]);
    }
}

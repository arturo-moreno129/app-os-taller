<?php
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/User.php';

class LoginController extends Controller
{
    public function index(): void
    {
        session_start();

        if (isset($_SESSION['ususario'])) {
            $this->redirect('index.php?action=dashboard');
        }

        $errorMessage = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user = trim($_POST['usuario'] ?? '');
            $pass = $_POST['password'] ?? '';

            if ($user === '' || $pass === '') {
                $errorMessage = 'Ingresa tu usuario y tu contrasena.';
            } else {
                $userModel = new User();
                $row = $userModel->authenticate($user, $pass);

                if ($row) {
                    $_SESSION['id_usuario'] = $row['id_usuario'] ?? null;
                    $_SESSION['ususario'] = $row['usuario'] ?? '';
                    $_SESSION['nombre'] = $row['nombre'] ?? '';
                    $_SESSION['apellidoP'] = $row['apellidoP'] ?? '';
                    $_SESSION['apellidoM'] = $row['apellidoM'] ?? '';
                    $_SESSION['sexo'] = $row['sexo'] ?? '';
                    $_SESSION['puesto'] = $row['puesto'] ?? '';
                    $_SESSION['departamento'] = $row['departamento'] ?? '';
                    $_SESSION['rol'] = $row['rol'] ?? '';

                    $this->redirect('index.php?action=dashboard');
                }

                $errorMessage = 'Usuario o contrasena incorrectos.';
            }
        }

        $this->render('login.php', ['errorMessage' => $errorMessage]);
    }
}

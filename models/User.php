<?php
require_once __DIR__ . '/../core/Database.php';

class User
{
    private ?mysqli $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function authenticate(string $username, string $password): ?array
    {
        if (!$this->db) {
            return null;
        }

        $query = "SELECT * FROM usuarios WHERE usuario = ? AND estatus = 'Activo' LIMIT 1";
        $stmt = mysqli_prepare($this->db, $query);

        if (!$stmt) {
            return null;
        }

        mysqli_stmt_bind_param($stmt, 's', $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = $result ? mysqli_fetch_assoc($result) : null;
        mysqli_stmt_close($stmt);

        if ($user && password_verify($password, $user['contrasena'])) {
            return $user;
        }

        return null;
    }

    public function exists(string $username): bool
    {
        if (!$this->db) {
            return false;
        }

        $query = "SELECT id_usuario FROM usuarios WHERE usuario = ? LIMIT 1";
        $stmt = mysqli_prepare($this->db, $query);

        if (!$stmt) {
            return false;
        }

        mysqli_stmt_bind_param($stmt, 's', $username);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        $exists = mysqli_stmt_num_rows($stmt) > 0;
        mysqli_stmt_close($stmt);

        return $exists;
    }

    public function create(array $data): bool
    {
        if (!$this->db) {
            return false;
        }

        $insert = mysqli_prepare(
            $this->db,
            "INSERT INTO usuarios (usuario, contrasena, nombre, apellidoP, apellidoM, sexo, puesto, departamento, rol, estatus) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Activo')"
        );

        if (!$insert) {
            return false;
        }

        $hash = password_hash($data['contrasena'], PASSWORD_DEFAULT);

        mysqli_stmt_bind_param(
            $insert,
            'sssssssss',
            $data['usuario'],
            $hash,
            $data['nombre'],
            $data['apellidoP'],
            $data['apellidoM'],
            $data['sexo'],
            $data['puesto'],
            $data['departamento'],
            $data['rol']
        );

        $success = mysqli_stmt_execute($insert);
        mysqli_stmt_close($insert);
        return $success;
    }

    public function formatFullName(array $user): string
    {
        $name = trim(($user['nombre'] ?? '') . ' ' . ($user['apellidoP'] ?? ''));
        return $name === '' ? ($user['usuario'] ?? '') : $name;
    }
}

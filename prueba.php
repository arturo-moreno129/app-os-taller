<?php

/*
|--------------------------------------------------------------------------
| Script inicial para crear usuario Administrador
|--------------------------------------------------------------------------
| Ejecutar una sola vez y después eliminar por seguridad
|--------------------------------------------------------------------------
*/

$con = require_once __DIR__ . '/conexion.php';

$usuario = "JMoreno";
$nombre = "José Arturo";
$apellidoP = "Moreno";
$apellidoM = "Aguilar";
$sexo = "Masculino";
$puesto = "Administrador General";
$departamento = "Ti - Sistemas";
$passwordPlano = "Benito290496$";
$rol = "Administrador";
$estatus = "Activo";

/*
|--------------------------------------------------------------------------
| Generar hash seguro de contraseña
|--------------------------------------------------------------------------
*/

$contrasenaHash = password_hash($passwordPlano, PASSWORD_DEFAULT);

try {
    if (!defined('NO_DB_ACCESS') && $con) {
        $sqlInsert = "INSERT INTO usuarios (
                        usuario,
                        nombre,
                        apellidoP,
                        apellidoM,
                        sexo,
                        puesto,
                        departamento,
                        contrasena,
                        rol,
                        estatus
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmtInsert = mysqli_prepare($con, $sqlInsert);
        if (!$stmtInsert) {
            throw new Exception('Error al preparar la consulta: ' . mysqli_error($con));
        }

        mysqli_stmt_bind_param(
            $stmtInsert,
            'ssssssssss',
            $usuario,
            $nombre,
            $apellidoP,
            $apellidoM,
            $sexo,
            $puesto,
            $departamento,
            $contrasenaHash,
            $rol,
            $estatus
        );

        if (!mysqli_stmt_execute($stmtInsert)) {
            throw new Exception('Error al ejecutar la consulta: ' . mysqli_stmt_error($stmtInsert));
        }

        mysqli_stmt_close($stmtInsert);

        echo "Usuario administrador creado correctamente.<br><br>";
        echo "Usuario: $usuario<br>";
        echo "Contraseña: $passwordPlano<br><br>";
        echo "IMPORTANTE: elimina este archivo después de usarlo.";
    } else {
        throw new Exception('No se pudo conectar a la base de datos.');
    }
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
<?php
use MongoDB\Client;

// Seguridad: No mostrar errores de texto que rompan el JSON
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');

try {
    // Ruta corregida: sube de 'Back' a la raíz para encontrar 'vendor'
    require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

    $response = ["status" => "error", "message" => "Error interno"];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($email) || empty($password)) {
            $response["message"] = "Todos los campos son obligatorios.";
        } else {
            // Verificamos si el EMAIL O el USERNAME ya existen
            $existeUsuario = $collection->findOne([
                '$or' => [
                    ['email' => $email],
                    ['username' => $username]
                ]
            ]);

            if ($existeUsuario) {
                $response["message"] = "El correo o el nombre de usuario ya están registrados.";
            } else {
                $collection->insertOne([
                    "username" => $username, // Guardamos el nuevo campo
                    "email" => $email,
                    "password" => password_hash($password, PASSWORD_BCRYPT),
                    "fecha_creacion" => date("Y-m-d H:i:s")
                ]);

                $response = [
                    "status" => "success",
                    "message" => "¡Registro completado exitosamente!",
                    "redirect" => "../Pages/login.html"
                ];
            }
        }
    }
} catch (Exception $e) {
    $response["message"] = "Error de conexión con la base de datos.";
}

echo json_encode($response);
exit();
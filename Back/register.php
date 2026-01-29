<?php
use MongoDB\Client;

// Habilitar errores temporalmente para depurar
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

try {
    require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

    $response = ["status" => "error", "message" => "Error desconocido"];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Recogemos el nuevo campo 'username'
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($email) || empty($password)) {
            $response["message"] = "Todos los campos (usuario, email y clave) son obligatorios.";
        } else {
            $uri = "mongodb+srv://alexiscastelln_db_user:LOLOKRIKO@cluster0.zfxempk.mongodb.net/?appName=Cluster0";
            $client = new Client($uri, [], ["tlsInsecure" => true]);
            $collection = $client->KIBO->datos;

            // Buscamos si el email O el username ya existen
            $existe = $collection->findOne([
                '$or' => [
                    ['email' => $email],
                    ['username' => $username]
                ]
            ]);

            if ($existe) {
                $response["message"] = "El nombre de usuario o el correo ya están en uso.";
            } else {
                $insertar = $collection->insertOne([
                    "username" => $username,
                    "email" => $email,
                    "password" => password_hash($password, PASSWORD_BCRYPT),
                    "role" => "user", // Rol por defecto
                    "fecha_creacion" => date("Y-m-d H:i:s")
                ]);

                if ($insertar->getInsertedCount() > 0) {
                    $response = [
                        "status" => "success",
                        "message" => "¡Registro completado! Ya puedes iniciar sesión.",
                        "redirect" => "../Pages/login.html"
                    ];
                }
            }
        }
    }
} catch (Exception $e) {
    // Esto nos dirá el error real en la consola del navegador
    $response["message"] = "Error de base de datos: " . $e->getMessage();
}

echo json_encode($response);
exit();
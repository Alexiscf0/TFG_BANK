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
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $response["message"] = "Todos los campos son obligatorios.";
        } else {
            $uri = "mongodb+srv://alexiscastelln_db_user:LOLOKRIKO@cluster0.zfxempk.mongodb.net/?appName=Cluster0";

            // Conexión con bypass de TLS para evitar problemas de certificados en Windows
            $client = new Client($uri, [], ["tlsInsecure" => true]);
            $collection = $client->KIBO->datos;

            if ($collection->findOne(['email' => $email])) {
                $response["message"] = "Este correo ya está registrado.";
            } else {
                $collection->insertOne([
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
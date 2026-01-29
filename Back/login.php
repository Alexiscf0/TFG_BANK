<?php
header('Content-Type: application/json');
require 'C:/xampp/htdocs/Kibo/vendor/autoload.php';
use MongoDB\Client;
session_start();

$response = ["status" => "error", "message" => "Error desconocido"];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recogemos 'username' en lugar de 'email'
    $userInput = trim($_POST['username'] ?? '');
    $passInput = $_POST['password'] ?? '';

    try {
        $client = new Client("mongodb+srv://alexiscastelln_db_user:LOLOKRIKO@cluster0.zfxempk.mongodb.net/?appName=Cluster0");
        $collection = $client->KIBO->datos;

        // Buscamos por el campo username
        $usuario = $collection->findOne(['username' => $userInput]);

        if ($usuario) {
            if (password_verify($passInput, $usuario['password'])) {
                $_SESSION['user_email'] = (string)$usuario['email'];
                $_SESSION['username'] = (string)$usuario['username'];
                $_SESSION['role'] = isset($usuario['role']) ? (string)$usuario['role'] : 'user';

                $response = [
                    "status" => "success",
                    "message" => "¡Bienvenido, " . $_SESSION['username'] . "!",
                    "role" => $_SESSION['role'],
                    "redirect" => "../Pages/index.html"
                ];
            } else {
                $response["message"] = "Contraseña incorrecta.";
            }
        } else {
            $response["message"] = "El usuario '$userInput' no existe.";
        }
    } catch (Exception $e) {
        $response["message"] = "Error de conexión: " . $e->getMessage();
    }
}

echo json_encode($response);
exit();
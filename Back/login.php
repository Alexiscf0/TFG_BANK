<?php
header('Content-Type: application/json');
require 'C:/xampp/htdocs/Kibo/vendor/autoload.php';
use MongoDB\Client;
session_start();

$response = ["status" => "error", "message" => "Ocurrió un error"];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $emailInput = trim($_POST['email'] ?? '');
    $passInput = $_POST['password'] ?? '';

    try {
        $client = new Client("mongodb+srv://alexiscastelln_db_user:LOLOKRIKO@cluster0.zfxempk.mongodb.net/?appName=Cluster0");
        $collection = $client->KIBO->datos;

        $usuario = $collection->findOne(['email' => $emailInput]);

        if ($usuario) {
            if (password_verify($passInput, $usuario['password'])) {

                $_SESSION['user_email'] = (string)$usuario['email'];
                $_SESSION['role'] = isset($usuario['role']) ? (string)$usuario['role'] : 'user';

                // Respuesta indicando si es admin o no
                $response = [
                    "status" => "success",
                    "message" => "Login correcto",
                    "es_admin" => ($_SESSION['role'] === 'admin'),
                    "role" => $_SESSION['role'],
                    "redirect" => "../Pages/index.html" // <--- Ambos van al mismo sitio
                ];
            } else {
                $response["message"] = "Contraseña incorrecta.";
            }
        } else {
            $response["message"] = "El usuario no existe.";
        }
    } catch (Exception $e) {
        $response["message"] = "Error de servidor: " . $e->getMessage();
    }
}

echo json_encode($response);
exit();
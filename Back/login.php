<?php
header('Content-Type: application/json');
require 'C:/xampp/htdocs/Kibo/vendor/autoload.php';
use MongoDB\Client;
session_start();

$response = ["status" => "error", "message" => "Ocurrió un error"];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userInput = trim($_POST['username'] ?? ''); // Cambiado de email a username
    $passInput = $_POST['password'] ?? '';

    try {
        // ... conexión ...
        $usuario = $collection->findOne(['username' => $userInput]); // Buscar por usuario

        if ($usuario) {
            if (password_verify($passInput, $usuario['password'])) {
                $_SESSION['user_email'] = (string)$usuario['email'];
                $_SESSION['username'] = (string)$usuario['username']; // Guardamos también el nombre en sesión
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
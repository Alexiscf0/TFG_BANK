<?php
header('Content-Type: application/json'); // Indicamos que devolvemos JSON
require_once __DIR__ . '/../vendor/autoload.php';
use MongoDB\Client;
session_start();

$response = ["status" => "error", "message" => "Método no permitido"];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $emailInput = trim($_POST['email'] ?? '');
    $passInput = $_POST['password'] ?? '';

    try {
        $client = new Client("mongodb+srv://alexiscastelln_db_user:LOLOKRIKO@cluster0.zfxempk.mongodb.net/?appName=Cluster0");
        $collection = $client->KIBO->datos;

        $usuario = $collection->findOne(['email' => $emailInput]);

        if ($usuario) {
            if (password_verify($passInput, $usuario['password'])) {
                $_SESSION['user_email'] = $usuario['email'];
                $response = [
                    "status" => "success",
                    "message" => "Login correcto",
                    "redirect" => "../Pages/index.html"
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
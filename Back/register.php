<?php
header('Content-Type: application/json');
require 'C:/xampp/htdocs/Kibo/vendor/autoload.php';
use MongoDB\Client;

$response = ["status" => "error", "message" => "Error desconocido"];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $response["message"] = "Rellena todos los campos.";
    } else {
        try {
            $client = new Client("mongodb+srv://alexiscastelln_db_user:LOLOKRIKO@cluster0.zfxempk.mongodb.net/?appName=Cluster0");
            $collection = $client->KIBO->datos;

            // Verificar si el email ya existe
            $existe = $collection->findOne(['email' => $email]);
            if ($existe) {
                $response["message"] = "El correo ya está registrado.";
            } else {
                $passwordHash = password_hash($password, PASSWORD_BCRYPT);
                $collection->insertOne([
                    "email" => $email,
                    "password" => $passwordHash,
                    "fecha_creacion" => date("Y-m-d H:i:s")
                ]);

                $response = [
                    "status" => "success",
                    "message" => "Usuario registrado con éxito",
                    "redirect" => "../Pages/login.html"
                ];
            }
        } catch (Exception $e) {
            $response["message"] = "Error: " . $e->getMessage();
        }
    }
}

echo json_encode($response);
exit();
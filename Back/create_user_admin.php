<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../vendor/autoload.php';
use MongoDB\Client;

$admin_email = "admin@gmail.com";
if (!isset($_SESSION['user_email']) || $_SESSION['user_email'] !== $admin_email) {
    echo json_encode(["status" => "error", "message" => "No autorizado"]);
    exit;
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

try {
    $uri = "mongodb+srv://alexiscastelln_db_user:LOLOKRIKO@cluster0.zfxempk.mongodb.net/?appName=Cluster0";
    $client = new Client($uri, [], ["tlsInsecure" => true]);
    $collection = $client->KIBO->datos;

    if ($collection->findOne(['email' => $email])) {
        echo json_encode(["status" => "error", "message" => "El usuario ya existe"]);
    } else {
        $collection->insertOne([
            "email" => $email,
            "password" => password_hash($password, PASSWORD_BCRYPT), // Encriptación segura
            "fecha_creacion" => date("Y-m-d H:i:s")
        ]);
        echo json_encode(["status" => "success", "message" => "Usuario creado con éxito"]);
    }
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => "Error de BD"]);
}
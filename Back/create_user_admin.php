<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../vendor/autoload.php';
use MongoDB\Client;

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(["status" => "error", "message" => "No autorizado"]);
    exit;
}

$username = trim($_POST['username'] ?? ''); // Nuevo campo recibido del formulario
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

try {
    $uri = "mongodb+srv://alexiscastelln_db_user:LOLOKRIKO@cluster0.zfxempk.mongodb.net/?appName=Cluster0";
    $client = new Client($uri, [], ["tlsInsecure" => true]);
    $collection = $client->KIBO->datos;

    // Verificar si el usuario o email ya existen
    if ($collection->findOne(['$or' => [['email' => $email], ['username' => $username]]])) {
        echo json_encode(["status" => "error", "message" => "El usuario o email ya existe"]);
    } else {
        $collection->insertOne([
            "username" => $username,
            "email" => $email,
            "password" => password_hash($password, PASSWORD_BCRYPT),
            "role" => "user",
            "fecha_creacion" => date("Y-m-d H:i:s")
        ]);
        echo json_encode(["status" => "success", "message" => "Usuario creado con éxito"]);
    }
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => "Error de BD"]);
}
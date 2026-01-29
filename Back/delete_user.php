<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../vendor/autoload.php';
use MongoDB\Client;

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(["status" => "error", "message" => "No autorizado"]);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$emailAEliminar = $input['email'] ?? '';

// Comprobar que el admin no se borre a sí mismo comparando correos
if ($emailAEliminar === $_SESSION['user_email']) {
    echo json_encode(["status" => "error", "message" => "No puedes eliminarte a ti mismo"]);
    exit;
}

try {
    $uri = "mongodb+srv://alexiscastelln_db_user:LOLOKRIKO@cluster0.zfxempk.mongodb.net/?appName=Cluster0";
    $client = new Client($uri);
    $collection = $client->KIBO->datos;

    $resultado = $collection->deleteOne(['email' => $emailAEliminar]);

    if ($resultado->getDeletedCount() > 0) {
        echo json_encode(["status" => "success", "message" => "Usuario eliminado"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Usuario no encontrado"]);
    }
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
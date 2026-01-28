<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../vendor/autoload.php';
use MongoDB\Client;

if (!isset($_SESSION['user_email'])) {
    echo json_encode(["status" => "error", "message" => "Sesión no iniciada"]);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

try {
    $uri = "mongodb+srv://alexiscastelln_db_user:LOLOKRIKO@cluster0.zfxempk.mongodb.net/?appName=Cluster0";
    $client = new Client($uri);
    $collection = $client->KIBO->metas;
    $email = $_SESSION['user_email'];

    // Usamos 'upsert' para que si no existe el registro del usuario, lo cree; y si existe, lo actualice
    $collection->updateOne(
        ['user_email' => $email],
        ['$set' => [
            'meta' => (float)$input['meta'],
            'acumulado' => (float)$input['acumulado'],
            'fecha_actualizacion' => date("Y-m-d H:i:s")
        ]],
        ['upsert' => true]
    );

    echo json_encode(["status" => "success", "message" => "Ahorro persistido"]);
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
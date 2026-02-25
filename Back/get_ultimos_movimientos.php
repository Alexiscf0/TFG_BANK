<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../vendor/autoload.php';
use MongoDB\Client;

if (!isset($_SESSION['user_email'])) {
    echo json_encode(["status" => "error", "message" => "Sesión no iniciada"]);
    exit;
}

try {
    $uri = "mongodb+srv://alexiscastelln_db_user:LOLOKRIKO@cluster0.zfxempk.mongodb.net/?appName=Cluster0";
    $client = new Client($uri, [], ["tlsInsecure" => true]);
    $collection = $client->KIBO->movimientos;

    $cursor = $collection->find(
        ['user_email' => $_SESSION['user_email']],
        ['limit' => 5, 'sort' => ['fecha' => -1, '_id' => -1]]
    );

    $movimientos = [];
    foreach ($cursor as $doc) {
        $movimientos[] = [
            "id" => (string)$doc['_id'],
            "concepto" => $doc['concepto'] ?? 'Sin concepto',
            "precio" => (float)($doc['precio'] ?? 0),
            "categoria" => $doc['categoria'] ?? 'Otros',
            "fecha" => $doc['fecha'] ?? ''
        ];
    }
    echo json_encode(["status" => "success", "data" => $movimientos]);
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
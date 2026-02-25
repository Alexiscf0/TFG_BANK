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

    $cursor = $collection->find(['user_email' => $_SESSION['user_email']]);

    $movimientos = [];
    foreach ($cursor as $doc) {
        $movimientos[] = [
            "id"        => (string)$doc['_id'], // Campo vital para el desempate
            "fecha"     => $doc['fecha'] ?? 'N/A',
            "concepto"  => $doc['concepto'] ?? 'Sin descripción',
            "categoria" => $doc['categoria'] ?? 'General',
            "precio"    => $doc['precio'] ?? 0
        ];
    }
    echo json_encode($movimientos);
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
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
    $client = new Client($uri);
    $collection = $client->KIBO->movimientos;
    $email = $_SESSION['user_email'];

    // Buscamos los 3 últimos ordenados por fecha (descendente)
    $cursor = $collection->find(
        ['user_email' => $email],
        [
            'limit' => 3,
            'sort' => ['fecha' => -1]
        ]
    );

    $movimientos = [];
    foreach ($cursor as $doc) {
        $movimientos[] = [
            "concepto"  => $doc['concepto'],
            "precio"    => $doc['precio'],
            "categoria" => $doc['categoria'],
            "fecha"     => $doc['fecha'],
            "tipo"      => $doc['tipo']
        ];
    }

    echo json_encode(["status" => "success", "data" => $movimientos]);
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_email'])) {
    echo json_encode(["status" => "error", "message" => "Sesión no iniciada"]);
    exit;
}

require_once __DIR__ . '/../vendor/autoload.php';
use MongoDB\Client;

try {
    $uri = "mongodb+srv://alexiscastelln_db_user:LOLOKRIKO@cluster0.zfxempk.mongodb.net/?appName=Cluster0";
    $client = new Client($uri, [], ["tlsInsecure" => true]);
    $collection = $client->KIBO->movimientos;

    $userEmail = $_SESSION['user_email'];

    // ORDEN ABSOLUTO: Fecha del ticket DESC y luego _id DESC (orden real de inserción)
    $opciones = [
        'limit' => 5,
        'sort' => [
            'fecha' => -1,
            '_id' => -1
        ]
    ];

    $cursor = $collection->find(['user_email' => $userEmail], $opciones);

    $movimientos = [];
    foreach ($cursor as $doc) {
        $movimientos[] = [
            "id"        => (string)$doc['_id'], // Pasamos el ID para desempatar en el front
            "concepto"  => $doc['concepto'] ?? 'Sin concepto',
            "precio"    => (float)($doc['precio'] ?? 0),
            "categoria" => $doc['categoria'] ?? 'Otros',
            "fecha"     => $doc['fecha'] ?? ''
        ];
    }

    echo json_encode(["status" => "success", "data" => $movimientos]);

} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
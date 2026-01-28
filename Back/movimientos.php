<?php
session_start();
use MongoDB\Client;

header('Content-Type: application/json');

error_reporting(0);
ini_set('display_errors', 0);

try {
    // 1. Verificar sesión (Coincide con tu login.php)
    if (!isset($_SESSION['user_email'])) {
        echo json_encode(["status" => "error", "message" => "Sesión no iniciada"]);
        exit;
    }

    $emailUsuario = $_SESSION['user_email'];

    // 2. Cargar dependencias
    require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

    $uri = "mongodb+srv://alexiscastelln_db_user:LOLOKRIKO@cluster0.zfxempk.mongodb.net/?appName=Cluster0";
    $client = new Client($uri, [], ["tlsInsecure" => true]);

    $collection = $client->KIBO->movimientos;

    // 3. CONSULTA CORREGIDA: En tu DB el campo se llama 'user_email'
    $cursor = $collection->find(['user_email' => $emailUsuario]);

    $movimientos = [];
    foreach ($cursor as $doc) {
        $movimientos[] = [
            "fecha"     => $doc['fecha'] ?? 'N/A',
            "concepto"  => $doc['concepto'] ?? 'Sin descripción',
            "categoria" => $doc['categoria'] ?? 'General',
            // 4. MAPEO CORREGIDO: En tu DB el campo se llama 'precio'
            "cantidad"  => $doc['precio'] ?? 0
        ];
    }

    echo json_encode($movimientos);

} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
exit;
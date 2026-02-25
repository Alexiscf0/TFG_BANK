<?php
// Back/get_presupuestos.php
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
    $collection = $client->KIBO->presupuestos;

    $email = $_SESSION['user_email'];
    // Buscamos solo los presupuestos del usuario actual
    $cursor = $collection->find(['user_email' => $email]);

    $resultados = [];
    foreach ($cursor as $doc) {
        $resultados[] = [
            "categoria" => $doc['categoria'] ?? 'Sin categoría',
            "limite_mensual" => (float)($doc['limite_mensual'] ?? 0)
        ];
    }

    echo json_encode(["status" => "success", "data" => $resultados]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
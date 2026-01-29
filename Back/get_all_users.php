<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../vendor/autoload.php';
use MongoDB\Client;

// Seguridad: Verifica el rol 'admin' guardado en la sesión
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(["status" => "error", "message" => "Acceso denegado: No eres administrador"]);
    exit;
}

try {
    $uri = "mongodb+srv://alexiscastelln_db_user:LOLOKRIKO@cluster0.zfxempk.mongodb.net/?appName=Cluster0";
    $client = new Client($uri);
    $collection = $client->KIBO->datos;

    $cursor = $collection->find([], ['projection' => ['password' => 0]]);

    $usuarios = [];
    foreach ($cursor as $doc) {
        $usuarios[] = [
            "username" => $doc['username'] ?? 'Sin usuario', // Nuevo campo
            "email" => $doc['email'],
            "fecha" => $doc['fecha_creacion'] ?? 'N/A'
        ];
    }
    echo json_encode(["status" => "success", "data" => $usuarios]);
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
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
    $collection = $client->KIBO->metas;
    $email = $_SESSION['user_email'];

    $datos = $collection->findOne(['user_email' => $email]);

    echo json_encode([
        "status" => "success",
        "meta" => $datos ? (float)$datos['meta'] : 1000,
        "acumulado" => $datos ? (float)$datos['acumulado'] : 0
    ]);
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
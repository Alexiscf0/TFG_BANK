<?php

// Usamos __DIR__ para asegurar que la ruta sea relativa a este archivo
require_once __DIR__ . '/../vendor/autoload.php';

use MongoDB\Client;

// Tu cadena de conexión de Atlas (Paso 5 de Drivers)
$uri = "mongodb+srv://alexiscastelln_db_user:LOLOKRIKO@cluster0.zfxempk.mongodb.net/?appName=Cluster0";

try {
    $client = new Client($uri);

    // Esto confirma que la extensión está bien instalada
    $db = $client->selectDatabase('test');

    echo json_encode(["status" => "success", "message" => "Conectado a Atlas"]);
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
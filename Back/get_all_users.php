<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../vendor/autoload.php';
use MongoDB\Client;

// SEGURIDAD: Solo el admin debería entrar.
// Puedes poner aquí tu correo para que solo tú tengas acceso:
$admin_email = "admin@gmail.com";

if (!isset($_SESSION['user_email']) || $_SESSION['user_email'] !== $admin_email) {
    echo json_encode(["status" => "error", "message" => "Acceso denegado: No eres administrador"]);
    exit;
}

try {
    $uri = "mongodb+srv://alexiscastelln_db_user:LOLOKRIKO@cluster0.zfxempk.mongodb.net/?appName=Cluster0";
    $client = new Client($uri);
    $collection = $client->KIBO->datos; // Donde se guardan los usuarios

    $cursor = $collection->find([], ['projection' => ['password' => 0]]); // Excluimos el hash de la pass

    $usuarios = [];
    foreach ($cursor as $doc) {
        $usuarios[] = [
            "email" => $doc['email'],
            "fecha" => $doc['fecha_creacion'] ?? 'Antiguo'
        ];
    }

    echo json_encode(["status" => "success", "data" => $usuarios]);

} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
<?php
// Back/eliminar_presupuesto.php
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

    $categoria = $_POST['categoria'];
    $email = $_SESSION['user_email'];

    // Eliminamos el presupuesto que coincida con el usuario y la categoría
    $resultado = $collection->deleteOne([
        'user_email' => $email,
        'categoria' => $categoria
    ]);

    if ($resultado->getDeletedCount() > 0) {
        echo json_encode(["status" => "success", "message" => "Límite eliminado correctamente"]);
    } else {
        echo json_encode(["status" => "error", "message" => "No se encontró el límite para eliminar"]);
    }

} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
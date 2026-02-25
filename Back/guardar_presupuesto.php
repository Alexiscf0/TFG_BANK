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
    $collection = $client->KIBO->presupuestos;

    $categoria = $_POST['categoria'];
    $limite = (float)$_POST['limite'];
    $email = $_SESSION['user_email'];

    // Usamos updateOne con 'upsert' => true.
    // Si existe el presupuesto para ese usuario y categoría, lo actualiza. Si no, lo crea.
    $resultado = $collection->updateOne(
        ['user_email' => $email, 'categoria' => $categoria],
        ['$set' => ['limite_mensual' => $limite]],
        ['upsert' => true]
    );

    echo json_encode(["status" => "success", "message" => "Presupuesto de $categoria actualizado a $limite €"]);

} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
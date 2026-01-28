<?php
// Back/procesar_gasto.php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../vendor/autoload.php';
use MongoDB\Client;

// Comprobamos si hay un usuario logueado
if (!isset($_SESSION['user_email'])) {
    echo json_encode(["status" => "error", "message" => "Sesión no iniciada"]);
    exit;
}

try {
    // Conexión a tu MongoDB Atlas
    $uri = "mongodb+srv://alexiscastelln_db_user:LOLOKRIKO@cluster0.zfxempk.mongodb.net/?appName=Cluster0";
    $client = new Client($uri);
    $collection = $client->KIBO->movimientos;

    // Recogemos los datos del formulario
    $concepto  = $_POST['concepto']  ?? 'Sin concepto';
    $precio    = (float)($_POST['precio'] ?? 0);
    $categoria = $_POST['categoria'] ?? 'Otros';
    $fecha     = $_POST['fecha']     ?? date("Y-m-d");
    $email     = $_SESSION['user_email'];

    // Preparamos el documento con los campos que requiere el Dashboard
    $documento = [
        "user_email" => $email,
        "concepto"   => $concepto,
        "precio"     => $precio,
        "categoria"  => $categoria,
        "fecha"      => $fecha,
        "tipo"       => "gasto", // Fundamental para que el Dashboard lo sume como gasto
        "fecha_creacion" => date("Y-m-d H:i:s")
    ];

    $resultado = $collection->insertOne($documento);

    if ($resultado->getInsertedCount() > 0) {
        echo json_encode(["status" => "success", "message" => "Guardado en Atlas"]);
    } else {
        echo json_encode(["status" => "error", "message" => "No se pudo insertar en la BD"]);
    }

} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => "Error de BD: " . $e->getMessage()]);
}
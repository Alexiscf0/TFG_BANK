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
    // Conexión a MongoDB Atlas
    $uri = "mongodb+srv://alexiscastelln_db_user:LOLOKRIKO@cluster0.zfxempk.mongodb.net/?appName=Cluster0";
    $client = new Client($uri);
    $collection = $client->KIBO->movimientos;

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email     = $_SESSION['user_email'];
        $concepto  = $_POST['concepto']  ?? 'Sin concepto';
        $precioBruto = (float)($_POST['precio'] ?? 0);
        $categoria = $_POST['categoria'] ?? 'Otros';
        $fecha     = $_POST['fecha']     ?? date("Y-m-d");
        $tipo      = $_POST['tipo']      ?? 'Gasto';

        // --- LÓGICA DE SIGNOS (Crucial para el Dashboard) ---
        // Si es Gasto, el precio debe ser negativo para que el Dashboard reste.
        if (strtolower($tipo) === 'gasto') {
            $precioFinal = -abs($precioBruto);
        } else {
            $precioFinal = abs($precioBruto);
        }

        // Preparamos el documento para MongoDB
        $documento = [
            "user_email" => $email,
            "concepto"   => $concepto,
            "precio"     => $precioFinal,
            "categoria"  => $categoria,
            "fecha"      => $fecha,
            "tipo"       => strtolower($tipo),
            "fecha_creacion" => date("Y-m-d H:i:s")
        ];

        $resultado = $collection->insertOne($documento);

        if ($resultado->getInsertedCount() > 0) {
            echo json_encode(["status" => "success", "message" => "Movimiento guardado como " . $tipo]);
        } else {
            echo json_encode(["status" => "error", "message" => "No se pudo insertar en la BD"]);
        }
    }
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => "Error de BD: " . $e->getMessage()]);
}
exit;
<?php
// Back/procesar_gasto.php
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
    $collection = $client->KIBO->movimientos;

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $concepto  = $_POST['concepto'] ?? 'Sin concepto';
        $precio    = (float)($_POST['precio'] ?? 0);
        $categoria = $_POST['categoria'] ?? 'Otros';
        $fecha     = $_POST['fecha'] ?? date("Y-m-d");

        // CORRECCIÓN CLAVE: Ahora leemos lo que el usuario ha elegido en el desplegable
        $tipo      = $_POST['tipo'] ?? 'gasto';

        $email     = $_SESSION['user_email'];

        $documento = [
            "user_email" => $email,
            "concepto"   => $concepto,
            "precio"     => $precio,
            "categoria"  => $categoria,
            "fecha"      => $fecha,
            "tipo"       => $tipo, // <--- Aquí ya no pone "gasto" fijo, usa el valor del formulario
            "fecha_creacion" => date("Y-m-d H:i:s")
        ];

        $resultado = $collection->insertOne($documento);

        if ($resultado->getInsertedCount() > 0) {
            echo json_encode(["status" => "success", "message" => "Movimiento guardado"]);
        } else {
            echo json_encode(["status" => "error", "message" => "No se pudo insertar"]);
        }
    }
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
exit;
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
    $db = $client->KIBO;

    $email = $_SESSION['user_email'];
    $categoria = $_POST['categoria'] ?? 'Otros';
    $precioBruto = (float)($_POST['precio'] ?? 0);
    $tipo = $_POST['tipo'] ?? 'Gasto';
    $fecha = $_POST['fecha'] ?? date("Y-m-d");

    if (strtolower($tipo) === 'gasto') {
        // Buscar límite en la colección presupuestos (según tu imagen)
        $presupuestoDoc = $db->presupuestos->findOne([
            'user_email' => $email,
            'categoria' => $categoria
        ]);

        if ($presupuestoDoc && isset($presupuestoDoc['limite_mensual'])) {
            $limiteMax = (float)$presupuestoDoc['limite_mensual'];

            // Sumar gastos del mes actual
            $primerDia = date("Y-m-01", strtotime($fecha));
            $ultimoDia = date("Y-m-t", strtotime($fecha));

            $pipeline = [
                ['$match' => [
                    'user_email' => $email,
                    'categoria' => $categoria,
                    'tipo' => 'gasto',
                    'fecha' => ['$gte' => $primerDia, '$lte' => $ultimoDia]
                ]],
                ['$group' => ['_id' => null, 'total' => ['$sum' => '$precio']]]
            ];

            $cursor = $db->movimientos->aggregate($pipeline);
            $resAgregacion = $cursor->toArray();
            $yaGastado = isset($resAgregacion[0]) ? abs((float)$resAgregacion[0]['total']) : 0;

            if (($yaGastado + $precioBruto) > $limiteMax) {
                echo json_encode([
                    "status" => "error",
                    "message" => "Límite de $limiteMax € excedido para $categoria. Llevas: $yaGastado €."
                ]);
                exit;
            }
        }
    }

    // Inserción normal si pasa la validación
    $documento = [
        "user_email" => $email,
        "concepto" => $_POST['concepto'] ?? 'Sin concepto',
        "precio" => (strtolower($tipo) === 'gasto') ? -abs($precioBruto) : abs($precioBruto),
        "categoria" => $categoria,
        "fecha" => $fecha,
        "tipo" => strtolower($tipo),
        "fecha_creacion" => date("Y-m-d H:i:s")
    ];

    $db->movimientos->insertOne($documento);
    echo json_encode(["status" => "success", "message" => "Movimiento guardado"]);

} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
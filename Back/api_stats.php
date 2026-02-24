<?php
session_start();
// Limpiamos cualquier salida previa para evitar errores de JSON
if (ob_get_length()) ob_clean();
header('Content-Type: application/json');

if (!isset($_SESSION['user_email'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Sesión no iniciada']);
    exit;
}

require_once __DIR__ . '/../vendor/autoload.php';
use MongoDB\Client;

$uri = "mongodb+srv://alexiscastelln_db_user:LOLOKRIKO@cluster0.zfxempk.mongodb.net/?appName=Cluster0";

try {
    // Añadimos tlsInsecure por si tu servidor local tiene problemas de certificados
    $client = new Client($uri, [], ["tlsInsecure" => true]);
    $collection = $client->KIBO->movimientos;

    $userEmail = $_SESSION['user_email'];
    $cursor = $collection->find(['user_email' => $userEmail]);

    $ingresos = 0;
    $gastos = 0;
    $categoriasGasto = [];

    foreach ($cursor as $doc) {
        $valor = floatval($doc['precio'] ?? 0);
        $tipo = strtolower($doc['tipo'] ?? 'gasto');
        $cat = $doc['categoria'] ?? 'Otros';

        if ($tipo === 'ingreso') {
            $ingresos += $valor;
        } else {
            $montoAbs = abs($valor);
            $gastos += $montoAbs;
            // Agrupamos por categoría
            $categoriasGasto[$cat] = ($categoriasGasto[$cat] ?? 0) + $montoAbs;
        }
    }

    // Ordenar de mayor a menor gasto
    arsort($categoriasGasto);

    echo json_encode([
        'status' => 'success',
        'ingresos' => $ingresos,
        'gastos' => $gastos,
        'ahorro' => $ingresos - $gastos,
        'categorias' => $categoriasGasto
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
exit;
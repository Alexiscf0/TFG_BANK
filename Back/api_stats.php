<?php
session_start();
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
    $client = new Client($uri, [], ["tlsInsecure" => true]);
    $userEmail = $_SESSION['user_email'];

    // 1. Calcular Rango de Fechas
    $periodo = $_GET['periodo'] ?? 'mes';
    $fechaRef = $_GET['fecha_ref'] ?? date('Y-m-d');
    $inicio = ""; $fin = "";

    switch ($periodo) {
        case 'semana':
            $inicio = date('Y-m-d', strtotime('monday this week', strtotime($fechaRef)));
            $fin = date('Y-m-d', strtotime('sunday this week', strtotime($fechaRef)));
            break;
        case 'ano':
        case 'año':
            $inicio = date('Y-01-01', strtotime($fechaRef));
            $fin = date('Y-12-31', strtotime($fechaRef));
            break;
        case 'mes':
        default:
            $inicio = date('Y-m-01', strtotime($fechaRef));
            $fin = date('Y-m-t', strtotime($fechaRef));
            break;
    }

    // 2. Obtener Movimientos
    $collection = $client->KIBO->movimientos;
    $cursor = $collection->find(['user_email' => $userEmail, 'fecha' => ['$gte' => $inicio, '$lte' => $fin]]);

    $ingresos = 0; $gastos = 0; $categoriasGasto = [];
    foreach ($cursor as $doc) {
        $valor = floatval($doc['precio'] ?? 0);
        $tipo = strtolower($doc['tipo'] ?? 'gasto');
        $cat = $doc['categoria'] ?? 'Otros';
        if ($tipo === 'ingreso') { $ingresos += $valor; }
        else { $montoAbs = abs($valor); $gastos += $montoAbs; $categoriasGasto[$cat] = ($categoriasGasto[$cat] ?? 0) + $montoAbs; }
    }
    arsort($categoriasGasto);

    // 3. Obtener Límites establecidos
    $colLimites = $client->KIBO->limites;
    $limitesDocs = $colLimites->find(['user_email' => $userEmail]);
    $limitesMap = [];
    foreach ($limitesDocs as $l) { $limitesMap[$l['categoria']] = (float)$l['tope']; }

    echo json_encode([
        'status' => 'success',
        'ingresos' => $ingresos,
        'gastos' => $gastos,
        'ahorro' => $ingresos - $gastos,
        'categorias' => $categoriasGasto,
        'limites' => $limitesMap,
        'rango' => ['inicio' => $inicio, 'fin' => $fin]
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
<?php
// Iniciamos sesión para acceder al email del usuario
session_start();
ob_clean();
header('Content-Type: application/json');

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_email'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Sesión no iniciada']);
    exit;
}

$userEmail = $_SESSION['user_email'];

require_once __DIR__ . '/../vendor/autoload.php';

$uri = "mongodb+srv://alexiscastelln_db_user:LOLOKRIKO@cluster0.zfxempk.mongodb.net/?appName=Cluster0";

try {
    $client = new MongoDB\Client($uri);
    $dbSeleccionada = "KIBO";
    $coleccionSeleccionada = "movimientos";
    $collection = $client->$dbSeleccionada->$coleccionSeleccionada;

    // FILTRO: Solo buscamos documentos donde user_email coincida con la sesión
    $filtro = ['user_email' => $userEmail];
    $cursor = $collection->find($filtro);

    $ingresos = 0;
    $gastos = 0;

    foreach ($cursor as $doc) {
        $valor = floatval($doc['precio'] ?? 0);
        $tipo = strtolower($doc['tipo'] ?? '');

        if ($tipo === 'ingreso') {
            $ingresos += $valor;
        } elseif ($tipo === 'gasto') {
            $gastos += $valor;
        }
    }

    $ahorroNeto = $ingresos + $gastos;

    echo json_encode([
        'ingresos' => $ingresos,
        'gastos' => $gastos,
        'ahorro' => $ahorroNeto,
        'usuario' => $userEmail // Para confirmar en el frontend
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
exit;
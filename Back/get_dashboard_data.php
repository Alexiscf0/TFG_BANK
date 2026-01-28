<?php
session_start();
header('Content-Type: application/json');

/** @noinspection PhpIncludeInspection */
require_once __DIR__ . '/../vendor/autoload.php';

use MongoDB\Client;
use MongoDB\Database;
use MongoDB\Collection;

if (!isset($_SESSION['user_email'])) {
    echo json_encode(["status" => "error", "message" => "Sesión no iniciada"], JSON_THROW_ON_ERROR);
    exit;
}

try {
    $uri = "mongodb+srv://alexiscastelln_db_user:LOLOKRIKO@cluster0.zfxempk.mongodb.net/?appName=Cluster0";
    $client = new Client($uri);

    /** @var Database $db */
    $db = $client->selectDatabase('KIBO'); // Usamos método explícito en vez de mágico

    /** @var Collection $collection */
    $collection = $db->selectCollection('movimientos');

    $email = $_SESSION['user_email'];

    $cursor = $collection->find(['user_email' => $email]);

    $totalIngresos = 0;
    $totalGastos = 0;
    $expense_categories = [];
    $income_categories = [];
    $expense_trend = array_fill(0, 7, 0);
    $income_trend = array_fill(0, 7, 0);
    $hoy = new DateTime();

    foreach ($cursor as $doc) {
        // Forzamos al editor a entender que $doc es un array o objeto accesible
        $monto = (float)($doc['precio'] ?? 0);
        $tipo = strtolower($doc['tipo'] ?? 'gasto');
        $cat = $doc['categoria'] ?? 'Otros';

        try {
            $fechaMov = new DateTime($doc['fecha'] ?? 'now');
        } catch (Exception $e) {
            $fechaMov = new DateTime();
        }

        $intervalo = $hoy->diff($fechaMov);
        $esReciente = ($intervalo->days < 7);
        $indiceDia = (int)$fechaMov->format('N') - 1;

        if ($tipo === 'ingreso') {
            $totalIngresos += $monto;
            $income_categories[$cat] = ($income_categories[$cat] ?? 0) + $monto;
            if ($esReciente && isset($income_trend[$indiceDia])) $income_trend[$indiceDia] += $monto;
        } else {
            $montoAbs = abs($monto);
            $totalGastos += $montoAbs;
            $expense_categories[$cat] = ($expense_categories[$cat] ?? 0) + $montoAbs;
            if ($esReciente && isset($expense_trend[$indiceDia])) $expense_trend[$indiceDia] += $montoAbs;
        }
    }

    $score = ($totalIngresos > 0) ? round(500 + (($totalIngresos - $totalGastos) / $totalIngresos * 400)) : 500;

    echo json_encode([
        "status" => "success",
        "score" => (int)max(100, min(900, $score)),
        "analysis" => [
            "details" => [
                "expense_categories" => $expense_categories,
                "income_categories" => $income_categories,
                "expense_trend" => $expense_trend,
                "income_trend" => $income_trend
            ]
        ]
    ], JSON_THROW_ON_ERROR);

} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()], JSON_THROW_ON_ERROR);
}
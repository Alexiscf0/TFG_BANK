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
    $client = new Client($uri, [], ["tlsInsecure" => true]);
    $collection = $client->KIBO->datos;

    $email = $_SESSION['user_email'];
    $nuevoUsername = trim($_POST['username'] ?? '');
    $nuevaPass = $_POST['password'] ?? '';

    $updateData = [];
    if (!empty($nuevoUsername)) {
        $updateData['username'] = $nuevoUsername;
    }
    if (!empty($nuevaPass)) {
        $updateData['password'] = password_hash($nuevaPass, PASSWORD_BCRYPT);
    }

    if (empty($updateData)) {
        echo json_encode(["status" => "error", "message" => "No hay datos para actualizar"]);
        exit;
    }

    $resultado = $collection->updateOne(
        ['email' => $email],
        ['$set' => $updateData]
    );

    if ($resultado->getModifiedCount() > 0) {
        if (isset($updateData['username'])) {
            $_SESSION['username'] = $updateData['username'];
        }
        echo json_encode(["status" => "success", "message" => "Perfil actualizado correctamente"]);
    } else {
        echo json_encode(["status" => "info", "message" => "No se realizaron cambios"]);
    }

} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
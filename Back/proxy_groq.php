<?php
session_start();
header('Content-Type: application/json');

// Solo usuarios logueados pueden usar la IA
if (!isset($_SESSION['user_email'])) {
    echo json_encode(["error" => "No autorizado"]);
    exit;
}

// Aquí guardamos la clave de forma privada
$apiKey = "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";
$groqUrl = "https://api.groq.com/openai/v1/chat/completions";

// Leemos lo que envía el frontend (la imagen y el prompt)
$postData = file_get_contents('php://input');

$ch = curl_init($groqUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $apiKey
]);

$response = curl_exec($ch);
curl_close($ch);

echo $response;
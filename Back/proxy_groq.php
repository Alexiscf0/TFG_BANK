<?php
// Back/proxy_groq.php
header('Content-Type: application/json');

// Cargamos la configuración privada
$config = require 'config_privada.php';

// Obtenemos la imagen enviada desde el JS
$input = json_decode(file_get_contents('php://input'), true);
$imagenBase64 = $input['image'] ?? null;

if (!$imagenBase64) {
    echo json_encode(['error' => 'No se ha recibido ninguna imagen']);
    exit;
}

$url = "https://api.groq.com/openai/v1/chat/completions";
$prompt = "Analiza este ticket. Devuelve EXCLUSIVAMENTE un JSON con: \"concepto\" (nombre tienda), \"cantidad\" (número decimal con punto), \"categoria\" (Comida, Ocio, Transporte, Hogar, Otros), \"fecha\" (YYYY-MM-DD).";

$data = [
    "model" => "meta-llama/llama-4-scout-17b-16e-instruct",
    "messages" => [
        [
            "role" => "user",
            "content" => [
                ["type" => "text", "text" => $prompt],
                ["type" => "image_url", "image_url" => ["url" => $imagenBase64]]
            ]
        ]
    ],
    "temperature" => 0.1
];

// Petición segura usando cURL
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $config['groq_key']
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200) {
    http_response_code($httpCode);
}
echo $response;
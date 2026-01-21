<?php
require __DIR__ . '/../vendor/autoload.php';
// Carga todas las librerías instaladas con Composer (MongoDB y JWT).

use MongoDB\Client;
// Importa la clase Client para poder hablar con la base de datos MongoDB.

$client = new Client("mongodb://localhost:27017");
// Crea la conexión física con tu base de datos local.

$collection = $client->KIBO->datos;
// Selecciona la base de datos ("tu_database") y la colección ("usuarios").

$passwordHash = password_hash($password, PASSWORD_BCRYPT);
// LA LÍNEA CLAVE: Toma la contraseña (ej: "1234") y la convierte en un hash
// irreconocible (ej: "$2y$10$abc..."). Bcrypt hace que sea imposible volver atrás.

$resultado = $collection->insertOne([
    "username" => $_POST['username'], // Toma el 'name' del input del HTML
    "password" => $passwordHash,]);
// Inserta el documento en MongoDB con el username y la contraseña ya encriptada.
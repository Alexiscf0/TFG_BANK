<?php
require 'C:/xampp/htdocs/Kibo/vendor/autoload.php';
use MongoDB\Client;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // trim() es fundamental para no guardar espacios invisibles
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        die("Error: Rellena todos los campos.");
    }

    try {
        $client = new Client("mongodb+srv://alexiscastelln_db_user:LOLOKRIKO@cluster0.zfxempk.mongodb.net/?appName=Cluster0");
        $collection = $client->KIBO->datos;

        // Generamos el hash
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);

        $collection->insertOne([
            "email" => $email,
            "password" => $passwordHash,
            "fecha_creacion" => date("Y-m-d H:i:s")
        ]);

        echo "Usuario registrado con éxito. Contraseña procesada con Bcrypt. <a href='../Pages/login.html'>Ir al login</a>";
    } catch (Exception $e) {
        die("Error: " . $e->getMessage());
    }
}
?>
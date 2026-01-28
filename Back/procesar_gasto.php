<?php
require 'C:/xampp/htdocs/Kibo/vendor/autoload.php';
use MongoDB\Client;
session_start();

// 1. Seguridad: Verificar si hay un usuario conectado
if (!isset($_SESSION['user_email'])) {
    die("Error: Debes iniciar sesión para registrar datos. <a href='../Pages/login.html'>Ir al Login</a>");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 2. Capturar datos del formulario
    $emailUsuario = $_SESSION['user_email']; // Identificador del dueño
    $concepto = trim($_POST['concepto']);
    $precio = (float)$_POST['precio'];
    $categoria = $_POST['categoria'];
    $tipo = $_POST['tipo'];

    try {
        // 3. Conexión a tu Atlas (usando tus credenciales)
        $client = new Client("mongodb+srv://alexiscastelln_db_user:LOLOKRIKO@cluster0.zfxempk.mongodb.net/?appName=Cluster0");
        $collection = $client->KIBO->movimientos; // Nueva colección para gastos

        // 4. Insertar documento con la "marca" del usuario
        $resultado = $collection->insertOne([
            "user_email" => $emailUsuario,
            "concepto"   => $concepto,
            "precio"     => $precio,
            "categoria"  => $categoria,
            "tipo"       => $tipo,
            "fecha"      => date("Y-m-d H:i:s")
        ]);

        if ($resultado->getInsertedCount() > 0) {
            header("Location: ../Pages/historial.html?success=1");
            exit();
        }

    } catch (Exception $e) {
        die("Error al guardar en MongoDB: " . $e->getMessage());
    }
}
?>
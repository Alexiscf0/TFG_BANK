<?php
require 'C:/xampp/htdocs/Kibo/vendor/autoload.php';
use MongoDB\Client;
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $emailInput = trim($_POST['email'] ?? '');
    $passInput = $_POST['password'] ?? '';

    try {
        $client = new Client("mongodb+srv://alexiscastelln_db_user:LOLOKRIKO@cluster0.zfxempk.mongodb.net/?appName=Cluster0");
        $collection = $client->KIBO->datos;

        $usuario = $collection->findOne(['email' => $emailInput]);

        if ($usuario) {
            // Verificamos el hash
            if (password_verify($passInput, $usuario['password'])) {
                $_SESSION['user_email'] = $usuario['email'];
                header("Location: ../Pages/index.html");
                exit();
            } else {
                // ESTO TE AYUDARÁ A SABER QUÉ PASA:
                echo "<h3>Error de validación</h3>";
                echo "Contraseña escrita: " . htmlspecialchars($passInput) . "<br>";
                echo "Hash en la Base de Datos: " . $usuario['password'] . "<br>";
                echo "<p>Si el Hash de arriba no empieza por '$2y$10$', el registro está mal hecho.</p>";
                echo "<a href='../Pages/login.html'>Volver</a>";
            }
        } else {
            echo "El usuario no existe. <a href='../Pages/register.html'>Regístrate</a>";
        }
    } catch (Exception $e) {
        die("Error: " . $e->getMessage());
    }
}
?>
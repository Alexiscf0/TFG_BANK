<?php
session_start();

function Prohibido(): void
{

    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        header('Location: ../Back/login.php');
        exit();
    }
}

function esAdmin() {
   if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'){

       return true;
   }
}

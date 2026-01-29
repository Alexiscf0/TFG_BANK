<?php
session_start();
header('Content-Type: application/json');

    'role' => $_SESSION['role'] ?? 'user'

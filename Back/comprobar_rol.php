<?php
session_start();
header('Content-Type: application/json');

$response = [
    'logged_in' => isset($_SESSION['user_email']),
    'role' => $_SESSION['role'] ?? 'user'
];

echo json_encode($response);
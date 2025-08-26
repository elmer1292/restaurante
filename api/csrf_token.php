<?php
// Endpoint para obtener un nuevo token CSRF
require_once __DIR__ . '/../helpers/Csrf.php';
session_start();
header('Content-Type: application/json');
echo json_encode(['csrf_token' => Csrf::getToken()]);

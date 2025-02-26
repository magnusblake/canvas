<?php
session_start();
require_once '../../config/database.php';
require_once '../../src/controllers/AuthController.php';

$database = new Database();
$db = $database->getConnection();
$auth = new AuthController($db);

header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'isAuthenticated' => isset($_SESSION['user_id']),
    'user' => isset($_SESSION['user_id']) ? [
        'id' => $_SESSION['user_id'],
        'username' => $_SESSION['username']
    ] : null
]);

<?php
require_once '../config/database.php';
require_once '../src/controllers/AuthController.php';

$database = new Database();
$db = $database->getConnection();
$auth = new AuthController($db);

header('Content-Type: application/json');
echo json_encode($auth->checkAuth());

<?php
session_start();
header('Content-Type: application/json');

require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'auth_required']);
    exit;
}

$database = new Database();
$db = $database->getConnection();

$userId = $_SESSION['user_id'];

$query = "SELECT design_id FROM hidden_designs WHERE user_id = :user_id";
$stmt = $db->prepare($query);
$stmt->execute([':user_id' => $userId]);
$hiddenDesigns = $stmt->fetchAll(PDO::FETCH_COLUMN);

echo json_encode([
    'success' => true,
    'hidden_designs' => $hiddenDesigns
]);

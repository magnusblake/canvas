<?php
header('Content-Type: application/json');
session_start();

require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'auth_required']);
    exit;
}
$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents('php://input'), true);
$designId = $data['design_id'];
$userId = $_SESSION['user_id'];

try {
    $query = "INSERT INTO hidden_designs (user_id, design_id) VALUES (:user_id, :design_id)";
    $stmt = $db->prepare($query);
    $result = $stmt->execute([
        ':user_id' => $userId,
        ':design_id' => $designId
    ]);

    echo json_encode(['success' => true]);
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}

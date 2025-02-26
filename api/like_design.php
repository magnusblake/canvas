<?php
session_start();
header('Content-Type: application/json');

// Simple session check
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'auth_required']);
    exit;
}
// Rest of the like logic here
require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents('php://input'), true);
$designId = $data['design_id'];
$userId = $_SESSION['user_id'];

try {
    // Check if already liked
    $checkQuery = "SELECT id FROM likes WHERE user_id = ? AND design_id = ?";
    $stmt = $db->prepare($checkQuery);
    $stmt->execute([$userId, $designId]);
    
    if ($stmt->rowCount() > 0) {
        // Unlike
        $query = "DELETE FROM likes WHERE user_id = ? AND design_id = ?";
        $action = 'unliked';
    } else {
        // Like
        $query = "INSERT INTO likes (user_id, design_id) VALUES (?, ?)";
        $action = 'liked';
    }

    $stmt = $db->prepare($query);
    $result = $stmt->execute([$userId, $designId]);

    // Get updated count
    $countQuery = "SELECT COUNT(*) as count FROM likes WHERE design_id = ?";
    $stmt = $db->prepare($countQuery);
    $stmt->execute([$designId]);
    $likesCount = $stmt->fetchColumn();

    echo json_encode([
        'success' => true,
        'action' => $action,
        'likes_count' => $likesCount
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error processing like'
    ]);
}

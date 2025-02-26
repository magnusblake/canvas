<?php
require_once "../config/database.php";
session_start();

header('Content-Type: application/json');

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents('php://input'), true);
$designId = $data['design_id'];
$userId = $_SESSION['user_id'] ?? null;
$userIp = $_SERVER['REMOTE_ADDR'];

// Check if view already exists for this user/IP
$checkQuery = "SELECT id FROM views 
              WHERE design_id = ? 
              AND (user_id = ? OR (user_id IS NULL AND user_ip = ?))";

$stmt = $db->prepare($checkQuery);
$stmt->execute([$designId, $userId, $userIp]);

if (!$stmt->fetch()) {
    // Add new view
    $query = "INSERT INTO views (user_id, design_id, user_ip) VALUES (?, ?, ?)";
    $stmt = $db->prepare($query);
    $success = $stmt->execute([$userId, $designId, $userIp]);
}

// Get updated view count
$viewsQuery = "SELECT COUNT(DISTINCT id) FROM views WHERE design_id = ?";
$stmt = $db->prepare($viewsQuery);
$stmt->execute([$designId]);
$viewsCount = $stmt->fetchColumn();

echo json_encode([
    'success' => true,
    'views_count' => $viewsCount
]);

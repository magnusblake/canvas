<?php
require_once "../config/database.php";
session_start();

header('Content-Type: application/json');

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents('php://input'), true);
$profileId = $data['profile_id'];
$viewerId = $_SESSION['user_id'] ?? null;

// Добавляем просмотр профиля
$query = "INSERT INTO profile_views (profile_id, viewer_id) 
          VALUES (?, ?) 
          ON DUPLICATE KEY UPDATE viewed_at = CURRENT_TIMESTAMP";
$stmt = $db->prepare($query);
$success = $stmt->execute([$profileId, $viewerId]);

// Получаем общее количество просмотров
$viewsQuery = "SELECT COUNT(DISTINCT viewer_id) as views_count 
               FROM profile_views 
               WHERE profile_id = ?";
$stmt = $db->prepare($viewsQuery);
$stmt->execute([$profileId]);
$viewsCount = $stmt->fetchColumn();

echo json_encode([
    'success' => $success,
    'views_count' => $viewsCount
]);

<?php
require_once "../config/database.php";
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Требуется авторизация']);
    exit;
}

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents('php://input'), true);
$followingId = $data['following_id'];
$followerId = $_SESSION['user_id'];

// Проверяем существующую подписку
$checkQuery = "SELECT id FROM subscriptions WHERE follower_id = ? AND following_id = ?";
$stmt = $db->prepare($checkQuery);
$stmt->execute([$followerId, $followingId]);

if ($stmt->rowCount() > 0) {
    // Отписываемся
    $query = "DELETE FROM subscriptions WHERE follower_id = ? AND following_id = ?";
    $action = 'unsubscribed';
} else {
    // Подписываемся
    $query = "INSERT INTO subscriptions (follower_id, following_id) VALUES (?, ?)";
    $action = 'subscribed';
}

$stmt = $db->prepare($query);
$success = $stmt->execute([$followerId, $followingId]);

// Получаем количество подписчиков
$subsQuery = "SELECT COUNT(*) FROM subscriptions WHERE following_id = ?";
$stmt = $db->prepare($subsQuery);
$stmt->execute([$followingId]);
$subsCount = $stmt->fetchColumn();

echo json_encode([
    'success' => $success,
    'action' => $action,
    'subscribers_count' => $subsCount
]);

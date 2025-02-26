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
$designId = $data['design_id'];
$rating = $data['rating'];
$userId = $_SESSION['user_id'];

// Проверяем существующий рейтинг
$checkQuery = "SELECT id FROM ratings WHERE user_id = ? AND design_id = ?";
$stmt = $db->prepare($checkQuery);
$stmt->execute([$userId, $designId]);

if ($stmt->rowCount() > 0) {
    // Обновляем существующий рейтинг
    $query = "UPDATE ratings SET rating = ? WHERE user_id = ? AND design_id = ?";
} else {
    // Добавляем новый рейтинг
    $query = "INSERT INTO ratings (rating, user_id, design_id) VALUES (?, ?, ?)";
}

$stmt = $db->prepare($query);
$success = $stmt->execute([$rating, $userId, $designId]);

// Получаем средний рейтинг
$avgQuery = "SELECT AVG(rating) as avg_rating FROM ratings WHERE design_id = ?";
$stmt = $db->prepare($avgQuery);
$stmt->execute([$designId]);
$avgRating = $stmt->fetch(PDO::FETCH_ASSOC)['avg_rating'];

echo json_encode([
    'success' => $success,
    'average_rating' => round($avgRating, 1)
]);

<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$tutorial_id = $data['tutorial_id'];
$progress = $data['progress'];
$duration = $data['duration'];

$database = new Database();
$db = $database->getConnection();

$query = "INSERT INTO tutorial_progress 
          (user_id, tutorial_id, watched_duration, completed) 
          VALUES (?, ?, ?, ?)
          ON DUPLICATE KEY UPDATE 
          watched_duration = ?, 
          completed = ?,
          last_watched = CURRENT_TIMESTAMP";

$completed = ($progress / $duration) >= 0.9; // Считаем просмотренным если просмотрено 90%

$stmt = $db->prepare($query);
$stmt->execute([
    $_SESSION['user_id'],
    $tutorial_id,
    $progress,
    $completed,
    $progress,
    $completed
]);

echo json_encode(['success' => true]);

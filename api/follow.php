<?php
require_once "../config/database.php";
session_start();

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit('Unauthorized');
}

$database = new Database();
$db = $database->getConnection();

$follower_id = $_SESSION['user_id'];
$following_id = $_POST['user_id'];
$action = $_POST['action'];

if ($action === 'follow') {
    $query = "INSERT INTO followers (follower_id, following_id) VALUES (?, ?)";
} else {
    $query = "DELETE FROM followers WHERE follower_id = ? AND following_id = ?";
}

$stmt = $db->prepare($query);
$result = $stmt->execute([$follower_id, $following_id]);

if ($result) {
    http_response_code(200);
    echo json_encode(['success' => true]);
} else {
    http_response_code(400);
    echo json_encode(['success' => false]);
}

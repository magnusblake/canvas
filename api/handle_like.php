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
$userId = $_SESSION['user_id'];

// Check existing like
$checkQuery = "SELECT id FROM likes WHERE user_id = :user_id AND design_id = :design_id";
$stmt = $db->prepare($checkQuery);
$stmt->execute([
    ':user_id' => $userId,
    ':design_id' => $designId
]);

$exists = $stmt->fetch();

try {
    $db->beginTransaction();

    if ($exists) {
        // Unlike
        $query = "DELETE FROM likes WHERE user_id = :user_id AND design_id = :design_id";
        $action = 'unliked';
    } else {
        // Like
        $query = "INSERT INTO likes (user_id, design_id) VALUES (:user_id, :design_id)";
        $action = 'liked';
    }

    $stmt = $db->prepare($query);
    $success = $stmt->execute([
        ':user_id' => $userId,
        ':design_id' => $designId
    ]);

    // Get updated count
    $countQuery = "SELECT COUNT(*) as count FROM likes WHERE design_id = :design_id";
    $stmt = $db->prepare($countQuery);
    $stmt->execute([':design_id' => $designId]);
    $likesCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

    $db->commit();

    echo json_encode([
        'success' => true,
        'action' => $action,
        'likes_count' => $likesCount,
        'is_liked' => ($action === 'liked')
    ]);

} catch (Exception $e) {
    $db->rollBack();
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

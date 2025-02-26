<?php
header('Content-Type: application/json');
session_start();

require_once "../config/database.php";
$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents('php://input'), true);

if (isset($_SESSION['user_id']) && isset($data['design_id']) && isset($data['text'])) {
    $query = "INSERT INTO design_comments (design_id, user_id, text) VALUES (?, ?, ?)";
    $stmt = $db->prepare($query);
    
    if ($stmt->execute([$data['design_id'], $_SESSION['user_id'], $data['text']])) {
        $commentId = $db->lastInsertId();
        
        // Получаем данные нового комментария
        $getComment = "SELECT 
            dc.*,
            u.username,
            u.avatar as user_avatar
            FROM design_comments dc
            JOIN users u ON dc.user_id = u.id
            WHERE dc.id = ?";
            
        $stmt = $db->prepare($getComment);
        $stmt->execute([$commentId]);
        $comment = $stmt->fetch(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true,
            'comment' => $comment
        ]);
        exit;
    }
}

echo json_encode(['success' => false]);

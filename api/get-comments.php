<?php
header('Content-Type: application/json');
require_once "../config/database.php";

$database = new Database();
$db = $database->getConnection();

if (isset($_GET['design_id'])) {
    $query = "SELECT 
        dc.*,
        u.username,
        u.avatar as user_avatar,
        u.id as user_id
        FROM design_comments dc
        JOIN users u ON dc.user_id = u.id
        WHERE dc.design_id = ?
        ORDER BY dc.created_at DESC";
        
    $stmt = $db->prepare($query);
    $stmt->execute([$_GET['design_id']]);
    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'comments' => $comments
    ]);
    exit;
}

echo json_encode(['success' => false]);

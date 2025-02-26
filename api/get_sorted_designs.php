<?php
require_once "../config/database.php";
header('Content-Type: application/json');

$database = new Database();
$db = $database->getConnection();

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 8;
$offset = ($page - 1) * $limit;

$query = "SELECT 
    d.*, 
    u.username,
    u.avatar as user_avatar,
    COUNT(DISTINCT l.id) as likes_count,
    COUNT(DISTINCT v.id) as views_count
    FROM designs d 
    JOIN users u ON d.user_id = u.id
    LEFT JOIN likes l ON d.id = l.design_id
    LEFT JOIN views v ON d.id = v.design_id
    WHERE d.status = 'approved'
    GROUP BY d.id
    ORDER BY d.created_at DESC 
    LIMIT :offset, :limit";

$stmt = $db->prepare($query);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
$stmt->execute();

echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    
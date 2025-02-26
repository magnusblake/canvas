<?php
header('Content-Type: application/json');

require_once "../config/database.php";
$database = new Database();
$db = $database->getConnection();

if (isset($_GET['id'])) {
    $workId = $_GET['id'];
    
    // Основной запрос с подсчетом лайков и просмотров
    $query = "SELECT 
        d.*,
        u.username as author_name,
        u.avatar as author_avatar,
        (SELECT COUNT(*) FROM likes l WHERE l.design_id = d.id) as likes_count,
        (SELECT COUNT(*) FROM views v WHERE v.design_id = d.id) as views_count
        FROM designs d
        LEFT JOIN users u ON d.user_id = u.id
        WHERE d.id = ?";
    
    $stmt = $db->prepare($query);
    $stmt->execute([$workId]);
    $design = $stmt->fetch(PDO::FETCH_ASSOC);

    // Отдельный запрос для картинок
    $imagesQuery = "SELECT * FROM design_images WHERE design_id = ? ORDER BY is_main DESC, position ASC";
    $stmt = $db->prepare($imagesQuery);
    $stmt->execute([$workId]);
    $design['images'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => $design
    ]);
    exit;
}

echo json_encode(['success' => false]);

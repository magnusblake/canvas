<?php
header('Content-Type: application/json');

require_once "../config/database.php";
$database = new Database();
$db = $database->getConnection();

try {
    $type = $_GET['type'] ?? 'all';
    $category = $_GET['category'] ?? null;
    
    $query = "SELECT 
        d.id, d.title, d.description, d.user_id,
        u.username, u.avatar as user_avatar,
        di.image_path,
        l.likes_count,
        v.views_count
        FROM designs d 
        JOIN users u ON d.user_id = u.id
        LEFT JOIN design_images di ON d.id = di.design_id AND di.is_main = 1
        LEFT JOIN (
            SELECT design_id, COUNT(*) as likes_count 
            FROM likes 
            GROUP BY design_id
        ) l ON l.design_id = d.id
        LEFT JOIN (
            SELECT design_id, COUNT(*) as views_count 
            FROM views 
            GROUP BY design_id
        ) v ON v.design_id = d.id
        WHERE d.status = 'approved'";

    if ($type !== 'all') {
        $query .= " AND d.primary_category_id = :type_id";
    }
    
    if ($category) {
        $query .= " AND d.secondary_category_id = :category";
    }

    $query .= " ORDER BY d.created_at DESC LIMIT :limit OFFSET :offset";

    $stmt = $db->prepare($query);
    
    if ($type !== 'all') {
        $stmt->bindValue(':type_id', ($type === '2d' ? 2 : 1), PDO::PARAM_INT);
    }
    
    if ($category) {
        $stmt->bindValue(':category', $category, PDO::PARAM_INT);
    }
    
    $stmt->bindValue(':limit', 8, PDO::PARAM_INT);
    $stmt->bindValue(':offset', (int)($_GET['offset'] ?? 0), PDO::PARAM_INT);
    
    $stmt->execute();
    $designs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => $designs
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

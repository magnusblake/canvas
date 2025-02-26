<?php
header('Content-Type: application/json');
require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$query = $_GET['query'] ?? '';

if (empty($query)) {
    echo json_encode(['success' => false]);
    exit;
}

try {
    // Поиск работ
    $designsQuery = "SELECT 
        d.id, 
        d.title,
        di.image_path,
        'design' as type
        FROM designs d
        LEFT JOIN design_images di ON d.id = di.design_id AND di.is_main = 1
        WHERE d.title LIKE :query AND d.status = 'approved'
        LIMIT 5";

    // Поиск авторов
    $usersQuery = "SELECT 
        id,
        username,
        avatar,
        'user' as type
        FROM users 
        WHERE username LIKE :query
        LIMIT 5";

    $stmt = $db->prepare($designsQuery);
    $stmt->execute(['query' => "%$query%"]);
    $designs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $db->prepare($usersQuery);
    $stmt->execute(['query' => "%$query%"]);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'results' => [
            'designs' => $designs,
            'users' => $users
        ]
    ]);

} catch (Exception $e) {
    echo json_encode(['success' => false]);
}

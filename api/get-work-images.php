<?php
header('Content-Type: application/json');

require_once "../config/database.php";
$database = new Database();
$db = $database->getConnection();

if (isset($_GET['id'])) {
    $workId = $_GET['id'];
    
    $query = "SELECT * FROM design_images WHERE design_id = ? ORDER BY position ASC";
    $stmt = $db->prepare($query);
    $stmt->execute([$workId]);
    $images = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'images' => $images
    ]);
}

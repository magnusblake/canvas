<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

// Add debug output
$query = "SELECT 
    d.id,
    d.title,
    COALESCE(di.image_path, d.image_path) as image_path
    FROM designs d 
    LEFT JOIN design_images di ON d.id = di.design_id AND di.is_main = 1
    WHERE d.status = 'approved'
    ORDER BY d.created_at DESC";  // Removed LIMIT to see all available works

try {
    $stmt = $db->prepare($query);
    $stmt->execute();
    $works = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Debug output
    error_log("Total works found: " . count($works));
    
    echo json_encode($works);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}

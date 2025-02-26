<?php
require_once "../config/database.php";
session_start();

header('Content-Type: application/json');

$database = new Database();
$db = $database->getConnection();

$userId = $_SESSION['user_id'];
$query = "SELECT d.*, di.image_path 
          FROM designs d 
          LEFT JOIN design_images di ON d.id = di.design_id 
          WHERE d.user_id = ? AND di.is_main = 1
          ORDER BY d.created_at DESC";
$stmt = $db->prepare($query);
$stmt->execute([$userId]);
$designs = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($designs);

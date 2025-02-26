<?php
require_once "config/database.php";
session_start();

$response = ['success' => false];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $database = new Database();
    $db = $database->getConnection();
    
    $title = $_POST['title'];
    $description = $_POST['description'];
    $filters = $_POST['filters'] ?? [];
    
    if (isset($_FILES['image'])) {
        $image = $_FILES['image'];
        $imagePath = time() . '_' . $image['name'];
        
        if (move_uploaded_file($image['tmp_name'], 'uploads/designs/' . $imagePath)) {
            $query = "INSERT INTO designs (user_id, title, description, image_path) VALUES (?, ?, ?, ?)";
            $stmt = $db->prepare($query);
            
            if ($stmt->execute([$_SESSION['user_id'], $title, $description, $imagePath])) {
                $response = [
                    'success' => true,
                    'title' => $title,
                    'image_path' => $imagePath
                ];
            }
        }
    }
}

header('Content-Type: application/json');
echo json_encode($response);

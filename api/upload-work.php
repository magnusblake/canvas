<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0);

require_once "../config/database.php";
session_start();

$database = new Database();
$db = $database->getConnection();

$response = ['success' => false];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $title = $_POST['title'];
        $description = $_POST['description'];
        $mainPhotoIndex = $_POST['main_photo_index'];
        $userId = $_SESSION['user_id'];
        $primaryCategory = $_POST['primary_category'];
        $secondaryCategory = $_POST['secondary_category'];
        
        $mainImageName = uniqid() . '_' . $_FILES['images']['name'][$mainPhotoIndex];

        $stmt = $db->prepare("INSERT INTO designs (user_id, title, description, status, image_path, primary_category_id, secondary_category_id) VALUES (?, ?, ?, 'pending', ?, ?, ?)");
        $stmt->execute([$userId, $title, $description, $mainImageName, $primaryCategory, $secondaryCategory]);
        
        $designId = $db->lastInsertId();
        
        foreach ($_FILES['images']['tmp_name'] as $index => $tmpName) {
            $fileName = uniqid() . '_' . $_FILES['images']['name'][$index];
            $targetPath = '../uploads/designs/' . $fileName;
            
            if (move_uploaded_file($tmpName, $targetPath)) {
                $isMain = ($index == $mainPhotoIndex) ? 1 : 0;
                $stmt = $db->prepare("INSERT INTO design_images (design_id, image_path, position, is_main) VALUES (?, ?, ?, ?)");
                $stmt->execute([$designId, $fileName, $index, $isMain]);
            }
        }
        
        $response['success'] = true;
        
    } catch (Exception $e) {
        $response['error'] = $e->getMessage();
    }
}

echo json_encode($response);

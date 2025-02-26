<?php
require_once '../config/database.php';
session_start();

if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    http_response_code(403);
    echo json_encode(['error' => 'Доступ запрещен']);
    exit;
}

$database = new Database();
$db = $database->getConnection();

$response = ['success' => false];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Обработка видео
    $videoName = time() . '_' . $_FILES['video']['name'];
    $videoPath = '../uploads/tutorials/videos/' . $videoName;
    
    // Обработка превью
    $thumbnailName = time() . '_' . $_FILES['thumbnail']['name'];
    $thumbnailPath = '../uploads/tutorials/thumbnails/' . $thumbnailName;
    
    if (move_uploaded_file($_FILES['video']['tmp_name'], $videoPath) && 
        move_uploaded_file($_FILES['thumbnail']['tmp_name'], $thumbnailPath)) {
        
        $query = "INSERT INTO tutorials (author_id, category_id, title, description, video_path, thumbnail_path, difficulty_level) 
                  VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $db->prepare($query);
        $result = $stmt->execute([
            $_SESSION['user_id'],
            $_POST['category_id'],
            $_POST['title'],
            $_POST['description'],
            $videoName,
            $thumbnailName,
            $_POST['difficulty_level']
        ]);
        
        if ($result) {
            $response = [
                'success' => true,
                'message' => 'Туториал успешно загружен'
            ];
        }
    }
}

header('Content-Type: application/json');
echo json_encode($response);

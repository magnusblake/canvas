<?php
header('Content-Type: application/json');
session_start();

require_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents('php://input'), true);
error_log(print_r($data, true)); // Add this line to see incoming data

// Validate input
if (empty($data['name']) || empty($data['username']) || empty($data['email']) || empty($data['password'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Все поля должны быть заполнены'
    ]);
    exit;
}

// Check if username exists
$stmt = $db->prepare("SELECT id FROM users WHERE username = ?");
$stmt->execute([$data['username']]);
if ($stmt->rowCount() > 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Этот логин уже занят'
    ]);
    exit;
}

// Check if email exists
$stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$data['email']]);
if ($stmt->rowCount() > 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Этот email уже зарегистрирован'
    ]);
    exit;
}

try {
    $password = password_hash($data['password'], PASSWORD_DEFAULT);
    
    $query = "INSERT INTO users (name, username, email, password) VALUES (:name, :username, :email, :password)";
    $stmt = $db->prepare($query);
    
    $result = $stmt->execute([
        ':name' => $data['name'],
        ':username' => $data['username'],
        ':email' => $data['email'],
        ':password' => $password
    ]);

    if ($result) {
        $_SESSION['user_id'] = $db->lastInsertId();
        $_SESSION['username'] = $data['username'];
        
        echo json_encode([
            'success' => true,
            'message' => 'Регистрация успешна'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Ошибка при регистрации'
        ]);
    }

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Ошибка при регистрации'
    ]);
}

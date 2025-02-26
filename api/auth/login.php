<?php
header('Content-Type: application/json');

require_once "../../config/database.php";
$database = new Database();
$db = $database->getConnection();

// Get raw POST data
$data = json_decode(file_get_contents('php://input'), true);

// Get credentials
$email = $data['email'];
$password = $data['password'];

// Get user from database with is_moderator field
$query = "SELECT id, username, email, password, avatar, is_moderator, is_admin FROM users WHERE email = ?";

$stmt = $db->prepare($query);
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && password_verify($password, $user['password'])) {
    if (!isset($_SESSION)) {
        session_start();
    }
    
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['is_admin'] = $user['is_admin'];

    $_SESSION['username'] = $user['username'];
    $_SESSION['avatar'] = $user['avatar'];
    $_SESSION['is_moderator'] = $user['is_moderator'];

    echo json_encode([
        'success' => true,
        'user' => [
            'id' => $user['id'],
            'username' => $user['username'],
            'avatar' => $user['avatar'],
            'is_moderator' => $user['is_moderator']
        ]
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid login credentials'
    ]);
}

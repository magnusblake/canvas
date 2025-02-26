<?php
class AuthController {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function login($data) {
        $email = $data['email'];
        $password = $data['password'];

        $query = "SELECT * FROM users WHERE email = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['avatar'] = $user['avatar'];
            
            return [
                'success' => true,
                'user' => [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'avatar' => $user['avatar']
                ]
            ];
        }

        return ['success' => false, 'message' => 'Invalid email or password'];
    }

    public function checkAuth() {
        if (isset($_SESSION['user_id'])) {
            return [
                'success' => true,
                'user' => [
                    'id' => $_SESSION['user_id'],
                    'username' => $_SESSION['username'],
                    'avatar' => $_SESSION['avatar']
                ]
            ];
        }
        return ['success' => false, 'message' => 'Not authenticated'];
    }
}

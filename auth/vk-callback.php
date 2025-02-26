<?php
require_once "../config/database.php";

if (isset($_GET['code'])) {
    $token_data = file_get_contents("https://oauth.vk.com/access_token?client_id={$vk_config['client_id']}&client_secret={$vk_config['client_secret']}&code={$_GET['code']}&redirect_uri={$vk_config['redirect_uri']}");
    
    $token = json_decode($token_data, true);
    
    $user_data = file_get_contents("https://api.vk.com/method/users.get?user_ids={$token['user_id']}&fields=photo_400_orig&access_token={$token['access_token']}&v=5.131");
    $user = json_decode($user_data, true)['response'][0];
    
    // Download and save avatar
    $avatar_url = $user['photo_400_orig'];
    $avatar_filename = time() . '_vk_avatar.jpg';
    file_put_contents("../uploads/avatars/{$avatar_filename}", file_get_contents($avatar_url));
    
    // Update user record
    $stmt = $db->prepare("UPDATE users SET avatar = ?, vk_id = ? WHERE id = ?");
    $stmt->execute([$avatar_filename, $token['user_id'], $_SESSION['user_id']]);
}

<?php
require_once "../config/database.php";

if (isset($_GET['id'])) {
    $tg_user_id = $_GET['id'];
    $avatar_info = file_get_contents("https://api.telegram.org/bot{$bot_token}/getUserProfilePhotos?user_id={$tg_user_id}");
    $avatar_data = json_decode($avatar_info, true);
    
    if (!empty($avatar_data['result']['photos'])) {
        $file_id = $avatar_data['result']['photos'][0][0]['file_id'];
        $file_path = json_decode(file_get_contents("https://api.telegram.org/bot{$bot_token}/getFile?file_id={$file_id}"), true)['result']['file_path'];
        
        $avatar_filename = time() . '_tg_avatar.jpg';
        file_put_contents("../uploads/avatars/{$avatar_filename}", file_get_contents("https://api.telegram.org/file/bot{$bot_token}/{$file_path}"));
        
        $stmt = $db->prepare("UPDATE users SET avatar = ?, telegram_id = ? WHERE id = ?");
        $stmt->execute([$avatar_filename, $tg_user_id, $_SESSION['user_id']]);
    }
}

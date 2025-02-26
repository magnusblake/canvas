<?php
require_once "../config/database.php";

$bot_token = "YOUR_BOT_TOKEN";
$telegram_config = [
    'bot_username' => 'YOUR_BOT_USERNAME'
];

$auth_url = "https://oauth.telegram.org/auth?bot_id={$bot_token}&origin=https://your-domain.com&return_to=/auth/telegram-callback.php";

header("Location: {$auth_url}");

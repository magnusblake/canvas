<?php
require_once "../config/database.php";

$vk_config = [
    'client_id' => 'YOUR_VK_APP_ID',
    'client_secret' => 'YOUR_VK_SECRET_KEY',
    'redirect_uri' => 'https://your-domain.com/auth/vk-callback.php'
];

$auth_url = "https://oauth.vk.com/authorize?client_id={$vk_config['client_id']}&redirect_uri={$vk_config['redirect_uri']}&response_type=code&scope=photos,email";

header("Location: {$auth_url}");

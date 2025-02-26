function getUsernameWithStyle($username, $role) {
    switch($role) {
        case 'admin':
            return "<span class='username-admin'>{$username}</span>";
        case 'moderator':
            return "<span class='username-moderator'>{$username}</span>";
        default:
            return "<span class='username-user'>{$username}</span>";
    }
}

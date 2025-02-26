<?php
require_once "database.php";

function isUserModerator($userId) {
    $database = new Database();
    $db = $database->getConnection();

    $query = "SELECT is_moderator FROM users WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$userId]);
    return (bool)$stmt->fetchColumn();
}


function isUserAdmin($userId) {
    $database = new Database();
    $db = $database->getConnection();

    $query = "SELECT is_admin FROM users WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$userId]);
    return (bool)$stmt->fetchColumn();
}

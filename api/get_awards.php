<?php
require_once '../config/database.php';
header('Content-Type: application/json');

session_start();
$userId = $_SESSION['user_id'];

$query = "SELECT a.name, a.description, a.icon
          FROM awards a
          JOIN user_awards ua ON a.id = ua.award_id
          WHERE ua.user_id = ?
          ORDER BY ua.awarded_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute([$userId]);
$awards = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($awards);

<?php
require_once "../config/database.php";
header('Content-Type: application/json');

$database = new Database();
$db = $database->getConnection();

$query = "SELECT * FROM filters ORDER BY category, name";
$stmt = $db->prepare($query);
$stmt->execute();
$filters = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($filters);

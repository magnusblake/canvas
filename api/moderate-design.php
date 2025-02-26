<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once "../config/database.php";
require_once "../config/helpers.php";
session_start();

$database = new Database();
$db = $database->getConnection();

$input = file_get_contents('php://input');
$data = json_decode($input, true);

$designId = $data['designId'];
$action = $data['action'];
$status = $action === 'approve' ? 'approved' : 'rejected';

$query = "UPDATE designs SET status = ? WHERE id = ?";
$stmt = $db->prepare($query);
$result = $stmt->execute([$status, $designId]);

$response = [
    'success' => $result,
    'status' => $status,
    'designId' => $designId,
    'query' => $query,
    'input' => $input
];

echo json_encode($response);
  
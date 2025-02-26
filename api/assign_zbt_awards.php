<?php
require_once '../config/database.php';

$startDate = '2025-02-02 00:00:00';
$endDate = '2025-03-30 23:59:59';

$query = "INSERT INTO user_awards (user_id, award_id)
          SELECT u.id, (SELECT id FROM awards WHERE name = 'ЗБТ Участник')
          FROM users u
          WHERE u.created_at BETWEEN ? AND ?
          AND NOT EXISTS (
              SELECT 1 FROM user_awards ua 
              WHERE ua.user_id = u.id 
              AND ua.award_id = (SELECT id FROM awards WHERE name = 'ЗБТ Участник')
          )";

$stmt = $pdo->prepare($query);
$stmt->execute([$startDate, $endDate]);

echo json_encode(['success' => true, 'awards_assigned' => $stmt->rowCount()]);

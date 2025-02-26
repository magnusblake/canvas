<?php
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

function assignAward($userId, $awardId) {
    global $db;
    
    // Let's see what's coming in!
    error_log("Attempting to assign award $awardId to user $userId");
    
    $query = "INSERT INTO user_awards (user_id, award_id, awarded_at) 
              SELECT ?, ?, NOW() 
              WHERE NOT EXISTS (
                  SELECT 1 FROM user_awards 
                  WHERE user_id = ? AND award_id = ?
              )";
              
    $stmt = $db->prepare($query);
    $result = $stmt->execute([$userId, $awardId, $userId, $awardId]);
    
    // Track the result
    error_log("Award assignment result: " . ($result ? "SUCCESS" : "FAILED"));
    
    return $result;
}
function checkAndAssignWorkCountAwards($userId) {
    global $db;
    
    $query = "SELECT COUNT(*) as work_count FROM designs WHERE user_id = ? AND status = 'approved'";
    $stmt = $db->prepare($query);
    $stmt->execute([$userId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $count = $result['work_count'];
    
    // FORCE those awards!
    if($count >= 1) assignAward($userId, 1);
    if($count >= 25) assignAward($userId, 2);
    if($count >= 50) assignAward($userId, 3);
    if($count >= 100) assignAward($userId, 4);
}

function checkAndAssignPlaceAwards($userId, $workId, $place) {
    if($place == 1) assignAward($userId, 5);
    if($place == 2) assignAward($userId, 6);
    if($place == 3) assignAward($userId, 7);
}

function assignVipAward($userId) {
    assignAward($userId, 8);
}

function checkAndAssignCreativeAward($userId) {
    global $db;
    
    // Count likes from the likes table instead
    $query = "SELECT d.id
              FROM designs d
              LEFT JOIN likes l ON d.id = l.design_id
              LEFT JOIN views v ON d.id = v.design_id
              WHERE d.user_id = ?
              GROUP BY d.id
              HAVING COUNT(DISTINCT l.id) > 100 
              AND COUNT(DISTINCT v.id) > 1000";
              
    $stmt = $db->prepare($query);
    $stmt->execute([$userId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if($result) {
        assignAward($userId, 9);
    }
}

function checkAndAssignStarAward($userId) {
    global $db;
    
    // Check if user has high follower count
    $query = "SELECT COUNT(*) as followers_count 
              FROM subscriptions 
              WHERE following_id = ?";
              
    $stmt = $db->prepare($query);
    $stmt->execute([$userId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if($result['followers_count'] >= 1000) {
        assignAward($userId, 10); // Star Award for 1000+ followers
    }
}

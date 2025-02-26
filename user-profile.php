<?php
require_once "config/database.php";
require_once "api/header.php";

$database = new Database();
$db = $database->getConnection();

$profileId = $_GET['id'];

// Получаем данные пользователя
$userQuery = "SELECT id, username, email, bio, created_at, occupation, location, avatar, banner FROM users WHERE id = ?";
$stmt = $db->prepare($userQuery);
$stmt->execute([$profileId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Получаем статистику
$statsQuery = "SELECT 
    (SELECT COUNT(*) FROM followers WHERE following_id = ?) as followers_count,
    (SELECT COUNT(*) FROM designs WHERE user_id = ?) as designs_count,
    (SELECT COUNT(*) FROM likes l JOIN designs d ON l.design_id = d.id WHERE d.user_id = ?) as total_likes";
$stmt = $db->prepare($statsQuery);
$stmt->execute([$profileId, $profileId, $profileId]);
$stats = $stmt->fetch(PDO::FETCH_ASSOC);

// Получаем работы пользователя
$worksQuery = "SELECT d.*, di.image_path,
    (SELECT COUNT(*) FROM likes WHERE design_id = d.id) as likes_count,
    (SELECT COUNT(*) FROM views WHERE design_id = d.id) as views_count
    FROM designs d
    LEFT JOIN design_images di ON d.id = di.design_id AND di.is_main = 1
    WHERE d.user_id = ? AND d.status = 'approved'
    ORDER BY d.created_at DESC";
$stmt = $db->prepare($worksQuery);
$stmt->execute([$profileId]);
$works = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Проверяем подписку
$isSubscribed = false;
if (isset($_SESSION['user_id'])) {
    $subQuery = "SELECT 1 FROM followers WHERE follower_id = ? AND following_id = ?";
    $stmt = $db->prepare($subQuery);
    $stmt->execute([$_SESSION['user_id'], $profileId]);
    $isSubscribed = $stmt->rowCount() > 0;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <link rel="stylesheet" href="css/profile.css" />
</head>
<body>
<div class="screen">
    <div class="content-wrapper">
        <div class="profile-header">
            <div class="profile-banner" style="background-image: url('uploads/banners/<?php echo $user['banner'] ?? 'default-banner.jpg'; ?>')"></div>
            <img class="profile-avatar" src="uploads/avatars/<?php echo $user['avatar'] ?? 'default-avatar.svg'; ?>" alt="Avatar">
                          <div class="profile-name-container">
                              <div class="profile-name"><?php echo htmlspecialchars($user['username']); ?></div>
                              <div class="profile-email"><?php echo htmlspecialchars($user['email']); ?></div>

                          </div>
            
            <div class="profile-stats">
                <div class="stat-item">
                    <div class="stat-value"><?php echo $stats['designs_count']; ?></div>
                    <div class="stat-label">работ</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?php echo $stats['followers_count']; ?></div>
                    <div class="stat-label">подписчиков</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?php echo $stats['total_likes']; ?></div>
                    <div class="stat-label">лайков</div>
                </div>
            </div>
            
            <!-- Add this subscribe button -->

        </div>
                <div class="main-content">
            <div class="left-column">
                <div class="info-section">
                    <div class="info-header" id="infoHeader">
                        <div class="info-title">ОБО МНЕ</div>
                        <div class="vector"></div>
                    </div>
                    <div class="info-content" id="infoContent">
                        <div class="info-row">
                            <div class="info-label">Обо Мне</div>
                            <div class="info-value"><?php echo htmlspecialchars($user['bio'] ?? ''); ?></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Дата регистрации</div>
                            <div class="info-value"><?php echo date('d.m.Y', strtotime($user['created_at'])); ?></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Род деятельности</div>
                            <div class="info-value"><?php echo htmlspecialchars($user['occupation'] ?? ''); ?></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Гео</div>
                            <div class="info-value"><?php echo htmlspecialchars($user['location'] ?? ''); ?></div>
                        </div>
                        

                    </div>
                </div>
                
                <div class="awards-section">
                    <div class="awards-header" id="awardsHeader">
                        <div class="awards-title">НАГРАДЫ</div>
                        <div class="vector"></div>
                    </div>
                    <div class="awards-content" id="awardsContent">
                        <!-- Награды пользователя -->
                    </div>
                </div>
            </div>

            <div class="works-section">
                <div class="works-tabs">
                    <div class="tab active">
                        <span class="tab-text">Работы</span>
                    </div>
                    <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] != $profileId): ?>
                                  <button class="subscribe-btn <?php echo $isSubscribed ? 'subscribed' : ''; ?>" 
                                          data-user-id="<?php echo $profileId; ?>"
                                          onclick="handleSubscribe(this)">
                                      <?php echo $isSubscribed ? 'Отписаться' : 'Подписаться'; ?>
                                  </button>
                              <?php endif; ?>
                </div>
                <div class="works-grid">
                    <?php foreach($works as $work): ?>
                        <div class="work-item" data-id="<?php echo $work['id']; ?>" onclick="workModal.open(<?php echo $work['id']; ?>)">
                            <img src="uploads/designs/<?php echo htmlspecialchars($work['image_path']); ?>" 
                                 alt="<?php echo htmlspecialchars($work['title']); ?>">
                            <div class="work-info">
                                <h3><?php echo htmlspecialchars($work['title']); ?></h3>
                                <div class="work-stats">
                                    <span>👁 <?php echo $work['views_count']; ?></span>
                                    <span>❤️ <?php echo $work['likes_count']; ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'components/work-modal.php'; ?>
<script src="js/user-profile.js"></script>
</body>
</html>

<?php
require_once "config/database.php";
require_once "api/header.php";
require_once "config/helpers.php";
require_once 'api/assign_award.php';

session_start();


$database = new Database();
$db = $database->getConnection();

// Получаем данные пользователя
$query = "SELECT id, username, email, bio, created_at, occupation, location, avatar, banner, is_admin FROM users WHERE id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Add this check for username display
if (empty($user['username'])) {
    $displayName = "Пользователь";
} else {
    $displayName = $user['username'];
}

if (!$user) {
    // Redirect to login if user not found
    header('Location: login.php');
    exit();
}
// Get total views from all user's works
$viewsQuery = "SELECT COUNT(v.id) as total_views 
               FROM views v 
               JOIN designs d ON v.design_id = d.id 
               WHERE d.user_id = ?";
$stmt = $db->prepare($viewsQuery);
$stmt->execute([$_SESSION['user_id']]);
$views = $stmt->fetch(PDO::FETCH_ASSOC)['total_views'];

// Get followers count
$followersQuery = "SELECT COUNT(*) as followers FROM followers WHERE following_id = ?";
$stmt = $db->prepare($followersQuery);
$stmt->execute([$_SESSION['user_id']]);
$followers = $stmt->fetch(PDO::FETCH_ASSOC)['followers'];

// Get total likes count from all user's works
$likesQuery = "SELECT COUNT(l.id) as likes 
               FROM likes l 
               JOIN designs d ON l.design_id = d.id 
               WHERE d.user_id = ?";
$stmt = $db->prepare($likesQuery);
$stmt->execute([$_SESSION['user_id']]);
$likes = $stmt->fetch(PDO::FETCH_ASSOC)['likes'];


// Обновляем SQL запрос для получения работ с главной картинкой
$worksQuery = "SELECT d.*, di.image_path 
               FROM designs d 
               LEFT JOIN design_images di ON d.id = di.design_id 
               WHERE d.user_id = ? AND di.is_main = 1
               ORDER BY d.created_at DESC";
$stmt = $db->prepare($worksQuery);
$stmt->execute([$_SESSION['user_id']]);
$works = $stmt->fetchAll(PDO::FETCH_ASSOC);

// В начале файла добавим запрос для получения фильтров
$filtersQuery = "SELECT * FROM categories ORDER BY type, name";
$stmt = $db->prepare($filtersQuery);
$stmt->execute();
$filters = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
        <!-- Добавляем в начало profile.php -->
        <?php
        if ($_FILES) {
            if (isset($_FILES['avatar'])) {
                $avatar = $_FILES['avatar'];
                $avatarPath = time() . '_' . $avatar['name'];
                move_uploaded_file($avatar['tmp_name'], 'uploads/avatars/' . $avatarPath);
                
                $query = "UPDATE users SET avatar = ? WHERE id = ?";
                $stmt = $db->prepare($query);
                $stmt->execute([$avatarPath, $_SESSION['user_id']]);
            }
            
            if (isset($_FILES['banner'])) {
                $banner = $_FILES['banner'];
                $bannerPath = time() . '_' . $banner['name'];
                move_uploaded_file($banner['tmp_name'], 'uploads/banners/' . $bannerPath);
                
                $query = "UPDATE users SET banner = ? WHERE id = ?";
                $stmt = $db->prepare($query);
                $stmt->execute([$bannerPath, $_SESSION['user_id']]);
            }
        }
        ?>
<div class="profile-banner-1" style="background-image: url('uploads/banners/<?php echo $user['banner'] ?? 'default_banner.svg'; ?>')">
</div>
        <div class="profile-header">
            <div class="profile-banner" style="background-image: url('uploads/banners/<?php echo $user['banner'] ?? 'default-banner.svg'; ?>')"></div>
            <img class="profile-avatar" src="uploads/avatars/<?php echo $user['avatar'] ?? 'default-avatar.svg'; ?>" alt="Аватар">
            
<div class="profile-name-container">
    <div class="profile-name"><?php echo htmlspecialchars($displayName); ?></div>
    <div class="profile-email"><?php echo htmlspecialchars($user['email']); ?></div>
</div>

            
            <div class="profile-stats">
                <div class="stat-item">
                    <div class="stat-value"><?php echo $views; ?></div>
                    <div class="stat-label">просмотров</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?php echo $followers; ?></div>
                    <div class="stat-label">подписчиков</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?php echo $likes; ?></div>
                    <div class="stat-label">лайков</div>
                </div>
            </div>
        </div>
        
        <!-- Остальной контент -->
    </div>
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
                            <div class="info-value"><?php echo htmlspecialchars($user['bio'] ?? 'Расскажите о себе...'); ?></div>
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

                        <a href="edit-profile.php" class="edit-profile-btn">
    Редактировать профиль
</a>
                    </div>
                    <?php if (isUserModerator($_SESSION['user_id'])): ?>
<a href="moderator.php" class="moderator-btn">
    <span class="icon">⚡</span>
    Модерация работ
</a>
<?php endif; ?>
                </div>
                <div class="awards-section">
                    <div class="awards-header" id="awardsHeader">
                        <div class="awards-title">НАГРАДЫ</div>
                        <div class="vector"></div>
                    </div>
                    <div class="awards-content" id="awardsContent">
                        <?php
                        // Get user awards
// In the awards section, modify the query:
$awardsQuery = "SELECT a.* 
                FROM awards a
                JOIN user_awards ua ON a.id = ua.award_id 
                WHERE ua.user_id = ? AND ua.is_visible = 1";

                        $stmt = $db->prepare($awardsQuery);
                        $stmt->execute([$_SESSION['user_id']]);
                        $awards = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        if (!empty($awards)) {
                            foreach ($awards as $award) {
                                echo '<div class="award-wrapper">';
                                echo '<img class="award-item" src="uploads/awards/' . htmlspecialchars($award['icon']) . '" 
                                           alt="' . htmlspecialchars($award['name']) . '"
                                           title="' . htmlspecialchars($award['description']) . '" />';
                                echo '</div>';
                            }
                        } else {
                            echo '<div class="no-awards">У вас пока нет наград</div>';
                        }
                        ?>
                    </div>
                </div>
            </div>
            <div class="works-section">
            <div class="works-tabs">
    <div class="tab active" data-tab="works">
        <span class="tab-text">Работы</span>
    </div>
    <div class="tab" data-tab="following">
        <span class="tab-text">Подписки</span>
    </div>
    <?php if(isUserAdmin($_SESSION['user_id'])): ?>
    <div class="tab" data-tab="tutorials">
        <span class="tab-text">Туториалы</span>
    </div>
    <?php endif; ?>
</div>

<div class="content-sections">
    <div class="works-grid active" data-content="works">
        <!-- контент работ -->
        <div class="create-work" onclick="window.location.href='upload-work.php'">
            <div class="frame-4"></div>
            <span class="create-text">Создать проект</span>
        </div>
        
<?php if ($works): ?>

            <?php foreach ($works as $work): ?>
                <div class="work-item <?php echo $work['status']; ?>" data-id="<?php echo $work['id']; ?>" onclick="workModal.open(<?php echo $work['id']; ?>)">
                    <img src="uploads/designs/<?php echo htmlspecialchars($work['image_path']); ?>" 
                         alt="<?php echo htmlspecialchars($work['title']); ?>">
                    <?php if ($work['status'] === 'pending'): ?>
                        <div class="status-badge pending">На модерации</div>
                    <?php elseif ($work['status'] === 'rejected'): ?>
                        <div class="status-badge rejected">Отклонено</div>
                    <?php endif; ?>
                    <div class="work-info">
                        <h3><?php echo htmlspecialchars($work['title']); ?></h3>
                        <div class="design-modal__stats">
    <div class="design-modal__stat">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
        </svg>
        <span class="design-modal__likes"></span>
    </div>
    <div class="design-modal__stat">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
            <circle cx="12" cy="12" r="3"/>
        </svg>
        <span class="design-modal__views"></span>
    </div>
</div>

                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    
    <div class="following-grid" data-content="following">
        <!-- контент подписок -->
        <?php
        $followingQuery = "SELECT u.* FROM users u 
                          JOIN followers f ON u.id = f.following_id 
                          WHERE f.follower_id = ?";
        $stmt = $db->prepare($followingQuery);
        $stmt->execute([$_SESSION['user_id']]);
        $following = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach($following as $user): ?>
            <div class="user-card">
                <img src="uploads/avatars/<?php echo $user['avatar'] ?? 'default-avatar.svg'; ?>" class="user-avatar">
                <div class="user-info">
                    <h3><?php echo htmlspecialchars($user['username']); ?></h3>
                    <p><?php echo htmlspecialchars($user['occupation'] ?? ''); ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    
    <?php if(isUserAdmin($_SESSION['user_id'])): ?>
    <div class="tutorials-grid" data-content="tutorials">
        <!-- контент туториалов -->
        <div class="create-work" onclick="window.location.href='upload-tutorial.php'">
            <div class="frame-4"></div>
            <span class="create-text">Создать туториал</span>
        </div>
        <?php
        // Получаем туториалы пользователя
        $tutorialsQuery = "SELECT * FROM tutorials WHERE author_id = ?";
        $stmt = $db->prepare($tutorialsQuery);
        $stmt->execute([$_SESSION['user_id']]);
        $tutorials = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach($tutorials as $tutorial): ?>
            <div class="tutorial-item">
                <img src="uploads/tutorials/thumbnails/<?php echo htmlspecialchars($tutorial['thumbnail_path']); ?>" alt="<?php echo htmlspecialchars($tutorial['title']); ?>">
                <div class="tutorial-info">
                    <h3><?php echo htmlspecialchars($tutorial['title']); ?></h3>
                    <p><?php echo htmlspecialchars($tutorial['description']); ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>
        </div>    </div>
</div>
    <script src="js/profile.js"></script>
    <script>
const infoHeader = document.querySelector('.info-header');
const awardsHeader = document.querySelector('.awards-header');

infoHeader.addEventListener('click', function() {
    const content = document.querySelector('.info-content');
    const arrow = this.querySelector('.vector');
    content.classList.toggle('visible');
    arrow.classList.toggle('rotated');
});

awardsHeader.addEventListener('click', function() {
    const content = document.querySelector('.awards-content');
    const arrow = this.querySelector('.vector');
    content.classList.toggle('visible');
    arrow.classList.toggle('rotated');
});
</script>



</body>
</html>
<!-- В нужном месте страницы -->
<?php include 'components/work-modal.php'; ?>

<!-- В head -->
<link rel="stylesheet" href="css/modal.css">

<!-- Перед закрывающим тегом body --><!-- At the top of the file -->
<link rel="stylesheet" href="css/work-modal.css">

<!-- At the bottom of the file, after the modal HTML -->
<script src="js/modal.js"></script>
  <script>
  </script>


<?php
// Add follow button if viewing someone else's profile
if (isset($profileUser) && $profileUser['id'] !== $_SESSION['user_id']) {
    $isFollowing = $db->prepare("SELECT 1 FROM followers WHERE follower_id = ? AND following_id = ?");
    $isFollowing->execute([$_SESSION['user_id'], $profileUser['id']]);
    $following = $isFollowing->fetch() ? true : false;
    ?>
    <button class="follow-btn <?php echo $following ? 'following' : ''; ?>" 
            data-user-id="<?php echo $profileUser['id']; ?>">
        <?php echo $following ? 'Following' : 'Follow'; ?>
    </button>
    <?php
}

// После получения $user
require_once "api/assign_award.php";

// At the point where you want to check awards
checkAndAssignWorkCountAwards($userId);
checkAndAssignCreativeAward($userId);
checkAndAssignStarAward($userId);

// Для ЗБТ награды отдельная проверка
if ($user['created_at'] >= '2025-02-02' && $user['created_at'] <= '2025-03-30') {
    assignAward($_SESSION['user_id'], 1); // 1 - ID награды ЗБТ
}

 if($user['is_admin']): ?>
    <!-- для проверки -->
    <script>console.log('User is admin:', <?php echo json_encode($user); ?>);</script>
<?php endif; ?>

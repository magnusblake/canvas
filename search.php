<?php
require_once 'config/database.php';
include 'api/header.php';

$database = new Database();
$db = $database->getConnection();

$query = $_GET['q'] ?? '';
$userId = $_SESSION['user_id'] ?? null;

$designsQuery = "SELECT 
    d.*, 
    u.username,
    u.avatar as user_avatar,
    di.image_path,
    COUNT(DISTINCT l.id) as likes_count,
    COUNT(DISTINCT v.id) as views_count,
    EXISTS (
        SELECT 1 
        FROM likes ul 
        WHERE ul.design_id = d.id 
        AND ul.user_id = :user_id
    ) as is_liked
    FROM designs d
    JOIN users u ON d.user_id = u.id
    LEFT JOIN design_images di ON d.id = di.design_id AND di.is_main = 1
    LEFT JOIN likes l ON d.id = l.design_id
    LEFT JOIN views v ON d.id = v.design_id
    WHERE (d.title LIKE :query 
    OR d.description LIKE :query)
    AND d.status = 'approved'
    GROUP BY d.id, d.title, d.description, d.user_id, d.created_at, 
             u.username, u.avatar, di.image_path
    ORDER BY d.created_at DESC";
$usersQuery = "SELECT 
    u.*,
    COUNT(DISTINCT d.id) as projects_count,
    COUNT(DISTINCT l.id) as likes_count
    FROM users u
    LEFT JOIN designs d ON u.id = d.user_id
    LEFT JOIN likes l ON d.id = l.design_id
    WHERE u.username LIKE :query
    GROUP BY u.id";

$stmt = $db->prepare($designsQuery);
$stmt->execute([
    'query' => "%$query%",
    'user_id' => $userId
]);
$designs = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $db->prepare($usersQuery);
$stmt->execute(['query' => "%$query%"]);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<link rel="stylesheet" href="css/style.css">

<div class="search-results-page">
    <h1>Результаты поиска: <?php echo htmlspecialchars($query); ?></h1>
    
    <?php if ($users): ?>
    <section class="users-results">
        <h2>Авторы</h2>
        <div class="users-grid">
            <?php foreach($users as $user): ?>
                <div class="user-card">
                    <img src="uploads/avatars/<?php echo $user['avatar']; ?>" alt="<?php echo $user['username']; ?>">
                    <h3><?php echo $user['username']; ?></h3>
                    <div class="user-stats">
                        <span><?php echo $user['projects_count']; ?> проектов</span>
                        <span><?php echo $user['likes_count']; ?> лайков</span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <?php if ($designs): ?>
    <section class="designs-results">
        <h2>Результаты поиска работ</h2>
        <div class="projects-grid">
            <?php foreach($designs as $design): ?>
                <div class="project-card" data-id="<?php echo $design['id']; ?>" data-liked="<?php echo $design['is_liked'] ? 'true' : 'false'; ?>">
                    <div class="project-image">
                        <img src="uploads/designs/<?php echo $design['image_path']; ?>" alt="<?php echo $design['title']; ?>">
                        <div class="project-overlay">
                            <h3 class="project-title"><?php echo $design['title']; ?></h3>
                            <div class="author-info">
                                <img src="uploads/avatars/<?php echo $design['user_avatar']; ?>" class="author-avatar">
                                <span><?php echo $design['username']; ?></span>
                            </div>
                            <div class="action-buttons">
                <button class="action-btn like-btn" data-id="<?php echo $design['id']; ?>">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                    </svg>
                    <span class="like-count"><?php echo $design['likes_count']; ?></span>
                </button>
                <button class="action-btn hide-btn" data-id="<?php echo $design['id']; ?>">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                        <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                        <line x1="1" y1="1" x2="23" y2="23"></line>
                    </svg>
                </button>
            </div>
                        </div>
                    </div>
                    <div class="project-info">
                        <div class="project-info-left">
                            <img src="uploads/avatars/<?php echo $design['user_avatar']; ?>" class="project-info-avatar" alt="<?php echo $design['username']; ?>">
                            <div class="project-info-content">
                                <div class="project-info-title"><?php echo $design['title']; ?></div>
                                <a href="profile.php?id=<?php echo $design['user_id']; ?>" class="author-link">
                                    <?php echo $design['username']; ?>
                                </a>
                                <div class="project-info-stats">
                                    <div class="stat-item">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                            <circle cx="12" cy="12" r="3"></circle>
                                        </svg>
                                        <span><?php echo $design['views_count']; ?></span>
                                    </div>
                                    <div class="stat-item">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                                        </svg>
                                        <span><?php echo $design['likes_count']; ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="project-info">
        <div class="project-info-left">
            <img src="uploads/avatars/<?php echo $design['user_avatar']; ?>" class="project-info-avatar" alt="<?php echo $design['username']; ?>">
            <div class="project-info-content">
                <div class="project-info-title"><?php echo $design['title']; ?></div>
                    <a href="profile.php?id=<?php echo $design['user_id']; ?>" class="author-link">
                    <?php echo $design['username']; ?>
                </a>
                <div class="project-info-stats">
                    <div class="stat-item">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                        <span><?php echo $design['views_count']; ?></span>
                    </div>
                    <div class="stat-item">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                        </svg>
                        <span><?php echo $design['likes_count']; ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </section>
    <?php endif; ?>
</div>

<script src="js/main.js"></script>
<script src="js/modal.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    new ProjectsManager();
});
</script>

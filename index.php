<?php 
require_once 'config/database.php';
include 'api/header.php';

$database = new Database();
$db = $database->getConnection();

// Get top-10 projects
$topQuery = "SELECT 
    d.*, 
    u.username,
    di.image_path,
    COUNT(DISTINCT l.id) as likes_count,
    COUNT(DISTINCT v.id) as views_count
    FROM designs d 
    JOIN users u ON d.user_id = u.id
    LEFT JOIN design_images di ON d.id = di.design_id AND di.is_main = 1
    LEFT JOIN likes l ON d.id = l.design_id
    LEFT JOIN views v ON d.id = v.design_id";

// Add hidden designs filter for logged in users
if (isset($_SESSION['user_id'])) {
    $topQuery .= " LEFT JOIN hidden_designs hd ON d.id = hd.design_id AND hd.user_id = :user_id
    WHERE d.status = 'approved' AND hd.id IS NULL";
} else {
    $topQuery .= " WHERE d.status = 'approved'";
}

$topQuery .= " GROUP BY d.id, u.username, di.image_path
    ORDER BY likes_count DESC 
    LIMIT 10";

$stmt = $db->prepare($topQuery);

// Execute with or without user_id parameter
if (isset($_SESSION['user_id'])) {
    $stmt->execute([':user_id' => $_SESSION['user_id']]);
} else {
    $stmt->execute();
}

$topDesigns = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Get new projects query
// Get new projects query
$newProjectsQuery = "SELECT 
    d.*, 
    u.username,
    u.avatar as user_avatar,
    c.name as category_name,
    di.image_path,
    COUNT(DISTINCT l.id) as likes_count,
    COUNT(DISTINCT v.id) as views_count
    FROM designs d 
    JOIN users u ON d.user_id = u.id
    LEFT JOIN categories c ON d.category_id = c.id
    LEFT JOIN design_images di ON d.id = di.design_id AND di.is_main = 1
    LEFT JOIN likes l ON d.id = l.design_id
    LEFT JOIN views v ON d.id = v.design_id
    WHERE d.status = 'approved'
    GROUP BY d.id, d.title, d.description, d.created_at, 
             u.username, u.avatar, c.name, di.image_path
    ORDER BY d.created_at DESC";

$stmt = $db->prepare($newProjectsQuery);
$stmt->execute(); // Remove the parameter since we're not using user_id
$designs = $stmt->fetchAll(PDO::FETCH_ASSOC);




// Get categories with project counts
$categoriesQuery = "SELECT 
    c.*, 
    COUNT(d.id) as projects_count 
    FROM categories c
    LEFT JOIN designs d ON c.id = d.category_id
    GROUP BY c.id
    ORDER BY projects_count DESC";

// Get top authors
$topAuthorsQuery = "SELECT 
    u.*,
    COUNT(DISTINCT d.id) as projects_count,
    COUNT(DISTINCT l.id) as total_likes,
    COUNT(DISTINCT f.follower_id) as followers_count
    FROM users u
    LEFT JOIN designs d ON u.id = d.user_id
    LEFT JOIN likes l ON d.id = l.design_id
    LEFT JOIN subscriptions f ON u.id = f.following_id
    GROUP BY u.id
    ORDER BY total_likes DESC
    LIMIT 6";

$stmt = $db->prepare($newProjectsQuery);
$stmt->execute(); // Remove any parameters since the query doesn't use them
$newProjects = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get categories
$stmt = $db->prepare($categoriesQuery);
$stmt->execute(); // This also doesn't need parameters
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $db->prepare($topAuthorsQuery);
$stmt->execute();
$topAuthors = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <link rel="stylesheet" href="/css/style.css" />
    <link rel="stylesheet" href="/css/modal.css" />
</head>
<body>

<section class="today-works">
    <div class="platforms-container">
        <div class="title-platform">
            <h1>C<span class="accent">A</span>NV<span class="accent">A</span>S</h1>
        </div>
        <!-- Платформы с работами будут добавляться через JS -->
    </div>
</section>




        <!-- Hero секция -->
        <section class="hero">
            <div class="hero-gradient"></div>
        </section>
          <!-- Топ проекты -->
          <section class="top-projects">
              <h2 class="section-title">Лучшие проекты за все время</h2>
              <div class="frame-5">
                  <div class="frame-13 prev">
                      <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                          <path d="M9 18L15 12L9 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                      </svg>
                  </div>
                  <div class="frame-6">
                      <div class="overlap-group">
                          <?php 
                          // Получаем главное изображение для топового проекта
                          $mainImageQuery = "SELECT di.image_path 
                                            FROM design_images di 
                                            WHERE di.design_id = ? AND di.is_main = 1 
                                            LIMIT 1";
                          $stmt = $db->prepare($mainImageQuery);
                          $stmt->execute([$topDesigns[0]['id']]);
                          $mainImage = $stmt->fetch(PDO::FETCH_ASSOC);
                          ?>
                          <img src="uploads/designs/<?php echo $mainImage['image_path']; ?>" alt="<?php echo $topDesigns[0]['title']; ?>">
                          <div class="frame-7">
                              <div class="text-wrapper-2">#1</div>
                          </div>
                      </div>

                      <div class="text-wrapper-4"><?php echo $topDesigns[0]['title']; ?></div>
                      <div class="text-wrapper-5">Автор - <?php echo $topDesigns[0]['username']; ?></div>
                  </div>
                  <?php for($i = 1; $i < count($topDesigns); $i++): ?>
                      <div class="project-card" data-id="<?php echo $topDesigns[$i]['id']; ?>">
                          <div class="project-image">
                              <?php
                              // Получаем главное изображение для каждого проекта
                              $stmt->execute([$topDesigns[$i]['id']]);
                              $projectImage = $stmt->fetch(PDO::FETCH_ASSOC);
                              ?>
                              <img src="uploads/designs/<?php echo $projectImage['image_path']; ?>" alt="<?php echo $topDesigns[$i]['title']; ?>">
                              <!-- Остальной код -->
                          </div>
                      </div>
                  <?php endfor; ?>
                  <div class="frame-13 next">
                      <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                          <path d="M9 18L15 12L9 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                      </svg>
                  </div>
                  <div class="project-stats">
                          <div class="frame-8">
                              <div class="text-wrapper-3"><?php echo $topDesigns[0]['views_count']; ?></div>
                              <img class="img" src="img/frame-695-5.svg" />
                          </div>
                          <div class="frame-9">
                              <div class="text-wrapper-3"><?php echo $topDesigns[0]['likes_count']; ?></div>
                              <img class="img" src="img/image.svg" />
                          </div>
                      </div>
              </div>
          </section>
        <!-- Секция с новыми проектами -->
        <section class="new-projects">
            <div class="container">
                <div class="section-header">
                    <h2>Новые проекты</h2>
                    <?php
                    // Get categories for filters from database
                    $categoriesQuery = "SELECT * FROM categories ORDER BY name ASC";
                    $stmt = $db->prepare($categoriesQuery);
                    $stmt->execute();
                    $filterCategories = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    ?>
                      <div class="filters-section">
                          <div class="filters-header">
                          <div class="primary-filters">
    <button class="filter-btn active" data-type="all">Все</button>
    <?php
    // Get primary type filters (3D, 2D)
    $primaryQuery = "SELECT * FROM categories WHERE type = 'primary'";
    $stmt = $db->prepare($primaryQuery);
    $stmt->execute();
    $primaryFilters = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach($primaryFilters as $filter): ?>
        <button class="filter-btn" data-type="<?php echo strtolower($filter['name']); ?>">
            <?php echo $filter['name']; ?>
        </button>
    <?php endforeach; ?>
</div>
        
                              <button class="filters-toggle-btn">
                                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                      <path d="M3 6h18M6 12h12M9 18h6"/>
                                  </svg>
                                  Категории
                              </button>
                          </div>
    
                          <div class="filters-dropdown">
                              <div class="filters-dropdown-header">
                                  <h3>Категории</h3>
                                  <button class="reset-filters-btn">
                                      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                          <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                                      </svg>
                                      Сбросить
                                  </button>
                              </div>
                              <div class="filter-group">
                                  <?php
                                  $categoryQuery = "SELECT * FROM categories WHERE type = 'secondary' ORDER BY name";
                                  $stmt = $db->prepare($categoryQuery);
                                  $stmt->execute();
                                  $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
                                  foreach($categories as $category): ?>
                                      <button class="filter-btn" data-category="<?php echo $category['id']; ?>">
                                          <?php echo $category['name']; ?>
                                      </button>
                                  <?php endforeach; ?>
                              </div>
                          </div>
                      </div>
                </div>                <div class="projects-grid">
                    <?php if (!empty($designs)): ?>
                        <?php foreach($designs as $design): ?>
                            <div class="project-card" data-id="<?php echo $design['id']; ?>">
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
                                                <a href="user-profile.php?id=<?php echo $design['user_id']; ?>" class="author-link">
                                                <?php echo $design['username']; ?>
                                            </a>
                                            <div class="project-info-stats">
                                                <div class="stat-item">
                                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" color = "black">
                                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                        <circle cx="12" cy="12" r="3"></circle>
                                                    </svg>
                                                    <span><?php echo $design['views_count']; ?></span>
                                                </div>
                                                <div class="stat-item">
                                                    <svg width="16" height="16" viewBox="0 0 24 24" fill = "red" stroke="currentColor" color = "red">
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
                    <?php else: ?>
                        <div class="no-projects">
                            <h3>Проекты не найдены</h3>
                        </div>
                    <?php endif; ?>
                </div>
                <button class="load-more-btn">Показать еще</button>
            </div>
        </section>
    </main>
    <?php include 'components/work-modal.php'; ?>
    
<!-- Order matters! -->
<script src="js/auth-modal.js"></script>
<script src="js/modal.js"></script>
<script src="js/main.js"></script>
<script src="js/wave-animation.js"></script>
<script src="js/slider.js"></script>

</body>
</html>
<div class="auth-modal">
    <div class="modal-content">
        <div class="modal-close">
            <img src="src/default_img/Vector.svg" alt="Close">
        </div>

        <div class="modal-body">
    <img src="src/default_img/logo.svg" alt="Logo" class="modal-logo">
    <h2 class="modal-title">Вход в систему</h2>
    
    <div class="social-buttons">
    <div class="social-btn">
        <img src="src/default_img/yandex-icon.png" alt="Yandex" width="24" height="24">
    </div>
    <div class="social-btn">
        <img src="src/default_img/vk-icon.png" alt="VK" width="24" height="24">
    </div>
    <div class="social-btn">
        <img src="src/default_img/telegram-icon.png" alt="Telegram" width="24" height="24">
    </div>
</div>
              <div class="tab-content login active">
                  <form id="loginForm">
                      <div class="input-group">
                          <label>Email</label>
                          <input type="email" name="email" placeholder="">
                      </div>
                      <div class="input-group">
                          <label>Пароль</label>
                          <input type="password" name="password" placeholder="">
                      </div>
                      <a href="#" class="forgot-link">Забыли пароль?</a>
                      <button type="submit" class="submit-btn">Войти</button>
                      <a href="#" class="switch-auth" data-type="register">Еще нет аккаунта? Зарегистрироваться</a>
                  </form>
              </div>
            <div class="tab-content register">
    <form id="registerForm">
        <div class="input-row">
            <div class="input-group">
                <label>Имя</label>
                <input type="text" name="name" placeholder="">
            </div>
            <div class="input-group">
                <label>Логин</label>
                <input type="text" name="username" placeholder="">
            </div>
        </div>
        <div class="input-group">
            <label>Email</label>
            <input type="email" name="email" placeholder="">
        </div>
        <div class="input-group">
            <label>Пароль</label>
            <input type="password" name="password" placeholder="">
        </div>
        <div class="input-group">
            <label>Подтверждение пароля</label>
            <input type="password" name="password_confirm" placeholder="">
        </div>
        <button type="submit" class="submit-btn">Зарегистрироваться</button>
    </form>
</div>

        </div>
    </div>
</div>


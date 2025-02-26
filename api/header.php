<?php
session_start();

// Если юзер залогинен - тащим его свежие данные из базы
if (isset($_SESSION['user_id'])) {
    require_once "config/database.php";
    $database = new Database();
    $db = $database->getConnection();
    
    $query = "SELECT avatar FROM users WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$_SESSION['user_id']]);
    $userData = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $_SESSION['avatar'] = $userData['avatar'];
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/header.css">
</head>
<body data-logged-in="<?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>">
    <header class="main-header">
        <div class="header-container">
            <div class="left-section">
            <div class="burger-menu-icon">
    <img src="src/default_img/logo.svg" alt="Menu">
</div>
  <div class="burger-menu">
      <nav class="menu-nav">
          <ul>
              <li><a href="#top">Главная</a></li>
              <li><a href="#new-projects">Дизайны</a></li>
              <li><a href="#top-projects">Топ проекты</a></li>
              <li><a href="#authors">Авторы</a></li>
              <li><a href="#categories">Категории</a></li>
          </ul>
      </nav>
  </div>
                <a href="index.php" class="logo">C<span class="accent">A</span>NV<span class="accent">A</span>S</a>
            </div>
            <nav class="main-nav">
    <div class="nav-group">
        <div class="nav-item dropdown">
            <button class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                Обзор
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="tutorials.php">Туториалы</a></li>
                <!-- <li><a class="dropdown-item" href="/popular">Популярное</a></li>
                <li><a class="dropdown-item" href="/recommended">Для вас</a></li> -->
            </ul>
        </div>
        <div class="nav-item dropdown">
            <button class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                Услуги
            </button>
           <!--  <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="/services/design">Дизайн</a></li>
                <li><a class="dropdown-item" href="/services/development">Разработка</a></li>
                <li><a class="dropdown-item" href="/services/marketing">Маркетинг</a></li> 
            </ul>-->
        </div>
        <div class="nav-item dropdown">
            <button class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                Блог
            </button>
          <!--  <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="/blog/articles">Статьи</a></li>
                <li><a class="dropdown-item" href="/blog/news">Новости</a></li>
                <li><a class="dropdown-item" href="/blog/guides">Руководства</a></li> 
            </ul>-->
        </div>
    </div>
</nav>
                          <div class="center-section">
                              <div class="search-bar">
                                  <input type="text" id="searchInput" placeholder="Поиск">
                                  <div class="search-results"></div>
                                  <button class="search-button">
                                      <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                                          <path d="M15.5 14H14.71L14.43 13.73C15.41 12.59 16 11.11 16 9.5C16 5.91 13.09 3 9.5 3C5.91 3 3 5.91 3 9.5C3 13.09 5.91 16 9.5 16C11.11 16 12.59 15.41 13.73 14.43L14 14.71V15.5L19 20.49L20.49 19L15.5 14ZM9.5 14C7.01 14 5 11.99 5 9.5C5 7.01 7.01 5 9.5 5C11.99 5 14 7.01 14 9.5C14 11.99 11.99 14 9.5 14Z" fill="currentColor"/>
                                      </svg>
                                  </button>
                              </div>
            </div>
                          <div class="right-section">
                              <div class="upgrade-button">
                                  <button>Повышение прав</button>
                              </div>
                              <?php if (isset($_SESSION['user_id'])): ?>
    <div class="user-dropdown">
    <button class="user-menu-btn" type="button" data-bs-toggle="dropdown">
    <?php 
    $avatar = isset($_SESSION['avatar']) && !empty($_SESSION['avatar']) 
        ? $_SESSION['avatar'] 
        : 'default-avatar.svg';
    ?>
    <img src="uploads/avatars/<?php echo $avatar; ?>" class="user-avatar-1" alt="User avatar">
    <span class="username"><?php echo $_SESSION['username']; ?></span>
</button>

        <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="profile.php">Профиль</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="#" id="logoutBtn">Выйти</a></li>
        </ul>
    </div>
<?php else: ?>
    <button class="login-btn">Войти</button>
<?php endif; ?>
                          </div>
                      </div>
    </header>

        

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/header.js"></script>
    
<!-- Add this JavaScript at the bottom of header.php -->
<script>
document.getElementById('logoutBtn').addEventListener('click', function(e) {
    e.preventDefault();
    window.location.href = '/src/handlers/logout.php';
});
</script>

</body>
</html>

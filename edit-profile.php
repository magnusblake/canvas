<?php
session_start();
require_once "config/database.php";
require_once "config/helpers.php";

// Check authentication first
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$database = new Database();
$db = $database->getConnection();

// Process form submission if POST data exists
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updateFields = [];
    $params = [];
    
    $fields = ['username', 'email', 'location', 'occupation', 'bio'];
    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            $updateFields[] = "$field = ?";
            $params[] = $_POST[$field];
        }
    }
    
    if (!empty($_FILES['avatar']['name'])) {
        $uploadDir = 'uploads/avatars/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $fileName = time() . '_' . $_FILES['avatar']['name'];
        $targetFile = $uploadDir . $fileName;
        
        if (move_uploaded_file($_FILES['avatar']['tmp_name'], $targetFile)) {
            $updateFields[] = "avatar = ?";
            $params[] = $fileName;
            echo "Файл успешно загружен";
        } else {
            echo "Ошибка загрузки файла";
        }
    }

    if (!empty($_FILES['banner']['name'])) {
        $uploadDir = 'uploads/banners/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $fileName = time() . '_' . $_FILES['banner']['name'];
        $targetFile = $uploadDir . $fileName;
        
        if (move_uploaded_file($_FILES['banner']['tmp_name'], $targetFile)) {
            $updateFields[] = "banner = ?";
            $params[] = $fileName;
        }
    }
    $params[] = $_SESSION['user_id'];
    
    $sql = "UPDATE users SET " . implode(', ', $updateFields) . " WHERE id = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    
    // Update awards visibility
    $visibleAwards = $_POST['visible_awards'] ?? [];
    
    // First set all user awards to invisible
    $stmt = $db->prepare("UPDATE user_awards SET is_visible = 0 WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    
    // Then set selected awards to visible
    if (!empty($visibleAwards)) {
        $placeholders = str_repeat('?,', count($visibleAwards) - 1) . '?';
        $stmt = $db->prepare("UPDATE user_awards SET is_visible = 1 
                             WHERE user_id = ? AND award_id IN ($placeholders)");
        $params = array_merge([$_SESSION['user_id']], $visibleAwards);
        $stmt->execute($params);
    }
    
    header('Location: profile.php');
    exit;
}

// After all potential redirects, include the header
require_once "api/header.php";

// Get user data
$query = "SELECT username, email, location, occupation, bio, avatar, banner FROM users WHERE id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Rest of your HTML and form code
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <link rel="stylesheet" href="/css/edit-profile.css" />
  </head>
  <body>
    <div class="screen">
      <div class="container">
      <!-- Боковая навигация -->
      <div class="sidebar">
          <a href="#main" class="nav-item active">Основные</a>
          <a href="#connections" class="nav-item">Привязки</a>
          <a href="#awards" class="nav-item">Награды</a>
          <a href="#about" class="nav-item">Обо мне</a>
          <a href="#links" class="nav-item">Ссылки</a>
      </div>
      
        <!-- Основной контент -->
        <form method="POST" enctype="multipart/form-data" class="main-content">
          <h1 class="page-title">Редактирование профиля</h1>
          
      <!-- Загрузка фото -->
      <div class="upload-section">
          <!-- Добавляем скрытые input для файлов -->
          <input type="file" id="banner" name="banner" accept="image/*" style="display: none;">
          <input type="file" id="avatar" name="avatar" accept="image/*" style="display: none;">


          <!-- Кнопки для загрузки -->
   

          <div class="banner-upload">
              <img src="uploads/banners/<?php echo $user['banner'] ?? 'default-banner.svg'; ?>" alt="Banner">
          </div>
          <div class="avatar-upload">
              <img src="uploads/avatars/<?php echo $user['avatar'] ?? 'default-avatar.svg'; ?>" alt="Avatar">
          </div>
          <p class="upload-hint">Загрузите аватар и обложку профиля</p>
      </div>          <!-- Поля ввода -->
          <div id="main" class="input-section">
            <h2>Основная информация</h2>
            
            <div class="input-group">
              <label>Имя</label>
              <input type="text" name="username" value="<?php echo htmlspecialchars($user['username'] ?? ''); ?>">
            </div>

            <div class="input-group">
              <label>Род деятельности</label>
              <input type="text" name="occupation" value="<?php echo htmlspecialchars($user['occupation'] ?? ''); ?>">
            </div>

            <div class="input-group">
              <label>Гео</label>
              <input type="text" name="location" value="<?php echo htmlspecialchars($user['location'] ?? ''); ?>">
            </div>
          </div>

          <!-- Привязки -->
          <div id="connections" class="input-section">
    <h2>Привязки</h2>
    <div class="social-buttons">
        <a href="/auth/vk-auth.php" class="social-btn vk">
            <img src="/images/vk-icon.svg" alt="VK">
            <span>Привязать VK</span>
        </a>
        
        <a href="/auth/telegram-auth.php" class="social-btn telegram">
            <img src="/images/telegram-icon.svg" alt="Telegram">
            <span>Привязать Telegram</span>
        </a>
    </div>
</div>
            <!-- Награды -->
            <div id="awards" class="input-section">
      <h2>Награды</h2>
      <div class="awards-grid">
          <?php
          // Get user awards with visibility status
          $awardsQuery = "SELECT a.*, COALESCE(ua.is_visible, 1) as is_visible 
                       FROM awards a
                       JOIN user_awards ua ON a.id = ua.award_id 
                       WHERE ua.user_id = ?";
          $stmt = $db->prepare($awardsQuery);
          $stmt->execute([$_SESSION['user_id']]);
          $awards = $stmt->fetchAll(PDO::FETCH_ASSOC);

          foreach ($awards as $award) {
              echo '<div class="award-item">
                      <img src="uploads/awards/' . htmlspecialchars($award['icon']) . '" alt="' . htmlspecialchars($award['name']) . '">
                      <span>' . htmlspecialchars($award['name']) . '</span>
                      <label class="award-visibility">
                          <input type="checkbox" name="visible_awards[]" 
                               value="' . $award['id'] . '" 
                               ' . ($award['is_visible'] ? 'checked' : '') . '>
                          <i class="visibility-icon"></i>
                      </label>
                  </div>';
          }
          ?>
      </div>
  </div>
          <!-- Обо мне -->
          <div id="about" class="input-section">
              <h2>Обо мне</h2>
              <div class="input-group">
                  <label>Биография</label>
                  <textarea name="bio" rows="4"><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
              </div>
          </div>

          <!-- Ссылки -->
          <div id="links" class="input-section">
              <h2>Ссылки</h2>
              <div class="input-group">
                  <label>Behance</label>
                  <input type="text" name="behance" value="<?php echo htmlspecialchars($user['behance'] ?? ''); ?>">
              </div>
              <div class="input-group">
                  <label>Dribbble</label>
                  <input type="text" name="dribbble" value="<?php echo htmlspecialchars($user['dribbble'] ?? ''); ?>">
              </div>
                </div>
                <button type="submit" class="save-button">Сохранить изменения</button>
                <a href="profile.php" class="back-button">Назад</a>

        </form>
      </div>
    </div>
  </body>
</html>
    <script src="js/edit-profile.js"></script>
        
        
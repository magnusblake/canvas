<?php
require_once 'config/database.php';
include 'api/header.php';

if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: index.php');
    exit;
}

$database = new Database();
$db = $database->getConnection();

// Получаем категории для выпадающего списка
$categoryQuery = "SELECT * FROM tutorial_categories ORDER BY name";
$stmt = $db->prepare($categoryQuery);
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="css/upload-tutorial.css">
</head>
<body>
    <div class="tutorial-upload-container">
        <h1 class="tutorial-title">Загрузка туториала</h1>
        
        <form id="tutorialForm" enctype="multipart/form-data">
            <div class="tutorial-form-group">
                <label class="tutorial-label">Название туториала</label>
                <input type="text" name="title" class="tutorial-input" required>
            </div>

            <div class="tutorial-form-group">
                <label class="tutorial-label">Описание</label>
                <textarea name="description" class="tutorial-textarea" required></textarea>
            </div>

            <div class="tutorial-form-row">
                <div class="tutorial-form-group">
                    <label class="tutorial-label">Категория</label>
                    <select name="category_id" class="tutorial-select" required>
                        <?php foreach($categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>">
                                <?php echo $category['name']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="tutorial-form-group">
                    <label class="tutorial-label">Уровень сложности</label>
                    <select name="difficulty_level" class="tutorial-select" required>
                        <option value="beginner">Начинающий</option>
                        <option value="intermediate">Средний</option>
                        <option value="expert">Эксперт</option>
                    </select>
                </div>
            </div>

            <div class="tutorial-form-group">
                <label class="tutorial-label">Видео туториала</label>
                <input type="file" name="video" class="tutorial-file-input" accept="video/*" required>
            </div>

            <div class="tutorial-form-group">
                <label class="tutorial-label">Превью (thumbnail)</label>
                <input type="file" name="thumbnail" class="tutorial-file-input" accept="image/*" required>
                <div class="tutorial-thumbnail-preview"></div>
            </div>

            <button type="submit" class="tutorial-submit-btn">Загрузить туториал</button>
        </form>
    </div>

    <script src="js/upload-tutorial.js"></script>
</body>
</html>


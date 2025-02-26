<?php
require_once "config/database.php";
require_once "api/header.php";

session_start();

$database = new Database();
$db = $database->getConnection();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Загрузка работы</title>
    <link rel="stylesheet" href="css/upload-work.css">
</head>
<body>


    <div class="upload-container">
    <a href="profile.php" class="back-button">Назад</a>

        <div class="upload-content">
            <div class="left-column">
                <div class="upload-zone" id="uploadZone">
                    <input type="file" id="imageInput" multiple accept="image/*" hidden>
                    <div class="upload-placeholder">
                        <span>Загрузить изображения</span>
                    </div>
                    <div class="preview-grid" id="previewGrid"></div>
                    <button type="button" id="addMoreBtn" class="add-more-btn" hidden>+ Добавить еще</button>
                </div>

                <div class="layout-templates">
                    <h3>Выберите шаблон отображения</h3>
                    <div class="templates-grid">
                        <div class="template" data-images="2">
                            <div class="template-preview">
                                <div class="preview-block"></div>
                                <div class="preview-block"></div>
                            </div>
                            <span>2 фото</span>
                        </div>
                        <div class="template" data-images="3">
                            <div class="template-preview">
                                <div class="preview-block"></div>
                                <div class="preview-block"></div>
                                <div class="preview-block"></div>
                            </div>
                            <span>3 фото</span>
                        </div>
                        <div class="template" data-images="4">
                            <div class="template-preview">
                                <div class="preview-block"></div>
                                <div class="preview-block"></div>
                                <div class="preview-block"></div>
                                <div class="preview-block"></div>
                            </div>
                            <span>4 фото</span>
                        </div>
                        <div class="template" data-images="5">
                            <div class="template-preview">
                                <div class="preview-block"></div>
                                <div class="preview-block"></div>
                                <div class="preview-block"></div>
                                <div class="preview-block"></div>
                                <div class="preview-block"></div>
                            </div>
                            <span>5 фото</span>
                        </div>
                    </div>
                </div>
            </div>
              <form id="uploadForm" class="upload-form">
                  <div class="form-group">
                      <label for="title">Название работы</label>
                      <input type="text" id="title" name="title" placeholder="Введите название" required>
                  </div>
                
                  <div class="form-group">
                      <label for="description">Описание работы</label>
                      <textarea id="description" name="description" placeholder="Расскажите о вашей работе" rows="6"></textarea>
                  </div>
                
                  <div class="form-group">
                      <label for="primary_category">Основная категория</label>
                      <select id="primary_category" name="primary_category" required>
                          <option value="">Выберите основную категорию</option>
                          <?php
                          $primaryQuery = "SELECT * FROM categories WHERE type = 'primary' ORDER BY name";
                          $stmt = $db->prepare($primaryQuery);
                          $stmt->execute();
                          $primaryCategories = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        
                          foreach($primaryCategories as $category): ?>
                              <option value="<?php echo $category['id']; ?>">
                                  <?php echo htmlspecialchars($category['name']); ?>
                              </option>
                          <?php endforeach; ?>
                      </select>
                  </div>

                  <div class="form-group">
                      <label for="secondary_category">Дополнительная категория</label>
                      <select id="secondary_category" name="secondary_category" required>
                          <option value="">Выберите дополнительную категорию</option>
                          <?php
                          $secondaryQuery = "SELECT * FROM categories WHERE type = 'secondary' ORDER BY name";
                          $stmt = $db->prepare($secondaryQuery);
                          $stmt->execute();
                          $secondaryCategories = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        
                          foreach($secondaryCategories as $category): ?>
                              <option value="<?php echo $category['id']; ?>">
                                  <?php echo htmlspecialchars($category['name']); ?>
                              </option>
                          <?php endforeach; ?>
                      </select>
                  </div>

                  <button type="submit" class="submit-btn">Опубликовать</button>
              </form>
          </div>
    </div>
    <script src="js/upload-work.js"></script>
</body>
</html>

<input type="file" id="imageInput" multiple accept="image/*" hidden>

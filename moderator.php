<?php
require_once "config/database.php";
require_once "api/header.php";
require_once "config/helpers.php";
session_start();

if (!isUserModerator($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$database = new Database();
$db = $database->getConnection();

$query = "SELECT d.*, u.username, u.avatar, di.image_path, di.position, di.is_main 
          FROM designs d 
          JOIN users u ON d.user_id = u.id 
          JOIN design_images di ON d.id = di.design_id
          WHERE d.status = 'pending' 
          ORDER BY d.created_at ASC, di.position ASC";

$stmt = $db->prepare($query);
$stmt->execute();
$works = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Group images by design
$groupedWorks = [];
foreach ($works as $work) {
    $designId = $work['id'];
    if (!isset($groupedWorks[$designId])) {
        $groupedWorks[$designId] = $work;
        $groupedWorks[$designId]['images'] = [];
    }
    $groupedWorks[$designId]['images'][] = [
        'image_path' => $work['image_path'],
        'position' => $work['position'],
        'is_main' => $work['is_main']
    ];
}

$work = reset($groupedWorks); // Get the first work
?>

<!DOCTYPE html>
<html>
<head>
    <title>Модерация работ</title>
    <link rel="stylesheet" href="css/moderator.css">
</head>
<body>
    <div class="moderator-container">
        <?php if ($work): ?>
            <div class="card-container" data-id="<?php echo $work['id']; ?>">
                <div class="images-section">
                    <?php 
                    $imagesQuery = "SELECT * FROM design_images WHERE design_id = ? ORDER BY position ASC";
                    $stmt = $db->prepare($imagesQuery);
                    $stmt->execute([$work['id']]);
                    $images = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    foreach ($images as $index => $image): 
                        $gridArea = '';
                        switch(count($images)) {
                            case 3:
                                $gridArea = ['featured', 'second', 'third'][$index];
                                break;
                            case 4:
                                $gridArea = ['big', 'normal1', 'normal2', 'normal3'][$index];
                                break;
                            case 5:
                                $gridArea = ['main', 'side1', 'side2', 'side3', 'side4'][$index];
                                break;
                        }
                    ?>
                        <div class="modal-image" style="grid-area: <?php echo $gridArea; ?>">
                            <img src="uploads/designs/<?php echo $image['image_path']; ?>" alt="">
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="work-info">
                    <div class="author-info">
                        <img src="uploads/avatars/<?php echo $work['avatar']; ?>" alt="<?php echo $work['username']; ?>" class="author-avatar">
                        <span class="author-name"><?php echo $work['username']; ?></span>
                    </div>
                    <h3><?php echo $work['title']; ?></h3>
                    <p><?php echo $work['description']; ?></p>
                </div>
            </div>
            <div class="swipe-actions">
                <button class="reject-btn">✕</button>
                <button class="approve-btn">✓</button>
            </div>
        <?php else: ?>
            <div class="no-works">
                <h2>Нет работ на модерацию</h2>
            </div>
        <?php endif; ?>
    </div>
    <script src="js/moderator.js"></script>
</body>
</html>

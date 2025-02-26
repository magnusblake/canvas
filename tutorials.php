<?php
require_once 'config/database.php';
include 'api/header.php';

$database = new Database();
$db = $database->getConnection();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="/css/tutorials.css">
    <title>Туториалы</title>
</head>
<body>
    <div class="tutorials-page">
    <section class="hero-tutorials">
    <img src="src/default_img/image.svg" alt="Tutorials Banner">
    <div class="hero-content">
        <h1>Туториалы</h1>
        <p>Изучайте новые техники и делитесь своими знаниями с сообществом</p>
    </div>
    <div class="hero-gradient"></div>
</section>


        <div class="tutorials-container">
            <section class="top-tutorials">
                <h2>Популярные туториалы</h2>
                <div class="tutorials-grid">
                    <?php
                    $query = "SELECT t.*, u.username, tc.name as category 
                             FROM tutorials t
                             LEFT JOIN users u ON t.author_id = u.id 
                             LEFT JOIN tutorial_categories tc ON t.category_id = tc.id
                             ORDER BY t.views_count DESC LIMIT 6";
                    $stmt = $db->prepare($query);
                    $stmt->execute();
                    $tutorials = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    foreach($tutorials as $tutorial): ?>
                        <div class="tutorial-card">
                        <div class="tutorial-preview">
    <img src="/uploads/tutorials/thumbnails/<?php echo $tutorial['thumbnail_path']; ?>" alt="<?php echo $tutorial['title']; ?>">
</div>

                            <div class="tutorial-info">
                                <span class="category"><?php echo $tutorial['category']; ?></span>
                                <h3><?php echo $tutorial['title']; ?></h3>
                                <div class="meta">
                                    <span class="author"><?php echo $tutorial['username']; ?></span>
                                    <div class="info-stats">
        <div class="stat-item">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" color="black">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                <circle cx="12" cy="12" r="3"></circle>
            </svg>
            <span><?php echo $tutorial['views_count']; ?></span>
        </div>
        <div class="stat-item">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="red" stroke="currentColor" color="red">
                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
            </svg>
            <span><?php echo $tutorial['likes_count']; ?></span>
        </div>
    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>

            <section class="all-tutorials">
                <div class="section-header">
                    <h2>Все туториалы</h2>
                    <div class="filters">
                        <button class="filter-btn active">Все</button>
                        <button class="filter-btn">Начинающим</button>
                        <button class="filter-btn">Продвинутым</button>
                        <button class="filter-btn">Экспертам</button>
                    </div>
                </div>
                <div class="tutorials-grid">
                    <?php
                    $tutorialsQuery = "SELECT t.*, tc.name as category_name, u.username as author 
                                      FROM tutorials t 
                                      LEFT JOIN tutorial_categories tc ON t.category_id = tc.id 
                                      LEFT JOIN users u ON t.author_id = u.id 
                                      ORDER BY t.created_at DESC";
                    $stmt = $db->prepare($tutorialsQuery);
                    $stmt->execute();
                    $tutorials = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    foreach($tutorials as $tutorial): ?>
                        <div class="tutorial-card">
                        <div class="tutorial-preview">
    <img src="/uploads/tutorials/thumbnails/<?php echo $tutorial['thumbnail_path']; ?>" alt="<?php echo $tutorial['title']; ?>">
</div>

                            <div class="tutorial-info">
                                <span class="category"><?php echo $tutorial['category']; ?></span>
                                <h3><?php echo $tutorial['title']; ?></h3>
                                <div class="meta">
                                    <span class="author"><?php echo $tutorial['username']; ?></span>
                                    <div class="info-stats">
        <div class="stat-item">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" color="black">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                <circle cx="12" cy="12" r="3"></circle>
            </svg>
            <span><?php echo $tutorial['views_count']; ?></span>
        </div>
        <div class="stat-item">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="red" stroke="currentColor" color="red">
                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
            </svg>
            <span><?php echo $tutorial['likes_count']; ?></span>
        </div>
    </div>
</div>

                    <?php endforeach; ?>
                </div>
            </section>
        </div>
    </div>
</body>
</html>

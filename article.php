<?php
require_once __DIR__ . '/config/db.php';
$pdo = getDBConnection();

$articleId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$article = null;

if ($articleId > 0) {
    // Increment view count in MySQL
    $stmtView = $pdo->prepare("UPDATE articles SET views = views + 1 WHERE id = :id");
    $stmtView->execute([':id' => $articleId]);

    // Fetch article with category name
    $stmt = $pdo->prepare("
        SELECT a.*, c.name AS category_name, c.slug AS category_slug 
        FROM articles a 
        JOIN categories c ON a.category_id = c.id 
        WHERE a.id = :id
    ");
    $stmt->execute([':id' => $articleId]);
    $article = $stmt->fetch();
}

$pageTitle = $article ? $article['title'] . " - CityNews360" : "Article Not Found - CityNews360";
$activePage = "article";
require_once __DIR__ . '/includes/header.php';
?>

    <!-- Article Content -->
    <main class="article-main">
        <div class="container">
            <?php if ($article): ?>
                <article class="article-content" id="articleContent" data-article-id="<?php echo $article['id']; ?>">
                    <div class="article-header">
                        <h1 class="article-title"><?php echo htmlspecialchars($article['title']); ?></h1>
                        <div class="article-meta">
                            <span>By <?php echo htmlspecialchars($article['author_name'] ?: 'Admin'); ?></span>
                            <span><?php echo date('M d, Y', strtotime($article['created_at'])); ?></span>
                            <span><?php echo number_format($article['views']); ?> views</span>
                            <span style="background: #E2E8F0; padding: 2px 8px; border-radius: 12px; color: #475569; font-size: 0.85rem;">
                                <?php echo htmlspecialchars($article['category_name']); ?>
                            </span>
                        </div>
                    </div>
                    <?php if (!empty($article['image_url'])): ?>
                        <img src="<?php echo htmlspecialchars($article['image_url']); ?>" alt="<?php echo htmlspecialchars($article['title']); ?>" class="article-image">
                    <?php endif; ?>
                    <div class="article-text">
                        <?php echo $article['content']; ?>
                    </div>
                    <?php if (!empty($article['tags'])): ?>
                        <div class="article-tags">
                            <?php 
                            $tags = explode(',', $article['tags']);
                            foreach ($tags as $tag):
                                $cleanTag = trim($tag);
                                if (!empty($cleanTag)):
                            ?>
                                <span class="tag"><?php echo htmlspecialchars($cleanTag); ?></span>
                            <?php 
                                endif;
                            endforeach; 
                            ?>
                        </div>
                    <?php endif; ?>
                </article>

                <!-- Article Actions -->
                <div class="article-actions">
                    <div class="like-section">
                        <button class="like-btn" id="likeBtn" data-id="<?php echo $article['id']; ?>">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                            </svg>
                            <span id="likeCount"><?php echo number_format($article['likes']); ?></span> Likes
                        </button>
                    </div>
                    
                    <div class="share-section">
                        <span>Share:</span>
                        <button class="share-btn" data-platform="twitter">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/>
                            </svg>
                        </button>
                        <button class="share-btn" data-platform="facebook">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                        </button>
                        <button class="share-btn" data-platform="linkedin">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Related Articles -->
                <section class="related-articles">
                    <h2>Related Articles</h2>
                    <div class="related-grid" id="relatedGrid">
                        <?php
                        $stmtRel = $pdo->prepare("
                            SELECT * FROM articles 
                            WHERE category_id = :cat_id AND id != :id 
                            ORDER BY created_at DESC LIMIT 3
                        ");
                        $stmtRel->execute([':cat_id' => $article['category_id'], ':id' => $article['id']]);
                        $related = $stmtRel->fetchAll();

                        if (empty($related)) {
                            // Fallback to top recent articles if no category matches
                            $stmtRel = $pdo->prepare("SELECT * FROM articles WHERE id != :id ORDER BY created_at DESC LIMIT 3");
                            $stmtRel->execute([':id' => $article['id']]);
                            $related = $stmtRel->fetchAll();
                        }

                        foreach ($related as $rel):
                        ?>
                            <div class="related-card" onclick="window.location.href='article.php?id=<?php echo $rel['id']; ?>'" style="cursor: pointer;">
                                <img src="<?php echo htmlspecialchars($rel['image_url']); ?>" alt="<?php echo htmlspecialchars($rel['title']); ?>">
                                <div class="related-card-content">
                                    <h3><?php echo htmlspecialchars($rel['title']); ?></h3>
                                    <p><?php echo htmlspecialchars($rel['excerpt']); ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php else: ?>
                <div style="text-align: center; padding: 4rem 1rem;">
                    <h2>Article Not Found</h2>
                    <p style="margin: 1rem 0; color: #64748B;">The article you are looking for does not exist or has been removed.</p>
                    <a href="index.php" class="cta-btn">Back to Home</a>
                </div>
            <?php endif; ?>
        </div>
    </main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

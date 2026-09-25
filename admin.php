<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth.php';

// Strict Admin Access Control Requirement
requireAdmin();

$pdo = getDBConnection();
$currentUser = getCurrentUser();

// Fetch Dashboard Overview Stats directly from MySQL
$totalArticles = $pdo->query("SELECT COUNT(*) FROM articles")->fetchColumn();
$totalViews    = $pdo->query("SELECT COALESCE(SUM(views), 0) FROM articles")->fetchColumn();
$totalCategories = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();

// Fetch most popular category by views
$stmtPop = $pdo->query("
    SELECT c.name, SUM(a.views) as total_views 
    FROM articles a 
    JOIN categories c ON a.category_id = c.id 
    GROUP BY c.id 
    ORDER BY total_views DESC 
    LIMIT 1
");
$popCat = $stmtPop->fetch();
$popularCategory = $popCat ? $popCat['name'] : '-';

// Fetch all articles for management table
$stmtArticles = $pdo->query("
    SELECT a.*, c.name AS category_name 
    FROM articles a 
    JOIN categories c ON a.category_id = c.id 
    ORDER BY a.created_at DESC
");
$adminArticles = $stmtArticles->fetchAll();

// Additional overview analytics
$recentArticles = $pdo->query("
    SELECT a.id, a.title, a.created_at, c.name AS category_name
    FROM articles a
    JOIN categories c ON a.category_id = c.id
    ORDER BY a.created_at DESC
    LIMIT 4
")->fetchAll();

$categoryPerformance = $pdo->query("
    SELECT c.name, COUNT(a.id) AS article_count, COALESCE(SUM(a.views), 0) AS total_views
    FROM categories c
    LEFT JOIN articles a ON a.category_id = c.id
    GROUP BY c.id, c.name
    ORDER BY total_views DESC, article_count DESC
    LIMIT 5
")->fetchAll();

$publishedThisWeek = $pdo->query("SELECT COUNT(*) FROM articles WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetchColumn();
$avgLikes = $pdo->query("SELECT COALESCE(ROUND(AVG(likes), 0), 0) FROM articles")->fetchColumn();
$avgViews = $pdo->query("SELECT COALESCE(ROUND(AVG(views), 0), 0) FROM articles")->fetchColumn();

// Fetch all categories for management grid
$stmtCategories = $pdo->query("
    SELECT c.*, COUNT(a.id) AS article_count
    FROM categories c
    LEFT JOIN articles a ON a.category_id = c.id
    GROUP BY c.id
    ORDER BY c.name ASC
");
$adminCategories = $stmtCategories->fetchAll();

$pageTitle = "Admin Dashboard - CityNews360";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body class="admin-body">
    <!-- Admin Dashboard -->
    <div class="admin-dashboard" id="adminDashboard" style="display: block;">
        <!-- Header -->
        <header class="admin-header">
            <div class="container">
                <div class="admin-header-content">
                    <div class="admin-logo">
                        <a href="index.php" style="color: white; text-decoration: none;"><h1>CityNews360 Admin</h1></a>
                    </div>
                    <nav class="admin-nav">
                        <button class="admin-nav-btn active" data-section="overview">Overview</button>
                        <button class="admin-nav-btn" data-section="articles">Articles</button>
                        <button class="admin-nav-btn" data-section="categories">Categories</button>
                        <a href="index.php" class="admin-nav-btn" style="text-decoration: none;">View Site 🌐</a>
                    </nav>
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <span style="color: rgba(255,255,255,0.85); font-size: 0.9rem;">
                            Logged as: <strong><?php echo htmlspecialchars($currentUser['username']); ?></strong>
                        </span>
                        <a href="logout.php" class="logout-btn" style="text-decoration: none; text-align: center;">Logout</a>
                    </div>
                </div>
            </div>
        </header>

        <!-- Dashboard Content -->
        <main class="admin-main">
            <div class="container">
                <!-- Overview Section -->
                <section class="admin-section active" id="overview">
                    <h2>Dashboard Overview</h2>
                    <div class="stats-grid">
                        <div class="stat-card">
                            <h3 id="totalArticles"><?php echo number_format($totalArticles); ?></h3>
                            <p>Total Articles</p>
                        </div>
                        <div class="stat-card">
                            <h3 id="totalViews"><?php echo number_format($totalViews); ?></h3>
                            <p>Total Views</p>
                        </div>
                        <div class="stat-card">
                            <h3 id="totalCategories"><?php echo number_format($totalCategories); ?></h3>
                            <p>Categories</p>
                        </div>
                        <div class="stat-card">
                            <h3 id="popularCategory"><?php echo htmlspecialchars($popularCategory); ?></h3>
                            <p>Most Popular Category</p>
                        </div>
                    </div>

                    <div class="overview-panels">
                        <div class="panel panel-chart">
                            <div class="panel-header">
                                <div>
                                    <p class="panel-label">Performance</p>
                                    <h3>Traffic Trend</h3>
                                </div>
                                <span class="trend-badge positive">+18.2%</span>
                            </div>
                            <svg viewBox="0 0 520 180" class="trend-chart" aria-label="Traffic trend chart">
                                <defs>
                                    <linearGradient id="chartFill" x1="0" x2="0" y1="0" y2="1">
                                        <stop offset="0%" stop-color="#60a5fa" stop-opacity="0.35" />
                                        <stop offset="100%" stop-color="#60a5fa" stop-opacity="0.04" />
                                    </linearGradient>
                                </defs>
                                <path d="M10 150 C 80 130, 120 110, 160 120 S 250 90, 310 98 S 410 60, 510 35 L 510 180 L 10 180 Z" fill="url(#chartFill)"></path>
                                <path d="M10 150 C 80 130, 120 110, 160 120 S 250 90, 310 98 S 410 60, 510 35" fill="none" stroke="#2563eb" stroke-width="4" stroke-linecap="round"></path>
                                <circle cx="310" cy="98" r="5" fill="#2563eb"></circle>
                                <circle cx="510" cy="35" r="5" fill="#1d4ed8"></circle>
                            </svg>
                            <div class="chart-labels">
                                <span>Jan</span>
                                <span>Feb</span>
                                <span>Mar</span>
                                <span>Apr</span>
                                <span>May</span>
                                <span>Jun</span>
                            </div>
                        </div>

                        <div class="panel panel-list">
                            <div class="panel-header">
                                <div>
                                    <p class="panel-label">Top categories</p>
                                    <h3>Engagement</h3>
                                </div>
                            </div>
                            <ul class="category-stats-list">
                                <?php foreach ($categoryPerformance as $item): ?>
                                    <li>
                                        <div class="category-meta">
                                            <span class="dot" style="background-color: <?php echo htmlspecialchars($item['name'] === 'Sports' ? '#2563eb' : ($item['name'] === 'Lifestyle' ? '#10b981' : '#f59e0b')); ?>;"></span>
                                            <span><?php echo htmlspecialchars($item['name']); ?></span>
                                        </div>
                                        <div class="category-score">
                                            <strong><?php echo number_format($item['total_views']); ?></strong>
                                            <small>views</small>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>

                    <div class="mini-stats-grid">
                        <div class="mini-stat">
                            <span class="mini-label">Published This Week</span>
                            <strong><?php echo number_format($publishedThisWeek); ?></strong>
                        </div>
                        <div class="mini-stat">
                            <span class="mini-label">Avg. Likes</span>
                            <strong><?php echo number_format($avgLikes); ?></strong>
                        </div>
                        <div class="mini-stat">
                            <span class="mini-label">Avg. Views</span>
                            <strong><?php echo number_format($avgViews); ?></strong>
                        </div>
                        <div class="mini-stat">
                            <span class="mini-label">Active Categories</span>
                            <strong><?php echo number_format($totalCategories); ?></strong>
                        </div>
                    </div>

                    <div class="activity-grid">
                        <div class="panel">
                            <div class="panel-header">
                                <div>
                                    <p class="panel-label">Latest</p>
                                    <h3>Recent Articles</h3>
                                </div>
                            </div>
                            <ul class="activity-list">
                                <?php foreach ($recentArticles as $article): ?>
                                    <li>
                                        <div>
                                            <strong><?php echo htmlspecialchars($article['title']); ?></strong>
                                            <small><?php echo htmlspecialchars($article['category_name']); ?></small>
                                        </div>
                                        <span><?php echo date('M d', strtotime($article['created_at'])); ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>

                        <div class="panel">
                            <div class="panel-header">
                                <div>
                                    <p class="panel-label">Summary</p>
                                    <h3>Site Snapshot</h3>
                                </div>
                            </div>
                            <div class="snapshot-box">
                                <div class="snapshot-item">
                                    <span>Engagement</span>
                                    <strong>81%</strong>
                                </div>
                                <div class="snapshot-item">
                                    <span>Reader Retention</span>
                                    <strong>72%</strong>
                                </div>
                                <div class="snapshot-item">
                                    <span>Conversion</span>
                                    <strong>14.8%</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Articles Section -->
                <section class="admin-section" id="articles">
                    <div class="section-header">
                        <h2>Article Management</h2>
                        <button class="add-btn" id="addArticleBtn">Add New Article</button>
                    </div>
                    
                    <div class="articles-table-container">
                        <table class="articles-table" id="articlesTable">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Category</th>
                                    <th>Views</th>
                                    <th>Likes</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="articlesTableBody">
                                <?php foreach ($adminArticles as $art): ?>
                                    <tr data-article-id="<?php echo $art['id']; ?>">
                                        <td><strong><?php echo htmlspecialchars($art['title']); ?></strong></td>
                                        <td><span class="category-badge"><?php echo htmlspecialchars($art['category_name']); ?></span></td>
                                        <td><?php echo number_format($art['views']); ?></td>
                                        <td><?php echo number_format($art['likes']); ?></td>
                                        <td>
                                            <button class="action-btn edit-btn" onclick="editArticle(<?php echo $art['id']; ?>)">Edit</button>
                                            <button class="action-btn delete-btn" onclick="deleteArticle(<?php echo $art['id']; ?>)">Delete</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- Categories Section -->
                <section class="admin-section" id="categories">
                    <div class="section-header">
                        <h2>Category Management</h2>
                        <button class="add-btn" id="addCategoryBtn">Add New Category</button>
                    </div>
                    
                    <div class="categories-grid" id="categoriesGrid">
                        <?php foreach ($adminCategories as $cat): ?>
                            <div class="category-admin-card" data-category-id="<?php echo $cat['id']; ?>">
                                <div class="category-card-heading">
                                    <span class="category-card-mark" aria-hidden="true"><?php echo htmlspecialchars(strtoupper(substr($cat['name'], 0, 1))); ?></span>
                                    <span class="category-article-count"><?php echo number_format($cat['article_count']); ?> <?php echo (int) $cat['article_count'] === 1 ? 'article' : 'articles'; ?></span>
                                </div>
                                <h3><?php echo htmlspecialchars($cat['name']); ?></h3>
                                <p><?php echo htmlspecialchars($cat['description'] ?: 'No description provided.'); ?></p>
                                <div class="category-actions">
                                    <button class="action-btn add-btn" onclick="openArticleForCategory(<?php echo $cat['id']; ?>)">Add Article</button>
                                    <button class="action-btn edit-btn" onclick="editCategory(<?php echo $cat['id']; ?>, '<?php echo addslashes(htmlspecialchars($cat['name'])); ?>', '<?php echo addslashes(htmlspecialchars($cat['description'])); ?>')">Edit</button>
                                    <button class="action-btn delete-btn" onclick="deleteCategory(<?php echo $cat['id']; ?>)">Delete</button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            </div>
        </main>
    </div>

    <!-- Add/Edit Article Modal -->
    <div class="modal" id="articleModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle">Add New Article</h3>
                <button class="close-btn" id="closeModal">&times;</button>
            </div>
            <form id="articleForm" class="modal-form">
                <input type="hidden" id="articleId" name="id" value="">
                <div class="form-group">
                    <label for="articleTitle">Title *</label>
                    <input type="text" id="articleTitle" name="title" required>
                </div>
                <div class="form-group">
                    <label for="articleCategory">Category *</label>
                    <select id="articleCategory" name="category_id" required>
                        <option value="">Select Category</option>
                        <?php foreach ($adminCategories as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="articleImage">Image URL</label>
                    <input type="url" id="articleImage" name="imageUrl" placeholder="https://images.unsplash.com/photo-1504711434969-e33886168f5c">
                </div>
                <div class="form-group">
                    <label for="articleContent">Content *</label>
                    <textarea id="articleContent" name="content" rows="10" required></textarea>
                </div>
                <div class="form-actions">
                    <button type="button" class="cancel-btn" id="cancelArticle">Cancel</button>
                    <button type="submit" class="save-btn">Save Article</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add/Edit Category Modal -->
    <div class="modal" id="categoryModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="categoryModalTitle">Add New Category</h3>
                <button class="close-btn" id="closeCategoryModal">&times;</button>
            </div>
            <form id="categoryForm" class="modal-form">
                <input type="hidden" id="categoryId" name="id" value="">
                <div class="form-group">
                    <label for="categoryName">Category Name *</label>
                    <input type="text" id="categoryName" name="name" required>
                </div>
                <div class="form-group">
                    <label for="categoryDescription">Description</label>
                    <textarea id="categoryDescription" name="description" rows="3"></textarea>
                </div>
                <div class="form-actions">
                    <button type="button" class="cancel-btn" id="cancelCategory">Cancel</button>
                    <button type="submit" class="save-btn">Save Category</button>
                </div>
            </form>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>

<?php
$pageTitle = "Categories - CityNews360";
$activePage = "categories";
require_once __DIR__ . '/includes/auth.php';
startAppSession();
$canAddArticle = isAdmin();
require_once __DIR__ . '/includes/header.php';
?>

    <!-- Categories Section -->
    <section class="categories-page">
        <div class="container">
            <div class="categories-layout">
                <!-- Main Content -->
                <div class="main-content">
                    <div class="category-header">
                        <div style="display: flex; justify-content: space-between; align-items: center; gap: 1rem; flex-wrap: wrap;">
                            <h1>Browse Articles by Category</h1>
                            <?php if ($canAddArticle): ?>
                                <button class="add-btn" id="categoriesAddArticleBtn">Add Article</button>
                            <?php endif; ?>
                        </div>
                        <div class="category-selector" id="categorySelector">
                            <!-- Category tabs are loaded dynamically from the admin-managed categories -->
                        </div>
                    </div>
                    
                    <div class="articles-grid" id="articlesGrid">
                        <!-- Articles will be loaded dynamically from MySQL -->
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="sidebar">
                    <div class="sidebar-widget">
                        <h3>Recent Posts</h3>
                        <div class="recent-posts" id="recentPosts">
                            <!-- Recent posts will be loaded dynamically from MySQL -->
                        </div>
                    </div>
                    
                    <div class="sidebar-widget">
                        <h3>Most Viewed</h3>
                        <div class="most-viewed" id="mostViewed">
                            <!-- Most viewed posts will be loaded dynamically from MySQL -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php if ($canAddArticle): ?>
    <div class="modal" id="articleModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle">Add New Article</h3>
                <button class="close-btn" id="closeModal" type="button">&times;</button>
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
    <?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

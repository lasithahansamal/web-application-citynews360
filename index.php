<?php
$pageTitle = "CityNews360 - Stay Updated with What Matters Most";
$activePage = "index";
require_once __DIR__ . '/includes/header.php';
?>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-background"></div>
        <div class="container">
            <div class="hero-content">
                <h1 class="hero-title">Stay Updated with What Matters Most</h1>
                <p class="hero-subtitle">Tech • Sports • Lifestyle • More</p>
                <a href="#top-stories" class="cta-btn">Explore News</a>
            </div>
        </div>
    </section>

    <!-- Top Stories Section -->
    <section id="top-stories" class="top-stories">
        <div class="container">
            <h2 class="section-title">Top Stories</h2>
            <div class="stories-grid" id="storiesGrid">
                <!-- Stories will be loaded dynamically from MySQL -->
            </div>
        </div>
    </section>

    <!-- Trending Categories -->
    <section class="trending-categories">
        <div class="container">
            <h2 class="section-title">Trending Categories</h2>
            <div class="categories-grid" id="trendingCategoriesGrid">
                <!-- Categories will be loaded dynamically from the admin-managed database -->
            </div>
        </div>
    </section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

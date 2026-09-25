<?php
// config/db.php - Central Database Configuration & Auto-Initializer for CityNews360

define('DB_HOST', getenv('CITYNEWS360_DB_HOST') ?: '127.0.0.1');
define('DB_PORT', getenv('CITYNEWS360_DB_PORT') ?: '3306');
define('DB_NAME', getenv('CITYNEWS360_DB_NAME') ?: 'citynews360_db');
define('DB_USER', getenv('CITYNEWS360_DB_USER') ?: 'root');
define('DB_PASS', getenv('CITYNEWS360_DB_PASS') ?: '');

function getDBConnection() {
    static $pdo = null;

    if ($pdo !== null) {
        return $pdo;
    }

    try {
        $dsnDb = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $pdo = new PDO($dsnDb, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);

        autoInitializeDatabase($pdo);

        return $pdo;
    } catch (PDOException $e) {
        error_log('Database Connection Error: ' . $e->getMessage());
        $pdo = false;
        return false;
    }
}

function getFallbackCategories() {
    return [
        ['id' => 1, 'name' => 'Tech', 'slug' => 'tech', 'description' => 'Latest in technology and innovation'],
        ['id' => 2, 'name' => 'Sports', 'slug' => 'sports', 'description' => 'Sports news and updates'],
        ['id' => 3, 'name' => 'Lifestyle', 'slug' => 'lifestyle', 'description' => 'Health, fashion, and lifestyle trends']
    ];
}

function getFallbackArticles() {
    return [
        [
            'id' => 1,
            'title' => 'The Future of Artificial Intelligence in 2024',
            'excerpt' => 'Discover the latest breakthroughs in AI technology and how they\'re shaping our world.',
            'content' => '<h2>The Rise of AI in Modern Technology</h2><p>Artificial Intelligence has become one of the most transformative technologies of our time.</p>',
            'image_url' => 'https://images.unsplash.com/photo-1677442136019-21780efad99a?auto=format&fit=crop&w=800&q=80',
            'category_id' => 1,
            'author_id' => 1,
            'author_name' => 'Sarah Johnson',
            'views' => 1250,
            'likes' => 89,
            'tags' => '#Tech,#AI,#Innovation',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'category_name' => 'Tech',
            'category_slug' => 'tech'
        ],
        [
            'id' => 2,
            'title' => 'Championship Finals: A Historic Victory',
            'excerpt' => 'Relive the thrilling moments of this year\'s championship game that will be remembered for generations.',
            'content' => '<h2>The Road to Victory</h2><p>After months of intense competition, the championship finals delivered one of the most memorable games.</p>',
            'image_url' => 'https://images.unsplash.com/photo-1508098682722-e99c43a406b2?auto=format&fit=crop&w=800&q=80',
            'category_id' => 2,
            'author_id' => 1,
            'author_name' => 'Mike Thompson',
            'views' => 2100,
            'likes' => 156,
            'tags' => '#Sports,#Championship,#Victory',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'category_name' => 'Sports',
            'category_slug' => 'sports'
        ],
        [
            'id' => 3,
            'title' => 'Wellness Trends That Are Here to Stay',
            'excerpt' => 'Explore the latest wellness and lifestyle trends that are transforming how we approach health and happiness.',
            'content' => '<h2>Mindfulness and Mental Health</h2><p>Mental wellness has taken center stage in 2024, with more people embracing mindfulness practices.</p>',
            'image_url' => 'https://images.unsplash.com/photo-1544367567-0f2fcb009e0b?auto=format&fit=crop&w=800&q=80',
            'category_id' => 3,
            'author_id' => 1,
            'author_name' => 'Emma Davis',
            'views' => 980,
            'likes' => 67,
            'tags' => '#Lifestyle,#Wellness,#Health',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'category_name' => 'Lifestyle',
            'category_slug' => 'lifestyle'
        ]
    ];
}

function autoInitializeDatabase($pdo) {
    // 1. Create users table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `users` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `full_name` VARCHAR(100) NOT NULL,
            `email` VARCHAR(100) NOT NULL UNIQUE,
            `username` VARCHAR(50) NOT NULL UNIQUE,
            `password` VARCHAR(255) NOT NULL,
            `role` ENUM('ADMIN', 'USER') DEFAULT 'USER',
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");

    // 2. Create categories table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `categories` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(50) NOT NULL UNIQUE,
            `slug` VARCHAR(50) NOT NULL UNIQUE,
            `description` TEXT,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");

    // 3. Create articles table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `articles` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `title` VARCHAR(255) NOT NULL,
            `excerpt` TEXT NOT NULL,
            `content` TEXT NOT NULL,
            `image_url` VARCHAR(500) DEFAULT '',
            `category_id` INT NOT NULL,
            `author_id` INT DEFAULT NULL,
            `author_name` VARCHAR(100) DEFAULT 'Admin',
            `views` INT DEFAULT 0,
            `likes` INT DEFAULT 0,
            `tags` VARCHAR(255) DEFAULT '',
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            CONSTRAINT `fk_article_category` FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
            CONSTRAINT `fk_article_author` FOREIGN KEY (`author_id`) REFERENCES `users`(`id`) ON DELETE SET NULL ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");

    // 4. Create contact_messages table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `contact_messages` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(100) NOT NULL,
            `email` VARCHAR(100) NOT NULL,
            `subject` VARCHAR(255),
            `message` TEXT NOT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");

    // Seed Default Admin Account and repair stale hashes
    $adminPass = password_hash('admin123', PASSWORD_DEFAULT);
    $adminExists = $pdo->prepare("SELECT COUNT(*) FROM `users` WHERE `username` = 'admin' LIMIT 1");
    $adminExists->execute();

    if ($adminExists->fetchColumn() == 0) {
        $stmtInsertAdmin = $pdo->prepare("INSERT INTO `users` (`full_name`, `email`, `username`, `password`, `role`) VALUES ('System Administrator', 'admin@citynews360.com', 'admin', ?, 'ADMIN')");
        $stmtInsertAdmin->execute([$adminPass]);
    } else {
        $stmtUpdateAdmin = $pdo->prepare("UPDATE `users` SET `password` = ?, `full_name` = 'System Administrator', `email` = 'admin@citynews360.com', `role` = 'ADMIN' WHERE `username` = 'admin' LIMIT 1");
        $stmtUpdateAdmin->execute([$adminPass]);
    }

    // Seed Default Categories if missing
    $stmtCat = $pdo->query("SELECT COUNT(*) FROM `categories`");
    if ($stmtCat->fetchColumn() == 0) {
        $pdo->exec("INSERT INTO `categories` (`name`, `slug`, `description`) VALUES
            ('Tech', 'tech', 'Latest in technology and innovation'),
            ('Sports', 'sports', 'Sports news and updates'),
            ('Lifestyle', 'lifestyle', 'Health, fashion, and lifestyle trends')
        ");
    }

    // Seed Sample Articles if missing
    $stmtArt = $pdo->query("SELECT COUNT(*) FROM `articles`");
    if ($stmtArt->fetchColumn() == 0) {
        // Fetch category IDs
        $techId = $pdo->query("SELECT id FROM categories WHERE slug = 'tech'")->fetchColumn() ?: 1;
        $sportsId = $pdo->query("SELECT id FROM categories WHERE slug = 'sports'")->fetchColumn() ?: 2;
        $lifestyleId = $pdo->query("SELECT id FROM categories WHERE slug = 'lifestyle'")->fetchColumn() ?: 3;
        
        $adminUserId = $pdo->query("SELECT id FROM users WHERE username = 'admin'")->fetchColumn() ?: null;

        $stmtInsertArt = $pdo->prepare("INSERT INTO `articles` (`title`, `excerpt`, `content`, `image_url`, `category_id`, `author_id`, `author_name`, `views`, `likes`, `tags`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $stmtInsertArt->execute([
            "The Future of Artificial Intelligence in 2024",
            "Discover the latest breakthroughs in AI technology and how they're shaping our world.",
            "<h2>The Rise of AI in Modern Technology</h2><p>Artificial Intelligence has become one of the most transformative technologies of our time. From autonomous vehicles to smart home systems, AI is revolutionizing how we live and work.</p><h2>Key Developments in 2024</h2><p>This year has seen remarkable progress in machine learning algorithms, natural language processing, and computer vision. Companies are investing billions in AI research and development.</p><h2>Impact on Various Industries</h2><p>Healthcare, finance, education, and manufacturing are just a few sectors experiencing the AI revolution. The technology is creating new opportunities while also raising important questions about ethics and job displacement.</p><h2>Looking Ahead</h2><p>As we move forward, the integration of AI into everyday life will continue to accelerate. It's crucial for individuals and organizations to understand and adapt to these changes.</p>",
            "https://images.unsplash.com/photo-1677442136019-21780efad99a?auto=format&fit=crop&w=800&q=80",
            $techId,
            $adminUserId,
            "Sarah Johnson",
            1250,
            89,
            "#Tech,#AI,#Innovation"
        ]);

        $stmtInsertArt->execute([
            "Championship Finals: A Historic Victory",
            "Relive the thrilling moments of this year's championship game that will be remembered for generations.",
            "<h2>The Road to Victory</h2><p>After months of intense competition, the championship finals delivered one of the most memorable games in recent history. The underdog team's journey to the top captured the hearts of millions.</p>2 Key Moments of the Game</h2><p>From the opening whistle to the final buzzer, every moment was filled with excitement. The game went into overtime, keeping fans on the edge of their seats until the very end.</p><h2>Player Performance</h2><p>Several players delivered career-defining performances, with the MVP award going to a player who scored the winning goal in the final seconds of overtime.</p><h2>Celebration and Aftermath</h2><p>The victory parade drew thousands of fans, celebrating not just a win, but the spirit of determination and teamwork that led to this historic moment.</p>",
            "https://images.unsplash.com/photo-1508098682722-e99c43a406b2?auto=format&fit=crop&w=800&q=80",
            $sportsId,
            $adminUserId,
            "Mike Thompson",
            2100,
            156,
            "#Sports,#Championship,#Victory"
        ]);

        $stmtInsertArt->execute([
            "Wellness Trends That Are Here to Stay",
            "Explore the latest wellness and lifestyle trends that are transforming how we approach health and happiness.",
            "<h2>Mindfulness and Mental Health</h2><p>Mental wellness has taken center stage in 2024, with more people embracing mindfulness practices, meditation, and stress-reduction techniques.</p><h2>Plant-Based Living</h2><p>The shift toward plant-based diets continues to grow, driven by health benefits, environmental concerns, and ethical considerations.</p><h2>Digital Detox</h2><p>As technology becomes more pervasive, people are increasingly seeking ways to disconnect and find balance in their digital lives.</p><h2>Sustainable Living</h2><p>From eco-friendly products to sustainable fashion, consumers are making choices that align with their environmental values.</p>",
            "https://images.unsplash.com/photo-1544367567-0f2fcb009e0b?auto=format&fit=crop&w=800&q=80",
            $lifestyleId,
            $adminUserId,
            "Emma Davis",
            980,
            67,
            "#Lifestyle,#Wellness,#Health"
        ]);
    }
}

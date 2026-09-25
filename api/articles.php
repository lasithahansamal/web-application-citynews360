<?php
// api/articles.php - Articles API Handler (GET, POST - SELECT, INSERT, UPDATE, DELETE)
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';

startAppSession();
$pdo = getDBConnection();

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    try {
        $id       = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $category = isset($_GET['category']) ? trim($_GET['category']) : '';
        $top      = isset($_GET['top']) ? intval($_GET['top']) : 0;
        $stats    = isset($_GET['stats']) ? true : false;

        if ($pdo === false) {
            $articles = getFallbackArticles();

            if ($stats) {
                echo json_encode([
                    'success' => true,
                    'totalArticles' => count($articles),
                    'totalViews' => array_sum(array_column($articles, 'views')),
                    'totalCategories' => count(getFallbackCategories()),
                    'popularCategory' => 'Tech'
                ]);
                exit;
            }

            if ($id > 0) {
                $article = null;
                foreach ($articles as $item) {
                    if ((int)$item['id'] === $id) {
                        $article = $item;
                        break;
                    }
                }

                if ($article) {
                    $article['views'] = (int)$article['views'] + 1;
                    echo json_encode(['success' => true, 'article' => $article]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Article not found.']);
                }
                exit;
            }

            if (!empty($category) && strtolower($category) !== 'all') {
                $filtered = array_values(array_filter($articles, function ($article) use ($category) {
                    return strtolower($article['category_slug']) === strtolower($category) || strtolower($article['category_name']) === strtolower($category);
                }));
                $articles = $filtered;
            }

            if ($top > 0) {
                $articles = array_slice($articles, 0, $top);
            }

            echo json_encode(['success' => true, 'articles' => $articles]);
            exit;
        }

        // If stats requested for admin overview
        if ($stats) {
            requireAdmin();
            $totalArticles = $pdo->query("SELECT COUNT(*) FROM articles")->fetchColumn();
            $totalViews    = $pdo->query("SELECT COALESCE(SUM(views), 0) FROM articles")->fetchColumn();
            $totalCats     = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();

            // Most popular category
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

            echo json_encode([
                'success' => true,
                'totalArticles' => intval($totalArticles),
                'totalViews' => intval($totalViews),
                'totalCategories' => intval($totalCats),
                'popularCategory' => $popularCategory
            ]);
            exit;
        }

        if ($id > 0) {
            // Get single article
            $stmt = $pdo->prepare("
                SELECT a.*, c.name AS category_name, c.slug AS category_slug 
                FROM articles a 
                JOIN categories c ON a.category_id = c.id 
                WHERE a.id = :id
            ");
            $stmt->execute([':id' => $id]);
            $article = $stmt->fetch();

            if ($article) {
                // Increment views
                $updateViews = $pdo->prepare("UPDATE articles SET views = views + 1 WHERE id = :id");
                $updateViews->execute([':id' => $id]);
                $article['views'] = intval($article['views']) + 1;

                echo json_encode(['success' => true, 'article' => $article]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Article not found.']);
            }
            exit;
        }

        // Base query for articles list
        $sql = "
            SELECT a.*, c.name AS category_name, c.slug AS category_slug 
            FROM articles a 
            JOIN categories c ON a.category_id = c.id 
        ";
        $params = [];

        if (!empty($category) && strtolower($category) !== 'all') {
            $sql .= " WHERE c.slug = :cat OR c.name = :cat ";
            $params[':cat'] = strtolower($category);
        }

        if ($top > 0) {
            $sql .= " ORDER BY a.views DESC LIMIT " . intval($top);
        } else {
            $sql .= " ORDER BY a.created_at DESC ";
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $articles = $stmt->fetchAll();

        echo json_encode(['success' => true, 'articles' => $articles]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database Error: ' . $e->getMessage()]);
    }
    exit;
}

if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        $input = $_POST;
    }

    $action = $input['action'] ?? '';

    // Action: Like article (public action)
    if ($action === 'like') {
        $id = intval($input['id'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid article ID.']);
            exit;
        }

        try {
            $stmt = $pdo->prepare("UPDATE articles SET likes = likes + 1 WHERE id = :id");
            $stmt->execute([':id' => $id]);

            $stmtGet = $pdo->prepare("SELECT likes FROM articles WHERE id = :id");
            $stmtGet->execute([':id' => $id]);
            $likes = $stmtGet->fetchColumn();

            echo json_encode(['success' => true, 'likes' => intval($likes)]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Database Error: ' . $e->getMessage()]);
        }
        exit;
    }

    // Admin authorization required for CRUD actions (create, update, delete)
    requireAdmin();

    if ($action === 'create') {
        $title    = trim($input['title'] ?? '');
        $category = trim($input['category'] ?? '');
        $catId    = intval($input['category_id'] ?? 0);
        $imageUrl = trim($input['imageUrl'] ?? $input['image_url'] ?? '');
        $content  = trim($input['content'] ?? '');

        if (empty($title) || (empty($category) && $catId <= 0) || empty($content)) {
            echo json_encode(['success' => false, 'message' => 'Title, category, and content are required.']);
            exit;
        }

        if (empty($imageUrl)) {
            $imageUrl = 'https://images.unsplash.com/photo-1504711434969-e33886168f5c?auto=format&fit=crop&w=800&q=80';
        }

        // Resolve category_id if name passed
        if ($catId <= 0 && !empty($category)) {
            $stmtCat = $pdo->prepare("SELECT id FROM categories WHERE name = :cat OR slug = :cat LIMIT 1");
            $stmtCat->execute([':cat' => $category]);
            $catId = $stmtCat->fetchColumn();
            if (!$catId) {
                // Auto create category if missing
                $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $category));
                $stmtNewCat = $pdo->prepare("INSERT INTO categories (name, slug, description) VALUES (:name, :slug, '')");
                $stmtNewCat->execute([':name' => $category, ':slug' => $slug]);
                $catId = $pdo->lastInsertId();
            }
        }

        $excerpt = mb_substr(strip_tags($content), 0, 150) . '...';
        $authorId = $_SESSION['user_id'] ?? null;
        $authorName = $_SESSION['full_name'] ?: ($_SESSION['username'] ?? 'Admin');
        $tags = '#' . preg_replace('/\s+/', '', $category);

        try {
            $stmt = $pdo->prepare("
                INSERT INTO articles (title, excerpt, content, image_url, category_id, author_id, author_name, views, likes, tags) 
                VALUES (:title, :excerpt, :content, :image_url, :category_id, :author_id, :author_name, 0, 0, :tags)
            ");
            $stmt->execute([
                ':title'       => $title,
                ':excerpt'     => $excerpt,
                ':content'     => nl2br($content),
                ':image_url'   => $imageUrl,
                ':category_id' => $catId,
                ':author_id'   => $authorId,
                ':author_name' => $authorName,
                ':tags'        => $tags
            ]);

            echo json_encode(['success' => true, 'message' => 'Article added successfully!', 'id' => $pdo->lastInsertId()]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Database Error: ' . $e->getMessage()]);
        }
        exit;
    }

    if ($action === 'update') {
        $id       = intval($input['id'] ?? 0);
        $title    = trim($input['title'] ?? '');
        $category = trim($input['category'] ?? '');
        $catId    = intval($input['category_id'] ?? 0);
        $imageUrl = trim($input['imageUrl'] ?? $input['image_url'] ?? '');
        $content  = trim($input['content'] ?? '');

        if ($id <= 0 || empty($title) || (empty($category) && $catId <= 0) || empty($content)) {
            echo json_encode(['success' => false, 'message' => 'Valid ID, title, category, and content are required.']);
            exit;
        }

        if ($catId <= 0 && !empty($category)) {
            $stmtCat = $pdo->prepare("SELECT id FROM categories WHERE name = :cat OR slug = :cat LIMIT 1");
            $stmtCat->execute([':cat' => $category]);
            $catId = $stmtCat->fetchColumn();
        }

        $excerpt = mb_substr(strip_tags($content), 0, 150) . '...';

        try {
            $stmt = $pdo->prepare("
                UPDATE articles 
                SET title = :title, category_id = :category_id, image_url = :image_url, content = :content, excerpt = :excerpt 
                WHERE id = :id
            ");
            $stmt->execute([
                ':title'       => $title,
                ':category_id' => $catId,
                ':image_url'   => $imageUrl,
                ':content'     => strpos($content, '<p>') !== false ? $content : nl2br($content),
                ':excerpt'     => $excerpt,
                ':id'          => $id
            ]);

            echo json_encode(['success' => true, 'message' => 'Article updated successfully!']);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Database Error: ' . $e->getMessage()]);
        }
        exit;
    }

    if ($action === 'delete') {
        $id = intval($input['id'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid article ID.']);
            exit;
        }

        try {
            $stmt = $pdo->prepare("DELETE FROM articles WHERE id = :id");
            $stmt->execute([':id' => $id]);

            echo json_encode(['success' => true, 'message' => 'Article deleted successfully!']);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Database Error: ' . $e->getMessage()]);
        }
        exit;
    }

    echo json_encode(['success' => false, 'message' => 'Invalid action specified.']);
    exit;
}

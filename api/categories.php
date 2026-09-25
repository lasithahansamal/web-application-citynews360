<?php
// api/categories.php - Category Management API (GET, POST - SELECT, INSERT, UPDATE, DELETE)
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';

startAppSession();
$pdo = getDBConnection();

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    if ($pdo === false) {
        echo json_encode(['success' => true, 'categories' => getFallbackCategories()]);
        exit;
    }

    try {
        $stmt = $pdo->query("SELECT * FROM categories ORDER BY name ASC");
        $categories = $stmt->fetchAll();
        echo json_encode(['success' => true, 'categories' => $categories]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database Error: ' . $e->getMessage()]);
    }
    exit;
}

if ($method === 'POST') {
    requireAdmin(); // Access control check

    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        $input = $_POST;
    }

    $action = $input['action'] ?? '';

    if ($action === 'create') {
        $name        = trim($input['name'] ?? '');
        $description = trim($input['description'] ?? '');

        if (empty($name)) {
            echo json_encode(['success' => false, 'message' => 'Category name is required.']);
            exit;
        }

        $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $name));

        try {
            // Check duplicate
            $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM categories WHERE name = :name");
            $stmtCheck->execute([':name' => $name]);
            if ($stmtCheck->fetchColumn() > 0) {
                echo json_encode(['success' => false, 'message' => 'Category name already exists.']);
                exit;
            }

            $stmt = $pdo->prepare("INSERT INTO categories (name, slug, description) VALUES (:name, :slug, :description)");
            $stmt->execute([
                ':name'        => $name,
                ':slug'        => $slug,
                ':description' => $description
            ]);

            echo json_encode(['success' => true, 'message' => 'Category added successfully!', 'id' => $pdo->lastInsertId()]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Database Error: ' . $e->getMessage()]);
        }
        exit;
    }

    if ($action === 'update') {
        $id          = intval($input['id'] ?? 0);
        $name        = trim($input['name'] ?? '');
        $description = trim($input['description'] ?? '');

        if ($id <= 0 || empty($name)) {
            echo json_encode(['success' => false, 'message' => 'Valid ID and category name are required.']);
            exit;
        }

        $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $name));

        try {
            $stmt = $pdo->prepare("UPDATE categories SET name = :name, slug = :slug, description = :description WHERE id = :id");
            $stmt->execute([
                ':name'        => $name,
                ':slug'        => $slug,
                ':description' => $description,
                ':id'          => $id
            ]);

            echo json_encode(['success' => true, 'message' => 'Category updated successfully!']);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Database Error: ' . $e->getMessage()]);
        }
        exit;
    }

    if ($action === 'delete') {
        $id = intval($input['id'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid category ID.']);
            exit;
        }

        try {
            $stmt = $pdo->prepare("DELETE FROM categories WHERE id = :id");
            $stmt->execute([':id' => $id]);

            echo json_encode(['success' => true, 'message' => 'Category deleted successfully!']);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Database Error: ' . $e->getMessage()]);
        }
        exit;
    }

    echo json_encode(['success' => false, 'message' => 'Invalid action specified.']);
    exit;
}

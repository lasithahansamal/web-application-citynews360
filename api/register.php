<?php
// api/register.php - User Registration API / POST Handler
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';

startAppSession();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

// Get POST data (supports JSON body or form data)
$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    $input = $_POST;
}

$fullName = trim($input['full_name'] ?? $input['fullName'] ?? '');
$email    = trim($input['email'] ?? '');
$username = trim($input['username'] ?? '');
$password = $input['password'] ?? '';
$confirmPassword = $input['confirm_password'] ?? $input['confirmPassword'] ?? '';

// 1. Validate Form Fields
if (empty($fullName) || empty($email) || empty($username) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
    exit;
}

if (strlen($username) < 3) {
    echo json_encode(['success' => false, 'message' => 'Username must be at least 3 characters long.']);
    exit;
}

if (strlen($password) < 6) {
    echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters long.']);
    exit;
}

if (!empty($confirmPassword) && $password !== $confirmPassword) {
    echo json_encode(['success' => false, 'message' => 'Passwords do not match.']);
    exit;
}

try {
    $pdo = getDBConnection();

    // 2. Check if username already exists
    $stmtUser = $pdo->prepare("SELECT COUNT(*) FROM `users` WHERE `username` = :username");
    $stmtUser->execute([':username' => $username]);
    if ($stmtUser->fetchColumn() > 0) {
        echo json_encode(['success' => false, 'message' => 'Username is already taken. Please choose another.']);
        exit;
    }

    // 3. Check if email already exists
    $stmtEmail = $pdo->prepare("SELECT COUNT(*) FROM `users` WHERE `email` = :email");
    $stmtEmail->execute([':email' => $email]);
    if ($stmtEmail->fetchColumn() > 0) {
        echo json_encode(['success' => false, 'message' => 'Email is already registered. Please use another or login.']);
        exit;
    }

    // 4. Save User to MySQL with Password Hashing
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmtInsert = $pdo->prepare("INSERT INTO `users` (`full_name`, `email`, `username`, `password`, `role`) VALUES (:full_name, :email, :username, :password, 'USER')");
    $stmtInsert->execute([
        ':full_name' => $fullName,
        ':email' => $email,
        ':username' => $username,
        ':password' => $hashedPassword
    ]);

    $userId = $pdo->lastInsertId();

    echo json_encode([
        'success' => true,
        'message' => 'Registration successful! You can now log in.',
        'user_id' => $userId
    ]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database Error: ' . $e->getMessage()]);
}

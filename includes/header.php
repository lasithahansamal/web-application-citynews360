<?php
// includes/header.php - Global Shared Header Component
require_once __DIR__ . '/auth.php';
startAppSession();
$user = getCurrentUser();
$activePage = $activePage ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) . ' - CityNews360' : 'CityNews360 - Stay Updated with What Matters Most'; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <a href="index.php" style="text-decoration: none; color: inherit;"><h1>CityNews360</h1></a>
                </div>
                <nav class="nav">
                    <a href="index.php" class="nav-link <?php echo $activePage === 'index' ? 'active' : ''; ?>">Home</a>
                    <a href="categories.php" class="nav-link <?php echo $activePage === 'categories' ? 'active' : ''; ?>">Categories</a>
                    <a href="about.php" class="nav-link <?php echo $activePage === 'about' ? 'active' : ''; ?>">About</a>
                    <a href="contact.php" class="nav-link <?php echo $activePage === 'contact' ? 'active' : ''; ?>">Contact</a>
                </nav>
                <div class="header-user-actions" style="display: flex; align-items: center; gap: 1rem;">
                    <?php if ($user): ?>
                        <span style="font-size: 0.9rem; font-weight: 500; color: #1E293B;">
                            👤 <?php echo htmlspecialchars($user['full_name'] ?: $user['username']); ?>
                            <?php if ($user['role'] === 'ADMIN'): ?>
                                <span style="background: #2563EB; color: white; padding: 2px 6px; border-radius: 4px; font-size: 0.75rem; margin-left: 4px;">ADMIN</span>
                            <?php endif; ?>
                        </span>
                        <?php if ($user['role'] === 'ADMIN'): ?>
                            <a href="admin.php" class="admin-btn" style="background: #1E293B;">Admin Panel</a>
                        <?php endif; ?>
                        <a href="logout.php" class="admin-btn" style="background: #EF4444;">Logout</a>
                    <?php else: ?>
                        <a href="login.php" class="nav-link <?php echo $activePage === 'login' ? 'active' : ''; ?>" style="font-weight: 500;">Login</a>
                        <a href="register.php" class="nav-link <?php echo $activePage === 'register' ? 'active' : ''; ?>" style="font-weight: 500;">Register</a>
                        <a href="admin.php" class="admin-btn">Admin Login</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

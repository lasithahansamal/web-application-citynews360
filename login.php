<?php
require_once __DIR__ . '/includes/auth.php';
startAppSession();

if (isLoggedIn()) {
    $redirect = isAdmin() ? 'admin.php' : 'index.php';
    header("Location: $redirect");
    exit;
}

$pageTitle = "Login - CityNews360";
$activePage = "login";
require_once __DIR__ . '/includes/header.php';

$errorMsg = '';
$successMsg = '';

if (isset($_GET['msg']) && $_GET['msg'] === 'please_login') {
    $errorMsg = 'Please log in to access that page.';
} elseif (isset($_GET['msg']) && $_GET['msg'] === 'registered') {
    $successMsg = 'Registration successful! You can now log in with your credentials.';
} elseif (isset($_GET['error']) && $_GET['error'] === 'unauthorized') {
    $errorMsg = 'Access denied. Administrator privileges required.';
}
?>

    <div class="admin-login" style="min-height: calc(100vh - 200px); padding: 4rem 1rem;">
        <div class="login-container">
            <div class="login-shell">
                <div class="login-card">
                    <h2>Account Login</h2>

                    <?php if (!empty($errorMsg)): ?>
                        <div class="alert alert-error" style="background: #FEE2E2; color: #991B1B; padding: 0.75rem 1rem; border-radius: 8px; margin-bottom: 1rem; font-size: 0.9rem;">
                            ⚠️ <?php echo htmlspecialchars($errorMsg); ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($successMsg)): ?>
                        <div class="alert alert-success" style="background: #D1FAE5; color: #065F46; padding: 0.75rem 1rem; border-radius: 8px; margin-bottom: 1rem; font-size: 0.9rem;">
                            ✅ <?php echo htmlspecialchars($successMsg); ?>
                        </div>
                    <?php endif; ?>

                    <div id="loginAlert" class="login-alert" aria-live="polite" aria-atomic="true">
                        <button type="button" class="login-alert-close" aria-label="Close alert">×</button>
                        <div class="login-alert-message">Login error occurred. Please try again.</div>
                        <button type="button" class="login-alert-ok">OK</button>
                    </div>

                    <form id="pageLoginForm" class="login-form">
                        <div class="form-group">
                            <label for="username">Username or Email</label>
                            <input type="text" id="username" name="username" placeholder="Enter username or email" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" id="password" name="password" placeholder="Enter password" required>
                        </div>
                        <button type="submit" class="login-btn">Login</button>
                    </form>

                    <div style="margin-top: 1.5rem; text-align: center; border-top: 1px solid #E2E8F0; padding-top: 1rem;">
                        <p style="font-size: 0.9rem; color: #64748B;">
                            Don't have an account? <a href="register.php" style="color: #2563EB; font-weight: 600; text-decoration: none;">Register here</a>
                        </p>
                    </div>

                    <p class="login-hint" style="margin-top: 1rem; background: #F8FAFC; padding: 0.5rem; border-radius: 6px;">
                        🔑 Default Admin: <strong>admin</strong> / <strong>admin123</strong>
                    </p>
                </div>
            </div>
        </div>
    </div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

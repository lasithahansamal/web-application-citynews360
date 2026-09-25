<?php
require_once __DIR__ . '/includes/auth.php';
startAppSession();

if (isLoggedIn()) {
    header("Location: index.php");
    exit;
}

$pageTitle = "Register - CityNews360";
$activePage = "register";
require_once __DIR__ . '/includes/header.php';
?>

    <div class="admin-login" style="min-height: calc(100vh - 200px); padding: 4rem 1rem;">
        <div class="login-container">
            <div class="login-card" style="max-width: 450px;">
                <h2>Create an Account</h2>
                
                <div id="registerAlert" style="display: none; padding: 0.75rem 1rem; border-radius: 8px; margin-bottom: 1rem; font-size: 0.9rem;"></div>

                <form id="registerForm" class="login-form">
                    <div class="form-group">
                        <label for="regFullName">Full Name *</label>
                        <input type="text" id="regFullName" name="full_name" placeholder="John Doe" required>
                    </div>

                    <div class="form-group">
                        <label for="regEmail">Email Address *</label>
                        <input type="email" id="regEmail" name="email" placeholder="john@example.com" required>
                    </div>

                    <div class="form-group">
                        <label for="regUsername">Username *</label>
                        <input type="text" id="regUsername" name="username" placeholder="johndoe" required>
                    </div>

                    <div class="form-group">
                        <label for="regPassword">Password *</label>
                        <input type="password" id="regPassword" name="password" placeholder="Minimum 6 characters" required>
                    </div>

                    <div class="form-group">
                        <label for="regConfirmPassword">Confirm Password *</label>
                        <input type="password" id="regConfirmPassword" name="confirm_password" placeholder="Re-enter password" required>
                    </div>

                    <button type="submit" class="login-btn">Register Account</button>
                </form>

                <div style="margin-top: 1.5rem; text-align: center; border-top: 1px solid #E2E8F0; padding-top: 1rem;">
                    <p style="font-size: 0.9rem; color: #64748B;">
                        Already have an account? <a href="login.php" style="color: #2563EB; font-weight: 600; text-decoration: none;">Login here</a>
                    </p>
                </div>
            </div>
        </div>
    </div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

<?php
session_start();
$error = $_SESSION["auth_error"] ?? "";
$success = $_SESSION["auth_success"] ?? "";
unset($_SESSION["auth_error"], $_SESSION["auth_success"]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Login</title>
    <link rel="stylesheet" href="login-styles.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>
                <span class="icon">🔐</span>
                Staff Login
            </h1>
            <p>Access your account dashboard</p>
        </div>

        <div class="form-content">
            <!-- Success from create account OR redirected with ?new=true -->
            <div class="alert alert-success" id="successAlert" style="display: none;">
                <div class="alert-title">✅ Account Created Successfully!</div>
                <div class="alert-text">
                    Your staff account has been created. Please login with your credentials.
                </div>
            </div>

            <?php if (!empty($success)): ?>
                <div class="alert alert-success" style="display:block;">
                    <div class="alert-title">✅ Success</div>
                    <div class="alert-text"><?php echo htmlspecialchars($success); ?></div>
                </div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <div class="alert" style="display:block; border-left: 4px solid #e74c3c;">
                    <div class="alert-title">❌ Login Failed</div>
                    <div class="alert-text"><?php echo htmlspecialchars($error); ?></div>
                </div>
            <?php endif; ?>

            <!-- IMPORTANT: now posts to auth_login.php -->
            <form id="loginForm" method="POST" action="auth_login.php" novalidate>
                <div class="form-group">
                    <label>Username or Email <span class="required">*</span></label>
                    <input type="text" id="username" name="login" placeholder="Enter your username or email" required>
                    <div class="error-message" id="usernameError"></div>
                </div>

                <div class="form-group">
                    <label>Password <span class="required">*</span></label>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                    <div class="error-message" id="passwordError"></div>
                    
                </div>
                

                <div class="form-options">
                    <label class="remember-me">
                        <input type="checkbox" id="rememberMe" name="remember_me" value="1">
                        <span>Remember me</span>
                    </label>
            
                </div>


                <button type="submit" class="btn btn-submit">Login</button>



</body>
</html>

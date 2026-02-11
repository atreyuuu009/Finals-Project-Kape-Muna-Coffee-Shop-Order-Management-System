<?php
require "db.php"; // includes session_start() too (recommended)

// If not logged in, send to login
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit;
}

$role = $_SESSION["user"]["role"]; // ✅ correct role source
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coffee Shop Management System</title>
    <link rel="stylesheet" href="home-styles.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="logo">
                <span class="logo-icon">☕</span>
                <span class="logo-text">Kape Muna</span>
            </div>
            <div class="nav-links">
                <a href="#features">Features</a>
                <a href="#about">About</a>
                <a href="logout.php" class="order-btn">Logout</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <div class="hero-text">
                <h1 class="hero-title">
                    Manage Your Coffee Shop
                    <span class="highlight">Effortlessly</span>
                </h1>
                <p class="hero-description">
                    Complete staff management system designed for modern coffee shops. 
                    Track sales, manage inventory, and streamline operations all in one place.
                </p>
               
            </div>
            <div class="hero-visual">
                <div class="coffee-cup">
                    <div class="cup-body">
                        <div class="steam steam-1"></div>
                        <div class="steam steam-2"></div>
                        <div class="steam steam-3"></div>
                        ☕
                    </div>
                </div>
            </div>
        </div>
        <div class="scroll-indicator">
            <div class="mouse">
                <div class="wheel"></div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features" id="features">
        <div class="container">
            <h2 class="section-title">Everything You Need</h2>
            <p class="section-subtitle">Powerful features to run your coffee shop smoothly</p>
            
            <div class="features-grid"
            > <?php if (!$role): ?>
  <!-- Not logged in: show nothing or show a message -->
  <p style="text-align:center; opacity:.8;">Please login to access system modules.</p>

<?php else: ?>

  <?php if (in_array($role, ['staff','cashier','manager','admin'])): ?>
    <div class="feature-card" data-link="menu.php">
      <div class="feature-icon">📋</div>
      <h3>Menu Control</h3>
      <p>View menu items and available products.</p>
    </div>
  <?php endif; ?>

  <?php if (in_array($role, ['cashier','admin', 'manager'])): ?>
    <div class="feature-card" data-link="orders.php">
      <div class="feature-icon">🤎</div>
      <h3>Order Processing</h3>
      <p>Create orders and update their status.</p>
    </div>
  <?php endif; ?>

  <?php if (in_array($role, ['manager','admin'])): ?>
    <div class="feature-card" data-link="analytics.php">
      <div class="feature-icon">📊</div>
      <h3>Sales Analytics</h3>
      <p>View sales reports and performance charts.</p>
    </div>
  <?php endif; ?>
  <?php if ($role === 'admin'): ?>
    <div class="feature-card" data-link="register.php">
      <div class="feature-icon">👤</div>
      <h3>Create Accounts</h3>
      <p>Create staff, cashier, and manager accounts.</p>
    </div>
  <?php endif; ?>

<?php endif; ?>

                

  
 


</div>

        </div>
    </section>

    <!-- About Section -->
    <section class="about" id="about">
        <div class="container">
            <div class="about-content">
                <div class="about-text">
                    <h2>Built for Coffee Lovers</h2>
                    <p>Our management system is designed specifically for coffee shops, from small cafés to large chains. We understand the unique challenges you face and provide tools to solve them.</p>
                    <ul class="benefits-list">
                        <li>
                            <span class="check-icon">✓</span>
                            <span>Easy to use interface</span>
                        </li>
                        <li>
                            <span class="check-icon">✓</span>
                            <span>No technical knowledge required</span>
                        </li>
                        <li>
                            <span class="check-icon">✓</span>
                            <span>Free for small businesses</span>
                        </li>
                        <li>
                            <span class="check-icon">✓</span>
                            <span>24/7 customer support</span>
                        </li>
                    </ul>
                </div>
                <div class="about-stats">
                    <div class="stat-card">
                        <div class="stat-number">1</div>
                        <div class="stat-label">Coffee Shops</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">4</div>
                        <div class="stat-label">Active Users</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">100%</div>
                        <div class="stat-label">Uptime</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta">
        <div class="container">
            <h2>Tired?</h2>
            <p>Rest ka muna here. <3 </p>
            
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <div class="footer-logo">
                        <span class="logo-icon">☕</span>
                        <span class="logo-text">Kape Muna</span>
                    </div>
                    
                
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 Kape Muna. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="home-script.js"></script>
</body>
</html>
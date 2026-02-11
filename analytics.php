<?php
require "db.php";

if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit;
}

$role = $_SESSION["user"]["role"] ?? null;

if ($role === null) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit;
}

// Only manager/admin can view analytics
if (!in_array($role, ["manager", "admin"])) {
    die("Access denied.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Analytics - Kape Muna</title>
  <link rel="stylesheet" href="analytics_style.css">
</head>
<body>

<header class="navbar">
  <h2 class="logo">Kape Muna</h2>

  <nav class="nav-links">
    <a href="home.php">Home</a>
    <a href="menu.php">Menu</a>

    <?php if (in_array($role, ['cashier','manager','admin'])): ?>
      <a href="orders.php">Orders</a>
    <?php endif; ?>
  </nav>
</header>

<section class="hero">
  <h1>Sales Analytics<br><span>Track your performance</span></h1>

  <!-- Date Filter -->
  <div class="date-filter">
    <div class="filter-pills">
      <div class="filter-pill active" onclick="filterByDate('today', this)">Today</div>
      <div class="filter-pill" onclick="filterByDate('week', this)">This Week</div>
      <div class="filter-pill" onclick="filterByDate('month', this)">This Month</div>
      <div class="filter-pill" onclick="filterByDate('year', this)">This Year</div>
    </div>
    <div class="custom-date">
      <input type="date" id="startDate" value="<?php echo date('Y-m-01'); ?>">
      <span>to</span>
      <input type="date" id="endDate" value="<?php echo date('Y-m-d'); ?>">
      <button onclick="filterCustomDate()">Apply</button>
    </div>
  </div>
</section>

<!-- Summary Cards -->
<section class="stats-section">
  <div class="stats-grid">
    <div class="stat-card">
      <div class="stat-icon" style="background: #ff6b6b;">💰</div>
      <div class="stat-info">
        <p class="stat-label">Total Revenue</p>
        <h2 class="stat-value" id="totalRevenue">₱0.00</h2>
        
      </div>
    </div>

    <div class="stat-card">
      <div class="stat-icon" style="background: #4ecdc4;">📦</div>
      <div class="stat-info">
        <p class="stat-label">Total Orders</p>
        <h2 class="stat-value" id="totalOrders">0</h2>
        
      </div>
    </div>

    <div class="stat-card">
      <div class="stat-icon" style="background: #f9ca24;">☕</div>
      <div class="stat-info">
        <p class="stat-label">Items Sold</p>
        <h2 class="stat-value" id="totalItems">0</h2>
        
      </div>
    </div>

    <div class="stat-card">
      <div class="stat-icon" style="background: #6c5ce7;">📊</div>
      <div class="stat-info">
        <p class="stat-label">Average Order</p>
        <h2 class="stat-value" id="avgOrder">₱0.00</h2>
        
      </div>
    </div>
  </div>
</section>

<!-- Charts Section -->
<section class="charts-section">
  <div class="charts-grid">

    <!-- Sales Trend -->
    <div class="chart-card wide">
      <h3>📈 Sales Trend</h3>
      <div class="chart-container">
        <canvas id="salesChart"></canvas>
      </div>
    </div>

    <!-- Top Products -->
    <div class="chart-card">
      <h3>🏆 Top Selling Products</h3>
      <div class="product-list" id="topProducts">
        <!-- Will be populated by JS -->
      </div>
    </div>

  </div>
</section>

<!-- Detailed Tables -->
<section class="menu">
  <div class="table-container card">
    <h2>📋 Recent Orders</h2>
    <table>
      <thead>
        <tr>
          <th>Order ID</th>
          <th>Customer</th>
          <th>Date</th>
          <th>Items</th>
          <th>Total</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody id="recentOrders">
        <!-- Will be populated by JS -->
      </tbody>
    </table>
  </div>

  <div class="table-container card" style="margin-top: 30px;">
    <h2>☕ Product Performance</h2>
    <table>
      <thead>
        <tr>
          <th>Product</th>
          <th>Category</th>
          <th>Units Sold</th>
          <th>Revenue</th>
          <th>Popularity</th>
        </tr>
      </thead>
      <tbody id="productPerformance">
        <!-- Will be populated by JS -->
      </tbody>
    </table>
  </div>
</section>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
<script src="analytics_script.js"></script>
</body>
</html>

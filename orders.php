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
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Order Processing - Kape Muna</title>
  <link rel="stylesheet" href="order_processing_style.css">
</head>
<body>

<!-- Navbar -->
<header class="navbar">
  <h2 class="logo">Kape Muna</h2>
  <nav>
    <a href="home.php">Home</a>
    <a href="menu.php">Menu</a>

    <?php if (in_array($role, ['manager','admin'])): ?>
      <a href="analytics.php">Analytics</a>
    <?php endif; ?>

    <a href="orders.php">Orders</a>
    
  </nav>
</header>

<!-- Hero Section -->
<section class="hero">
  <h1>Order Processing<br><span>Manage customer orders</span></h1>
</section>

<!-- Main Container -->
<div class="container">

  <!-- New Order Form Card -->
  <div class="card form-card">
    <h2>📝 New Order</h2>

    <div class="form-row">
      <div class="form-group">
        <label>Customer Name <span class="required">*</span></label>
        <input type="text" id="customerName" placeholder="Enter customer name" required>
      </div>

      <div class="form-group">
        <label>Date</label>
        <input type="text" id="orderDate" readonly>
      </div>
    </div>

    <div class="form-row">
      <div class="form-group">
        <label>Source</label>
        <select id="source">
          <option value="Online">Online</option>
          <option value="In-Store">In-Store</option>
        </select>
      </div>

      <div class="form-group">
        <label>Payment Method</label>
        <select id="payment">
          <option value="Cash">Cash</option>
          <option value="GCash">GCash</option>
          <option value="PayMaya">PayMaya</option>
          <option value="Online Card">Online Card</option>
        </select>
      </div>
    </div>

    <button class="btn-create-order" onclick="createOrder()">Create Order</button>
  </div>

  <!-- Add Items Section (Hidden until order created) -->
  <div class="card add-items-card" id="addItemsCard" style="display: none;">
    <h2>🛒 Add Items to Order <span id="currentOrderNumber"></span></h2>

    <div class="form-row">
      <div class="form-group" style="flex: 2;">
        <label>Menu Item <span class="required">*</span></label>
        <select id="menuItem">
          <option value="">-- Select Item --</option>
          <?php
            $q = mysqli_query($conn, "SELECT item_id, item_name, price FROM menu_items WHERE is_available = 1 ORDER BY item_name ASC");
            if ($q && mysqli_num_rows($q) > 0) {
              while ($row = mysqli_fetch_assoc($q)) {
                $id = (int)$row['item_id'];
                $name = htmlspecialchars($row['item_name'], ENT_QUOTES);
                $price = number_format((float)$row['price'], 2, '.', '');
                echo "<option value='{$id}' data-name='{$name}' data-price='{$price}'>{$name} - ₱{$price}</option>";
              }
            }
          ?>
        </select>
      </div>

      <div class="form-group">
        <label>Quantity</label>
        <input type="number" id="quantity" value="1" min="1" max="99">
      </div>
    </div>

    <div class="button-group">
      <button class="btn-add-item" onclick="addItemToOrder()">+ Add Item</button>
      <button class="btn-finish-order" onclick="finishOrder()">Finish Order</button>
    </div>

    <!-- Current Order Items -->
    <div class="current-items" id="currentItems"></div>
    <div class="order-total" id="orderTotal">Total: ₱0.00</div>
  </div>

  <!-- Orders Table -->
  <div class="card table-card">
    <div class="table-header">
      <h2>📋 Current Orders</h2>
      <div class="tabs">
        <button class="tab-btn active" onclick="showTab('current')">Current</button>
        <button class="tab-btn" onclick="showTab('history')">History</button>
      </div>
    </div>

    <!-- Current Orders Tab -->
    <div id="currentTab" class="tab-content active">
      <table id="ordersTable">
        <thead>
          <tr>
            <th>Order ID</th>
            <th>Time</th>
            <th>Customer</th>
            <th>Items</th>
            <th>Total</th>
            <th>Status</th>
            <th>Source</th>
            <th>Payment</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
      <div id="pagination" class="pagination"></div>
    </div>

    <!-- History Tab -->
    <div id="historyTab" class="tab-content">
      <table id="historyTable">
        <thead>
          <tr>
            <th>Order ID</th>
            <th>Date</th>
            <th>Customer</th>
            <th>Items</th>
            <th>Total</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
      <div id="historyPagination" class="pagination"></div>
    </div>
  </div>

</div>

<!-- Order Details Modal -->
<div id="orderModal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="closeModal()">&times;</span>
    <h3 id="modalTitle">Order Details</h3>
    <div id="modalBody"></div>
  </div>
</div>

<!-- Receipt Modal -->
<div id="receiptModal" class="modal">
  <div class="modal-content receipt-modal">
    <span class="close" onclick="closeReceiptModal()">&times;</span>
    <div id="receiptContent"></div>
    <div style="text-align:center; margin-top:40px;">
      <button class="btn-print" onclick="window.print()">Print / Save as PDF</button>
      <button class="btn-close-receipt" onclick="closeReceiptModal()">Close</button>
    </div>
  </div>
</div>

<!-- Toast Container -->
<div id="toastContainer" class="toast-container"></div>

<script src="order_processing_script.js"></script>
</body>
</html>

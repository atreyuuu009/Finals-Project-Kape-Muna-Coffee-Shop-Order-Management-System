<?php
// ==========================================
// get_orders.php
// ==========================================
?>
<?php
require 'connect.php';
header('Content-Type: application/json');

$status = $_GET['status'] ?? 'current';

if ($status === 'current') {
  // Get orders with status Pending, In Progress, Payment Received
  $sql = "SELECT o.*, 
          (SELECT COUNT(*) FROM order_items WHERE order_id = o.id) as item_count
          FROM orders o
          WHERE o.status IN ('Pending', 'In Progress', 'Payment Received')
          ORDER BY o.order_date DESC, o.order_time DESC";
} else {
  // Get completed, cancelled, or failed orders
  $sql = "SELECT o.*,
          (SELECT COUNT(*) FROM order_items WHERE order_id = o.id) as item_count
          FROM orders o
          WHERE o.status IN ('Completed', 'Cancelled', 'Payment Failed')
          ORDER BY o.order_date DESC, o.order_time DESC";
}

$result = $conn->query($sql);

$orders = [];
if ($result && $result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
  }
}

echo json_encode([
  'success' => true,
  'orders' => $orders
]);

$conn->close();
?>
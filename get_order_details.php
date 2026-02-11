<?php
// ==========================================
// get_order_details.php
// ==========================================
?>
<?php
require 'connect.php';
header('Content-Type: application/json');

$order_id = (int)$_GET['order_id'];

// Get order
$order_sql = "SELECT * FROM orders WHERE id = $order_id";
$order_result = $conn->query($order_sql);

if (!$order_result || $order_result->num_rows === 0) {
  echo json_encode(['success' => false, 'message' => 'Order not found']);
  exit;
}

$order = $order_result->fetch_assoc();

// Get items
$items_sql = "SELECT * FROM order_items WHERE order_id = $order_id";
$items_result = $conn->query($items_sql);

$items = [];
if ($items_result && $items_result->num_rows > 0) {
  while ($row = $items_result->fetch_assoc()) {
    $items[] = $row;
  }
}

echo json_encode([
  'success' => true,
  'order' => $order,
  'items' => $items
]);

$conn->close();
?>

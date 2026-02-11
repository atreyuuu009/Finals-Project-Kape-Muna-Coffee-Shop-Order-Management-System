<?php
// ==========================================
// finish_order.php
// ==========================================
?>
<?php
require 'connect.php';
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

if (!$input || empty($input['order_id'])) {
  echo json_encode(['success' => false, 'message' => 'Order ID required']);
  exit;
}

$order_id = (int)$input['order_id'];

// Calculate total and build summary
$items_query = "SELECT item_name, quantity, unit_price FROM order_items WHERE order_id = $order_id";
$items_result = $conn->query($items_query);

if (!$items_result || $items_result->num_rows === 0) {
  echo json_encode(['success' => false, 'message' => 'No items in order']);
  exit;
}

$total = 0;
$summary_parts = [];

while ($row = $items_result->fetch_assoc()) {
  $item_total = $row['quantity'] * $row['unit_price'];
  $total += $item_total;
  $summary_parts[] = $row['item_name'] . ' x' . $row['quantity'];
}

$summary = implode(', ', $summary_parts);

// Update order
$update_sql = "UPDATE orders SET 
               order_summary = '" . $conn->real_escape_string($summary) . "',
               total_price = $total
               WHERE id = $order_id";

if ($conn->query($update_sql) === TRUE) {
  echo json_encode(['success' => true, 'message' => 'Order completed']);
} else {
  echo json_encode(['success' => false, 'message' => 'Failed to finish order']);
}

$conn->close();
?>

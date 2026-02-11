<?php
require 'connect.php';
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

if (!$input || empty($input['customer'])) {
  echo json_encode(['success' => false, 'message' => 'Customer name is required']);
  exit;
}

$customer = $conn->real_escape_string(trim($input['customer']));
$source = $conn->real_escape_string($input['source'] ?? 'Online');
$payment = $conn->real_escape_string($input['payment'] ?? 'Cash');

$order_date = date('Y-m-d');
$order_time = date('H:i:s');

// Insert order with empty summary (will update after adding items)
$sql = "INSERT INTO orders (
  order_number,
  order_date,
  order_time,
  customer_name,
  order_summary,
  status,
  source,
  payment,
  total_price
) VALUES (
  '',
  '$order_date',
  '$order_time',
  '$customer',
  'Pending items...',
  'Pending',
  '$source',
  '$payment',
  0
)";

if ($conn->query($sql) === TRUE) {
  $order_id = $conn->insert_id;
  
  // Generate order number: ORD-000001
  $order_number = 'ORD-' . str_pad($order_id, 6, '0', STR_PAD_LEFT);
  
  // Update order number
  $update_sql = "UPDATE orders SET order_number = '$order_number' WHERE id = $order_id";
  $conn->query($update_sql);
  
  echo json_encode([
    'success' => true,
    'message' => 'Order created successfully',
    'order_id' => $order_id,
    'order_number' => $order_number
  ]);
} else {
  echo json_encode(['success' => false, 'message' => 'Failed to create order: ' . $conn->error]);
}

$conn->close();
?>
<?php
require 'connect.php';
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

if (!$input || empty($input['order_id']) || empty($input['item_id']) || empty($input['quantity'])) {
  echo json_encode(['success' => false, 'message' => 'Missing required fields']);
  exit;
}

$order_id = (int)$input['order_id'];
$item_id = (int)$input['item_id'];
$quantity = (int)$input['quantity'];

if ($quantity < 1) {
  echo json_encode(['success' => false, 'message' => 'Invalid quantity']);
  exit;
}

// Get item details from menu_items
$res = $conn->query("SELECT item_name, price FROM menu_items WHERE item_id = $item_id AND is_available = 1");
if (!$res || $res->num_rows === 0) {
  echo json_encode(['success' => false, 'message' => 'Invalid or unavailable menu item']);
  exit;
}

$itemRow = $res->fetch_assoc();
$item_name = $conn->real_escape_string($itemRow['item_name']);
$unit_price = (float)$itemRow['price'];

// Insert order item
$sql = "INSERT INTO order_items (order_id, item_id, item_name, quantity, unit_price)
        VALUES ($order_id, $item_id, '$item_name', $quantity, $unit_price)";

if ($conn->query($sql) === TRUE) {
  $item_id_inserted = $conn->insert_id;
  
  echo json_encode([
    'success' => true,
    'message' => 'Item added successfully',
    'item_id' => $item_id_inserted
  ]);
} else {
  echo json_encode(['success' => false, 'message' => 'Failed to add item: ' . $conn->error]);
}

$conn->close();
?>
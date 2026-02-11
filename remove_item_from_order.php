
<?php
// ==========================================
// remove_item_from_order.php
// ==========================================
?>
<?php
require 'connect.php';
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

if (!$input || empty($input['item_id'])) {
  echo json_encode(['success' => false, 'message' => 'Item ID required']);
  exit;
}

$item_id = (int)$input['item_id'];

$sql = "DELETE FROM order_items WHERE id = $item_id";

if ($conn->query($sql) === TRUE) {
  echo json_encode(['success' => true, 'message' => 'Item removed']);
} else {
  echo json_encode(['success' => false, 'message' => 'Failed to remove item']);
}

$conn->close();
?>


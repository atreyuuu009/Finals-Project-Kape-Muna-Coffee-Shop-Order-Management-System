<?php
// ==========================================
// update_status.php
// ==========================================
?>
<?php
require 'connect.php';
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

if (!$input || empty($input['order_id']) || empty($input['status'])) {
  echo json_encode(['success' => false, 'message' => 'Missing fields']);
  exit;
}

$order_id = (int)$input['order_id'];
$status = $conn->real_escape_string($input['status']);

$sql = "UPDATE orders SET status = '$status' WHERE id = $order_id";

if ($conn->query($sql) === TRUE) {
  echo json_encode(['success' => true, 'message' => 'Status updated']);
} else {
  echo json_encode(['success' => false, 'message' => 'Failed to update status']);
}
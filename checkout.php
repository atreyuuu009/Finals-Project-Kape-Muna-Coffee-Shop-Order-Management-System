<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');
include 'db.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(["success" => false, "error" => "No data received"]);
    exit;
}

$user_id = 1; // placeholder until you add login
$subtotal = $data['subtotal'];
$delivery_fee = $data['delivery'];
$total = $data['total'];
$items = $data['items'];

$sql = "INSERT INTO orders (user_id, subtotal, delivery_fee, total) 
        VALUES ($user_id, $subtotal, $delivery_fee, $total)";
if (mysqli_query($conn, $sql)) {
    $order_id = mysqli_insert_id($conn);

    foreach ($items as $item) {
        $item_id = (int)$item['id'];
        $quantity = (int)$item['qty'];
        $price = (float)$item['price'];

        $sql_item = "INSERT INTO order_items (order_id, item_id, quantity, price) 
                     VALUES ($order_id, $item_id, $quantity, $price)";
        mysqli_query($conn, $sql_item);
    }

    echo json_encode(["success" => true, "order_id" => $order_id]);
} else {
    echo json_encode(["success" => false, "error" => mysqli_error($conn)]);
}
?>

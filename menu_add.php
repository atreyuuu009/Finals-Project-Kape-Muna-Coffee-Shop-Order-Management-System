<?php
// menu_add.php
include 'db.php';

/*
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: index.php");
  exit;
}
*/

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header("Location: menu_admin.php");
  exit;
}

$name = $_POST['item_name'] ?? '';
$desc = $_POST['description'] ?? '';
$price = (float)($_POST['price'] ?? 0);
$cat = (int)($_POST['category_id'] ?? 0);
$available = isset($_POST['is_available']) ? 1 : 0;

$imagePath = '';

if (!empty($_FILES["image"]["name"])) {
  $targetDir = "images/";
  if (!is_dir($targetDir)) { @mkdir($targetDir, 0755, true); }

  $ext = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
  $allowed = ["jpg", "jpeg", "png", "webp"];

  if (in_array($ext, $allowed)) {
    $fileName = uniqid("menu_", true) . "." . $ext;
    $targetFilePath = $targetDir . $fileName;

    if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
      $imagePath = $targetFilePath;
    }
  }
}

$stmt = $conn->prepare("
  INSERT INTO menu_items (item_name, description, price, image_url, category_id, is_available)
  VALUES (?, ?, ?, ?, ?, ?)
");
$stmt->bind_param("ssdsii", $name, $desc, $price, $imagePath, $cat, $available);
$stmt->execute();
$stmt->close();

header("Location: menu_admin.php");
exit;

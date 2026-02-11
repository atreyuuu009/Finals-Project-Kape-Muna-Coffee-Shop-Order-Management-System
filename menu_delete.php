<?php
// menu_delete.php
include 'db.php';

/*
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: index.php");
  exit;
}
*/

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
  header("Location: menu_admin.php");
  exit;
}

$stmt = $conn->prepare("DELETE FROM menu_items WHERE item_id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();

header("Location: menu_admin.php");
exit;

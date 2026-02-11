<?php
// menu_edit.php
include 'db.php';

/*
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: index.php");
  exit;
}
*/

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { die("Invalid menu item ID."); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = $_POST['item_name'] ?? '';
  $desc = $_POST['description'] ?? '';
  $price = (float)($_POST['price'] ?? 0);
  $cat = (int)($_POST['category_id'] ?? 0);
  $available = isset($_POST['is_available']) ? 1 : 0;

  $imagePath = $_POST['current_image'] ?? '';

  // Upload (optional)
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
    UPDATE menu_items
    SET item_name=?, description=?, price=?, image_url=?, category_id=?, is_available=?
    WHERE item_id=?
  ");
  $stmt->bind_param("ssdsiii", $name, $desc, $price, $imagePath, $cat, $available, $id);
  $stmt->execute();
  $stmt->close();

  header("Location: menu_admin.php");
  exit;
}

// Fetch item
$stmt = $conn->prepare("SELECT * FROM menu_items WHERE item_id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$item = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$item) { die("Menu item not found."); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Menu Item</title>
  <style>
    body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background: #faf7f3;
      color: #3e2723;
      padding: 20px;
    }

    h2 {
      text-align: center;
      color: #3e2723;
      margin-bottom: 20px;
      font-size: 30px;
    }

    .card {
      background: white;
      border-radius: 20px;
      padding: 20px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.1);
      margin-bottom: 30px;
    }

    .back-btn {
      display: inline-block;
      background: #5d4037;
      color: white;
      text-decoration: none;
      padding: 8px 14px;
      border-radius: 20px;
      margin-bottom: 15px;
    }

    .back-btn:hover {
      background: #4e342e;
    }

    .form-group {
      display: flex;
      align-items: center;
      margin: 8px 0;
    }

    .form-group label {
      width: 150px;
      font-weight: 600;
      margin-right: 10px;
    }

    .form-group input, .form-group textarea, .form-group select {
      flex: 1;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 8px;
    }

    .form-group textarea {
      resize: vertical;
      min-height: 60px;
    }

    .checkbox-group {
      display: flex;
      align-items: center;
      margin: 8px 0;
    }

    .checkbox-group label {
      margin-left: 5px;
    }

    button {
      background: #6d4c41;
      color: white;
      border: none;
      padding: 10px 20px;
      border-radius: 20px;
      cursor: pointer;
      transition: all 0.3s ease;
      font-weight: 600;
      box-shadow: 0 4px 8px rgba(0,0,0,0.15);
      display: inline-block;
      width: auto !important;
    }

    button:hover {
      background: #5d4037;
      transform: translateY(-2px);
      box-shadow: 0 6px 12px rgba(0,0,0,0.2);
    }

    button:active {
      transform: translateY(0);
      box-shadow: 0 3px 6px rgba(0,0,0,0.15);
    }

    .menu-img {
      width: 250px;
      max-width: 100%;
      border-radius: 12px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.2);
    }
  </style>
</head>
<body>

<h2>✏️ Edit Menu Item</h2>
<a href="menu_admin.php" class="back-btn">⬅ Back to Manage Menu Items</a>

<div class="card">
  <form method="POST" enctype="multipart/form-data">
    <div class="form-group">
      <label for="item_name">Item Name:</label>
      <input type="text" id="item_name" name="item_name" value="<?php echo htmlspecialchars($item['item_name']); ?>" required>
    </div>

    <div class="form-group">
      <label for="description">Description:</label>
      <textarea id="description" name="description"><?php echo htmlspecialchars($item['description']); ?></textarea>
    </div>

    <div class="form-group">
      <label for="price">Price:</label>
      <input type="number" id="price" step="0.01" name="price" value="<?php echo htmlspecialchars($item['price']); ?>" required>
    </div>

    <div style="text-align: center; margin: 15px 0;">
      <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="Current Image" class="menu-img">
    </div>

    <input type="hidden" name="current_image" value="<?php echo htmlspecialchars($item['image_url']); ?>">

    <div class="form-group">
      <label for="image">New Image:</label>
      <input type="file" id="image" name="image" accept=".jpg,.jpeg,.png,.webp">
    </div>

    <div class="form-group">
      <label for="category_id">Category:</label>
      <select id="category_id" name="category_id">
        <?php
        $catRes = mysqli_query($conn, "SELECT category_id, category_name FROM categories ORDER BY category_name ASC");
        while ($c = mysqli_fetch_assoc($catRes)) {
          $cid = (int)$c['category_id'];
          $cname = htmlspecialchars($c['category_name']);
          $selected = ($cid == (int)$item['category_id']) ? "selected" : "";
          echo "<option value='{$cid}' {$selected}>{$cname}</option>";
        }
        ?>
      </select>
    </div>

    <div class="checkbox-group">
      <input type="checkbox" id="is_available" name="is_available" <?php echo ((int)$item['is_available'] === 1) ? "checked" : ""; ?>>
      <label for="is_available">Available</label>
    </div>

    <button type="submit">Update Item</button>
  </form>
</div>

</body>
</html>

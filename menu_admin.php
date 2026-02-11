<?php
// menu_admin.php
include 'db.php';

/*
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: index.php");
  exit;
}
*/
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Menu Admin</title>
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

    table {
      width: 100%;
      border-collapse: collapse;
    }

    th, td {
      padding: 12px;
      text-align: center;
      border-bottom: 1px solid #eee;
    }

    th {
      background: #f0e5d8;
      color: #3e2723;
    }

    tr:hover {
      background: #faf3e7;
    }

    .available {
      color: green;
      font-weight: bold;
    }

    .not-available {
      color: red;
      font-weight: bold;
    }

    .action-btn {
      display: inline-block;
      background: #5d4037;
      color: white;
      text-decoration: none;
      padding: 6px 12px;
      border-radius: 20px;
      margin: 2px;
      font-size: 14px;
      transition: background 0.3s ease;
    }

    .action-btn:hover {
      background: #4e342e;
    }

    .menu-img {
      width: 80px;
      height: 80px;
      object-fit: cover;
      border-radius: 12px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
  </style>
</head>
<body>

<h2>☕ Manage Menu Items</h2>
<a href="menu.php" class="back-btn">⬅ Back</a>

<div class="card">
  <h3>Add New Item</h3>
  <form action="menu_add.php" method="POST" enctype="multipart/form-data">
    <div class="form-group">
      <label for="item_name">Item Name:</label>
      <input type="text" id="item_name" name="item_name" placeholder="Item name" required>
    </div>
    <div class="form-group">
      <label for="description">Description:</label>
      <textarea id="description" name="description" placeholder="Description"></textarea>
    </div>
    <div class="form-group">
      <label for="price">Price:</label>
      <input type="number" id="price" step="0.01" name="price" placeholder="Price" required>
    </div>
    <div class="form-group">
      <label for="image">Image:</label>
      <input type="file" id="image" name="image" required>
    </div>
    <div class="form-group">
      <label for="category_id">Category:</label>
      <select id="category_id" name="category_id" required>
        <?php
        $catRes = mysqli_query($conn, "SELECT category_id, category_name FROM categories ORDER BY category_name ASC");
        while ($c = mysqli_fetch_assoc($catRes)) {
          $cid = (int)$c['category_id'];
          $cname = htmlspecialchars($c['category_name']);
          echo "<option value='{$cid}'>{$cname}</option>";
        }
        ?>
      </select>
    </div>
    <div class="checkbox-group">
      <input type="checkbox" id="is_available" name="is_available" checked>
      <label for="is_available">Available</label>
    </div>
    <button type="submit">➕ Add Item</button>
  </form>
</div>

<div class="card">
  <h3>Menu Items</h3>
  <table>
    <tr>
      <th>Image</th>
      <th>Name</th>
      <th>Description</th>
      <th>Price</th>
      <th>Category</th>
      <th>Available</th>
      <th>Actions</th>
    </tr>

    <?php
    $sql = "SELECT m.item_id, m.item_name, m.description, m.price, m.image_url, m.is_available, c.category_name
            FROM menu_items m
            JOIN categories c ON m.category_id = c.category_id
            ORDER BY m.item_id DESC";
    $result = mysqli_query($conn, $sql);

    while ($row = mysqli_fetch_assoc($result)) {
      $id = (int)$row['item_id'];
      $name = htmlspecialchars($row['item_name']);
      $desc = htmlspecialchars($row['description']);
      $price = htmlspecialchars($row['price']);
      $catName = htmlspecialchars($row['category_name']);
      $img = htmlspecialchars($row['image_url']);
      $avail = (int)$row['is_available'];

      $availText = $avail ? "<span class='available'>Yes</span>" : "<span class='not-available'>No</span>";

      echo "<tr>
        <td><img src='{$img}' alt='{$name}' class='menu-img'></td>
        <td>{$name}</td>
        <td>{$desc}</td>
        <td>₱{$price}</td>
        <td>{$catName}</td>
        <td>{$availText}</td>
        <td>
          <a href='menu_edit.php?id={$id}' class='action-btn'>✏️ Edit</a>
          <a href='menu_delete.php?id={$id}' class='action-btn' onclick=\"return confirm('Delete this item?');\">🗑️ Delete</a>
        </td>
      </tr>";
    }
    ?>
  </table>
</div>

</body>
</html>

<?php
require "db.php";

if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit;
}

$role = $_SESSION["user"]["role"] ?? null;
if ($role === null) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Kape Muna</title>
  <link rel="stylesheet" href="menu-style.css">
  <style>
    .payment-method {
      margin-top: 20px;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 8px;
    }
    .payment-method h3 { margin-bottom: 10px; }
    .payment-method label { display: block; margin: 5px 0; }

    /* Global button style */
    button {
      background: #6d4c41;
      color: #fff;
      border: none;
      padding: 8px 16px;
      border-radius: 8px;
      cursor: pointer;
      transition: 0.3s;
      display: inline-block;
      width: auto !important;
    }
    button:hover { background: #5d4037; }

    .checkout-container { text-align: center; margin-top: 15px; }

    /* ====== HIDE ELEMENTS (NO FUNCTION CHANGES) ====== */

    /* Hide Add to Cart buttons */
    .menu .card button {
      display: none;
    }

    /* Hide Process Order section */
    section.cart {
      display: none;
    }
  </style>
</head>
<body>

<header class="navbar">
  <h2 class="logo">Kape Muna</h2>
  <nav>
    <a href="home.php">Home</a>

    <?php if (in_array($role, ['admin','manager'])): ?>
      <a href="menu_admin.php">Add Menu</a>
    <?php endif; ?>


  </nav>
</header>

<section class="hero">
  <h1>Masarap ang kape<br><span>pag may kasama</span></h1>
  <div class="search-box">
    <input type="text" id="searchInput" placeholder="Search coffee..." oninput="searchMenu(this.value)">
    <button onclick="searchMenuButton()">Search</button>
  </div>
  <div class="categories">
    <div class="category-pill active" onclick="filterCategory('All', this)">All</div>
    <div class="category-pill" onclick="filterCategory('Hot Coffee', this)">☕ Hot Coffee</div>
    <div class="category-pill" onclick="filterCategory('Cold Coffee', this)">🧊 Cold Coffee</div>
    <div class="category-pill" onclick="filterCategory('Tea', this)">🍵 Tea</div>
    <div class="category-pill" onclick="filterCategory('Dessert', this)">🍰 Dessert</div>
  </div>
</section>

<section class="menu">
  <h2>Menu</h2>
  <div class="menu-grid">
    <?php
    $sql = "SELECT m.item_id, m.item_name, m.description, m.price, m.image_url, c.category_name
            FROM menu_items m
            JOIN categories c ON m.category_id = c.category_id
            WHERE m.is_available = 1
            ORDER BY m.item_id DESC";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $id = $row['item_id'];
            $name = htmlspecialchars($row['item_name'], ENT_QUOTES);
            $price = number_format((float)$row['price'], 2, '.', '');
            $category = htmlspecialchars($row['category_name'], ENT_QUOTES);
            $image = htmlspecialchars($row['image_url'], ENT_QUOTES);
            $desc = htmlspecialchars($row['description'], ENT_QUOTES);

            echo "
            <div class='card' data-category='$category'>
              <div class='image'>
                <img src='$image' alt='$name'>
              </div>
              <h3>$name</h3>
              <p>$desc</p>
              <p class='price'>₱$price</p>
              <button onclick=\"addToCart($id, '$name', $price, '$category')\">Add to Cart</button>
            </div>";
        }
    } else {
        echo "<p>No items found.</p>";
    }
    ?>
  </div>
</section>

<script src="menu-script.js"></script>
</body>
</html>

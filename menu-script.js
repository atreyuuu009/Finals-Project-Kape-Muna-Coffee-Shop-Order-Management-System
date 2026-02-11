let cart = [];
let currentCategory = "All";
let searchQuery = "";
const deliveryFee = 50;

function renderMenu() {
  const cards = document.querySelectorAll(".menu-grid .card");
  cards.forEach(card => {
    const category = card.getAttribute("data-category");
    const name = card.querySelector("h3").textContent.toLowerCase();
    let show = true;
    if (currentCategory !== "All" && category !== currentCategory) show = false;
    if (searchQuery !== "" && !name.includes(searchQuery.toLowerCase())) show = false;
    card.style.display = show ? "block" : "none";
  });
}

function filterCategory(category, el) {
  currentCategory = category;
  document.querySelectorAll(".category-pill").forEach(pill => pill.classList.remove("active"));
  el.classList.add("active");
  renderMenu();
}

function searchMenu(value) {
  searchQuery = value;
  renderMenu();
}

function searchMenuButton() {
  const value = document.getElementById("searchInput").value;
  searchQuery = value;
  renderMenu();
}

function addToCart(id, name, price, category) {
  const item = cart.find(i => i.id === id);
  if (item) {
    item.qty++;
  } else {
    cart.push({ id, name, price: parseFloat(price), category, qty: 1 });
  }
  renderCart();
}

function removeFromCart(id) {
  cart = cart.filter(i => i.id !== id);
  renderCart();
}

function changeQty(id, qty) {
  const item = cart.find(i => i.id === id);
  if (!item) return;
  item.qty = parseInt(qty);
  if (item.qty <= 0) removeFromCart(id);
  renderCart();
}

function renderCart() {
  const tbody = document.getElementById("cartItems");
  tbody.innerHTML = "";
  let subtotal = 0;

  cart.forEach(item => {
    const total = item.qty * item.price;
    subtotal += total;
    tbody.innerHTML += `
      <tr>
        <td>${item.name}<br><small>${item.category}</small></td>
        <td><input type="number" min="1" value="${item.qty}" onchange="changeQty(${item.id}, this.value)"></td>
        <td>₱${item.price.toFixed(2)}</td>
        <td>₱${total.toFixed(2)}</td>
        <td><button onclick="removeFromCart(${item.id})">Remove</button></td>
      </tr>
    `;
  });

  document.getElementById("subtotal").textContent = `₱${subtotal.toFixed(2)}`;
  document.getElementById("delivery").textContent = `₱${deliveryFee.toFixed(2)}`;
  document.getElementById("total").textContent = `₱${(subtotal + deliveryFee).toFixed(2)}`;
}

function checkout() {
  if (cart.length === 0) {
    alert("Cart is empty!");
    return;
  }

  const subtotal = cart.reduce((sum, item) => sum + item.qty * item.price, 0);
  const total = subtotal + deliveryFee;

  alert(
    `Order placed!\nSubtotal: ₱${subtotal.toFixed(2)}\nDelivery: ₱${deliveryFee.toFixed(2)}\nTotal: ₱${total.toFixed(2)}`
  );

  // Clear cart after checkout
  cart = [];
  renderCart();
}

// Run menu filter once page loads
document.addEventListener("DOMContentLoaded", renderMenu);

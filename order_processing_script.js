// Global variables
let currentOrderId = null;
let currentOrderNumber = null;
let currentOrderItems = [];
let currentPageCurrent = 1;
let currentPageHistory = 1;
const itemsPerPage = 10;

// Initialize
document.addEventListener('DOMContentLoaded', function() {
  // Set current date
  document.getElementById('orderDate').value = new Date().toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
  
  loadOrders();
  loadHistory();
});

// ================================
// CREATE NEW ORDER
// ================================
async function createOrder() {
  const customerName = document.getElementById('customerName').value.trim();
  const source = document.getElementById('source').value;
  const payment = document.getElementById('payment').value;
  
  if (!customerName) {
    showToast('Please enter customer name', 'error');
    return;
  }
  
  try {
    const response = await fetch('create_order.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        customer: customerName,
        source: source,
        payment: payment
      })
    });
    
    const data = await response.json();
    
    if (data.success) {
      currentOrderId = data.order_id;
      currentOrderNumber = data.order_number;
      currentOrderItems = [];
      
      // Show add items section
      document.getElementById('addItemsCard').style.display = 'block';
      document.getElementById('currentOrderNumber').textContent = `(${currentOrderNumber})`;
      document.getElementById('currentItems').innerHTML = '<p style="text-align:center; color:#888; padding:20px;">No items added yet. Add your first item above.</p>';
      document.getElementById('orderTotal').textContent = 'Total: ₱0.00';
      
      // Scroll to add items section
      document.getElementById('addItemsCard').scrollIntoView({ behavior: 'smooth' });
      
      showToast('Order created! Now add items.', 'success');
    } else {
      showToast(data.message || 'Failed to create order', 'error');
    }
  } catch (error) {
    console.error('Error:', error);
    showToast('Error creating order', 'error');
  }
}

// ================================
// ADD ITEM TO CURRENT ORDER
// ================================
async function addItemToOrder() {
  if (!currentOrderId) {
    showToast('Please create an order first', 'error');
    return;
  }
  
  const menuSelect = document.getElementById('menuItem');
  const quantity = parseInt(document.getElementById('quantity').value);
  
  if (!menuSelect.value) {
    showToast('Please select a menu item', 'error');
    return;
  }
  
  const selectedOption = menuSelect.options[menuSelect.selectedIndex];
  const itemId = parseInt(menuSelect.value);
  const itemName = selectedOption.getAttribute('data-name');
  const price = parseFloat(selectedOption.getAttribute('data-price'));
  
  if (quantity < 1) {
    showToast('Quantity must be at least 1', 'error');
    return;
  }
  
  try {
    const response = await fetch('add_item_to_order.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        order_id: currentOrderId,
        item_id: itemId,
        quantity: quantity
      })
    });
    
    const data = await response.json();
    
    if (data.success) {
      // Add to local array
      currentOrderItems.push({
        id: data.item_id,
        item_id: itemId,
        name: itemName,
        quantity: quantity,
        price: price
      });
      
      // Reset form
      menuSelect.selectedIndex = 0;
      document.getElementById('quantity').value = 1;
      
      // Update display
      renderCurrentItems();
      showToast('Item added!', 'success');
    } else {
      showToast(data.message || 'Failed to add item', 'error');
    }
  } catch (error) {
    console.error('Error:', error);
    showToast('Error adding item', 'error');
  }
}

// ================================
// RENDER CURRENT ORDER ITEMS
// ================================
function renderCurrentItems() {
  const container = document.getElementById('currentItems');
  
  if (currentOrderItems.length === 0) {
    container.innerHTML = '<p style="text-align:center; color:#888; padding:20px;">No items added yet.</p>';
    document.getElementById('orderTotal').textContent = 'Total: ₱0.00';
    return;
  }
  
  let html = '';
  let total = 0;
  
  currentOrderItems.forEach((item, index) => {
    const itemTotal = item.quantity * item.price;
    total += itemTotal;
    
    html += `
      <div class="item-row">
        <div class="item-info">
          <div class="item-name">${item.name}</div>
          <div class="item-details">Qty: ${item.quantity} × ₱${item.price.toFixed(2)}</div>
        </div>
        <div class="item-price">₱${itemTotal.toFixed(2)}</div>
        <button class="btn-remove-item" onclick="removeItem(${index})">Remove</button>
      </div>
    `;
  });
  
  container.innerHTML = html;
  document.getElementById('orderTotal').textContent = `Total: ₱${total.toFixed(2)}`;
}

// ================================
// REMOVE ITEM FROM CURRENT ORDER
// ================================
async function removeItem(index) {
  const item = currentOrderItems[index];
  
  if (!confirm(`Remove ${item.name} from order?`)) {
    return;
  }
  
  try {
    const response = await fetch('remove_item_from_order.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        item_id: item.id
      })
    });
    
    const data = await response.json();
    
    if (data.success) {
      currentOrderItems.splice(index, 1);
      renderCurrentItems();
      showToast('Item removed', 'info');
    } else {
      showToast(data.message || 'Failed to remove item', 'error');
    }
  } catch (error) {
    console.error('Error:', error);
    showToast('Error removing item', 'error');
  }
}

// ================================
// FINISH ORDER
// ================================
async function finishOrder() {
  if (currentOrderItems.length === 0) {
    showToast('Please add at least one item', 'error');
    return;
  }
  
  if (!confirm('Finish this order?')) {
    return;
  }
  
  try {
    const response = await fetch('finish_order.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        order_id: currentOrderId
      })
    });
    
    const data = await response.json();
    
    if (data.success) {
      showToast('Order completed successfully!', 'success');
      
      // Reset form
      document.getElementById('customerName').value = '';
      document.getElementById('source').selectedIndex = 0;
      document.getElementById('payment').selectedIndex = 0;
      document.getElementById('addItemsCard').style.display = 'none';
      
      currentOrderId = null;
      currentOrderNumber = null;
      currentOrderItems = [];
      
      // Reload orders table
      loadOrders();
    } else {
      showToast(data.message || 'Failed to finish order', 'error');
    }
  } catch (error) {
    console.error('Error:', error);
    showToast('Error finishing order', 'error');
  }
}

// ================================
// LOAD ORDERS
// ================================
async function loadOrders() {
  try {
    const response = await fetch('get_orders.php?status=current');
    const data = await response.json();
    
    if (data.success) {
      renderOrdersTable(data.orders);
    }
  } catch (error) {
    console.error('Error loading orders:', error);
  }
}

// ================================
// RENDER ORDERS TABLE
// ================================
function renderOrdersTable(orders) {
  const tbody = document.querySelector('#ordersTable tbody');
  
  if (!orders || orders.length === 0) {
    tbody.innerHTML = '<tr><td colspan="9" style="text-align:center; padding:30px; color:#888;">No current orders</td></tr>';
    renderPaginationCurrent(0);
    return;
  }
  
  // Pagination
  const start = (currentPageCurrent - 1) * itemsPerPage;
  const end = start + itemsPerPage;
  const paginatedOrders = orders.slice(start, end);
  
  let html = '';
  paginatedOrders.forEach(order => {
    html += `
      <tr>
        <td><strong>${order.order_number}</strong></td>
        <td>${order.order_time}</td>
        <td>${order.customer_name}</td>
        <td>${order.item_count} items</td>
        <td><strong>₱${parseFloat(order.total_price).toFixed(2)}</strong></td>
        <td>
          <select class="status-select" onchange="updateStatus(${order.id}, this.value)">
            <option value="Pending" ${order.status === 'Pending' ? 'selected' : ''}>Pending</option>
            <option value="In Progress" ${order.status === 'In Progress' ? 'selected' : ''}>In Progress</option>
            <option value="Payment Received" ${order.status === 'Payment Received' ? 'selected' : ''}>Payment Received</option>
            <option value="Completed" ${order.status === 'Completed' ? 'selected' : ''}>Completed</option>
            <option value="Cancelled" ${order.status === 'Cancelled' ? 'selected' : ''}>Cancelled</option>
            <option value="Payment Failed" ${order.status === 'Payment Failed' ? 'selected' : ''}>Payment Failed</option>
          </select>
        </td>
        <td>${order.source}</td>
        <td>${order.payment}</td>
        <td>
          <button class="btn-view" onclick="viewOrderDetails(${order.id})">View</button>
        </td>
      </tr>
    `;
  });
  
  tbody.innerHTML = html;
  renderPaginationCurrent(orders.length);
}

// ================================
// LOAD HISTORY
// ================================
async function loadHistory() {
  try {
    const response = await fetch('get_orders.php?status=history');
    const data = await response.json();
    
    if (data.success) {
      renderHistoryTable(data.orders);
    }
  } catch (error) {
    console.error('Error loading history:', error);
  }
}

// ================================
// RENDER HISTORY TABLE
// ================================
function renderHistoryTable(orders) {
  const tbody = document.querySelector('#historyTable tbody');
  
  if (!orders || orders.length === 0) {
    tbody.innerHTML = '<tr><td colspan="7" style="text-align:center; padding:30px; color:#888;">No order history</td></tr>';
    renderPaginationHistory(0);
    return;
  }
  
  // Pagination
  const start = (currentPageHistory - 1) * itemsPerPage;
  const end = start + itemsPerPage;
  const paginatedOrders = orders.slice(start, end);
  
  let html = '';
  paginatedOrders.forEach(order => {
    const statusClass = `status-${order.status.toLowerCase().replace(/\s+/g, '-')}`;
    html += `
      <tr>
        <td><strong>${order.order_number}</strong></td>
        <td>${order.order_date}</td>
        <td>${order.customer_name}</td>
        <td>${order.item_count} items</td>
        <td><strong>₱${parseFloat(order.total_price).toFixed(2)}</strong></td>
        <td><span class="status-badge ${statusClass}">${order.status}</span></td>
        <td>
          <button class="btn-receipt" onclick="generateReceipt(${order.id})">Receipt</button>
        </td>
      </tr>
    `;
  });
  
  tbody.innerHTML = html;
  renderPaginationHistory(orders.length);
}

// ================================
// TAB SWITCHING
// ================================
function showTab(tab) {
  const tabs = document.querySelectorAll('.tab-btn');
  const contents = document.querySelectorAll('.tab-content');
  
  tabs.forEach(t => t.classList.remove('active'));
  contents.forEach(c => c.classList.remove('active'));
  
  if (tab === 'current') {
    tabs[0].classList.add('active');
    document.getElementById('currentTab').classList.add('active');
  } else {
    tabs[1].classList.add('active');
    document.getElementById('historyTab').classList.add('active');
  }
}

// ================================
// PAGINATION
// ================================
function renderPaginationCurrent(totalItems) {
  const totalPages = Math.ceil(totalItems / itemsPerPage);
  const container = document.getElementById('pagination');
  
  if (!container) return;
  
  if (totalPages <= 1) {
    container.innerHTML = '';
    return;
  }
  
  container.innerHTML = `
    <button onclick="changePageCurrent(${currentPageCurrent - 1})" ${currentPageCurrent === 1 ? 'disabled' : ''}>← Previous</button>
    <span style="padding: 0 15px; font-weight: 600;">Page ${currentPageCurrent} of ${totalPages}</span>
    <button onclick="changePageCurrent(${currentPageCurrent + 1})" ${currentPageCurrent === totalPages ? 'disabled' : ''}>Next →</button>
  `;
}

function renderPaginationHistory(totalItems) {
  const totalPages = Math.ceil(totalItems / itemsPerPage);
  const container = document.getElementById('historyPagination');
  
  if (!container) return;
  
  if (totalPages <= 1) {
    container.innerHTML = '';
    return;
  }
  
  container.innerHTML = `
    <button onclick="changePageHistory(${currentPageHistory - 1})" ${currentPageHistory === 1 ? 'disabled' : ''}>← Previous</button>
    <span style="padding: 0 15px; font-weight: 600;">Page ${currentPageHistory} of ${totalPages}</span>
    <button onclick="changePageHistory(${currentPageHistory + 1})" ${currentPageHistory === totalPages ? 'disabled' : ''}>Next →</button>
  `;
}

function changePageCurrent(page) {
  currentPageCurrent = page;
  loadOrders();
}

function changePageHistory(page) {
  currentPageHistory = page;
  loadHistory();
}

// ================================
// UPDATE STATUS
// ================================
async function updateStatus(orderId, newStatus) {
  try {
    const response = await fetch('update_status.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        order_id: orderId,
        status: newStatus
      })
    });
    
    const data = await response.json();
    
    if (data.success) {
      showToast('Status updated', 'success');
      loadOrders();
      loadHistory();
    } else {
      showToast(data.message || 'Failed to update status', 'error');
    }
  } catch (error) {
    console.error('Error:', error);
    showToast('Error updating status', 'error');
  }
}

// ================================
// VIEW ORDER DETAILS
// ================================
async function viewOrderDetails(orderId) {
  try {
    const response = await fetch(`get_order_details.php?order_id=${orderId}`);
    const data = await response.json();
    
    if (data.success) {
      const modal = document.getElementById('orderModal');
      document.getElementById('modalTitle').textContent = `Order ${data.order.order_number}`;
      
      let html = `
        <p><strong>Customer:</strong> ${data.order.customer_name}</p>
        <p><strong>Date:</strong> ${data.order.order_date} ${data.order.order_time}</p>
        <p><strong>Status:</strong> ${data.order.status}</p>
        <hr style="margin: 20px 0;">
        <h4>Order Items:</h4>
      `;
      
      data.items.forEach(item => {
        html += `
          <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #eee;">
            <div>
              <strong>${item.item_name}</strong><br>
              <small>Qty: ${item.quantity} × ₱${parseFloat(item.unit_price).toFixed(2)}</small>
            </div>
            <div><strong>₱${(item.quantity * item.unit_price).toFixed(2)}</strong></div>
          </div>
        `;
      });
      
      html += `
        <div style="text-align: right; margin-top: 20px; font-size: 18px;">
          <strong>Total: ₱${parseFloat(data.order.total_price).toFixed(2)}</strong>
        </div>
      `;
      
      document.getElementById('modalBody').innerHTML = html;
      modal.classList.add('show');
    }
  } catch (error) {
    console.error('Error:', error);
    showToast('Error loading order details', 'error');
  }
}

function closeModal() {
  document.getElementById('orderModal').classList.remove('show');
}

// ================================
// GENERATE RECEIPT
// ================================
async function generateReceipt(orderId) {
  try {
    const response = await fetch(`get_order_details.php?order_id=${orderId}`);
    if (!response.ok) throw new Error(`HTTP ${response.status}`);

    const data = await response.json();

    if (!data.order || !data.items) {
      showToast('Order details not found', 'error');
      return;
    }

    const receiptContent = document.getElementById('receiptContent');
    if (!receiptContent) return;

    receiptContent.innerHTML = `
      <div style="text-align:center; margin-bottom:30px;">
        <h2 style="color:#3e2723; margin:0; font-size:32px; font-weight:bold;">
          Kape Muna Receipt
        </h2>
        <p style="color:#555; margin:10px 0 0; font-style:italic; font-size:17px;">
          Thank you for your order!
        </p>
      </div>

      <div style="max-width:550px; margin:0 auto 35px; line-height:1.7;">
        <p><strong>Order ID:</strong> ${data.order.order_number}</p>
        <p><strong>Customer:</strong> ${data.order.customer_name}</p>
        <p><strong>Date & Time:</strong> ${formatDisplayDate(data.order.order_date)} ${data.order.order_time}</p>
        <p><strong>Source:</strong> ${data.order.source}</p>
        <p><strong>Payment Method:</strong> ${data.order.payment}</p>
        <p><strong>Status:</strong> ${data.order.status}</p>
      </div>

      <h3 style="color:#3e2723; text-align:center; margin:40px 0 15px; font-size:24px;">
        Order Items
      </h3>

      <table style="width:100%; border-collapse:collapse; margin-bottom:30px;">
        <thead>
          <tr style="background:#3e2723; color:white;">
            <th style="padding:14px; text-align:left;">Item</th>
            <th style="padding:14px; text-align:center;">Qty</th>
            <th style="padding:14px; text-align:right;">Price</th>
            <th style="padding:14px; text-align:right;">Total</th>
          </tr>
        </thead>
        <tbody>
          ${data.items.map(item => `
            <tr style="border-bottom:1px solid #eee;">
              <td style="padding:14px;">${item.item_name}</td>
              <td style="padding:14px; text-align:center;">${item.quantity}</td>
              <td style="padding:14px; text-align:right;">₱${Number(item.unit_price).toFixed(2)}</td>
              <td style="padding:14px; text-align:right;">₱${(item.quantity * item.unit_price).toFixed(2)}</td>
            </tr>
          `).join('')}
        </tbody>
      </table>

      <div style="text-align:right; font-size:1.5em; font-weight:bold; margin:30px 0;">
        Grand Total: ₱${Number(data.order.total_price).toFixed(2)}
      </div>

      <div style="text-align:center; margin-top:60px; color:#555; font-style:italic; font-size:15px;">
        Kape Muna - Quezon City<br>
        Thank you! Come again ♥
      </div>
    `;

    document.getElementById('receiptModal').classList.add('show');
  } catch (error) {
    console.error('Error loading receipt:', error);
    showToast('Could not load receipt', 'error');
  }
}

function closeReceiptModal() {
  document.getElementById('receiptModal').classList.remove('show');
}

// Helper function to format date
function formatDisplayDate(isoDate) {
  if (!isoDate) return '—';
  const [year, month, day] = isoDate.split('-');
  return `${day}/${month}/${year}`;
}

// ================================
// TOAST NOTIFICATIONS
// ================================
function showToast(message, type = 'info') {
  const container = document.getElementById('toastContainer');
  const toast = document.createElement('div');
  toast.className = `toast ${type}`;
  toast.textContent = message;
  
  container.appendChild(toast);
  
  setTimeout(() => {
    toast.remove();
  }, 3000);
}
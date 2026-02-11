// ============================================
// ANALYTICS DASHBOARD - Connected to Database
// ============================================

let salesChart;
let currentData = null;

// ============================================
// FETCH ANALYTICS DATA FROM PHP
// ============================================
async function fetchAnalyticsData(startDate, endDate) {
  try {
    const response = await fetch(`get_analytics.php?start=${startDate}&end=${endDate}`);
    
    if (!response.ok) {
      throw new Error('Failed to fetch analytics data');
    }
    
    const data = await response.json();
    
    if (data.success) {
      currentData = data;
      updateSummaryStats(data.summary);
      createSalesChart(data.salesTrend);
      renderTopProducts(data.topProducts);
      renderRecentOrders(data.recentOrders);
      renderProductPerformance(data.productPerformance);
    } else {
      console.error('Error in response:', data);
      showError('Failed to load analytics data');
    }
    
  } catch (error) {
    console.error('Error fetching analytics:', error);
    showError('Unable to connect to server');
  }
}

// ============================================
// UPDATE SUMMARY STATS
// ============================================
function updateSummaryStats(data) {
  document.getElementById('totalRevenue').textContent = `₱${data.totalRevenue.toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
  document.getElementById('totalOrders').textContent = data.totalOrders.toLocaleString();
  document.getElementById('totalItems').textContent = data.totalItems.toLocaleString();
  document.getElementById('avgOrder').textContent = `₱${data.avgOrder.toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
}

// ============================================
// SALES TREND CHART
// ============================================
function createSalesChart(data) {
  const ctx = document.getElementById('salesChart');
  
  if (!ctx) {
    console.error('Sales chart canvas not found');
    return;
  }
  
  // Destroy existing chart if it exists
  if (salesChart) {
    salesChart.destroy();
  }
  
  // If no data, show message
  if (!data || data.length === 0) {
    ctx.getContext('2d').font = '16px Poppins';
    ctx.getContext('2d').fillStyle = '#888';
    ctx.getContext('2d').textAlign = 'center';
    ctx.getContext('2d').fillText('No sales data for this period', ctx.width / 2, ctx.height / 2);
    return;
  }
  
  salesChart = new Chart(ctx.getContext('2d'), {
    type: 'line',
    data: {
      labels: data.map(d => {
        const date = new Date(d.date);
        return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
      }),
      datasets: [{
        label: 'Daily Sales',
        data: data.map(d => d.sales),
        borderColor: '#6d4c41',
        backgroundColor: 'rgba(109, 76, 65, 0.1)',
        tension: 0.4,
        fill: true,
        pointBackgroundColor: '#6d4c41',
        pointBorderColor: '#fff',
        pointBorderWidth: 2,
        pointRadius: 5,
        pointHoverRadius: 7
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: false
        },
        tooltip: {
          backgroundColor: 'rgba(0, 0, 0, 0.8)',
          padding: 12,
          titleFont: {
            size: 14,
            family: 'Poppins'
          },
          bodyFont: {
            size: 13,
            family: 'Poppins'
          },
          callbacks: {
            label: function(context) {
              return 'Sales: ₱' + context.parsed.y.toLocaleString('en-PH', {minimumFractionDigits: 2});
            }
          }
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            callback: function(value) {
              return '₱' + value.toLocaleString('en-PH');
            },
            font: {
              family: 'Poppins',
              size: 12
            }
          },
          grid: {
            color: 'rgba(0,0,0,0.05)'
          }
        },
        x: {
          grid: {
            display: false
          },
          ticks: {
            font: {
              family: 'Poppins',
              size: 12
            }
          }
        }
      },
      interaction: {
        intersect: false,
        mode: 'index'
      },
      animation: {
        duration: 750
      }
    }
  });
}

// ============================================
// TOP PRODUCTS LIST
// ============================================
function renderTopProducts(data) {
  const container = document.getElementById('topProducts');
  
  if (!data || data.length === 0) {
    container.innerHTML = '<p style="text-align: center; color: #888; padding: 20px;">No product data available</p>';
    return;
  }
  
  container.innerHTML = '';
  
  data.forEach((product, index) => {
    const item = document.createElement('div');
    item.className = 'product-item';
    item.innerHTML = `
      <div class="product-info">
        <div class="product-rank">${index + 1}</div>
        <div class="product-details">
          <h4>${escapeHtml(product.name)}</h4>
          <p>${escapeHtml(product.category)}</p>
        </div>
      </div>
      <div class="product-stats">
        <div class="sales">${product.units} sold</div>
        <div class="revenue">₱${product.revenue.toLocaleString('en-PH', {minimumFractionDigits: 2})}</div>
      </div>
    `;
    container.appendChild(item);
  });
}

// ============================================
// RECENT ORDERS TABLE
// ============================================
function renderRecentOrders(data) {
  const tbody = document.getElementById('recentOrders');
  
  if (!data || data.length === 0) {
    tbody.innerHTML = '<tr><td colspan="5" style="text-align: center; padding: 30px; color: #888;">No orders found for this period</td></tr>';
    return;
  }
  
  tbody.innerHTML = '';
  
  data.forEach(order => {
    // Map status to standardized values
    let status = order.status.toLowerCase();
    let statusClass = '';
    
    if (status === 'completed') {
      statusClass = 'status-completed';
    } else if (status === 'pending') {
      statusClass = 'status-pending';
    } else {
      statusClass = 'status-cancelled';
    }
    
    const row = document.createElement('tr');
    row.innerHTML = `
      <td><strong>${escapeHtml(order.id)}</strong></td>
      <td>${escapeHtml(order.customer)}</td>
      <td>${escapeHtml(order.date)}</td>
      <td>${order.items} items</td>
      <td><strong>₱${order.total.toLocaleString('en-PH', {minimumFractionDigits: 2})}</strong></td>
      <td><span class="status-badge ${statusClass}">${escapeHtml(order.status.toUpperCase())}</span></td>
    `;
    tbody.appendChild(row);
  });
}

// ============================================
// PRODUCT PERFORMANCE TABLE
// ============================================
function renderProductPerformance(data) {
  const tbody = document.getElementById('productPerformance');
  
  if (!data || data.length === 0) {
    tbody.innerHTML = '<tr><td colspan="5" style="text-align: center; padding: 30px; color: #888;">No product performance data available</td></tr>';
    return;
  }
  
  tbody.innerHTML = '';
  
  data.forEach(product => {
    const row = document.createElement('tr');
    row.innerHTML = `
      <td><strong>${escapeHtml(product.name)}</strong></td>
      <td>${escapeHtml(product.category)}</td>
      <td>${product.units} units</td>
      <td><strong>₱${product.revenue.toLocaleString('en-PH', {minimumFractionDigits: 2})}</strong></td>
      <td>
        <div class="popularity-bar">
          <div class="popularity-fill" style="width: ${product.popularity}%"></div>
        </div>
      </td>
    `;
    tbody.appendChild(row);
  });
}

// ============================================
// DATE FILTER FUNCTIONS
// ============================================
function filterByDate(period, element) {
  // Remove active class from all pills
  document.querySelectorAll('.filter-pill').forEach(pill => {
    pill.classList.remove('active');
  });
  
  // Add active class to clicked pill
  element.classList.add('active');
  
  let startDate, endDate;
  const today = new Date();
  
  switch(period) {
    case 'today':
      startDate = endDate = formatDate(today);
      break;
    case 'week':
      const weekAgo = new Date(today);
      weekAgo.setDate(today.getDate() - 7);
      startDate = formatDate(weekAgo);
      endDate = formatDate(today);
      break;
    case 'month':
      const monthStart = new Date(today.getFullYear(), today.getMonth(), 1);
      startDate = formatDate(monthStart);
      endDate = formatDate(today);
      break;
    case 'year':
      const yearStart = new Date(today.getFullYear(), 0, 1);
      startDate = formatDate(yearStart);
      endDate = formatDate(today);
      break;
    default:
      startDate = endDate = formatDate(today);
  }
  
  // Update date inputs
  document.getElementById('startDate').value = startDate;
  document.getElementById('endDate').value = endDate;
  
  // Fetch new data
  fetchAnalyticsData(startDate, endDate);
}

function filterCustomDate() {
  const startDate = document.getElementById('startDate').value;
  const endDate = document.getElementById('endDate').value;
  
  if (!startDate || !endDate) {
    alert('Please select both start and end dates');
    return;
  }
  
  if (new Date(startDate) > new Date(endDate)) {
    alert('Start date must be before end date');
    return;
  }
  
  // Remove active from pills
  document.querySelectorAll('.filter-pill').forEach(pill => {
    pill.classList.remove('active');
  });
  
  // Fetch new data
  fetchAnalyticsData(startDate, endDate);
}

// ============================================
// HELPER FUNCTIONS
// ============================================
function formatDate(date) {
  const year = date.getFullYear();
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const day = String(date.getDate()).padStart(2, '0');
  return `${year}-${month}-${day}`;
}

function escapeHtml(text) {
  const div = document.createElement('div');
  div.textContent = text;
  return div.innerHTML;
}

function showError(message) {
  alert(message);
}

// ============================================
// INITIALIZE ON PAGE LOAD
// ============================================
document.addEventListener('DOMContentLoaded', function() {
  // Set default dates (this month)
  const today = new Date();
  const monthStart = new Date(today.getFullYear(), today.getMonth(), 1);
  
  document.getElementById('startDate').value = formatDate(monthStart);
  document.getElementById('endDate').value = formatDate(today);
  
  // Load initial data
  fetchAnalyticsData(formatDate(monthStart), formatDate(today));
});
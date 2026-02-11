<?php
// ============================================
// get_analytics.php
// Real backend connected to kape_muna_db
// ============================================

include 'db.php';
header('Content-Type: application/json');

// Get date range from query parameters
$startDate = isset($_GET['start']) ? $_GET['start'] : date('Y-m-01');
$endDate = isset($_GET['end']) ? $_GET['end'] : date('Y-m-d');

// Add end of day to endDate for inclusive range
$endDateTime = $endDate . ' 23:59:59';
$startDateTime = $startDate . ' 00:00:00';

// ============================================
// 1. SUMMARY STATISTICS
// ============================================

// Total Revenue (only completed orders)
$revenueQuery = "SELECT COALESCE(SUM(total_price), 0) as total_revenue 
                 FROM orders 
                 WHERE CONCAT(order_date, ' ', order_time) BETWEEN '$startDateTime' AND '$endDateTime'
                 AND status = 'Completed'";
$revenueResult = mysqli_query($conn, $revenueQuery);
$revenue = mysqli_fetch_assoc($revenueResult)['total_revenue'];

// Total Orders (all statuses)
$ordersQuery = "SELECT COUNT(*) as total_orders 
                FROM orders 
                WHERE CONCAT(order_date, ' ', order_time) BETWEEN '$startDateTime' AND '$endDateTime'";
$ordersResult = mysqli_query($conn, $ordersQuery);
$totalOrders = mysqli_fetch_assoc($ordersResult)['total_orders'];

// Total Items Sold (only from completed orders)
$itemsQuery = "SELECT COALESCE(SUM(oi.quantity), 0) as total_items
               FROM order_items oi
               JOIN orders o ON oi.order_id = o.id
               WHERE CONCAT(o.order_date, ' ', o.order_time) BETWEEN '$startDateTime' AND '$endDateTime'
               AND o.status = 'Completed'";
$itemsResult = mysqli_query($conn, $itemsQuery);
$totalItems = mysqli_fetch_assoc($itemsResult)['total_items'];

// Average Order Value
$avgOrder = $totalOrders > 0 ? $revenue / $totalOrders : 0;

$summary = [
    'totalRevenue' => (float)$revenue,
    'totalOrders' => (int)$totalOrders,
    'totalItems' => (int)$totalItems,
    'avgOrder' => (float)$avgOrder
];

// ============================================
// 2. SALES TREND (Daily)
// ============================================

$trendQuery = "SELECT 
                    order_date as date, 
                    COALESCE(SUM(total_price), 0) as sales
               FROM orders
               WHERE CONCAT(order_date, ' ', order_time) BETWEEN '$startDateTime' AND '$endDateTime'
               AND status = 'Completed'
               GROUP BY order_date
               ORDER BY order_date ASC";
$trendResult = mysqli_query($conn, $trendQuery);

$salesTrend = [];
while ($row = mysqli_fetch_assoc($trendResult)) {
    $salesTrend[] = [
        'date' => $row['date'],
        'sales' => (float)$row['sales']
    ];
}

// If no data, create empty trend for the date range
if (empty($salesTrend)) {
    $salesTrend[] = [
        'date' => date('Y-m-d'),
        'sales' => 0
    ];
}

// ============================================
// 3. TOP SELLING PRODUCTS
// ============================================

$topProductsQuery = "SELECT 
                        oi.item_name as name,
                        c.category_name as category,
                        SUM(oi.quantity) as units,
                        SUM(oi.quantity * oi.unit_price) as revenue
                     FROM order_items oi
                     JOIN orders o ON oi.order_id = o.id
                     JOIN menu_items m ON oi.item_id = m.item_id
                     JOIN categories c ON m.category_id = c.category_id
                     WHERE CONCAT(o.order_date, ' ', o.order_time) BETWEEN '$startDateTime' AND '$endDateTime'
                     AND o.status = 'Completed'
                     GROUP BY oi.item_id, oi.item_name, c.category_name
                     ORDER BY units DESC
                     LIMIT 5";
$topProductsResult = mysqli_query($conn, $topProductsQuery);

$topProducts = [];
while ($row = mysqli_fetch_assoc($topProductsResult)) {
    $topProducts[] = [
        'name' => $row['name'],
        'category' => $row['category'],
        'units' => (int)$row['units'],
        'revenue' => (float)$row['revenue']
    ];
}

// ============================================
// 4. RECENT ORDERS
// ============================================

$recentOrdersQuery = "SELECT 
                        o.id,
                        o.order_number,
                        CONCAT(o.order_date, ' ', o.order_time) as datetime,
                        o.customer_name,
                        (SELECT COUNT(*) FROM order_items WHERE order_id = o.id) as items,
                        o.total_price,
                        o.status
                      FROM orders o
                      WHERE CONCAT(o.order_date, ' ', o.order_time) BETWEEN '$startDateTime' AND '$endDateTime'
                      ORDER BY o.order_date DESC, o.order_time DESC
                      LIMIT 10";
$recentOrdersResult = mysqli_query($conn, $recentOrdersQuery);

$recentOrders = [];
while ($row = mysqli_fetch_assoc($recentOrdersResult)) {
    $recentOrders[] = [
        'id' => $row['order_number'] ? $row['order_number'] : 'ORD-' . str_pad($row['id'], 6, '0', STR_PAD_LEFT),
        'date' => date('Y-m-d H:i', strtotime($row['datetime'])),
        'customer' => $row['customer_name'],
        'items' => (int)$row['items'],
        'total' => (float)$row['total_price'],
        'status' => strtolower($row['status'])
    ];
}

// ============================================
// 5. PRODUCT PERFORMANCE
// ============================================

// First, get the max units for popularity calculation
$maxUnitsQuery = "SELECT MAX(total_units) as max_units FROM (
                    SELECT SUM(oi.quantity) as total_units
                    FROM order_items oi
                    JOIN orders o ON oi.order_id = o.id
                    WHERE CONCAT(o.order_date, ' ', o.order_time) BETWEEN '$startDateTime' AND '$endDateTime'
                    AND o.status = 'Completed'
                    GROUP BY oi.item_id
                  ) as subquery";
$maxUnitsResult = mysqli_query($conn, $maxUnitsQuery);
$maxUnitsRow = mysqli_fetch_assoc($maxUnitsResult);
$maxUnits = $maxUnitsRow['max_units'] ? $maxUnitsRow['max_units'] : 1;

$performanceQuery = "SELECT 
                        oi.item_name as name,
                        c.category_name as category,
                        SUM(oi.quantity) as units,
                        SUM(oi.quantity * oi.unit_price) as revenue
                     FROM order_items oi
                     JOIN orders o ON oi.order_id = o.id
                     JOIN menu_items m ON oi.item_id = m.item_id
                     JOIN categories c ON m.category_id = c.category_id
                     WHERE CONCAT(o.order_date, ' ', o.order_time) BETWEEN '$startDateTime' AND '$endDateTime'
                     AND o.status = 'Completed'
                     GROUP BY oi.item_id, oi.item_name, c.category_name
                     ORDER BY units DESC";
$performanceResult = mysqli_query($conn, $performanceQuery);

$productPerformance = [];
while ($row = mysqli_fetch_assoc($performanceResult)) {
    $popularity = $maxUnits > 0 ? ($row['units'] / $maxUnits) * 100 : 0;
    $productPerformance[] = [
        'name' => $row['name'],
        'category' => $row['category'],
        'units' => (int)$row['units'],
        'revenue' => (float)$row['revenue'],
        'popularity' => round($popularity, 0)
    ];
}

// ============================================
// 6. ORDER STATUS BREAKDOWN
// ============================================

$statusQuery = "SELECT 
                    status,
                    COUNT(*) as count,
                    SUM(total_price) as revenue
                FROM orders
                WHERE CONCAT(order_date, ' ', order_time) BETWEEN '$startDateTime' AND '$endDateTime'
                GROUP BY status";
$statusResult = mysqli_query($conn, $statusQuery);

$statusBreakdown = [];
while ($row = mysqli_fetch_assoc($statusResult)) {
    $statusBreakdown[] = [
        'status' => $row['status'],
        'count' => (int)$row['count'],
        'revenue' => (float)$row['revenue']
    ];
}

// ============================================
// 7. CATEGORY BREAKDOWN
// ============================================

$categoryQuery = "SELECT 
                    c.category_name,
                    SUM(oi.quantity) as items_sold,
                    SUM(oi.quantity * oi.unit_price) as revenue
                  FROM order_items oi
                  JOIN orders o ON oi.order_id = o.id
                  JOIN menu_items m ON oi.item_id = m.item_id
                  JOIN categories c ON m.category_id = c.category_id
                  WHERE CONCAT(o.order_date, ' ', o.order_time) BETWEEN '$startDateTime' AND '$endDateTime'
                  AND o.status = 'Completed'
                  GROUP BY c.category_id, c.category_name
                  ORDER BY revenue DESC";
$categoryResult = mysqli_query($conn, $categoryQuery);

$categoryBreakdown = [];
while ($row = mysqli_fetch_assoc($categoryResult)) {
    $categoryBreakdown[] = [
        'category' => $row['category_name'],
        'items_sold' => (int)$row['items_sold'],
        'revenue' => (float)$row['revenue']
    ];
}

// ============================================
// RETURN JSON RESPONSE
// ============================================

$response = [
    'success' => true,
    'dateRange' => [
        'start' => $startDate,
        'end' => $endDate
    ],
    'summary' => $summary,
    'salesTrend' => $salesTrend,
    'topProducts' => $topProducts,
    'recentOrders' => $recentOrders,
    'productPerformance' => $productPerformance,
    'statusBreakdown' => $statusBreakdown,
    'categoryBreakdown' => $categoryBreakdown
];

echo json_encode($response);

mysqli_close($conn);
?>
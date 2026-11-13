<?php
// Inventory Management System | DBMS Project | Author: Kainat Jameel
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/header.php';
$stats = [
    'products' => pg_fetch_result(pg_query($conn, 'SELECT COUNT(*) FROM products'), 0, 0),
    'suppliers' => pg_fetch_result(pg_query($conn, 'SELECT COUNT(*) FROM suppliers'), 0, 0),
    'low_stock' => pg_fetch_result(pg_query($conn, 'SELECT COUNT(*) FROM products WHERE quantity <= reorder_level'), 0, 0),
    'stock_value' => pg_fetch_result(pg_query($conn, 'SELECT COALESCE(SUM(quantity * unit_price),0) FROM products'), 0, 0),
];
?>
<section class="page-heading"><div><p class="eyebrow">OVERVIEW</p><h2>Inventory Dashboard</h2><p>Welcome back. Here is your current inventory summary.</p></div></section>
<section class="stats-grid">
<div class="stat-card"><span>Total Products</span><strong><?=number_format((int)$stats['products'])?></strong><small>Products in your catalog</small></div>
<div class="stat-card"><span>Total Suppliers</span><strong><?=number_format((int)$stats['suppliers'])?></strong><small>Registered suppliers</small></div>
<div class="stat-card warning-stat"><span>Low Stock Items</span><strong><?=number_format((int)$stats['low_stock'])?></strong><small>Need restocking attention</small></div>
<div class="stat-card"><span>Stock Value</span><strong>Rs. <?=number_format((float)$stats['stock_value'],2)?></strong><small>Current inventory value</small></div>
</section>
<section class="panel"><h3>Manage Your Inventory</h3><div class="dashboard-links">
<a href="../pages/products.php"><span class="dash-icon">▦</span><strong>Products</strong><small>Add, edit and manage products</small></a>
<a href="../pages/suppliers.php"><span class="dash-icon">♙</span><strong>Suppliers</strong><small>Manage supplier contacts</small></a>
<a href="../pages/purchases.php"><span class="dash-icon">＋</span><strong>Purchases</strong><small>Record incoming stock</small></a>
<a href="../pages/sales.php"><span class="dash-icon">↗</span><strong>Sales</strong><small>Record sales and stock out</small></a>
<a href="../reports/low_stock.php"><span class="dash-icon">!</span><strong>Low Stock Report</strong><small>View items below reorder level</small></a>
</div></section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>

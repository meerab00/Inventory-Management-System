<?php

// Inventory Management System
// DBMS Project
// Author: Kainat Jameel

require_once "../config/database.php";
require_once "../includes/header.php";

?>

<section class="welcome">

    <h2>Welcome to Inventory Management System</h2>

    <p>
        This system is used to manage products, suppliers,
        purchases, sales, and stock.
    </p>

    <div class="dashboard-links">

        <a href="../pages/products.php">Manage Products</a>

        <a href="../pages/suppliers.php">Manage Suppliers</a>

        <a href="../pages/purchases.php">Manage Purchases</a>

        <a href="../pages/sales.php">Manage Sales</a>

        <a href="../reports/low_stock.php">View Low Stock</a>

    </div>

</section>

<?php

require_once "../includes/footer.php";

?>

<?php
// Inventory Management System
// DBMS Project
// Author: Kainat Jameel

require_once "../config/database.php";
require_once "../includes/header.php";

$query = "
    SELECT
        product_id,
        product_name,
        sku,
        quantity,
        reorder_level
    FROM products
    WHERE quantity <= reorder_level
    ORDER BY quantity ASC
";

$result = pg_query($conn, $query);
?>

<section>
    <h2>Low Stock Report</h2>

    <p>
        This report shows products whose quantity is
        equal to or below the reorder level.
    </p>

    <?php if ($result && pg_num_rows($result) > 0): ?>

        <table>
            <thead>
                <tr>
                    <th>Product ID</th>
                    <th>Product Name</th>
                    <th>SKU</th>
                    <th>Quantity</th>
                    <th>Reorder Level</th>
                </tr>
            </thead>

            <tbody>
                <?php while ($row = pg_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['product_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['sku']); ?></td>
                        <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                        <td><?php echo htmlspecialchars($row['reorder_level']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

    <?php else: ?>

        <p>No low-stock products found.</p>

    <?php endif; ?>
</section>

<?php
require_once "../includes/footer.php";
?>

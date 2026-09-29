<?php
// Inventory Management System
// DBMS Project
// Author: Kainat Jameel

require_once "../config/database.php";
require_once "../includes/header.php";

$query = "
    SELECT
        p.product_id,
        p.product_name,
        p.sku,
        c.category_name,
        p.quantity,
        p.reorder_level,
        p.unit_price
    FROM products p
    JOIN categories c
        ON p.category_id = c.category_id
    ORDER BY p.product_id
";

$result = pg_query($conn, $query);
?>

<section>
    <h2>Products</h2>

    <?php if ($result && pg_num_rows($result) > 0): ?>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Product Name</th>
                    <th>SKU</th>
                    <th>Category</th>
                    <th>Quantity</th>
                    <th>Reorder Level</th>
                    <th>Unit Price</th>
                </tr>
            </thead>

            <tbody>
                <?php while ($row = pg_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['product_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['sku']); ?></td>
                        <td><?php echo htmlspecialchars($row['category_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                        <td><?php echo htmlspecialchars($row['reorder_level']); ?></td>
                        <td><?php echo htmlspecialchars($row['unit_price']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

    <?php else: ?>

        <p>No products found.</p>

    <?php endif; ?>
</section>

<?php
require_once "../includes/footer.php";
?>

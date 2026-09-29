<?php
// Inventory Management System
// DBMS Project
// Author: Kainat Jameel

require_once "../config/database.php";
require_once "../includes/header.php";

$query = "
    SELECT
        p.purchase_id,
        s.supplier_name,
        p.purchase_date,
        p.total_amount
    FROM purchases p
    JOIN suppliers s
        ON p.supplier_id = s.supplier_id
    ORDER BY p.purchase_date DESC
";

$result = pg_query($conn, $query);
?>

<section>
    <h2>Purchases</h2>

    <?php if ($result && pg_num_rows($result) > 0): ?>

        <table>
            <thead>
                <tr>
                    <th>Purchase ID</th>
                    <th>Supplier</th>
                    <th>Purchase Date</th>
                    <th>Total Amount</th>
                </tr>
            </thead>

            <tbody>
                <?php while ($row = pg_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['purchase_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['supplier_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['purchase_date']); ?></td>
                        <td><?php echo htmlspecialchars($row['total_amount']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

    <?php else: ?>

        <p>No purchases found.</p>

    <?php endif; ?>
</section>

<?php
require_once "../includes/footer.php";
?>

<?php
// Inventory Management System
// DBMS Project
// Author: Kainat Jameel

require_once "../config/database.php";
require_once "../includes/header.php";

$query = "
    SELECT
        sale_id,
        sale_date,
        total_amount
    FROM sales
    ORDER BY sale_date DESC
";

$result = pg_query($conn, $query);
?>

<section>
    <h2>Sales</h2>

    <?php if ($result && pg_num_rows($result) > 0): ?>

        <table>
            <thead>
                <tr>
                    <th>Sale ID</th>
                    <th>Sale Date</th>
                    <th>Total Amount</th>
                </tr>
            </thead>

            <tbody>
                <?php while ($row = pg_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['sale_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['sale_date']); ?></td>
                        <td><?php echo htmlspecialchars($row['total_amount']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

    <?php else: ?>

        <p>No sales found.</p>

    <?php endif; ?>
</section>

<?php
require_once "../includes/footer.php";
?>

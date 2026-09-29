<?php
// Inventory Management System
// DBMS Project
// Author: Kainat Jameel

require_once "../config/database.php";
require_once "../includes/header.php";

$query = "
    SELECT
        supplier_id,
        supplier_name,
        phone,
        email,
        address
    FROM suppliers
    ORDER BY supplier_id
";

$result = pg_query($conn, $query);
?>

<section>
    <h2>Suppliers</h2>

    <?php if ($result && pg_num_rows($result) > 0): ?>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Supplier Name</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Address</th>
                </tr>
            </thead>

            <tbody>
                <?php while ($row = pg_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['supplier_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['supplier_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['phone'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row['email'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row['address'] ?? ''); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

    <?php else: ?>

        <p>No suppliers found.</p>

    <?php endif; ?>
</section>

<?php
require_once "../includes/footer.php";
?>

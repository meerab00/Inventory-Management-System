<?php
// Inventory Management System | Author: Kainat Jameel
require_once __DIR__ . '/../config/database.php';
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

function h($value) { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }
function product_redirect($message, $type = 'success') {
    $_SESSION['flash'] = ['message' => $message, 'type' => $type];
    header('Location: products.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) product_redirect('Invalid request. Please try again.', 'error');
    $action = $_POST['action'] ?? '';
    if ($action === 'delete') {
        $id = filter_var($_POST['product_id'] ?? null, FILTER_VALIDATE_INT);
        if (!$id) product_redirect('Invalid product selected.', 'error');
        $deleted = pg_query_params($conn, 'DELETE FROM products WHERE product_id = $1', [$id]);
        product_redirect($deleted && pg_affected_rows($deleted) ? 'Product deleted.' : 'This product could not be deleted. It may be used in purchases or sales.', $deleted && pg_affected_rows($deleted) ? 'success' : 'error');
    }
    if ($action === 'save') {
        $id = ($_POST['product_id'] ?? '') !== '' ? filter_var($_POST['product_id'], FILTER_VALIDATE_INT) : null;
        $name = trim($_POST['product_name'] ?? '');
        $sku = strtoupper(trim($_POST['sku'] ?? ''));
        $category = filter_var($_POST['category_id'] ?? null, FILTER_VALIDATE_INT);
        $quantity = filter_var($_POST['quantity'] ?? null, FILTER_VALIDATE_INT);
        $reorder = filter_var($_POST['reorder_level'] ?? null, FILTER_VALIDATE_INT);
        $price = $_POST['unit_price'] ?? '';
        if ($name === '' || $sku === '' || !$category || $quantity === false || $quantity === null || $quantity < 0 || $reorder === false || $reorder === null || $reorder < 0 || !is_numeric($price) || (float)$price < 0 || ($id === false)) {
            product_redirect('Please enter valid product details.', 'error');
        }
        if ($id) {
            $saved = pg_query_params($conn, 'UPDATE products SET category_id=$1, product_name=$2, sku=$3, quantity=$4, reorder_level=$5, unit_price=$6 WHERE product_id=$7', [$category,$name,$sku,$quantity,$reorder,$price,$id]);
        } else {
            $saved = pg_query_params($conn, 'INSERT INTO products (category_id, product_name, sku, quantity, reorder_level, unit_price) VALUES ($1,$2,$3,$4,$5,$6)', [$category,$name,$sku,$quantity,$reorder,$price]);
        }
        product_redirect($saved ? ($id ? 'Product updated.' : 'Product added.') : 'Could not save product. Check that the SKU is unique.', $saved ? 'success' : 'error');
    }
}

$categories = pg_query($conn, 'SELECT category_id, category_name FROM categories ORDER BY category_name');
$edit = null;
if (isset($_GET['edit']) && filter_var($_GET['edit'], FILTER_VALIDATE_INT)) {
    $editResult = pg_query_params($conn, 'SELECT * FROM products WHERE product_id=$1', [$_GET['edit']]);
    $edit = $editResult ? pg_fetch_assoc($editResult) : null;
}
$result = pg_query($conn, 'SELECT p.product_id,p.product_name,p.sku,c.category_name,p.category_id,p.quantity,p.reorder_level,p.unit_price FROM products p JOIN categories c ON c.category_id=p.category_id ORDER BY p.product_id');
$flash = $_SESSION['flash'] ?? null; unset($_SESSION['flash']);
require_once __DIR__ . '/../includes/header.php';
?>
<section class="page-heading"><div><p class="eyebrow">INVENTORY</p><h2>Products</h2><p>Manage your product list and stock levels.</p></div></section>
<?php if ($flash): ?><div class="notice <?= h($flash['type']) ?>"><?= h($flash['message']) ?></div><?php endif; ?>
<section class="panel">
    <h3><?= $edit ? 'Edit Product' : 'Add New Product' ?></h3>
    <form method="post" class="data-form">
        <input type="hidden" name="csrf_token" value="<?= h($_SESSION['csrf_token']) ?>">
        <input type="hidden" name="action" value="save">
        <input type="hidden" name="product_id" value="<?= h($edit['product_id'] ?? '') ?>">
        <label>Product Name<input name="product_name" required maxlength="120" value="<?= h($edit['product_name'] ?? '') ?>" placeholder="e.g. Wireless Mouse"></label>
        <label>SKU<input name="sku" required maxlength="50" value="<?= h($edit['sku'] ?? '') ?>" placeholder="e.g. MS-002"></label>
        <label>Category<select name="category_id" required><option value="">Choose category</option><?php if ($categories) while ($cat=pg_fetch_assoc($categories)): ?><option value="<?= h($cat['category_id']) ?>" <?= (string)($edit['category_id'] ?? '') === (string)$cat['category_id'] ? 'selected' : '' ?>><?= h($cat['category_name']) ?></option><?php endwhile; ?></select></label>
        <label>Quantity<input type="number" name="quantity" min="0" required value="<?= h($edit['quantity'] ?? '0') ?>"></label>
        <label>Reorder Level<input type="number" name="reorder_level" min="0" required value="<?= h($edit['reorder_level'] ?? '5') ?>"></label>
        <label>Unit Price<input type="number" name="unit_price" min="0" step="0.01" required value="<?= h($edit['unit_price'] ?? '') ?>" placeholder="0.00"></label>
        <div class="form-actions"><button class="btn-primary" type="submit"><?= $edit ? 'Update Product' : 'Add Product' ?></button><?php if ($edit): ?><a class="btn-light" href="products.php">Cancel</a><?php endif; ?></div>
    </form>
</section>
<section class="panel">
    <div class="section-title"><h3>Product List</h3><span class="muted"><?= $result ? pg_num_rows($result) : 0 ?> products</span></div>
    <div class="table-wrap"><table><thead><tr><th>ID</th><th>Product</th><th>SKU</th><th>Category</th><th>Quantity</th><th>Reorder</th><th>Unit Price</th><th>Actions</th></tr></thead><tbody>
    <?php if ($result && pg_num_rows($result)): while ($row=pg_fetch_assoc($result)): ?>
    <tr><td><?= h($row['product_id']) ?></td><td><strong><?= h($row['product_name']) ?></strong></td><td><?= h($row['sku']) ?></td><td><?= h($row['category_name']) ?></td><td><?= h($row['quantity']) ?></td><td><?= h($row['reorder_level']) ?></td><td>Rs. <?= number_format((float)$row['unit_price'],2) ?></td><td class="actions"><a class="btn-small" href="products.php?edit=<?= h($row['product_id']) ?>">Edit</a><form method="post" onsubmit="return confirm('Delete this product?')"><input type="hidden" name="csrf_token" value="<?= h($_SESSION['csrf_token']) ?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="product_id" value="<?= h($row['product_id']) ?>"><button class="btn-small btn-danger" type="submit">Delete</button></form></td></tr>
    <?php endwhile; else: ?><tr><td colspan="8" class="empty">No products yet. Add your first product above.</td></tr><?php endif; ?>
    </tbody></table></div>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>

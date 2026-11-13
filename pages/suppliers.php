<?php
// Inventory Management System | Author: Kainat Jameel
require_once __DIR__ . '/../config/database.php';
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
function hs($value) { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }
function supplier_redirect($message, $type='success') { $_SESSION['flash']=['message'=>$message,'type'=>$type]; header('Location: suppliers.php'); exit; }

if ($_SERVER['REQUEST_METHOD']==='POST') {
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) supplier_redirect('Invalid request. Please try again.','error');
    $action=$_POST['action'] ?? '';
    if ($action==='delete') {
        $id=filter_var($_POST['supplier_id'] ?? null,FILTER_VALIDATE_INT);
        $deleted=$id ? pg_query_params($conn,'DELETE FROM suppliers WHERE supplier_id=$1',[$id]) : false;
        supplier_redirect($deleted && pg_affected_rows($deleted) ? 'Supplier deleted.' : 'Supplier could not be deleted. It may be linked to purchases.', $deleted && pg_affected_rows($deleted) ? 'success' : 'error');
    }
    if ($action==='save') {
        $id=($_POST['supplier_id'] ?? '')!=='' ? filter_var($_POST['supplier_id'],FILTER_VALIDATE_INT) : null;
        $name=trim($_POST['supplier_name'] ?? '');
        $phone=trim($_POST['phone'] ?? '');
        $email=trim($_POST['email'] ?? '');
        $address=trim($_POST['address'] ?? '');
        if ($name==='' || strlen($name)>120 || ($email!=='' && !filter_var($email,FILTER_VALIDATE_EMAIL)) || $id===false) supplier_redirect('Enter a valid supplier name and email.','error');
        if ($id) $saved=pg_query_params($conn,'UPDATE suppliers SET supplier_name=$1,phone=$2,email=$3,address=$4 WHERE supplier_id=$5',[$name,$phone ?: null,$email ?: null,$address ?: null,$id]);
        else $saved=pg_query_params($conn,'INSERT INTO suppliers (supplier_name,phone,email,address) VALUES ($1,$2,$3,$4)',[$name,$phone ?: null,$email ?: null,$address ?: null]);
        supplier_redirect($saved ? ($id ? 'Supplier updated.' : 'Supplier added.') : 'Could not save supplier.','success' === 'no' ? 'error' : ($saved ? 'success' : 'error'));
    }
}
$edit=null;
if (isset($_GET['edit']) && filter_var($_GET['edit'],FILTER_VALIDATE_INT)) { $q=pg_query_params($conn,'SELECT * FROM suppliers WHERE supplier_id=$1',[$_GET['edit']]); $edit=$q ? pg_fetch_assoc($q) : null; }
$result=pg_query($conn,'SELECT supplier_id,supplier_name,phone,email,address FROM suppliers ORDER BY supplier_id');
$flash=$_SESSION['flash'] ?? null; unset($_SESSION['flash']);
require_once __DIR__ . '/../includes/header.php';
?>
<section class="page-heading"><div><p class="eyebrow">CONTACTS</p><h2>Suppliers</h2><p>Keep supplier contact information organized.</p></div></section>
<?php if ($flash): ?><div class="notice <?= hs($flash['type']) ?>"><?= hs($flash['message']) ?></div><?php endif; ?>
<section class="panel"><h3><?= $edit ? 'Edit Supplier' : 'Add New Supplier' ?></h3>
<form method="post" class="data-form">
<input type="hidden" name="csrf_token" value="<?= hs($_SESSION['csrf_token']) ?>"><input type="hidden" name="action" value="save"><input type="hidden" name="supplier_id" value="<?= hs($edit['supplier_id'] ?? '') ?>">
<label>Supplier Name<input name="supplier_name" required maxlength="120" value="<?= hs($edit['supplier_name'] ?? '') ?>" placeholder="Company or supplier name"></label>
<label>Phone<input name="phone" maxlength="30" value="<?= hs($edit['phone'] ?? '') ?>" placeholder="Phone number"></label>
<label>Email<input type="email" name="email" maxlength="120" value="<?= hs($edit['email'] ?? '') ?>" placeholder="name@example.com"></label>
<label class="wide-field">Address<input name="address" maxlength="255" value="<?= hs($edit['address'] ?? '') ?>" placeholder="City and address"></label>
<div class="form-actions"><button class="btn-primary" type="submit"><?= $edit ? 'Update Supplier' : 'Add Supplier' ?></button><?php if ($edit): ?><a class="btn-light" href="suppliers.php">Cancel</a><?php endif; ?></div>
</form></section>
<section class="panel"><div class="section-title"><h3>Supplier List</h3><span class="muted"><?= $result ? pg_num_rows($result) : 0 ?> suppliers</span></div><div class="table-wrap"><table><thead><tr><th>ID</th><th>Supplier</th><th>Phone</th><th>Email</th><th>Address</th><th>Actions</th></tr></thead><tbody>
<?php if ($result && pg_num_rows($result)): while ($row=pg_fetch_assoc($result)): ?><tr><td><?= hs($row['supplier_id']) ?></td><td><strong><?= hs($row['supplier_name']) ?></strong></td><td><?= hs($row['phone']) ?></td><td><?= hs($row['email']) ?></td><td><?= hs($row['address']) ?></td><td class="actions"><a class="btn-small" href="suppliers.php?edit=<?= hs($row['supplier_id']) ?>">Edit</a><form method="post" onsubmit="return confirm('Delete this supplier?')"><input type="hidden" name="csrf_token" value="<?= hs($_SESSION['csrf_token']) ?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="supplier_id" value="<?= hs($row['supplier_id']) ?>"><button class="btn-small btn-danger" type="submit">Delete</button></form></td></tr><?php endwhile; else: ?><tr><td colspan="6" class="empty">No suppliers found. Add one above.</td></tr><?php endif; ?>
</tbody></table></div></section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>

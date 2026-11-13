<?php
// Inventory Management System | Author: Kainat Jameel
require_once __DIR__ . '/../config/database.php';
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token']=bin2hex(random_bytes(32));
function hp($v){return htmlspecialchars((string)$v,ENT_QUOTES,'UTF-8');}
function purchase_flash($m,$t='success'){$_SESSION['flash']=['message'=>$m,'type'=>$t];header('Location: purchases.php');exit;}

if($_SERVER['REQUEST_METHOD']==='POST'){
    if(!hash_equals($_SESSION['csrf_token'],$_POST['csrf_token']??'')) purchase_flash('Invalid request. Please try again.','error');
    $supplier=filter_var($_POST['supplier_id']??null,FILTER_VALIDATE_INT);
    $product=filter_var($_POST['product_id']??null,FILTER_VALIDATE_INT);
    $qty=filter_var($_POST['quantity']??null,FILTER_VALIDATE_INT);
    $cost=$_POST['unit_cost']??'';
    $date=trim($_POST['purchase_date']??'');
    if(!$supplier||!$product||$qty===false||$qty===null||$qty<1||!is_numeric($cost)||(float)$cost<0||($date!==''&&!preg_match('/^\d{4}-\d{2}-\d{2}$/',$date))) purchase_flash('Enter valid purchase details.','error');
    pg_query($conn,'BEGIN');
    $header=pg_query_params($conn,"INSERT INTO purchases(supplier_id,purchase_date,total_amount) VALUES($1,COALESCE(NULLIF($2,'')::date,CURRENT_DATE),$3) RETURNING purchase_id",[$supplier,$date,$qty*(float)$cost]);
    $purchase=$header?pg_fetch_assoc($header):false;
    $lock=pg_query_params($conn,'SELECT product_id FROM products WHERE product_id=$1 FOR UPDATE',[$product]);
    $item=$purchase&&$lock&&pg_num_rows($lock) ? pg_query_params($conn,'INSERT INTO purchase_items(purchase_id,product_id,quantity,unit_cost) VALUES($1,$2,$3,$4)',[$purchase['purchase_id'],$product,$qty,$cost]) : false;
    $stock=$item ? pg_query_params($conn,'UPDATE products SET quantity=quantity+$1 WHERE product_id=$2',[$qty,$product]) : false;
    $adjust=$stock ? pg_query_params($conn,"INSERT INTO stock_adjustments(product_id,adjustment_type,quantity,reason) VALUES($1,'IN',$2,$3)",[$product,$qty,'Purchase #'.$purchase['purchase_id']]) : false;
    if($header&&$purchase&&$lock&&pg_num_rows($lock)&&$item&&$stock&&$adjust){pg_query($conn,'COMMIT');purchase_flash('Purchase saved and stock increased.');}
    pg_query($conn,'ROLLBACK');
    purchase_flash('Purchase could not be saved. Check supplier and product details.','error');
}
$suppliers=pg_query($conn,'SELECT supplier_id,supplier_name FROM suppliers ORDER BY supplier_name');
$products=pg_query($conn,'SELECT product_id,product_name,sku,unit_price FROM products ORDER BY product_name');
$result=pg_query($conn,'SELECT p.purchase_id,s.supplier_name,p.purchase_date,p.total_amount FROM purchases p JOIN suppliers s ON s.supplier_id=p.supplier_id ORDER BY p.purchase_date DESC,p.purchase_id DESC');
$flash=$_SESSION['flash']??null;unset($_SESSION['flash']);
require_once __DIR__.'/../includes/header.php';
?>
<section class="page-heading"><div><p class="eyebrow">INVENTORY FLOW</p><h2>Purchases</h2><p>Record incoming stock from your suppliers.</p></div></section>
<?php if($flash):?><div class="notice <?=hp($flash['type'])?>"><?=hp($flash['message'])?></div><?php endif;?>
<section class="panel"><h3>Record a Purchase</h3>
<?php if(!$suppliers||pg_num_rows($suppliers)===0||!$products||pg_num_rows($products)===0):?><p class="empty">Add at least one supplier and one product before recording a purchase.</p><?php else:?>
<form method="post" class="data-form">
<input type="hidden" name="csrf_token" value="<?=hp($_SESSION['csrf_token'])?>">
<label>Supplier<select name="supplier_id" required><option value="">Choose supplier</option><?php while($s=pg_fetch_assoc($suppliers)):?><option value="<?=hp($s['supplier_id'])?>"><?=hp($s['supplier_name'])?></option><?php endwhile;?></select></label>
<label>Product<select name="product_id" required><option value="">Choose product</option><?php while($p=pg_fetch_assoc($products)):?><option value="<?=hp($p['product_id'])?>"><?=hp($p['product_name'])?> (<?=hp($p['sku'])?>)</option><?php endwhile;?></select></label>
<label>Quantity<input type="number" name="quantity" min="1" required placeholder="e.g. 10"></label>
<label>Unit Cost (Rs.)<input type="number" name="unit_cost" min="0" step="0.01" required placeholder="0.00"></label>
<label>Purchase Date<input type="date" name="purchase_date"></label>
<div class="form-actions"><button class="btn-primary" type="submit">Save Purchase</button></div>
</form><?php endif;?></section>
<section class="panel"><div class="section-title"><h3>Purchase History</h3><span class="muted"><?= $result?pg_num_rows($result):0 ?> records</span></div><div class="table-wrap"><table><thead><tr><th>Purchase ID</th><th>Supplier</th><th>Date</th><th>Total Amount</th></tr></thead><tbody>
<?php if($result&&pg_num_rows($result)):while($r=pg_fetch_assoc($result)):?><tr><td>#<?=hp($r['purchase_id'])?></td><td><?=hp($r['supplier_name'])?></td><td><?=hp($r['purchase_date'])?></td><td>Rs. <?=number_format((float)$r['total_amount'],2)?></td></tr><?php endwhile;else:?><tr><td colspan="4" class="empty">No purchases recorded yet.</td></tr><?php endif;?>
</tbody></table></div></section>
<?php require_once __DIR__.'/../includes/footer.php';?>

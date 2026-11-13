<?php
// Inventory Management System | Author: Kainat Jameel
require_once __DIR__ . '/../config/database.php';
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token']=bin2hex(random_bytes(32));
function hsale($v){return htmlspecialchars((string)$v,ENT_QUOTES,'UTF-8');}
function sale_flash($m,$t='success'){$_SESSION['flash']=['message'=>$m,'type'=>$t];header('Location: sales.php');exit;}

if($_SERVER['REQUEST_METHOD']==='POST'){
    if(!hash_equals($_SESSION['csrf_token'],$_POST['csrf_token']??'')) sale_flash('Invalid request. Please try again.','error');
    $product=filter_var($_POST['product_id']??null,FILTER_VALIDATE_INT);
    $qty=filter_var($_POST['quantity']??null,FILTER_VALIDATE_INT);
    $price=$_POST['unit_price']??'';
    $date=trim($_POST['sale_date']??'');
    if(!$product||$qty===false||$qty===null||$qty<1||!is_numeric($price)||(float)$price<0||($date!==''&&!preg_match('/^\d{4}-\d{2}-\d{2}$/',$date))) sale_flash('Enter valid sale details.','error');
    pg_query($conn,'BEGIN');
    $lock=pg_query_params($conn,'SELECT quantity FROM products WHERE product_id=$1 FOR UPDATE',[$product]);
    $available=$lock?pg_fetch_assoc($lock):false;
    if(!$available||(int)$available['quantity']<$qty){pg_query($conn,'ROLLBACK');sale_flash('Not enough stock available for this sale.','error');}
    $total=$qty*(float)$price;
    $header=pg_query_params($conn,"INSERT INTO sales(sale_date,total_amount) VALUES(COALESCE(NULLIF($1,'')::date,CURRENT_DATE),$2) RETURNING sale_id",[$date,$total]);
    $sale=$header?pg_fetch_assoc($header):false;
    $item=$sale?pg_query_params($conn,'INSERT INTO sale_items(sale_id,product_id,quantity,unit_price) VALUES($1,$2,$3,$4)',[$sale['sale_id'],$product,$qty,$price]):false;
    $stock=$item?pg_query_params($conn,'UPDATE products SET quantity=quantity-$1 WHERE product_id=$2 AND quantity >= $1',[$qty,$product]):false;
    $adjust=$stock&&pg_affected_rows($stock)===1?pg_query_params($conn,"INSERT INTO stock_adjustments(product_id,adjustment_type,quantity,reason) VALUES($1,'OUT',$2,$3)",[$product,$qty,'Sale #'.$sale['sale_id']]):false;
    if($header&&$sale&&$item&&$stock&&pg_affected_rows($stock)===1&&$adjust){pg_query($conn,'COMMIT');sale_flash('Sale saved and stock reduced.');}
    pg_query($conn,'ROLLBACK');
    sale_flash('Sale could not be saved. Please check the details.','error');
}
$products=pg_query($conn,'SELECT product_id,product_name,sku,quantity,unit_price FROM products WHERE quantity>0 ORDER BY product_name');
$result=pg_query($conn,'SELECT s.sale_id,s.sale_date,s.total_amount FROM sales s ORDER BY s.sale_date DESC,s.sale_id DESC');
$flash=$_SESSION['flash']??null;unset($_SESSION['flash']);
require_once __DIR__.'/../includes/header.php';
?>
<section class="page-heading"><div><p class="eyebrow">INVENTORY FLOW</p><h2>Sales</h2><p>Record sales and automatically deduct sold stock.</p></div></section>
<?php if($flash):?><div class="notice <?=hsale($flash['type'])?>"><?=hsale($flash['message'])?></div><?php endif;?>
<section class="panel"><h3>Record a Sale</h3>
<?php if(!$products||pg_num_rows($products)===0):?><p class="empty">There is no available stock to sell. Record a purchase or add product stock first.</p><?php else:?>
<form method="post" class="data-form">
<input type="hidden" name="csrf_token" value="<?=hsale($_SESSION['csrf_token'])?>">
<label>Product<select name="product_id" required><option value="">Choose product</option><?php while($p=pg_fetch_assoc($products)):?><option value="<?=hsale($p['product_id'])?>"><?=hsale($p['product_name'])?> (<?=hsale($p['sku'])?>) — Stock: <?=hsale($p['quantity'])?></option><?php endwhile;?></select></label>
<label>Quantity<input type="number" name="quantity" min="1" required placeholder="e.g. 2"></label>
<label>Unit Sale Price (Rs.)<input type="number" name="unit_price" min="0" step="0.01" required placeholder="0.00"></label>
<label>Sale Date<input type="date" name="sale_date"></label>
<div class="form-actions"><button class="btn-primary" type="submit">Save Sale</button></div>
</form><?php endif;?></section>
<section class="panel"><div class="section-title"><h3>Sales History</h3><span class="muted"><?= $result?pg_num_rows($result):0 ?> records</span></div><div class="table-wrap"><table><thead><tr><th>Sale ID</th><th>Date</th><th>Total Amount</th></tr></thead><tbody>
<?php if($result&&pg_num_rows($result)):while($r=pg_fetch_assoc($result)):?><tr><td>#<?=hsale($r['sale_id'])?></td><td><?=hsale($r['sale_date'])?></td><td>Rs. <?=number_format((float)$r['total_amount'],2)?></td></tr><?php endwhile;else:?><tr><td colspan="3" class="empty">No sales recorded yet.</td></tr><?php endif;?>
</tbody></table></div></section>
<?php require_once __DIR__.'/../includes/footer.php';?>

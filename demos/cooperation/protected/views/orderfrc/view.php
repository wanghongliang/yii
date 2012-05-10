<?php
$this->breadcrumbs=array(
	'订单信息'=>'index',
	$model->order_no,
);
$this->pageTitle=$model->order_no; 
  
?>

<div id="Orderfcr">

<h1>订单内容</h1>

<table>
	<tr>
		<td>订单号：</td>
		<td><?php echo $model->order_no;  ?></td>
	</tr>
	<tr>
		<td>时间：</td>
		<td><?php echo date('Y-m-d H:i:s',$model->created);  ?></td>
	</tr>

</table>

</div><!-- comments -->

<?php
$this->layout  = 'column3';
$this->pageTitle=Yii::app()->name;
$this->breadcrumbs=array(
	'管理中心',
);
?>
<div style="padding:10px 0px;" >
<div class="last_order" >
	<div class="t">
		最新订单信息
	</div>
	
	<div class="b" >
<?php 
$dataProvider=new CActiveDataProvider('Orderfrc');

$this->widget('zii.widgets.CListView', array(
    'dataProvider'=>$dataProvider, 
    'itemView'=>'_order_item', 
));

?>	</div>
</div> 



<div class="sort_menu" >

<div class='b' >
	<?php 
	
	//首页内容 
	echo CHtml::link( '彩票订单记录', array('orderfcr/index')); 
	
	?>
	&nbsp;&nbsp;
	
	<?php
	echo CHtml::link( '会员信息', array('order/index'));  
	?>
</div>

</div>

</div>
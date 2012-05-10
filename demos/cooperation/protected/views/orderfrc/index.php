<?php
$this->layout  = 'column3';
$this->pageTitle=Yii::app()->name;
$this->breadcrumbs=array(
	'¶©µ¥ÐÅÏ¢',
);
?>
<?php $this->widget('zii.widgets.grid.CGridView', array(
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'filterPosition'=>'body',
	'filterStr'=>$this->renderPartial('_fitler', array(
	'data'=>$model),true),
	'columns'=>array(  
array(
		'class'=>'CCheckBoxColumn','name'=>'id','id'=>'select'), 
		array(
			'name'=>'id',
			'type'=>'raw',
			'filter'=>false,
 		),
		array(
			'name'=>'order_no',
			'type'=>'raw',
			'value'=>'CHtml::link( $data->order_no , $data->url)'
		), 
		array(
			'name'=>'user_name',
			'type'=>'raw', 
		),
		array(
			'name'=>'amount',
			'type'=>'raw', 
			'filter'=>false,
		),
		array(
			'name'=>'integral',
			'type'=>'raw',
			'filter'=>false,
		),
		array(
			'name'=>'mobile',
			'type'=>'raw', 
		),

		array(
			'name'=>'created',
			'type'=>'datetime',
			'filter'=>false,
		),
		array(
			'class'=>'CButtonColumn',
			'template'=>'{view}', 
		),
	),
)); ?>

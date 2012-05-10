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
	'filterPosition'=>'footer',
	'columns'=>array(  
array(
		'class'=>'CCheckBoxColumn','name'=>'user_id','id'=>'select'),
 
		array(
			'name'=>'user_id',
			'type'=>'raw',
			'filter'=>false,
		),
		array(
			'name'=>'user_name',
			'type'=>'raw',
			'value'=>'CHtml::link( $data->user_name , $data->url)'
		), 
		array(
			'name'=>'user_email',
			'type'=>'raw', 
		),
		array(
			'name'=>'user_regtime',
			'type'=>'datetime',
			'filter'=>false,
		),
		array(
			'name'=>'login_time',
			'type'=>'datetime',
			'filter'=>false,
		),
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>

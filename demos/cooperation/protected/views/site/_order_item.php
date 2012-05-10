<div class="order_item">
	<div class="title">
		¡¤ <?php echo CHtml::link(CHtml::encode($data->order_no), $data->url); ?>
	</div> 
	<div class="date">
		<?php echo date('Y-m-d H:i:s',$data->created); ?>
	</div> 
</div>

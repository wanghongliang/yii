<?php
$this->pageTitle=Yii::app()->name . ' - Login';
$this->breadcrumbs=array(
	'Login',
);
?>

<div style="text-align:center" >
<h1>合作平台登陆</h1> 
</div>

<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'login-form',
	'enableAjaxValidation'=>true,
)); ?>
 
 
 <div style="padding:20px 0px 0px 100px;" >
 <table>
 	<tr>
 		<td width="50" ><?php echo $form->labelEx($model,'username'); ?></td>
 		<td>
 		<?php echo $form->textField($model,'username'); ?>
 		<?php echo $form->error($model,'username'); ?> 
 		</td>
 	</tr>
 	
 	<tr>
 		<td><?php echo $form->labelEx($model,'password'); ?></td>
 		<td>
 		<?php echo $form->passwordField($model,'password'); ?>
 		<?php echo $form->error($model,'password'); ?> 
 		</td>
 	</tr>
 	 	
  	<tr>
 		<td></td>
 		<td>
 		
 		<div style="float:left;" >
 		<?php echo $form->checkBox($model,'rememberMe'); ?>
 		</div>
 		
 		<div style="float:left;" >
 		<?php echo $form->label($model,'rememberMe'); ?>
 		</div>
 		
 		<div style="float:left;" >
 		<?php echo $form->error($model,'rememberMe'); ?>
 		</div>
 		 
 		</td>
 	</tr>
 	
 	
 	 <tr>
 		<td ></td>
 		<td>
			<div class="row submit">
				<?php echo CHtml::submitButton('登陆'); ?>
			</div>
 		</td>
 	</tr>
 		
 </table>
 </div>

<?php $this->endWidget(); ?>
</div><!-- form -->

<style type="text/css" >
#page{margin-top:80px; }
.container{ width:500px;padding-top:5px; }
#content{   }
</style>

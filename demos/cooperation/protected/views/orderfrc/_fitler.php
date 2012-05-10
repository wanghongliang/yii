<?php
$cs=Yii::app()->getClientScript(); 
$cs->registerScriptFile(Yii::app()->request->baseUrl.'/js/My97DatePicker/WdatePicker.js',CClientScript::POS_END);

$id = '_filter';
$url = Yii::app()->request->getBaseUrl();
$cs->registerScript(__CLASS__.'#'.$id,"jQuery('#$id').live('click',function() { 
	location.href='{$url}'+'?sd='+jQuery('#sd').val()+'&ed='+jQuery('#ed').val();
});");
?>
<div class="fitler_by_time" >
按时间查找：
<input type="text" id="sd" value="" class="w-t"  onclick="WdatePicker( );" />
到
<input type="text" id="ed" value="" class="w-t"  onclick="WdatePicker( );" />

<input type="button" value="查找" class="w-btn1" id="_filter" />
<?php

?>
</div>
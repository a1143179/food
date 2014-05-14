<?php 
	if(empty($whatToCook)){
		echo 'Order Takeout.';
	}else{
		echo 'You can cook <b>' . htmlentities($whatToCook).'.</b>';
	}
	
?>

<div><?php echo CHtml::link('Re-upload files.',array('site/index'))?></div>
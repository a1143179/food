<?php
/* @var $this SiteController */

echo CHtml::form('','post',array('enctype'=>'multipart/form-data'));
echo CHtml::errorSummary($uploadModel);
?>
<div>
<?php
echo CHtml::label('Fridge', 'fridge');
echo CHtml::activeFileField($uploadModel, 'fridge');
echo CHtml::error($uploadModel, 'fridge');
?>
</div>

<div>
<?php
echo CHtml::label('Recipe', 'recipe');
echo CHtml::activeFileField($uploadModel, 'recipe');
echo CHtml::error($uploadModel, 'recipe');
?>
</div>

<div>
<?php
echo CHtml::submitButton('Submit');
echo CHtml::endForm();
?>
</div>

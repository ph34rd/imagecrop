<?php $this->pageTitle=Yii::app()->name; ?>

<h1><?php echo CHtml::encode(Yii::app()->name); ?></h1>

<?php if(Yii::app()->user->hasFlash('uploaded')): ?>

<div class="flash-success">
<?php echo Yii::app()->user->getFlash('uploaded'); ?>

<?php echo CHtml::link('View Image', array('site/viewimage', 'uuid' => $admin)); ?>
</div>

<?php endif; ?>


<div class="form">
<?php $form = $this->beginWidget('CActiveForm', array(
	'id' => 'upload-form',
	'enableClientValidation' => false,
	'htmlOptions' => array('enctype' => 'multipart/form-data'),
)); ?>

	<div class="row">
		<?php echo $form->labelEx($model, 'image'); ?>
		<?php echo $form->fileField($model, 'image'); ?>
		<?php echo $form->error($model, 'image'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'parts'); ?>
		<?php echo $form->dropDownList($model, 'parts', $model->getPartsOptions()); ?>
		<?php echo $form->error($model, 'parts'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'enabled parts'); ?>
		<?php echo $form->textField($model, 'enabled'); ?>
		<?php echo $form->error($model, 'enabled'); ?>
	</div>	

	<div class="row buttons">
		<?php echo CHtml::submitButton('Upload'); ?>
	</div>

<?php $this->endWidget(); ?>
</div>

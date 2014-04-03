<?php $this->pageTitle=Yii::app()->name; ?>

<h1><?php echo CHtml::encode(Yii::app()->name.' View Image'); ?></h1>

<div id="image">
	<ul>
		<?php for ($i = 1; $i <= $image->parts; $i++) { ?>
			<li id="<?php echo 'part'.$i; ?>"><?php if (in_array($i, $image->enabledArr)) { 
				echo CHtml::image($this->createAbsoluteUrl('/site/loadimage/' . $image->uuid .'/' . $i), 'Part '.$i); 
			} else { 
				echo CHtml::image(Yii::app()->request->baseUrl.'/img/1.png', 'Part '.$i); 
			} ?></li>
		<?php } ?>
	</ul>
</div>

<script type="text/javascript">
$(function() {

<?php for ($i = 1; $i <= $image->parts; $i++) {
	if (!in_array($i, $image->enabledArr)) { ?>

	$("#part<?php echo $i; ?>").click(function(){
		$(this).find("img").attr("src", "<?php echo $this->createAbsoluteUrl('/site/loadimageonce/' . $image->uuid .'/' . $i); ?>");
	});

<?php } } ?>

	$("#image").css('width', '<?php echo $image->width; ?>px');
	$("#image img").css('width', '<?php echo ($image->horizontal) ? $image->width/$image->parts : $image->width; ?>px');
	$("#image img").css('height', '<?php echo (!$image->horizontal) ? $image->height/$image->parts : $image->height; ?>px');
});
</script>

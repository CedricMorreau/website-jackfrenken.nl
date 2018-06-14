<div class="column-content">
	
	<div class="column-sidebar">
	
		<?php echo $template->cmsData('page][navigation/2/subnav/' . $template->findHighestParent() . '/active/' . $template->getPageId()); ?>
		
		<?php echo $template->cmsData('page][section/widgets'); ?>

	</div>

	<div class="column-content">
	
		<?php
		
		$textBlock = trim($template->cmsData('page][section/content'));
		$optTitle = $template->getData('optionalTitle', 3);
		
		?>

		<div class="content-wrapper">
		
			<?php if (!empty($optTitle)) { ?>
		
			<h2><?php echo $optTitle; ?></h2>
			
			<?php } ?>

			<?php echo $textBlock; ?>
		</div>
	</div>
</div>
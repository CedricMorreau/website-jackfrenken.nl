<div class="column-header">

	<?php
	
	// Get nav name of highest parent
	$pageId = $template->findHighestParent();	
	$navName = $cms['database']->prepare("SELECT `mod_pa_nav` FROM `tbl_mod_pages` WHERE `mod_pa_id`=?", "i", array($pageId));
	
	?>

	<div class="header-title-wrapper animated fadeInLeft">
		<div class="header-title">
			<?php
			
// 			if ($template->getPageDataMulti('navTitle') != $navName[0]['mod_pa_nav']) {
				
			?>
			
			<p class="title-category"><?php echo $navName[0]['mod_pa_nav']; ?></p>
			
			<?php //} ?>
			<h1>
			<?php 
		
			if (isset($pageOverride))
				echo $pageOverride;
			else
				echo $template->getPageDataMulti('navTitle');
			
			?>
			</h1>
		</div>
	</div>
	
	<?php
	
	if (!empty($sfeerbeeld)) {
	
	?>

	<div class="content-image animated fadeInRight" style="background-image: url(<?php echo $sfeerbeeld; ?>);">
		<!-- bg img -->
	</div>
	
	<?php } else { ?>
	
	<div class="content-image">
		<!-- bg img -->
	</div>
	
	<?php } ?>

</div>
<div class="column-header">

	<?php
	
	// Get nav name of highest parent
	$pageId = $template->findHighestParent();	
	$navName = $cms['database']->prepare("SELECT `mod_pa_nav` FROM `tbl_mod_pages` WHERE `mod_pa_id`=?", "i", array($pageId));
	
	?>

	<div class="header-title-wrapper">
		<div class="header-title">
			<p class="title-category"><?php echo $navName[0]['mod_pa_nav']; ?></p>
			<h1>
			
			<?php echo $values['art_visibleTitle'] ?? $values['art_title']; ?>
			
			</h1>
			
			<?php
			
			if (!empty($values['art_intro'])) {

			?>
			
			<p class="title-description">
				<?php echo $values['art_intro']; ?>
			</p>
			
			<?php } ?>
		</div>
	</div>

	<div class="content-image" style="background-image: url(<?php echo $values['art_overviewPhoto']; ?>);">
		<!-- bg img -->
	</div>

</div>
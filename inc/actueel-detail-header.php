<div class="column-header">

	<?php
	
	// Get nav name of highest parent
	$pageId = $template->findHighestParent();	
	$navName = $cms['database']->prepare("SELECT `mod_pa_nav` FROM `tbl_mod_pages` WHERE `mod_pa_id`=?", "i", array($pageId));
	
	?>
	<div class="content-wrapper">
		<div class="header-main-content">
			<div class="header-title-wrapper">
				<div class="header-title">
					<!-- <p class="title-category"><?php echo $navName[0]['mod_pa_nav']; ?></p> -->
					<h1>
					
					<?php echo $values['art_title']; ?>
					
					</h1>
					
					<!-- <?php
					
					if (!empty($values['art_intro'])) {

<<<<<<< HEAD
					?>
					
					<p class="title-description">
						<?php echo $values['art_intro']; ?>
					</p>
					
					<?php } ?> -->
				</div>
			</div>
=======
	<div class="header-title-wrapper">
		<div class="header-title">
			<p class="title-category"><?php echo $navName[0]['mod_pa_nav']; ?></p>
			<h1>
			
			<?php echo $values['art_visibleTitle'] ?? $values['art_title']; ?>
			
			</h1>
			
			<?php
			
			if (!empty($values['art_intro'])) {
>>>>>>> d11322d4b02ef4e73cfc2a86a75d02f59278c7d7

			<div class="content-image" style="background-image: url(<?php echo $values['art_overviewPhoto']; ?>);">
				<!-- bg img -->
			</div>
		</div>
	</div>
</div>
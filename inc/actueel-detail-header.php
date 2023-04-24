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

					?>
					
					<p class="title-description">
						<?php echo $values['art_visibleTitle'] ?? $values['art_title']; ?>
					</p>
					
					<?php } ?> -->
				</div>
			</div>

			<div class="content-image" style="background-image: url(<?php echo $values['art_overviewPhoto']; ?>);">
				<!-- bg img -->
			</div>

			
		</div>

		<div class="breadcrumbs-row-wrapper">
			<div class="breadcrumbs-spacer"></div>
			<div class="breadcrumbs-wrapper">

				<?php

				if (!isset($extraCrumbs))
					$extraCrumbs = array();

				if (!isset($ignoreCrumb))
					$ignoreCrumb = 0;
							
				$breadCrumbs = new Breadcrumbs($template->getPageData('id'), $template->getPageData('nav'), $cms['database'], $template, $extraCrumbs, $ignoreCrumb);

				echo $breadCrumbs->displayCrumbs();

				?>

			</div>

		</div>
	</div>
</div>
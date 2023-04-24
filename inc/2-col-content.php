<div class="column-content">
	
	<div class="column-sidebar">
	
		<?php echo $template->cmsData('page][navigation/2/subnav/' . $template->findHighestParent() . '/active/' . $template->getPageId()); ?>
		
		<?php echo $template->cmsData('page][section/widgets'); ?>

		<?php

		$parent = $template->findParent();

		$page = $cms['database']->prepare("SELECT * FROM `tbl_mod_pages` WHERE `mod_pa_id`=?", "i", [$parent]);

		if (count($page) > 0) {

			if ($page[0]['mod_pa_type'] == 1 && ($page[0]['mod_pa_templateId'] = 13)) {

				echo '<a href="' . $template->findPermalink($parent, 1) . '" class="back-link"><span class="arrow">&#x25B8;</span>Terug naar&nbsp;<strong>' . strtolower($page[0]['mod_pa_nav']) . '</strong></a>';		
			}
		}

		?>
	</div>

	<div class="column-content">
	
		<?php
		
		$textBlock = trim($template->cmsData('page][section/content'));
		$optTitle = $template->getData('optionalTitle', 3);
		
		?>

		<div class="content-wrapper">

			<?php echo $textBlock; ?>

			<!-- Only for Funda reviews page -->
			<a href="https://www.funda.nl/makelaars/roermond/21047-jack-frenken-makelaars-en-adviseurs/" class="funda-review-banner">
				<span class="icon-external">
				<?php include($documentRoot . "/resources/icon-external.svg"); ?>

				</span> 
					Bekijk ons kantoor op funda
				 <div class="funda-logo">
					<img src="/resources/logo-funda-business.png">
				</div>
			</a>
		</div>
	</div>
</div>
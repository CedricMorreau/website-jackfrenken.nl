<div class="column-content">
	
	<div class="column-sidebar">
	
		<?php echo $template->cmsData('page][navigation/2/subnav/' . $template->findHighestParent() . '/active/' . $template->getPageId()); ?>
		
		<?php echo $template->cmsData('page][section/widgets'); ?>
		<a href="#" class="back-link"><span class="arrow">&#x25B8;</span>Terug naar&nbsp;<strong>over ons</strong></a>
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
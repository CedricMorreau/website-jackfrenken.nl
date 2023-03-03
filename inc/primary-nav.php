<div class="primary-nav content-wrapper">

	<div class="logo-container">
		<a href="/">
			<img src="<?php echo $dynamicRoot; ?>resources/logo.svg" alt="Jack Frenken logo" class="desktop-logo" />
			<img src="<?php echo $dynamicRoot; ?>resources/logo_jf_landscape.svg" alt="Jack Frenken beeldmerk" class="mobile-beeldmerk" />
		</a>
	</div>

	<a href="tel:0475335225" class="mobile-contact-icon">
		<img src="/resources/icon-telefoon-small.svg" alt="Bel ons">
	</a>

	<div class="mobile-toggle">
		<img class="hamburger-icon open" src="<?php echo $dynamicRoot; ?>resources/icon-hamburger.svg" alt="Menu">

		<img class="close-icon" src="<?php echo $dynamicRoot; ?>resources/icon-close.svg" alt="Menu">
	</div>

	<nav>
	
		<?php echo $template->cmsData('page][navigation/1/active/' . $template->findHighestParent()); ?>
	
	</nav>
	
</div>
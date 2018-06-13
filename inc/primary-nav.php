<div class="primary-nav">

	<div class="logo-container">
		<a href="/">
			<img src="<?php echo $dynamicRoot; ?>resources/logo.svg" alt="Jack Frenken logo" class="desktop-logo" />
			<img src="<?php echo $dynamicRoot; ?>resources/logo_jf_landscape.svg" alt="Jack Frenken beeldmerk" class="mobile-beeldmerk" />
		</a>
	</div>


	<div class="mobile-toggle">
		<img class="hamburger-icon open" src="<?php echo $dynamicRoot; ?>resources/icon-hamburger.svg" alt="Menu">
		<img class="close-icon" src="<?php echo $dynamicRoot; ?>resources/icon-close.svg" alt="Menu">
	</div>

	<nav>
	
		<?php echo $template->cmsData('page][navigation/1/active/' . $template->findHighestParent()); ?>
	
	</nav>	
	
</div>
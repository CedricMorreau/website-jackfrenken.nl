<div class="primary-nav">

	<div class="logo-container">
		<a href="/">
			<img src="/resources/logo.svg" alt="Jack Frenken logo" class="desktop-logo" />
			<img src="/resources/logo_jf_landscape.svg" alt="Jack Frenken beeldmerk" class="mobile-beeldmerk" />
		</a>
	</div>


	<div class="mobile-toggle">
		<img class="hamburger-icon open" src="/resources/icon-hamburger.svg" alt="Menu">
		<img class="close-icon" src="/resources/icon-close.svg" alt="Menu">
	</div>

	<nav>
	
		<?php echo $template->cmsData('page][navigation/1/active/' . $template->findHighestParent()); ?>
	
	</nav>	
	
</div>
<!doctype html>

<html lang="nl">
	<head>
		
		<?php include($documentRoot . "inc/head.php"); ?>
		<link rel="stylesheet" href="<?php echo $dynamicRoot; ?>css/autocomplete.css">
	</head>

	<body>
		<!-- KMH pixel --> 
		<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
		new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0], j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src= '//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f); })(window,document,'script','kmhPixel','GTM-PX4GN2');</script> 
		<!-- End KMH pixel -->

		<!-- Media Group Holland -->
		<!-- Google Tag Manager (noscript) -->
		<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-5DGFMBF"
		height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
		<!-- End Google Tag Manager (noscript) -->

		<div class="page-wrapper homepage-wrapper">

			<?php include($documentRoot . "inc/primary-nav.php"); ?>

			<?php include($documentRoot . "inc/homepage-header.php"); ?>

			<?php include($documentRoot . "inc/homepage-services.php"); ?>
			
			<!-- Open huis banner -->
			<?php //include($documentRoot . "inc/homepage-banner.php"); ?>

			<?php include($documentRoot . "inc/homepage-actueel.php"); ?>

			<?php include($documentRoot . "inc/footer.php"); ?>

		</div>
		
		<?php include($documentRoot . "inc/footer-scripting.php"); ?>

		<script src="<?php echo $dynamicRoot; ?>js/jquery-ui-1.10.3.custom.min.js"></script>
		<script src="<?php echo $dynamicRoot; ?>js/global-makelaardij.js"></script>
	</body>

</html>
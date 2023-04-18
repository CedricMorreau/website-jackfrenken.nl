<?php

$template->cmsData('page][section/sfeerbeeld');
$sfeerbeeld = trim($template->getCustomVar('sfeerbeeld'));

?>

<!doctype html>

<html lang="nl">
	<head>
		
		<?php include($documentRoot . "inc/head.php"); ?>

	</head>

	<body>
		<!-- KMH pixel --> 
		<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
		new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0], j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src= '//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f); })(window,document,'script','kmhPixel','GTM-PX4GN2');</script> 
		<!-- End KMH pixel -->

		<div class="page-wrapper">

			<?php include($documentRoot . "inc/primary-nav.php"); ?>

			<section class="column-header">
				<div class="content-wrapper">
					<div class="header-main-content">
					<?php
					
					// Get nav name of highest parent
					$pageId = $template->findHighestParent();	
					$navName = $cms['database']->prepare("SELECT `mod_pa_nav` FROM `tbl_mod_pages` WHERE `mod_pa_id`=?", "i", array($pageId));
					
					?>

					<div class="header-title-wrapper">
						<div class="header-title">
							
							<h1>
							<?php 
						
							if (isset($pageOverride))
								echo $pageOverride;
							else
								echo $template->getPageDataMulti('navTitle');
							
							?>
							</h1>

							<a href="javascript:void(0);" onclick="javascript:$.scrollTo('#stappenplan', 1000)" class="scroll-link">Zie stappenplan</a>
						</div>
					</div>
					
					<?php
					
					if (!empty($sfeerbeeld)) {
					
					?>

					<div class="content-image" style="background-image: url(<?php echo $sfeerbeeld; ?>);">
						<!-- bg img -->
					</div>
					
					<?php } else { ?>
					
					<div class="content-image">
						<!-- bg img -->
					</div>
					
					<?php } ?>
					</div>
					
					<div class="breadcrumbs-row-wrapper">
						<div class="breadcrumbs-spacer"></div>
						<div class="breadcrumbs-wrapper">
							<ul>
								<li>home &rsaquo;</li>
								<li class="active">hypotheekadvies &rsaquo;</li>
							</ul>
						</div>

					</div>
				</div>
			</section>

			<!-- Page content -->
			<section class="wide-block-wrapper bg-gray">
				<div class="content-wrapper">
					<h2>Financieel advies van A tot Z</h2>
					<p>Bij Jack Frenken Financieel Advies ben je verzekerd van een compleet advies. Wij blijven jouw sparringpartner tijdens het hele proces van zoektocht tot notaris. Van de onderhandeling en het doornemen van de contracten, tot de uiteindelijke sleuteloverdracht. Wij werken samen met nagenoeg alle geldverstrekkers en verzekeraars, (aankoop)makelaars, taxateurs, bouwkundig experts. mediators en notarissen. Ook adviseren wij jou bij het afsluiten van de benodigde levens- en schadeverzekeringen. Wij verzorgen het gehele traject naar jouw droomwoning.</p>
				</div>
			</section>

			<section class="wide-block-wrapper">
				<div class="content-wrapper">
					<h2>Hypotheek <strong>op maat</strong></h2>
					<div class="square-blocks-wrapper">
						<a href="#" title="Starter" class="square-option"><h5>Starter<span>.</span></h5></a>
						<a href="#" title="Verhuizen" class="square-option"><h5>Verhuizen<span>.</span></h5></a>
						<a href="#" title="Relatie beëindiging" class="square-option"><h5>Relatie <br>beëindiging<span>.</span></h5></a>
						<a href="#" title="Senioren" class="square-option"><h5>Senioren<span>.</span></h5></a>
						<a href="#" title="Verduurzamen" class="square-option"><h5>Verduurzamen<span>.</span></h5></a>
						<a href="#" title="Ondernemer" class="square-option"><h5>Ondernemer<span>.</span></h5></a>
					</div>
				</div>
			</section>


			<section class="wide-block-wrapper image-banner" style="background-image: url('http://localhost:42069/resources/homepage-header.jpg')">
				<div class="content-wrapper">
					<h3>
						Voor een <em>gratis</em><br>
						intake bel met<br>
						onze <strong>Financieel <br>adviseurs</strong>
						<br><br>
						<strong><span>[]</span> (0475) 31 88 88</strong>
					</h3>
				</div>
			</section>

			<section class="wide-block-wrapper square-sub-blocks-section">
				<div class="content-wrapper">
					<div class="square-sub-blocks-wrapper">
						<a href="#" title="Uw maximale hypotheek (afspraak)" class="square-option">
							<div class="label">Afspraak</div>
							<h5>Uw maximale<br><strong>Hypotheek</strong></h5>
						</a>
						<a href="#" title="Hypotheek informatie" class="square-option"><h5>Hypotheek<br><strong>informatie</strong></h5></a>
						<a href="#" title="Nieuwbouw financieringen" class="square-option"><h5><strong>Nieuwbouw</strong><br>Financieringen</h5></a>
					</div>

					<h5 class="square-sub-block-title">De kracht van Jack Frenken<span>.</span></h5>
				</div>
			</section>

			<?php include($documentRoot . "inc/footer.php"); ?>

		</div>
		
		<?php include($documentRoot . "inc/footer-scripting.php"); ?>

		<script>

		</script>
	</body>

</html>
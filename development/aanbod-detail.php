<!doctype html>

<html lang="nl">
	<head>
		<meta charset="utf-8">
		<title>Jack Frenken Makelaars en adviseurs</title>
		<meta property="og:site_name" content="Jack Frenken Makelaars en adviseurs">
		<meta property="og:title" content="Jack Frenken Makelaars en adviseurs">
		<meta property="og:description" content="Jack Frenken Makelaars en adviseurs">
	  	<meta name="description" content="Jack Frenken Makelaars en adviseurs">
	  	<meta name="author" content="Pixelplus Interactieve Media">
		
		<?php include("../inc/head.php"); ?>

	</head>

	<body>
		<div class="page-wrapper aanbod-detail">

			<?php include("../inc_dev/primary-nav.php"); ?>

			<?php include("../inc_dev/aanbod-header.php"); ?>

			<div class="column-content">
				
				<div class="column-sidebar">
				
					<?php include("../inc/sidebar-nav.php"); ?>
				
					<?php include("../inc/widget.php"); ?>

					<a href="aanbod-overzicht.php" class="back-link">&xlarr; Terug naar overzicht</a>

				</div>

				<div class="column-content">

					<div class="content-wrapper">
						<a id="content" class="anchor"></a>
						<h2>Beschrijving</h2>
						<p class="intro">
							U zoekt rust, ruimte, de mogelijkheid om een bedrijf aan huis te starten, of gewoon een lekker groot
							huis met een eigen atelier of riante hobby ruimte? Deze riante vrijstaande geschakelde woning biedt u
							alle ruimte die u zoekt! Met een woonoppervlak van 215 m² en een perceel van maar liefst 1012m2 m²
							kunt u met wat werk al uw dromen realiseren.
						</p>

						<p>
						De indeling is als volgt:	
						</p>

						<p>
							Begane grond:<br>
							De hal brengt u direct in de riante woonkamer met aansluitend een open keuken. Aan de voorzijde van de woning is een aparte speel/werkkamer, die weer in verbinding staat met de werkruimte van ca. 80m2 aan de rechterzijde van de woning. Deze werkruimte is verdeeld in 2 vertrekken heeft een eigen ingang aan de straatzijde. Aan de achterzijde heeft de werkruimte een deur naar de tuin en komt er dus voldoende daglicht binnen. Verder biedt de begane grond ruimte aan een luxe badkamer met ligbad en douche, een slaapkamer, een kantoor- en wasruimte. Ook de garage links naast de woning is inpandig bereikbaar.
						</p>

						<p>
							1e Verdieping:<br>
							Vanuit de overloop zijn er 3 ruime slaapkamers bereikbaar en de badkamer. De badkamer is ingericht met een douche, wandcloset en badmeubel. De masterbedroom is zeer riant en alle slaapkamers zijn voorzien van laminaat.
						</p>

						<p>Zolderberging:<br>
							Middels een vlizotrap is de zolderberging bereikbaar.
						</p>
					</div>


				</div>
			</div>

			<?php include("../inc_dev/aanbod-banner.php"); ?>

			<?php include("../inc_dev/footer.php"); ?>

		</div>
		
		<?php include("../inc_dev/footer-scripting.php"); ?>

		<link rel="stylesheet" type="text/css" href="../js/royalslider/royalslider/royalslider.css">
		<link rel="stylesheet" type="text/css" href="../js/royalslider/royalslider/skins/minimal-white/rs-minimal-white.css">

		<script type="text/javascript" src="../js/royalslider/royalslider/jquery.royalslider.min.js"></script>
		
		<script type="text/javascript">
		
			// Start royalslider
			$(document).ready(function($) {
			  $('#royal-slider').royalSlider({
				  	autoHeight: true,
				    arrowsNav: false,
				    fadeinLoadedSlide: false,
					fullscreen: {
						enabled: true,
						nativeFS: false,
						buttonFS: true,
						controlsInside: true
					},
				    controlNavigationSpacing: 0,
				    controlNavigation: 'thumbnails',
				    thumbs: {
				        orientation: 'horizonal',
				        appendSpan: true
				    },
				    imageScaleMode: 'fill',
				    imageAlignCenter: false,
				    loop: false,
				    loopRewind: true,
				    numImagesToPreload: 3,
				    keyboardNavEnabled: true,
				    usePreloader: true,
				    transitionType: 'fade',
				    transitionSpeed: 100

			
				});

			});
		
			// EINDE ROYALSLIDER
			
		</script>
	</body>

</html>
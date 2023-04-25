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

		<div class="page-wrapper verduurzamen">

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
							else {

								if (!empty($template->getPageDataMulti('alternateTitle'))) {
			
									echo $template->getPageDataMulti('alternateTitle');
								}
								else {
			
									echo $template->getPageDataMulti('navTitle');
								}
							}
							
							?>
								<img src="<?php echo $dynamicRoot; ?>resources/line-arrow-lit-short.svg">		

							</h1>

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
			</section>

			<!-- Page content -->
			<section class="wide-block-wrapper bg-odd step-section center">
				<div class="content-wrapper">
					<h2>stappen <strong>plan</strong></h2>
					<p>Het afsluiten van een hypotheek kost enige tijd en vraagt de nodige deskundigheid. 
						<br>Voor het afsluiten van de hypotheek doorlopen we de volgende 5 stappen.</p>
				</div>
			</section>
			
			<!-- Stap 1 -->
			<section class="wide-block-wrapper step-section left">
				<div class="content-wrapper">
					<img src="/resources/stappenplan/stappenplan-stap-1.png" class="step-img" alt="Stap 1">
					<h2>Kennismaking</h2>
					<p>U maakt inzichtelijk hoe uw leven eruitziet, <br>
					wat u belangrijk vindt en wat uw toekomstplannen zijn.<br>
					<br>
					Onze hypotheekadviseur maakt een inschatting van uw maximale hypotheek en brengt de daarbij behorende maandlasten in kaart. <br>
					Daarnaast legt hij uit wat u verder van Jack Frenken kunt verwachten en berekent <br>
					hij de advies- en bemiddelingskosten.</p>
					
				</div>
			</section>
			
			<!-- Stap 2 -->
			<section class="wide-block-wrapper bg-gray step-section right">
				<div class="content-wrapper">
					<img src="/resources/stappenplan/stappenplan-stap-2.png" class="step-img" alt="Stap 2">

					<h2>Inventarisatie en analyse</h2>
					<p>Zodra u akkoord bent maken we een inventarisatie en<br>
						analyse van uw gehele financiële situatie.<br>
						In een persoonlijk klantprofiel leggen we uw wensen, <br>
						toekomstplannen en het maximale hypotheek bedrag vast.
					</p>
				</div>
			</section>

			<!-- Stap 3 -->
			<section class="wide-block-wrapper step-section left">
				<div class="content-wrapper">
					<img src="/resources/stappenplan/stappenplan-stap-3.png" class="step-img" alt="Stap 3">

					<h2>Advies</h2>
					<p>Met het persoonlijk klantprofiel als basis stelt onze
						<br>hypotheekadviseur een financieel advies op. We vergelijken hypotheken, rentetarieven en voorwaarden van verschillendehypotheekverstrekkers. We maken een selectie van een de best passende hypotheken.<br>
					<br>
					U krijgt inzicht in de financiële gevolgen van verschillende<br>
					levensveranderingen (verlies van inkomen, gezinsuitbreiding, een hoger of lager salaris, pensioen). We maken samen de keuze voor de juiste hypotheek.</p>
				</div>
			</section>

			<!-- Stap 4 -->
			<section class="wide-block-wrapper bg-gray step-section right">
				<div class="content-wrapper">
					<img src="/resources/stappenplan/stappenplan-stap-4.png" class="step-img" alt="Stap 4">

					<h2>Bemiddeling</h2>
					<p>Onze hypotheekadviseur zorgt voor een juiste verwerking van de aangevraagde hypotheek offerte en aanverwante producten.<br>
						<br>
						Samen met u zorgen wij dat het hypotheek dossier compleet naar de<br>
 						geldverstrekker wordt verstuurd. Wij zorgen dat de hypotheek compleet en tijdig naar de notaris verzonden zal worden.</p>
				</div>
			</section>	

			<!-- Stap 5 -->
			<section class="wide-block-wrapper step-section left">
				<div class="content-wrapper">
					<img src="/resources/stappenplan/stappenplan-stap-5.png" class="step-img" alt="Stap 5">

					<h2>Nazorg</h2>
					<p>Na het afsluiten bieden wij nazorg zodat u met uw vragen bij ons terecht kunt.<br>
						Onze adviseur zal u uitleggen hoe u deze standaard nazorg naar wens kunt uitbreiden.</p>
				</div>
			</section>	
			

			<section class="wide-block-wrapper bg-nth image-banner" style="background-image: url('/resources/fin-adv-team.jpg')">
				<div class="content-wrapper">
					<h3>
						Maak kennis met<br>
						<strong>onze financieel <br>
						adviseurs</strong>
					</h3>
				</div>
			</section>

			<section class="wide-block-wrapper bg-nth form-wrapper">
				<div class="content-wrapper form-content-wrapper">

					<form id="appointment-form">
						<h4>Afspraak maken</h4>

						<div class="form-message success" style="display: none;">
							Het formulier is verzonden. We nemen zo snel mogelijk contact met u op.
						</div>

						<div class="form-message error" style="display: none;">
							Er ging iets mis op de server. Probeer het nogmaals.
						</div>

						<div class="input-row-wrapper">
							<div class="input-wrapper">
								<input name="first_name" type="text" placeholder="Voornaam*">
							</div>
							<div class="input-wrapper">
								<input name="last_name" type="text" placeholder="Achternaam*">
							</div>
							
						</div>

						<div class="input-row-wrapper">
							<div class="input-wrapper">
								<input name="email" type="email" placeholder="E-mailadres*">
							</div>
							<div class="input-wrapper">
								<input name="phone" type="tel" placeholder="Telefoon*">
							</div>
							
						</div>

						<div class="button-wrapper">
							<button type="submit" class="cta-button qui inline">
								Verstuur dit bericht
							</button>
						</div>
					</form>
				</div>
			</section>

			<?php include($documentRoot . "inc/footer.php"); ?>

		</div>
		
		<?php include($documentRoot . "inc/footer-scripting.php"); ?>

		<script>

		</script>
	</body>

	<script>

		$(document).ready(function() {

			// Form
			$("#appointment-form").validate({
				// focusInvalid: false,
				errorPlacement: function(error, element) {},
				rules: {
					first_name: {
						required: true,
						minlength: 2
					},
					last_name: {
						required: true
					},
					email: {
						required: true,
						email: true
					},
					phone: {
						required: true
					},
					msg: {
						required: true,
						minlength: 4
					}
				},
				submitHandler: function(form) {
					return SubmitContactForm();
				}
			});
		});

		function SubmitContactForm() {

			$.ajax({
				type: 'POST',
				url: '/inc/process-appointment-form.php',
				data: $('#appointment-form').serialize(),
				success: function(data) {

					if (data == 0) {

						$("#appointment-form .form-message.error").fadeIn();
					} else {
						dataLayer.push({
							'event': 'contactformulier-submit'
						});
						$("#appointment-form .form-message.error").hide();
						$("#appointment-form .input-row-wrapper").hide();
						$("#appointment-form .button-wrapper").hide();
						$("#appointment-form .form-message.success").fadeIn();
					}
				}

			});

			return false;
		}

	</script>

</html>
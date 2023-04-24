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
							else {

								if (!empty($template->getPageDataMulti('alternateTitle'))) {
			
									echo $template->getPageDataMulti('alternateTitle');
								}
								else {
			
									echo $template->getPageDataMulti('navTitle');
								}
							}
							
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
			<section class="wide-block-wrapper bg-nth">
				<div class="content-wrapper">
					<h2>Verduurzaamhypotheek afsluiten? <strong>Check je mogelijkheden</strong></h2>
					<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent tempor lectus et est porta rhoncus. Integer vitae sollicitudin magna. Nullam mi arcu, feugiat et faucibus eu, molestie vel nisl.</p><p>Ut tortor leo, hendrerit sit amet est eget, interdum finibus felis. Donec fermentum dui vel mauris luctus, et tincidunt nisl bibendum. Quisque sit amet placerat augue. Nullam euismod quam arcu, euismod tempor sem rhoncus nec. Ut nisi eros, scelerisque et dolor eget, iaculis rutrum augue. Nam pulvinar at libero ac bibendum. Nunc molestie nisi velit, sit amet accumsan lacus scelerisque ac. Suspendisse cursus mi non nisl posuere, et ultrices lectus ornare. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Morbi placerat orci quis laoreet venenatis. Duis consectetur quam a dolor gravida laoreet.</p>
				</div>
			</section>

			<section class="wide-block-wrapper bg-nth">
				<div class="content-wrapper">
					<h2>Jouw woning <strong>verduurzamen</strong></h2>
					<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent tempor lectus et est porta rhoncus. Integer vitae sollicitudin magna. Nullam mi arcu, feugiat et faucibus eu, molestie vel nisl. Ut tortor leo, hendrerit sit amet est eget, interdum finibus felis. Donec fermentum dui vel mauris luctus, et tincidunt nisl bibendum. Quisque sit amet placerat augue. Nullam euismod quam arcu, euismod tempor sem rhoncus nec. Ut nisi eros, scelerisque et dolor eget, iaculis rutrum augue. Nam pulvinar at libero ac bibendum. Nunc molestie nisi velit, sit amet accumsan lacus scelerisque ac. Suspendisse cursus mi non nisl posuere, et ultrices lectus ornare. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Morbi placerat orci quis laoreet venenatis. Duis consectetur quam a dolor gravida laoreet.</p>
				</div>
			</section>

			<section class="wide-block-wrapper bg-nth">
				<div class="content-wrapper">
					<h2>De waarde van jouw woning <strong>verhogen</strong></h2>
					<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent tempor lectus et est porta rhoncus. Integer vitae sollicitudin magna.</p><p>Nullam mi arcu, feugiat et faucibus eu, molestie vel nisl. Ut tortor leo, hendrerit sit amet est eget, interdum finibus felis. Donec fermentum dui vel mauris luctus, et tincidunt nisl bibendum. Quisque sit amet placerat augue. Nullam euismod quam arcu, euismod tempor sem rhoncus nec. Ut nisi eros, scelerisque et dolor eget, iaculis rutrum augue. Nam pulvinar at libero ac bibendum. Nunc molestie nisi velit, sit amet accumsan lacus scelerisque ac. Suspendisse cursus mi non nisl posuere, et ultrices lectus ornare. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Morbi placerat orci quis laoreet venenatis. Duis consectetur quam a dolor gravida laoreet.</p>
				</div>
			</section>

			<section class="wide-block-wrapper bg-nth two-col">
				<div class="content-wrapper">
					<div class="flex col row one">
						<div class="flex col one margin-right-m">
							<img src="/resources/item-thumb.jpg" alt="">
						</div>
						<div class="flex col one column text-align-left">
							<h2 class="text-align-left">Daarom financieel advies bij <strong>Jack Frenken</strong></h2>

							<ul>
								<li>Een lagere energierekening</li>
								<li>Waardestijging van de woning</li>
								<li>Diverse mogelijkheden voor financiering (Bijv. Het energie bespaar budget)</li>
								<li>De juiste route naar subsidies</li>
								<li>Ook voor (startende) ondernemers (zie tegel ondernemer)</li>
							</ul>

							<a href="#" class="cta-button ghost inline">Zie stappenplan</a>
						</div>
					</div>
				</div>
			</section>

			<section class="wide-block-wrapper bg-nth image-banner" style="background-image: url('http://localhost:42069/resources/homepage-header.jpg')">
				<div class="content-wrapper">
					<h3>
						Jack Frenken<br>
						financieel Advies<br>
						<strong>De kracht <br>van Frenken</strong>
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
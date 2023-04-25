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

            <?php

            $textblock_ids = [15, 16, 17];

            foreach ($textblock_ids as $block) {

                $textBlock = trim($template->cmsData('page][section/content/blockId/' . $block));

                if (!empty($textBlock)) {

                ?>

                <!-- Page content -->
                <section class="wide-block-wrapper bg-nth">
                    <div class="content-wrapper">
                        <?php echo $template->handlePlaceholders($textBlock); ?>
                    </div>
                </section>

                <?php

                }
            }

            trim($template->cmsData('page][section/content/blockId/18'));
            $img_block = $template->getCustomVar('sfeerbeeld');

            $textBlock = trim($template->cmsData('page][section/content/blockId/19'));
            
            if (!empty($img_block) && !empty($textBlock)) {

            ?>

			<section class="wide-block-wrapper bg-nth two-col">
				<div class="content-wrapper">
					<div class="flex col row one">
						<div class="flex col one margin-right-m">
							<img src="<?php echo $img_block; ?>" alt="">
						</div>
						<div class="flex col one column text-align-left">
							<?php echo $template->handlePlaceholders($textBlock); ?>
						</div>
					</div>
				</div>
			</section>

            <?php } ?>

			<?php

			$textBlock = trim($template->cmsData('page][section/content/blockId/63'));

			if (!empty($textBlock)) {

			?>

			<section class="wide-block-wrapper bg-nth image-banner" style="background-image: url('/resources/fin-adv-team.jpg')">
				<div class="content-wrapper">
					<?php echo $template->handlePlaceholders($textBlock); ?>
				</div>
			</section>

			<?php } ?>

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
<?php

// First of all grab all locations
$locaties = $cms['database']->prepare("SELECT * FROM `tbl_cms_locaties` WHERE `cl_status`=1 ORDER BY `cl_sortOrder` ASC");

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

		<div class="page-wrapper contact">

			<?php include($documentRoot . "inc/primary-nav.php"); ?>

			<?php include($documentRoot . "inc/contact-header.php"); ?>

			<div class="column-content">
				
				<div class="column-sidebar">
				
					<?php include($documentRoot . "inc/contact-widget.php"); ?>
					
					
					<!-- <a href="<?php echo $url; ?>" class="back-link ali-top"><span>&#9658;</span> terug naar <strong>overzicht</strong></a> -->

				</div>

				<div class="column-content">

					<div class="content-wrapper">
					
						<?php
						
						if (count($locaties) > 0) {
							
							foreach ($locaties as $key => $val) {
								
								$uniId = strtolower(str_replace(' ', '-', $val['cl_name']));
								
								?>
								
						<a id="<?php echo $uniId; ?>" class="anchor"></a>
						
						<h2><?php echo $val['cl_name']; ?></h2>
						<div class="location-wrapper" id="vestiging_<?php echo $key; ?>">
							
							<div class="location-details">
								<p>
									<a href="<?php echo $val['cl_googleLink']; ?>" target="_blank">
									<?php echo $val['cl_adres_straat']; ?> <?php echo $val['cl_adres_huisnummer']; ?><br>
									<?php echo $val['cl_adres_postcode']; ?> <?php echo $val['cl_adres_plaats']; ?>
									</a>
								</p>

								<p>
									<?php if (!empty($val['cl_telefoon'])) { ?>
									Tel: <?php echo $val['cl_telefoon']; ?><br>
									<?php } ?>
									<?php if (!empty($val['cl_fax'])) { ?>
									Fax: <?php echo $val['cl_fax']; ?><br>
									<?php } ?>
									<?php if (!empty($val['cl_email'])) { ?>
									<a href="mailto:<?php echo $val['cl_email']; ?>">
										<?php echo $val['cl_email']; ?>
									</a>
									<?php } ?>
								</p>
								
								<?php
					
								$times = $cms['database']->prepare("SELECT * FROM `tbl_cms_locatieOpening` WHERE `clo_locatieId`=? ORDER BY `clo_sortOrder` ASC, `clo_day` ASC", "i", array($val['cl_id']));
								
								if (count($times) > 0) {
									
									?>
									
								<table class="times-table">
								
									<?php
									
									foreach ($times as $sKey => $sVal) {
										
										$today = ($sVal['clo_day'] == date('w')) ? ' class="today"' : '';
										
										?>
										
									<tr<?php echo $today; ?>>
										<td><?php echo $sVal['clo_dayName']; ?></td>
										<td><?php echo $sVal['clo_openTime']; ?></td>
									</tr>	
										
										<?php
									}
									
									?>
								
								</table>
									
									<?php	
								}
								
								?>
							</div>
							<div class="location-photo">
								<img src="<?php echo $val['cl_photo']; ?>" alt="<?php echo $val['cl_name']; ?>">
							</div>
						</div>
								
								<?php
							}
						}
						
						?>

						<div class="social-wrapper">
							<div>Kvk-nummer: <?php echo $locaties[0]['cl_kvk']; ?></div>
						</div>
					</div>


				</div>
			</div>

			<?php include($documentRoot . "inc/footer.php"); ?>
		</div>
		
		<?php

		define('EXCEPTIONALBS', true);
		
		include($documentRoot . 'inc/map-script.php');
		
		?>

		<!-- Contact overlay -->
		<script>

			(function(doc) {
				//console.log("contact");
				var iconContact = doc.querySelector('[data-hook="Icon-contact"]');
				var contact = doc.querySelector('[data-hook="Contact"]');
				var contactText = doc.querySelector('[data-hook="Contact-text"]');
				var contactClose = doc.querySelector('[data-hook="Contact-close"]');
				var contactClosetwo = doc.querySelector('[data-hook="Contact-closetwo"]');
				
				// show overlay
				function showContact() {
				contact.classList.add('Contact--show');
				contact.classList.add('a-fadeIn');
				doc.body.classList.add('u-overflow-hidden');
				}

				// close overlay
				function hideContact() {
					contact.classList.add('a-fadeOut');

					setTimeout(function(){
					  contact.classList.remove('Contact--show');
					  contact.classList.remove('a-fadeOut');
					  contactText.classList.remove('Contact-text--hide');
					  doc.body.classList.remove('u-overflow-hidden');
					}, 100);
				}
				
				// close on ESC
				doc.addEventListener('keyup', function(e) {
					if (e.keyCode == 27) { 
					  hideContact();
					}
				});


				iconContact.addEventListener('click', showContact);
				contactClose.addEventListener('click', hideContact);
				contactClosetwo.addEventListener('click', hideContact);

			})(document);

			$(document).ready(function(){

				// Form
				$("#contactForm").validate({
					// focusInvalid: false,
					errorPlacement: function(error, element) {},
					rules: {
						contact_name: {
							required: true,
							minlength: 2
						},
						contact_email: {
							required: true,
							email: true
						},
						contact_phone: {
							required: true
						},
						contact_msg: {
							required: true,
							minlength: 4
						}
					},
					submitHandler: function(form) {
						return SubmitContactForm();
					}
				});
			});

			function SubmitContactForm(){
				
				$.ajax({
					type	: 'POST',
					url 	: '/inc/process-contactform.php',
					data	: $('#contactForm').serialize(),
					success	: function(data){

						if (data == 0) {

							$("#contactForm p.error").fadeIn();
						}
						else {
							dataLayer.push({
								'event': 'contactformulier-submit'
							});
							$("#contactForm p.error").hide();
							$("#contactForm").hide();
							$("#bedankt-melding").fadeIn();
							$(".close-overlay-text").text('Sluiten');
						}
					}
				
				});

				return false;
			}

			function scrollVestiging(id) {
	
				$('html, body').animate({
					scrollTop: $('#vestiging_' + id).offset().top
			    }, 1000);
			}

		</script>
		<?php include($documentRoot . "inc/footer-scripting.php"); ?>

	</body>

</html>
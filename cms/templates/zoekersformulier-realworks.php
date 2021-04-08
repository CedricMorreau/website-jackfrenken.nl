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

		<div class="page-wrapper zoekersformulier">

			<?php include($documentRoot . "inc/primary-nav.php"); ?>

			<?php include($documentRoot . "inc/2-col-header.php"); ?>

			<div class="column-content">
				
				<div class="column-sidebar">

					<?php echo $template->cmsData('page][navigation/2/subnav/' . $template->findHighestParent() . '/active/' . $template->getPageId()); ?>
		
					<?php echo $template->cmsData('page][section/widgets'); ?>

				</div>

				<div class="column-content">

					<div class="content-wrapper">
						<h2>Schrijf u in als zoeker!</h2>
						<p class="subtitle">De woning van uw dromen nog niet kunnen vinden? Wij zoeken graag met u mee.</p>

						<?php include ($documentRoot . "inc/zoekersformulier-form.php"); ?>


					</div>
				</div>
			</div>	


			<?php include($documentRoot . "inc/footer.php"); ?>

		</div>
		
		<?php include($documentRoot . "inc/footer-scripting.php"); ?>
		
		<script type="text/javascript" src="<?php echo $dynamicRoot; ?>js/jquery.validate.js"></script>
		<script type="text/javascript" src="<?php echo $dynamicRoot; ?>js/jquery.multiselects-0.3.js"></script>

		<script>

			$(document).ready(function(){

				$("select[name='plaatsnamenOverzicht']").multiSelect("select[name='plaatsnaam[]']", {
					trigger: ".add"
				});

				$("select[name='plaatsnaam[]']").multiSelect("select[name='plaatsnamenOverzicht']", {
					trigger: ".remove"
				});

				$("#zoeker-formulier").validate({
					focusInvalid: false,
					errorPlacement: function(error, element) { },
					rules: {
						voornaam: "required",
						achternaam: "required",
						contactStraat: "required",
						contactHuisnummer: "required",
						contactPostcode: "required",
						contactPlaats: "required",
						contactTelefoon: "required",
						contactEmail: {
							required: true,
							email: true
						}
					},
					submitHandler: function(form) {
						return SubmitContactForm();
					}
				});
			});

			function SubmitContactForm(){

				$('.form_error').fadeOut('slow');
				$('#zoeker-formulier').fadeOut('slow', function(){
				$('.form_loading').css({ display : 'none' }).fadeIn('slow');
					$.ajax({
						type	: 'POST',
						url 	: '<?php echo $dynamicRoot; ?>inc/process_zoekersformulier-realworks.php',
						data	: $('#zoeker-formulier').serialize(),
						success	: function(data){
							$('.form_loading').fadeOut('fast', function(){
								if(!data){
									$('.form_error').css({ display : 'none' }).fadeIn('fast');
									$('#zoeker-formulier').fadeIn('fast');
								} else {
									$('#zoeker-formulier').remove();
									$('.form_result').css({ display : 'none' }).fadeIn();
									// $('#zoekersForm').html(data); //Test mail output
								}
							});
						}
					
					});


				});
				return false;
			}

		</script>
	</body>

</html>
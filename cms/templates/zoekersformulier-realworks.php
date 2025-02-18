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
						<h2><strong>Schrijf</strong> u in als <strong>zoeker!</strong></h2>
						<p class="subtitle">De woning van uw dromen nog niet kunnen vinden? Wij zoeken graag met u mee.</p>

						<?php include ($documentRoot . "inc/zoekersformulier-form.php"); ?>


					</div>
				</div>
			</div>	


			<?php include($documentRoot . "inc/footer.php"); ?>

		</div>
		
		<?php include($documentRoot . "inc/footer-scripting.php"); ?>
		
		<script type="text/javascript" src="<?php echo $dynamicRoot; ?>js/jquery.multiselects-0.3.js"></script>

		<script>
			window.isRent = false;

			var formatter = new Intl.NumberFormat('nl-NL', {
				style: 'currency',
				currency: 'EUR',
			});

			const updatePriceFrom = () => generatePriceOptions('select[name=prijsVanaf]', window.isRent ? [ 500, 1000, 1500, 2000, 2500, 3000, 10000, 35000 ] :
					[ 0, 100000, 200000, 300000, 400000, 500000, 600000, 700000, 800000, 900000, 1000000, 1250000 ], true);

			const updatePriceTo = () => generatePriceOptions('select[name=prijsTot]', window.isRent ? [ 500, 1000, 1500, 2000, 2500, 3000, 10000, 35000 ] :
					[ 100000, 200000, 300000, 400000, 500000, 600000, 700000, 800000, 900000, 1000000, 1250000 ], false);
			
			function generatePriceOptions(element, object, from) {
				let output = [];

				if (from === true)
					output.push('<option>Prijs vanaf</option>');
				else
					output.push('<option>Prijs tot</option>');

				for (const option of object) {
					output.push('<option value="' + option + '">' + (from == true ? 'Vanaf ' : 'Tot ')
						+ formatter.format(option) + '</option>');
				}

				$(element).html(output.join('\n'));
			}

			$(document).ready(function(){

				$("select[name='plaatsnamenOverzicht']").multiSelect("select[name='plaatsnaam[]']", {
					trigger: ".add"
				});

				$("select[name='plaatsnaam[]']").multiSelect("select[name='plaatsnamenOverzicht']", {
					trigger: ".remove"
				});

				$('#financieel-kopen').click(function () {
					$('#financieel-huren').removeClass('error');
					$('#financieel-kopen').removeClass('error');

					window.isRent = false;
					
					updatePriceFrom();
					updatePriceTo();
				});

				$('#financieel-huren').click(function () {
					$('#financieel-kopen').removeClass('error');
					$('#financieel-huren').removeClass('error');

					window.isRent = true;

					updatePriceFrom();
					updatePriceTo();
				});

				$("#zoeker-formulier").validate({
					focusInvalid: false,
					errorPlacement: function(error, element) {

						if (element.attr('id') == 'financieel-kopen')
							$('#financieel-huren').addClass('error');

						if (element.attr('id') == 'financieel-huren')
							$('#financieel-kopen').addClass('error');

						return true;
					},
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
						},
						'plaatsnaam[]': "required",
						prijsVanaf: "required",
						prijsTot: "required",
						'soortAankoop[]': "required",
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
								if(data != 1){
									$('.form_error').css({ display : 'none' }).fadeIn('fast');
									$('#zoeker-formulier').fadeIn('fast');
								} else {
									$('#zoeker-formulier').remove();
									$('.form_result').css({ display : 'none' }).fadeIn();
									 dataLayer.push({
									  'event': 'zoekersformulier-submit'
									 });
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
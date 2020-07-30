<?php

// Grab the proper download
$article = $cms['database']->prepare("SELECT * FROM `tbl_mod_articleContent` WHERE EXISTS(SELECT * FROM `tbl_mod_articleContentValues` WHERE `mod_cv_articleId`=`mod_co_id` AND `mod_cv_attributeId`=28 AND `mod_cv_value`=?)", "s", array($template->detailPage));

if (count($article) == 0)
	Core::redirect('/');

$dataArray = Content::getArticleValues($article[0]['mod_co_id'], $cms, $template->getCurrentLanguage());

if (!isset($dataArray['dl_status'][11]))
	Core::redirect('/');

$title = $dataArray['dl_title'];
$injectTitle = $title;
$pageOverride = $dataArray['dl_title'];

$sfeerbeeld = $dataArray['dl_sfeerbeeld'];

$hasImage = false;

if (!empty($sfeerbeeld))
	$hasImage = true;

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

		<div class="page-wrapper aanbod-detail">

			<?php include($documentRoot . "inc/primary-nav.php"); ?>

			<?php include($documentRoot . "inc/2-col-header.php"); ?>

			<div class="column-content">
	
				<div class="column-sidebar">
				
					<?php echo $template->cmsData('page][navigation/2/subnav/' . $template->findHighestParent() . '/active/' . $template->getPageId()); ?>

				</div>

				<div class="column-content">

					<div class="content-wrapper">
					
						<?php echo $dataArray['dl_content']; ?>

						<div id="object-contact-form">

							<p class="form-header">Vul je gegevens in om de rapportage te downloaden.</p>

							<div id="download-form-output" class="clearfix">
								<div class="form_loading group" style="display: none;">
									<p>
										<i>Een ogenblik geduld a.u.b.</i>
									</p>
								</div>
								<div class="form_error general" style="display: none;"><h2>Foutje</h2><p>Er ging iets mis op de server. Probeer het nog eens.</p></div>
								<div class="form_result" style="display: none;"></div>
							</div>

							<form id="download-form" class="standard flex-row flex-wrap">
								
								<fieldset class="flex-col size100">
									<input type="hidden" name="page" value="<?php echo $template->detailPage; ?>">
									<input type="text" name="name" value="" placeholder="Naam*">
									<input type="text" name="telnr" value="" placeholder="Woonplaats*">
									<input type="email" name="email" value="" placeholder="E-mailadres*">
									<input type="submit" name="object-contact-submit" value="Verstuur dit bericht">
								</fieldset>

							</form>

						</div>
					</div>
				</div>
			</div>


			<?php include($documentRoot . "inc/footer.php"); ?>

		</div>
		
		<?php include($documentRoot . "inc/footer-scripting.php"); ?>
		<script type="text/javascript" src="<?php echo $dynamicRoot; ?>js/jquery.validate.js"></script>

		<script type="text/javascript">

			// Download form
			$(document).ready(function() {

			$("#download-form").validate({
				focusInvalid: false,
				errorPlacement: function(error, element) {},
				rules: {
					name: {
						required: true,
						minlength: 2
					},
					telnr: {
						required: true,
						minlength: 2
					},
					email: {
						required: true,
						minlength: 2
					}
				},
				submitHandler: function(form) {
					return SubmitDownloadForm();
				}
			});
			});

			function SubmitDownloadForm(){

				$('#download-form-output .form_error').fadeOut('slow');
				$('#download-form').fadeOut('slow', function(){

				$('#download-form-output .form_loading').css({ display : 'none' }).fadeIn('slow');
					$.ajax({
						type	: 'POST',
						url 	: '/inc/process-form-download.php',
						data	: $('#download-form').serialize(),
						success	: function(data){
							$('#download-form-output .form_loading').fadeOut('fast', function(){
								if(!data){

									$('#download-form-output .form_error.general').css({ display : 'none' }).fadeIn('fast');
									$('#download-form').fadeIn('fast');
								} else {

									$('#download-form').remove();
									$('.form-header').remove();
									$('#download-form-output .form_result').css({ display : 'none' }).html(data).fadeIn();
									// $('#object-contact-form').html(data); //Test mail output
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
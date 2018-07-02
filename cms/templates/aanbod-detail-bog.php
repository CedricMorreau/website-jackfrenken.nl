<?php

function translateDay($val, $type = 'default') {

	$en = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday", "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");

	if ($type == 'short') {

		$nl = array("ma", "di", "wo", "do", "vr", "za", "zo", "Januari", "Februari", "Maart", "April", "Mei", "Juni", "Juli", "Augustus", "September", "Oktober", "November", "December");
	}
	else {

		$nl = array("Maandag", "Dinsdag", "Woensdag", "Donderdag", "Vrijdag", "Zaterdag", "Zondag", "januari", "februari", "maart", "april", "mei", "juni", "juli", "augustus", "september", "oktober", "november", "december");
	}

	return str_replace($en, $nl, $val);
}

$moduleId = $template->getModuleId();

$objectData = $cms['database']->prepare("SELECT * FROM `tbl_OG_bog` WHERE `id`=? AND (NOT `objectDetails_Status_StatusType` IN ('Ingetrokken', 'ingetrokken', 'verkocht', 'gearchiveerd', 'Verkocht', 'Gearchiveerd'))", "i", array($moduleId));

if (count($objectData) == 0)
	Core::redirect($template->findPermalink(112, 1) . '.html');

$val = $objectData[0];

$priceText = obj_showPrice($val['objectDetails_Koop_KoopConditie'], $val['objectDetails_Koop_PrijsSpecificatie_Prijs'], $val['objectDetails_Koop_KoopConditie'], $val['objectDetails_Huur_PrijsSpecificatie_Prijs'], $val['objectDetails_Huur_HuurConditie']);

if ($val['object_Web_PrijsTonen'] == 'nee') {
	
	$priceText = 'Prijs op aanvraag';
}

// Find other media
$extraMedia = array();

$findMedia = $cms['database']->prepare("SELECT * FROM `tbl_OG_media` WHERE `id_OG_bog`=? AND `media_Groep` IN ('Brochure', 'Overig') AND `media_status`=2 ORDER BY `media_Id` ASC", "i", array($val['id']));

if (count($findMedia) > 0) {

	foreach ($findMedia as $key => $sVal) {

		switch ($sVal['media_Groep']) {

			case 'Brochure':

				$extraMedia['brochure'] = $dynamicRoot . 'og_media/bog_' . $val['object_NVMVestigingNR'] . '_' . $val['object_ObjectTiaraID']. '/' . $sVal['bestandsnaam'];

				break;

			default:

			// Plattegrond (Floorplanner)
			if (strpos('http://pl.an/', $sVal['media_URL']) !== false) {

				$extraMedia['plattegrond'] = $sVal['media_URL'];
			}

			// Virtuele tour (Virtueletourzien.nl)
			if (strpos($sVal['media_URL'], 'http://bestellen.virtueletourzien.nl/') !== false) {

				$extraMedia['virtueleTour'] = $sVal['media_URL'];
			}

			// Video (mp4)
			if (strpos($sVal['media_URL'], '.mp4?') !== false) {

				$extraMedia['video'] = $sVal['media_URL'];
			}

			// Video (vimeo)
			if (strpos($sVal['media_URL'], 'vimeo.com/') !== false) {

				$extraMedia['vimeo'] = str_replace(array('http://', 'https://', 'www.vimeo.com/', 'vimeo.com/'), array('', '', '', ''), $sVal['media_URL']);
			}
		}
	}
}

// Fetch extra object details (if any)
$objectDetails = $cms['database']->prepare("SELECT * FROM `tbl_OG_objectDetails` WHERE `ood_table`=? AND `ood_ogId`=? LIMIT 1", "si", array('tbl_OG_bog', $val['id']));

if (count($objectDetails) > 0) {

	$detailData = $objectDetails[0];
}
else {

	$detailData = null;
}

$fetchImage = $cms['database']->prepare("SELECT `bestandsnaam_tn`, `bestandsnaam_medium` FROM `tbl_OG_media` WHERE `id_OG_bog`=? AND `media_status`=2 AND `media_groep`='Hoofdfoto'", "i", array($val['id']));

if (count($fetchImage) > 0) {

	$headImage = $dynamicRoot . 'og_media/bog_' . $val['object_NVMVestigingNR'] . '_' . $val['object_ObjectTiaraID']. '/' . $fetchImage[0]['bestandsnaam_medium'];
	$injectImage = $headImage;
}
else {

	$headImage = $dynamicRoot . 'img/aanbod_geen-afbeelding_tn02.svg';
}

$typeText = 'Te koop';

if ($val['objectDetails_Huur_PrijsSpecificatie_Prijs'] > 0) { $typeText = 'Te huur'; } else { $typeText = 'Te koop'; }

$injectTitle = $typeText . ': ' . ucwords($val['objectDetails_Bestemming_Hoofdbestemming']) . ' te ' . $val['objectDetails_Adres_Woonplaats'] . ', ' . obj_generateAddress($val['objectDetails_Adres_Straatnaam'], $val['objectDetails_Adres_Huisnummer'], $val['objectDetails_Adres_HuisnummerToevoeging']) . ' | Jack Frenken - Makelaars en Adviseurs';
$injectDescription = $injectTitle;

// Breadcrumbs
$extraCrumb = array();
$extraCrumb[ucwords($val['objectDetails_Bestemming_Hoofdbestemming']) . ' te ' . $val['objectDetails_Adres_Woonplaats'] . ', ' . obj_generateAddress($val['objectDetails_Adres_Straatnaam'], $val['objectDetails_Adres_Huisnummer'], $val['objectDetails_Adres_HuisnummerToevoeging'])] = 'javascript:void(0);';

if (isset($_GET['searchHash'])) {

	$returnUrl = $template->findPermalink(112, 1) . '.html&searchHash=' . $_GET['searchHash'];
}
else {

	$returnUrl = $template->findPermalink(112, 1) . '.html';
}

$mediaList = $cms['database']->prepare("SELECT `id`, `object_ObjectTiaraID`, `bestandsnaam`, `bestandsnaam_tn`, `bestandsnaam_medium`, `media_MediaOmschrijving` FROM `tbl_OG_media` WHERE `id_OG_bog`=? AND `media_status`=2 AND `media_Groep` IN ('HoofdFoto', 'Foto') ORDER BY `media_Groep` DESC, `media_Id` ASC", "i", array($val['id']));

$ogType = 'bog';

?>

<!doctype html>

<html lang="nl">
	<head>
		
		<?php include($documentRoot . "inc/head.php"); ?>
		
		<meta data-ogId="<?php echo $moduleId; ?>" data-feed="wonen" data-objectTiaraId="<?php echo $val['object_ObjectTiaraID']; ?>">

	</head>

	<body>
		<div class="page-wrapper aanbod-detail">

			<?php include($documentRoot . "inc/primary-nav.php"); ?>

			<?php include($documentRoot . "inc/aanbod-header-bedrijven.php"); ?>

			<div class="column-content">
				
				<div class="column-sidebar">
				
					<ul class="sidebar-nav">
						<li class="active"><a href="javascript:void(0);" data-tab="1" data-desc="Beschrijving" onclick="changeTab(1);">Beschrijving &xrarr;</a></li>
						<li><a href="javascript:void(0);" data-tab="2" data-desc="Kenmerken" onclick="changeTab(2);">Kenmerken</a></li>
						<li><a href="javascript:void(0);" data-tab="3" data-desc="Locatie" onclick="changeTab(3);">Locatie</a></li>
						<?php if (!empty($extraMedia['brochure'])) { ?>
						<li><a href="javascript:void(0);" data-tab="4" data-desc="Downloads" onclick="changeTab(4);">Downloads</a></li>
						<?php } ?>
						<li><a href="javascript:void(0);" data-tab="5" data-desc="Contact" onclick="changeTab(5);">Contact</a></li>
					</ul>
				
					<?php include($documentRoot . "inc/widget.php"); ?>
					
					<?php
					
					if (!($url = $template->getBackUrl($template->findPermalink(37, 1)))) {
						
						$url = $template->findPermalink(37, 1);
					}
					
					?>

					<a href="<?php echo $url; ?>" class="back-link">&xlarr; Terug naar overzicht</a>

				</div>

				<div class="column-content">
					<a id="content" class="anchor"></a>

					<div class="content-wrapper" data-tab="1">
						<h2>Beschrijving</h2>
						
						<?php echo utf8_encode(nl2br(Core::fixEncoding($val['objectDetails_Aanbiedingstekst']))); ?>
					</div>

					<div class="content-wrapper" data-tab="2" style="display: none;">
						<div class="table-flex-wrapper">
							<div class="table-wrapper">					
								<h2>Overdracht</h2>

								<table cellpadding="0" cellspacing="0" border="0">

									<tr>
										<th>Aangeboden sinds</th>
										<td>
										
										<?php

										$dateAdded = strtotime($val['objectDetails_DatumInvoer']);
										$dateNow = time();
										$dateDiff = time() - $dateAdded;
										$weeks = ceil($dateDiff / (7 * 86400));

										echo $weeks . (($weeks == 1) ? ' week' : ' weken');

										?>

										</td>
									</tr>

									<?php

									if (!empty($val['objectDetails_Aanvaarding_TypeAanvaarding'])) {

										?>

									<tr>
										<th class="description">Aanvaarding</th>
										<td class="value">
										
										<?php

										echo ucfirst($val['objectDetails_Aanvaarding_TypeAanvaarding']);

										?>

										</td>
									</tr>

										<?php
									}
									?>

								</table>
							</div>

							<div class="table-wrapper">					

								<h2>Bebouwing</h2>

								<table>

									<?php

									if (!empty($val['objectDetails_Bouwvorm'])) {

										?>

									<tr>
										<th class="description">Soort bouw</th>
										<td class="value">
										
										<?php

										echo ucfirst($val['objectDetails_Bouwvorm']);

										?>

										</td>
									</tr>

										<?php
									}
									?>

									<?php

									if (!empty($val['objectDetails_Bestemming_Hoofdbestemming'])) {

										?>

									<tr>
										<th class="description">Hoofdbestemming</th>
										<td class="value">
										
										<?php

										echo ucfirst($val['objectDetails_Bestemming_Hoofdbestemming']);

										?>

										</td>
									</tr>

										<?php
									}
									?>

									<?php

									if (!empty($val['objectDetails_Bestemming_Nevenbestemmingen'])) {

										?>

									<tr>
										<th class="description">Nevenbestemming</th>
										<td class="value">
										
										<?php

										$explodedTemp = explode(',', $val['objectDetails_Bestemming_Nevenbestemmingen']);

										foreach ($explodedTemp as $subKey => $subVal) {

											$newValue = str_replace(array('[', ']'), array('', ''), $subVal);

											echo (($subKey == 0) ? ucfirst($newValue) : ', ' . $newValue);
										}

										?>

										</td>
									</tr>

										<?php
									}
									?>

									<?php

									if (!empty($val['objectDetails_Bouwjaar_JaarOmschrijving_Jaar'])) {

										?>

									<tr>
										<th class="description">Bouwjaar</th>
										<td class="value">
										
										<?php

										echo ucfirst($val['objectDetails_Bouwjaar_JaarOmschrijving_Jaar']);

										?>

										</td>
									</tr>

										<?php
									}
									?>

									<?php

									if (!empty($val['objectDetails_Kantoorruimte_Verdiepingen'])) {

										?>

									<tr>
										<th class="description">Aantal bouwlagen</th>
										<td class="value">
										
										<?php

										echo ucfirst($val['objectDetails_Kantoorruimte_Verdiepingen']);

										?>

										</td>
									</tr>

										<?php
									}
									?>

								</table>
							</div>

							<div class="table-wrapper">					

								<h2>Kenmerken</h2>

								<table>

									<?php if (!is_null($val['objectDetails_Woonobject_Oppervlakte']) && $val['objectDetails_Woonobject_Oppervlakte'] > 0) { ?>
									<tr>
										<th class="description">Woonobject</th>
										<td class="value">
										
										<?php echo number_format($val['objectDetails_Woonobject_Oppervlakte'], 0, ",", "."); ?> m<sup>2</sup>

										</td>
									</tr>
									<?php } ?>
									<?php if (!is_null($val['objectDetails_Bedrijfshal_Oppervlakte']) && $val['objectDetails_Bedrijfshal_Oppervlakte'] > 0) { ?>
									<tr>
										<th class="description">Bedrijfshal</th>
										<td class="value">
										
										<?php echo number_format($val['objectDetails_Bedrijfshal_Oppervlakte'], 0, ",", "."); ?> m<sup>2</sup>

										</td>
									</tr>
									<?php } ?>
									<?php if (!is_null($val['objectDetails_Kantoorruimte_Oppervlakte']) && $val['objectDetails_Kantoorruimte_Oppervlakte'] > 0) { ?>
									<tr>
										<th class="description">Kantoorruimte</th>
										<td class="value">
										
										<?php echo number_format($val['objectDetails_Kantoorruimte_Oppervlakte'], 0, ",", "."); ?> m<sup>2</sup>

										</td>
									</tr>
									<?php } ?>
									<?php if (!is_null($val['objectDetails_BKantoorruimte_Oppervlakte']) && $val['objectDetails_BKantoorruimte_Oppervlakte'] > 0) { ?>
									<tr>
										<th class="description">Kantoorruimte</th>
										<td class="value">
										
										<?php echo number_format($val['objectDetails_BKantoorruimte_Oppervlakte'], 0, ",", "."); ?> m<sup>2</sup>

										</td>
									</tr>
									<?php } ?>
									<?php if (!is_null($val['objectDetails_Terrein_Oppervlakte']) && $val['objectDetails_Terrein_Oppervlakte'] > 0) { ?>
									<tr>
										<th class="description">Terrein</th>
										<td class="value">
										
										<?php echo number_format($val['objectDetails_Terrein_Oppervlakte'], 0, ",", "."); ?> m<sup>2</sup>

										</td>
									</tr>
									<?php } ?>
									<?php if (!is_null($val['objectDetails_Horeca_Oppervlakte']) && $val['objectDetails_Horeca_Oppervlakte'] > 0) { ?>
									<tr>
										<th class="description">Horeca</th>
										<td class="value">
										
										<?php echo number_format($val['objectDetails_Horeca_Oppervlakte'], 0, ",", "."); ?> m<sup>2</sup>

										</td>
									</tr>
									<?php } ?>
									<?php if (!is_null($val['objectDetails_Winkelruimte_Oppervlakte']) && $val['objectDetails_Winkelruimte_Oppervlakte'] > 0) { ?>
									<tr>
										<th class="description">Winkelruimte</th>
										<td class="value">
										
										<?php echo number_format($val['objectDetails_Winkelruimte_Oppervlakte'], 0, ",", "."); ?> m<sup>2</sup>

										</td>
									</tr>
									<?php } ?>

								</table>
							</div>

							<div class="table-wrapper">					

								<h2>Voorzieningen</h2>

								<table cellpadding="0" cellspacing="0" border="0">

									<?php

									if (!empty($val['objectDetails_Lokatie_Parkeren_Parkeerplaatsen_Aantal'])) {

										?>

									<tr>
										<th class="description">Parkeerplaatsen</th>
										<td class="value">
										
										<?php

										echo ucfirst($val['objectDetails_Lokatie_Parkeren_Parkeerplaatsen_Aantal']);

										?>

										</td>
									</tr>

										<?php
									}
									else {

										?>

									<tr>
										<th class="description">Parkeerplaatsen</th>
										<td class="value">
										
										Geen

										</td>
									</tr>

										<?php
									}
									?>

									<?php

									if (!empty($val['objectDetails_Kantoorruimte_Opleveringsniveau'])) {

										?>

									<tr>
										<th class="description">Opleveringsniveau</th>
										<td class="value">
										
										<?php

										$explodedTemp = explode(',', $val['objectDetails_Kantoorruimte_Opleveringsniveau']);

										foreach ($explodedTemp as $subKey => $subVal) {

											$newValue = str_replace(array('[', ']'), array('', ''), $subVal);

											echo (($subKey == 0) ? ucfirst($newValue) : ', ' . $newValue);
										}

										?>

										</td>
									</tr>

										<?php
									}
									?>

								</table>	
							</div>	
						</div>
						
					</div>
					

					<div class="content-wrapper" data-tab="3" style="display: none;">
						<h2>Locatie</h2>
						<div id="map_canvas"></div>
						<div id="pano"></div>
					</div>
					
					<?php if (!empty($extraMedia['brochure'])) { ?>

					<div class="content-wrapper" data-tab="4" style="display: none;">
						<h2>Downloads</h2>
						<ul>
							<li><a href="<?php echo $extraMedia['brochure']; ?>" title="Download de brochure" target="_blank">Download de brochure</a></li>
						</ul>
					</div>
					
					<?php } ?>

					<div class="content-wrapper" data-tab="5" style="display: none;">
						<div id="object-contact-form">

							<div id="object-contact-form-output" class="clearfix">
								<div class="form_loading group" style="display: none;">
									<p>
										<i>Het contactformulier wordt verstuurd&hellip;</i>
									</p>
								</div>
								<div class="form_error general" style="display: none;"><h2>Foutje</h2><p>Er ging iets mis op de server. Probeer het nog eens.</p></div>
								<div class="form_result" style="display: none;"><h2>Bedankt!</h2><p>Wij zullen indien nodig z.s.m. reageren.</p></div>
							</div>
	
							<h2>Contact</h2>

							<form action="#" class="standard flex-row flex-wrap">
								<?php // FIXIT SB: Values van deze 3 hidden inputs dynamisch vullen ?>
								<input type="hidden" name="object_plaatsnaam" value="<?php echo $val['objectDetails_Adres_Woonplaats']; ?>">
								<input type="hidden" name="object_adres" value="<?php echo obj_generateAddress($val['objectDetails_Adres_Straatnaam'], $val['objectDetails_Adres_Huisnummer'], $val['objectDetails_Adres_HuisnummerToevoeging']); ?>">
								<input type="hidden" name="object_url" value="<?php echo 'https://www.jackfrenken.nl/' . $template->getPermalink(1); ?>">
								
								<fieldset class="flex-col size50">
									<input type="text" name="name" value="" placeholder="Naam*">
									<input type="email" name="email" value="" placeholder="E-mailadres*">
									<input type="text" name="city" value="" placeholder="Woonplaats">
									<input type="text" name="phone" value="" placeholder="Telefoon*">
								</fieldset>

								<fieldset class="flex-col size50">
									<textarea name="message" placeholder="Uw bericht*"></textarea>
									<input type="submit" name="object-contact-submit" value="Verstuur dit bericht">
								</fieldset>
							</form>
						</div>
						
					</div>

				</div>
			</div>

			<?php include($documentRoot . "inc/aanbod-banner.php"); ?>

			<?php include($documentRoot . "inc/footer.php"); ?>

		</div>
		
		<?php include($documentRoot . "inc/footer-scripting.php"); ?>

		<link rel="stylesheet" type="text/css" href="<?php echo $dynamicRoot; ?>js/royalslider/royalslider/royalslider.css">
		<link rel="stylesheet" type="text/css" href="<?php echo $dynamicRoot; ?>js/royalslider/royalslider/skins/minimal-white/rs-minimal-white.css">

		<script type="text/javascript" src="<?php echo $dynamicRoot; ?>js/royalslider/royalslider/jquery.royalslider.min.js"></script>
		<script type="text/javascript" src="https://maps.google.com/maps/api/js?key=AIzaSyATO501GwK6eyvxPwA6TIdbmc_PcfKvPAg"></script>
		<script type="text/javascript" src="<?php echo $dynamicRoot; ?>js/google-maps.js"></script>
		<script type="text/javascript" src="<?php echo $dynamicRoot; ?>js/jquery.validate.js"></script>
		
		<script type="text/javascript">
		
			function changeTab(tab) {
	
				// Current active tab
				activeTab = $('li.active a[data-tab]');
	
				if ($('li a[data-tab="' + tab + '"]').length > 0) {
	
					newTab = $('li a[data-tab="' + tab + '"]');
	
					activeTab.html(activeTab.data('desc')).parent().removeClass('active');
					newTab.html(newTab.data('desc') + ' &xrarr;').parent().addClass('active');
	
					// Hide all tabs
					$('.content-wrapper[data-tab]').hide();
	
					// Show new tab
					$('.content-wrapper[data-tab="' + tab + '"]').show();
				}
			}
			
			$(document).ready(function($) {
	
				// START ROYALSLIDER
	
				var slideWidth = $('#royal-slider').width();
	
				slider = $("#royal-slider").royalSlider({
					
		            keyboardNavEnabled: true,
		            fullscreen: {
			            enabled: true,
						nativeFS: false
		            },
		            imageScaleMode: 'fill',
		            imageAlignCenter: false,
		            arrowsNavAutoHide: false,
		            controlNavigation: 'none',
		            autoHeight: true
		        }).data('royalSlider');
	
				slider.ev.on('rsEnterFullscreen', function() {
	
					console.log(slideWidth);
	
					slider.st.imageScaleMode='fit';
					slider.st.imageAlignCenter= true;
					slider.updateSliderSize(true);
				});
	
				slider.ev.on('rsExitFullscreen', function() {
	
					console.log(slideWidth);
	
					slider.st.imageScaleMode='fill';
					slider.st.imageAlignCenter= false;
					slider.updateSliderSize(true);
	
					$('.rsMainSlideImage').css({
	
						'height': 'auto',
						'width': slideWidth,
						'margin-left': '0px'
					});
					
					$('.rsOverflow').css({
	
						'height': 'auto',
						'width': slideWidth,
						'margin-left': '0px'
					});
				});
	
				// END ROYALSLIDER
	
				$('li a[data-tab="3"]').click(function(){
					load_map_and_street_view_from_address('<?php echo str_replace("'", "\'", obj_generateGoogleAddress($val['objectDetails_Adres_Woonplaats'], $val['objectDetails_Adres_Straatnaam'], $val['objectDetails_Adres_Huisnummer'], $val['objectDetails_Adres_HuisnummerToevoeging'], $val['objectDetails_Adres_Postcode'])); ?>');
				});
	
				// Form
				$("#object-contact-form form").validate({
					focusInvalid: false,
					errorPlacement: function(error, element) {},
					rules: {
						name: {
							required: true,
							minlength: 2
						},
						email: {
							required: true,
							email: true
						},
						phone: {
							required: true
						},
						message: {
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
	
				$('#object-contact-form-output .form_error').fadeOut('slow');
				$('#object-contact-form form').fadeOut('slow', function(){
				
					$('#object-contact-form-output .form_loading').css({ display : 'none' }).fadeIn('slow');
					$.ajax({
						type	: 'POST',
						url 	: '/inc/process-form-info.php',
						data	: $('#object-contact-form form').serialize(),
						success	: function(data){
							$('#object-contact-form-output .form_loading').fadeOut('fast', function(){
								if(!data){
									
									$('#object-contact-form-output .form_error.general').css({ display : 'none' }).fadeIn('fast');
									$('#object-contact-form form').fadeIn('fast');
								} else {
									
									$('#object-contact-form form').remove();
									$('#object-contact-form-output .form_result').css({ display : 'none' }).fadeIn();
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
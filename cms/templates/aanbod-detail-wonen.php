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

// See if object even exists
$objectData = $cms['database']->prepare("SELECT * FROM `tbl_OG_wonen` WHERE `id`=? AND (NOT `objectDetails_StatusBeschikbaarheid_Status` IN ('Ingetrokken'))", "i", array($moduleId));

if (count($objectData) == 0)
	Core::redirect($template->findPermalink(108, 1) . '.html');

$val = $objectData[0];

$priceText = obj_showPrice($val['objectDetails_Koop_Prijsvoorvoegsel'], $val['objectDetails_Koop_Koopprijs'], $val['objectDetails_Koop_KoopConditie'], $val['objectDetails_Huur_Huurprijs'], $val['objectDetails_Huur_HuurConditie']);

// Find other media
$extraMedia = array();

$findMedia = $cms['database']->prepare("SELECT * FROM `tbl_OG_media` WHERE `id_OG_wonen`=? AND `media_Groep` IN ('Brochure', 'Overig', 'Plattegrond') AND `media_status`=2 ORDER BY `media_Id` ASC", "i", array($val['id']));

if (count($findMedia) > 0) {

	// $extraMedia['plattegrond'] = array();

	foreach ($findMedia as $key => $sVal) {

		switch ($sVal['media_Groep']) {

			case 'Brochure':

				$extraMedia['brochure'] = $dynamicRoot . 'og_media/wonen_' . $val['object_NVMVestigingNR'] . '_' . $val['object_ObjectTiaraID']. '/' . $sVal['bestandsnaam'];

				break;

			default:

			// Plattegrond (Floorplanner)
			if ($sVal['media_Groep']=='Plattegrond') {

				// Check if its an image hosted by realworks
				if (strpos($sVal['media_URL'], 'realworks.nl')) {

					$extraMedia['plattegrond'][] = $dynamicRoot . 'og_media/wonen_' . $val['object_NVMVestigingNR'] . '_' . $val['object_ObjectTiaraID']. '/' . $sVal['bestandsnaam'];
				}
				else {

					$extraMedia['plattegrond'][] = $sVal['media_URL'];
				}
			}

			// Plattegrond (floorplanner)
// 			if (strpos($sVal['media_URL'], 'zien.floorplanner.com/') !== false) {

// 				$extraMedia['plattegrond'] = $sVal['media_URL'];
// 			}

			// Virtuele tour (Virtueletourzien.nl)
			if (strpos($sVal['media_URL'], 'dashboard.virtueletourzien.nl/') !== false) {

				$extraMedia['virtueleTour'] = $sVal['media_URL'];
			}

			// Video (mp4)
			// if (strpos($sVal['media_URL'], '.mp4?') !== false) {

			// 	$extraMedia['video'] = $sVal['media_URL'];
			// }

			// Vimeo
			if (strpos($sVal['media_URL'], 'vimeo') !== false) {

				// Grab the ID
				$id = str_replace('http://', '', $sVal['media_URL']);
				$id = str_replace('https://', '', $id);
				$id = str_replace('www.vimeo.com', '', $id);
				$id = str_replace('vimeo.com', '', $id);
				$id = trim($id, '/');
				$id = trim($id, '?');

				$extraMedia['vimeo'] = $id;
			}
			elseif (strpos($sVal['media_URL'], 'youtu') !== false) {

				$extraMedia['video'] = $sVal['media_URL'];
			}
		}
	}
}

// Fetch extra object details (if any)
$objectDetails = $cms['database']->prepare("SELECT * FROM `tbl_OG_objectDetails` WHERE `ood_table`=? AND `ood_ogId`=? LIMIT 1", "si", array('tbl_OG_wonen', $val['id']));

if (count($objectDetails) > 0) {

	$detailData = $objectDetails[0];
}
else {

	$detailData = null;
}

$fetchImage = $cms['database']->prepare("SELECT `bestandsnaam`, `bestandsnaam_tn`, `bestandsnaam_medium` FROM `tbl_OG_media` WHERE `id_OG_wonen`=? AND `media_status`=2 AND `media_groep` IN ('Hoofdfoto', 'Foto')  ORDER BY `media_groep` DESC, `media_id` ASC LIMIT 1", "i", array($val['id']));

if (count($fetchImage) > 0) {

	$headImage = $dynamicRoot . 'og_media/wonen_' . $val['object_NVMVestigingNR'] . '_' . $val['object_ObjectTiaraID']. '/' . $fetchImage[0]['bestandsnaam'];
	$injectImage = $headImage;
}
else {

	$headImage = $dynamicRoot . 'img/aanbod_geen-afbeelding_tn02.svg';
}

if ($val['objectDetails_Huur_Huurprijs'] > 0) {

	$injectTitle = 'Te huur: ' . ucwords($val['wonen_Woonhuis_SoortWoning']) . ' ' . $val['objectDetails_Adres_NL_Woonplaats'] . ', ' . obj_generateAddress($val['objectDetails_Adres_NL_Straatnaam'], $val['objectDetails_Adres_NL_Huisnummer'], $val['objectDetails_Adres_NL_HuisnummerToevoeging']) . ' | Schep Makelaardij - Landelijk Wonen';
}
else {
	
	$injectTitle = 'Te koop: ' . ucwords($val['wonen_Woonhuis_SoortWoning']) . ' ' . $val['objectDetails_Adres_NL_Woonplaats'] . ', ' . obj_generateAddress($val['objectDetails_Adres_NL_Straatnaam'], $val['objectDetails_Adres_NL_Huisnummer'], $val['objectDetails_Adres_NL_HuisnummerToevoeging']) . ' | Schep Makelaardij - Landelijk Wonen';
}

$injectDescription = $injectTitle;

$filter['provincie'] = (isset($_POST['provincie'])) ? $_POST['provincie'] : ((isset($_GET['provincie'])) ? $_GET['provincie'] : 0);
$filter['plaatsnaam'] = (isset($_POST['plaatsnaam'])) ? $_POST['plaatsnaam'] : ((isset($_GET['plaatsnaam'])) ? $_GET['plaatsnaam'] : '');
$filter['radius'] = (isset($_POST['radius'])) ? $_POST['radius'] : ((isset($_GET['radius'])) ? $_GET['radius'] : 0);
$filter['prijsVan'] = (isset($_POST['prijsVan'])) ? $_POST['prijsVan'] : ((isset($_GET['prijsVan'])) ? $_GET['prijsVan'] : '');
$filter['prijsTot'] = (isset($_POST['prijsTot'])) ? $_POST['prijsTot'] : ((isset($_GET['prijsTot'])) ? $_GET['prijsTot'] : '');
$filter['oppPerceel'] = (isset($_POST['oppPerceel'])) ? $_POST['oppPerceel'] : ((isset($_GET['oppPerceel'])) ? $_GET['oppPerceel'] : 0);
$filter['oppWoon'] = (isset($_POST['oppWoon'])) ? $_POST['oppWoon'] : ((isset($_GET['oppWoon'])) ? $_GET['oppWoon'] : 0);

// $filter['prijsOpAanvraag'] = (isset($_POST['prijsOpAanvraag'])) ? $_POST['prijsOpAanvraag'] : ((isset($_GET['prijsOpAanvraag'])) ? $_GET['prijsOpAanvraag'] : '');

$filter['bestemming'] = (isset($_POST['bestemming'])) ? $_POST['bestemming'] : ((isset($_GET['bestemming'])) ? $_GET['bestemming'] : ''); //str_f01
// $filter['bedrijfswoning'] = (isset($_POST['bedrijfswoning'])) ? $_POST['bedrijfswoning'] : ((isset($_GET['bedrijfswoning'])) ? $_GET['bedrijfswoning'] : array()); //str_f02
// $filter['hoofdfunctie'] = (isset($_POST['hoofdfunctie'])) ? $_POST['hoofdfunctie'] : ((isset($_GET['hoofdfunctie'])) ? $_GET['hoofdfunctie'] : array());
$filter['verkocht'] = (isset($_POST['verkocht'])) ? $_POST['verkocht'] : ((isset($_GET['verkocht'])) ? $_GET['verkocht'] : '');
$filter['video'] = (isset($_POST['video'])) ? $_POST['video'] : ((isset($_GET['video'])) ? $_GET['video'] : '');

$filter['openhuis'] = (isset($_POST['openhuis'])) ? $_POST['openhuis'] : ((isset($_GET['openhuis'])) ? $_GET['openhuis'] : '');

// $filter['slaapkamers'] = (isset($_POST['slaapkamers'])) ? $_POST['slaapkamers'] : ((isset($_GET['slaapkamers'])) ? $_GET['slaapkamers'] : ''); //str_f02
// $filter['perceelOppervlakte'] = (isset($_POST['perceelOppervlakte'])) ? $_POST['perceelOppervlakte'] : ((isset($_GET['perceelOppervlakte'])) ? $_GET['perceelOppervlakte'] : ''); //str_f03
// $filter['woonfunctieOppervlakte'] = (isset($_POST['woonfunctieOppervlakte'])) ? $_POST['woonfunctieOppervlakte'] : ((isset($_GET['woonfunctieOppervlakte'])) ? $_GET['woonfunctieOppervlakte'] : ''); //str_f04
// $filter['openhuisVanaf'] = (isset($_POST['openhuisVanaf'])) ? $_POST['openhuisVanaf'] : ((isset($_GET['openhuisVanaf'])) ? $_GET['openhuisVanaf'] : '');

// Extended search filter
if (!empty($filter['plaatsnaam']) || !empty($filter['radius']) || !empty($filter['prijsVan']) || !empty($filter['prijsTot'])/* || count($filter['bedrijfswoning']) > 0*/) {

	$classExtended = '';
}
else {

	$classExtended = 'closed';
}

$ogType = 'wonen';

if (!($url = $template->getBackUrl($template->findPermalink(33, 1)))) {
	
	$url = $template->findPermalink(33, 1);
}

if (isset($_GET['searchHash'])) {

	$returnUrl = $url. '.?searchHash=' . $_GET['searchHash'];
}
else {

	$returnUrl = $url;
}

$mediaList = $cms['database']->prepare("SELECT `id`, `object_ObjectTiaraID`, `bestandsnaam`, `bestandsnaam_tn`, `bestandsnaam_medium`, `media_MediaOmschrijving` FROM `tbl_OG_media` WHERE `id_OG_wonen`=? AND `media_status`=2 AND `media_Groep` IN ('HoofdFoto', 'Foto') ORDER BY `media_Groep` DESC, `media_Id` ASC", "i", array($val['id']));

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

			<?php include($documentRoot . "inc/aanbod-header-wonen.php"); ?>

			<div class="column-content">
				
				<div class="column-sidebar">
				
					<?php include($documentRoot . "inc/sidebar-nav.php"); ?>
				
					<?php include($documentRoot . "inc/widget.php"); ?>

					<a href="<?php echo $url; ?>" class="back-link">&xlarr; Terug naar overzicht</a>

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

			<?php include($documentRoot . "inc/aanbod-banner.php"); ?>

			<?php include($documentRoot . "inc/footer.php"); ?>

		</div>
		
		<?php include($documentRoot . "inc/footer-scripting.php"); ?>

		<link rel="stylesheet" type="text/css" href="<?php echo $dynamicRoot; ?>js/royalslider/royalslider/royalslider.css">
		<link rel="stylesheet" type="text/css" href="<?php echo $dynamicRoot; ?>js/royalslider/royalslider/skins/minimal-white/rs-minimal-white.css">

		<script type="text/javascript" src="<?php echo $dynamicRoot; ?>js/royalslider/royalslider/jquery.royalslider.min.js"></script>
		
		<script type="text/javascript">
		
			// Start royalslider
			$(document).ready(function($) {

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
				
// 				slider = $('#royal-slider').royalSlider({
// 					addActiveClass: true,
// 				  	autoHeight: true,
// 				    arrowsNav: true,
// 				    fadeinLoadedSlide: false,
// 					fullscreen: {
// 						enabled: true,
// 						nativeFS: false
// 					},
// 					buttonFS: true,
// 					nativeFS: true,
// 					slidesSpacing: 0,
// 				    controlNavigationSpacing: 0,
// 				    arrowsNavAutoHide: false,
// 				    controlNavigation: 'none',
// 				    imageScaleMode: 'fill',
// 				    imageAlignCenter: false,
// 				    loop: false,
// 				    loopRewind: true,
// 				    numImagesToPreload: 3,
// 				    keyboardNavEnabled: true,
// 				    usePreloader: true,
// 				    transitionType: 'fade',
// 				    transitionSpeed: 100
// 				}).data('royalSlider');

// 			  slider = $('#royal-slider').royalSlider({
// 					addActiveClass: true,
// 					arrowsNav: true,
// 					imageScaleMode: 'fill',
// 					controlNavigation: 'none',
// 					// autoScaleSlider: true, 
// 					// autoScaleSliderWidth: 960,
// 					// autoScaleSliderHeight: 300,
// 					fullscreen: {
// 						enabled: true,
// 						nativeFS: false
// 					},
// 					video: {
// 						autoHideControlNav: true
// 					},
// 					buttonFS: true,
// 					nativeFS: true,
// 					slidesSpacing: 0,
// 					loop: true,
// 					fadeinLoadedSlide: false,
// 					globalCaption: false,
// 					keyboardNavEnabled: true,
// 					globalCaptionInside: false,
// 					visibleNearby: {
// 						enabled: true,
// 						centerArea: 0.8,
// 						center: true,
// 						breakpoint: 650,
// 						breakpointCenterArea: 0.90,
// 						navigateByCenterClick: true
// 					}
// 				}).data('royalSlider');

// 				slider.ev.on('rsEnterFullscreen', function() {
					
// 					slider.st.imageScaleMode='fit';
// 					slider.updateSliderSize(true);
// 				});
				
// 				slider.ev.on('rsExitFullscreen', function() {

// 					slider.st.imageScaleMode='fill';
// 					slider.updateSliderSize(true);
// 				});
			});
		
			// EINDE ROYALSLIDER
			
		</script>
	</body>

</html>
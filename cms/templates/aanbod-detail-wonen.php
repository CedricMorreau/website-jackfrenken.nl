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
$objectData = $cms['database']->prepare("SELECT * FROM `tbl_OG_wonen` WHERE `id`=? AND (NOT `objectDetails_StatusBeschikbaarheid_Status` IN ('Ingetrokken', 'gearchiveerd'))", "i", array($moduleId));

if (count($objectData) == 0)
	Core::redirect($template->findPermalink(33, 1));

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

	$injectTitle = 'Te huur: ' . ucwords($val['wonen_Woonhuis_SoortWoning']) . ' ' . $val['objectDetails_Adres_NL_Woonplaats'] . ', ' . obj_generateAddress($val['objectDetails_Adres_NL_Straatnaam'], $val['objectDetails_Adres_NL_Huisnummer'], $val['objectDetails_Adres_NL_HuisnummerToevoeging']) . ' | Jack Frenken - Makelaars en Adviseurs';
}
else {
	
	$injectTitle = 'Te koop: ' . ucwords($val['wonen_Woonhuis_SoortWoning']) . ' ' . $val['objectDetails_Adres_NL_Woonplaats'] . ', ' . obj_generateAddress($val['objectDetails_Adres_NL_Straatnaam'], $val['objectDetails_Adres_NL_Huisnummer'], $val['objectDetails_Adres_NL_HuisnummerToevoeging']) . ' | Jack Frenken - Makelaars en Adviseurs';
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

$mediaList = $cms['database']->prepare("SELECT `id`, `object_ObjectTiaraID`, `bestandsnaam`, `bestandsnaam_tn`, `bestandsnaam_medium`, `media_MediaOmschrijving` FROM `tbl_OG_media` WHERE `id_OG_wonen`=? AND `media_status`=2 AND `media_Groep` IN ('HoofdFoto', 'Foto') ORDER BY `media_volgorde` ASC, `media_Id` ASC", "i", array($val['id']));

?>

<!doctype html>

<html lang="nl">
	<head>
		
		<?php include($documentRoot . "inc/head.php"); ?>
		
		<meta data-ogId="<?php echo $moduleId; ?>" data-feed="wonen" data-objectTiaraId="<?php echo $val['object_ObjectTiaraID']; ?>">

	</head>

	<body>
		<?php $val['objectDetails_Adres_NL_Postcode'] = preg_replace('/\s+/','',$val['objectDetails_Adres_NL_Postcode']); ?>
		<script>
		  kmhPixel = [{
		      'objectIdentifier': '<?php echo $val['object_ObjectTiaraID']; ?>',
		      'objectZipcode': '<?php echo $val['objectDetails_Adres_NL_Postcode']; ?>'
		  }];
		</script>

		<!-- KMH pixel --> 
		<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
		new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0], j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src= '//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f); })(window,document,'script','kmhPixel','GTM-PX4GN2');</script> 
		<!-- End KMH pixel -->

		<div class="page-wrapper aanbod-detail">

			<?php include($documentRoot . "inc/primary-nav.php"); ?>

			<?php include($documentRoot . "inc/aanbod-header-wonen.php"); ?>

			<div class="column-content">
				
				<div class="column-sidebar">
				
					<ul class="sidebar-nav">
						<li class="active"><a href="javascript:void(0);" data-tab="1" data-desc="Beschrijving" onclick="changeTab(1);">Beschrijving &xrarr;</a></li>
						<li><a href="javascript:void(0);" data-tab="2" data-desc="Kenmerken" onclick="changeTab(2);">Kenmerken</a></li>
						<li><a href="javascript:void(0);" data-tab="3" data-desc="Locatie" onclick="changeTab(3);">Locatie</a></li>
						<?php if (!empty($extraMedia['brochure'])) { ?>
						<li><a href="javascript:void(0);" data-tab="4" data-desc="Brochure" onclick="changeTab(4);">Brochure</a></li>
						<?php } ?>
						<li><a href="javascript:void(0);" data-tab="5" data-desc="Contact" onclick="changeTab(5);">Contact</a></li>
					</ul>
				
					<?php include($documentRoot . "inc/widget.php"); ?>

					<a href="<?php echo $url; ?>" class="back-link">&xlarr; Terug naar overzicht</a>

				</div>

				<div class="column-content">
					<a id="content" class="anchor"></a>

					<div class="content-wrapper" data-tab="1">
						<h2>Beschrijving</h2>

						<?php if ($val['crmLeverancier'] == 'realworks-api') { ?>
							<?php echo nl2br($val['objectDetails_Aanbiedingstekst']); ?>
						<?php } else { ?>
							<?php echo utf8_encode(nl2br(Core::fixEncoding($val['objectDetails_Aanbiedingstekst']))); ?>
						<?php } ?>
					</div>

					<div class="content-wrapper" data-tab="2" style="display: none;">
						<div class="table-flex-wrapper">			
							<div class="table-wrapper">
								<h2>Bebouwing</h2>
		
								<table>
									<?php if (!empty($val['bouwgrond_HuidigGebruik']) && $val['bouwgrond_Oppervlakte'] > 0) { ?>
									<tr>
										<th class="description">Bouwgrond</th>
										<td class="value"><?php echo number_format($val['bouwgrond_Oppervlakte'], 0, ",", "."); ?> m<sup>2</sup> perceeloppervlak</td>
									</tr>
									<?php } ?>
			
									<?php if (!empty($val['wonen_WonenDetails_Liggingen']) && !empty($val['wonen_WonenDetails_Liggingen'])) { ?>
									<tr>
										<th class="description">Ligging</th>
										<td class="value"><?php echo obj_splitValues($val['wonen_WonenDetails_Liggingen']); ?></td>
									</tr>
									<?php } ?>
			
									<?php if (!empty($val['wonen_WonenDetails_Bestemming_HuidigGebruik'])) { ?>
			
										<?php if (!empty($val['wonen_Woonhuis_SoortWoning'])) { ?>
										<tr>
											<th class="description">Soort woonhuis</th>
											<td class="value"><?php echo ucfirst($val['wonen_Woonhuis_SoortWoning']) . ',<br>' . $val['wonen_Woonhuis_TypeWoning']; ?></td>
										</tr>
										<?php } ?>
			
										<?php if (!empty($val['wonen_Appartement_SoortAppartement'])) { ?>
										<tr>
											<th class="description">Soort appartement</th>
											<td class="value"><?php echo $val['wonen_Appartement_SoortAppartement']; ?></td>
										</tr>
										<?php } ?>
			
										<?php if (!empty($val['wonen_WonenDetails_Bouwjaar_JaarOmschrijving_Jaar'])) { ?>
										<tr>
											<th class="description">Bouwjaar</th>
											<td class="value"><?php echo $val['wonen_WonenDetails_Bouwjaar_JaarOmschrijving_Jaar']; ?></td>
										</tr>
										<?php } ?>
			
										<?php if (!is_null($val['wonen_WonenDetails_GebruiksoppervlakteWoonfunctie']) && $val['wonen_WonenDetails_GebruiksoppervlakteWoonfunctie'] > 0) { ?>
										<tr>
											<th class="description">Woonoppervlakte</th>
											<td class="value"><?php echo number_format($val['wonen_WonenDetails_GebruiksoppervlakteWoonfunctie'], 0, ",", "."); ?> m<sup>2</sup></td>
										</tr>
										<?php } ?>
			
										<?php if (!is_null($val['wonen_WonenDetails_GebruiksoppervlakteOverigeFuncties']) && $val['wonen_WonenDetails_GebruiksoppervlakteOverigeFuncties'] > 0) { ?>
										<tr>
											<th class="description">Overige inpandige oppervlakte</th>
											<td class="value"><?php echo number_format($val['wonen_WonenDetails_GebruiksoppervlakteOverigeFuncties'], 0, ",", "."); ?> m<sup>2</sup></td>
										</tr>
										<?php } ?>
			
										<?php if (!is_null($val['wonen_WonenDetails_PerceelOppervlakte']) && $val['wonen_WonenDetails_PerceelOppervlakte'] > 0) { ?>
										<tr>
											<th class="description">Perceeloppervlakte</th>
											<td class="value"><?php echo number_format($val['wonen_WonenDetails_PerceelOppervlakte'], 0, ",", "."); ?> m<sup>2</sup></td>
										</tr>
										<?php } ?>
			
										<?php if (!is_null($val['wonen_WonenDetails_Inhoud']) && $val['wonen_WonenDetails_Inhoud'] > 0) { ?>
										<tr>
											<th class="description">Inhoud</th>
											<td class="value"><?php echo number_format($val['wonen_WonenDetails_Inhoud'], 0, ",", "."); ?> m<sup>3</sup></td>
										</tr>
										<?php } ?>
			
									<?php } ?>
								</table>
							</div>
						<?php
	
						$boolInfo = false;
	
						if (!empty($val['wonen_Verdiepingen_AantalSlaapKamers']) || !empty($val['wonen_Verdiepingen_Aantal']) || !empty($val['wonen_WonenDetails_VoorzieningenWonen']))
							$boolInfo = true;
	
						if ($boolInfo) {
	
						?>
						<div class="table-wrapper">			
							<h2>Indeling</h2>
		
							<table>
		
								<?php if (!empty($val['wonen_Verdiepingen_AantalSlaapKamers'])) { ?>
								<tr>
									<th class="description">Slaapkamers</th>
									<td class="value"><?php echo $val['wonen_Verdiepingen_AantalSlaapKamers']; ?></td>
								</tr>
								<?php } ?>
		
								<?php if (!empty($val['wonen_Verdiepingen_Aantal'])) { ?>
								<tr>
									<th class="description">Verdiepingen</th>
									<td class="value"><?php echo $val['wonen_Verdiepingen_Aantal']; ?></td>
								</tr>
								<?php } ?>
		
							</table>
						</div>
						<?php
	
						}
	
						?>
						
						<?php
	
						$boolInfo = false;
	
						if (!empty($val['wonen_WonenDetails_Installatie_CVKetel_CVKetelType']) || !empty($val['wonen_WonenDetails_Installatie_CVKetel_Eigendom']) || !empty($val['wonen_WonenDetails_Installatie_CVKetel_Combiketel']) || !empty($val['wonen_WonenDetails_Diversen_Isolatievormen']) || !empty($val['wonen_WonenDetails_Installatie_SoortenVerwarming']) || !empty($val['wonen_WonenDetails_Installatie_SoortenWarmWater']))
							$boolInfo = true;
	
						if ($boolInfo) {
	
						?>
						<div class="table-wrapper">						<h2>Energie</h2>
						
							<table>
		
								<?php if (!empty($val['wonen_WonenDetails_Installatie_CVKetel_CVKetelType'])) { ?>
								<tr>
									<th class="description">C.V.-ketel</th>
									<td class="value">
									
										<?php
										
										echo ucfirst($val['wonen_WonenDetails_Installatie_CVKetel_CVKetelType']);
										
										if (!empty($val['wonen_WonenDetails_Installatie_CVKetel_Eigendom']) && !empty($val['wonen_WonenDetails_Installatie_CVKetel_Bouwjaar']) && !empty($val['wonen_WonenDetails_Installatie_CVKetel_Combiketel'])) {
											
											if ($val['wonen_WonenDetails_Installatie_CVKetel_Combiketel'] == "ja")
												echo ', Combiketel';
											
											if ($val['wonen_WonenDetails_Installatie_CVKetel_Bouwjaar'] > 0)
												echo ', ' . $val['wonen_WonenDetails_Installatie_CVKetel_Bouwjaar'];
										}
										
										?>
										
									</td>
								</tr>
								<?php } ?>
								
								<?php if (!empty($val['wonen_WonenDetails_Diversen_Isolatievormen'])) { ?>
								<tr>
									<th class="description">Isolatie</th>
									<td class="value"><?php echo obj_splitValues($val['wonen_WonenDetails_Diversen_Isolatievormen']); ?></td>
								</tr>
								<?php } ?>
								
								<?php if (!empty($val['wonen_WonenDetails_Installatie_SoortenVerwarming'])) { ?>
								<tr>
									<th class="description">Verwarming</th>
									<td class="value"><?php echo obj_splitValues($val['wonen_WonenDetails_Installatie_SoortenVerwarming']); ?></td>
								</tr>
								<?php } ?>
								
								<?php if (!empty($val['wonen_WonenDetails_MatenEnLigging_Liggingen'])) { ?>
								<tr>
									<th class="description">Warm water</th>
									<td class="value"><?php echo obj_splitValues($val['wonen_WonenDetails_MatenEnLigging_Liggingen']); ?></td>
								</tr>
								<?php } ?>

								<?php if (!empty($val['objectDetails_Energielabel_Energieklasse'])) { ?>
								<tr>
									<th>Energielabel</th>
									<td class="value">

										<span class="energy-label energy-label-<?php echo strtolower($val['objectDetails_Energielabel_Energieklasse']); ?>">
											<?php echo $val['objectDetails_Energielabel_Energieklasse']; ?>

										</span>

										<!-- <a href="<?php //echo $dynamicRoot; ?>vastgoed/diensten/energielabel-woningen.html" target="_blank">
											Wat betekent dit?
										</a> -->
									
									</td>
								</tr>
								<?php } ?>

							</table>
						</div>

						<?php
	
						}
	
						?>
						
						<?php
	
						$boolInfo = false;
	
						if (!empty($val['wonen_WonenDetails_MatenEnLigging_Liggingen']) || !empty($val['wonen_WonenDetails_Tuin_Tuintypen']) || !empty($val['wonen_WonenDetails_Hoofdtuin_Afmetingen_Oppervlakte']) || !empty($val['wonen_WonenDetails_Hoofdtuin_Positie']) || !empty($val['wonen_WonenDetails_Garage_Soorten']) || !empty($val['wonen_WonenDetails_SchuurBerging_Soort']))
							$boolInfo = true;
	
						if ($boolInfo) {
	
						?>

						<div class="table-wrapper">
						
							<h2>Buitenruimte</h2>
							
							<table>
							
								<?php if (!empty($val['wonen_WonenDetails_MatenEnLigging_Liggingen'])) { ?>
								<tr>
									<th class="description">Ligging</th>
									<td class="value"><?php echo obj_splitValues($val['wonen_WonenDetails_MatenEnLigging_Liggingen']); ?></td>
								</tr>
								<?php } ?>
							
								<?php if (!empty($val['wonen_WonenDetails_Tuin_Tuintypen'])) { ?>
								<tr>
									<th class="description">Tuin</th>
									<td class="value"><?php echo obj_splitValues($val['wonen_WonenDetails_Tuin_Tuintypen']); ?></td>
								</tr>
								<?php } ?>
							
								<?php if (!empty($val['wonen_WonenDetails_Hoofdtuin_Afmetingen_Oppervlakte'])) { ?>
								<tr>
									<th class="description">Achtertuin</th>
									<td class="value"><?php echo $val['wonen_WonenDetails_Hoofdtuin_Afmetingen_Oppervlakte']; ?>m<sup>2</sup> (<?php echo number_format(($val['wonen_WonenDetails_Hoofdtuin_Afmetingen_Lengte'] / 100), 2, ',', '.'); ?>m diep en <?php echo number_format(($val['wonen_WonenDetails_Hoofdtuin_Afmetingen_Breedte'] / 100), 2, ',', '.'); ?>m breed)</td>
								</tr>
								<?php } ?>

								<?php if (!empty($val['wonen_WonenDetails_Hoofdtuin_Positie'])) { ?>
								<tr>
									<th class="description">Ligging tuin</th>
									<td class="value">
									
										<?php

										switch (strtolower($val['wonen_WonenDetails_Hoofdtuin_Positie'])) {
											
											case 'noord': echo 'Gelegen op het noorden'; break;
											case 'noordoost': echo 'Gelegen op het noordoosten'; break;
											case 'oost': echo 'Gelegen op het oosten'; break;
											case 'zuidoost': echo 'Gelegen op het zuidoosten'; break;
											case 'zuid': echo 'Gelegen op het zuiden'; break;
											case 'zuidwest': echo 'Gelegen op het zuidwesten'; break;
											case 'west': echo 'Gelegen op het westen'; break;
											case 'noordwest': echo 'Gelegen op het noordwesten'; break;
										}
										
										if ($val['wonen_WonenDetails_Hoofdtuin_Achterom'] != 'nee')
											echo ', bereikbaar via achterom';
										
										?>
									
									</td>
								</tr>
								<?php } ?>
								
								<?php if (!empty($val['wonen_WonenDetails_SchuurBerging_Soort'])) { ?>
								<tr>
									<th class="description">Ligging tuin</th>
									<td class="value">
									
										<?php
										
										switch ($val['wonen_WonenDetails_SchuurBerging_Soort']) {
											
											case 'aangebouwd steen': echo 'Aangebouwde stenen schuur/berging'; break;
											case 'aangebouwd hout': echo 'Aangebouwde houten schuur/berging'; break;
											case 'vrijstaand steen': echo 'Vrijstaande stenen schuur/berging'; break;
											case 'vrijstaand hout': echo 'Vrijstaande houten schuur/berging'; break;
											case 'inpandig': echo 'Inpandige schuur/berging'; break;
											case 'box': echo 'Box'; break;
										}
										
										if (!empty($val['wonen_WonenDetails_SchuurBerging_TotaalAantal']))
											echo '&nbsp(' . $val['wonen_WonenDetails_SchuurBerging_TotaalAantal'] . ')';
										
										?>
									
									</td>
								</tr>
								<?php } ?>
							
							</table>

						</div>
						
						<?php
	
						}
	
						?>				
					</div>	
					</div>
					

					<div class="content-wrapper" data-tab="3" style="display: none;">
						<h2>Locatie</h2>
						<div id="map_canvas"></div>
						<div id="pano"></div>
					</div>
					
					<?php if (!empty($extraMedia['brochure'])) { ?>

					<div class="content-wrapper" data-tab="4" style="display: none;">
						<h2>Brochure</h2>
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
								<input type="hidden" name="object_plaatsnaam" value="<?php echo $val['objectDetails_Adres_NL_Woonplaats']; ?>">
								<input type="hidden" name="object_adres" value="<?php echo obj_generateAddress($val['objectDetails_Adres_NL_Straatnaam'], $val['objectDetails_Adres_NL_Huisnummer'], $val['objectDetails_Adres_NL_HuisnummerToevoeging']); ?>">
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
		            controlNavigation: 'thumbnails',
		            fullscreen: {
			            enabled: true,
						nativeFS: false
		            },
		            imageScaleMode: 'fill-if-smaller',
		            imageAlignCenter: false,
		            arrowsNavAutoHide: false,
		            autoHeight: true,
		            loop: true,
		            thumbs: {
		                appendSpan: true,
		                firstMargin: true,
		                paddingBottom: 4
	              	}
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
					load_map_and_street_view_from_address('<?php echo str_replace("'", "\'", obj_generateGoogleAddress($val['objectDetails_Adres_NL_Woonplaats'], $val['objectDetails_Adres_NL_Straatnaam'], $val['objectDetails_Adres_NL_Huisnummer'], $val['objectDetails_Adres_NL_HuisnummerToevoeging'], $val['objectDetails_Adres_NL_Postcode'])); ?>');
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
									dataLayer.push({
										'event': 'contactformulier-submit'
									});	
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
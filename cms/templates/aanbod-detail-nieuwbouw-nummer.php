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
$objectData = $cms['database']->prepare("SELECT * FROM `tbl_OG_nieuwbouw_bouwNummers` WHERE `id`=? AND (NOT `Status_ObjectStatus` IN ('Ingetrokken', 'gearchiveerd'))", "i", array($moduleId));

if (count($objectData) == 0)
	Core::redirect($template->findPermalink(40, 1));
	
$val = $objectData[0];

$priceText = 'Prijs op aanvraag';

// Buy
if ($val['Financieel_Koop_Koopprijs'] > 1)
	$priceText = obj_generateCost($val['Financieel_Koop_Koopprijs']);
	
if (!empty($oVal['Financieel_Koop_KoopConditie']) && !is_null($val['Financieel_Koop_KoopConditie']) && $priceText != 'Prijs op aanvraag')
	$priceText .= ' ' . $val['Financieel_Koop_KoopConditie'];
	
// Rent
if ($val['Financieel_Huur_Huurprijs'] > 1)
	$priceText = obj_generateCost($oVal['Financieel_Huur_Huurprijs']);
	
if (!empty($oVal['Huur_HuurConditie']) && !is_null($val['Huur_HuurConditie']) && $oPriceText != 'Prijs op aanvraag')
	$priceText .= ' ' . $val['Huur_HuurConditie'];
	
// Find other media
$extraMedia = array();

$findMedia = $cms['database']->prepare("SELECT * FROM `tbl_OG_media` WHERE `id_OG_nieuwbouw_bouwnummers`=? AND `media_Groep` IN ('Brochure', 'Overig') AND `media_status`=2 ORDER BY `media_Id` ASC", "i", array($val['id']));

if (count($findMedia) > 0) {
	
	foreach ($findMedia as $key => $sVal) {
		
		switch ($sVal['media_Groep']) {
			
			case 'Brochure':
				
				$extraMedia['brochure'] = $dynamicRoot . 'og_media/nieuwbouw__' . $val['bouwNummer_NVMVestigingNR'] . '_' . $val['bouwNummer_ObjectTiaraID']. '/' . $sVal['bestandsnaam'];
				
				break;
				
			default:
				
				// Plattegrond (Floorplanner)
				if (strpos('http://pl.an/', $sVal['media_URL']) !== false) {
					
					$extraMedia['plattegrond'] = $sVal['media_URL'];
				}
				
				// Virtuele tour (Virtueletourzien.nl)
				if (strpos('http://bestellen.virtueletourzien.nl/', $sVal['media_URL']) !== false) {
					
					$extraMedia['virtueleTour'] = $sVal['media_URL'];
				}
				
				// Video (mp4)
				if (strpos('.mp4?', $sVal['media_URL']) !== false) {
					
					$extraMedia['video'] = $sVal['media_URL'];
				}
		}
	}
}

// Fetch extra object details (if any)
$objectDetails = $cms['database']->prepare("SELECT * FROM `tbl_OG_objectDetails` WHERE `ood_table`=? AND `ood_ogId`=? LIMIT 1", "si", array('id_OG_nieuwbouw_bouwnummers', $val['id']));

if (count($objectDetails) > 0) {
	
	$detailData = $objectDetails[0];
}
else {
	
	$detailData = null;
}

$fetchImage = $cms['database']->prepare("SELECT `bestandsnaam_tn`, `bestandsnaam_medium` FROM `tbl_OG_media` WHERE `id_OG_nieuwbouw_bouwnummers`=? AND `media_status`=2 AND `media_groep`='Hoofdfoto'", "i", array($val['id']));

if (count($fetchImage) > 0) {
	
	$headImage = $dynamicRoot . 'og_media/nieuwbouw__' . $val['bouwNummer_NVMVestigingNR'] . '_' . $val['bouwNummer_ObjectTiaraID']. '/' . $fetchImage[0]['bestandsnaam_medium'];
	$injectImage = $headImage;
}
else {
	
	$headImage = $dynamicRoot . '/img/aanbod_geen-afbeelding_tn01.svg';
}

$injectTitle = 'Te koop: ' . ucwords($val['Wonen_Woonhuis_SoortWoning']) . ' te ' . $val['Adres_Woonplaats'] . ', ' . obj_generateAddress($val['Adres_Straatnaam'], $val['Adres_Huisnummer'], $val['Adres_HuisnummerToevoeging']) . ' | Jack Frenken - Makelaars en Adviseurs';

if (isset($_GET['searchHash'])) {
	
	$searchHash = $_GET['searchHash'];
	
	if (isset($_SESSION['search'][$searchHash])) {
		
		foreach ($_SESSION['search'][$searchHash] as $sKey => $sVal) {
			
			$_GET[$sKey] = $sVal;
		}
	}
	
	unset($_SESSION['search'][$searchHash]);
}

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

// Fetch project
$objectData = $cms['database']->prepare("SELECT *, (SELECT COUNT(id) AS rsCount FROM tbl_OG_nieuwbouw_bouwTypes WHERE id_OG_nieuwbouw_projecten=tbl_OG_nieuwbouw_projecten.id) as aantal_bouwTypes, (SELECT COUNT(id) AS rsCount FROM tbl_OG_nieuwbouw_bouwNummers WHERE id_OG_nieuwbouw_projecten=tbl_OG_nieuwbouw_projecten.id) as aantal_bouwNummers FROM `tbl_OG_nieuwbouw_projecten` WHERE `id`=? AND (NOT `project_ProjectDetails_Status_ObjectStatus` IN ('Ingetrokken'))", "i", array($val['id_OG_nieuwbouw_projecten']));

// Fetch permalink
$permaLink = $cms['database']->prepare("SELECT `cms_per_link` FROM `tbl_cms_permaLinks` WHERE `cms_per_tableName`='tbl_mod_pages' AND `cms_per_tableId`=40 AND `cms_per_moduleId`=? AND `cms_per_moduleExtra` IS NULL", "i", array($objectData[0]['id']));

if (count($permaLink) > 0)
	$hrefProject = $dynamicRoot . $permaLink[0]['cms_per_link'];
else
	$hrefProject = $dynamicRoot . 'error/404';

$mediaList = $cms['database']->prepare("SELECT `id`, `object_ObjectTiaraID`, `bestandsnaam`, `bestandsnaam_tn`, `bestandsnaam_medium`, `media_MediaOmschrijving` FROM `tbl_OG_media` WHERE `id_OG_nieuwbouw_bouwnummers`=? AND `media_status`=2 AND `media_Groep` IN ('HoofdFoto', 'Foto') ORDER BY `media_Groep` DESC, `media_Id` ASC", "i", array($val['id']));
	
?>

<!doctype html>

<html lang="nl">
	<head>
		
		<?php include($documentRoot . "inc/head.php"); ?>
		
		<meta data-ogId="<?php echo $moduleId; ?>" data-feed="wonen" data-objectTiaraId="<?php echo $val['bouwNummer_ObjectTiaraID']; ?>">

	</head>

	<body>
		<div class="page-wrapper aanbod-detail">

			<?php include($documentRoot . "inc/primary-nav.php"); ?>

			<?php include($documentRoot . "inc/aanbod-header-nieuwbouw-nummer.php"); ?>

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

					<a href="<?php echo $hrefProject; ?>" class="back-link">&xlarr; Terug naar <?php echo $objectData[0]['project_ProjectDetails_Projectnaam']; ?></a>

				</div>

				<div class="column-content">
					<a id="content" class="anchor"></a>

					<div class="content-wrapper" data-tab="1">
						<h2>Beschrijving</h2>
						
						<?php echo utf8_encode(nl2br($val['Aanbiedingstekst'])); ?>
					</div>

					<div class="content-wrapper" data-tab="2" style="display: none;">
					
						<?php

							$boolInfo = false;
							$hasItems = 0;

							if (!empty($val['MatenEnLigging_Liggingen']) || !empty($val['Wonen_Woonhuis_SoortWoning']) || !empty($val['Wonen_Appartement_SoortAppartement']) || (!is_null($val['MatenEnLigging_GebruiksoppervlakteWoonfunctie']) && $val['MatenEnLigging_GebruiksoppervlakteWoonfunctie'] > 0) || (!is_null($val['MatenEnLigging_GebruiksoppervlakteOverigeFuncties']) && $val['MatenEnLigging_GebruiksoppervlakteOverigeFuncties'] > 0) || (!is_null($val['MatenEnLigging_PerceelOppervlakte']) && $val['MatenEnLigging_PerceelOppervlakte'] > 0) || (!is_null($val['MatenEnLigging_Inhoud']) && $val['MatenEnLigging_Inhoud'] > 0))
								$boolInfo = true;


							if ($boolInfo) {

								$hasItems++;
								?>
								
							<h2>Specificaties</h2>

							<table>

								<?php if (!empty($val['MatenEnLigging_Liggingen'])) { ?>
								<tr>
									<td class="description">Ligging</td>
									<td class="value"><?php echo obj_splitValues($val['MatenEnLigging_Liggingen']); ?></td>
								</tr>
								<?php } ?>

								<?php if (!empty($val['Wonen_Woonhuis_SoortWoning'])) { ?>
								<tr>
									<td class="description">Soort woonhuis</td>
									<td class="value"><?php echo ucfirst($val['Wonen_Woonhuis_SoortWoning']) . ', ' . $val['Wonen_Woonhuis_TypeWoning']; ?></td>
								</tr>
								<?php } ?>

								<?php if (!empty($val['Wonen_Appartement_SoortAppartement'])) { ?>
								<tr>
									<td class="description">Soort appartement</td>
									<td class="value"><?php echo $val['Wonen_Appartement_SoortAppartement']; ?></td>
								</tr>
								<?php } ?>

								<?php if (!is_null($val['MatenEnLigging_GebruiksoppervlakteWoonfunctie']) && $val['MatenEnLigging_GebruiksoppervlakteWoonfunctie'] > 0) { ?>
								<tr>
									<td class="description">Woonoppervlakte</td>
									<td class="value"><?php echo number_format($val['MatenEnLigging_GebruiksoppervlakteWoonfunctie'], 0, ",", "."); ?> m<sup>2</sup></td>
								</tr>
								<?php } ?>

								<?php if (!is_null($val['MatenEnLigging_GebruiksoppervlakteOverigeFuncties']) && $val['MatenEnLigging_GebruiksoppervlakteOverigeFuncties'] > 0) { ?>
								<tr>
									<td class="description">Oppervlakte bijgebouwen</td>
									<td class="value"><?php echo number_format($val['MatenEnLigging_GebruiksoppervlakteOverigeFuncties'], 0, ",", "."); ?> m<sup>2</sup></td>
								</tr>
								<?php } ?>

								<?php if (!is_null($val['MatenEnLigging_PerceelOppervlakte']) && $val['MatenEnLigging_PerceelOppervlakte'] > 0) { ?>
								<tr>
									<td class="description">Perceeloppervlakte</td>
									<td class="value"><?php echo number_format($val['MatenEnLigging_PerceelOppervlakte'], 0, ",", "."); ?> m<sup>2</sup></td>
								</tr>
								<?php } ?>

								<?php if (!is_null($val['MatenEnLigging_Inhoud']) && $val['MatenEnLigging_Inhoud'] > 0) { ?>
								<tr>
									<td class="description">Inhoud</td>
									<td class="value"><?php echo number_format($val['MatenEnLigging_Inhoud'], 0, ",", "."); ?> m<sup>3</sup></td>
								</tr>
								<?php } ?>
							</table>

								<?php

							}

							$boolInfo = false;

							if (!empty($val['Wonen_Verdiepingen_AantalSlaapKamers']) || !empty($val['Wonen_Verdiepingen_Aantal']))
								$boolInfo = true;

							if ($boolInfo) {

								$hasItems++;

								?>
								
							<h2>Indeling</h2>

							<table>

								<?php if (!empty($val['Wonen_Verdiepingen_AantalSlaapKamers'])) { ?>
								<tr>
									<td class="description">Aantal slaapkamers</td>
									<td class="value"><?php echo $val['Wonen_Verdiepingen_AantalSlaapKamers']; ?></td>
								</tr>
								<?php } ?>

								<?php if (!empty($val['Wonen_Verdiepingen_Aantal'])) { ?>
								<tr>
									<td class="description">Aantal verdiepingen</td>
									<td class="value"><?php echo $val['Wonen_Verdiepingen_Aantal']; ?></td>
								</tr>
								<?php } ?>

							</table>
								
								<?php
							}

							$boolInfo = false;

							if (!empty($val['Wonen_Details_Installatie_CVKetel_CVKetelType']) || !empty($val['Wonen_Details_Diversen_Isolatievormen']) || !empty($val['Wonen_Details_Installatie_SoortenVerwarming']) || !empty($val['Wonen_Details_Installatie_SoortenWarmWater']))
								$boolInfo = true;

							if ($boolInfo) {

								$hasItems++;

								?>
								
							<h2>Energie</h2>

							<table>

								<?php if (!empty($val['Wonen_Details_Installatie_CVKetel_CVKetelType'])) { ?>
								<tr>
									<td class="description">C.V.-ketel</td>
									<td class="value">
										
										<?php
										if (!empty($val['Wonen_Details_Installatie_CVKetel_CVKetelType']))
											echo ucfirst($val['Wonen_Details_Installatie_CVKetel_CVKetelType']);

										if (!empty($val['Wonen_Details_Installatie_CVKetel_Eigendom']) && !empty($val['Wonen_Details_Installatie_CVKetel_Bouwjaar']) && !empty($val['Wonen_Details_Installatie_CVKetel_Combiketel'])) {

											echo ' (' . ucfirst($val['Wonen_Details_Installatie_CVKetel_Eigendom']);

											if ($val['Wonen_Details_Installatie_CVKetel_Combiketel'] == 'ja')
												echo ', Combiketel';

											if ($val['Wonen_Details_Installatie_CVKetel_Bouwjaar'] > 0)
												echo ', ' . $val['Wonen_Details_Installatie_CVKetel_Bouwjaar'];

											echo ')';
												
										}
										?>

									</td>
								</tr>
								<?php } ?>

								<?php if (!empty($val['Wonen_Details_Diversen_Isolatievormen'])) { ?>
								<tr>
									<td class="description">Isolatie</td>
									<td class="value"><?php echo obj_splitValues($val['Wonen_Details_Diversen_Isolatievormen']); ?></td>
								</tr>
								<?php } ?>

								<?php if (!empty($val['Wonen_Details_Installatie_SoortenVerwarming'])) { ?>
								<tr>
									<td class="description">Verwarming</td>
									<td class="value"><?php echo obj_splitValues($val['Wonen_Details_Installatie_SoortenVerwarming']); ?></td>
								</tr>
								<?php } ?>

								<?php if (!empty($val['Wonen_Details_Installatie_SoortenWarmWater'])) { ?>
								<tr>
									<td class="description">Warm water</td>
									<td class="value"><?php echo obj_splitValues($val['Wonen_Details_Installatie_SoortenWarmWater']); ?></td>
								</tr>
								<?php } ?>

							</table>
								
								<?php
							}

							$boolInfo = false;

							if (!empty($val['MatenEnLigging_Liggingen']) || !empty($val['Wonen_Details_Tuin_Tuintypen']) || !empty($val['Wonen_Details_Tuin_Hoofdtuin_Afmetingen_Oppervlakte']) || !empty($val['Wonen_Details_Tuin_Hoofdtuin_Positie']) || !empty($val['Wonen_Details_Garage_Soorten']) || !empty($val['Wonen_Details_SchuurBerging_Soort']))
								$boolInfo = true;

							if ($boolInfo) {

								$hasItems++;

								?>
								
							<h2>Buitenruimte</h2>

							<table>

								<?php if (!empty($val['MatenEnLigging_Liggingen'])) { ?>
								<tr>
									<td class="description">Ligging</td>
									<td class="value"><?php echo obj_splitValues($val['MatenEnLigging_Liggingen']); ?></td>
								</tr>
								<?php } ?>

								<?php if (!empty($val['Wonen_Details_Tuin_Tuintypen'])) { ?>
								<tr>
									<td class="description">Tuin</td>
									<td class="value"><?php echo obj_splitValues($val['Wonen_Details_Tuin_Tuintypen']); ?></td>
								</tr>
								<?php } ?>

								<?php if (!empty($val['Wonen_Details_Tuin_Hoofdtuin_Afmetingen_Oppervlakte'])) { ?>
								<tr>
									<td class="description">Achtertuin</td>
									<td class="value"><?php echo number_format($val['Wonen_Details_Tuin_Hoofdtuin_Afmetingen_Oppervlakte'], 0, ",", "."); ?> m<sup>2</sup> (<?php echo number_format(($val['Wonen_Details_Tuin_Hoofdtuin_Afmetingen_Lengte'] / 100), 0, ",", "."); ?>m diep en <?php echo number_format(($val['Wonen_Details_Tuin_Hoofdtuin_Afmetingen_Breedte'] / 100), 0, ",", "."); ?>m breed)</td>
								</tr>
								<?php } ?>

								<?php if (!empty($val['Wonen_Details_Tuin_Hoofdtuin_Positie'])) { ?>
								<tr>
									<td class="description">Ligging tuin</td>
									<td class="value">Gelegen op het <?php echo $val['Wonen_Details_Tuin_Hoofdtuin_Positie']; ?><?php if ($val['Wonen_Details_Tuin_Hoofdtuin_Achterom'] == 'ja') echo ', bereikbaar via achterom'; ?></td>
								</tr>
								<?php } ?>

								<?php if (!empty($val['Wonen_Details_Garage_Soorten'])) { ?>
								<tr>
									<td class="description">Garage</td>
									<td class="value">

										<?php

										$explode = explode(',', $val['Wonen_Details_Garage_Soorten']);

										$garageSorts = array();

										foreach ($explode as $sKey => $sVal) {

											switch ($sVal) {

												case "[geen garage]": $garageSorts[] = "Geen garage"; break;
												case "[aangebouwd steen]": $garageSorts[] =  "Aangebouwde stenen garage"; break;
												case "[aangebouwd hout]": $garageSorts[] =  "Aangebouwde houten garage"; break;
												case "[vrijstaand steen]": $garageSorts[] =  "Vrijstaande stenen garage"; break;
												case "[vrijstaand hout]": $garageSorts[] =  "Brijstaande houten garage"; break;
												case "[inpandig]": $garageSorts[] =  "Inpandige garage"; break;
												case "[garagebox]": $garageSorts[] =  "Garagebox"; break;
												case "[parkeerkelder]": $garageSorts[] =  "Parkeerkelder"; break;
												case "[garage mogelijk]": $garageSorts[] =  "Garage mogelijk"; break;
												case "[carport]": $garageSorts[] =  "Carport"; break;
												case "[parkeerplaats]": $garageSorts[] =  "Parkeerplaats"; break;
												case "[souterrain]": $garageSorts[] =  "Souterrain"; break;
												case "[garage met carport]": $garageSorts[] =  "Garage met carport"; break;
											}
										}

										echo ucfirst(strtolower(implode(', ', $garageSorts)));

										?>

									</td>
								</tr>
								<?php } ?>

							</table>
								
								<?php
							}

							if ($hasItems == 0) {

								echo '<p>Er zijn geen kenmerken beschikbaar.</p>';
							}

							?>			
						
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
								<input type="hidden" name="object_plaatsnaam" value="<?php echo $val['Adres_Woonplaats']; ?>">
								<input type="hidden" name="object_adres" value="<?php echo obj_generateAddress($val['Adres_Straatnaam'], $val['Adres_Huisnummer'], $val['Adres_HuisnummerToevoeging']); ?>">
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
					load_map_and_street_view_from_address('<?php echo str_replace("'", "\'", $val['Adres_Woonplaats']) . ',+' . str_replace("'", "\'", $val['Adres_Postcode']) . ',+Nederland'; ?>');
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
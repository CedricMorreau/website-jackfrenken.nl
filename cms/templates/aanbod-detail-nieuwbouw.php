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

function obj_generateNumber($values) {
	if (isset($values['Adres_Huisnummer']) && intval($values['Adres_Huisnummer']) > 0) {

		$number = $values['Adres_Huisnummer'];

		//if (isset($values['Adres_HuisnummerToevoeging']))
		//	$number .= ' ' . $values['Adres_HuisnummerToevoeging'];

		return $number;
	}
	
	if (isset($values['bouwNummer_Nummer']) && intval($values['bouwNummer_Nummer']) > 0)
		return $values['bouwNummer_Nummer'];

	if (isset($values['bouwNummer_ObjectCode']))
		return $values['bouwNummer_ObjectCode'];

	return null;
}

$moduleId = $template->getModuleId();

// See if object even exists
$objectData = $cms['database']->prepare("SELECT *, (SELECT COUNT(id) AS rsCount FROM tbl_OG_nieuwbouw_bouwTypes WHERE id_OG_nieuwbouw_projecten=tbl_OG_nieuwbouw_projecten.id AND EXISTS(SELECT * FROM tbl_OG_nieuwbouw_bouwNummers WHERE id_OG_nieuwbouw_bouwTypes=tbl_OG_nieuwbouw_bouwTypes.id AND lower(`Status_ObjectStatus`) NOT IN ('ingetrokken', 'verkocht', 'verhuurd')) AND lower(`bouwType_BouwTypeDetails_Status_ObjectStatus`) NOT IN ('ingetrokken', 'verkocht', 'verhuurd')) as aantal_bouwTypes, (SELECT COUNT(id) AS rsCount FROM tbl_OG_nieuwbouw_bouwNummers WHERE id_OG_nieuwbouw_projecten=tbl_OG_nieuwbouw_projecten.id AND lower(`Status_ObjectStatus`) NOT IN ('ingetrokken', 'verkocht', 'verhuurd')) as aantal_bouwNummers FROM `tbl_OG_nieuwbouw_projecten` WHERE `id`=? AND (NOT LOWER(`project_ProjectDetails_Status_ObjectStatus`) IN ('ingetrokken', 'gearchiveerd', ''))", "i", array($moduleId));

if (count($objectData) == 0)
	Core::redirect($template->findPermalink(40, 1));

$val = $objectData[0];

// Handle price for nieuwbouw
if ($val['project_ProjectDetails_KoopAanneemsom_Van'] > 2 && $val['project_ProjectDetails_KoopAanneemsom_TotEnMet'] > 2 && $val['project_ProjectDetails_KoopAanneemsom_Van'] != $val['project_ProjectDetails_KoopAanneemsom_TotEnMet']) {

	$priceText = 'Te koop vanaf ' . obj_generateCost($val['project_ProjectDetails_KoopAanneemsom_Van']) . ' tot ' . obj_generateCost($val['project_ProjectDetails_KoopAanneemsom_TotEnMet']) . ' v.o.n.';
}
elseif ($val['project_ProjectDetails_KoopAanneemsom_Van'] > 2) {

	$priceText = 'Te koop ' . obj_generateCost($val['project_ProjectDetails_KoopAanneemsom_Van']) . ' v.o.n.';
}
elseif ($val['project_ProjectDetails_Huurprijs_Van'] > 2 && $val['project_ProjectDetails_Huurprijs_TotEnMet'] > 2) {

	$priceText = 'Te huur vanaf ' . obj_generateCost($val['project_ProjectDetails_Huurprijs_Van']) . ' tot ' . obj_generateCost($val['project_ProjectDetails_Huurprijs_TotEnMet']);
}
elseif ($val['project_ProjectDetails_Huurprijs_Van'] > 2) {

	$priceText = 'Te huur ' . obj_generateCost($val['project_ProjectDetails_Huurprijs_Van']);
}
else {

	$priceText = 'Prijs op aanvraag';
}

// Find other media
$extraMedia = array();

$findMedia = $cms['database']->prepare("SELECT * FROM `tbl_OG_media` WHERE `id_OG_nieuwbouw_projecten`=? AND `media_Groep` IN ('Brochure', 'Overig') AND `media_status`=2 ORDER BY `media_Id` ASC", "i", array($val['id']));

if (count($findMedia) > 0) {

	foreach ($findMedia as $key => $sVal) {

		switch ($sVal['media_Groep']) {

			case 'Brochure':

				$extraMedia['brochure'] = $dynamicRoot . 'og_media/nieuwbouw_' . $val['object_NVMVestigingNR'] . '_' . $val['object_ObjectTiaraID']. '/' . $sVal['bestandsnaam'];

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
$objectDetails = $cms['database']->prepare("SELECT * FROM `tbl_OG_objectDetails` WHERE `ood_table`=? AND `ood_ogId`=? LIMIT 1", "si", array('tbl_OG_nieuwbouw_projecten', $val['id']));

if (count($objectDetails) > 0) {

	$detailData = $objectDetails[0];
}
else {

	$detailData = null;
}

$fetchImage = $cms['database']->prepare("SELECT `bestandsnaam_tn`, `bestandsnaam_medium` FROM `tbl_OG_media` WHERE `id_OG_nieuwbouw_projecten`=? AND `media_status`=2 AND `media_groep`='Hoofdfoto'", "i", array($val['id']));

if (count($fetchImage) > 0) {

	$headImage = $dynamicRoot . 'og_media/nieuwbouw_' . $val['project_NVMVestigingNR'] . '_' . $val['project_ObjectTiaraID']. '/' . $fetchImage[0]['bestandsnaam_medium'];
	$injectImage = $headImage;
}
else {

	$headImage = $dynamicRoot . '/img/aanbod_geen-afbeelding_tn01.svg';
}

$injectTitle = utf8_encode($val['project_ProjectDetails_Projectnaam']) . ', ' . $val['project_ProjectDetails_Adres_Woonplaats'] . ' - ' . $priceText . ' | Jack Frenken - Makelaars en Adviseurs';

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

$mediaList = $cms['database']->prepare("SELECT `id`, `object_ObjectTiaraID`, `bestandsnaam`, `bestandsnaam_tn`, `bestandsnaam_medium`, `media_MediaOmschrijving` FROM `tbl_OG_media` WHERE `id_OG_nieuwbouw_projecten`=? AND `media_status`=2 AND `media_Groep` IN ('HoofdFoto', 'Foto') ORDER BY `media_Groep` DESC, `media_Id` ASC", "i", array($val['id']));
 
?>

<!doctype html>

<html lang="nl">
	<head>
		
		<?php include($documentRoot . "inc/head.php"); ?>
		
		<meta data-ogId="<?php echo $moduleId; ?>" data-feed="wonen" data-objectTiaraId="<?php echo $val['project_ObjectTiaraID']; ?>">

	</head>

	<body>
		<?php $val['project_ProjectDetails_Adres_Postcode'] = preg_replace('/\s+/','',$val['project_ProjectDetails_Adres_Postcode']); ?>
		<script>
		  kmhPixel = [{
		      'objectIdentifier': '<?php echo $val['project_ObjectTiaraID']; ?>',
		      'objectZipcode': '<?php echo $val['project_ProjectDetails_Adres_Postcode']; ?>'
		  }];
		</script>


		<!-- KMH pixel --> 
		<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
		new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0], j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src= '//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f); })(window,document,'script','kmhPixel','GTM-PX4GN2');</script> 
		<!-- End KMH pixel -->

		<div class="page-wrapper aanbod-detail">

			<?php include($documentRoot . "inc/primary-nav.php"); ?>

			<?php include($documentRoot . "inc/aanbod-header-nieuwbouw.php"); ?>

			<div class="column-content">
				
				<div class="column-sidebar">
				
					<ul class="sidebar-nav">
						<li class="active"><a href="javascript:void(0);" data-tab="1" data-desc="Woningtypen" onclick="changeTab(1);">Woningtypen &xrarr;</a></li>
						<li><a href="javascript:void(0);" data-tab="2" data-desc="Projectomschrijving" onclick="changeTab(2);">Projectomschrijving</a></li>
						<li><a href="javascript:void(0);" data-tab="3" data-desc="Locatie" onclick="changeTab(3);">Locatie</a></li>
						<li><a href="javascript:void(0);" data-tab="4" data-desc="Contact" onclick="changeTab(4);">Contact</a></li>
					</ul>
				
					<?php include($documentRoot . "inc/widget.php"); ?>
					
					<?php
					
					if (!($url = $template->getBackUrl($template->findPermalink(35, 1)))) {
						
						$url = $template->findPermalink(35, 1);
					}
					
					?>

					<a href="<?php echo $url; ?>" class="back-link">&xlarr; Terug naar overzicht</a>

				</div>

				<div class="column-content">

					<div class="column-content">
						<a id="content" class="anchor"></a>
	
						<div class="content-wrapper" data-tab="1">
							
							<div class="counter">
							
								Dit project bestaat uit <?php echo $val['aantal_bouwNummers'] . (($val['aantal_bouwNummers'] == 1) ? ' woning' : ' woningen'); ?> verdeeld over <?php echo $val['aantal_bouwTypes'] . (($val['aantal_bouwTypes'] > 0) ? ' typen' : ' type'); ?>.
	
							</div>
							
							<div class="overviewObjects">

								<?php
		
								// Fetch available types
								$sql = "SELECT
									*,
									(SELECT COUNT(id) AS rsCount FROM tbl_OG_nieuwbouw_bouwNummers WHERE id_OG_nieuwbouw_projecten=? AND id_OG_nieuwbouw_bouwTypes=tbl_OG_nieuwbouw_bouwTypes.id) as aantal_bouwNummers
								FROM tbl_OG_nieuwbouw_bouwTypes
								WHERE id_OG_nieuwbouw_projecten=?
								AND EXISTS(SELECT * FROM tbl_OG_nieuwbouw_bouwNummers WHERE id_OG_nieuwbouw_bouwTypes=tbl_OG_nieuwbouw_bouwTypes.id AND lower(`Status_ObjectStatus`) NOT IN ('ingetrokken', 'verkocht', 'verhuurd'))
								ORDER BY bouwType_BouwTypeDetails_Naam ASC ";
		
								$fetchTypes = $cms['database']->prepare($sql, "ii", array($val['id'], $val['id']));
		
								if (count($fetchTypes) > 0) {
		
									foreach ($fetchTypes as $tKey => $tVal) {
		
										// Handle price for nieuwbouw
										if ($tVal['bouwType_BouwTypeDetails_KoopAanneemsom_Van'] > 2 && $tVal['bouwType_BouwTypeDetails_KoopAanneemsom_TotEnMet'] > 2 && $tVal['bouwType_BouwTypeDetails_KoopAanneemsom_Van'] != $tVal['bouwType_BouwTypeDetails_KoopAanneemsom_TotEnMet']) {
		
											$tPriceText = 'Te koop vanaf ' . obj_generateCost($tVal['bouwType_BouwTypeDetails_KoopAanneemsom_Van']) . ' tot ' . obj_generateCost($tVal['bouwType_BouwTypeDetails_KoopAanneemsom_TotEnMet']);
										}
										elseif ($tVal['bouwType_BouwTypeDetails_KoopAanneemsom_Van'] > 2) {
		
											$tPriceText = 'Te koop ' . obj_generateCost($tVal['bouwType_BouwTypeDetails_KoopAanneemsom_Van']);
										}
										elseif ($tVal['bouwType_BouwTypeDetails_Huurprijs_Van'] > 2 && $tVal['bouwType_BouwTypeDetails_Huurprijs_TotEnMet'] > 2) {
		
											$tPriceText = 'Te huur vanaf ' . obj_generateCost($tVal['bouwType_BouwTypeDetails_Huurprijs_Van']) . ' tot ' . obj_generateCost($tVal['bouwType_BouwTypeDetails_Huurprijs_TotEnMet']);
										}
										elseif ($tVal['bouwType_BouwTypeDetails_Huurprijs_Van'] > 2) {
		
											$tPriceText = 'Te huur ' . obj_generateCost($tVal['bouwType_BouwTypeDetails_Huurprijs_Van']);
										}
										else {
		
											$tPriceText = 'Prijs op aanvraag';
										}
		
										?>
		
									<header class="headerAanbod group">
										<div class="floatLeft"><span class="city"><?php echo utf8_encode($tVal['bouwType_BouwTypeDetails_Naam']); ?></span></div>
										<?php
										if (strtolower($val['project_ProjectDetails_Status_ObjectStatus']) != 'verkocht') {
										?>
										<div class="floatRight">&nbsp;&rsaquo; <?php echo $tPriceText; ?></div>
										<?php } ?>
									</header>
		
										<?php
		
										// Fetch all nummers belonging to the type
										$sql = "SELECT
													*
												FROM tbl_OG_nieuwbouw_bouwNummers
												WHERE id_OG_nieuwbouw_projecten=?
													AND id_OG_nieuwbouw_bouwTypes=?
													AND lower(`Status_ObjectStatus`) NOT IN ('ingetrokken', 'verkocht', 'verhuurd')
													ORDER BY bouwNummer_Nummer ASC ";
		
										$fetchObjects = $cms['database']->prepare($sql, "ii", array($val['id'], $tVal['id']));

										if (count($fetchObjects) > 0) {
		
											// Fetch image of the main object
											$fetchImage = $cms['database']->prepare("SELECT id, object_ObjectTiaraID, bestandsnaam, bestandsnaam_tn, bestandsnaam_medium FROM tbl_OG_media WHERE id_OG_nieuwbouw_bouwtypes=? AND media_status=2 AND media_Groep IN ('HoofdFoto', 'Foto') ORDER BY media_Id ASC LIMIT 1", "i", array($tVal['id']));
		
											if (count($fetchImage) > 0) {
		
												$headImage = $dynamicRoot . 'og_media/bouwtypen_' . $val['project_NVMVestigingNR'] . '_' . $tVal['bouwType_ObjectTiaraID']. '/' . $fetchImage[0]['bestandsnaam_medium'];
												$injectImage = $headImage;
											}
											else {
		
												$headImage = $dynamicRoot . '/img/aanbod_geen-afbeelding_tn01.svg';
											}
		
											?>
		
									<div class="projectinfo object group" title="Stad - Projectnaam">
										<span class="image">
											<div>
												<img src="<?php echo $headImage; ?>" alt="<?php echo $val['project_ProjectDetails_Adres_Woonplaats']; ?>, <?php echo utf8_encode($val['project_ProjectDetails_Projectnaam']); ?> - <?php echo $tVal['bouwType_BouwTypeDetails_Naam']; ?>" title="<?php echo $val['project_ProjectDetails_Adres_Woonplaats']; ?>, <?php echo utf8_encode($val['project_ProjectDetails_Projectnaam']); ?> - <?php echo $tVal['bouwType_BouwTypeDetails_Naam']; ?>">
											</div>
										</span>
										<span class="info">
		
											<?php
		
											foreach($fetchObjects as $oKey => $oVal) {
												
												$verkocht = false;
												
												if (strtolower($oVal['Status_ObjectStatus']) == 'verkocht' || strtolower($oVal['Status_ObjectStatus']) == 'verhuurd') {
													
													$verkocht = true;
												}
		
		
												if ($oVal['Status_ObjectStatus'] == 'Verkocht' || $oVal['Status_ObjectStatus'] == 'Verkocht onder voorbehoud') {
		
													echo '<div class="label sold">' . $oVal['Status_ObjectStatus'] . '</div>';
												}
		
												$oPriceText = 'Prijs op aanvraag';
		
												// Buy
												if ($oVal['Financieel_Koop_Koopprijs'] > 1)
													$oPriceText = obj_generateCost($oVal['Financieel_Koop_Koopprijs']);
		
												if (!empty($oVal['Financieel_Koop_KoopConditie']) && !is_null($oVal['Financieel_Koop_KoopConditie']) && $oPriceText != 'Prijs op aanvraag')
													$oPriceText .= ' ' . $oVal['Financieel_Koop_KoopConditie'];
		
												// Rent
												if ($oVal['Financieel_Huur_Huurprijs'] > 1)
													$oPriceText = obj_generateCost($oVal['Financieel_Huur_Huurprijs']);
		
												if (!empty($oVal['Huur_HuurConditie']) && !is_null($oVal['Huur_HuurConditie']) && $oPriceText != 'Prijs op aanvraag')
													$oPriceText .= ' ' . $oVal['Huur_HuurConditie'];
		
												$href = $template->getPermalink(1, 1) . '/' . Core::replaceSpecialChars($oVal['Adres_Straatnaam'] . ' ' . $oVal['bouwNummer_ObjectCode'], 'permaLink') . '-' . $oVal['id'];
												
												if ($verkocht)
													$url = 'javascript:void(0);';
												else
													$url = $href;
		
														
											if ($verkocht) {
												?>
		
											<span class="objectInfo">
											
											<?php } else { ?>
											
											<a href="<?php echo $url; ?>" class="objectInfo" title="<?php echo $oVal['Adres_Straatnaam']; ?> - <?php echo $oVal['bouwNummer_ObjectCode']; ?>">
											
											<?php } ?>

												<strong><span class="city"><?php echo $oVal['Adres_Straatnaam']; ?>, <?php echo $oVal['Adres_Postcode']; ?> <?php echo $oVal['Adres_Woonplaats']; ?> - <?php echo obj_generateNumber($oVal); ?></span></strong>
												
												<ul class="specs">
													<li>Status: <?php echo $oVal['Status_ObjectStatus']; ?></li>
													<?php
													if (strtolower($val['project_ProjectDetails_Status_ObjectStatus']) != 'verkocht') {
													?>
													<li>Prijs: <?php echo $oPriceText; ?></li>
													<?php } ?>
													<?php
		
													if (!empty($oVal['Wonen_Woonhuis_SoortWoning']) && !is_null($oVal['Wonen_Woonhuis_SoortWoning'])) {
		
														echo '<li>' . ucfirst($oVal['Wonen_Woonhuis_SoortWoning']);
		
														if (!empty($oVal['Wonen_Woonhuis_TypeWoning']) && !is_null($oVal['Wonen_Woonhuis_TypeWoning']))
															echo ', ' . $oVal['Wonen_Woonhuis_TypeWoning'];
		
														echo '</li>';
													}
		
													if ($oVal['MatenEnLigging_PerceelOppervlakte'] > 0) {
		
														echo '<li>Perceeloppervlak: ' . number_format($oVal['MatenEnLigging_PerceelOppervlakte'], 0, ",", ".") . 'm<sup>2</sup></li>';
													}
		
													if ($oVal['MatenEnLigging_GebruiksoppervlakteWoonfunctie'] > 0) {
		
														echo '<li>Woonoppervlak: ' . number_format($oVal['MatenEnLigging_GebruiksoppervlakteWoonfunctie'], 0, ",", ".") . 'm<sup>2</sup></li>';
													}
		
													if ($oVal['MatenEnLigging_Inhoud'] > 0) {
		
														echo '<li>Inhoud: ' . number_format($oVal['MatenEnLigging_Inhoud'], 0, ",", ".") . 'm<sup>3</sup></li>';
													}
		
													?>
												</ul>
												
												<?php if (!$verkocht) { ?>
												<span class="read-more"><strong>Lees meer ⟶</strong></span>
												<?php } ?>
												

											<?php if (strtolower($oVal['Status_ObjectStatus']) == 'verkocht' || strtolower($oVal['Status_ObjectStatus']) == 'verhuurd') { ?>
											</span>
											<?php } else { ?>
											</a>
											<?php } ?>
		
												<?php
											}
		
											?>
		
										</span>
									</div>
		
											<?php
										}
		
										?>
		
										<?php
									}
								}
		
								?>
		
								</div>
							
						</div>
	
						<div class="content-wrapper" data-tab="2" style="display: none;">
							<h2>Beschrijving</h2>
							
							<?php 

							if (!empty($val['project_ProjectDetails_Presentatie_Website'])) {

								echo 'Projectwebsite: <a href="' . $val['project_ProjectDetails_Presentatie_Website'] . '" target="_blank">' . trim($val['project_ProjectDetails_Presentatie_Website']) . '</a><br>';
							}
							
							if ($val['crmLeverancier'] == 'realworks-api') {
								echo nl2br($val['project_ProjectDetails_Presentatie_Aanbiedingstekst']);
							} else {
								echo utf8_encode(nl2br(Core::fixEncoding($val['project_ProjectDetails_Presentatie_Aanbiedingstekst'])));
							}

							?>
						</div>						
	
						<div class="content-wrapper" data-tab="3" style="display: none;">
							<h2>Locatie</h2>
							<div id="map_canvas"></div>
							<div id="pano"></div>
						</div>
	
						<div class="content-wrapper" data-tab="4" style="display: none;">
							<div id="object-contact-form">
	
								<div id="object-contact-form-output" class="clearfix">
									<div class="form_loading group" style="display: none;">
										<p>
											<img src="/img/loading.gif" alt="Het reactieformulier wordt verstuurd" title="Het reactieformulier wordt verstuurd">
											<i>Het contactformulier wordt verstuurd&hellip;</i>
										</p>
									</div>
									<div class="form_error general" style="display: none;"><h3>Foutje</h3><p>Er ging iets mis op de server. Probeer het nog eens.</p></div>
									<div class="form_result" style="display: none;"><h3>Bedankt!</h3><p>Wij zullen indien nodig z.s.m. reageren.</p></div>
								</div>
		
								<h2>Contact</h2>
	
								<form action="#" class="standard flex-row flex-wrap">
									<?php // FIXIT SB: Values van deze 3 hidden inputs dynamisch vullen ?>
									<input type="hidden" name="object_plaatsnaam" value="<?php echo $val['objectDetails_Adres_NL_Woonplaats']; ?>">
									<input type="hidden" name="object_adres" value="<?php echo obj_generateAddress($val['objectDetails_Adres_NL_Straatnaam'], $val['objectDetails_Adres_NL_Huisnummer'], $val['objectDetails_Adres_NL_HuisnummerToevoeging']); ?>">
									<input type="hidden" name="object_url" value="<?php echo 'https://www.jackfrenken.nl/' . $template->getPermalink(1) . '.html'; ?>">
									
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
					load_map_and_street_view_from_address('<?php echo str_replace("'", "\'", $val['project_ProjectDetails_Adres_Woonplaats']) . ',+' . str_replace("'", "\'", $val['project_ProjectDetails_Adres_Postcode']) . ',+Nederland'; ?>');
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
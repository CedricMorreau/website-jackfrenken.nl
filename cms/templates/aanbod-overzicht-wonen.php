<?php

if (isset($_GET['searchHash'])) {

	$searchHash = $_GET['searchHash'];

	if (isset($_SESSION['search'][$searchHash])) {

		foreach ($_SESSION['search'][$searchHash] as $key => $val) {

			$_GET[$key] = $val;
		}
	}

	unset($_SESSION['search'][$searchHash]);
}

function translateDay($val, $type = 'default') {

	$en = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday", "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");

	if ($type == 'short') {

		$nl = array("ma", "di", "wo", "do", "vr", "za", "zo", "januari", "februari", "maart", "april", "mei", "juni", "juli", "augustus", "september", "oktober", "november", "december");
	}
	else {

		$nl = array("Maandag", "Dinsdag", "Woensdag", "Donderdag", "Vrijdag", "Zaterdag", "Zondag", "januari", "februari", "maart", "april", "mei", "juni", "juli", "augustus", "september", "oktober", "november", "december");
	}

	return str_replace($en, $nl, $val);
}

$overviewType = 'kopen';
$filter['saleType'] = 'kopen';

// Determine overview type
if ($template->getPageId() == 34) {
	
	$overviewType = 'huren';
	$filter['saleType'] = 'rent';
}
elseif ($template->getPageId()== 36) {
	
	$overviewType = 'kavels';
}

$filter['provincie'] = (isset($_POST['provincie'])) ? $_POST['provincie'] : ((isset($_GET['provincie'])) ? $_GET['provincie'] : 0);
$filter['straatnaam'] = (isset($_POST['straatnaam'])) ? $_POST['straatnaam'] : ((isset($_GET['straatnaam'])) ? $_GET['straatnaam'] : '');
$filter['plaatsnaam'] = (isset($_POST['plaatsnaam'])) ? $_POST['plaatsnaam'] : ((isset($_GET['plaatsnaam'])) ? $_GET['plaatsnaam'] : '');
$filter['radius'] = (isset($_POST['radius'])) ? $_POST['radius'] : ((isset($_GET['radius'])) ? $_GET['radius'] : '');
$filter['prijsVan'] = (isset($_POST['prijsVan'])) ? $_POST['prijsVan'] : ((isset($_GET['prijsVan'])) ? $_GET['prijsVan'] : 0);
$filter['prijsTot'] = (isset($_POST['prijsTot'])) ? $_POST['prijsTot'] : ((isset($_GET['prijsTot'])) ? $_GET['prijsTot'] : '');
$filter['extraFilter'] = (isset($_POST['extraFilter'])) ? $_POST['extraFilter'] : ((isset($_GET['extraFilter'])) ? $_GET['extraFilter'] : '');

$filter['p'] = (isset($_POST['p'])) ? $_POST['p'] : ((isset($_GET['p'])) ? $_GET['p'] : 1);

$filter['viewType'] = (isset($_POST['viewType'])) ? $_POST['viewType'] : ((isset($_GET['viewType'])) ? $_GET['viewType'] : 'list');

// $filter['prijsOpAanvraag'] = (isset($_POST['prijsOpAanvraag'])) ? $_POST['prijsOpAanvraag'] : ((isset($_GET['prijsOpAanvraag'])) ? $_GET['prijsOpAanvraag'] : '');

$filter['bestemming'] = (isset($_POST['bestemming'])) ? $_POST['bestemming'] : ((isset($_GET['bestemming'])) ? $_GET['bestemming'] : '');
$filter['bedrijfswoning'] = (isset($_POST['bedrijfswoning'])) ? $_POST['bedrijfswoning'] : ((isset($_GET['bedrijfswoning'])) ? $_GET['bedrijfswoning'] : array());
$filter['hoofdfunctie'] = (isset($_POST['hoofdfunctie'])) ? $_POST['hoofdfunctie'] : ((isset($_GET['hoofdfunctie'])) ? $_GET['hoofdfunctie'] : array());
$filter['verkocht'] = (isset($_POST['verkocht'])) ? $_POST['verkocht'] : ((isset($_GET['verkocht'])) ? $_GET['verkocht'] : '');
$filter['video'] = (isset($_POST['video'])) ? $_POST['video'] : ((isset($_GET['video'])) ? $_GET['video'] : '');

$filter['openhuis'] = (isset($_POST['openhuis'])) ? $_POST['openhuis'] : ((isset($_GET['openhuis'])) ? $_GET['openhuis'] : '');

$filter['perceelOppervlakte'] = (isset($_POST['perceelOppervlakte'])) ? $_POST['perceelOppervlakte'] : ((isset($_GET['perceelOppervlakte'])) ? $_GET['perceelOppervlakte'] : '');
$filter['woonfunctieOppervlakte'] = (isset($_POST['woonfunctieOppervlakte'])) ? $_POST['woonfunctieOppervlakte'] : ((isset($_GET['woonfunctieOppervlakte'])) ? $_GET['woonfunctieOppervlakte'] : '');
$filter['slaapkamers'] = (isset($_POST['slaapkamers'])) ? $_POST['slaapkamers'] : ((isset($_GET['slaapkamers'])) ? $_GET['slaapkamers'] : '');
// $filter['openhuisVanaf'] = (isset($_POST['openhuisVanaf'])) ? $_POST['openhuisVanaf'] : ((isset($_GET['openhuisVanaf'])) ? $_GET['openhuisVanaf'] : '');

$extraSearch = false;

if (!empty($filter['perceelOppervlakte']) || !empty($filter['woonfunctieOppervlakte']) || !empty($filter['slaapkamers']) || !empty($filter['bestemming'])) {
	
	$extraSearch = true;
}

if ($overviewType != 'kavels') {
	
	if ($template->getPageId() == 73)
		$filter['verkocht'] = 1;
	
	// Overrule the above filters indien removeFilter isset
	if (!empty($_GET['removeFilter'])) {
	
		Core::redirect($template->getPermalink(1, 1));
	}
	
	if (!empty($filter['verkocht']) && $filter['verkocht'] == 1) {
	
		$extraState = '';
	}
	else {
	
	 	$extraState = ", 'verkocht'";
	}
	
	// Very basic query
	$sql = "SELECT
			`tbl_OG_wonen`.id,
	        'WONEN' as `og_type`,
	
			`tbl_OG_wonen`.object_NVMVestigingNR,
			`tbl_OG_wonen`.object_ObjectTiaraID,
	
			`tbl_OG_wonen`.objectDetails_Adres_NL_Woonplaats,
			`tbl_OG_wonen`.objectDetails_Adres_NL_Straatnaam,
			`tbl_OG_wonen`.objectDetails_Adres_NL_Huisnummer,
			`tbl_OG_wonen`.objectDetails_Adres_NL_HuisnummerToevoeging,
			`tbl_OG_wonen`.objectDetails_Koop_Koopprijs,
			`tbl_OG_wonen`.objectDetails_Koop_Prijsvoorvoegsel,
			`tbl_OG_wonen`.objectDetails_Koop_KoopConditie,
			`tbl_OG_wonen`.objectDetails_Huur_Huurprijs,
			`tbl_OG_wonen`.objectDetails_Huur_HuurConditie,
			`tbl_OG_wonen`.wonen_Woonhuis_SoortWoning,
			`tbl_OG_wonen`.wonen_Woonhuis_TypeWoning,
			`tbl_OG_wonen`.object_Web_OpenHuis_Vanaf,
			`tbl_OG_wonen`.object_Web_OpenHuis_Tot,
	        `tbl_OG_wonen`.bouwgrond_HuidigeBestemming,
			`tbl_OG_wonen`.bouwgrond_Oppervlakte,
	
			`tbl_OG_wonen`.objectDetails_StatusBeschikbaarheid_Status,
	
			`tbl_OG_wonen`.objectDetails_StatusBeschikbaarheid_TransactieDatum,
	
			`tbl_OG_wonen`.google_lat,
			`tbl_OG_wonen`.google_lng,
			`tbl_OG_wonen`.google_status,
	
			`wonen_Verdiepingen_AantalKamers`,
			`wonen_WonenDetails_GebruiksoppervlakteWoonfunctie`,
			`wonen_WonenDetails_PerceelOppervlakte`,
			`wonen_WonenDetails_Inhoud`,
			`wonen_Appartement_SoortAppartement`,
			`object_Web_Prioriteit`,
	
			`tbl_OG_provincies`.`provincie`,
			LOWER(`tbl_OG_provincies`.`provincie`) AS `lcase_provincie`,
			`tbl_OG_objectDetails`.*,
	        `tbl_OG_wonen`.id_provincies,
	        `tbl_OG_wonen`.wonen_Appartement_KenmerkAppartement,
	        `tbl_OG_wonen`.datum_toegevoegd,
	        `tbl_OG_wonen`.objectDetails_Adres_NL_Postcode,
	        '' as id_OG_nieuwbouw_projecten,
	        (SELECT `cms_per_link` FROM `tbl_cms_permaLinks` WHERE `cms_per_tableId`=38 AND `cms_per_tableName`='tbl_mod_pages' AND `cms_per_moduleId`=`tbl_OG_wonen`.`id`) as `cms_per_link`,
			(SELECT `bestandsnaam_medium` FROM `tbl_OG_media` WHERE `id_OG_wonen`=`tbl_OG_wonen`.`id` AND `media_status`=2 AND `media_groep` IN ('Hoofdfoto', 'Foto')  ORDER BY `media_groep` DESC, `media_id` ASC LIMIT 1) as `mainImage`
	
		FROM `tbl_OG_wonen`
			LEFT JOIN `tbl_OG_objectDetails` ON `ood_ogId`=`id` AND `ood_table`='tbl_OG_wonen'
			INNER JOIN `tbl_OG_provincies` ON `tbl_OG_provincies`.`id`=`tbl_OG_wonen`.`id_provincies`
	
		WHERE
			(NOT `tbl_OG_wonen`.`objectDetails_StatusBeschikbaarheid_Status` IN ('Ingetrokken', 'ingetrokken'" . $extraState . "))
			AND (`ood_onlineStatus` IS NULL or `ood_onlineStatus`=1)
			AND `tbl_OG_wonen`.`id`<>0";
	
	// Extended search filter
	// if (!empty($filter['plaatsnaam']) || !empty($filter['radius']) || !empty($filter['prijsVan']) || !empty($filter['prijsTot']) || count($filter['bedrijfswoning']) > 0) {
	
	// 	$classExtended = '';
	// }
	// else {
	
		$classExtended = 'closed';
	// }
	
	$filter['orderBy'] = (isset($_POST['orderBy'])) ? $_POST['orderBy'] : ((isset($_GET['orderBy'])) ? $_GET['orderBy'] : 'datum,desc');
	
	$splitOrder = explode(',', $filter['orderBy']);
	
	$data['orderByReal'] = $splitOrder[0];
	$filter['sortBy'] = $splitOrder[1];
	
	// Correct weird sortBy
	if (!in_array($filter['sortBy'], array('asc', 'desc')))
		$filter['sortBy'] = 'desc';
	
	// Pronvincie filter
	if (!empty($filter['provincie']) && $filter['provincie'] > 0) {
	
		$sql .= " AND `id_provincies`=" . $cms['database']->escape($filter['provincie']);
	}
	
	if (!empty($filter['verkocht']) && $filter['verkocht'] == 1) {
	
		$sql .= " AND `objectDetails_StatusBeschikbaarheid_Status` IN ('Verkocht', 'verkocht', 'gearchiveerd') ";
	}
	
	if (!empty($filter['video']) && $filter['video'] == 1) {
	
		$sql .= " AND `tbl_OG_objectDetails`.`ood_helicopterVideo` IS NOT NULL AND `tbl_OG_objectDetails`.`ood_helicopterVideo`!='' ";
	}
	
	// Handle extra filters
	if (!empty($filter['extraFilter'])) {
	
		switch ($filter['extraFilter']) {
	
			case 'paardenstal':
	
				$sql .= " AND `objectDetails_Aanbiedingstekst` LIKE '%paardenstal%'";
	
				break;
	
			case 'aanwater':
	
				$sql .= " AND `wonen_WonenDetails_Liggingen` LIKE '%aan water%'";
	
				break;
	
			case 'drone':
	
				$sql .= " AND EXISTS(SELECT * FROM `tbl_OG_media` WHERE `media_URL` LIKE '%vimeo%' AND `id_OG_wonen`=`tbl_OG_wonen`.`id`)";
	
				break;
	
			case 'monument':
	
				$sql .= " AND `wonen_WonenDetails_Diversen_Bijzonderheden` LIKE '%monumentaal pand%'";
	
				break;
	
			case 'werken':
	
				$sql .= " AND `objectDetails_Aanbiedingstekst` LIKE '%kantoorruimte%'";
	
				break;
		}
	}
	
	// Radius
	if (!empty($filter['plaatsnaam']) && !empty($filter['radius'])) {
	
		$filter['landcode'] = 'NL';
	
		include($documentRoot . 'inc/google_searchRadius.php');
	
		if (isset($boundingBox)) {
	
			$sql .= " AND (" .
						"`google_status`=1 " .
						"AND `google_lat` BETWEEN " . $cms['database']->escape($boundingBox['lat1']) . " AND " . $cms['database']->escape($boundingBox['lat2']) . " " .
						"AND `google_lng` BETWEEN " . $cms['database']->escape($boundingBox['lon1']) . " AND " . $cms['database']->escape($boundingBox['lon2']) . " " .
					")";
		}
	}
	
	// Plaatsnaam / postcode
	if (!empty($filter['plaatsnaam'])) {
	
		if (empty($filter['radius'])) {
	
			$filter['saleType'] = 'kopen';
	
			// Construct next query
			$sql .= " AND (";
	
			// Postcode
			$sql .= "`objectDetails_Adres_NL_Postcode` LIKE '%" . $cms['database']->escape($filter['plaatsnaam']) . "%' ";
	
			// Straat
			$sql .= "OR lower(`objectDetails_Adres_NL_Straatnaam`) LIKE '%" . $cms['database']->escape(strtolower($filter['plaatsnaam'])) . "%' ";
	
			// Plaatsnaam
			$sql .= "OR lower(`objectDetails_Adres_NL_Woonplaats`) LIKE '%" . $cms['database']->escape(strtolower($filter['plaatsnaam'])) . "%' ";
	
			// Close
			$sql .= ')';
		}
	}
	
	// Plaatsnaam / postcode
	if (!empty($filter['straatnaam'])) {
	
		// Construct next query
		$sql .= " AND (";
	
		// Straat
		$sql .= "lower(`objectDetails_Adres_NL_Straatnaam`) LIKE '%" . $cms['database']->escape(strtolower($filter['straatnaam'])) . "%' ";
	
		// Close
		$sql .= ')';
	}
	
	if (!empty($filter['saleType'])) {
	
		if ($filter['saleType'] == 'rent') {
	
			$sql .= " AND `objectDetails_Huur_Huurprijs`>0 ";
		}
		elseif ($filter['saleType'] == 'both') {
	
			$sql .= " AND (`objectDetails_Koop_Koopprijs`>0 OR (`objectDetails_Koop_Koopprijs`=0 AND `objectDetails_Huur_Huurprijs`=0))";
		}
		else {
	
			$sql .= " AND (`objectDetails_Koop_Koopprijs`>=0 OR `objectDetails_Huur_Huurprijs`>=0 OR `objectDetails_Koop_Koopprijs` IS NULL OR `objectDetails_Huur_Huurprijs` IS NULL) ";	
		}
	}
	
	if ($overviewType == 'kavels') {
		
		$sql .= " AND bouwgrond_HuidigeBestemming!=''";
	}
	
	// Prijs van
	if (!empty($filter['prijsVan']) && $filter['prijsVan'] > 0) {
	
		if ($filter['saleType'] == 'rent')
			$sql .= " AND `objectDetails_Huur_Huurprijs`>=" . $cms['database']->escape($filter['prijsVan']) . " ";
		else
			$sql .= " AND `objectDetails_Koop_Koopprijs`>=" . $cms['database']->escape($filter['prijsVan']) . " ";
	}
	
	// Perceeloppervlakte
	if (!empty($filter['perceelOppervlakte']) && $filter['perceelOppervlakte'] > 0) {
	
		$sql .= " AND `wonen_WonenDetails_PerceelOppervlakte`>=" . $cms['database']->escape($filter['perceelOppervlakte']) . " ";
	}
	
	// Woonoppervlakte
	if (!empty($filter['woonfunctieOppervlakte']) && $filter['woonfunctieOppervlakte'] > 0) {
	
		$sql .= " AND `wonen_WonenDetails_GebruiksoppervlakteWoonfunctie`>=" . $cms['database']->escape($filter['woonfunctieOppervlakte']) . " ";
	}
	
	// Slaapkamers
	if (!empty($filter['slaapkamers']) && $filter['slaapkamers'] > 0) {
	
		$sql .= " AND `wonen_Verdiepingen_AantalSlaapKamers`>=" . $cms['database']->escape($filter['slaapkamers']) . " ";
	}
	
	// Prijs tot
	if (!empty($filter['prijsTot']) && $filter['prijsTot'] > 0) {
	
		if ($filter['prijsTot'] > $filter['prijsVan']) {
	
			if ($filter['saleType'] == 'rent')
				$sql .= " AND `objectDetails_Huur_Huurprijs`<=" . $cms['database']->escape($filter['prijsTot']) . " ";
			else
				$sql .= " AND `objectDetails_Koop_Koopprijs`<=" . $cms['database']->escape($filter['prijsTot']) . " ";
		}
	}
	
	// // Temporary fix for openHuis page
	// if ($template->getPageId() == 87) {
	
	// 	$selectDate = $cms['database']->prepare("SELECT `object_Web_OpenHuis_Vanaf` FROM `tbl_OG_wonen` ORDER BY `object_Web_OpenHuis_Vanaf` DESC");
	
	// 	if (count($selectDate) > 0) {
	
	// 		$dateTime = new PP_DateTime($selectDate[0]['object_Web_OpenHuis_Vanaf']);
	
	// 		$filter['openhuis'] = $dateTime->format('d-m-Y');
	// 	}
	// }
	// End temporary fix for openHuis page
	
	if (!empty($filter['openhuis'])) {
	
		// Format date into readable format.
		$explodeDate = explode('-', $filter['openhuis']);
	
		$readableDate = $explodeDate[2] . '-' . $explodeDate[1] . '-' . $explodeDate[0];
		$firstDate = $readableDate . ' 00:00:00';
		$lastDate = $readableDate . ' 23:59:59';
	
		$realOpenDate = $firstDate;
	
		$sql .= " AND `object_Web_OpenHuis_Vanaf`>='" . $firstDate . "' AND `object_Web_OpenHuis_Vanaf`<='" . $lastDate . "'";
	}
	
	// Type object
	if (!empty($filter['bestemming'])) {
	
		if ($filter['bestemming'] == 'bouwgrond') {
	
			$sql .= " AND `bouwgrond_HuidigeBestemming`='bouwgrond' ";
		}
		elseif ($filter['bestemming'] == 'appartement') {
	
			$sql .= " AND `wonen_Appartement_KenmerkAppartement`='appartement' ";
		}
		else {
	
			$sql .= " AND `wonen_Woonhuis_SoortWoning` LIKE '%" . $cms['database']->escape($filter['bestemming']) . "%' ";
		}
	}
	
	// Hoofdfunctie
	if (!empty($filter['hoofdfunctie'])) {
	
		if (!is_array($filter['hoofdfunctie'])) {
			$arrData = array();
			$arrData[] = $filter['hoofdfunctie'];
		}
	
		if (count($arrData) > 0) {
	
			if ($arrData[0] == 'appartement') {
	
				$sql .= " AND `wonen_Appartement_KenmerkAppartement`='appartement' ";
			}
			else {
	
				$hoofdfuncties = array();
	
				foreach ($arrData as $key => $val) {
	
					$hoofdfuncties[] = "'" . $val . "'";
				}
	
				$implodeFuncties = implode(',', $hoofdfuncties);
	
				$sql .= " AND `wonen_Woonhuis_SoortWoning` IN (" . $implodeFuncties . ") ";
			}
		}
	}
	
	// KOOPWONINGEN
	if ($template->getPageId() == 33) {
	
		$sql .= "";
	}
	
	// HUURWONINGEN
	if ($template->getPageId() == 34) {
	
		$sql .= "";
	}
	
	// BOUWKAVELS
	if ($template->getPageId() == 36) {
	
		$sql .= "";
	}
	
	
	// Bedrijfswoning ja/nee
	// if (!empty($filter['bedrijfswoning'])) {
	
	// 	if (in_array('JA', $filter['bedrijfswoning']) || count($filter['bedrijfswoning'] == 0)) {
	
	// 		$sql .= " AND `tbl_OG_wonen`.`AenLV_Bedrijfswoningen` IS NOT NULL ";
	// 	}
	// 	else {
	
	// 		$sql .= " AND `tbl_OG_wonen`.`AenLV_Bedrijfswoningen` IS NULL ";
	// 	}
	// }
	
	// Handle sorting
	$tempSql = '';
	switch ($data['orderByReal']) {
	
		case 'prijs':
	
			$tempSql .= " `objectDetails_Koop_Koopprijs` " . $filter['sortBy'] . ", `objectDetails_Adres_NL_Woonplaats` ASC, `id` ASC ";
	
			break;
		case 'plaatsnaam':
	
			$tempSql .= " `objectDetails_Adres_NL_Woonplaats` " . $filter['sortBy'] . ", `id` ASC ";
	
			break;
		case 'datum':
	
			$tempSql .= " `datum_toegevoegd` " . $filter['sortBy'] . ", `id` ASC ";
	
			break;
	
		default:
	
			$tempSql .= " `datum_toegevoegd` DESC, `tbl_OG_wonen`.`id` ASC ";
	
	}
	
	if (!empty($tempSql)) {
	
		$sql .= " ORDER BY" . $tempSql;
	}
}
else {
	
	$sql = "
		(SELECT 
			`tbl_OG_wonen`.id AS id, 
			0 AS id_OG_nieuwbouw_projecten, 
			0 AS id_OG_nieuwbouw_bouwTypes, 
		
			`tbl_OG_wonen`.datum_toegevoegd, 
			`tbl_OG_wonen`.object_NVMVestigingNR AS NVMVestigingNR, 
			`tbl_OG_wonen`.object_ObjectTiaraID AS ObjectTiaraID, 
		
			`tbl_OG_wonen`.objectDetails_StatusBeschikbaarheid_Status AS status, 
		
			`tbl_OG_wonen`.objectDetails_Adres_NL_Straatnaam AS straatnaam, 
			`tbl_OG_wonen`.objectDetails_Adres_NL_Huisnummer AS huisnummer, 
			`tbl_OG_wonen`.objectDetails_Adres_NL_HuisnummerToevoeging AS huisnummerToevoeging, 
			`tbl_OG_wonen`.objectDetails_Adres_NL_Woonplaats AS woonplaats, 
		
			`tbl_OG_wonen`.objectDetails_Koop_Prijsvoorvoegsel as koopprijsVoorvoegsel,
			`tbl_OG_wonen`.objectDetails_Koop_Koopprijs AS koopprijs, 
			`tbl_OG_wonen`.objectDetails_Koop_KoopConditie AS koopconditie, 
			`tbl_OG_wonen`.objectDetails_Huur_Huurprijs AS huurprijs, 
			`tbl_OG_wonen`.objectDetails_Huur_HuurConditie AS huurconditie, 
		
			`tbl_OG_wonen`.bouwgrond_HuidigGebruik AS huidigGebruik, 
			`tbl_OG_wonen`.bouwgrond_Oppervlakte AS oppervlakte, 
			`tbl_OG_wonen`.bouwgrond_Liggingen AS liggingen, 
			'wonen' as ogType,

			(SELECT `cms_per_link` FROM `tbl_cms_permaLinks` WHERE `cms_per_tableId`=38 AND `cms_per_tableName`='tbl_mod_pages' AND `cms_per_moduleId`=`tbl_OG_wonen`.`id`) as `cms_per_link`,
			(SELECT `bestandsnaam_medium` FROM `tbl_OG_media` WHERE `id_OG_wonen`=`tbl_OG_wonen`.`id` AND `media_status`=2 AND `media_groep` IN ('Hoofdfoto', 'Foto')  ORDER BY `media_groep` DESC, `media_id` ASC LIMIT 1) as `mainImage`,
		
			'-' AS projectnaam 
		
			FROM `tbl_OG_wonen` 
				WHERE (NOT `tbl_OG_wonen`.objectDetails_StatusBeschikbaarheid_Status IN ('Ingetrokken')) 
				AND `tbl_OG_wonen`.bouwgrond_HuidigeBestemming<>'' )
		
		UNION ALL 
		
		(SELECT 
			`tbl_OG_nieuwbouw_bouwNummers`.id AS id, 
			`tbl_OG_nieuwbouw_bouwNummers`.id_OG_nieuwbouw_projecten AS id_OG_nieuwbouw_projecten, 
			`tbl_OG_nieuwbouw_bouwNummers`.id_OG_nieuwbouw_bouwTypes AS id_OG_nieuwbouw_bouwTypes, 
		
			`tbl_OG_nieuwbouw_bouwNummers`.datum_toegevoegd, 
			`tbl_OG_nieuwbouw_bouwNummers`.bouwNummer_NVMVestigingNR AS NVMVestigingNR, 
			`tbl_OG_nieuwbouw_bouwNummers`.bouwNummer_ObjectTiaraID AS ObjectTiaraID, 
		
			`tbl_OG_nieuwbouw_bouwNummers`.Status_ObjectStatus AS status, 
		
			`tbl_OG_nieuwbouw_bouwNummers`.Adres_Straatnaam AS straatnaam, 
			`tbl_OG_nieuwbouw_bouwNummers`.Adres_Huisnummer AS huisnummer, 
			`tbl_OG_nieuwbouw_bouwNummers`.Adres_HuisnummerToevoeging AS huisnummerToevoeging, 
			`tbl_OG_nieuwbouw_bouwNummers`.Adres_Woonplaats AS woonplaats, 
		
			'' as koopprijsVoorvoegsel,
			`tbl_OG_nieuwbouw_bouwNummers`.Financieel_Koop_Koopprijs AS koopprijs, 
			`tbl_OG_nieuwbouw_bouwNummers`.Financieel_Koop_KoopConditie AS koopconditie, 
			`tbl_OG_nieuwbouw_bouwNummers`.Financieel_Huur_Huurprijs AS huurprijs, 
			`tbl_OG_nieuwbouw_bouwNummers`.Financieel_Huur_HuurConditie AS huurconditie, 
		
			'-' AS huidigGebruik, 
			`tbl_OG_nieuwbouw_bouwNummers`.MatenEnLigging_PerceelOppervlakte AS oppervlakte, 
			`tbl_OG_nieuwbouw_bouwNummers`.MatenEnLigging_Liggingen AS liggingen, 
			'nieuwbouw_' AS ogType,

			(SELECT `cms_per_link` FROM `tbl_cms_permaLinks` WHERE `cms_per_tableId`=40 AND `cms_per_tableName`='tbl_mod_pages' AND `cms_per_moduleExtra`=`tbl_OG_nieuwbouw_bouwNummers`.`id`) as `cms_per_link`,
			(SELECT `bestandsnaam_medium` FROM `tbl_OG_media` WHERE `id_OG_nieuwbouw_bouwnummers`=`tbl_OG_nieuwbouw_bouwNummers`.`id` AND `media_status`=2 AND `media_groep` IN ('Hoofdfoto', 'Foto')  ORDER BY `media_groep` DESC, `media_id` ASC LIMIT 1) as `mainImage`,
		
			`tbl_OG_nieuwbouw_projecten`.project_ProjectDetails_Projectnaam AS projectnaam 
		
			FROM `tbl_OG_nieuwbouw_bouwNummers` 
			INNER JOIN `tbl_OG_nieuwbouw_projecten` ON `tbl_OG_nieuwbouw_projecten`.id=`tbl_OG_nieuwbouw_bouwNummers`.id_OG_nieuwbouw_projecten 
		
				WHERE NOT `tbl_OG_nieuwbouw_bouwNummers`.Status_ObjectStatus IN ('Ingetrokken') 
				AND `tbl_OG_nieuwbouw_bouwNummers`.Wonen_Woonhuis_SoortWoning='' 
				AND `tbl_OG_nieuwbouw_bouwNummers`.Wonen_Woonhuis_TypeWoning='' 
				AND `tbl_OG_nieuwbouw_bouwNummers`.Wonen_Verdiepingen_Aantal=0 
		
				AND NOT `tbl_OG_nieuwbouw_projecten`.project_ProjectDetails_Status_ObjectStatus IN ('Ingetrokken') )
		ORDER BY woonplaats ASC
	";
}

// Let's handle paging!
$currentPage = $filter['p'];

$perPage = 8;

$tempQ = $cms['database']->prepare($sql);
$totalRows = count($tempQ);

$totalPages = ceil($totalRows / $perPage);

if ($currentPage <= 0)
	$currentPage = 1;

if ($currentPage > $totalPages)
	$currentPage = $totalPages;

$currentResult = ($currentPage - 1) * $perPage;

// On first page, stille verkoop & video
if ($filter['p'] == 1) {

	$currentResult = 0;
	$perPage = 8;
}
else {

	$currentResult = $currentResult - 2;
}

if ($totalRows > 0 && $filter['viewType'] != 'map')
	$sql .= " LIMIT " . $currentResult . "," . $perPage;

// Kick out the query
$objects = $cms['database']->prepare($sql);

$MD5 = generateHash($filter, array('searchHash', 'p'));
$fullMD5 = generateHash($filter, array('searchHash', 'viewType'));
$orderMD5 = generateHash($filter, array('p', 'orderBy', 'searchHash'));
$sortMD5 = generateHash($filter, array('p', 'sortBy', 'searchHash'));

$tempFilter = $filter;
unset($tempFilter['p']);
unset($tempFilter['sortBy']);
unset($tempFilter['orderBy']);

$md5SQL = md5(serialize($tempFilter));

function generateHash($filters, $excludes = array()) {

	global $_SESSION;

	$searchQuerySort = '';

	foreach ($filters as $key => $val) {

		if (!in_array($key, $excludes)) {

			if (is_array($val)) {

				foreach ($val as $sKey => $sVal) {

					$searchQuerySort .= '&' . $key . '[]=' . $sVal;
				}
			}
			else {

				$searchQuerySort .= '&' . $key . '=' . $val;
			}
		}
	}

	$_SESSION['search'][md5('searchQuerySort_' . $_SERVER['REMOTE_ADDR'] . $searchQuerySort)] = $filters;

	if (count($excludes) > 0) {

		foreach ($excludes as $key => $val)
			unset($_SESSION['search'][md5('searchQuerySort_' . $_SERVER['REMOTE_ADDR'] . $searchQuerySort)][$val]);
	}
			
	return md5('searchQuerySort_' . $_SERVER['REMOTE_ADDR'] . $searchQuerySort);
}

if ($overviewType == 'kopen' || $overviewType == 'kavels')
	$noFilters = '156c72c3808f0f0644d4a7446008a93d';
elseif ($overviewType == 'huren')
	$noFilters = '837cdcb7cb5af81209ebd8679c9a3b8e';

?>

<!doctype html>

<html lang="nl">
	<head>
		
		<?php include($documentRoot . "inc/head.php"); ?>

	</head>

	<body>
		<div class="page-wrapper aanbod-overzicht">

			<?php include($documentRoot . "inc/primary-nav.php"); ?>

			<div class="column-content">
				
				<div class="column-sidebar">

				<?php include($documentRoot . "inc/aanbod-filtering-wonen.php"); ?>

				</div>

				<div class="column-content">

					<div class="content-wrapper">
					
					<?php
					
					if (count($objects) > 0) {
						
						foreach ($objects as $key => $val) {
							
							if ($overviewType == 'kavels')								
								include($documentRoot . 'inc/templates/aanbod-kavels.php');
							else
								include($documentRoot . 'inc/templates/aanbod-wonen.php');
						}
					}
					else {
						
						echo '<p>Er zijn geen woningen gevonden voor de opgegeven filtering.</p>';
					}
					
					?>
					
					<?php include($documentRoot . "inc/aanbod-banner.php"); ?>
					
					<?php
					
					$pagingUrl = $dynamicRoot . $template->getPermaLink($template->getCurrentLanguage()) . '.html?searchHash=' . $MD5;
					
					include($documentRoot . "inc/paging.php");
					
					?>
					</div>


				</div>
			</div>

			<?php include($documentRoot . "inc/footer.php"); ?>

		</div>
		
		<?php include($documentRoot . "inc/footer-scripting.php"); ?>

		<script>

		</script>
	</body>

</html>
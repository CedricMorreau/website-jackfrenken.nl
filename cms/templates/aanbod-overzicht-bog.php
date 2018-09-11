<?php

if (isset($_GET['searchHash'])) {

	$searchHash = $_GET['searchHash'];

	if (isset($_SESSION['searchBog'][$searchHash])) {

		foreach ($_SESSION['searchBog'][$searchHash] as $key => $val) {

			$_GET[$key] = $val;
		}
	}

	unset($_SESSION['searchBog'][$searchHash]);
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

$filter['provincie'] = (isset($_POST['provincie'])) ? $_POST['provincie'] : ((isset($_GET['provincie'])) ? $_GET['provincie'] : 0);
$filter['straatnaam'] = (isset($_POST['straatnaam'])) ? $_POST['straatnaam'] : ((isset($_GET['straatnaam'])) ? $_GET['straatnaam'] : '');
$filter['plaatsnaam'] = (isset($_POST['plaatsnaam'])) ? $_POST['plaatsnaam'] : ((isset($_GET['plaatsnaam'])) ? $_GET['plaatsnaam'] : '');
$filter['radius'] = (isset($_POST['radius'])) ? $_POST['radius'] : ((isset($_GET['radius'])) ? $_GET['radius'] : '');
$filter['prijsVan'] = (isset($_POST['prijsVan'])) ? $_POST['prijsVan'] : ((isset($_GET['prijsVan'])) ? $_GET['prijsVan'] : 0);
$filter['prijsTot'] = (isset($_POST['prijsTot'])) ? $_POST['prijsTot'] : ((isset($_GET['prijsTot'])) ? $_GET['prijsTot'] : '');
$filter['saleType'] = (isset($_POST['saleType'])) ? $_POST['saleType'] : ((isset($_GET['saleType'])) ? $_GET['saleType'] : 'kopen');

if ($filter['saleType'] == 'rent')
	$filter['rentType'] = (isset($_POST['rentType'])) ? $_POST['rentType'] : ((isset($_GET['rentType'])) ? $_GET['rentType'] : '');

$filter['viewType'] = (isset($_POST['viewType'])) ? $_POST['viewType'] : ((isset($_GET['viewType'])) ? $_GET['viewType'] : 'list');

// $filter['prijsOpAanvraag'] = (isset($_POST['prijsOpAanvraag'])) ? $_POST['prijsOpAanvraag'] : ((isset($_GET['prijsOpAanvraag'])) ? $_GET['prijsOpAanvraag'] : '');

$filter['bestemming'] = (isset($_POST['bestemming'])) ? $_POST['bestemming'] : ((isset($_GET['bestemming'])) ? $_GET['bestemming'] : ''); //str_f01
$filter['bedrijfswoning'] = (isset($_POST['bedrijfswoning'])) ? $_POST['bedrijfswoning'] : ((isset($_GET['bedrijfswoning'])) ? $_GET['bedrijfswoning'] : array()); //str_f02
$filter['hoofdfunctie'] = (isset($_POST['hoofdfunctie'])) ? $_POST['hoofdfunctie'] : ((isset($_GET['hoofdfunctie'])) ? $_GET['hoofdfunctie'] : array());
$filter['verkocht'] = (isset($_POST['verkocht'])) ? $_POST['verkocht'] : ((isset($_GET['verkocht'])) ? $_GET['verkocht'] : '');
$filter['video'] = (isset($_POST['video'])) ? $_POST['video'] : ((isset($_GET['video'])) ? $_GET['video'] : '');

$filter['openhuis'] = (isset($_POST['openhuis'])) ? $_POST['openhuis'] : ((isset($_GET['openhuis'])) ? $_GET['openhuis'] : '');

// $filter['slaapkamers'] = (isset($_POST['slaapkamers'])) ? $_POST['slaapkamers'] : ((isset($_GET['slaapkamers'])) ? $_GET['slaapkamers'] : ''); //str_f02
// $filter['perceelOppervlakte'] = (isset($_POST['perceelOppervlakte'])) ? $_POST['perceelOppervlakte'] : ((isset($_GET['perceelOppervlakte'])) ? $_GET['perceelOppervlakte'] : ''); //str_f03
// $filter['woonfunctieOppervlakte'] = (isset($_POST['woonfunctieOppervlakte'])) ? $_POST['woonfunctieOppervlakte'] : ((isset($_GET['woonfunctieOppervlakte'])) ? $_GET['woonfunctieOppervlakte'] : '');
// $filter['slaapkamers'] = (isset($_POST['slaapkamers'])) ? $_POST['slaapkamers'] : ((isset($_GET['slaapkamers'])) ? $_GET['slaapkamers'] : '');
// $filter['openhuisVanaf'] = (isset($_POST['openhuisVanaf'])) ? $_POST['openhuisVanaf'] : ((isset($_GET['openhuisVanaf'])) ? $_GET['openhuisVanaf'] : '');

if (!empty($filter['verkocht']) && $filter['verkocht'] == 1) {

	$extraState = '';
}
else {

	$extraState = ", 'gearchiveerd'";
}

// Very basic query
$sql = "SELECT
		`tbl_OG_bog`.id,
        'BOG' as `og_type`,

		`tbl_OG_bog`.object_NVMVestigingNR,
		`tbl_OG_bog`.object_ObjectTiaraID,

		`tbl_OG_bog`.objectDetails_Adres_Woonplaats,
		`tbl_OG_bog`.objectDetails_Adres_Straatnaam,
		`tbl_OG_bog`.objectDetails_Adres_Huisnummer,
		`tbl_OG_bog`.objectDetails_Adres_HuisnummerToevoeging,
		`tbl_OG_bog`.objectDetails_Koop_PrijsSpecificatie_Prijs,
		`tbl_OG_bog`.objectDetails_Koop_KoopConditie,
		`tbl_OG_bog`.objectDetails_Huur_PrijsSpecificatie_Prijs,
		`tbl_OG_bog`.objectDetails_Huur_HuurConditie,
		`tbl_OG_bog`.objectDetails_Bestemming_Hoofdbestemming,
		`tbl_OG_bog`.objectDetails_Bestemming_Nevenbestemmingen,
		`tbl_OG_bog`.datum_toegevoegd,
		objectDetails_Bouwgrond_Bebouwingsmogelijkheid,
		'NIEUW' as `tblType`,

		`tbl_OG_bog`.objectDetails_Status_StatusType,

		`tbl_OG_bog`.objectDetails_Status_TransactieDatum,

		`tbl_OG_bog`.google_lat,
		`tbl_OG_bog`.google_lng,

		`objectDetails_Woonobject_Oppervlakte`,
		`objectDetails_Bedrijfshal_Oppervlakte`,
		`objectDetails_Kantoorruimte_Oppervlakte`,
		`objectDetails_BKantoorruimte_Oppervlakte`,
		`objectDetails_Terrein_Oppervlakte`,
		`objectDetails_Horeca_Oppervlakte`,
		`objectDetails_Winkelruimte_Oppervlakte`,
		`object_Web_PrijsTonen`,
		`tbl_OG_objectDetails`.`ood_alternativeStatus`,


		`tbl_OG_provincies`.`provincie`,
		LOWER(`tbl_OG_provincies`.`provincie`) AS `lcase_provincie`,
        (SELECT `cms_per_link` FROM `tbl_cms_permaLinks` WHERE `cms_per_tableId`=42 AND `cms_per_tableName`='tbl_mod_pages' AND `cms_per_moduleId`=`tbl_OG_bog`.`id`) as `cms_per_link`,
		(SELECT `bestandsnaam_medium` FROM `tbl_OG_media` WHERE `id_OG_bog`=`tbl_OG_bog`.`id` AND `media_status`=2 AND `media_groep` IN ('Hoofdfoto', 'Foto')  ORDER BY `media_groep` DESC, `media_id` ASC LIMIT 1) as `mainImage`

	FROM `tbl_OG_bog`
		LEFT JOIN `tbl_OG_objectDetails` ON `ood_ogId`=`tbl_OG_bog`.`id` AND `ood_table`='tbl_OG_bog'
		INNER JOIN `tbl_OG_provincies` ON `tbl_OG_provincies`.`id`=`tbl_OG_bog`.`id_provincies`

	WHERE
		(NOT `tbl_OG_bog`.`objectDetails_Status_StatusType` IN ('Ingetrokken', 'ingetrokken'" . $extraState . "))
		AND (`ood_onlineStatus` IS NULL or `ood_onlineStatus`=1)
		AND `tbl_OG_bog`.`id`<>0 ";

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
$filter['p'] = (isset($_POST['p'])) ? $_POST['p'] : ((isset($_GET['p'])) ? $_GET['p'] : 1);

// Correct weird sortBy
if (!in_array($filter['sortBy'], array('asc', 'desc')))
	$filter['sortBy'] = 'desc';

// Pronvincie filter
if (!empty($filter['provincie']) && $filter['provincie'] > 0) {

	$sql .= " AND `tbl_OG_bog`.`id_provincies`=" . $cms['database']->escape($filter['provincie']);
}

if (!empty($filter['verkocht']) && $filter['verkocht'] == 1) {

	$sql .= " AND `tbl_OG_bog`.`objectDetails_Status_StatusType` IN ('Verhuurd', 'verhuurd', 'Verkocht', 'verkocht', 'gearchiveerd') ";
}

if (!empty($filter['video']) && $filter['video'] == 1) {

	$sql .= " AND `tbl_OG_objectDetails`.`ood_helicopterVideo` IS NOT NULL AND `tbl_OG_objectDetails`.`ood_helicopterVideo`!='' ";
}

// Plaatsnaam / postcode
if (!empty($filter['plaatsnaam'])) {

	$filter['saleType'] = 'kopen';

	// Construct next query
	$sql .= " AND (";

	// Postcode
	$sql .= "`tbl_OG_bog`.`objectDetails_Adres_Postcode` LIKE '%" . $cms['database']->escape($filter['plaatsnaam']) . "%' ";

	// Plaatsnaam
	$sql .= "OR lower(`tbl_OG_bog`.`objectDetails_Adres_Woonplaats`) LIKE '%" . $cms['database']->escape(strtolower($filter['plaatsnaam'])) . "%' ";

	// Straat
	$sql .= "OR lower(`tbl_OG_bog`.`objectDetails_Adres_Straatnaam`) LIKE '%" . $cms['database']->escape(strtolower($filter['plaatsnaam'])) . "%' ";

	// Radius
	if (!empty($filter['radius'])) {

		$filter['landcode'] = 'NL';

		include($documentRoot . 'inc/google_searchRadius.php');

		if (isset($boundingBox)) {

			$sql .= " OR (" .
						"`tbl_OG_bog`.`google_status`=1 " .
						"AND `tbl_OG_bog`.`google_lat` BETWEEN " . $cms['database']->escape($boundingBox['lat1']) . " AND " . $cms['database']->escape($boundingBox['lat2']) . " " .
						"AND `tbl_OG_bog`.`google_lng` BETWEEN " . $cms['database']->escape($boundingBox['lon1']) . " AND " . $cms['database']->escape($boundingBox['lon2']) . " " .
					")";
		}
	}

	// Close
	$sql .= ')';
}

// Plaatsnaam / postcode
if (!empty($filter['straatnaam'])) {

	// Construct next query
	$sql .= " AND (";
	// Straat
	$sql .= "lower(`tbl_OG_bog`.`objectDetails_Adres_Straatnaam`) LIKE '%" . $cms['database']->escape(strtolower($filter['straatnaam'])) . "%' ";

	// Close
	$sql .= ')';
}

if (!empty($filter['saleType'])) {

	if ($filter['saleType'] == 'rent') {

		$sql .= " AND `tbl_OG_bog`.`objectDetails_Huur_PrijsSpecificatie_Prijs`>=0 ";
	}
	elseif ($filter['saleType'] == 'both') {

		$sql .= " AND `tbl_OG_bog`.`objectDetails_Koop_PrijsSpecificatie_Prijs`>=0 ";
	}
	else {

		$sql .= " AND (`tbl_OG_bog`.`objectDetails_Huur_PrijsSpecificatie_Prijs`>=0 OR `tbl_OG_bog`.`objectDetails_Koop_PrijsSpecificatie_Prijs`>=0 OR `tbl_OG_bog`.`objectDetails_Huur_PrijsSpecificatie_Prijs` IS NULL OR `tbl_OG_bog`.`objectDetails_Koop_PrijsSpecificatie_Prijs` IS NULL) ";
	}
}

if (!empty($filter['rentType'])) {

	$arrAllowed = array('per maand', 'per jaar', 'per vierkante meter per jaar');

	if (in_array($filter['rentType'], $arrAllowed))
		$sql .= " AND `tbl_OG_bog`.`objectDetails_Huur_HuurConditie`='" . $cms['database']->escape($filter['rentType']) . "' ";
}

// Prijs van
if (!empty($filter['prijsVan']) && $filter['prijsVan'] > 0) {

	if ($filter['saleType'] == 'rent')
		$sql .= " AND `tbl_OG_bog`.`objectDetails_Huur_PrijsSpecificatie_Prijs`>=" . $cms['database']->escape($filter['prijsVan']) . " ";
	else
		$sql .= " AND `tbl_OG_bog`.`objectDetails_Koop_PrijsSpecificatie_Prijs`>=" . $cms['database']->escape($filter['prijsVan']) . " ";
}

// Prijs tot
if (!empty($filter['prijsTot']) && $filter['prijsTot'] > 0) {

	if ($filter['prijsTot'] > $filter['prijsVan']) {

		if ($filter['saleType'] == 'rent')
			$sql .= " AND `tbl_OG_bog`.`objectDetails_Huur_PrijsSpecificatie_Prijs`<=" . $cms['database']->escape($filter['prijsTot']) . " ";
		else
			$sql .= " AND `tbl_OG_bog`.`objectDetails_Koop_PrijsSpecificatie_Prijs`<=" . $cms['database']->escape($filter['prijsTot']) . " ";
	}
}

// Type object
if (!empty($filter['bestemming'])) {

	$sql .= " AND `tbl_OG_bog`.`objectDetails_Bestemming_Hoofdbestemming` LIKE '%" . $cms['database']->escape($filter['bestemming']) . "%' ";
}

// Hoofdfunctie
if (!empty($filter['hoofdfunctie'])) {

	if (!is_array($filter['hoofdfunctie'])) {
		$arrData = array();
		$arrData[] = $filter['hoofdfunctie'];
	}

	if (count($arrData) > 0) {

		$hoofdfuncties = array();

		foreach ($arrData as $key => $val) {

			$hoofdfuncties[] = "'" . $val . "'";
		}

		$implodeFuncties = implode(',', $hoofdfuncties);

		$sql .= " AND `tbl_OG_bog`.`objectDetails_Bestemming_Hoofdbestemming` IN (" . $implodeFuncties . ") ";
	}
}

// Handle sorting
$tempSql = '';
switch ($data['orderByReal']) {

	case 'prijs':

		$tempSql .= " `objectDetails_Koop_PrijsSpecificatie_Prijs` " . $filter['sortBy'] . ", `objectDetails_Adres_Woonplaats` ASC ";

		break;
	case 'plaatsnaam':

		$tempSql .= " `objectDetails_Adres_Woonplaats` " . $filter['sortBy'] . " ";

		break;
	case 'datum':

		if (!isset($_GET['verkocht']))
			$tempSql .= " `objectDetails_DatumInvoer` " . $filter['sortBy'] . " ";

		break;

	default:

		if (!isset($_GET['verkocht']))
			$tempSql .= " `objectDetails_DatumInvoer` DESC ";

}

if (!empty($tempSql)) {

	$sql .= " ORDER BY" . $tempSql;
}

// Let's handle paging!
$currentPage = $filter['p'];

$perPage = 10;

$tempQ = $cms['database']->prepare($sql);
$totalRows = count($tempQ);

$totalPages = ceil($totalRows / $perPage);

if ($currentPage <= 0)
	$currentPage = 1;

if ($currentPage > $totalPages)
	$currentPage = $totalPages;

$currentResult = ($currentPage - 1) * $perPage;

if ($totalRows > 0 && $filter['viewType'] != 'map')
	$sql .= " LIMIT " . $currentResult . "," . $perPage;

// Kick out the query
$objects = $cms['database']->prepare($sql);

$MD5 = generateHash($filter, array('searchHash', 'p'));
$MD5None = generateHashNone($filter, array('searchHash', 'p'));
$fullMD5 = generateHash($filter, array('searchHash', 'viewType'));
$orderMD5 = generateHash($filter, array('p', 'orderBy', 'searchHash'));
$sortMD5 = generateHash($filter, array('p', 'sortBy', 'searchHash'));

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

	$_SESSION['searchBog'][md5('searchQuerySort_' . $_SERVER['REMOTE_ADDR'] . $searchQuerySort)] = $filters;

	if (count($excludes) > 0) {

		foreach ($excludes as $key => $val)
			unset($_SESSION['searchBog'][md5('searchQuerySort_' . $_SERVER['REMOTE_ADDR'] . $searchQuerySort)][$val]);
	}
			
	return md5('searchQuerySort_' . $_SERVER['REMOTE_ADDR'] . $searchQuerySort);
}

function generateHashNone($filters, $excludes = array()) {
	
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
	
	return md5('searchQuerySort_' . $searchQuerySort);
}

$ogType = 'bog';

$noFilters = 'fddd2a713d1f5c27ffb44c280c795fe2';

?>

<!doctype html>

<html lang="nl">
	<head>
		
		<?php include($documentRoot . "inc/head.php"); ?>
		
		<link rel="stylesheet" href="<?php echo $dynamicRoot; ?>css/autocomplete.css">

	</head>

	<body>
		<div class="page-wrapper aanbod-overzicht">

			<?php include($documentRoot . "inc/primary-nav.php"); ?>

			<div class="column-content">
				
				<div class="column-sidebar">

				<?php include($documentRoot . "inc/aanbod-filtering-bedrijven.php"); ?>

				</div>

				<div class="column-content">

					<div class="content-wrapper">
					
					<?php
					
					if (count($objects) > 0) {
						
						foreach ($objects as $key => $val) {
							
							include($documentRoot . 'inc/templates/aanbod-bog.php');
						}
					}
					else {
						
						echo '<p>Er zijn geen woningen gevonden voor de opgegeven filtering.</p>';
					}
					
					?>
										
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
		
		<script type="text/javascript" src="<?php echo $dynamicRoot; ?>js/global-makelaardij.js"></script>
		<script type="text/javascript" src="<?php echo $dynamicRoot; ?>js/jquery-ui-1.10.3.custom.min.js"></script>
	</body>

</html>
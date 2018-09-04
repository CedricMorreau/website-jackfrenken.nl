<?php

function langData($key) {

	echo Languages::returnLang($key);
}

include("cms/inc/config.php");

$browserDetect = new Mobile_Detect;

if (!isset($_GET['page']) || isset($_GET['page']) && empty($_GET['page'])) {

	$shortCode = (count($cms['languages']) > 1) ? $cms['languages'][0]['cms_la_shortName'] . '/' : '';

	// Core::redirect($shortCode . 'travel-management/welkom.html');
	$newUrl = $shortCode . 'welkom.html';
	$useIndex = 0;

}
else {

	// FIRST OF ALL.. see if the ENTIRE URL exists in urlReferers
	$checkUrl = $cms['database']->prepare("SELECT * FROM `tbl_cms_urlReferers` WHERE `sourceUrl`=?", "s", array(trim($_GET['page'], '/')));

	if (count($checkUrl) > 0) {
		
// 		Core::redirect($checkUrl[0]['destinationId'], '301');

		$fetchUrl = $cms['database']->prepare("SELECT * FROM `tbl_cms_permaLinks` WHERE `cms_per_tableName`='tbl_mod_pages' AND `cms_per_tableField`='mod_pa_id' AND `cms_per_tableId`=?", "i", array($checkUrl[0]['destinationId']));

		if (count($fetchUrl) > 0) {

			$constructRedirect = $dynamicRoot . $fetchUrl[0]['cms_per_link'];

			if (!empty($checkUrl[0]['destinationBit']))
				$constructRedirect .= $checkUrl[0]['destinationBit'];

			Core::redirect($constructRedirect, '301');
		}
	}

	// Handle the url..
	$urlSplit = explode('/', $_GET['page']);

	// Check if first parameters is a language
	$langCode = '';
	$useLang = 0;

	foreach ($cms['languages'] as $key => $val) {

		if (strtolower($val['cms_la_shortName']) == strtolower(reset($urlSplit))) {

			unset($urlSplit[key($urlSplit)]);
			$langCode = $val['cms_la_shortName'];
			$useLang = $val['cms_la_id'];
		}
	}

	if (empty($langCode)) {

		$useLang = $cms['languages'][0]['cms_la_id'];
		$langCode = $cms['languages'][0]['cms_la_shortName'];
	}

	$useIndex = 0;

	// Construct new URL
	if (count($urlSplit) > 0 && reset($urlSplit) != '')
		$newUrl = implode('/', $urlSplit);
	else
		$newUrl = 'welkom.html';

	if ($newUrl == 'welkom.html');
		$useIndex = 1;

	if (!empty($langCode))
		$newUrl = $langCode . '/' . $newUrl;
}

if (isset($_GET['objectId'])) {

	include("og_redirect.php");
}

if ($useIndex == 1)
	$languageUsage = $useLang;
else
	$languageUsage = $cms['languages'][0]['cms_la_id'];

// FIXIT: SB - 404 redirects 
//if ($newUrl == 'welkom.html' || $newUrl == 'NL/404.shtml')
if ($newUrl == 'welkom.html' || $newUrl == 'NL/404.shtml')
	$newUrl = 'NL/welkom.html';

$template = new Templates(str_replace('.html', '', $newUrl), $cms, $languageUsage);

$templateFile = $template->getTemplateData('templateName');

// Check for a redirect
$template->handleRedirect();

Languages::fillLanguage($template->getCurrentLanguage());

// Include the template file
if (file_exists("cms/templates/" . $templateFile))
	include("cms/templates/" . $templateFile);
else {

	// FIXIT: Temp voor concept!
	//	Core::redirect('/welkom.html');
	// die("Fatal error: Het opgegeven template kon niet gevonden worden.");
}

// Core::vd($cms['database']->fetchQueries());

?>
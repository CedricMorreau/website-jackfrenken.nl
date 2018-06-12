<?php

// Start session
session_start();

// OB
ob_start();

// FIXIT SB: Implementeren? Nog uitzoeken.
// include("cms/classes/bad-behavior/bad-behavior-generic.php");

// bb2_install();	// FIXME: see above

// bb2_start(bb2_read_settings());

// Global settings
$test = 1;
$localIp = '192.168.1.10';
$documentRoot = str_replace("\\", "/", $_SERVER['DOCUMENT_ROOT']) . '/';
$dynamicRoot = '/';

// This variable is used to define the version of the website (css files, js files, etc.)
// No &-signs!
$cms['siteVersion'] = '20170214';

include($documentRoot . 'inc/cms_functions.php');

// Autoloader
spl_autoload_register(function($class_name) {

	global $documentRoot;

	include($documentRoot . 'cms/classes/' . strtolower($class_name) . '.class.php');

});

// If we're local, set to test mode
if (isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], $localIp) !== false) {

	$test = 1;
}

// Set up document root, database connection, etc.
if ($test == 1) {

	$cms['database'] = new Database();
	$cms['database']->connection('jackfrenk_cms-usr', 'EtIWFhIhQP', 'mysql02.pixelplus.nl');
	$cms['database']->selectDatabase('jackfrenk_cms-db');
}
else {

	$cms['database'] = new Database();
	$cms['database']->connection('jackfrenk_cms-usr', 'EtIWFhIhQP', 'localhost');
	$cms['database']->selectDatabase('jackfrenk_cms-db');
}

// Select all languages into a global array
$cms['languages'] = $cms['database']->prepare("SELECT `cms_la_id`, `cms_la_name`, `cms_la_shortName`, `cms_la_flavourName`, `cms_la_flavourText` FROM `tbl_cms_languages` WHERE `cms_la_status`=1 ORDER BY `cms_la_id` ASC");

?>
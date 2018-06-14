<?php

$documentRoot = str_replace("\\", "/", $_SERVER['DOCUMENT_ROOT']) . '/';

// See if there's any widgets
$fetchWidgets = $this->cms['database']->prepare("SELECT * FROM `tbl_mod_pageWidgetsData` WHERE `mod_pd_blockId`=? AND `mod_pd_pageId`=? AND `mod_pd_widgetStatus`=1 ORDER BY `mod_pd_sortOrder`", "ii", array($blockId, $this->pageId));

if (count($fetchWidgets) > 0) {

	foreach ($fetchWidgets as $wKey => $wVal) {

		// See if file even exists
		if (file_exists($documentRoot . 'cms/blocks/widgets/' . $wVal['mod_pd_type'] . '.php')) {

			include($documentRoot . 'cms/blocks/widgets/' . $wVal['mod_pd_type'] . '.php');
		}
	}
}
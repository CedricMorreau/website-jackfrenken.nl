<?php

// For each template, see the similarly named templates (eg. aanbod-overzicht-wonen.php)
if ($template->getPageId() == 38 || $template->getPageId() == 39 || $template->getPageId() == 41) {

	include('aanbod-detail-wonen.php');
}
elseif ($template->getPageId() == 42) {

	include('aanbod-detail-bedrijven.php');
}
elseif ($template->getPageId() == 40) {

	include('aanbod-detail-agrarisch.php');
}
else {

	Core::redirect($dynamicRoot . $template->findPermalink(33, 1) . '.html');
}

?>
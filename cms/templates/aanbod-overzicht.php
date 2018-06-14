<?php

// For each template, see the similarly named templates (eg. aanbod-overzicht-wonen.php)
if ($template->getPageId() == 33 || $template->getPageId() == 34 || $template->getPageId() == 36) {

	include('aanbod-overzicht-wonen.php');
}
elseif ($template->getPageId() == 37) {

	include('aanbod-overzicht-bog.php');
}
elseif ($template->getPageId() == 35) {

	include('aanbod-overzicht-nieuwbouw.php');
}
else {

	Core::redirect($dynamicRoot . $template->findPermalink(33, 1) . '.html');
}

?>
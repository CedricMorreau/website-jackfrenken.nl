<?php

// See if article even exists
$article = $cms['database']->prepare("SELECT * FROM `tbl_mod_articleContent` INNER JOIN `tbl_cms_permaLinks` ON (`cms_per_tableId`=`mod_co_id` AND `cms_per_tableName`='tbl_mod_articleContent') WHERE `cms_per_link`=? AND `mod_co_articletypeId`=1", "s", array($template->detailPage));

if (count($article) <= 0) {
	
	Core::redirect('/404-pagina-niet-gevonden');
}

$values = Content::getArticleValues($article[0]['mod_co_id'], $cms, $template->getCurrentLanguage());

if (!isset($values['art_status'][1]) && !$template->previewMode())
	Core::redirect('/404-pagina-niet-gevonden');
	
$injectTitle = $values['art_title'] . $template->getPageDataMulti('pageTitle');
$injectImage = $values['art_overviewPhoto'];

$useTitle = $values['art_title'];

if (!($url = $template->getBackUrl($template->findPermalink(59, 1)))) {
	
	$url = $template->findPermalink(59, 1);
}

?>

<!doctype html>

<html lang="nl">
	<head>
		
		<?php include($documentRoot . "inc/head.php"); ?>

	</head>

	<body>
		<!-- KMH pixel --> 
		<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
		new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0], j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src= '//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f); })(window,document,'script','kmhPixel','GTM-PX4GN2');</script> 
		<!-- End KMH pixel -->

		<div class="page-wrapper actueel-detail">

			<?php include($documentRoot . "inc/primary-nav.php"); ?>

			<?php include($documentRoot . "inc/actueel-detail-header.php"); ?>

			<div class="column-content">
				
				<div class="column-sidebar">
				
					<a href="<?php echo $url; ?>" class="back-link ali-top">&xlarr; Terug naar overzicht</a>

				</div>

				<div class="column-content">

					<div class="content-wrapper">
						<a id="content" class="anchor"></a>
						<?php echo $values['art_content']; ?>
					</div>

				</div>
			</div>

			<?php include($documentRoot . "inc/footer.php"); ?>

		</div>
		
		<?php include($documentRoot . "inc/footer-scripting.php"); ?>

	</body>

</html>
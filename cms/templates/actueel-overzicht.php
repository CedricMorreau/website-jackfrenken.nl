<?php

$types = array('nieuws', 'columns', 'woonkrant');

if (!isset($_GET['type']))
	$_GET['type'] = 'nieuws';

if (!in_array($_GET['type'], $types))
	$_GET['type'] = 'nieuws';

$pageOverride = ucfirst($_GET['type']);

$template->cmsData('page][section/sfeerbeeld');
$sfeerbeeld = trim($template->getCustomVar('sfeerbeeld'));

$sortering = 'desc';

if (!empty($_GET['sortering'])) {
	
	if ($_GET['sortering'] == 'asc')
		$sortering = 'asc';
}

// Mini function hack 'n slash active status
function isActive($name) {
	
	if ($_GET['type'] == $name)
		return true;
	
	return false;
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

		<div class="page-wrapper actueel-overzicht">

			<?php include($documentRoot . "inc/primary-nav.php"); ?>

			<div class="column-content">
				
				<div class="column-sidebar">
					<h1 class="sidebar-title">Actualiteiten</h1>
					<ul class="sidebar-nav">
					
						<?php
						
						foreach ($types as $key => $val) {
							
							?>
							
						<li<?php if (isActive($val)) { echo ' class="active"'; } ?>><a href="<?php echo $template->getPermalink(1); ?>?type=<?php echo $val; ?>"><?php echo ucfirst($val); ?><?php if (isActive($val)) { echo '<span class="arrow">&rsaquo;</span>'; } ?></a></li>	
							
							<?php
						}
						
						?>
					
					</ul>
	
					<?php echo $template->cmsData('page][section/widgets'); ?>

				</div>
				
				<?php
				
				$extraSql = '';
				
				if ($_GET['type'] == 'nieuws') {
					
					$extraSql .= ' AND EXISTS(SELECT `mod_cv_value` FROM `tbl_mod_articleContentValues` WHERE `mod_cv_articleId`=`mod_co_id` AND `mod_cv_attributeId`=20 AND `mod_cv_attributeValueId`=7)';
				}

				elseif ($_GET['type'] == 'columns') {
					
					$extraSql .= ' AND EXISTS(SELECT `mod_cv_value` FROM `tbl_mod_articleContentValues` WHERE `mod_cv_articleId`=`mod_co_id` AND `mod_cv_attributeId`=20 AND `mod_cv_attributeValueId`=8)';
				}
				
				if ($_GET['type'] == 'woonkrant') {
					
					$actueelCount = $cms['database']->prepare("SELECT * FROM `tbl_mod_articleContent` LEFT JOIN `tbl_cms_permaLinks` ON `cms_per_tableId`=`mod_co_id` WHERE `mod_co_articleTypeId`=3 AND EXISTS(SELECT * FROM `tbl_mod_articleContentValues` WHERE `mod_cv_articleId`=`mod_co_id` AND `mod_cv_attributeId`=16 AND `mod_cv_attributeValueId`=5 AND `mod_cv_articleId`=`mod_co_id`) AND `cms_per_tableName`='tbl_mod_articleContent' AND (SELECT `mod_cv_valueDate` FROM `tbl_mod_articleContentValues` WHERE `mod_cv_articleId`=`mod_co_id` AND `mod_cv_attributeId`=19)<NOW()" . $extraSql . " GROUP BY `mod_co_id`");
				}
				else {
					
					$actueelCount = $cms['database']->prepare("SELECT * FROM `tbl_mod_articleContent` LEFT JOIN `tbl_cms_permaLinks` ON `cms_per_tableId`=`mod_co_id` WHERE `mod_co_articleTypeId`=1 AND EXISTS(SELECT * FROM `tbl_mod_articleContentValues` WHERE `mod_cv_articleId`=`mod_co_id` AND `mod_cv_attributeId`=8 AND `mod_cv_attributeValueId`=1 AND `mod_cv_articleId`=`mod_co_id`) AND `cms_per_tableName`='tbl_mod_articleContent' AND (SELECT `mod_cv_valueDate` FROM `tbl_mod_articleContentValues` WHERE `mod_cv_articleId`=`mod_co_id` AND `mod_cv_attributeId`=1)<NOW()" . $extraSql . " GROUP BY `mod_co_id`");
				}				
				
				$totalActueel = count($actueelCount);

				if ($totalActueel > 16)
					$totalActueel = 16;
				
				$page = 1;
				$perPage = 8;
				
				// Handle the paging
				if (isset($_GET['p']) && is_numeric($_GET['p'])) {
					
					$page = $_GET['p'];
				}
				
				// Handle the perPage
				if (isset($_GET['perPage']) && is_numeric($_GET['perPage']) && $_GET['perPage'] > 0) {
					
					$perPage= $_GET['perPage'];
				}
				
				$totalPages = ceil($totalActueel / $perPage);
				
				if ($page > $totalPages)
					$page = $totalPages;
					
				if ($page < 1)
					$page = 1;
						
				$startFrom = $perPage * ($page - 1);
				$endAt = $startFrom + $perPage;
						
				if ($_GET['type'] == 'woonkrant') {
					
					$actueel = $cms['database']->prepare("SELECT *, (SELECT `mod_cv_valueDate` FROM `tbl_mod_articleContentValues` WHERE `mod_cv_articleId`=`mod_co_id` AND `mod_cv_attributeId`=19) as orderDate FROM `tbl_mod_articleContent` LEFT JOIN `tbl_cms_permaLinks` ON `cms_per_tableId`=`mod_co_id` WHERE `mod_co_articleTypeId`=3 AND EXISTS(SELECT * FROM `tbl_mod_articleContentValues` WHERE `mod_cv_articleId`=`mod_co_id` AND `mod_cv_attributeId`=16 AND `mod_cv_attributeValueId`=5 AND `mod_cv_articleId`=`mod_co_id`) AND `cms_per_tableName`='tbl_mod_articleContent' AND (SELECT `mod_cv_valueDate` FROM `tbl_mod_articleContentValues` WHERE `mod_cv_articleId`=`mod_co_id` AND `mod_cv_attributeId`=19)<NOW()" . $extraSql . " GROUP BY `mod_co_id` ORDER BY `orderDate` " . $sortering. " LIMIT " . $startFrom . ", " . $perPage);
				}
				else {
					
					$actueel = $cms['database']->prepare("SELECT *, (SELECT `mod_cv_valueDate` FROM `tbl_mod_articleContentValues` WHERE `mod_cv_articleId`=`mod_co_id` AND `mod_cv_attributeId`=1) as orderDate FROM `tbl_mod_articleContent` LEFT JOIN `tbl_cms_permaLinks` ON `cms_per_tableId`=`mod_co_id` WHERE `mod_co_articleTypeId`=1 AND EXISTS(SELECT * FROM `tbl_mod_articleContentValues` WHERE `mod_cv_articleId`=`mod_co_id` AND `mod_cv_attributeId`=8 AND `mod_cv_attributeValueId`=1 AND `mod_cv_articleId`=`mod_co_id`) AND `cms_per_tableName`='tbl_mod_articleContent' AND (SELECT `mod_cv_valueDate` FROM `tbl_mod_articleContentValues` WHERE `mod_cv_articleId`=`mod_co_id` AND `mod_cv_attributeId`=1)<NOW()" . $extraSql . " GROUP BY `mod_co_id` ORDER BY `orderDate` " . $sortering. " LIMIT " . $startFrom . ", " . $perPage);
				}
				
				?>

				<div class="column-content">

					<div class="content-wrapper">
						<section class="items-wrapper">
							
							<div class="items-container">
								<?php
								
								if (count($actueel) > 0) {
									
									foreach ($actueel as $key => $val) {
										
										if ($_GET['type'] == 'woonkrant') {
											
											$values = Content::getArticleValues($val['mod_co_id'], $cms, $template->getCurrentLanguage());
											
											$url = $template->getPermaLink($template->getCurrentLanguage()) . '/' . $val['cms_per_link'];
											
											$date = new PP_DateTime($values['wk_pubDate']);
											
											?>
											
											<div class="item-container">

												<div class="item-image-wrapper">
													<div class="item-image" style="background-image: url(<?php echo $values['wk_overviewPhoto']; ?>);">
														&nbsp;
													</div>
													<div class="hover-overlay"></div>
													
												</div>

												<div class="item-image-content-wrapper">
													<p class="item-title"><?php echo $values['wk_title']; ?></p>
													<div class="item-content-except">
														<!-- Lorem ipsum dolor sit amet test test test. -->
													</div>
													<?php echo $values['wk_pdf']; ?>
													<div class="item-button">&xrarr;</div>
												</div>
											</div>
											
											<?php
										}
										else {
											
											$values = Content::getArticleValues($val['mod_co_id'], $cms, $template->getCurrentLanguage());
											
											$url = $template->getPermaLink($template->getCurrentLanguage()) . '/' . $val['cms_per_link'];
											
											$date = new PP_DateTime($values['art_pubDate']);

											if (empty($values['art_overviewPhoto']))
												$values['art_overviewPhoto'] = '/resources/aanbod-no-image.jpg';
											
											// Add thumb
											if (strpos($values['art_overviewPhoto'], '?') !== false)
												$values['art_overviewPhoto'] .= '&thumb=1';
											else 
												$values['art_overviewPhoto'] .= '?thumb=1';
											
											?>
											
											<div class="item-container">

												<div class="item-image-wrapper">
													<div class="item-image" style="background-image: url(<?php echo $values['art_overviewPhoto']; ?>);">
														
													</div>
													<div class="hover-overlay">
														<a href="<?php echo $url; ?>" title="<?php echo $values['art_title']; ?>" alt="<?php echo $values['art_title']; ?>">
														</a>

													</div>

												</div>


												<div class="item-image-content-wrapper">
													<p class="item-title"><?php echo $values['art_visibleTitle'] ?? $values['art_title']; ?></p>
													
													<div class="item-content-except">
														<?php
														
														if (!empty($values['art_intro'])) {

															echo $values['art_intro'];
														}

														?>
													</div>

													<a href="<?php echo $url; ?>" class="cta-button item-button" title="Lees meer">Lees verder</a>
												</div>
												
										
											</div>
											
											<?php
										}
									}
								}
								else {
									
									echo '<p>Er zijn geen berichten beschikbaar.</p>';
								}
								
								?>

							</div>

						</section>
						
						<?php
						
						if ($totalPages > 1) {
							
						?>

						<div class="paging-wrapper">
	
							<?php
							
							$pageStart = $startFrom + 1;
							$pageEnd = $endAt;
							
							if ($pageEnd > $totalActueel)
								$pageEnd = $totalActueel;
							
							?>
	
							<!-- <div class="text-wrapper">
								Resultaten <span class="bold"><?php echo $pageStart; ?> t/m <?php echo $pageEnd; ?></span> van <span class="bold"><?php echo $totalActueel; ?></span>
							</div>
							 -->
							<?php
							
							$pagingClass = new PP_Paging($totalPages, $perPage, $page, $dynamicRoot . $template->getPermaLink($template->getCurrentLanguage()) . '?type=' . $_GET['type']);
							
							?>
							
							<div class="numbers-wrapper">
							
								<?php echo $pagingClass->displayLeft('&xlarr;'); ?>
								<?php echo $pagingClass->displayPages(); ?>
								<?php echo $pagingClass->displayRight('&xrarr;'); ?>
								
							</div>
							
						</div>
						
						<?php } ?>
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
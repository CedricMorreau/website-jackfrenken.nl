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
		<div class="page-wrapper actueel-overzicht">

			<?php include($documentRoot . "inc/primary-nav.php"); ?>

			<?php include($documentRoot . "inc/2-col-header.php"); ?>

			<div class="column-content">
				
				<div class="column-sidebar">

					<ul class="sidebar-nav">
					
						<?php
						
						foreach ($types as $key => $val) {
							
							?>
							
						<li<?php if (isActive($val)) { echo ' class="active"'; } ?>><a href="<?php echo $template->getPermalink(1); ?>?type=<?php echo $val; ?>"><?php echo ucfirst($val); ?><?php if (isActive($val)) { echo ' &xrarr;'; } ?></a></li>	
							
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
											
											$url = $template->getPermaLink($template->getCurrentLanguage()) . '/' . $val['cms_per_link'] . '.html';
											
											$date = new PP_DateTime($values['wk_pubDate']);
											
											?>
											
											<div class="item-container">
												<div class="item-image-wrapper">
													<div class="item-image" style="background-image: url(<?php echo $values['wk_overviewPhoto']; ?>);">
														&nbsp;
													</div>
													<div class="hover-overlay"></div>
													<a href="<?php echo $values['wk_pdf']; ?>" target="_BLANK" class="item-button">&xrarr;</a>
												</div>
												
												<p class="item-title"><a href="<?php echo $values['wk_pdf']; ?>" target="_BLANK"><?php echo $values['wk_title']; ?></a></p>
											
											</div>
											
											<?php
										}
										else {
											
											$values = Content::getArticleValues($val['mod_co_id'], $cms, $template->getCurrentLanguage());
											
											$url = $template->getPermaLink($template->getCurrentLanguage()) . '/' . $val['cms_per_link'] . '.html';
											
											$date = new PP_DateTime($values['art_pubDate']);
											
											?>
											
											<div class="item-container">
												<div class="item-image-wrapper">
													<div class="item-image" style="background-image: url(<?php echo $values['art_overviewPhoto']; ?>);">
														&nbsp;
													</div>
													<div class="hover-overlay"></div>
													<a href="<?php echo $url; ?>" class="item-button">&xrarr;</a>
												</div>
												
												<p class="item-title"><a href="<?php echo $url; ?>"><?php echo $values['art_title']; ?></a></p>
											
											</div>
											
											<?php
										}
									}
								}
								
								?>

							</div>

						</section>

						<div class="paging-wrapper">
	
							<?php
							
							$pageStart = $startFrom + 1;
							$pageEnd = $endAt;
							
							if ($pageEnd > $totalActueel)
								$pageEnd = $totalActueel;
							
							?>
	
							<div class="text-wrapper">
								Resultaten <span class="bold"><?php echo $pageStart; ?> t/m <?php echo $pageEnd; ?></span> van <span class="bold"><?php echo $totalActueel; ?></span>
							</div>
							
							<div class="numbers-wrapper">
								<span><a href="#">1</a></span>
								<span class="active">2</span>
								<span><a href="#">3</a></span>
								<span class="no-link">...</span>
								<span><a href="#">9</a></span>
								
								<div class="next"><a href="#">&xrarr;</a></div>
							</div>
						
							
						</div>
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
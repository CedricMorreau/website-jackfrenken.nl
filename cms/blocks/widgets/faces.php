<?php

// Fetch data of the widget
$widgetData = array();

$widgetData = unserialize($wVal['mod_pd_extraData']);

$newArray = array();

if (count($widgetData) > 0) {
	
	// Find article
	$article = $this->cms['database']->prepare("SELECT * FROM `tbl_mod_articleContent` WHERE `mod_co_id`=? LIMIT 1", "i", array($widgetData[0]));
	
	if (count($article) > 0) {
		
		// Fetch all values
		$articleData = $this->cms['database']->prepare("SELECT * FROM `tbl_mod_articleContentValues` LEFT JOIN `tbl_mod_articleAttributeValues` ON `mod_cv_attributeValueId`=`mod_av_id` LEFT JOIN `tbl_mod_articleAttributes` ON `mod_aa_id`=`mod_cv_attributeId` WHERE `mod_cv_articleId`=?  AND `mod_cv_languageId` IN (0, ?)", "ii", array($article[0]['mod_co_id'], $this->getCurrentLanguage()));
		
		if (count($articleData) > 0) {
			
			$dataArray = $this->getArticleValues($article[0]['mod_co_id']);
		}
		
		if (isset($dataArray['faces_status'][58])) {
		
			$url = $this->buildDynamicUrl($dataArray['faces_personalSite'], 2);
			
			// Get URL
			$articleUrl = $this->getArticleUrl($article[0]['mod_co_id']);
			
			if ($articleUrl === false)
				$articleUrl = $dynamicRoot . '404.html';
			
			?>
			
			<div class="article-container-sidebar-foto">
				<a href="<?php echo $articleUrl; ?>">
				
					<img src="<?php echo $dataArray['faces_overviewPhoto']; ?>" class="foto" alt="foto">
					<img class="arrow-up" src="/resources/arrow-up.svg" alt="arrow-up">
	
				</a>
	
				<a href="<?php echo $articleUrl; ?>" class="person-link">Gezicht van Te Gek!? &rsaquo;</a>
				<h3><?php echo $dataArray['faces_quote']; ?></h3>
			</div>
			
			<?php
		
		}
	}
}
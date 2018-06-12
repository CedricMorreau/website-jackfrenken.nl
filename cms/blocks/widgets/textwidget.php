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
		
		if (isset($dataArray['widget_status'][7])) {
		
		$url = $this->buildDynamicUrl($dataArray['widget_landingPage'], 2);
		
		// Determine color
		$color = 'blue';
		
		if (count($dataArray['widget_color']) > 0) {
			
			$colorValue = array_values($dataArray['widget_color'])[0];
			
			switch (strtolower($colorValue)) {
				
				case 'blauw':
					
					$color = 'blue';
					
					break;
					
				case 'groen':
					
					$color = 'green';
					
					break;
					
				case 'paars':
					
					$color = 'purple';
					
					break;
					
				case 'rood':
					
					$color = 'red';
					
					break;
					
				default:
					
					$color = 'blue';
			}
		}
		
		?>
					
<div class="article-container-sidebar-problem <?php echo $color; ?>">

	<img src="/resources/arrow-down.svg" alt="arrow-down">
	<h3>
		<?php echo $dataArray['widget_title']; ?>
	</h3>
	
	<?php
	
	if ($url != '') {
	
	?>
	
	<a href="<?php echo $url; ?>">
		<?php echo $dataArray['widget_text']; ?>
	</a>
	
	<?php } else { ?>
	
	<?php echo $dataArray['widget_text']; ?>
	
	<?php } ?>
	
</div>
					
		<?php
		}
	}
}
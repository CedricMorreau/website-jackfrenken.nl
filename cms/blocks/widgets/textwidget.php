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
		
		if (isset($dataArray['widget_status'][3])) {
		
		$url = $this->buildDynamicUrl($dataArray['widget_landingPage'], 2);

		$color_class = '';
		$use_key = key($dataArray['widget_color'] ?? [0 => 0]);

		switch ($use_key) {

			case 14:

				$color_class = ' ghost-white';

				break;
		}
		
		?>
		
<div class="sidebar-widget<?php echo $color_class; ?>">

	<?php
	
	if ($url != '') {
		
	?>

	<h3>
		<a href="<?php echo $url; ?>"><?php echo $dataArray['widget_visibleTitle'] ?? $dataArray['widget_title']; ?></a>
	</h3>
	<p>
		<a href="<?php echo $url; ?>"><?php echo $dataArray['widget_text']; ?> &rsaquo;</a>
	</p>
	
	<?php } else { ?>
	
	<h3>
		<?php echo $dataArray['widget_visibleTitle'] ?? $dataArray['widget_title']; ?>
	</h3>
	<p>
		<?php echo $dataArray['widget_text']; ?>
	</p>
	
	<?php } ?>
</div>
					
		<?php
		}
	}
}
<?php

$actueel = $cms['database']->prepare("SELECT *, (SELECT `mod_cv_valueDate` FROM `tbl_mod_articleContentValues` WHERE `mod_cv_articleId`=`mod_co_id` AND `mod_cv_attributeId`=1) as orderDate FROM `tbl_mod_articleContent` LEFT JOIN `tbl_cms_permaLinks` ON `cms_per_tableId`=`mod_co_id` WHERE `mod_co_articleTypeId`=1 AND EXISTS(SELECT * FROM `tbl_mod_articleContentValues` WHERE `mod_cv_articleId`=`mod_co_id` AND `mod_cv_attributeId`=8 AND `mod_cv_attributeValueId`=1 AND `mod_cv_articleId`=`mod_co_id`) AND `cms_per_tableName`='tbl_mod_articleContent' AND (SELECT `mod_cv_valueDate` FROM `tbl_mod_articleContentValues` WHERE `mod_cv_articleId`=`mod_co_id` AND `mod_cv_attributeId`=1)<NOW() GROUP BY `mod_co_id` ORDER BY `orderDate` LIMIT 2");

if (count($actueel) > 0) {

?>

<section class="items-wrapper">
	<div class="items-container">
	
		<?php
		
		foreach ($actueel as $key => $val) {
			
			$values = Content::getArticleValues($val['mod_co_id'], $cms, $template->getCurrentLanguage());
			
			$url = $template->getPermaLink($template->getCurrentLanguage()) . '/' . $val['cms_per_link'];
			
			$date = new PP_DateTime($values['art_pubDate']);
			
			?>
			
		<div class="item-container">
			<div class="item-image-wrapper">
				<div class="item-image" style="background-image: url(<?php echo $values['art_overviewPhoto']; ?>);">
					&nbsp;
				</div>
				<div class="hover-overlay">
					<a href="<?php echo $url; ?>"></a>
				</div>
				<a href="<?php echo $url; ?>" class="item-button">&xrarr;</a>
			</div>
			
			<p class="item-title"><a href="<?php echo $url; ?>"><?php echo $values['art_title']; ?></a></p>

		</div>
			
			<?php
		}
		
		?>
		
	</div>
</section>

<?php } ?>
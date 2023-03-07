<?php

$actueel = $cms['database']->prepare("SELECT *, (SELECT `mod_cv_valueDate` FROM `tbl_mod_articleContentValues` WHERE `mod_cv_articleId`=`mod_co_id` AND `mod_cv_attributeId`=1) as orderDate FROM `tbl_mod_articleContent` LEFT JOIN `tbl_cms_permaLinks` ON `cms_per_tableId`=`mod_co_id` WHERE `mod_co_articleTypeId`=1 AND EXISTS(SELECT * FROM `tbl_mod_articleContentValues` WHERE `mod_cv_articleId`=`mod_co_id` AND `mod_cv_attributeId`=8 AND `mod_cv_attributeValueId`=1 AND `mod_cv_articleId`=`mod_co_id`) AND `cms_per_tableName`='tbl_mod_articleContent' AND (SELECT `mod_cv_valueDate` FROM `tbl_mod_articleContentValues` WHERE `mod_cv_articleId`=`mod_co_id` AND `mod_cv_attributeId`=1)<NOW() GROUP BY `mod_co_id` ORDER BY `orderDate` DESC LIMIT 2");

if (count($actueel) > 0) {

?>

<section class="items-wrapper content-wrapper">
	<div class="items-container">
	
		<?php
		
		foreach ($actueel as $key => $val) {
			
			$values = Content::getArticleValues($val['mod_co_id'], $cms, $template->getCurrentLanguage());
			
			$url = $template->getArticleUrl($val['mod_co_id']);
			
			$date = new PP_DateTime($values['art_pubDate']);
			
			?>
			
		<div class="item-container">
			<div class="item-image-wrapper">
				<div class="item-image" style="background-image: url(<?php echo $values['art_overviewPhoto']; ?>);" title="<?php echo $values['art_title']; ?>" alt="<?php echo $values['art_title']; ?>">
					&nbsp;
				</div>
				<div class="hover-overlay">
					<a href="<?php echo $url; ?>" title="<?php echo $values['art_title']; ?>" alt="<?php echo $values['art_title']; ?>"></a>
				</div>
				
			</div>
			
			<div class="item-image-content-wrapper">
				<p class="item-title">
					<a href="<?php echo $url; ?>" title="<?php echo $values['art_title']; ?>"><?php echo $values['art_title']; ?></a>
				</p>

				<div class="item-content-excerpt">
					Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque eget porttitor velit. Integer et semper metus. Proin dignissim orci sodales, eleifend nunc in, porta est. Aliquam velit urna, accumsan eget turpis quis, pretium aliquet odio. Cras semper vestibulum felis non maximus.
				</div>

				<a href="<?php echo $url; ?>" class="cta-button item-button" title="Lees meer">Lees verder</a> 
				<a href="<?php echo $url; ?>" class="cta-button item-button ghost" title="Meer artikelen">Meer artikelen</a>
			</div>

		</div>
			
			<?php
		}
		
		?>
		
	</div>
</section>

<?php } ?>
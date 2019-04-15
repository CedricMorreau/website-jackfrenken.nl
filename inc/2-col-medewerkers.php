<div class="column-content">
	
	<div class="column-sidebar">
	
		<?php echo $template->cmsData('page][navigation/2/subnav/' . $template->findHighestParent() . '/active/' . $template->getPageId()); ?>
		
		<?php echo $template->cmsData('page][section/widgets'); ?>

	</div>

	<div class="column-content">

		<?php
		
		$textBlock = trim($template->cmsData('page][section/content'));
		$optTitle = $template->getData('optionalTitle', 10);
		
		?>

		<div class="medewerkers-wrapper">

			<?php if (!empty($optTitle)) { ?>
		
			<h2><?php echo $optTitle; ?></h2>
			
			<?php } ?>

			<?php echo $textBlock; ?>




			<?php
            	$medewerkers = $cms['database']->prepare("SELECT *,

                    -- sorteren op 'naam' 
                    (SELECT `mod_cv_value` 
                        FROM `tbl_mod_articleContentValues` 
                        WHERE `mod_cv_articleId`=`mod_co_id` 
                        AND `mod_cv_attributeId`=21) 
                    as orderby 

                    -- bepalen om welk articleType het gaat: medewerkers
                    FROM `tbl_mod_articleContent` 
                    LEFT JOIN `tbl_cms_permaLinks` 
                    ON `cms_per_tableId`=`mod_co_id` 
                    WHERE `mod_co_articleTypeId`=4 

                    -- status moet 'online' zijn
                    AND EXISTS(
                        SELECT * 
                        FROM `tbl_mod_articleContentValues` 
                        WHERE `mod_cv_articleId`=`mod_co_id` 
                        AND `mod_cv_attributeId`=23 
                        AND `mod_cv_attributeValueId`=9 
                        AND `mod_cv_articleId`=`mod_co_id`)

                    AND `cms_per_tableName`='tbl_mod_articleContent' 
                        " . " GROUP BY `mod_co_id` ORDER BY `orderby` ASC");

            ?>




			<section id="flexbox-gallery">
	            <div class="limiter">
	                <div class="parent">



						<?php
			            	if (count($medewerkers) > 0) {
				                foreach ($medewerkers as $key => $val) {
				                    $values = Content::getArticleValues($val['mod_co_id'], $cms, $template->getCurrentLanguage());
				        ?>

				                    <div class="child">
				                        <div class="pic" style="background-image: url('<?php echo $values['mw_foto'] ?>')">
				                        	<span class="mw-naam"><?php echo $values['mw_naam'] ?></span>
				                        	<span class="mw-functie"><?php echo $values['mw_functie'] ?></span>
				                        </div>
				                    </div>

						<?php
				                }
				                //Core::vd($values);
				                //Core::vd($medewerkers);
				            }
						?>

				    </div>
	            </div>
	        </section>







		</div>
	</div>
</div>

<div class="column-content">
	
	<div class="column-sidebar">
		
		<?php echo $template->cmsData('page][section/widgets'); ?>

	</div>

	<div class="column-content">

		<?php
		
		$textBlock = trim($template->cmsData('page][section/content'));
		
		?>

		<div class="medewerkers-wrapper">

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
								</div>

								<div class="info">
									<div class="mw-naam"><?php echo $values['mw_naam'] ?></div>
									<div class="mw-functie"><?php echo $values['mw_functie'] ?></div>
									<div class="divider"></div>
									<div class="social-icons">
										<?php if (!empty($values['mw_email'])) { ?>
										<a href="mailto:<?php echo $values['mw_email']; ?>" class="icon mail" target="_blank">
											<?php include($documentRoot . "resources/icon-contact-email.svg"); ?>
										</a>
										<?php } ?>
										<?php if (!empty($values['mw_linkedin'])) { ?>
										<a href="<?php echo $values['mw_linkedin']; ?>" class="icon linkedin" target="_blank">
											<?php include($documentRoot . "resources/icon-contact-linkedin.svg"); ?>
										</a>
										<?php } ?>
									</div>
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

<section class="partners-banner-wrapper">
	<div class="column-content">
		<div class="column-sidebar"><!-- spacer --></div>
		<div class="column-content flex column">
			<h4>Partners</h4>
			<div class="partner-logos-wrapper">
				<a href="https://www.nvm.nl/" class="partner-logo" target="_blank">
					<img src="/resources/partner-logo-funda_powered_by_nvm.png" alt="Funda">
				</a>
				<a href="https://www.funda.nl" class="partner-logo" target="_blank">
					<img src="/resources/partner-logo-nvm.png" alt="Funda">
				</a>
			</div>
		</div>
		
	</div>
</section>
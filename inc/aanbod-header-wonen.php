<section class="column-header">
	<div class="content-wrapper">	

		<div class="header-title-wrapper">
			<div class="header-title">
				<h1><span class="city"><?php echo $val['objectDetails_Adres_NL_Woonplaats']; ?></span><br>
				<?php echo obj_generateAddress($val['objectDetails_Adres_NL_Straatnaam'], $val['objectDetails_Adres_NL_Huisnummer'], $val['objectDetails_Adres_NL_HuisnummerToevoeging']); ?></h1>
				
				<?php
				
				if (!empty($val['object_Web_OpenHuis_Vanaf']) && !empty($val['object_Web_OpenHuis_Tot']) && $val['object_Web_OpenHuis_Vanaf'] != '0000-00-00 00:00:00' && $val['object_Web_OpenHuis_Tot'] != '0000-00-00 00:00:00') {
					
					$toTimeFrom = strtotime($val['object_Web_OpenHuis_Vanaf']);
					$toTimeTill = strtotime($val['object_Web_OpenHuis_Tot']);
					
					if (time() < $toTimeTill) {
						
						// Open huis!
						$shownDate = translateDay(date('l. j F', $toTimeFrom), 'short');
						$shownTime = date('H:i', $toTimeFrom) . ' tot ' . date('H:i', $toTimeTill);
						
						echo '<p class="openhouse"><strong>Open Huis!</strong> <i>' . $shownDate . ' - ' . $shownTime . ' uur</i></p>';
					}
				}
				
				?>
				
				<p class="title-price"><?php echo $priceText; ?></p>

				<?php if ($val['objectDetails_Koop_Koopprijs'] > 1) { ?>
				
				<a href="<?php echo $template->findPermalink(88, 1); ?>" class="title-link">Hypotheekmogelijkheden &rsaquo;</a>

				<?php } ?>
			</div>
		</div>
		
		<div class="content-image" style="background: none;">
			<div class="image-container">

			<?php

			$is_open_house = false;

			if (!empty($val['object_Web_OpenHuis_Vanaf']) && !empty($val['object_Web_OpenHuis_Tot']) && $val['object_Web_OpenHuis_Vanaf'] != '0000-00-00 00:00:00' && $val['object_Web_OpenHuis_Tot'] != '0000-00-00 00:00:00') {

				$toTimeFrom = strtotime($val['object_Web_OpenHuis_Vanaf']);
				$toTimeTill = strtotime($val['object_Web_OpenHuis_Tot']);

				if (time() < $toTimeTill) {

					$is_open_house = true;

					// Open huis!
					$shownDate = translateDay(date('l. j F', $toTimeFrom), 'short');
					$shownTime = date('H:i', $toTimeFrom) . ' tot ' . date('H:i', $toTimeTill);

					echo '<div class="image-label status-openhouse"><span>Open Huis!</span> <i>' . $shownDate . ' - ' . $shownTime . ' uur</i></div>';
				}
			}

			if (!$is_open_house) {

				if (strtolower($val['objectDetails_StatusBeschikbaarheid_Status']) == 'verkocht' || strtolower($val['objectDetails_StatusBeschikbaarheid_Status']) == 'verhuurd' || strtolower($val['objectDetails_StatusBeschikbaarheid_Status']) == 'onder optie') {

					echo '<div class="image-label status-sold">' . $val['objectDetails_StatusBeschikbaarheid_Status'] . '</div>';
				}
				elseif (strtolower($val['objectDetails_StatusBeschikbaarheid_Status']) == 'verkocht onder voorbehoud' || strtolower($val['objectDetails_StatusBeschikbaarheid_Status']) == 'verhuurd onder voorbehoud') {

					echo '<div class="image-label status-soldsubject">' . $val['objectDetails_StatusBeschikbaarheid_Status'] . '</div>';
				}
				elseif (isset($val['ood_alternativeStatus']) && !empty($val['ood_alternativeStatus'])) {

			?>

			<div class="image-label status-new"><?php echo $val['ood_alternativeStatus']; ?></div>

				<?php } ?>
			<?php } ?>

				<?php if (isset($mediaList[0])): ?>

					<div id="royal-slider" class="royalSlider rsMinW visibleNearby">
					
						<?php
						
						$i = 0;
						
						foreach ($mediaList as $key => $media) {
							
							$fullImage = $dynamicRoot . 'og_media/wonen_' . $val['object_NVMVestigingNR'] . '_' . $val['object_ObjectTiaraID']. '/' . $media['bestandsnaam'];
							$tnImage = $dynamicRoot . 'og_media/wonen_' . $val['object_NVMVestigingNR'] . '_' . $val['object_ObjectTiaraID']. '/' . $media['bestandsnaam_medium'];
							
							echo '<a class="rsImg" href="' . $tnImage . '" data-rsTmb="' . $tnImage . '" data-rsBigImg="' . $fullImage . '">' . $val['objectDetails_Adres_NL_Woonplaats'] . ' - ' . obj_generateAddress($val['objectDetails_Adres_NL_Straatnaam'], $val['objectDetails_Adres_NL_Huisnummer'], $val['objectDetails_Adres_NL_HuisnummerToevoeging']) . '</a>' . PHP_EOL;
							
							$i++;
						}
						
						?>
					
					</div>

				<?php endif; ?>
			</div>	

			<div class="breadcrumbs-wrapper">

				<?php

				$extraCrumbs = array($val['objectDetails_Adres_NL_Woonplaats'] . ' - ' . obj_generateAddress($val['objectDetails_Adres_NL_Straatnaam'], $val['objectDetails_Adres_NL_Huisnummer'], $val['objectDetails_Adres_NL_HuisnummerToevoeging']) => 1);

				$ignoreCrumb = 1;

				$use_id = $template->getPageData('id');

				if ($val['objectDetails_Koop_Koopprijs'] < 1)
					$use_id = 39;
							
				$breadCrumbs = new Breadcrumbs($use_id, $template->getPageData('nav'), $cms['database'], $template, $extraCrumbs, $ignoreCrumb);

				echo $breadCrumbs->displayCrumbs();

				?>

			</div>

		</div>

	</div>
</section>
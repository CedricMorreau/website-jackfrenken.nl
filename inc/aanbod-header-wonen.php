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

				if ($val['objectDetails_StatusBeschikbaarheid_Status'] == 'Verkocht' || $val['objectDetails_StatusBeschikbaarheid_Status'] == 'Verkocht onder voorbehoud') {

					echo '<span class="label verkocht">' . $val['objectDetails_StatusBeschikbaarheid_Status'] . '</span>';
				}
				elseif ($val['objectDetails_StatusBeschikbaarheid_Status'] == 'Verhuurd' || $val['objectDetails_StatusBeschikbaarheid_Status'] == 'Verhuurd onder voorbehoud') {

					echo '<span class="label verkocht">' . $val['objectDetails_StatusBeschikbaarheid_Status'] . '</span>';
				}
				
				?>
				
				<p class="title-price"><?php echo $priceText; ?></p>
				
				<a href="<?php echo $template->findPermalink(65, 1); ?>" class="title-link">Bereken hier uw hypotheeklasten &rsaquo;</a>
			</div>
		</div>
		
		<div class="content-image" style="background: none;">
			<div class="image-container">

				<div class="image-label status-soldsubject">Verkocht onder voorbehoud</div>


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
							
				$breadCrumbs = new Breadcrumbs($template->getPageData('id'), $template->getPageData('nav'), $cms['database'], $template, $extraCrumbs, $ignoreCrumb);

				echo $breadCrumbs->displayCrumbs();

				?>

			</div>

		</div>

	</div>
</section>
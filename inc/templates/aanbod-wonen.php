<?php

$priceText = obj_showPrice($val['objectDetails_Koop_Prijsvoorvoegsel'], $val['objectDetails_Koop_Koopprijs'], $val['objectDetails_Koop_KoopConditie'], $val['objectDetails_Huur_Huurprijs'], $val['objectDetails_Huur_HuurConditie']);
	
if (!is_null($val['cms_per_link']))
	$href = $dynamicRoot . $val['cms_per_link'];
else
	$href = $dynamicRoot . 'error/404';

$image = (!is_null($val['mainImage'])) ? $dynamicRoot . 'og_media/wonen_' . $val['object_NVMVestigingNR'] . '_' . $val['object_ObjectTiaraID']. '/' . $val['mainImage']: $dynamicRoot . 'resources/aanbod-no-image.jpg';

?>

<div class="item-wrapper">
	<a href="<?php echo $href; ?>">
		<div class="item-description">
			<div class="item-overlay-wrapper">
				
				<p class="item-title">
					<?php echo $val['objectDetails_Adres_NL_Woonplaats']; ?><br>
					<?php echo obj_generateAddress($val['objectDetails_Adres_NL_Straatnaam'], $val['objectDetails_Adres_NL_Huisnummer'], $val['objectDetails_Adres_NL_HuisnummerToevoeging']); ?>
				</p>
				
				<p class="item-subtitle">
					<?php echo $priceText; ?>
				</p>


				<?php if (!empty($val['wonen_Woonhuis_SoortWoning'])) { ?>
				<p><?php echo ucfirst($val['wonen_Woonhuis_SoortWoning']); ?>, <?php echo ucfirst($val['wonen_Woonhuis_TypeWoning']); ?></p>
				<?php } ?>

				<?php if (!is_null($val['wonen_WonenDetails_GebruiksoppervlakteWoonfunctie']) && $val['wonen_WonenDetails_GebruiksoppervlakteWoonfunctie'] > 0) { ?>
				<p>Woonoppervlak: <?php echo number_format($val['wonen_WonenDetails_GebruiksoppervlakteWoonfunctie'], 0, ",", "."); ?> m<sup>2</sup></p>
				<?php } ?>
				<?php if (!is_null($val['wonen_WonenDetails_PerceelOppervlakte']) && $val['wonen_WonenDetails_PerceelOppervlakte'] > 0) { ?>
				<p>Perceel: <?php echo number_format($val['wonen_WonenDetails_PerceelOppervlakte'], 0, ",", "."); ?> m<sup>2</sup></p>
				<?php } ?>
				<?php if (!is_null($val['wonen_WonenDetails_Bouwjaar_JaarOmschrijving_Jaar']) && $val['wonen_WonenDetails_Bouwjaar_JaarOmschrijving_Jaar'] > 0) { ?>
				<p>Bouwjaar: <?php echo $val['wonen_WonenDetails_Bouwjaar_JaarOmschrijving_Jaar']; ?></p>
				<?php } ?>

				<?php if (!empty($val['objectDetails_Energielabel_Energieklasse'])) { ?>

				<p>Energielabel: 
					<span class="energy-label energy-label-<?php echo strtolower($val['objectDetails_Energielabel_Energieklasse']); ?>">
						<?php echo $val['objectDetails_Energielabel_Energieklasse']; ?>
					</span>
				</p>

				<?php } ?>
			</div>
			
		</div>
	</a>
	<div class="item-image">
		<a href="<?php echo $href; ?>">
			<?php if (!empty($image)) { ?>
			<img src="<?php echo $image; ?>" alt="<?php echo $val['objectDetails_Adres_NL_Woonplaats']; ?>
					<?php echo obj_generateAddress($val['objectDetails_Adres_NL_Straatnaam'], $val['objectDetails_Adres_NL_Huisnummer'], $val['objectDetails_Adres_NL_HuisnummerToevoeging']); ?>" title="<?php echo $val['objectDetails_Adres_NL_Woonplaats']; ?>
					<?php echo obj_generateAddress($val['objectDetails_Adres_NL_Straatnaam'], $val['objectDetails_Adres_NL_Huisnummer'], $val['objectDetails_Adres_NL_HuisnummerToevoeging']); ?>">
			<?php } ?>
			
			<?php

			if (!empty($val['object_Web_OpenHuis_Vanaf']) && !empty($val['object_Web_OpenHuis_Tot']) && $val['object_Web_OpenHuis_Vanaf'] != '0000-00-00 00:00:00' && $val['object_Web_OpenHuis_Tot'] != '0000-00-00 00:00:00') {

				$toTimeFrom = strtotime($val['object_Web_OpenHuis_Vanaf']);
				$toTimeTill = strtotime($val['object_Web_OpenHuis_Tot']);

				if (time() < $toTimeTill) {

					// Open huis!
					$shownDate = translateDay(date('l. j F', $toTimeFrom), 'short');
				    $shownTime = date('H:i', $toTimeFrom) . ' tot ' . date('H:i', $toTimeTill);

					echo '<div class="image-label status-openhouse"><span>Open Huis!</span> <i>' . $shownDate . ' - ' . $shownTime . ' uur</i></div>';
				}
			}
			elseif (strtolower($val['objectDetails_StatusBeschikbaarheid_Status']) == 'verkocht' || strtolower($val['objectDetails_StatusBeschikbaarheid_Status']) == 'verhuurd' || strtolower($val['objectDetails_StatusBeschikbaarheid_Status']) == 'onder optie') {

				echo '<div class="image-label status-sold">' . $val['objectDetails_StatusBeschikbaarheid_Status'] . '</div>';
			}
			elseif (strtolower($val['objectDetails_StatusBeschikbaarheid_Status']) == 'verkocht onder voorbehoud' || strtolower($val['objectDetails_StatusBeschikbaarheid_Status']) == 'verhuurd onder voorbehoud') {

				echo '<div class="image-label status-soldsubject">' . $val['objectDetails_StatusBeschikbaarheid_Status'] . '</div>';
			}
			elseif (isset($val['ood_alternativeStatus']) && !empty($val['ood_alternativeStatus'])) {

			?>

			<div class="image-label status-new"><?php echo $val['ood_alternativeStatus']; ?></div>

			<?php } ?>
			
			<div class="hover-overlay" title="<?php echo $val['objectDetails_Adres_NL_Woonplaats']; ?> - <?php echo obj_generateAddress($val['objectDetails_Adres_NL_Straatnaam'], $val['objectDetails_Adres_NL_Huisnummer'], $val['objectDetails_Adres_NL_HuisnummerToevoeging']); ?>">
			</div>
		</a>
	</div>


</div>
<?php

$priceText = obj_showPrice($val['objectDetails_Koop_KoopConditie'], $val['objectDetails_Koop_PrijsSpecificatie_Prijs'], $val['objectDetails_Koop_KoopConditie'], $val['objectDetails_Huur_PrijsSpecificatie_Prijs'], $val['objectDetails_Huur_HuurConditie']);

if (!empty($val['objectDetails_Bouwgrond_Bebouwingsmogelijkheid']))
	$priceText = str_replace(' k.k.', ' per m<sup>2</sup> excl. btw k.k.', $priceText);
	
if (!is_null($val['cms_per_link']))
	$href = $dynamicRoot . $val['cms_per_link'];
else
	$href = $dynamicRoot . 'error/404';

$image = (!is_null($val['mainImage'])) ? $dynamicRoot . 'og_media/bog_' . $val['object_NVMVestigingNR'] . '_' . $val['object_ObjectTiaraID']. '/' . $val['mainImage']: $dynamicRoot . 'resources/aanbod-no-image.jpg';

?>

<div class="item-wrapper">
	<a href="<?php echo $href; ?>">
		<div class="item-description">
			<div class="item-overlay-wrapper">
				
				<p class="item-title">
					<span class="uppercase"><?php echo $val['objectDetails_Adres_Woonplaats']; ?></span><br>
					<?php echo obj_generateAddress($val['objectDetails_Adres_Straatnaam'], $val['objectDetails_Adres_Huisnummer'], $val['objectDetails_Adres_HuisnummerToevoeging']); ?>
				</p>
				
				<p class="item-subtitle">
					<?php echo $priceText; ?>
				</p>

				<p class="item-type">

					<?php echo $val['objectDetails_Bestemming_Hoofdbestemming']; ?>

				</p>

				<table>

					<tbody>

						<?php

						if (!empty($val['objectDetails_Lokatie_Liggingen'])) { 

							$process = explode(',', $val['objectDetails_Lokatie_Liggingen']);

							foreach ($process as $s_key => $s_val) {

								$process[$s_key] = str_replace(['[', ']'], ['', ''], $s_val);

								if ($process[$s_key] == 'Overig')
									unset($process[$s_key]);
							}

							$process = implode(', ', $process);
							
						?>

						<tr>
							<td>Ligging:</td>
							<td><?php echo $process; ?></td>
						</tr>

						<?php } ?>

						<?php if (!empty($val['objectDetails_Energieklasse'])) { ?>

						<tr>
							<td>Energielabel:</td>
							<td><span class="energy-label energy-label-<?php echo strtolower($val['objectDetails_Energieklasse']); ?>">
							<?php echo str_replace(['P', '_'], ['+', ''], $val['objectDetails_Energieklasse']); ?>
							</span></td>
						</tr>

						<?php } ?>

					</tbody>

				</table>
				
			</div>
		</div>
	</a>
	<div class="item-image">
		<a href="<?php echo $href; ?>">
			<?php if (!empty($image)) { ?>
			<img src="<?php echo $image; ?>" alt="<?php echo $val['objectDetails_Adres_Woonplaats']; ?> - <?php echo obj_generateAddress($val['objectDetails_Adres_Straatnaam'], $val['objectDetails_Adres_Huisnummer'], $val['objectDetails_Adres_HuisnummerToevoeging']); ?>" title="<?php echo $val['objectDetails_Adres_Woonplaats']; ?> - <?php echo obj_generateAddress($val['objectDetails_Adres_Straatnaam'], $val['objectDetails_Adres_Huisnummer'], $val['objectDetails_Adres_HuisnummerToevoeging']); ?>">
			<?php } ?>
			
			<?php

			if (strtolower($val['objectDetails_Status_StatusType']) == 'verkocht' || strtolower($val['objectDetails_Status_StatusType']) == 'verhuurd' || strtolower($val['objectDetails_Status_StatusType']) == 'onder optie') {

				echo '<div class="image-label status-sold">' . $val['objectDetails_Status_StatusType'] . '</div>';
			}
			elseif (strtolower($val['objectDetails_Status_StatusType']) == 'verkocht onder voorbehoud' || strtolower($val['objectDetails_Status_StatusType']) == 'verhuurd onder voorbehoud') {

				echo '<div class="image-label status-soldsubject">' . $val['objectDetails_Status_StatusType'] . '</div>';
			}
			elseif (isset($val['ood_alternativeStatus']) && !empty($val['ood_alternativeStatus'])) {

			?>

			<div class="image-label status-new"><?php echo $val['ood_alternativeStatus']; ?></div>

			<?php } ?>
			
			<div class="hover-overlay" title="<?php echo $val['objectDetails_Adres_Woonplaats']; ?> - <?php echo obj_generateAddress($val['objectDetails_Adres_Straatnaam'], $val['objectDetails_Adres_Huisnummer'], $val['objectDetails_Adres_HuisnummerToevoeging']); ?>"></div>
		</a>
	</div>


</div>
<?php

$priceText = obj_showPrice($val['objectDetails_Koop_KoopConditie'], $val['objectDetails_Koop_PrijsSpecificatie_Prijs'], $val['objectDetails_Koop_KoopConditie'], $val['objectDetails_Huur_PrijsSpecificatie_Prijs'], $val['objectDetails_Huur_HuurConditie']);
	
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
					<?php echo $val['objectDetails_Adres_Woonplaats']; ?>
				</p>
				
				<p class="item-subtitle">
					Vraagprijs <?php echo $priceText; ?>
				</p>

				<p class="item-bold-title">
					<?php echo obj_generateAddress($val['objectDetails_Adres_Straatnaam'], $val['objectDetails_Adres_Huisnummer'], $val['objectDetails_Adres_HuisnummerToevoeging']); ?>
				</p>
				
			</div>
		</div>
	</a>
	<div class="item-image">
		<a href="<?php echo $href; ?>">
			<?php if (!empty($image)) { ?>
			<img src="<?php echo $image; ?>">
			<?php } ?>
			
			<?php

			if (strtolower($val['objectDetails_Status_StatusType']) == 'verkocht' || strtolower($val['objectDetails_Status_StatusType']) == 'verhuurd') {

				echo '<div class="image-label status-sold">' . $val['objectDetails_Status_StatusType'] . '</div>';
			}
			elseif (strtolower($val['objectDetails_Status_StatusType']) == 'verkocht onder voorbehoud' || strtolower($val['objectDetails_Status_StatusType']) == 'verhuurd onder voorbehoud') {

				echo '<div class="image-label status-soldsubject">' . $val['objectDetails_Status_StatusType'] . '</div>';
			}
			elseif (isset($val['ood_alternativeStatus']) && !empty($val['ood_alternativeStatus'])) {

			?>

			<div class="image-label status-new"><?php echo $val['ood_alternativeStatus']; ?></div>

			<?php } ?>
			
			<div class="hover-overlay"></div>
		</a>
	</div>


</div>
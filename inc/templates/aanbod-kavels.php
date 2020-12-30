<?php

$priceText = obj_showPrice($val['koopprijsVoorvoegsel'], $val['koopprijs'], $val['koopconditie'], $val['huurprijs'], $val['huurconditie']);
	
if (!is_null($val['cms_per_link']))
	$href = $dynamicRoot . $val['cms_per_link'];
else
	$href = $dynamicRoot . 'error/404';

$image = (!is_null($val['mainImage'])) ? $dynamicRoot . 'og_media/' . $val['ogType'] . '_' . $val['NVMVestigingNR'] . '_' . $val['ObjectTiaraID']. '/' . $val['mainImage']: $dynamicRoot . 'resources/aanbod-no-image.jpg';

?>

<div class="item-wrapper">
	<a href="<?php echo $href; ?>">
		<div class="item-description">
			<div class="item-overlay-wrapper">
				
				<p class="item-title">
					<?php echo $val['woonplaats']; ?><br>
					<?php echo obj_generateAddress($val['straatnaam'], $val['huisnummer'], $val['huisnummerToevoeging']); ?>
				</p>
				
				<p class="item-subtitle">
					<?php echo $priceText; ?>
				</p>

				<p class="item-sub-subtitle">
					Bouwgrond
					
					<?php
					
					if ($val['projectnaam'] != '-') {
						
						echo '<br><br>Onderdeel van nieuwbouwproject ' . $val['projectnaam'];
					}
					
					?>
				</p>

				<?php if (!is_null($val['oppervlakte']) && $val['oppervlakte'] > 0) { ?>
				<p>Perceel: <?php echo number_format($val['oppervlakte'], 0, ",", "."); ?> m<sup>2</sup></p>
				<?php } ?>
			</div>
		</div>
	</a>
	<div class="item-image">
		<a href="<?php echo $href; ?>">
			<?php if (!empty($image)) { ?>
			<img src="<?php echo $image; ?>" alt="<?php echo $val['woonplaats']; ?> -
					<?php echo obj_generateAddress($val['straatnaam'], $val['huisnummer'], $val['huisnummerToevoeging']); ?>" title="<?php echo $val['woonplaats']; ?> - <?php echo obj_generateAddress($val['straatnaam'], $val['huisnummer'], $val['huisnummerToevoeging']); ?>">
			<?php } ?>
			
			<?php

			if (strtolower($val['status']) == 'verkocht' || strtolower($val['status']) == 'verhuurd' || strtolower($val['status']) == 'onder optie') {

				echo '<div class="image-label status-sold">' . $val['status'] . '</div>';
			}
			elseif (strtolower($val['status']) == 'verkocht onder voorbehoud' || strtolower($val['status']) == 'verhuurd onder voorbehoud') {

				echo '<div class="image-label status-soldsubject">' . $val['status'] . '</div>';
			}
			
			?>
			
			<div class="hover-overlay" title="<?php echo $val['woonplaats']; ?> - <?php echo obj_generateAddress($val['straatnaam'], $val['huisnummer'], $val['huisnummerToevoeging']); ?>"></div>
		</a>
	</div>


</div>
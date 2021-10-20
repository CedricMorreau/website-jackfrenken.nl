<?php

// Handle price for nieuwbouw
if ($val['project_ProjectDetails_KoopAanneemsom_Van'] > 2 && $val['project_ProjectDetails_KoopAanneemsom_TotEnMet'] > 2 && $val['project_ProjectDetails_KoopAanneemsom_Van'] != $val['project_ProjectDetails_KoopAanneemsom_TotEnMet']) {
	
	$priceText = 'V.a. ' . obj_generateCost($val['project_ProjectDetails_KoopAanneemsom_Van']) . ' tot ' . obj_generateCost($val['project_ProjectDetails_KoopAanneemsom_TotEnMet']) . ' v.o.n.';
}
elseif ($val['project_ProjectDetails_KoopAanneemsom_Van'] > 2) {
	
	$priceText = obj_generateCost($val['project_ProjectDetails_KoopAanneemsom_Van']) . ' v.o.n.';
}
elseif ($val['project_ProjectDetails_Huurprijs_Van'] > 2 && $val['project_ProjectDetails_Huurprijs_TotEnMet'] > 2) {
	
	$priceText = 'V.a. ' . obj_generateCost($val['project_ProjectDetails_Huurprijs_Van']) . ' tot ' . obj_generateCost($val['project_ProjectDetails_Huurprijs_TotEnMet']);
}
elseif ($val['project_ProjectDetails_Huurprijs_Van'] > 2) {
	
	$priceText = obj_generateCost($val['project_ProjectDetails_Huurprijs_Van']);
}
else {
	
	$priceText = 'Prijs op aanvraag';
}

$fetchImage = $cms['database']->prepare("SELECT `bestandsnaam_medium` FROM `tbl_OG_media` WHERE `id_OG_nieuwbouw_projecten`=? AND `media_status`=2 AND `media_groep`='Hoofdfoto'", "i", array($val['id']));

if (count($fetchImage) > 0) {
	
	$image = $dynamicRoot . 'og_media/nieuwbouw_' . $val['project_NVMVestigingNR'] . '_' . $val['project_ObjectTiaraID']. '/' . $fetchImage[0]['bestandsnaam_medium'];
}
else {
	
	$image = $dynamicRoot . 'resources/aanbod-no-image.jpg';
}
	
// Fetch permalink
$permaLink = $cms['database']->prepare("SELECT `cms_per_link` FROM `tbl_cms_permaLinks` WHERE `cms_per_tableName`='tbl_mod_pages' AND `cms_per_tableId`=40 AND `cms_per_moduleId`=? AND `cms_per_moduleExtra` IS NULL", "i", array($val['id']));

if (count($permaLink) > 0)
	$href = $dynamicRoot . $permaLink[0]['cms_per_link'];
else
	$href = $dynamicRoot . 'error/404';

?>

<div class="item-wrapper">
	<a href="<?php echo $href; ?>">
		<div class="item-description">
			<div class="item-overlay-wrapper">
				
				<p class="item-title">
					<span class="uppercase"><?php echo $val['project_ProjectDetails_Adres_Woonplaats']; ?></span>
				</p>
				
				<p class="item-subtitle">
					<?php echo $priceText; ?>
				</p>

				<p class="item-bold-title">
					<?php echo utf8_encode($val['project_ProjectDetails_Projectnaam']); ?>
				</p>
				
				<?php

				if ($val['project_ProjectDetails_Maten_Perceeloppervlakte_Van'] > 1) {
	
				?>

				<p>Perceeloppervlak: 
				<?php

					echo number_format($val['project_ProjectDetails_Maten_Perceeloppervlakte_Van'], 0, ",", ".");

					if ($val['project_ProjectDetails_Maten_Perceeloppervlakte_TotEnMet'] > 0 && $val['project_ProjectDetails_Maten_Perceeloppervlakte_Van'] != $val['project_ProjectDetails_Maten_Perceeloppervlakte_TotEnMet']) {

						echo ' t/m ' . number_format($val['project_ProjectDetails_Maten_Perceeloppervlakte_TotEnMet'], 0, ",", ".");
					}

					echo ' m<sup>2</sup>';

				?></p>
				
				<?php } ?>
				
				<?php

				if ($val['project_ProjectDetails_Maten_Woonoppervlakte_Van'] > 1) {

				?>
				<p>Woonoppervlak: 
				<?php

					echo number_format($val['project_ProjectDetails_Maten_Woonoppervlakte_Van'], 0, ",", ".");

					if ($val['project_ProjectDetails_Maten_Woonoppervlakte_TotEnMet'] > 0 && $val['project_ProjectDetails_Maten_Woonoppervlakte_TotEnMet'] != $val['project_ProjectDetails_Maten_Woonoppervlakte_Van']) {

						echo ' t/m ' . number_format($val['project_ProjectDetails_Maten_Woonoppervlakte_TotEnMet'], 0, ",", ".");
					}

					echo ' m<sup>2</sup>';

				?>
				</p>
				
				<?php

				if ($val['project_ProjectDetails_Maten_Inhoud_Van'] > 1) {

				?>

				<p>Inhoud: 
				<?php

					echo number_format($val['project_ProjectDetails_Maten_Inhoud_Van'], 0, ",", ".");

					if ($val['project_ProjectDetails_Maten_Inhoud_TotEnMet'] > 0 && $val['project_ProjectDetails_Maten_Inhoud_TotEnMet'] != $val['project_ProjectDetails_Maten_Inhoud_Van']) {

						echo ' t/m ' . number_format($val['project_ProjectDetails_Maten_Inhoud_TotEnMet'], 0, ",", ".");
					}

					echo ' m<sup>3</sup>';

				?>
				</p>
				<?php } ?>

				<?php } ?>
			</div>
		</div>
	</a>
	<div class="item-image">
		<a href="<?php echo $href; ?>">
			<?php if (!empty($image)) { ?>
			<img src="<?php echo $image; ?>" alt="<?php echo utf8_encode($val['project_ProjectDetails_Projectnaam']); ?> - <?php echo $val['project_ProjectDetails_Adres_Woonplaats']; ?>" title="<?php echo $val['project_ProjectDetails_Projectnaam']; ?> - <?php echo $val['project_ProjectDetails_Adres_Woonplaats']; ?>">
			
			<?php } ?>
			
			<?php

			if (isset($val['ood_alternativeStatus']) && !empty($val['ood_alternativeStatus'])) {

				echo '<div class="image-label status-new">' . $val['ood_alternativeStatus'] . '</div>';
			}
			
			if ($val['aantal_bouwNummers'] == $val['aantal_verkochtBouwNummers'] && $val['aantal_bouwNummers'] > 0) {
				
				$val['project_ProjectDetails_Status_ObjectStatus'] = 'Verkocht';
			}

			if ($val['project_ProjectDetails_Status_ObjectStatus'] == 'Verkocht' || $val['project_ProjectDetails_Status_ObjectStatus'] == 'Verkocht onder voorbehoud') {

				echo '<div class="image-label status-sold">' . $val['project_ProjectDetails_Status_ObjectStatus'] . '</div>';
			}

			if ($val['id'] == 44) {

			?>
			<div class="secondary-label">
				Financieel meer ruimte dankzij de groenverklaring en NOM
			</div>
			<?php } ?>
			<div class="hover-overlay" title="<?php echo $val['project_ProjectDetails_Projectnaam']; ?> - <?php echo $val['project_ProjectDetails_Adres_Woonplaats']; ?>"></div>
		</a>
	</div>


</div>
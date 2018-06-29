<div class="column-header">
	
	<div class="header-title-wrapper">
		<div class="header-title">
			<h1><?php echo $val['Adres_Woonplaats']; ?><br>
			<?php echo obj_generateAddress($val['Adres_Straatnaam'], $val['Adres_Huisnummer'], $val['Adres_HuisnummerToevoeging']); ?></h1>
			<p><?php echo $priceText; ?></p>
			<p>Onderdeel van nieuwbouwproject<br><a href="<?php echo $hrefProject; ?>"><?php echo $objectData[0]['project_ProjectDetails_Projectnaam']; ?></a></p>

			<a href="javascript:void(0);" onclick="javascript:$.scrollTo('#content', 1000)" class="scroll-down">Meer over deze woning &darr;</a>
		</div>
	</div>
	
	<?php
	
	if (count($mediaList) > 0) {
	
	?>

	<div class="content-image" style="background: none;">
		<div class="image-container">
		
			<div id="royal-slider" class="royalSlider rsMinW visibleNearby">
			
				<?php
				
				$i = 0;
				
				foreach ($mediaList as $key => $media) {
					
					$fullImage = $dynamicRoot . 'og_media/nieuwbouw__' . $val['bouwNummer_NVMVestigingNR'] . '_' . $val['bouwNummer_ObjectTiaraID']. '/' . $media['bestandsnaam'];
					$tnImage = $dynamicRoot . 'og_media/nieuwbouw__' . $val['bouwNummer_NVMVestigingNR'] . '_' . $val['bouwNummer_ObjectTiaraID']. '/' . $media['bestandsnaam_medium'];
					
					echo '<a class="rsImg" href="' . $fullImage . '" data-rsTmb="' . $tnImage . '" data-rsBigImg="' . $fullImage . '">' . $val['Adres_Woonplaats'] . ' - ' . obj_generateAddress($val['Adres_Straatnaam'], $val['Adres_Huisnummer'], $val['Adres_HuisnummerToevoeging']) . '</a>' . PHP_EOL;
					
					$i++;
				}
				
				?>
			
			</div>
		
		</div>
		
	</div>
	
	<?php } ?>

</div>
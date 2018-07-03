<div class="column-header">
	
	<div class="header-title-wrapper">
		<div class="header-title">
			<h1><?php echo $val['objectDetails_Adres_Woonplaats']; ?><br>
			<?php echo obj_generateAddress($val['objectDetails_Adres_Straatnaam'], $val['objectDetails_Adres_Huisnummer'], $val['objectDetails_Adres_HuisnummerToevoeging']); ?></h1>
			<p><?php echo $priceText; ?></p>

			<a href="javascript:void(0);" onclick="javascript:$.scrollTo('#content', 1000)" class="scroll-down">Meer informatie &nbsp; &darr;</a>
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
					
					$fullImage = $dynamicRoot . 'og_media/bog_' . $val['object_NVMVestigingNR'] . '_' . $val['object_ObjectTiaraID']. '/' . $media['bestandsnaam'];
					$tnImage = $dynamicRoot . 'og_media/bog_' . $val['object_NVMVestigingNR'] . '_' . $val['object_ObjectTiaraID']. '/' . $media['bestandsnaam_medium'];
					
					echo '<a class="rsImg" href="' . $tnImage . '" data-rsTmb="' . $tnImage . '" data-rsBigImg="' . $fullImage . '">' . $val['objectDetails_Adres_Woonplaats'] . ' - ' . obj_generateAddress($val['objectDetails_Adres_Straatnaam'], $val['objectDetails_Adres_Huisnummer'], $val['objectDetails_Adres_HuisnummerToevoeging']). '</a>' . PHP_EOL;
					
					$i++;
				}
				
				?>
			
			</div>
		
		</div>
		
	</div>
	
	<?php } ?>

</div>
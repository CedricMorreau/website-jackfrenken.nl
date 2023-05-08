<section class="service-bar content-wrapper">
	
	<div class="anchor" id="services"></div>

	<div class="service-quote">
		<div class="quote-wrapper">
			<h2>... en waar <br>bent u naar <br><strong>op zoek?</strong></h2>
		</div>
	</div>
	
	<div class="service-wrapper">
		
		<a href="<?php echo $template->findPermalink(33, 1); ?>">
			<div class="service-item">
				
				<strong>Koop</strong>woning
			</div>
		</a>

		<a href="<?php echo $template->findPermalink(34, 1); ?>">
			<div class="service-item">
				
				<strong>Huur</strong>woning
			</div>
		</a>

		<a href="<?php echo $template->findPermalink(35, 1); ?>">
			<div class="service-item">
				<strong>Nieuw</strong>bouw
			</div>
		</a>

		<a href="<?php echo $template->findPermalink(36, 1); ?>">
			<div class="service-item">
				<strong>Bouw</strong>kavels
			</div>
		</a>

		<a href="<?php echo $template->findPermalink(37, 1); ?>">
			<div class="service-item">
				<strong>Bedrijfs</strong>panden
			</div>
		</a>
		
		<a href="<?php echo $template->findPermalink(88, 1); ?>">
			<div class="service-item">
				<strong>Hypo-</strong>theek
			</div>
		</a>

		
	</div>
</section>

<section class="search-filter-wrapper content-wrapper">
	<div class="search-filter-container">
		<div class="search-text">
			<span>Gericht <br>zoeken:</span>
		</div>
		<div class="search-filter-bar">
			<form action="<?php echo $template->findPermalink(33, 1); ?>" id="zoek-form" method="GET">
				<div class="select-wrapper">
					<select id="form-changer">
					<optgroup>
					  <option value="0" selected="">Wat zoekt u?</option>
					  <option data-src="<?php echo $template->findPermalink(33, 1); ?>" data-value="koop">Koopwoningen</option>
					  <option data-src="<?php echo $template->findPermalink(34, 1); ?>" data-value="huur">Huurwoningen</option>
					  <option data-src="<?php echo $template->findPermalink(35, 1); ?>" data-value="nieuwbouw">Nieuwbouw</option>
					  <option data-src="<?php echo $template->findPermalink(36, 1); ?>" data-value="bouwkavel">Bouwkavel</option>
					  <option data-src="<?php echo $template->findPermalink(37, 1); ?>" data-value="bog">Bedrijfspanden</option>

					  </optgroup>
					</select>
				</div>
				<input src="<?php echo $dynamicRoot; ?>inc/ajax_searchAutoComplete.php" type="text" id="wonen_plaats" class="auto_complete" name="plaatsnaam" placeholder="Plaats, straat" data-ogType="wonen">

<!-- 				<div class="select-wrapper">
					<select name="radius">
						<optgroup>
				  		<option value="" selected="">Straal</option>
					  
				  		<?php

						$arrRadius = array(
							0 => '0 km',
							5 => '5 km',
							10 => '10 km',
							25 => '25 km',
							50 => '50 km',
							75 => '75 km',
							100 => '100 km',
							125 => '125 km',
							150 => '150 km'
						);
						
						foreach ($arrRadius as $key => $val) {
						
							echo '<option value="' . $key . '">' . $val . '</option>';
						}
						
						?>

					</select>
					</optgroup>
				</div> -->

				<div class="select-wrapper price-buy">
					<select name="prijsVan" class="prijsVan">
						<optgroup>
							<option value="" selected="">Prijs vanaf</option>						
							<?php

							$arrPriceFrom = array(0, 100000, 200000, 300000, 400000, 500000, 600000, 700000, 800000, 900000, 1000000, 1250000);
			
							foreach ($arrPriceFrom as $key => $val) {
			
								echo '<option value="' . $val . '">&euro; ' . number_format($val, 0, ',', '.') . ',-</option>';
							}
			
							?>
						</optgroup>
					</select>
				</div>
				
				<div class="select-wrapper price-buy">
					<select name="prijsTot" class="prijsTot">
						<optgroup>
						<option value="" selected="">Prijs tot</option>			
						<?php

						$arrPriceFrom = array(100000, 150000, 200000, 300000, 400000, 500000, 600000, 700000, 800000, 900000, 1000000, 1250000, 'Onbeperkt');
		
						foreach ($arrPriceFrom as $key => $val) {
		
							if ($val == 'Onbeperkt') {
		
								echo '<option value="">Onbeperkt</option>';
							}
							else {
		
								echo '<option value="' . $val . '">&euro; ' . number_format($val, 0, ',', '.') . ',-</option>';
							}
						}
		
						?>
					</select>
					</optgroup>
				</div>

				<div class="select-wrapper price-rent" style="display: none;">
					<select name="" class="prijsVan">
						<option value="" selected="">Prijs vanaf</option>						
						<?php

						$arrPriceFrom = array(0, 500, 1000, 1500, 2000, 2500, 3000, 35000, 10000);
		
						foreach ($arrPriceFrom as $key => $val) {
		
							echo '<option value="' . $val . '">&euro; ' . number_format($val, 0, ',', '.') . ',-</option>';
						}
		
						?>
					</select>
				</div>
				
				<div class="select-wrapper price-rent" style="display: none;">
					<select name="" class="prijsTot">
						<option value="" selected="">Prijs tot</option>			
						<?php

						$arrPriceFrom = array(500, 1000, 1500, 2000, 2500, 3500, 4000, 10000, 20000, 'Onbeperkt');
		
						foreach ($arrPriceFrom as $key => $val) {
		
							if ($val == 'Onbeperkt') {
		
								echo '<option value="">Onbeperkt</option>';
							}
							else {
		
								echo '<option value="' . $val . '">&euro; ' . number_format($val, 0, ',', '.') . ',-</option>';
							}
						}
		
						?>
					</select>
				</div>

		</div>
		<div class="search-filter-button">
			<input type="submit" value="Zoek" title="Zoek in aanbod"></input>
		</div>
		<div class="search-filter-button-mobile">
				<button type="submit">Zoeken <span>⟶</span></button>
		</div>
			</form>


		</div>
	<script type="text/javascript">

		$(document).ready(function() {

			$('#form-changer').change(function() {

				type = $(this).find('option:selected').data('value');

				if (type == "huur") {

					$('.price-buy').hide();
					$('.price-buy').find('.prijsVan').attr('name', '');
					$('.price-buy').find('.prijsTot').attr('name', '');

					$('.price-rent').show();
					$('.price-rent').find('.prijsVan').attr('name', 'prijsVan');
					$('.price-rent').find('.prijsTot').attr('name', 'prijsTot');
				}
				else {

					$('.price-rent').hide();
					$('.price-rent').find('.prijsVan').attr('name', '');
					$('.price-rent').find('.prijsTot').attr('name', '');

					$('.price-buy').show();
					$('.price-buy').find('.prijsVan').attr('name', 'prijsVan');
					$('.price-buy').find('.prijsTot').attr('name', 'prijsTot');
				}

				// Hier verder: update form action
				$('#zoek-form').attr('action', $(this).find('option:selected').data('src'));
			});
		});

	</script>

</section>
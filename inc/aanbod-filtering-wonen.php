<div class="sidebar-filtering">

	<p class="filter-head active">Koopwoningen &xrarr;</p>

	<a class="remove-filter">Filtering wissen <span class="remove-icon">&#x2715;</span></a>
	
	<form action="<?php echo $template->getPermalink(1, 1); ?>" id="filter-form" class="standard">

	<p class="filter-head toggle">Locatie</p>
	<div class="filter-wrapper">
		<input src="<?php echo $dynamicRoot; ?>inc/ajax_searchAutoComplete.php" type="text" id="<?php echo $ogType; ?>_plaats" name="plaatsnaam" class="auto_complete" value="<?php echo $filter['plaatsnaam']; ?>" data-ogType="wonen" placeholder="Plaats, straat of postcode">
		<div class="select-wrapper">
			<select name="radius" id="filter-straal">
				<option value="">Geen straal</option>

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

					$selected = (is_numeric($filter['radius']) && $filter['radius'] == $key) ? ' selected="selected"' : '';

					echo '<option value="' . $key . '"' . $selected . '>' . $val . '</option>';
				}

				?>

			</select>
		</div>
	</div>

	<p class="filter-head toggle">Prijs</p>
	<div class="filter-wrapper">
		<div class="select-wrapper">
			<select name="prijsVan" id="filter-prijs-vanaf">

				<?php

				if ($filter['saleType'] == 'rent')
					$arrPriceFrom = array(0, 500, 1000, 1500, 2000, 2500, 3000, 35000, 10000);
				else
					$arrPriceFrom = array(0, 100000, 200000, 300000, 400000, 500000, 600000, 700000, 800000, 900000, 1000000, 1250000);

				foreach ($arrPriceFrom as $key => $val) {

					$selected = ($filter['prijsVan'] == $val) ? ' selected="selected"' : '';

					echo '<option value="' . $val . '"' . $selected . '>Vanaf &euro; ' . number_format($val, 0, ',', '.') . ',-</option>';
				}

				?>

			</select>
		</div>

		<div class="select-wrapper">
			<select name="prijsTot" id="filter-prijs-tot">

				<?php

				if ($filter['saleType'] == 'rent')
					$arrPriceFrom = array(500, 1000, 1500, 2000, 2500, 3500, 4000, 10000, 20000, 'Onbeperkt');
				else
					$arrPriceFrom = array(100000, 150000, 200000, 300000, 400000, 500000, 600000, 700000, 800000, 900000, 1000000, 1250000, 'Onbeperkt');

				foreach ($arrPriceFrom as $key => $val) {

					if ($val == 'Onbeperkt') {

						$selected = (empty($filter['prijsTot'])) ? ' selected="selected"' : '';

						echo '<option value=""' . $selected . '>Prijs tot onbeperkt</option>';
					}
					else {

						$selected = ($filter['prijsTot'] == $val) ? ' selected="selected"' : '';

						echo '<option value="' . $val . '"' . $selected . '>Prijs tot &euro; ' . number_format($val, 0, ',', '.') . ',-</option>';
					}
				}

				?>

			</select>
		</div>
	</div>
		
	<div id="additional-filters" style="display: none;">
	
		<p class="filter-head toggle">Objectkenmerken</p>
		
		<div class="filter-wrapper">
		
			<?php if ($filter['saleType'] == "kopen") { ?>
			<div class="select-wrapper">
			
				<?php
				
				// Load the cache for functions
				$filterCache = unserialize(file_get_contents($documentRoot . 'data/cache/og/filters_wonen_koop.txt'));
				
				$typeArray = array('eengezinswoning', 'herenhuis', 'villa', 'landhuis', 'bungalow', 'woonboerderij', 'grachtenpand', 'woonboot', 'stacaravan', 'woonwagen', 'landgoed');
				$fullArray = array();
				
				$totalCount = 0;
				
				foreach ($typeArray as $key => $val) {
					
					$fullArray[$val] = array();
					
					if (isset($filterCache['functions'][$val]) && $filterCache['functions'][$val] > 0) {
						
						$fullArray[$val]['disabled'] = '';
						$fullArray[$val]['class'] = '';
						$fullArray[$val]['counter'] = $filterCache['functions'][$val];
						
						$totalCount += $filterCache['functions'][$val];
					}
					else {
						
						$fullArray[$val]['disabled'] = ' disabled="disabled"';
						$fullArray[$val]['class'] = 'disabled';
						$fullArray[$val]['counter'] = 0;
					}
				}
				
				ksort($fullArray);
				
				?>
				
				<select name="bestemming" id="filter-type-object">
				
					<option value="">Alle objecttypes</option>
	
					<?php
	
					foreach ($fullArray as $key => $val) {
	
						if ($val['counter'] > 0) {
	
							$selected = (isset($filter['bestemming']) && $filter['bestemming'] == $key) ? ' selected="selected"' : '';
	
							echo '<option value="' . $key . '"' . $selected . '>' . ucwords($key) . ' (' . $val['counter'] . ')</option>';
						}
					}
	
					?>
	
				</select>
			
			</div>
			<?php } ?>
			
			<div class="select-wrapper">
			
				<select name="perceelOppervlakte" id="filter-perceelopp">
					<option value="">Alle perceeloppervlaktes</option>
	
					<?php
	
					$arrOpp = array(100, 250, 500, 1000, 2500, 5000, 7500, 10000);
	
					foreach ($arrOpp as $key => $val) {
	
						$selected = ($filter['perceelOppervlakte'] == $val) ? ' selected="selected"' : '';
	
						echo '<option value="' . $val . '"' . $selected . '>Perceel: ca. ' . number_format($val, 0, ',', '.') . ' m&sup2;</option>';
					}
	
					?>
	
				</select>
			
			</div>
			
			<div class="select-wrapper">
			
				<select name="woonfunctieOppervlakte" id="filter-woonopp">
					<option value="">Alle woonoppervlaktes</option>
	
					<?php
	
					$arrOpp = array(50, 100, 250, 500, 750, 1000);
	
					foreach ($arrOpp as $key => $val) {
	
						$selected = ($filter['woonfunctieOppervlakte'] == $val) ? ' selected="selected"' : '';
	
						echo '<option value="' . $val . '"' . $selected . '>Woonopp: ca. ' . number_format($val, 0, ',', '.') . ' m&sup2;</option>';
					}
	
					?>
	
				</select>
			
			</div>
			
			<div class="select-wrapper">
			
				<select name="slaapkamers" id="filter-slaapkamers">
					<option value="">1+ slaapkamers</option>
	
					<?php
	
					$arrOpp = array(2, 3, 4, 5, 6);
	
					foreach ($arrOpp as $key => $val) {
	
						$selected = ($filter['slaapkamers'] == $val) ? ' selected="selected"' : '';
	
						echo '<option value="' . $val . '"' . $selected . '>' . number_format($val, 0, ',', '.') . '+ slaapkamers</option>';
					}
	
					?>
	
				</select>
			
			</div>
			
		</div>
	
	</div>
	
	</form>

	<p class="more-filters"><a href="javascript:void(0);" id="more-filters">Meer zoekfilters &darr;</a></p>

	<div class="filter-list-wrapper">
	<p class="filter-head"><a href="#">Huurwoningen</a></p>
	<p class="filter-head"><a href="#">Nieuwbouw</a></p>
	<p class="filter-head"><a href="#">Bouwkavels</a></p>
	<p class="filter-head"><a href="#">Bedrijfspanden</a></p>
	<p class="filter-head"><a href="#">Recent verkocht</a></p>
	</div>
</div>

<script type="text/javascript">

	$(document).ready(function() {

		$('#more-filters').click(function() {

			showFilters();
		});

		$('form#filter-form select').change(function() { 

			$('form#filter-form').submit();
		});

		<?php
		
		if ($extraSearch) {
			
			?>

		showFilters();

			<?php
		}
		
		?>
	});

	function showFilters() {

		$('div#additional-filters').show();
		$('#more-filters').parent().hide();
	}

</script>
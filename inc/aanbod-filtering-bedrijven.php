<div class="sidebar-filtering">
	
	<?php
	
	echo '<h1 class="filter-head">' . $totalRows . ' bedrijfspanden</h1>';
		
	?>

	<?php if ($noFilters != $MD5None) { ?>
	<a class="remove-filter" href="<?php echo $template->getPermalink(1, 1); ?>">Filtering wissen <span class="remove-icon">&#x2715;</span></a>
	<?php } ?>
	
	<form action="<?php echo $template->getPermalink(1, 1); ?>" id="filter-form" class="standard">

	<p class="filter-head toggle">Locatie</p>
	<div class="filter-wrapper">
		<input src="<?php echo $dynamicRoot; ?>inc/ajax_searchAutoComplete.php" type="text" id="bog_plaats" name="plaatsnaam" class="auto_complete" value="<?php echo $filter['plaatsnaam']; ?>" data-ogType="bog" placeholder="Plaats, straat of postcode">
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
			<select name="saleType" id="filter-prijstype">

				<?php

				$arrType = array(
					'both' => 'Kopen en huren',
					'rent' => 'Huren',
					'kopen' => 'Kopen'
				);

				foreach ($arrType as $key => $val) {

					$selected = ($filter['saleType'] == $key) ? ' selected="selected"' : '';

					echo '<option value="' . $key . '"' . $selected . '>' . $val . '</option>';
				}

				?>

			</select>
		</div>
	
		<div class="select-wrapper">
			<select name="prijsVan" id="filter-prijs-vanaf">

				<?php

				if ($filter['saleType'] == 'rent')
					$arrPriceFrom = array(0, 500, 1000, 1500, 2000, 2500, 3000, 3500, 10000);
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
		
		<?php if ($filter['saleType'] == 'rent') { ?>
		
		<div class="select-wrapper">
		
			<select name="rentType" id="filter-renttype">

				<?php

				$arrType = array(

					'' => 'Alle prijstypen',
					'per maand' => 'Per maand',
					'per jaar' => 'Per jaar',
					'per vierkante meter per jaar' => 'Per m<sup>2</sup> per jaar'
				);

				foreach ($arrType as $key => $val) {

					$selected = ($filter['rentType'] == $key) ? ' selected="selected"' : '';

					echo '<option value="' . $key . '"' . $selected . '>' . $val . '</option>';
				}

				?>

			</select>
		
		</div>
		
		<?php } ?>
	</div>

	<p class="filter-head toggle">Type</p>
	<div class="filter-wrapper">
		<div class="select-wrapper">
			<select name="bestemming" id="filter-bestemming">

				<?php

				$arrType = array(
					'' => 'Geen type',
					'bouwgrond' => 'Bouwgrond',
					'winkelruimte' => 'Winkelruimte',
					'kantoorruimte' => 'Kantoorruimte',
					'bedrijfsruimte' => 'Bedrijfsruimte',
					'horeca' => 'Horeca',
					'belegging' => 'Belegging',
				);

				foreach ($arrType as $key => $val) {

					$selected = ($filter['bestemming'] == $key) ? ' selected="selected"' : '';

					echo '<option value="' . $key . '"' . $selected . '>' . $val . '</option>';
				}

				?>

			</select>
		</div>
	</div>
	
	</form>

<!-- 	<p class="more-filters"><a href="javascript:void(0);" id="more-filters">Meer zoekfilters &darr;</a></p> -->
	
	<?php
	
	include($documentRoot . 'inc/aanbod-sidenav.php');
	
	?>
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
		
		/*if ($extraSearch) {
			
			?>

		showFilters();

			<?php
		}*/
		
		?>
	});

	function showFilters() {

		$('div#additional-filters').show();
		$('#more-filters').parent().hide();
	}

</script>
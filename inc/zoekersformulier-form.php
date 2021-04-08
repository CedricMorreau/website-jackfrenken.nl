<div id="object-contact-form-output" class="clearfix">
	<div class="form_loading group" style="display: none;">
		<p>
			<i>Het formulier wordt verstuurd&hellip;</i>
		</p>
	</div>
	<div class="form_error general" style="display: none;"><h2>Foutje</h2><p>Er ging iets mis op de server. Probeer het nog eens.</p></div>
	<div class="form_result" style="display: none;"><h2>Bedankt!</h2><p>Wij zullen indien nodig z.s.m. reageren.</p></div>
</div>

<form id="zoeker-formulier">
	
	<!-- Naam -->
	<div class="title-row-wrapper">
		<div class="title-text">Uw naam</div><div class="title-line"></div>
	</div>


	<div class="name-row">
			<div class="gender-wrapper">
			    <label for="contactChoice1" class="form-item size15">
			    	<input type="radio" checked="checked" name="aanhef" id="contactChoice1" value="DHR">
			    	Dhr.
				</label>

			    <label for="contactChoice2" class="form-item size15">
			    	<input type="radio" name="aanhef" id="contactChoice2" value="MEVR">
			    	Mevr.
			    </label>
				
				<label for="contactChoice3" class="form-item size15">
			    	<input type="radio" name="aanhef" id="contactChoice3" value="FAM">
			    	Fam.
			    </label>
			</div>
		
	    	<input type="text" name="voornaam" placeholder="Voornaam*" class="form-item size25" autocomplete="given-name">
	    	<input type="text" name="tussenvoegsel" placeholder="Tussenv." class="form-item size20" autocomplete="additional-name">
	    	<input type="text" name="achternaam" placeholder="Achternaam*" class="form-item size25" autocomplete="family-name">
		
	</div>
	

	<!-- Contactgegevens -->
	<div class="title-row-wrapper">
		<div class="title-text">Uw contactgegevens</div><div class="title-line"></div>
	</div>

	<div class="contactgegevens-row">
		<input type="text" name="contactStraat" placeholder="Straat*" class="form-item size30" autocomplete="street-address">
		<input type="text" name="contactHuisnummer" placeholder="Nr*" class="form-item size20" autocomplete="">
		<input type="text" name="contactPostcode" placeholder="Postcode.*" class="form-item size25" autocomplete="postal-code">
		<input type="text" name="contactPlaats" placeholder="Plaats*" class="form-item size20" autocomplete="address-level2">
	</div>

	<div class="contactgegevens-row">
		<input type="text" name="contactTelefoon" placeholder="Telefoonnummer*" class="form-item size30" autocomplete="tel home">
		<input type="text" name="contactMobiel" placeholder="Mobiel" class="form-item size20" autocomplete="tel mobile">
		<input type="text" name="contactEmail" placeholder="E-mailadres*" class="form-item size25" autocomplete="email">
		<div class="form-item size25">* verplichte velden</div>
	</div>	
	
	<!-- Plaats -->
	<div class="title-row-wrapper">
		<div class="title-text">Zoeken in plaats</div><div class="title-line"></div>
	</div>

	<div class="plaats-row">
						
		<label for="plaatsnamenOverzicht" class="size40">
			<select size="9" name="plaatsnamenOverzicht" multiple="multiple" class="form-item plaatskeuzes">

				<?php

				$plaatsen = Cache::get('realworks/plaatsen');

				if ($plaatsen === false) {

					$search_form = new RealworksSearchForm('e2ed5b0a-d544-409b-aa06-7f3a875c2403', 44003);
					$search_form->fetch_locations();
	
					$plaatsen = array_values($search_form->locations('plaatsen'));
					sort($plaatsen);

					Cache::set($plaatsen, 'realworks/plaatsen', 86400);
				}

				?>

				<?php foreach ($plaatsen as $plaats): ?>
					<option value="<?php echo strtolower($plaats); ?>">
						<?php echo ucfirst($plaats); ?>
					</option>
				<?php endforeach; ?>
			
			</select>
		</label>


		<div class="form-item size20">
			<div class="control-arrows-wrapper">
				<div class="col size50">
					<a href="javascript:void(0);" class="button left remove" title="Verwijderen uit selectie">&larr;</a>
				</div>
				<div class="col size50">
					<a href="javascript:void(0);" class="button right add" title="Toevoegen aan selectie">&rarr;</a>										
				</div>
			</div>
		</div>

		<div class="form-item size40">
			<select name="plaatsnaam[]" multiple="multiple" class="form-item plaatskeuzes">
				<optgroup label="Uw selectie:"></optgroup>
			</select>
		</div>




	</div>
	
	<!-- Financieel -->
	<div class="title-row-wrapper">
		<div class="title-text">Financieel</div><div class="title-line"></div>
	</div>

	<div class="financieel-row">
		
		<div class="checkbox-wrapper">
			<label for="financieel-kopen" class="form-item size20">
				<input type="checkbox" id="financieel-kopen" name="soortAankoop[]" value="Kopen">Kopen
			</label>
			<label for="financieel-huren" class="form-item size20">
				<input type="checkbox" id="financieel-huren" name="soortAankoop[]" value="Huren">Huren
			</label>
		</div>

		<select class="form-item size30"name="prijsVanaf">
			<option value="">Prijs vanaf</option>
			
			<?php
			
			$arrPriceFrom = array(0, 100000, 200000, 300000, 400000, 500000, 600000, 700000, 800000, 900000, 1000000, 1250000);

			foreach ($arrPriceFrom as $key => $val) {

				echo '<option value="' . $val . '">Vanaf &euro; ' . number_format($val, 0, ',', '.') . ',-</option>';
			}

			?>
			
		</select>

		<select class="form-item size30" name="prijsTot">
			<option value="">Prijs tot</option>
			
			<?php
			
			$arrPriceFrom = array(100000, 150000, 200000, 300000, 400000, 500000, 600000, 700000, 800000, 900000, 1000000, 1250000, 'Onbeperkt');

			foreach ($arrPriceFrom as $key => $val) {

				if ($val == 'Onbeperkt') {

					echo '<option value="">Prijs tot onbeperkt</option>';
				}
				else {

					echo '<option value="' . $val . '">Prijs tot &euro; ' . number_format($val, 0, ',', '.') . ',-</option>';
				}
			}

			?>
			
		</select>

	</div>

	<!-- Kenmerken -->
	<div class="title-row-wrapper">
		<div class="title-text">Kenmerken</div><div class="title-line"></div>
	</div>

	<div class="kenmerken-row">

		<select class="form-item size25" name="objectSoort">
			<option value="">Soort object</option>
			<option value="appartement">Appartement</option>
			<option value="bungalow">Bungalow</option>
	        <option value="eengezinswoning">Eengezinswoning</option>
	        <option value="grachtenpand">Grachtenpand</option>
	        <option value="herenhuis">Herenhuis</option>
	        <option value="landgoed">Landgoed</option>
	        <option value="landhuis">Landhuis</option>
	        <option value="stacaravan">Stacaravan</option>
	        <option value="villa">Villa</option>
	        <option value="woonboerderij">Woonboerderij</option>
	        <option value="woonboot">Woonboot</option>
	        <option value="woonwagen">Woonwagen</option>
		</select>

		<select class="form-item size25" name="objectBouwvorm">
			<option value="">Bouwvorm</option>
			<option value="eindwoning">Eindwoning</option>
			<option value="2-onder-1-kapwoning">2-onder-1-kapwoning</option>
			<option value="geschakelde woning">Geschakelde woning</option>
			<option value="geschakelde 2-onder-1-kapwoning">Geschakelde 2-onder-1-kapwoning</option>
			<option value="halfvrijstaande woning">Halfvrijstaande woning</option>
			<option value="hoekwoning">Hoekwoning</option>
			<option value="tussenwoning">Tussenwoning</option>
			<option value="vrijstaande woning">Vrijstaande woning</option>
		</select>
		
		<select class="form-item size25" name="objectPerceelOpp">
			<option value="">Perceeloppervlakte</option>
			<option value="<250">Tot 250 m&#178;</option>
			<option value="250">250+ m&#178;</option>
			<option value="500">500+ m&#178;</option>
			<option value="1000">1000+ m&#178;</option>
			<option value="2500">2500+ m&#178;</option>
			<option value="5000">5000+ m&#178;</option>
		</select>

		<select class="form-item size25" name="objectSlaapkamers">
			<option value="">Slaapkamers</option>
			<option value="1">1+ kamer</option>
			<option value="2">2+ kamers</option>
			<option value="3">3+ kamers</option>
			<option value="4">4+ kamers</option>
			<option value="5">5+ kamers</option>
		</select>

	</div>

	<!-- Opmerkingen en versturen -->
	<div class="title-row-wrapper">
		<div class="title-text">Opmerkingen en versturen</div><div class="title-line"></div>
	</div>

	<div class="opmerkingen-row">
		<textarea class="size75" name="message" rows="5"></textarea>

		<input type="submit" name="submitSearchForm" value="Schrijf mij in als zoeker" class="size25">

	</div>


</form>
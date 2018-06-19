<form id="zoeker-formulier">
	
	<!-- Naam -->
	<div class="title-row-wrapper">
		<div class="title-text">Uw naam</div><div class="title-line"></div>
	</div>


	<div class="name-row">
			<div class="gender-wrapper">
			    <label for="contactChoice1" class="form-item size15">
			    	<input type="radio" id="contactChoice1" name="contact" value="email">
			    	Dhr.
				</label>

			    <label for="contactChoice2" class="form-item size15">
			    	<input type="radio" id="contactChoice2" name="contact" value="phone">
			    	Mevr.
			    </label>
				
				<label for="contactChoice3" class="form-item size15">
			    	<input type="radio" id="contactChoice3" name="contact" value="mail">
			    	Fam.
			    </label>
			</div>
		
	    	<input type="text" name="" placeholder="Voornaam*" class="form-item size25" autocomplete="given-name">
	    	<input type="text" name="" placeholder="Tussenv.*" class="form-item size20" autocomplete="additional-name">
	    	<input type="text" name="" placeholder="Achternaam*" class="form-item size25" autocomplete="family-name">
		
	</div>
	

	<!-- Contactgegevens -->
	<div class="title-row-wrapper">
		<div class="title-text">Uw contactgegevens</div><div class="title-line"></div>
	</div>

	<div class="contactgegevens-row">
		<input type="text" name="" placeholder="Straat*" class="form-item size30" autocomplete="street-address">
		<input type="text" name="" placeholder="Nr*" class="form-item size20" autocomplete="">
		<input type="text" name="" placeholder="Postcode.*" class="form-item size25" autocomplete="postal-code">
		<input type="text" name="" placeholder="Plaats*" class="form-item size20" autocomplete="address-level2">
	</div>

	<div class="contactgegevens-row">
		<input type="text" name="" placeholder="Telefoonnummer*" class="form-item size30" autocomplete="tel home">
		<input type="text" name="" placeholder="Mobiel*" class="form-item size20" autocomplete="tel mobile">
		<input type="text" name="" placeholder="E-mailadres*" class="form-item size25" autocomplete="email">
		<div class="form-item size25">* verplichte velden</div>
	</div>	
	
	<!-- Plaats -->
	<div class="title-row-wrapper">
		<div class="title-text">Zoeken in plaats</div><div class="title-line"></div>
	</div>

	<div class="plaats-row">
						
		<label for="plaatsnamenOverzicht" class="size40">
			<select size="9" name="plaatsnamenOverzicht" multiple="multiple" class="form-item plaatskeuzes">
				<option value="baexem">Baexem</option>
				<option value="beegden">Beegden</option>
				<option value="beesel">Beesel</option>
				<option value="echt">Echt</option>
				<option value="haelen">Haelen</option>
				<option value="heel">Heel</option>
				<option value="herkenbosch">Herkenbosch</option>
				<option value="herten">Herten</option>
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
				<input type="checkbox" id="financieel-kopen" name="financieel-kopen" value="Kopen">Kopen
			</label>
			<label for="financieel-huren" class="form-item size20">
				<input type="checkbox" id="financieel-huren" name="financieel-huren" value="Huren">Huren
			</label>
		</div>

		<select class="form-item size30">
			<option>Prijs vanaf</option>
		</select>

		<select class="form-item size30">
			<option>Prijs tot</option>
		</select>

	</div>

	<!-- Kenmerken -->
	<div class="title-row-wrapper">
		<div class="title-text">Kenmerken</div><div class="title-line"></div>
	</div>

	<div class="kenmerken-row">

		<select class="form-item size25">
			<option>Soort object</option>
		</select>

		<select class="form-item size25">
			<option>Bouwvorm</option>
		</select>
		
		<select class="form-item size25">
			<option>Perceeloppervlakte</option>
		</select>

		<select class="form-item size25">
			<option>Slaapkamers</option>
		</select>

	</div>

	<!-- Opmerkingen en versturen -->
	<div class="title-row-wrapper">
		<div class="title-text">Opmerkingen en versturen</div><div class="title-line"></div>
	</div>

	<div class="opmerkingen-row">
		<textarea class="size75" rows="5"></textarea>

		<input type="submit" value="Schrijf mij in als zoeker" class="size25">

	</div>


</form>
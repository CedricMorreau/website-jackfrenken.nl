<form id="zoeker-formulier">

	<div class="title-row-wrapper">
		<div class="title-text">Uw naam</div><div class="title-line"></div>
	</div>


	<div class="name-row">

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
		
		
	    	<input type="text" name="" placeholder="Voornaam*" class="form-item size25">
	    	<input type="text" name="" placeholder="Tussenv.*" class="form-item size20">
	    	<input type="text" name="" placeholder="Achternaam*" class="form-item size25">
		
	</div>

	<div class="title-row-wrapper">
		<div class="title-text">Uw contactgegevens</div><div class="title-line"></div>
	</div>

	<div class="contactgegevens-row">
		<input type="text" name="" placeholder="Straat*" class="form-item size30"><input type="text" name="" placeholder="Nr*" class="form-item size20"><input type="text" name="" placeholder="Postcode.*" class="form-item size25"><input type="text" name="" placeholder="Plaats*" class="form-item size25">
	</div>
	<div class="contactgegevens-row"><input type="text" name="" placeholder="Telefoonnummer*" class="form-item size30"><input type="text" name="" placeholder="Mobiel*" class="form-item size20"><input type="text" name="" placeholder="E-mailadres*" class="form-item size25"><div class="size25">* verplichte velden</div></div>	

	<div class="title-row-wrapper">
		<div class="title-text">Zoeken in plaats</div><div class="title-line"></div>
	</div>

	<div class="plaats-row">
					
		<div class="form-item size40">
			<label for="plaatsnamenOverzicht"> <!-- FIXIT SB: Hier moeten plaatsnamen dynamisch ingeladen worden. -->
				<select size="9" name="plaatsnamenOverzicht" multiple="multiple" class="plaatskeuzes">
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
		</div>

		<div class="form-item size20">
			<div class="control-arrows-wrapper">
				<div class="col size50 alignCenter">
					<a href="javascript:void(0);" class="button left remove" title="Verwijderen uit selectie">&larr;</a>
				</div>
				<div class="col size50 alignCenter">
					<a href="javascript:void(0);" class="button right add" title="Toevoegen aan selectie">&rarr;</a>										
				</div>
			</div>
		</div>

		<div class="form-item size40">
			<select name="plaatsnaam[]" multiple="multiple" class="plaatskeuzes">
				<optgroup label="Uw selectie:"></optgroup>
			</select>
		</div>

	</div>



</form>
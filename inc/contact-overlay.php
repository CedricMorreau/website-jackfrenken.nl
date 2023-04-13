<!-- Contact overlay -->
<div class="contact-overlay-wrapper contact" id="contact" data-hook="Contact">
	<div class="contact-overlay-container">
		<div class="close-overlay" data-hook="Contact-close" title="Sluit venster">
			<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 15 11">
			  <path d="M2 .508L2.508 0 13 10.492l-.508.508L2 .508z"/>
			  <path d="M2 10.493L12.492 0 13 .508 2.508 11 2 10.493z"/>
			</svg>

		</div>
		<div class="contact-content-wrapper">
			<h2>heeft u een vraag of opmerking? <strong>Stuur ons <br>een bericht!</strong></h2>
			<form id="contactForm" action="#" class="standard" method="post">
			
				

				<p class="error" style="display: none;">Er ging iets mis op de server. Probeer het nogmaals.</p>
				
				<div class="label-wrapper">
					<label for="contact_name" value="Uw naam">Uw naam *</label>
						<input type="text" name="contact_name" id="contact_name" data-hook="Contact-text">

				</div>

				<div class="label-wrapper">
					<label for="contact_phone">Telefoonnummer *</label>
						<input type="text" name="contact_phone" id="contact_phone">
				</div>

				<div class="label-wrapper">
					<label for="contact_email">E-mailadres *</label>
						<input type="text" name="contact_email" id="contact_email">
				</div>
				
				<textarea name="contact_msg" placeholder="Uw vraag of opmerking..."></textarea>


				<input type="submit" value="Verstuur dit bericht" title="Verstuur">
				<div class="close-overlay-text" data-hook="Contact-closetwo" title="Sluiten">&#xd7; sluiten</div>
			</form>
		</div>
		
		<div id="bedankt-melding" style="display: none;">
		
			<h2>Bedankt voor uw bericht</h2>
			<p>We nemen, indien van toepassing, zo spoedig mogelijk contact met u op.</p>
		
		</div>


	</div>
</div>
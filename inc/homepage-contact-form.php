<section class="homepage-contact-wrapper">
	<div class="content-wrapper" id="new-contact-form">
		<h4><strong>Vragen of een afspraak?</strong> <br>Neem gerust contact met ons op</h4>

		<div class="form-message success">
			Het formulier is verzonden. We nemen zo snel mogelijk contact met u op.
		</div>

		<div class="form-message error">
			Er ging iets mis op de server. Probeer het nogmaals.
		</div>

		<form action="#">
			<div class="form-row-wrapper">
				<div class="input-wrapper">
					<input name="first_name" type="text" placeholder="Voornaam*">
				</div>

				<div class="input-wrapper">
					<input name="last_name" type="text" placeholder="Achternaam*">
				</div>
			</div>

			<div class="form-row-wrapper">
				<div class="input-wrapper">
					<input name="email" type="email" placeholder="E-mailadres*">
				</div>

				<div class="input-wrapper">
					<input name="phone" type="tel" placeholder="Telefoon*">
				</div>
			</div>

			<div class="form-row-wrapper">
				<div class="input-wrapper textarea">
					<textarea name="msg" placeholder="Stel uw vraag"></textarea>
				</div>
			</div>

			<div class="form-submit-wrapper">
				<button type="submit" class="cta-button qua">Verstuur</button>
			</div>
		</form>
	</div>
</section>

<script>
	$(document).ready(function() {

		// Form
		$("#new-contact-form form").validate({
			// focusInvalid: false,
			errorPlacement: function(error, element) {},
			rules: {
				first_name: {
					required: true,
					minlength: 2
				},
				last_name: {
					required: true
				},
				email: {
					required: true,
					email: true
				},
				phone: {
					required: true
				},
				msg: {
					required: true,
					minlength: 4
				}
			},
			submitHandler: function(form) {
				return SubmitContactForm();
			}
		});
	});

	function SubmitContactForm() {

		$.ajax({
			type: 'POST',
			url: '/inc/process-contactform-new.php',
			data: $('#new-contact-form form').serialize(),
			success: function(data) {

				if (data == 0) {

					$("#new-contact-form .form-message.error").fadeIn();
				} else {
					dataLayer.push({
						'event': 'contactformulier-submit'
					});
					$("#new-contact-form .form-message.error").hide();
					$("#new-contact-form form").hide();
					$("#new-contact-form .form-message.success").fadeIn();
				}
			}

		});

		return false;
	}
</script>
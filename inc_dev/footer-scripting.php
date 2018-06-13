<!-- Footer scripting -->
<script type="text/javascript" src="../js/jquery.scrollTo.min.js"></script>
<script src="../js/scripts.js"></script>


<!-- Contact overlay -->
<script>

	(function(doc) {
		//console.log("contact");
		var iconContact = doc.querySelector('[data-hook="Icon-contact"]');
		var contact = doc.querySelector('[data-hook="Contact"]');
		var contactText = doc.querySelector('[data-hook="Contact-text"]');
		var contactClose = doc.querySelector('[data-hook="Contact-close"]');
		var contactClosetwo = doc.querySelector('[data-hook="Contact-closetwo"]');
		
		console.log("contact overlay");
		
		function showContact() {
		contact.classList.add('Contact--show');
		contact.classList.add('a-fadeIn');
		//contactText.focus();
		doc.body.classList.add('u-overflow-hidden');
		console.log("show contact");
		}

		function hideContact() {
		console.log("hide contact");
		//mailingText.classList.remove('a-fadeUp');
		//mailingText.classList.add('Search-text--hide');
		//mailing.classList.remove('a-fadeIn');
		contact.classList.add('a-fadeOut');

		setTimeout(function(){
		  contact.classList.remove('Contact--show');
		  contact.classList.remove('a-fadeOut');
		  contactText.classList.remove('Contact-text--hide');
		  doc.body.classList.remove('u-overflow-hidden');
		}, 100);
		}
		// close on ESC
		doc.addEventListener('keyup', function(e) {
		if (e.keyCode == 27) { 
		  hideContact();
		}
		});


		iconContact.addEventListener('click', showContact);
		contactClose.addEventListener('click', hideContact);
		contactClosetwo.addEventListener('click', hideContact);
		// Contact popup ready

	})(document);

</script>
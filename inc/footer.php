<section class="quote-wrapper">
	<div class="quote-beeldmerk">
		<img src="<?php echo $dynamicRoot; ?>resources/jf_beeldmerk-s.png">
	</div>
	
	<?php
	
	$slogans = array(
		'Alles voor huis en hypotheek<br>...in een keer geregeld!',
		'De deur naar het geluk<br>gaat naar binnen open',
		'Dit is h&eacute;t moment',
		'Dat is lekker thuiskomen'
	);
	
	$rand = $slogans[array_rand($slogans)];
	
	?>

	<h4><?php echo $rand; ?></h4>

</section>

<footer class="primary-footer">
	
	<div class="phone-icon">
		<a href="tel:0475335225">
			<img src="<?php echo $dynamicRoot; ?>resources/icon-telefoon.svg" alt="telefoon">
		</a>
	</div>

	<div class="footer-row-1">
		<div class="footer-links-wrapper">
			
			<div class="footer-links-col-1">
				<p><a href="<?php echo $template->findPermalink(33, 1); ?>">Koopwoningen</a></p>
				<p><a href="<?php echo $template->findPermalink(34, 1); ?>">Huurwoningen</a></p>
				<p><a href="<?php echo $template->findPermalink(35, 1); ?>">Nieuwbouw</a></p>
			</div>

			<div class="footer-links-col-2">
				<p><a href="<?php echo $template->findPermalink(36, 1); ?>">Bouwkavels</a></p>
				<p><a href="<?php echo $template->findPermalink(37, 1); ?>">Bedrijfspanden</a></p>
				<p><a href="<?php echo $template->findPermalink(61, 1); ?>">Hypotheken</a></p>
			</div>
		</div>

		<div class="footer-contact-wrapper">
			<p class="contact-phone"><a href="tel:0475335225">(0475) 33 52 25</a></p>
			<a href="<?php echo $template->findPermalink(70, 1); ?>" class="alle-contactegegevens">Alle contactgegevens &rarr;</a>
		</div>
	</div>

	<div class="footer-row-2">
		<a href="<?php echo $template->findPermalink(48, 1); ?>">
			<img src="<?php echo $dynamicRoot; ?>resources/nvm-logo-lit.svg" class="nvm-logo" alt="NVM logo">Wij <span class="rating">scoren</span> op Funda &rarr;
		</a>
	</div>

</footer>
<footer class="secondary-footer">
	<div class="links-wrapper">
		<span>2018 &copy; Jack Frenken Makelaars en adviseurs</span> 
		<a href="<?php echo $template->findPermalink(52, 1); ?>">Disclaimer <span class="entity">&rarr;</span></a>
		<a href="<?php echo $template->findPermalink(71, 1); ?>">Colofon <span class="entity">&rarr;</span></a>
		<a href="<?php echo $template->findPermalink(51, 1); ?>">Cookies <span class="entity">&rarr;</span></a>
		<a href="<?php echo $template->findPermalink(50, 1); ?>">Privacybeleid <span class="entity">&rarr;</span></a>
	</div>
	<div class="social-links">
		<a href="https://www.facebook.com/jackfrenken" title="Facebook" target="_blank"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 50 50" class="fb-icon">
			  <path d="M0 0h50v50H0z"/>
			  <defs>
			    <path id="a" d="M0 0h50v50H0z"/>
			  </defs>
			  <clipPath id="b">
			    <use xlink:href="#a" overflow="visible"/>
			  </clipPath>
			  <path d="M23.1 33.8v-8h-2.8v-3.1h2.8v-2.3c0-2.7 1.7-4.1 4.1-4.1 1.2 0 2.2.1 2.5.1v2.8H28c-1.3 0-1.6.6-1.6 1.5v2h3.2l-.4 3.1h-2.7v8h-3.4z" clip-path="url(#b)" fill="#fff"/>
			</svg></a>

		<a href="https://twitter.com/JackFrenkenNVM">
			<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 50 50" class="tw-icon" target="_blank">
			  <path d="M0 0h50v50H0z"/>
			  <defs>
			    <path id="a" d="M0 0h50v50H0z"/>
			  </defs>
			  <clipPath id="b">
			    <use xlink:href="#a" overflow="visible"/>
			  </clipPath>
			  <path d="M15 31.3c1.8 1.2 4 1.8 6.3 1.8 7.6 0 11.9-6.4 11.7-12.2.8-.6 1.5-1.3 2-2.1-.7.3-1.5.5-2.4.6.8-.5 1.5-1.3 1.8-2.3-.8.5-1.7.8-2.6 1-.7-.8-1.8-1.3-3-1.3-2.6 0-4.6 2.5-4 5-3.4-.2-6.4-1.8-8.5-4.3-1.1 1.8-.6 4.3 1.3 5.5-.7 0-1.3-.2-1.9-.5 0 1.9 1.3 3.7 3.3 4.1-.6.2-1.2.2-1.9.1.5 1.6 2 2.8 3.8 2.9-1.6 1.3-3.7 1.9-5.9 1.7" clip-path="url(#b)" fill="#fff"/>
			</svg>
		</a>

		<a href="https://www.linkedin.com/company/1680615?trk=tyah&trkInfo=tarId%3A1410275372935%2Ctas%3Ajack%20frenken%2Cidx%3A1-1-1" target="_blank">
			<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 50 50" class="in-icon">
			  <path d="M0 0h50v50H0z"/>
			  <defs>
			    <path id="a" d="M0 0h50v50H0z"/>
			  </defs>
			  <clipPath id="b">
			    <use xlink:href="#a" overflow="visible"/>
			  </clipPath>
			  <path d="M34 33.8h-3.7v-5.7c0-1.4 0-3.1-1.9-3.1s-2.2 1.5-2.2 3v5.8h-3.7V22h3.6v1.6c.5-.9 1.7-1.9 3.5-1.9 3.8 0 4.5 2.4 4.5 5.6v6.5zM18.2 20.4c-1.2 0-2.2-1-2.2-2.1 0-1.2 1-2.1 2.2-2.1 1.2 0 2.2.9 2.2 2.1-.1 1.2-1 2.1-2.2 2.1m1.9 13.4h-3.7V22h3.7v11.8z" clip-path="url(#b)" fill="#fff"/>
			</svg>

		</a>

	</div>
</footer>

<?php include("inc/contact-overlay.php"); ?>
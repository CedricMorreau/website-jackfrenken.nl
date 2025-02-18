<footer class="primary-footer">

	<!-- Footer content -->
	<div class="content-container">
		<!-- Scroll to top -->
		<div class="scroll-to-top-wrapper" onclick="javascript:$.scrollTo('body', 1000)">
			<div class="scroll-content-wrapper">
				<img src="<?php echo $dynamicRoot; ?>resources/icon-arrow-up.svg" alt="">
			</div>
		</div>
		<div class="footer-row-1">
			<div class="footer-links-wrapper">
				
				<div class="footer-links-col footer-links-col-1">
					<h5>Navigatie</h5>
					<p><a href="<?php echo $template->findPermalink(33, 1); ?>">Aanbod</a></p>
					<p><a href="<?php echo $template->findPermalink(76, 1); ?>">Over ons</a></p>
					<p><a href="<?php echo $template->findPermalink(53, 1); ?>">Diensten</a></p>
					<p><a href="<?php echo $template->findPermalink(88, 1); ?>">Hypotheekadvies</a></p>
					<p><a href="<?php echo $template->findPermalink(59, 1); ?>">Actualiteiten</a></p>
					<p><a href="<?php echo $template->findPermalink(70, 1); ?>">Contact</a></p>
				</div>

				<div class="footer-links-col footer-links-col-2">
					<h5>Aanbod</h5>
					<p><a href="<?php echo $template->findPermalink(33, 1); ?>">Koopwoningen</a></p>
					<p><a href="<?php echo $template->findPermalink(34, 1); ?>">Huurwoningen</a></p>
					<p><a href="<?php echo $template->findPermalink(35, 1); ?>">Nieuwbouw</a></p>
					<p><a href="<?php echo $template->findPermalink(36, 1); ?>">Bouwkavels</a></p>
					<p><a href="<?php echo $template->findPermalink(37, 1); ?>">Bedrijfspanden</a></p>
					<p><a href="<?php echo $template->findPermalink(88, 1); ?>">Hypotheken</a></p>
				</div>

				<div class="footer-links-col footer-links-col-3">
					<h5>Hypotheek</h5>
					<p><a href="<?php echo $template->findPermalink(91, 1); ?>">Starter</a></p>
					<p><a href="<?php echo $template->findPermalink(92, 1); ?>">Ondernemer</a></p>
					<p><a href="<?php echo $template->findPermalink(90, 1); ?>">Gescheiden</a></p>
					<p><a href="<?php echo $template->findPermalink(93, 1); ?>">Senioren</a></p>
					<p><a href="<?php echo $template->findPermalink(87, 1); ?>">Verduurzamen</a></p>
					<p><a href="<?php echo $template->findPermalink(94, 1); ?>">Verhuizen</a></p>
				</div>

				<div class="footer-links-col footer-links-col-4">

					<p class="contact-phone">
						<a href="tel:0475335225">
							<?php include ("resources/icon-telefoon-clean.svg"); ?>
							(0475) 33 52 25
						</a>
					</p>

					<div class="contact-email"><a href="mailto:info@jackfrenken.nl">info@jackfrenken.nl</a></div>


					<div class="contact-city-header">
						Roermond
					</div>

					<div class="contact-address">
						Graaf Reinaldstraat 1, 6041 XB
					</div>

					<div class="contact-divider"></div>

					<div class="contact-city-header">
						Echt
					</div>

					<div class="contact-address">
						Gelrestraat 18, 6101 EW
					</div>

					<div class="social-links">
						<a href="https://www.instagram.com/jackfrenkenmakelaarsadviseurs/" title="Instagram" target="_blank">
							<?php include($documentRoot . "resources/icon_instagram.svg"); ?>
						</a>

						<a href="https://www.facebook.com/jackfrenken" target="_blank" class="Facebook" title="Facebook">
							<?php include($documentRoot . "resources/icon_facebook.svg"); ?>
						</a>

					<a href="https://www.linkedin.com/company/jack-frenken-makelaars-en-adviseurs" target="_blank" title="LinkedIn">
						
						<?php include($documentRoot . "resources/icon_linkedin.svg"); ?>

					</a>

				</div>
				</div>

			</div>

		</div>

		<div class="footer-row-2">
			<a href="<?php echo $template->findPermalink(48, 1); ?>">
				<img src="<?php echo $dynamicRoot; ?>resources/nvm-logo-lit.svg" class="nvm-logo" alt="NVM logo" title="NVM">Wij <span class="rating">scoren</span> op Funda <span class="arrow">&rsaquo;</span>
			</a>

		</div>
	</div>
</footer>

<footer class="secondary-footer">
	<div class="content-container">
		<span>2023 &copy; Jack Frenken Makelaars en adviseurs</span> 

		<div class="links-wrapper">
			
			<a href="<?php echo $template->findPermalink(86, 1); ?>">Algemene voorwaarden</a>
			<a href="<?php echo $template->findPermalink(71, 1); ?>">Colofon</a>
			<a href="<?php echo $template->findPermalink(50, 1); ?>">Privacy</a>
			<a href="<?php echo $template->findPermalink(51, 1); ?>">Cookies</a>
			<a href="<?php echo $template->findPermalink(52, 1); ?>">Disclaimer</a>
		</div>
	</div>

</footer>



<!-- Contact overlay -->
<?php include("inc/contact-overlay.php"); ?>
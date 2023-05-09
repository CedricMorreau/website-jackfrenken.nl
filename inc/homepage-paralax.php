<section class="parallax-banner">

	<div class="parallax-image slide" data-slide="1" style="background-image: url('<?php echo $dynamicRoot; ?>resources/homepage-parralax.jpg');">
		<div class="content-wrapper">
			<div class="review-emblem">
				<div class="review-container">
					<h5>Funda<br>
					Score</h5>

					<div class="review-score">9,<span>5</span></div>

					<div class="review-text">
						Jack Frenken heeft een gemiddelde score van 9,5 op Funda
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="parallax-image slide" data-slide="2" style="display: none; background-image: url('<?php echo $dynamicRoot; ?>resources/homepage-parralax.jpg');">
		<div class="content-wrapper">
			<div class="review-emblem">
				<div class="review-container">
					<h5>Funda<br>
					Score</h5>

					<div class="review-score">9,<span>5</span></div>

					<div class="review-text">
						Jack Frenken heeft een gemiddelde score van 9,5 op Funda
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Paging -->
	<div class="slider-paging">
		<ul>
			<li class="active" data-slide="1"></li>
			<li data-slide="2"></li>
		</ul>
	</div>

</section>

<script type="text/javascript">

	<?php /* basic slider */ ?>

	var current_slide = 1;
	var max_slides;
	var timeout = 10000;

	$(document).ready(function() {

		max_slides = $('.slide[data-slide]').length;

		$('.slider-paging ul li').click(function() {

			new_slide = $(this).attr('data-slide');

			move_slide(new_slide);
		});

		setInterval(() => {
			next_slide();
		}, timeout);
	});

	function move_slide(index) {

		if ($(`.slide[data-slide="${index}"]`).length > 0) {

			// Hide the slides
			$(`.slide[data-slide]`).hide();

			// Show the proper slide
			$(`.slide[data-slide="${index}"]`).show();

			// Change the paging
			$(`.slider-paging ul li[data-slide]`).removeClass('active');
			$(`.slider-paging ul li[data-slide="${index}"]`).addClass('active');

			current_slide = index;
		}
	}

	function next_slide() {

		new_slide = (current_slide + 1);

		if (new_slide > max_slides)
		new_slide = 1;

		if (new_slide < 1)
		new_slide = 1;

		move_slide(new_slide);
	}

</script>
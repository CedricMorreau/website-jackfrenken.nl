function windowScrollTo(scrollToPosition) {
	$(window).scrollTo(scrollToPosition);
}

// Hamburger menu toggle
$('.mobile-toggle').click(function() {
	$('.hamburger-icon').toggleClass('open');
	$('.close-icon').toggleClass('open');
	$('.primary-nav nav').toggleClass('open');
	$('.primary-nav nav').toggleClass('animated fadeIn faster');
});

// open/close submenu on hover/mouseout
if (screen.width >= 768) {
	$( ".primary-nav nav li" ).hover(

		function() {
		$( this ).children("a").addClass( "open" );
		$( this ).children("ul").addClass( "open" );
		}, 

		function() {
			$( this ).children("a").removeClass( "open" );
			$( this ).children("ul").removeClass( "open" );
			$( this ).children("ul").addClass( "animated FadeOut faster" );
		}

	);
}

// Toggle filters
$(document).ready(function (e) {
	$(".filter-head.toggle").click(function() {
		$(this).next().toggle();
	});
});


// Move sidenav to bottom of page on mobile
$(document).ready(function(e) {
	
	// On load
	if ($(window).width() <= 768) {
		$(".aanbod-overzicht .sidebar-filtering .filter-list-wrapper").detach().insertAfter('.content-wrapper .paging-wrapper');
	}
	else {
		$(".content-wrapper .filter-list-wrapper").detach().insertAfter('.aanbod-overzicht .sidebar-filtering .standard');
	}
	
	// On resize
	$(window).resize(function(e) {

		if ($(window).width() <= 768) {
			$(".aanbod-overzicht .sidebar-filtering .filter-list-wrapper").detach().insertAfter('.content-wrapper .paging-wrapper');
		}
		else {
			$(".content-wrapper .filter-list-wrapper").detach().insertAfter('.aanbod-overzicht .sidebar-filtering .standard');
		}
	});

});

function setCookie(c_name,value,expiredays){
		
	var exdate=new Date();
	exdate.setDate(exdate.getDate()+expiredays);
	document.cookie=c_name+ "=" +escape(value)+((expiredays==null) ? "" : "; expires="+exdate.toGMTString())+ "; path=/";
}

// Show popup
function showPopup() {
	$('.popup-wrapper').addClass('active');
}


$(document).ready(function(e) {
	//showPopup();

	$('.popup-close').click(function(e) {
		e.preventDefault();
		$('.sell').removeClass('active');
		setCookie('sellPopup', 1, 7);
	});


});
/*
	Verti by HTML5 UP
	html5up.net | @ajlkn
	Free for personal and commercial use under the CCA 3.0 license (html5up.net/license)
*/

function resizeNav(media){
	if (media.matches){
		if ($('body').scrollTop > 80 || document.documentElement.scrollTop > 80) {
			$("#header-wrapper").css("padding","5px 0 5px 0");
			$("#logo h1").css("fontSize","1.8em");
		} else {
			$("#header-wrapper").css("padding","4.5em 0 3em 0");
			$("#logo h1").css("fontSize","4.0em");
		}
	}
}

function reloadPlugin(){
	var container_width = (Number($('.fb-column').width())).toFixed(0);
	//getting parent box height
	var container_height = (Number($('.fb-column').height())).toFixed(0);
	if (!isNaN(container_width) && !isNaN(container_height)) {
		$(".fb-page").attr("data-width", container_width).attr("data-height", container_height);
	}
	if (typeof FB !== 'undefined') {
		FB.XFBML.parse();
	}
}


(function($) {

	var	$window = $(window),
		$body = $('body');

	// Breakpoints.
		breakpoints({
			xlarge:  [ '1281px',  '1680px' ],
			large:   [ '981px',   '1280px' ],
			medium:  [ '737px',   '980px'  ],
			small:   [ null,      '736px'  ]
		});

	// Play initial animations on page load.
		$window.on('load', function() {
			window.setTimeout(function() {
				$body.removeClass('is-preload');
			}, 100);
			reloadPlugin();
		});

		var rtime;
		var timeout = false;
		var delta = 200;
		$(window).resize(function() {
			rtime = new Date();
			if (timeout === false) {
				timeout = true;
				setTimeout(resizeend, delta);
			}
		});

		function resizeend() {
			if (new Date() - rtime < delta) {
				setTimeout(resizeend, delta);
			} else {
				timeout = false;
				reloadPlugin();
			}               
		}

		$(document).ready(function(){
			if (document.querySelector('.shrink')==null){
				var media = window.matchMedia("(min-width: 980px)");
				window.onscroll=  function(){
					resizeNav(media);
				};
				media.addListener(resizeNav);
			}
		})


	// Dropdowns.
		$('#nav > ul').dropotron({
			mode: 'fade',
			noOpenerFade: true,
			speed: 300
		});

	// Nav.

		// Toggle.
			$(
				'<div id="navToggle">' +
					'<a href="#navPanel" class="toggle"></a>' +
				'</div>'
			)
				.appendTo($body);

		// Panel.
			$(
				'<div id="navPanel">' +
					'<nav>' +
						$('#nav').navList() +
					'</nav>' +
				'</div>'
			)
				.appendTo($body)
				.panel({
					delay: 500,
					hideOnClick: true,
					hideOnSwipe: true,
					resetScroll: true,
					resetForms: true,
					side: 'left',
					target: $body,
					visibleClass: 'navPanel-visible'
				});

})(jQuery);
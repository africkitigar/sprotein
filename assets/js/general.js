(function ($, root, undefined) {
	
	$(function () {
		
		$(document).ready(function(){


			    const breakpoint = 1180;

    // zapamti originalnog parenta i poziciju
    const $socials = $('.socials');
    const $originalParent = $socials.parent();
    const originalIndex = $socials.index();

    function moveSocials() {
        const winWidth = $(window).width();

        if (winWidth < breakpoint) {
            // prebaci u mobile menu footer
            if (!$socials.parent().is('.mobile-menu-footer')) {
                $socials.detach().appendTo('.mobile-menu-footer');
            }
        } else {
            // vrati nazad u header (na isto mesto)
            if (!$socials.parent().is($originalParent)) {
                if (originalIndex === 0) {
                    $socials.detach().prependTo($originalParent);
                } else {
                    $socials.detach().insertAfter(
                        $originalParent.children().eq(originalIndex - 1)
                    );
                }
            }
        }
    }

    // init
    moveSocials();

    // resize (debounce light)
    let resizeTimer;
    $(window).on('resize', function () {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(moveSocials, 100);
    });


	


			$('.wp-block-gallery a').attr('data-gall', 'gall1');
			$(".wp-block-gallery .wp-block-image").each(function(){
			    $(this).find('a').attr('title', $(this).find('img').attr('title'));
			  });

	/*		new VenoBox({
    selector: '.wp-block-gallery a',
    numeration: true,
    infinigall: true,
    share: true,
    spinner: 'rotating-plane'
});
*/



			/*------------------------------------*\
   				 MOBILE MENU
			\*------------------------------------*/			
			//open mobile menu
			$('#mob-menu-bar').click(function() {
				$(this).toggleClass('change');
				$('.header-bottom nav, body').toggleClass('menu-open');
			});

			$('header nav .cta > a').click(function() {
				$('#mob-menu-bar').trigger('click');
			});

			 //mob submenus
			 $('nav .menu-item-has-children > a').click(function (e) {
				e.preventDefault();
				
				var $submenu = $(this).next().next('.sub-menu'); // Target the sub-menu directly
				$(this).parent().toggleClass('opened');
				
				$(this).parent().siblings().find('.menu-item-has-children').removeClass('opened');
			});
			/*------------------------------------*\
   				 END OF MOBILE MENU
			\*------------------------------------*/
			$(window).scroll(function() {
				if ($(this).scrollTop() >= 500) {        
					$('#return-to-top').fadeIn(200);    
				} else {
					$('#return-to-top').fadeOut(200);   
				}
			});


			$('#return-to-top').click(function() {      
				$('body,html').animate({
					scrollTop : 0                      
				}, 500);
			});
			


			//create sticky nav
			$(window).scroll(function() {

				if ($(this).scrollTop() > 60){  
					$('.header-bottom').addClass("sticky");
					$('#theme-toggle').addClass('hide');
				}
				else{
					$('.header-bottom').removeClass("sticky");
					$('#theme-toggle').removeClass('hide');
				}
			});


			//accordions
			$('.accordion-title').click(function(){
				$(this).toggleClass('opened');
				$(this).next().slideToggle();
				$(this).parent().siblings().find('.accordion-content').slideUp();
				$(this).parent().siblings().find('.accordion-title').removeClass('opened');
			});


			//animate click on achor
				var $root = $('html, body');

				$('a[href^="#"]').not('.wc-tabs a, .woocommerce-tabs a').on('click', function (e) {
					var target = $($.attr(this, 'href'));

					if (!target.length) return;

					e.preventDefault();

					$root.animate({
						scrollTop: target.offset().top
					}, 500);
				});


			$('.gutenberg .sticky a:first-child').addClass('clicked');

			$('.gutenberg .sticky a').click(function(){
				$(this).addClass('clicked');
				$(this).siblings().removeClass('clicked');
			});



			$(window).scroll(function() {
	        var scrollDistance = $(window).scrollTop();
	    
	        // Assign active class to nav links while scolling
	        $('.wp-block-group').each(function(i) {
                if ($(this).position().top <= scrollDistance) {
                    var id = $(this).attr("id");
                        $('.gutenberg .sticky a[href="#'+id+'"]').addClass("clicked").siblings().removeClass("clicked");
                }
			        });
			}).scroll();





			// Hide Header on on scroll down
/*var didScroll;
var lastScrollTop = 0;
var delta = 1;
var navbarHeight = $('header').outerHeight();

$(window).scroll(function(event){
    didScroll = true;
});

setInterval(function() {
    if (didScroll) {
        hasScrolled();
        didScroll = false;
    }
}, 250);

function hasScrolled() {
    var st = $(this).scrollTop();
    
    // Make sure they scroll more than delta
    if(Math.abs(lastScrollTop - st) <= delta)
        return;
    
    // If they scrolled down and are past the navbar, add class .nav-up.
    // This is necessary so you never see what is "behind" the navbar.
    if (st > lastScrollTop && st > navbarHeight){
        // Scroll Down
        $('header').removeClass('nav-down').addClass('nav-up');
    } else {
        // Scroll Up
        if(st + $(window).height() < $(document).height()) {
            $('header').removeClass('nav-up').addClass('nav-down');
        }
    }
    
    lastScrollTop = st;
}*/











$(window).on("load", function () {
    var urlHash = window.location.href.split("#")[1];
    if (urlHash &&  $('#' + urlHash).length )
          $('html,body').animate({
              scrollTop: $('#' + urlHash).offset().top
          }, 500);
});







	//Scroll back to top
	
	var progressPath = document.querySelector('.progress-wrap path');
	var pathLength = progressPath.getTotalLength();
	progressPath.style.transition = progressPath.style.WebkitTransition = 'none';
	progressPath.style.strokeDasharray = pathLength + ' ' + pathLength;
	progressPath.style.strokeDashoffset = pathLength;
	progressPath.getBoundingClientRect();
	progressPath.style.transition = progressPath.style.WebkitTransition = 'stroke-dashoffset 10ms linear';		
	var updateProgress = function () {
		var scroll = $(window).scrollTop();
		var height = $(document).height() - $(window).height();
		var progress = pathLength - (scroll * pathLength / height);
		progressPath.style.strokeDashoffset = progress;
	}
	updateProgress();
	$(window).scroll(updateProgress);	
	var offset = 50;
	var duration = 550;
	jQuery(window).on('scroll', function() {
		if (jQuery(this).scrollTop() > offset) {
			jQuery('.progress-wrap').addClass('active-progress');
		} else {
			jQuery('.progress-wrap').removeClass('active-progress');
		}
	});				
	jQuery('.progress-wrap').on('click', function(event) {
		event.preventDefault();
		jQuery('html, body').animate({scrollTop: 0}, duration);
		return false;
	})
	


	
    $('.zigzag .read-more').click(function (event) {
        event.preventDefault();
        $(this).prev().slideToggle('slow');
        $(this).text($(this).text() == 'Read less...' ? 'Read more...' : 'Read less...');
    });
	$('.coach .read-more').click(function (event) {
        event.preventDefault();
        $(this).prev().toggleClass('expanded');
        $(this).text($(this).text() == 'Read less...' ? 'Read more...' : 'Read less...');
    });

		});//ready
	});
	


})(jQuery, this);

var window_width = 0;
var lastScrollTop = 0;
var header_scrolled = false;
var isMobile = false;
var pTScrolled, pBScrolled, hScrolled, pTNoScrolled, pBNoScrolled, hNoScrolled;

var endscroll = false;
var scrolling = false;

let isTicking = false;

var onScroll = () => {
	if (!isTicking) {
		window.requestAnimationFrame(() => {
			var scrollTop = jQuery(window).scrollTop();
			var wWidth = jQuery(window).width();

			jQuery('.popover').attr('data-popper-placement', 'bottom');

			if(scrollTop != 0){
				jQuery('.topheader').addClass('scrolled');
				gsap.to(jQuery('.progress-dots'), .3, {opacity: '0', ease: Power2.easeOut});
			}else{
				jQuery('.topheader').removeClass('scrolled');
				gsap.to(jQuery('.progress-dots'), .5, {opacity: '1', ease: Power2.easeOut});
			}

			if(wWidth > 992){
				if(scrollTop > 100){
					gsap.to(jQuery('.sticky-top'), .5, {top: '100px', ease: Power2.easeOut});
				}else{
					gsap.to(jQuery('.sticky-top'), .5, {top: '0px', ease: Power2.easeOut});	
				}
			}else if(wWidth <= 992 && wWidth > 768){
				if(scrollTop > 200){
					gsap.to(jQuery('.sticky-top'), .5, {top: '100px', ease: Power2.easeOut});
				}else{
					gsap.to(jQuery('.sticky-top'), .5, {top: '0px', ease: Power2.easeOut});	
				}
			}

			isTicking = false;
		});
		isTicking = true;
	}
}

var onResize = () => {
	jQuery('.popover').attr('data-popper-placement', 'bottom');
	onScroll();
}

var message = (trigger) => {
	const triggerel = document.getElementById(trigger);
	const themeUrl = (typeof my_ajax_obj !== 'undefined' && my_ajax_obj.theme_url) ? my_ajax_obj.theme_url : (my_ajax_obj.base_url + '/wp-content/themes/tiendas');
	const popover = new bootstrap.Popover(triggerel, {
		'template': '<div class="popover" data-popper-placement="bottom" role="tooltip"><div class="popover-arrow"></div><span class="popover-close"><img src="'+themeUrl+'/img/close-gray.svg"/></span><span class="popover-success"><img src="'+themeUrl+'/img/success.svg"/></span><h3 class="popover-header"></h3><div class="popover-body"></div></div>',
		'placement': 'bottom',
		'html': true
	});
	popover.show(); 

	setTimeout(function(){
		jQuery('.popover').attr('data-popper-placement', 'bottom');
	}, 100);

	setTimeout(function(){
		jQuery('.popover').fadeOut(200, function(){
			jQuery(this).remove();
		});
	}, 7000);

	jQuery('.popover').click(function() {
		popover.hide(); 
	});

	return popover;
}

var popup_message = (message, type, time = 10000) => {
	if(jQuery('body').find('.popup-message').length > 0){
		jQuery('body').find('.popup-message').remove();
	}

	var title = '';
	var icon = '';
	const themeUrl = (typeof my_ajax_obj !== 'undefined' && my_ajax_obj.theme_url) ? my_ajax_obj.theme_url : (my_ajax_obj.base_url + '/wp-content/themes/tiendas');

	switch (type){
		case 'info':{
			title = my_ajax_obj.popup_info;
			icon = themeUrl + '/img/info.svg';
			break;
		}
		case 'success':{
			title = my_ajax_obj.popup_success;
			icon = themeUrl + '/img/success.svg';
			break;
		}
		case 'error':{
			title = my_ajax_obj.popup_error;
			icon = themeUrl + '/img/error.svg';
			break;
		}
		case 'warning':{
			title = my_ajax_obj.popup_warning;
			icon = themeUrl + '/img/bulb.svg';
			break;
		}
	}

	const popup_html = ''+
		'<div class="popup-message popup-message-container" style="display:none;">'+
			'<div class="popup-message-box">'+
				'<i class="popup-message-close"></i>'+
				'<div class="popup-message-header">'+
					'<h3 class="small d-flex align-items-center justify-content-start gap-2">'+
						'<img class="popup-message-icon" src="'+icon+'" />'+
						title+
					'</h3>'+
				'</div>'+
				'<div class="popup-message-body"><p class="mb-0">'+message+'</p></div>'+
			'</div>'+
		'</div>';

	jQuery('body').append(popup_html);

	jQuery('.popup-message').fadeIn(100, function(){
		gsap.to(jQuery('.popup-message .popup-message-box'), .4, {top: '0', ease: Power2.easeOut, onComplete: function() {
			setTimeout(function(){
				gsap.to(jQuery('.popup-message .popup-message-box'), .2, {top: '-100vh', ease: Power2.easeIn, onComplete: function() {
					jQuery('.popup-message').fadeOut();
				}});
			}, time);
		}});
	});
};

// popup_message('Morbi molestie arcu sit amet libero porttitor, a mollis odio suscipit. <b>Fusce at sapien</b> id justo cursus mollis. Ut non orci in magna pretium consequat. Nam id purus eu velit vulputate elementum. Mauris ac sapien non felis scelerisque tincidunt.', 'warning');

const getQueryVariable = variable => {
	var query = window.location.search.substring(1);
	var vars = query.split("&");
	for (var i=0;i<vars.length;i++) {
			var pair = vars[i].split("=");
			if(pair[0] == variable){return pair[1];}
	}
	return(false);
} 

const onlyNumbers = inputVal => {
	var numbers = /^[0-9]+$/;
	if(inputVal.match(numbers))
		return true;
	else
		return false;
} 

const isValidEmail = email => {
	if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(email)){
    	return true;
	}
    
	return false;
}

const truncateString = (str, maxLength) => {
    // Check if the string is longer than the maximum length
    if (str.length <= maxLength) {
        return str; // Return the original string if it's within the limit
    }

    // Calculate the number of characters to keep from the start and end
    const keepChars = Math.floor((maxLength - 3) / 2); // 3 for the ellipsis

    // Create the truncated string
    const truncated = str.slice(0, keepChars) + '...' + str.slice(-keepChars);

    return truncated;
}

const isValidUrl = urlString => {
	var urlPattern = new RegExp('^(https?:\\/\\/)?'+ // validate protocol
		'((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|'+ // validate domain name
		'((\\d{1,3}\\.){3}\\d{1,3}))'+ // validate OR ip (v4) address
		'(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*'+ // validate port and path
		'(\\?[;&a-z\\d%_.~+=-]*)?'+ // validate query string
		'(\\#[-a-z\\d_]*)?$','i'); // validate fragment locator
	return !!urlPattern.test(urlString);
}
  

var visit_url = window.location.href;

var done = false;

/**
 * Merges two FileList objects and returns a new FileList object
 * @param fileListA The first FileList object
 * @param fileListB The second FileList object
 */
const mergeFileLists = (fileListA, fileListB) => {
	const dataTransfer = new DataTransfer();

	for (let i = 0; i < fileListA.length; i++) {
		dataTransfer.items.add(fileListA[i]);
	}

	for (let i = 0; i < fileListB.length; i++) {
		dataTransfer.items.add(fileListB[i]);
	}

	return dataTransfer.files;
}

gsap.registerPlugin(ScrollTrigger);
gsap.registerPlugin(ScrollToPlugin);
gsap.config({
	autoSleep: 60,
	force3D: false,
	nullTargetWarn: false,
	trialWarn: false,
	units: { left: "%", top: "%", rotation: "rad" },
});

const animateValue = (obj, start, end, duration) => {
	var startTimestamp = null;
  
	var step = (timestamp) => {
	  if (!startTimestamp) startTimestamp = timestamp;
	  var progress = Math.min((timestamp - startTimestamp) / duration, 1);
	  obj.innerHTML = Math.floor(progress * (end - start) + start);
  
	  if (progress < 1) {
		window.requestAnimationFrame(step);
	  }
	};
  
	window.requestAnimationFrame(step);
}

window.addEventListener("dragover",function(e){
	e = e || event;
	e.preventDefault();
},false);
window.addEventListener("drop",function(e){
	e = e || event;
	e.preventDefault();
},false);

var update_site_version = () => {
	let version = jQuery("body").find('site_version').html(); 
	if(version != undefined){
		version = version.replace("{{site_version}}", jQuery('#site_version').val());
		jQuery("body").find('site_version').html(version);
	}
}

var update_popup = (storeForm) => {
	var actionUrl = my_ajax_obj.ajax_url;
	jQuery.post(actionUrl, {action: 'ws', wsa: 'update-popup-resume', store_id: storeForm.find('input[name="store_id"]').val()}, function(resp){
		jQuery('#popup-resume-container').html(resp.popup_resume);
		setTimeout(function(){
			update_site_version();
			var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
			tooltipTriggerList.map(function (tooltipTriggerEl) {
				return new bootstrap.Tooltip(tooltipTriggerEl, {
					html: true,
					sanitize: false,
				})
			});
		}, 100);
	});
}

jQuery(document).ready(function ($) {
	// Inject security nonce into all jQuery AJAX requests automatically
	$.ajaxSetup({
		data: {
			nonce: typeof my_ajax_obj !== 'undefined' ? my_ajax_obj.nonce : ''
		}
	});

	// Single delegated listener for popup message close button
	$('body').on('click', '.popup-message .popup-message-close', function(){
		gsap.to($('.popup-message .popup-message-box'), .2, {top: '-100vh', ease: Power2.easeIn, onComplete: function() {
			$('.popup-message').fadeOut();
		}});
	});

	$('a.direct-link').click(function(e){
		var href = $(this).attr('href');
		
		if(href != undefined && href.indexOf('#open-client-mail') != -1){
			e.preventDefault();
			window.open(atob('bWFpbHRvOmhvbGFAeG9waWZpZXIuY29t'));
		}
	});

	setTimeout(function(){
		update_site_version();
	}, 100);

	$('body').on('click', '.btn-open-sidebar', function() {
        $('#popup-resume').fadeIn(200, function() {
			$('.thumb-img').height($('.thumb-content').height() + 30);
            gsap.to($('#popup-resume #popup-resume-box'), .5, {right: '0px', ease: Power2.easeOut, onComplete: function() {}});
        });
    });

    $('body').on('click', '.popup-resume-close', function() {
        gsap.to($('#popup-resume #popup-resume-box'), .5, {right: '-100vw', ease: Power2.easeIn, onComplete: function() {
            $('#popup-resume').fadeOut();
        }});
    });

	$('.burger').click(function(){

		if(!$('.burger').hasClass('on')){
			$('.blur-section').addClass('active');
			$('body, html').css({
				'overflow-y': 'hidden',
				'overflow-x': 'hidden'
			});

			$('.burger').addClass('on');

			$('#header').addClass('menu-open');
			
			gsap.to($('#mobile-menu'), .4, {right: '12px', ease: Power2.easeOut, onComplete: function() {
				gsap.to($('#mobile-menu .mainmenu-container'), .2, {right: '-15px', ease: Power2.easeOut, onComplete: function() {}});
			}});
		}else{
			$('.burger').removeClass('on');

			$('#header').removeClass('menu-open');

			gsap.to($('#mobile-menu .mainmenu-container'), 0, {right: '-100vw', ease: Power2.easeOut, onComplete: function() {

				gsap.to($('#mobile-menu'), .4, {right: '-100vw', ease: Power2.easeOut, onComplete: function() {}});

				$('.blur-section').removeClass('active');
	
				$('body, html').css({
					'overflow-y': 'unset',
					'overflow-x': 'hidden',
				});
			}});
		}
	});

	$('#mobile-menu a').click(function(){
		$('.icon-close-menu').trigger('click');	
	})

	$('.icon-close-menu').click(function(){
		$('.burger').removeClass('on');

		gsap.to($('#mobile-menu .mainmenu-container'), .4, {right: '-100vw', ease: Power2.easeIn, onComplete: function() {

			gsap.to($('#mobile-menu'), .2, {background: 'rgba(0,0,0,0)', backdropFilter: 'blur(0px)', ease: Power2.easeIn, onComplete: function() {
				gsap.to($('#mobile-menu'), 0, {right: '-100vw', ease: Power2.easeOut, onComplete: function() {}});
			}});

			$('.blur-section').removeClass('active');

			$('body, html').css({
				'overflow-y': 'unset',
				'overflow-x': 'unset'
			});
		}});
	});

	$('.how-to-modal-box-close').click(function (){
		$('.how-to-modal').fadeOut();
	});

	$('a[href="#howto"]').click(function (e) {
		e.preventDefault();
        $('.how-to-modal').fadeIn();
	});

	var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
	tooltipTriggerList.map(function (tooltipTriggerEl) {
		return new bootstrap.Tooltip(tooltipTriggerEl, {
			html: true,
			sanitize: false,
		})
	});

	if($('#howto-popover').length > 0) {
		message('howto-popover');
		
		// setTimeout(function(){
		// 	$('.home .popover').attr('data-popper-placement', 'bottom');
		// 	$('.template-page .popover').attr('data-popper-placement', 'bottom');
		// }, 100);
		
		// setTimeout(function(){
		// 	$('.popover').fadeOut(200, function(){
		// 		$(this).remove();
		// 	});
		// }, 7000);
	}

	$('.price-block .columns-group .column-item').each(function(){
		$(this).click(function(){
			window.location = $(this).attr('target');
		});
	});

	// console.log(getQueryVariable('msg'))

	if($('#message-popover').length > 0 && getQueryVariable('msg') === '0') {
		$('#message-popover').attr('data-bs-content', my_ajax_obj.msg_0);
	}

	if($('#message-popover').length > 0 && getQueryVariable('msg') === '1') {
		$('#message-popover').attr('data-bs-content', my_ajax_obj.msg_1);
	}

	if($('#message-popover').length > 0 && getQueryVariable('msg') == 2) {
		$('#message-popover').attr('data-bs-content', my_ajax_obj.msg_2);
	}

	if($('#message-popover').length > 0 && getQueryVariable('msg') == 3) {
		$('#message-popover').attr('data-bs-content', my_ajax_obj.msg_3);
	}

	if($('#message-popover').length > 0 && getQueryVariable('msg') == 4) {
		$('#message-popover').attr('data-bs-content', my_ajax_obj.msg_4);
	}

	if($('#message-popover').length > 0 && getQueryVariable('msg') == 5) {
		$('#message-popover').attr('data-bs-content', my_ajax_obj.msg_5);
	}

	if($('#message-popover').length > 0 && getQueryVariable('msg') != undefined) {
		message('message-popover');
		
		// setTimeout(function(){
		// 	$('.home .popover').attr('data-popper-placement', 'bottom');
		// 	$('.template-page .popover').attr('data-popper-placement', 'bottom');
		// }, 100);

		// setTimeout(function(){
		// 	$('.popover').fadeOut(200, function(){
		// 		$(this).remove();
		// 	});
		// }, 7000);
	}

	if(!String.linkify) {
		String.prototype.linkify = function() {
	
			// http://, https://, ftp://
			var urlPattern = /\b(?:https?|ftp):\/\/[a-z0-9-+&@#\/%?=~_|!:,.;]*[a-z0-9-+&@#\/%=~_|]/gim;
	
			// www. sans http:// or https://
			var pseudoUrlPattern = /(^|[^\/])(www\.[\S]+(\b|$))/gim;
	
			// Email addresses
			var emailAddressPattern = /[\w.]+@[a-zA-Z_-]+?(?:\.[a-zA-Z]{2,6})+/gim;
	
			return this
				.replace(urlPattern, '<a href="$&">$&</a>')
				.replace(pseudoUrlPattern, '$1<a href="http://$2">$2</a>')
				.replace(emailAddressPattern, '<a href="mailto:$&">$&</a>');
		};
	}
	
	if($('.projects').length > 0) {
		$(".projects").slick({
			dots: false,
			arrows: true,
			centerMode: true,
			slidesToShow: 1,
			slidesToScroll: 1,
			centerPadding: '18%',
			speed: 700,
			autoplay: false,
			autoplaySpeed: 7000,
			adaptiveHeight: false,
			pauseOnHover: true,
			pauseOnFocus: false,
			pauseOnDotsHover: false,
			focusOnSelect: true,
			infinite: true,
			responsive: [
				{
					breakpoint: 1200,
					settings: {
						centerPadding: '15%',
					}
				},
				{
					breakpoint: 992,
					settings: {
						centerPadding: '10%',
					}
				},
				{
					breakpoint: 768,
					settings: {
						centerPadding: '0%',
					}
				}
			]
		});
	}

	$('a:not(.direct-link)').click(function (e) {
		e.preventDefault();
		let $href = $(this).attr('href');

		// console.log($href);

		if($href.indexOf('http') != -1){
			window.location = $href;
			return;
		}

		if($href[0] == '#'){
			if($($href).length > 0)
				gsap.to(window, {duration: .3, scrollTo:{ y: $href, offsetY: 100}, ease: Power2.easeOut});
		}else{
			if($href.indexOf('#') != -1){
				$target = $href.substring($href.indexOf('#'));
				if($($target).length > 0)
					gsap.to(window, {duration: .3, scrollTo:{ y: $target, offsetY: 100}, ease: Power2.easeOut});
				else
					window.location = $href;
			}else
				window.location = $href;
		}
	});
	
	var windowWidth = $(window).width();

	$(window).resize(function () {
		// if ($(window).width() != windowWidth) {
			onResize();
		// }
	});

	jQuery.fn.isInViewport = function() {
		var elementTop = jQuery(this).offset().top;
		var elementBottom = elementTop + jQuery(this).outerHeight();
		var viewportTop = jQuery(window).scrollTop();
		var viewportBottom = viewportTop + jQuery(window).height();
		
		return elementBottom > viewportTop && elementTop < viewportBottom;
	};

	$(window).scroll(function () {
		onScroll();
	});

	lastScrollTop = jQuery(window).scrollTop();

	onResize();

	$('.fade-right').attr({
        "data-aos": "fade-right",
    });

	$('.fade-left').attr({
        "data-aos": "fade-left",
    });

	$('.fade-up').attr({
        "data-aos": "fade-up",
    });

	$('.fade-down').attr({
        "data-aos": "fade-down",
    });

	$('.timeline-block-timeline').attr({
        "data-aos": "fade-up",
    });

	//aos init
	AOS.init({
		duration: 700,
	});
}); 
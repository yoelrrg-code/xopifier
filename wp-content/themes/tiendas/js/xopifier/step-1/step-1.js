var currentStep = 1;
var poppedstate = false;
var stepLoaded = false;
var categoryList = [];
var productsQTY = 0;
var productUID = 0;
var selectedStage = '';
var progressbar_count = 0;
var progressbar_step = 0;

var updateProgressBar = (href) => {
	const $progressbar = jQuery('.progressbar');
	const $progressOn   = $progressbar.find('.on');
	const $progressOff  = $progressbar.find('.off');
	const $useProgressBar = jQuery('.use-progress-bar');

	$progressbar.show();
	progressbar_count = $useProgressBar.length;
	progressbar_step = Math.round(100 / (progressbar_count || 1));
	
	$useProgressBar.each(function(){
		jQuery(this).attr('step', progressbar_step);
	});

	if(jQuery('#' + href).hasClass('use-progress-bar')){
		var step = progressbar_step;

		if(Number($progressOn.attr('percent')) === 0){
			$progressOn.attr('percent', step);
			$progressOff.attr('percent', (100 - step));
		}

		var progressPosition = 0;
		$useProgressBar.each(function(i){
			if(jQuery(this).hasClass('current')){
				progressPosition = i + 1;
			}
		});

		var currentPercent = step * progressPosition;
		var remainderPercent = 100 - currentPercent;

		$progressOn.css('width', currentPercent + '%').attr('percent', currentPercent);
		$progressOff.css('width', remainderPercent + '%').attr('percent', remainderPercent);

		if(currentPercent > 95){
			$progressbar.addClass('checked');
			$progressOn.attr('percent', 100).css('width', '100%');
			$progressOff.attr('percent', 0).css('width', '0%');
		}

		if(!$progressbar.hasClass('show')){
			$progressbar.addClass('show').fadeIn();
		}
	}else{
		$progressOn.attr('percent', 0);
		$progressOff.attr('percent', 100);
		$progressbar.removeClass('show').fadeOut();
	}
};


jQuery(document).ready(function ($) {
    //============================================================================================================================================================
	//form steps validations
	//============================================================================================================================================================

	const fileTypeIcons = {
		'application/vnd.openxmlformats-officedocument.wordprocessingml.document': 'docx.svg',
		'text/plain': 'txt.svg',
		'application/vnd.oasis.opendocument.text': 'odt.svg',
		'application/msword': 'doc.svg',
		'application/pages': 'pages.svg',
		'application/pdf': 'pdf.svg',
		'image/jpeg': 'img.svg',
		'image/png': 'img.svg',
		'image/gif': 'img.svg',
		'video/mp4': 'video.svg',
		'video/mov': 'video.svg',
	};

	progressbar_count = $('.use-progress-bar').length;
	progressbar_step = Math.round(100/progressbar_count); // 14 14 14 14 14 14 14
	
	$('.use-progress-bar').each(function(){
		$(this).attr('step', progressbar_step);
	});

	//============================================================================================================================================================
	//file uploads
	//============================================================================================================================================================

    const uploaderStep1Files = new UploadController('.field-upload', '', null);	
	
	//uploaded_files

	//============================================================================================================================================================
	//============================================================================================================================================================

	var linkProductsIndex = 0;
	var pcProductsIndex = 0;

	const pcProductsList = [];

	$('.update-service-price').on('keypress', function(e) {
		if (e.key === 'Enter' || e.keyCode === 13) {
			$('.update-service-price').trigger('blur');
			$('.update-form .btn-update').trigger('click');
			$('.update-service-price').focus();
			return;
		}

		const char = String.fromCharCode(e.which);

		// Permitir sólo dígitos (0-9)
		if (!/[0-9]/.test(char)) {
			e.preventDefault();
		}
	});

	$('.update-service-price').on('blur', function() {
		let value = parseInt($(this).val(), 10);

		if (isNaN(value) || value < 10) {
			$(this).val('10');
		}
	});

	$('.update-service-price').on('input', function() {
		let value = $(this).val();

		// Eliminar todo lo que no sea dígito
		value = value.replace(/[^0-9]/g, '');

		$(this).val(value);

		let num = parseInt(value, 10);
		if (!isNaN(num)) {
			if (num >= 101) {
				$(this).val('100');
			}
		}
	});

	//para actualizar la cantidad de productos en el formulario de resumen de los datos al final
    $('.update-form .btn-update').click(function () {
		var button = $(this);
		var initial_qty = Number(button.prev().attr('min'));
		var new_qty = Number(button.prev().val());
		var price_per_unit = Number(button.prev().attr('price-per-unit'));
		var total_price = Number($('.total-price').val());
		var previous_qty = button.prev().attr('previous-qty');
		
		if(new_qty > previous_qty){
			var new_price = (new_qty - initial_qty) * price_per_unit;
			total_price = total_price + new_price;

			$('.service-price-'+button.attr('service-id')).val(new_price);
			$('.service-price-'+button.attr('service-id')).next().html("$"+new_price);
		}else{
			var new_price = (previous_qty - new_qty) * price_per_unit;
			total_price = total_price - new_price;

			var previous_price = Number($('.service-price-'+button.attr('service-id')).val());

			$('.service-price-'+button.attr('service-id')).val(previous_price - new_price);
			$('.service-price-'+button.attr('service-id')).next().html("$"+ (previous_price - new_price));
		}

		$('.product-qty').html(new_qty);

		var prices = [];
		$('.optional-services .form-check-input').each(function(){
			if($(this).is(':checked')){
				prices.push(Number($(this).parents('.optional-services').next().find('input').val()));
			}	
		});

		$('.field-services-prices').val(prices);

		$('.products-qty').val(new_qty);

		button.prev().attr('previous-qty', new_qty);
		$('.total-price').val(total_price);
		$('.display-total-price').html('$'+total_price);
		var advanced_payment = $('.display-first-price').attr('price');
		$('.remainder-price').html(total_price-advanced_payment);
	});

	var selected_languages = [];

	//para el selector de idiomas evento click al seleccionar un idioma
	$('body').on('click', '#language-selector-box .language-selector-div .lang-div', function(){
		var lang_div = $(this);
		if(!lang_div.hasClass('separator') && !lang_div.hasClass('active')){
			var language_check_field = $(this).parents('.language-form-check');
			var field_id = language_check_field.find('.form-check-input').attr('id');
			field_id = field_id.split('language-service-')[1];
			
			var new_language_check_field = language_check_field.clone();
			
			new_language_check_field.removeClass('checked')
				.find('.form-check-input').attr('id', 'language-service-'+(Number(field_id)+1)).attr('checked', false).prop('checked', false)
				.next('.form-check-label').attr('for', 'language-service-'+(Number(field_id)+1))
				.parent().find('#language-selector-box').hide();

			new_language_check_field.find('.form-check-input').click(function(){
				input_check_click($(this));
			});

			new_language_check_field.appendTo($(this).parents('.optional-services'));
			
			lang_div.addClass('active');
			language_check_field.find('.form-check-input').val(lang_div.attr('value')).attr('checked', 'checked').prop('checked', 'checked');
			language_check_field.find('.form-check-label').html(lang_div.attr('value'));
			language_check_field.find('#language-selector-box').fadeOut();

			if(!language_check_field.hasClass('base-lang')){
				selected_languages.push(lang_div.attr('value'));
			}
		}
	});
	
	//controla el evento click en los checks del formulario de resumen al final
	var input_check_click = (checkbox_input) => {

		var total_price = Number($('.total-price').val());
		var base_price = Number($('.base-price').val());
		var service_price_str = checkbox_input.parents('.optional-services').next().find('input').val();

		var service_price = Number(0);

		if(service_price_str.indexOf('%') == -1){
			service_price = Number(service_price_str);
		}else{
			service_price_str = service_price_str.replace('%', '');
			service_price = (base_price * service_price_str)/100;
		}

		var prices = [];
		$('.optional-services .form-check-input').each(function(){
			if($(this).is(':checked')){
				if($(this).parent().hasClass('language-form-check')){
					var p = $(this).parents('.optional-services').next().find('input').val();
					var p_str = p.replace('%', '');
					p = (base_price * p_str)/100;
					prices.push(p);
				}else{
					prices.push(Number($(this).parents('.optional-services').next().find('input').val()));
				}
			}	
		});

		$('.field-services-prices').val(prices);

		if(checkbox_input.is(':checked')){//marco o activo el idioma
			
			if(checkbox_input.parents('.custom-box').length){
				var custom_box = checkbox_input.parents('.optional-services').find('.custom-box-container');
				custom_box.fadeIn();
			}

			if(checkbox_input.parent().next('.update-form').length){
				$('.products-qty').val(checkbox_input.parent().next('.update-form').find('input').val());
			}

			checkbox_input.parent().addClass('checked');
			total_price = total_price + service_price;
			$('.total-price').val(total_price);
			// $('.display-total-price').html(new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(total_price));
			$('.display-total-price').html('$'+total_price);
			var advanced_payment = $('.display-first-price').attr('price');
			$('.remainder-price').html(total_price-advanced_payment);

			if(checkbox_input.parent().hasClass('language-form-check')){
				var c = 0;
				checkbox_input.parents('.optional-services').find('.form-check-input').each(function(){
					if($(this).is(':checked')){
						c++;
					}
				});
				checkbox_input.parent().find('#language-selector-box').find('.lang-div').removeClass('active');
				checkbox_input.parent().find('#language-selector-box').find('.lang-div').each(function(){
					if(selected_languages.indexOf($(this).attr('value')) != -1){
						$(this).addClass('active');
					}
				});
				// console.log(selected_languages);
				checkbox_input.parent().find('#language-selector-box').fadeIn();
				checkbox_input.parents('.optional-services').next().find('h4').html('$'+(service_price*c));
			}else{
				checkbox_input.parents('.optional-services').next().find('h4').html('$'+(service_price));
			}
			
			checkbox_input.parents('.optional-services').next().find('h4').removeClass('d-none');
			checkbox_input.parent().next().addClass('show');

			gsap.to(checkbox_input.parents('.optional-services').next().find('h4'), .3, {background: '#FFFABC', ease: Power2.easeOut, onComplete: function() {
				gsap.to(checkbox_input.parents('.optional-services').next().find('h4'), .5, {background: 'transparent', ease: Power2.easeOut, onComplete: function() {}});	
			}});

			gsap.to($('.display-total-price'), .3, {background: '#FFFABC', ease: Power2.easeOut, onComplete: function() {
				gsap.to($('.display-total-price'), .5, {background: 'transparent', ease: Power2.easeOut, onComplete: function() {}});	
			}});
		}else{//desmarco o desactivo el idioma
			if(checkbox_input.parent().next('.update-form').length){
				$('.products-qty').val(checkbox_input.parent().next('.update-form').find('input').attr('min'));
			}

			if(checkbox_input.parents('.custom-box').length){
				var custom_box = checkbox_input.parents('.optional-services').find('.custom-box-container');
				custom_box.fadeOut();
			}

			checkbox_input.parent().removeClass('checked');
			total_price = total_price - service_price;
			$('.total-price').val(total_price);
			// $('.display-total-price').html(new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(total_price));
			$('.display-total-price').html('$'+total_price);
			var advanced_payment = $('.display-first-price').attr('price');
			$('.remainder-price').html(total_price-advanced_payment);

			if(checkbox_input.parent().hasClass('language-form-check')){

				if(checkbox_input.val() != undefined && checkbox_input.val() != '' && !checkbox_input.parent().hasClass('base-lang')){
					selected_languages.splice(selected_languages.indexOf(checkbox_input.val()), 1);
				}else{
					checkbox_input.parent().find('#language-selector-box').fadeOut();
				}

				var c = 0;
				checkbox_input.parents('.optional-services').find('.form-check-input').each(function(){
					if($(this).is(':checked')){
						c++;
					}
				});
				if(c == 0){
					checkbox_input.parents('.optional-services').next().find('h4').addClass('d-none');
					checkbox_input.parent().next().removeClass('show');
				}else{
					checkbox_input.parents('.optional-services').next().find('h4').html('$'+(service_price*c));
				}
			}else{
				checkbox_input.parents('.optional-services').next().find('h4').addClass('d-none');
				checkbox_input.parent().next().removeClass('show');
			}

			gsap.to($('.display-total-price'), .3, {background: '#FFFABC', ease: Power2.easeOut, onComplete: function() {
				gsap.to($('.display-total-price'), .5, {background: 'transparent', ease: Power2.easeOut, onComplete: function() {}});	
			}});

			if(checkbox_input.parent().hasClass('language-form-check') && !checkbox_input.parent().hasClass('base-lang')){
				checkbox_input.parent().find('.lang-div').each(function() {
					if($(this).attr('value') == checkbox_input.val()){
						$(this).removeClass('active');
					}
				});
				checkbox_input.val('').attr('checked', false).prop('checked', false);
				checkbox_input.next().html(my_ajax_obj.other);
				checkbox_input.parent().find('#language-selector-box').fadeOut();

				var checked_fields = checkbox_input.parents('.optional-services').find('.language-form-check:not(.checked):not(.base-lang)');
				// console.log(checked_fields);
				if(checked_fields.length > 1){
					checkbox_input.parent().remove();
				}
			}
		}
	};

	//aqui se asigna la funcion anterior al evento click
	$('.optional-services .form-check-input').each(function(){
		$(this).click(function(){
			input_check_click($(this));
		})
	});

	//para rellenar los campos del formulario de resumen con los datos seleccionados
    var fillup_resume_fields = () => {
		
		$('.resume-field').each(function(){
			var resume_field = $(this);
			resume_field.html('');

			var field = $('#field-'+resume_field.attr('field'));

			if(resume_field.hasClass('url')){
				if(field.val() != ''){
					resume_field.html(field.val());	
					resume_field.prop('href', field.val());
					resume_field.attr('href', field.val());
				}
			}else if(resume_field.hasClass('text') || resume_field.hasClass('textarea')){
				var values = $('input[id^="field-'+resume_field.attr('field')+'-"]');
				
				if(values.length){
					values.each(function(){
						if($(this).val() != ''){
							$('<li>'+$(this).val()+'</li>').appendTo(resume_field);
						}
					});
				}else{
					if(field.val() != undefined && field.val() != ''){
						resume_field.html('<p>'+(field.val()).linkify()+'</p>');
					}
				}
			}else if(resume_field.hasClass('uploads')){//aki muestro los thumbs de las imagenes subidas del paso 1 (el logo)
				var uploaded_files = uploaderStep1Files.getUploadedFiles();
				uploaded_files.forEach(function(file){
					const reader = new FileReader();
					reader.onload = (event) => {
						const icon = fileTypeIcons[file.type] || 'default.svg';
						const imagesTypes = ['image/jpeg', 'image/png', 'image/gif'];
        				const videosTypes = ['video/mp4', 'video/mov'];

						var previewHtml = '';

						if(imagesTypes.includes(file.type)){
							previewHtml = `
								<div class="img-preview-container">
									<a href="${event.target.result}" data-fancybox="image" target="_blank">
										<img src="${event.target.result}" class="img-preview" alt="">
										<small>${truncateString(file.name, 18)}</small>
									</a>
								</div>
							`;
							resume_field.append(previewHtml);
				
							Fancybox.bind('[data-fancybox="image"]', {
								hideScrollbar: false,
								Thumbs: false,
								Carousel: {
									transition: "fade",
								},
								Toolbar: {
									display: {
										left: [],
										middle: [],
										right: ["close"],
									},
								},
							});
						}else if(videosTypes.includes(file.type)){

							file.data = event.target.result;
				
							const fileURL = URL.createObjectURL(file);
							const windowWidth = jQuery(window).width();
				
							var dataFB = '';
				
							if(windowWidth > 1200){
								dataFB = 'data-fancybox="video" data-width="1024" data-height="720"';
							}else if(windowWidth > 768){
								dataFB = 'data-fancybox="video" data-width="992" data-height="620"';
							}else{
								dataFB = 'data-fancybox="video" data-width="640" data-height="360"';
							}
				
							previewHtml = `
								<div class="img-preview-container">
									<a href="${fileURL}" ${dataFB} target="_blank">
										<video class="img-preview" src="${fileURL}" autoplay loop muted></video>
										<small>${this.truncateString(file.name, 18)}</small>
									</a>
								</div>
							`;
							resume_field.append(previewHtml);
				
							Fancybox.bind('[data-fancybox="video"]', {
								hideScrollbar: false,
								Thumbs: false,
								defaultType: 'iframe',
								Carousel: {
									transition: "fade",
								},
								Toolbar: {
									display: {
										left: [],
										middle: [],
										right: ["close"],
									},
								},
							});
						}else{
							const previewHtml = `
								<div class="img-preview-container">
									<a href="${event.target.result}" target="_blank">
										<img src="${my_ajax_obj.base_url}/wp-content/themes/tiendas/img/${icon}" class="img-preview icon" alt="">
										<small>${truncateString(file.name, 18)}</small>
									</a>
								</div>
							`;
							resume_field.append(previewHtml);
						}
					};

					reader.readAsDataURL(file);
				});
			}else if(resume_field.hasClass('checkboxes')){
				var checkboxes = $('input[id^="field-'+resume_field.attr('field')+'-"]');
				if(checkboxes.length){
					checkboxes.each(function(){
						if($(this).is(":checked")){
							$('<li>'+$(this).val()+'</li>').appendTo(resume_field);
						}
					});
				}
			}else if(resume_field.hasClass('radios')){
				var radios = $('input[id^="field-'+resume_field.attr('field')+'-"]');
				if(radios.length){
					radios.each(function(){
						if($(this).is(":checked")){
							$('<li>'+$(this).val()+'</li>').appendTo(resume_field);
						}
					});
				}
			}else if(resume_field.hasClass('storeProducts')){
				var allproducts = $('.products-list-container');
				if(allproducts.length){
					allproducts.children('li.product-item').each(function(){
						$('<li>'+$(this).find('h3').find('a').html()+'</li>').appendTo(resume_field);
					});
				}
			}
		});

		$('.resume-form .form-item').each(function(){
			var form_item = $(this);
			var fields = [];

			$('.resume-box').find('.bordered-box-inner').each(function(){
				if($(this).find('.resume-field').length == 0){
					$(this).parent().hide();
				}
			});

			if(form_item.find('.resume-field').length){
				form_item.find('.resume-field').each(function(index){
					var resume_field = $(this);	
					if(!resume_field.hasClass('uploads')){
						if(resume_field.html() != ''){
							fields.push({
								'id': resume_field.attr('field'),
								'empty': false
							});
						}else{
							fields.push({
								'id': resume_field.attr('field'),
								'empty': true
							});

							$('#field-'+resume_field.attr('field')).remove();
						}
					}else{
						var uploaded_files = uploaderStep1Files.getUploadedFiles();
						// console.log(uploaded_files.length);
						if(uploaded_files.length <= 0){
							fields.push({
								'id': resume_field.attr('field'),
								'empty': true
							});
							$('#field-'+resume_field.attr('field')).remove();
						}else{
							fields.push({
								'id': resume_field.attr('field'),
								'empty': false
							});
						}
					}
				});

				var remove = true;
				fields.forEach(field => {
					if(!field.empty){
						remove = false;
					}
				});

				if(remove){
					form_item.remove();
				}
			}
		});

		// var grid = $('.masonry-container').masonry({
		// 	itemSelector: '.form-item',
		// 	columnWidth: 80,
		// 	percentPosition: true,
		// });
		// setTimeout(() => {
		// 	grid.masonry('layout');
		// 	grid.masonry('reloadItems')
		// }, 100);
	}

	// Handle the popstate event
    $(window).on('popstate', function(event) {
        // if (event.originalEvent.state) {
		// 	var h = visit_url.split('?step=')[0];

		// 	poppedstate = true;

		// 	if(currentStep > event.originalEvent.state.step){ // Step back
		// 		currentStep = event.originalEvent.state.step;
		// 		$('#step-1-form-'+(currentStep + 1)).find('.step-back').trigger('click');
		// 	}else if(currentStep < event.originalEvent.state.step){//step forward
		// 		currentStep = event.originalEvent.state.step;
		// 		if($('#step-1-form-'+(currentStep - 1)).find('.btn-next').length > 1){
		// 			$($('#step-1-form-'+(currentStep - 1)).find('.btn-next')[0]).trigger('click');
		// 		}else{
		// 			$('#step-1-form-'+(currentStep - 1)).find('.btn-next').trigger('click');
		// 		}
		// 	}
		// 	history.replaceState({ step: currentStep }, '', h+'?step=' + currentStep); // Update the URL
        // }
		event.preventDefault();
		event.stopPropagation();
    });

	//aqui cargo las categorias agregadas en el paso de las categorias al selector de categorias del paso de los productos
	// $('.btn-next[href="#step-1-form-8"]').click(function(){
	// 	categoryList = [];
	// 	$('#field-fromPCProductCategory').html('');
	// 	$('#field-fromLinkProductCategory').html('');
	// 	$('.category-repeater-fields').find('.form-control').each(function(){
	// 		if($(this).val() != ''){
	// 			categoryList.push($(this).val());
	// 		}
	// 	});
	// 	categoryList.forEach((item) => {
	// 		$('#field-fromPCProductCategory').append('<option value="'+item+'">'+item+'</option>');
	// 		$('#field-fromLinkProductCategory').append('<option value="'+item+'">'+item+'</option>');
	// 	});
	// });

	//al clickar cualquier boton que sea para ir al siguiente paso
	$('a[href^="#step-1-form-"]:not(.step-back)').click(function(e){//forward function
		e.preventDefault();

		if(poppedstate){
			poppedstate = false;
		}else{
			if(!stepLoaded){
				var h = visit_url.split('?step=')[0];
				currentStep++;
				// history.pushState({ step: currentStep }, '', h+'?step=' + currentStep); // Update the URL
			}else{
				stepLoaded = false;
			}
		}

		var href = $(this).attr('href').replace('#', '');
        $(this).parents('.sub-step').hide().removeClass('current');

		var btn = $(this);
		if(btn.find('.option-title').length > 0){
			$('.selected-scenery').val(btn.find('.option-title').html());
			selectedStage = btn.parent('.stage').attr('id');
			
			$('.use-progress-bar').each(function(){
				if($(this).attr('stage').indexOf(selectedStage) == -1 && $(this).attr('stage') != 'ALL'){
					$(this).removeClass('use-progress-bar');
				}
			});
		}

		if($('#'+href).attr('stage') != 'ALL'){
			while($('#'+href).attr('stage').indexOf(selectedStage) == -1 && $('#'+href).attr('stage') != 'ALL'){//no existe
				href = $('#'+href).next().attr('id');
			}
		}

		updateProgressBar(href);

		if($('#'+href).hasClass('resume-form')){
			fillup_resume_fields();
		}

		if($('#'+href).hasClass('end-form')){
			confirm_without_payment($('#'+href));
		}

		$('#'+href).fadeIn().addClass('current');

		if(btn.hasClass('btn-edit')){
			$('#'+href).find('.box-continue').hide();
			$('#'+href).find('.box-save').show();
			$('#'+href).find('.btn-save').attr('href', '#'+btn.parents('.resume-form').attr('id')).prop('href', '#'+btn.parents('.resume-form').attr('id'));
		}else{
			if($('#'+href).hasClass('use-progress-bar')){

				var step = Number($('#'+href).attr('step'));

				if($('.progressbar .on').attr('percent') == 0){
					$('.progressbar .on').attr('percent', step);
					$('.progressbar .off').attr('percent', (100-step));
				}

				var on = Number($('.progressbar .on').attr('percent'));

				var progressPosition = 0;

				$('.use-progress-bar').each(function(i){
					if($(this).hasClass('current')){
						progressPosition = i + 1;
					}
				});

				$('.progressbar .on').width((step * progressPosition)+'%');
				$('.progressbar .off').width((100 - (step * progressPosition))+'%');

				$('.progressbar .on').attr('percent', (step * progressPosition));
				$('.progressbar .off').attr('percent', (100 - (step * progressPosition)));

				on = Number($('.progressbar .on').attr('percent'));

				if(on > 95){
					$('.progressbar').addClass('checked');
					
					$('.progressbar .on').attr('percent', 100);
					$('.progressbar .off').attr('percent', 0);

					$('.progressbar .on').width('100%');
					$('.progressbar .off').width('0%');
				}

				if(!$('.progressbar').hasClass('show')){
					$('.progressbar').addClass('show').fadeIn();
				}
			}else{
				$('.progressbar .on').attr('percent', 0);
				$('.progressbar .off').attr('percent', 100);
				$('.progressbar').removeClass('show').fadeOut();
			}
		}

		$('#'+href+' .field').each(function(){
			$(this).find('.form-control').focus();
			return false;
		});
	});

	//para regresar al form anterior
	$('.step-back').click(function(e){//backward function
		e.preventDefault();

		if(poppedstate){
			poppedstate = false;
		}else{
			var h = visit_url.split('?step=')[0];
			currentStep--;
			// history.pushState({ step: currentStep }, '', h+'?step=' + currentStep); // Update the URL
		}

		var href = $(this).attr('href').replace('#', '');
		$(this).parents('.sub-step').hide().removeClass('current');

		var btn = $(this);
		if(btn.find('.option-title').length > 0){
			$('.selected-scenery').val(btn.find('.option-title').html());
			selectedStage = btn.parent('.stage').attr('id');
			
			$('.use-progress-bar').each(function(){
				if($(this).attr('stage').indexOf(selectedStage) == -1 && $(this).attr('stage') != 'ALL'){
					$(this).removeClass('use-progress-bar');
				}
			});
		}

		if($('#'+href).attr('stage') != 'ALL'){
			while($('#'+href).attr('stage').indexOf(selectedStage) == -1 && $('#'+href).attr('stage') != 'ALL'){//no existe
				href = $('#'+href).prev().attr('id');
			}
		}

		updateProgressBar(href);

		if($('#'+href).hasClass('resume-form')){
			fillup_resume_fields();
		}

		$('#'+href).fadeIn().addClass('current');

		if(btn.hasClass('btn-edit')){
			$('#'+href).find('.box-continue').hide();
			$('#'+href).find('.box-save').show();
			$('#'+href).find('.btn-save').attr('href', '#'+btn.parents('.resume-form').attr('id')).prop('href', '#'+btn.parents('.resume-form').attr('id'));
		}else{
			if($('#'+href).hasClass('use-progress-bar')){

				var step = Number($('#'+href).attr('step'));

				if($('.progressbar .on').attr('percent') == 0){
					$('.progressbar .on').attr('percent', step);
					$('.progressbar .off').attr('percent', (100-step));
				}

				var on = Number($('.progressbar .on').attr('percent'));

				var progressPosition = 0;

				$('.use-progress-bar').each(function(i){
					if($(this).hasClass('current')){
						progressPosition = i + 1;
					}
				});

				$('.progressbar .on').width((step * progressPosition)+'%');
				$('.progressbar .off').width((100 - (step * progressPosition))+'%');

				$('.progressbar .on').attr('percent', (step * progressPosition));
				$('.progressbar .off').attr('percent', (100 - (step * progressPosition)));

				on = Number($('.progressbar .on').attr('percent'));

				if(on > 95){
					$('.progressbar').addClass('checked');
					
					$('.progressbar .on').attr('percent', 100);
					$('.progressbar .off').attr('percent', 0);

					$('.progressbar .on').width('100%');
					$('.progressbar .off').width('0%');
				}

				if(!$('.progressbar').hasClass('show')){
					$('.progressbar').addClass('show').fadeIn();
				}
			}else{
				$('.progressbar .on').attr('percent', 0);
				$('.progressbar .off').attr('percent', 100);
				$('.progressbar').removeClass('show').fadeOut();
			}
		}

		$('#'+href+' .field').each(function(){
			$(this).find('.form-control').focus();
			return false;
		});
	});

	// Show the initial step
	var URI_ARRAY = visit_url.split('?step=');
	var h = URI_ARRAY[0];
	var opt = '';

	if(URI_ARRAY.length == 2){
		if(getQueryVariable('opt')){
			currentStep = 2;
			opt = getQueryVariable('opt');
		}

		if(currentStep == 2){
			stepLoaded = true;
			// history.replaceState({ step: currentStep }, '', h+'?step='+currentStep); // Update the URL
		
			var scenery = '';
			$('#step-1-form-2').find('ul').find('li').each(function(i){
				if(i == opt){
					scenery = $(this).find('.option-title').html();
					selectedStage = 'stage-'+(Number(opt)+1);
				}
			});
			$('.selected-scenery').val(scenery);

			$('.use-progress-bar').each(function(){
				if($(this).attr('stage').indexOf(selectedStage) == -1 && $(this).attr('stage') != 'ALL'){
					$(this).removeClass('use-progress-bar');
				}
			});

			var href = 'step-1-form-3';

			if($('#'+href).attr('stage') != 'ALL'){
				while($('#'+href).attr('stage').indexOf(selectedStage) == -1 && $('#'+href).attr('stage') != 'ALL'){//no existe
					href = $('#'+href).next().attr('id');
				}
			}

			//form step 1
			$('#form-step-1').find('.sub-step').hide();
			$('#'+href).show().addClass('current');

			updateProgressBar(href);
		}else{
			currentStep = 1;
			// history.replaceState({ step: currentStep }, '', h+'?step='+currentStep); // Update the URL
		}
	}else{
		// history.replaceState({ step: currentStep }, '', h+'?step='+currentStep); // Update the URL
	}

	//guardar los cambios cuando editamos algun dato en el penultimo form
	$('.btn-save').click(function(e){
		e.preventDefault();

		var href = $(this).attr('href').replace('#', '');
        $(this).parents('.sub-step').hide();

		var btn = $(this);

		if($('#'+href).hasClass('resume-form')){
			fillup_resume_fields();
		}

		$('#'+href).fadeIn();

		if(btn.find('.option-title').length > 0){
			$('.selected-scenery').val(btn.find('.option-title').html());
		}

		if(btn.hasClass('btn-edit')){
			$('#'+href).find('.box-continue').hide();
			$('#'+href).find('.box-save').show();
			$('#'+href).find('.btn-save').attr('href', '#'+btn.parents('.resume-form').attr('id')).prop('href', '#'+btn.parents('.resume-form').attr('id'));
		}else{
			if($('#'+href).hasClass('use-progress-bar')){

				var step = Number($('#'+href).attr('step'));

				if($('.progressbar .on').attr('percent') == 0){
					$('.progressbar .on').attr('percent', step);
					$('.progressbar .off').attr('percent', (100-step));
				}

				var on = Number($('.progressbar .on').attr('percent'));
				var off = Number($('.progressbar .off').attr('percent'));

				if($(this).hasClass('step-back')){
					$('.progressbar .on').width((on-step)+'%');
					$('.progressbar .off').width((off+step)+'%');

					$('.progressbar .on').attr('percent', (on-step));
					$('.progressbar .off').attr('percent', (off+step));
				}else{
					$('.progressbar .on').width((on+step)+'%');
					$('.progressbar .off').width((off-step)+'%');

					$('.progressbar .on').attr('percent', (on+step));
					$('.progressbar .off').attr('percent', (off-step));
				}

				on = Number($('.progressbar .on').attr('percent'));
				if(on > 95){
					$('.progressbar').addClass('checked');
				}

				if(!$('.progressbar').hasClass('show')){
					$('.progressbar').addClass('show').fadeIn();
				}
			}else{
				$('.progressbar .on').attr('percent', 0);
				$('.progressbar .off').attr('percent', 100);
				$('.progressbar').removeClass('show').fadeOut();
			}
		}

		$('#'+href+' .field').each(function(){
			$(this).find('.form-control').focus();
			return false;
		});
	});

	var fields_count = 1;
	var checked_fields = 0;

	//aqui se controla todo lo referente al paso de los productos
	$('.sub-step:not(.tabs-step-3)').each(function(){

		var substep = $(this);

		substep.find('.field').each(function(){

			var field = $(this);
			
			$(this).find('.form-checks').each(function(){
				var check = $(this);

				check.find('input').click(function(){
					if($(this).is(':checked')){
						checked_fields++;
					}else{
						checked_fields--;
					}

					if(checked_fields == field.attr('max-selected')){
						field.find('input:not(:checked)').attr('disabled', 'disabled');
					}else{
						field.find('input:not(:checked)').removeAttr('disabled');
					}
				});

				if(check.hasClass('has-more')){
					check.click(function(){
						var checkbox = $(this).find('input');
						var more = $(this).find('textarea');
						if(checkbox.is(':checked')){
							more.fadeIn().focus();
						}else{
							more.fadeOut();
						}
					});
				}
			});

		});

		substep.find('.btn-plus-categ').click(function(e){
			e.preventDefault();

			var btnplus = $(this);

			var repeater_field = substep.find('.category-repeater-fields').find('.repeater-field');
			
			var field = $('<div class="repeater-field mb-4">'+$(repeater_field[0]).html()+'</div>');

			field.find('.field').each(function(){
				var id = $(this).find('.form-control').attr('id');
				id = id.replace('-0', '-'+(repeater_field.length));
				$(this).find('.form-control').attr('id', id);

				var label = $(this).find('.form-label');
				var label_for = label.attr('for').replace('-0', '-'+(repeater_field.length));
				var label_text = my_ajax_obj.category+' '+(repeater_field.length + 1)+':';
				$(this).find('.form-label').attr('for', label_for).html(label_text);

			});

			substep.find('.category-repeater-fields').append('<div class="repeater-field mb-4">'+field.html()+'</div>');

			substep.find('.category-repeater-fields').find('.repeater-field:last-child').find('input[type="text"]').focus();

			if(repeater_field.length + 1 == 10){
				btnplus.hide();
			}
		});

		substep.find('.btn-plus').click(function(e){
			e.preventDefault();

			var btnplus = $(this);

			var repeater_field = substep.find('.repeater-fields').find('.repeater-field');
			
			if(repeater_field.length == 1){
				var field = $('<div class="repeater-field mb-4">'+repeater_field.html()+'</div>');

				// console.log(field);

				field.find('.field').each(function(){
					var id = $(this).find('.form-control').attr('id');
					id = id.replace('-0', '-'+fields_count);
					$(this).find('.form-control').attr('id', id);

					var classes = $(this).find('.form-control').attr('class');
					classes = classes.replace('verified', 'notverified');
					$(this).find('.form-control').attr('class', classes);

					var label = $(this).find('.form-label').attr('for');
					label = label.replace('-0', '-'+fields_count);
					$(this).find('.form-label').attr('for', label);
				});

				substep.find('.repeater-fields').append('<div class="repeater-field mb-4">'+field.html()+'</div>');
				fields_count++;
			}else{
				var field = $('<div class="repeater-field mb-4">'+$(repeater_field[0]).html()+'</div>');

				// console.log(field);

				field.find('.field').each(function(){
					var id = $(this).find('.form-control').attr('id');
					id = id.replace('-0', '-'+fields_count);
					$(this).find('.form-control').attr('id', id);

					var classes = $(this).find('.form-control').attr('class');
					classes = classes.replace('verified', 'notverified');
					$(this).find('.form-control').attr('class', classes);

					var label = $(this).find('.form-label').attr('for');
					label = label.replace('-0', '-'+fields_count);
					$(this).find('.form-label').attr('for', label);

				});

				substep.find('.repeater-fields').append('<div class="repeater-field mb-4">'+field.html()+'</div>');
				fields_count++;
			}

			setTimeout(function(){
				var new_repeater_field = substep.find('.repeater-fields');

				new_repeater_field.find('.form-control').each(function(){

					$(this).unbind('keyup');
					$(this).keyup(function(){
						var form_control = $(this);

						if(form_control.hasClass('required')){
							if(form_control.val() != ''){
								if(form_control.hasClass('validate-url')){
									if(isValidUrl(form_control.val())){
										if(!form_control.hasClass('verified')){
											form_control.addClass('verified').removeClass('notverified');
										}
										form_control.removeClass('notverified');
										form_control.next('.error').html('');
									}else{
										form_control.removeClass('verified').addClass('notverified');
										form_control.next('.error').html(my_ajax_obj.valid_url);
									}
								}else if(form_control.hasClass('validate-email')){
									if(isValidEmail(form_control.val())){
										if(!form_control.hasClass('verified')){
											form_control.addClass('verified').removeClass('notverified');
										}
										form_control.removeClass('notverified');
										form_control.next('.error').html('');

										//aki verifico si el email es unico
										if(form_control.hasClass('unique')){
											$.post(my_ajax_obj.ajax_url, {action: 'ws', wsa: 'verify_user', useremail: form_control.val()}, function(response){
												if(response.exists == true){
													form_control.removeClass('verified').addClass('notverified');
													form_control.next('.error').html(my_ajax_obj.store_registered);
													form_control.parents('.sub-step').find('.btn-next').addClass('disabled');
												}
											});
										}
									}else{
										form_control.removeClass('verified').addClass('notverified');
										form_control.next('.error').html(my_ajax_obj.valid_url);
									}
								}else{
									if(!form_control.hasClass('verified')){
										form_control.addClass('verified').removeClass('notverified');
									}
									form_control.removeClass('notverified');
								}
							}else{
								form_control.removeClass('verified').addClass('notverified');
								if(form_control.hasClass('validate-url')){
									form_control.next('.error').html(my_ajax_obj.valid_url);
								}
								if(form_control.hasClass('validate-email')){
									form_control.next('.error').html(my_ajax_obj.valid_email);
								}
							}
						}
		
						if(form_control.parents('.sub-step').find('.notverified').length == 0){
							form_control.parents('.sub-step').find('.btn-next').removeClass('disabled');
							form_control.parents('.sub-step').find('.btn-plus').removeClass('disabled');
						}else{
							form_control.parents('.sub-step').find('.btn-next').addClass('disabled');
							form_control.parents('.sub-step').find('.btn-plus').addClass('disabled');
						}
					})
				});

				if(btnplus.parents('.sub-step').find('.notverified').length == 0){
					btnplus.parents('.sub-step').find('.btn-next').removeClass('disabled');
					btnplus.removeClass('disabled');
				}else{
					btnplus.parents('.sub-step').find('.btn-next').addClass('disabled');
					btnplus.addClass('disabled');
				}
			}, 100);
		});

		substep.find('.field').each(function(){

			var field = $(this);

			field.find('.form-radio-input').each(function(){

				// if($(this).attr('name') == 'field-storeShopifyAccount' || $(this).attr('name') == 'field-storeActualShopifyAccount'){
				// 	$(this).click(function(){
				// 		var form_radio = $(this);
				// 		if(form_radio.is(':checked')){
				// 			form_radio.parents('.field').next().fadeIn();

				// 			form_radio.parents('.box').find('.form-tip').fadeOut();
				// 		}
				// 	});
				// }

				$(this).click(function(){

					var form_radio = $(this);

					if(form_radio.hasClass('required')){
						if(form_radio.is(":checked")){
							field.find('.form-radio-input').removeClass('notverified');
						}
					}

					if(form_radio.parents('.sub-step').find('.notverified').length == 0){
						if(!form_radio.parents('.sub-step').find('.btn-next').hasClass('btn-products')){
							form_radio.parents('.sub-step').find('.btn-next').removeClass('disabled');
						}
						form_radio.parents('.sub-step').find('.btn-plus').removeClass('disabled');
						form_radio.parents('.sub-step').find('.btn-plus-categ').removeClass('disabled');
					}else{
						if(!form_radio.parents('.sub-step').find('.btn-next').hasClass('btn-products')){
							form_radio.parents('.sub-step').find('.btn-next').addClass('disabled');
						}
						form_radio.parents('.sub-step').find('.btn-plus').addClass('disabled');
						form_radio.parents('.sub-step').find('.btn-plus-categ').addClass('disabled');
					}

					var radio_name = form_radio.attr('name');
					var radio_target = form_radio.attr('target');

					if(radio_name == 'field-storeShopifyAccount'){
						form_radio.parents('.form-box').find('.btn.btn-next').attr('href', '#'+radio_target);
					}

					if(form_radio.parent('.form-radio').hasClass('has-more')){
						$('.more-info-block').hide();
						var more_id = form_radio.attr('id') + '-more';
						var more = $('.more-info-block#'+more_id);
						if(form_radio.is(':checked')){
							more.fadeIn().focus();
						}else{
							more.fadeOut();
						}
					}

				});
			});

            field.find('.form-control').keyup(function(){

				var form_control = $(this);

				if(form_control.hasClass('required')){
					if(form_control.val() != ''){
						if(form_control.hasClass('validate-url')){
							if(isValidUrl(form_control.val())){
								if(!form_control.hasClass('verified')){
                                    form_control.addClass('verified').removeClass('notverified');
                                }
                                form_control.removeClass('notverified');
								form_control.next('.error').html('');
							}else{
								form_control.removeClass('verified').addClass('notverified');
								form_control.next('.error').html(my_ajax_obj.valid_url);
							}
						}else if(form_control.hasClass('validate-email')){
							if(isValidEmail(form_control.val())){
								if(!form_control.hasClass('verified')){
                                    form_control.addClass('verified').removeClass('notverified');
                                }
                                form_control.removeClass('notverified');
								form_control.next('.error').html('');
								
								//aki verifico si el email es unico
								if(form_control.hasClass('unique')){
									$.post(my_ajax_obj.ajax_url, {action: 'ws', wsa: 'verify_user', useremail: form_control.val()}, function(response){
										if(response.exists == true){
											form_control.removeClass('verified').addClass('notverified');
											form_control.next('.error').html(my_ajax_obj.store_registered);
											form_control.parents('.sub-step').find('.btn-next').addClass('disabled');
										}
									});
								}
							}else{
								form_control.removeClass('verified').addClass('notverified');
								form_control.next('.error').html(my_ajax_obj.valid_email);
							}
						}else{
							if(!form_control.hasClass('verified')){
								form_control.addClass('verified').removeClass('notverified');
							}
							form_control.removeClass('notverified');
						}
					}else{
						form_control.removeClass('verified').addClass('notverified');
						if(form_control.hasClass('validate-url')){
							form_control.next('.error').html(my_ajax_obj.valid_url);
						}
						if(form_control.hasClass('validate-email')){
							form_control.next('.error').html(my_ajax_obj.valid_email);
						}
					}
				}

				if(form_control.parents('.sub-step').find('.notverified').length == 0){
					if(!form_control.parents('.sub-step').find('.btn-next').hasClass('btn-products')){
						form_control.parents('.sub-step').find('.btn-next').removeClass('disabled');
					}
					form_control.parents('.sub-step').find('.btn-plus').removeClass('disabled');
					form_control.parents('.sub-step').find('.btn-plus-categ').removeClass('disabled');
				}else{
					if(!form_control.parents('.sub-step').find('.btn-next').hasClass('btn-products')){
						form_control.parents('.sub-step').find('.btn-next').addClass('disabled');
					}
					form_control.parents('.sub-step').find('.btn-plus').addClass('disabled');
					form_control.parents('.sub-step').find('.btn-plus-categ').addClass('disabled');
				}
            });

			if(field.parents('.sub-step').find('.notverified').length == 0){
				if(!field.parents('.sub-step').find('.btn-next').hasClass('btn-products')){
					field.parents('.sub-step').find('.btn-next').removeClass('disabled');
				}
				field.parents('.sub-step').find('.btn-plus').removeClass('disabled');
				field.parents('.sub-step').find('.btn-plus-categ').removeClass('disabled');
			}else{
				if(!field.parents('.sub-step').find('.btn-next').hasClass('btn-products')){
					field.parents('.sub-step').find('.btn-next').addClass('disabled');
				}
				field.parents('.sub-step').find('.btn-plus').addClass('disabled');
				field.parents('.sub-step').find('.btn-plus-categ').addClass('disabled');
			}
        });
	});

	//====================================================================================================================================================================================
	//=========================================================AQUI SE REALIZA TODO SOBRE EL FORM DE SUBIR PRODUCTOS DEL PASO 1===========================================================
	//====================================================================================================================================================================================

	$('.products-form').parents('.sub-step').find('.btn-next').addClass('btn-products').addClass('disabled');

	/**
	evento keyup de validacion para el campo nombre de los productos por referencia
	*/
	$('#field-fromLinkProductName').keyup(function(){
		if($(this).val() != '' && isValidUrl($('#field-fromLinkProductLink').val())){
			$('.btn-add-link-product').removeClass('disabled');
		}
	});

	/**
	evento keyup de validacion para el campo enlace de los productos por referencia
	*/
	$('#field-fromLinkProductLink').keyup(function(){
		if(isValidUrl($(this).val())){
			$(this).next('.error').html('');
		}else{
			$(this).next('.error').html(my_ajax_obj.valid_url);
		}

		if($('#field-fromLinkProductName').val() != '' && isValidUrl($(this).val())){
			$('.btn-add-link-product').removeClass('disabled');
		}
	});
	
	/**
	evento click para el form de los productos por referencia, donde se agrega el producto al listado de productos
	*/
	$('.step-1 .btn-add-link-product').click(function(){
		var productName = $('#field-fromLinkProductName').val();
		var productLink = $('#field-fromLinkProductLink').val();
		var productCategory = $('#field-fromLinkProductCategory').val();

		$('<input type="text" class="LinkProductName item-link-'+linkProductsIndex+'" name="field-LinkProductName[]" value="'+productName+'" />').appendTo($('.link-products'));
		$('<input type="text" class="LinkProductLink item-link-'+linkProductsIndex+'" name="field-LinkProductLink[]" value="'+productLink+'" />').appendTo($('.link-products'));
		$('<input type="text" class="LinkProductCategory item-link-'+linkProductsIndex+'" name="field-LinkProductCategory[]" value="'+productCategory+'" />').appendTo($('.link-products'));

		productsQTY++;
		productUID++;

		if(productsQTY > 1){
			$(".products-list-container").find('.product-placeholder').each(function(i){
				if(i == 0){
					$(this).remove();
				}
			});
		}

		var item = '<li class="product-item link-product d-flex align-items-center justify-content-between w-100 py-3 item-link-'+linkProductsIndex+' product-index-'+productUID+'" index="item-link-'+linkProductsIndex+'">'+
			'<span class="counter disabled me-3">'+productsQTY+'</span>'+
			'<div class="me-auto ms-0 d-flex align-items-center justify-content-start gap-3 w-75">'+
				'<img src="'+my_ajax_obj.base_url+'/wp-content/themes/tiendas/img/image-placeholder.png" class="product-thumb me-0 img-fluid" />'+
				'<div class="d-flex flex-column align-items-start justify-content-center w-100">'+
					'<h3 class="d-flex"><a class="link-product-edit" href="javascript:void(0)">'+productName+'</a></h3>'+
				'</div>'+
			'</div>'+
			'<a class="trash me-0 ms-0" href="javascript:void(0)"><img src="'+my_ajax_obj.base_url+'/wp-content/themes/tiendas/img/trash.svg" class="product-trash img-fluid me-3" /></a>'+
		'</li>';

		if(productsQTY > 1){
			//agrego los datos del producto al listado de productos
			$(item).insertAfter($(".product-item").last());
		}else{
			$(item).prependTo($(".products-list-container"));
		}

		$(".products-list-container").find('li').each(function(index){
			$(this).find('.counter').text(index + 1);
		});

		if(productsQTY >= 3){
			$('.products-form').parents('.sub-step').find('.btn-next').removeClass('disabled');
		}

		$('#field-fromLinkProductName').val('');
		$('#field-fromLinkProductLink').val('');

		$('.products-form').hide();
		$('.products-list').fadeIn();

		linkProductsIndex++;
	});

	var pcnameok = false; 
	var pcpriceok = false;
	var pcdescok = false;
	var pcmediaok = true;

	/**
	evento keyup de validacion para el campo nombre de los productos subidos desde la pc
	*/
	$('#field-fromPCProductName').keyup(function(){
		if($(this).val() != ''){
			pcnameok = true;
		}

		if(pcnameok && pcpriceok && pcdescok && pcmediaok){
            $('.btn-add-pc-product').removeClass('disabled');
        }else{
            $('.btn-add-pc-product').addClass('disabled');
        }
	});

	/**
	evento keyup de validacion para el campo precio de los productos subidos desde la pc
	*/
	$('#field-fromPCProductPrice').keyup(function(){
		if($(this).val() != ''){
			pcpriceok = true;
		}else{
			return false;
		}

		if($(this).val() == '.'){
			$(this).val('0.');
		}

		if(pcnameok && pcpriceok && pcdescok && pcmediaok){
            $('.btn-add-pc-product').removeClass('disabled');
        }else{
            $('.btn-add-pc-product').addClass('disabled');
        }
	});

	//aceptar solo numeros
	$('#field-fromPCProductPrice').keydown(function(event){
		if ( event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 27 || event.keyCode == 13 ||
			// Allow: Ctrl+A
		   (event.keyCode == 65 && event.ctrlKey === true) || event.keyCode == 190 ||
			// Allow: home, end, left, right
		   (event.keyCode >= 35 && event.keyCode <= 39)) {
				// let it happen, don't do anything
				return;
	   }
	   else {
		   // Ensure that it is a number and stop the keypress
		   if (event.shiftKey || (event.keyCode < 48 || event.keyCode > 57) && (event.keyCode < 96 || event.keyCode > 105 )) {
			   event.preventDefault();
		   }
	   }
	});

	//agrego un 0 antes del punto
	$('#field-fromPCProductSalePrice').keyup(function(){
		if($(this).val() == '.'){
			$(this).val('0.');
		}
	});

	//aceptar solo numeros
	$('#field-fromPCProductSalePrice').keydown(function(event){
		if ( event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 27 || event.keyCode == 13 ||
			// Allow: Ctrl+A
		   (event.keyCode == 65 && event.ctrlKey === true) || event.keyCode == 190 ||
			// Allow: home, end, left, right
		   (event.keyCode >= 35 && event.keyCode <= 39)) {
				// let it happen, don't do anything
				return;
	   }
	   else {
		   // Ensure that it is a number and stop the keypress
		   if (event.shiftKey || (event.keyCode < 48 || event.keyCode > 57) && (event.keyCode < 96 || event.keyCode > 105 )) {
			   event.preventDefault();
		   }
	   }
	});

	/**
	evento keyup de validacion para el campo descripcion de los productos subidos desde la pc
	*/
	$('#field-fromPCProductDescription').keyup(function(){
		if($(this).val() != ''){
			pcdescok = true;
		}

		if(pcnameok && pcpriceok && pcdescok && pcmediaok){
            $('.btn-add-pc-product').removeClass('disabled');
        }else{
            $('.btn-add-pc-product').addClass('disabled');
        }
	});

	//activar o desactivar el boton de continuar cuando hay menos de 3 productos
	$('#field-fromPCProductLessThanThree').click(function(){
		if($(this).is(":checked")){
			$('.products-form').parents('.sub-step').find('.btn-next').removeClass('disabled');
		}else{
			$('.products-form').parents('.sub-step').find('.btn-next').addClass('disabled');
		}
	});

	/**
	evento click para el form de los productos subidos desde pc, donde se agrega el producto al listado de productos
	*/
	$('.step-1 .btn-add-pc-product').click(function(){
		//obtengo los datos de los inputs
		var productName = $('#field-fromPCProductName').val();
		var productCurrency = $('#field-fromPCProductCurrecy').val();
		var productPrice = $('#field-fromPCProductPrice').val();
		var productSalePrice = $('#field-fromPCProductSalePrice').val();
		var productDesc = $('#field-fromPCProductDescription').val();
		var productDesc = $('#field-fromPCProductDescription').val();
		var productCategory = $('#field-fromPCProductCategory').val();
		const productMedia = document.getElementById('field-fromPCProductMedia').files;
		
		// Create a new input field for product media
		const newInput = $('<input type="file" class="PCProductMedia item-pc-' + pcProductsIndex + '" pc-product-index="' + pcProductsIndex + '" id="field-PCProductMedia-' + pcProductsIndex + '" name="field-PCProductMedia[' + pcProductsIndex + '][]" />');
    
		// Append the new input to the product list
		newInput.appendTo($('.pc-products'));
	
		// Create a new DataTransfer object to hold the files for this input
		const dataTransfer = new DataTransfer();
		for (let i = 0; i < productMedia.length; i++) {
			dataTransfer.items.add(productMedia[i]); // Add each file to the DataTransfer
		}

		// Assign the DataTransfer's files to the new input
		document.getElementById('field-PCProductMedia-' + pcProductsIndex).files = dataTransfer.files;

		// Store the files in the products list
		pcProductsList[pcProductsIndex] = dataTransfer.files;

		$('<input type="text" class="PCProductName item-pc-'+pcProductsIndex+'" name="field-PCProductName[]" value="'+productName+'" />').appendTo($('.pc-products'));
		$('<input type="text" class="PCProductCurrecy item-pc-'+pcProductsIndex+'" name="field-PCProductCurrecy[]" value="'+productCurrency+'" />').appendTo($('.pc-products'));
		$('<input type="text" class="PCProductPrice item-pc-'+pcProductsIndex+'" name="field-PCProductPrice[]" value="'+productPrice+'" />').appendTo($('.pc-products'));
		$('<input type="text" class="PCProductSalePrice item-pc-'+pcProductsIndex+'" name="field-PCProductSalePrice[]" value="'+productSalePrice+'" />').appendTo($('.pc-products'));
		$('<input type="text" class="PCProductDescription item-pc-'+pcProductsIndex+'" name="field-PCProductDescription[]" value="'+productDesc+'" />').appendTo($('.pc-products'));
		$('<input type="text" class="PCProductCategory item-pc-'+pcProductsIndex+'" name="field-PCProductCategory[]" value="'+productCategory+'" />').appendTo($('.pc-products'));

		var firstMedia = my_ajax_obj.base_url+'/wp-content/themes/tiendas/img/image-placeholder.png';
		var done = false;

		var products_uploaded_files = uploaderStep1ProductsFiles.getUploadedFiles();

		//obtengo la primera imagen subida para mostrar en el listado
		products_uploaded_files.forEach(function(file){
			if(file.index == 'item-pc-'+pcProductsIndex){
				if((file.type == 'image/jpeg' || file.type == 'image/png' || file.type == 'image/gif') && !done){
					firstMedia = file.data;
					done = true;
				}
			}
		});

		productsQTY++;
		productUID++;

		if(productsQTY > 1){
			$(".products-list-container").find('.product-placeholder').each(function(i){
				if(i == 0){
					$(this).remove();
				}
			});
		}

		var item = '<li class="product-item pc-product d-flex align-items-center justify-content-between w-100 py-3 item-pc-'+pcProductsIndex+' product-index-'+productUID+'" index="item-pc-'+pcProductsIndex+'">'+
			'<span class="counter disabled me-3">'+productsQTY+'</span>'+
			'<div class="me-auto ms-0 d-flex align-items-center justify-content-start gap-3 w-75">'+
				'<img src="'+firstMedia+'" class="product-thumb me-0 img-fluid" />'+
				'<div class="d-flex flex-column align-items-start justify-content-center w-100">'+
					'<h3 class="d-flex"><a class="pc-product-edit" href="javascript:void(0)">'+productName+'</a></h3>'+
				'</div>'+
			'</div>'+
			'<a class="trash me-0 ms-0" href="javascript:void(0)"><img src="'+my_ajax_obj.base_url+'/wp-content/themes/tiendas/img/trash.svg" class="product-trash img-fluid me-3" /></a>'+
		'</li>';

		if(productsQTY > 1){
			//agrego los datos del producto al listado de productos			
			$(item).insertAfter($(".product-item").last());
		}else{
			$(item).prependTo($(".products-list-container"));
		}

		$(".products-list-container").find('li').each(function(index){
			$(this).find('.counter').text(index + 1);
		});

		if(productsQTY >= 3){
			$('.products-form').parents('.sub-step').find('.btn-next').removeClass('disabled');
		}

		//aqui agrego todas los previews de las imagenes de los productos al popup de las imagenes del prod
		// console.log(pcProductsList[pcProductsIndex]);	

		for(var i = 0; i < pcProductsList[pcProductsIndex].length; i++){
			var file = pcProductsList[pcProductsIndex][i];

			if(file.index == 'item-pc-'+pcProductsIndex){
				const previewHtml = `
					<div class="img-preview-container">
						<img src="${file.data}" class="img-preview" alt="">
					</div>
				`;
				$('.product-images-'+pcProductsIndex).append(previewHtml);
			}

		}

		$('#field-fromPCProductName').val('');
		$('#field-fromPCProductPrice').val('');
		$('#field-fromPCProductSalePrice').val('');
		$('#field-fromPCProductDescription').val('');

		//============================================================================================================================================
		//limpio el upload field

		uploaderStep1ProductsFiles.clearFiles();
		//============================================================================================================================================

		$('.products-form').hide();
		$('.products-list').fadeIn();

		pcProductsIndex++;

		uploaderStep1ProductsFiles.updateIndex(pcProductsIndex);
	});

	/**
	evento click para cerrar el popup de las imagenes del producto
	*/
	$('body').on('click', '.product-images-close', function (e) {
		$(this).parent().fadeOut();
	});

	/**
	evento click para mostrar el popup de las imagenes del producto
	*/
	$('body').on('click', '.product-more-images', function(e){
		e.preventDefault();
		$(this).next().fadeIn();
	});

	/**
	evento click para eliminar el producto
	*/
	$('body').on('click', '.product-trash:not(.disabled):not(.step3)', function(e){
		e.preventDefault();
		var index = $(this).parents('li').attr('index');
		$('.'+index).remove();

		if($('.products-list-container').children().length < 3 && !$('#field-fromPCProductLessThanThree').is(":checked")){
			$('.products-form').parents('.sub-step').find('.btn-next').addClass('disabled');
		}

		if($('.products-list-container').children().length < 3){
			var placeholder = '<li class="product-placeholder d-flex align-items-center justify-content-between w-100 py-3">'+
                '<span class="counter disabled me-3">'+$('.products-list-container').children().length+'</span>'+
                '<div class="me-auto ms-0 d-flex align-items-center justify-content-start gap-3 w-75">'+
                    '<img src="'+my_ajax_obj.base_url+'/wp-content/themes/tiendas/img/image-placeholder.png" class="product-thumb me-0 img-fluid" />'+
                    '<div class="d-flex flex-column align-items-start justify-content-center w-100">'+
                        '<div class="gray-bar w-75"></div>'+
                        '<div class="gray-bar w-50 dark-gray"></div>'+
                        '<div class="gray-bar w-25"></div>'+
                    '</div>'+
                '</div>'+
                '<a class="trash me-0 ms-0 disabled" disabled href="javascript:void(0)"><img src="'+my_ajax_obj.base_url+'/wp-content/themes/tiendas/img/trash.svg" class="product-trash step3 img-fluid me-3 disabled" disabled /></a>'+
            '</li>';

			$(placeholder).appendTo($(".products-list-container"));
		}

		$(".products-list-container").find('li').each(function(index){
			$(this).find('.counter').text(index + 1);
		});
	});

	/**
	evento click para adicionar mas productos desde el listado de productos
	*/
	$('.step-1 .btn-plus-product').click(function(){
		$('.step-1 .btn-add-pc-product').addClass('disabled');
		$('.step-1 .btn-add-link-product').addClass('disabled');

		const pctab = document.querySelector('.step-1 #myTab button[data-bs-target="#frompc"]')
		bootstrap.Tab.getInstance(pctab).show();

		uploaderStep1ProductsFiles.updateIndex(pcProductsIndex);

		$(".step-1 #field-fromLinkProductName").val('');
		$(".step-1 #field-fromLinkProductLink").val('');

		$(".step-1 #field-fromPCProductMedia").val('');
		$(".step-1 #field-fromPCProductName").val('');
		$(".step-1 #field-fromPCProductPrice").val('');
		$(".step-1 #field-fromPCProductSalePrice").val('');
		$(".step-1 #field-fromPCProductDescription").val('');

		$('.step-1 .products-form .field-upload-products').find('.field-upload-content').hide();
		$('.step-1 .products-form .field-upload-products').find('.field-upload-content').find('.image-preview-close').hide();
		$('.step-1 .products-form .field-upload-products').find('.field-upload-field').fadeIn();
		$('.step-1 .products-form .field-upload-products').find('.field-upload-content').find('.img-preview-container').remove();
		$('.step-1 .products-form .field-upload-products').find('.field-upload-input')[0].value = null;

		$('.step-1 .field-preview-media').html('').hide();
		$('.step-1 .field-upload-products').fadeIn();
		$('.step-1 .field-upload-new-media').addClass('d-none').removeClass('d-block');
		editProductMedia = false;

		$('.step-1 .btn-cancel-link-product').removeClass('d-none');
		$('.step-1 .btn-save-link-product').addClass('d-none');
		$('.step-1 .btn-add-link-product').removeClass('d-none');

		$('.step-1 .btn-cancel-pc-product').removeClass('d-none');
		$('.step-1 .btn-save-pc-product').addClass('d-none');
		$('.step-1 .btn-add-pc-product').removeClass('d-none');

		$('.step-1 .products-list').hide();
		$('.step-1 .products-form').fadeIn();
	});

	/**
	evento para editar el producto desde el listado de productos de referencia
	*/
	var itemIndex = '';
	$('body').on('click', '.step-1 .link-product-edit', function(e){
		e.preventDefault();

		const linktab = document.querySelector('#myTab button[data-bs-target="#fromlink"]');
		bootstrap.Tab.getInstance(linktab).show();

		var item = $(this);
		itemIndex = item.parents('li').attr('index');
		var fields = $('.link-products .'+itemIndex);
		if(fields.length > 0) {
			$("#field-fromLinkProductName").val($('.link-products .'+itemIndex+'.LinkProductName').val());
			$("#field-fromLinkProductLink").val($('.link-products .'+itemIndex+'.LinkProductLink').val());
		}

		$('.btn-cancel-link-product').removeClass('d-none');
		$('.btn-save-link-product').removeClass('d-none');
		$('.btn-add-link-product').addClass('d-none');

		$('.products-list').hide();
		$('.products-form').fadeIn();
	});

	/**
	evento para cancelar la edicion del producto
	*/
	$('body').on('click', '.step-1 .btn-cancel-link-product', function(e) {
		$("#field-fromLinkProductName").val('');
		$("#field-fromLinkProductLink").val('');
		$('.products-form').hide();
		$('.products-list').fadeIn();
	});

	/**
	evento para guardar los cambios realizados al producto
	*/
	$('body').on('click', '.step-1 .btn-save-link-product', function(e) {
		var fields = $('.link-products .'+itemIndex);
		if(fields.length > 0) {
			$('li[index="'+itemIndex+'"] h3 a').html($("#field-fromLinkProductName").val());

			$('.link-products .'+itemIndex+'.LinkProductName').val($("#field-fromLinkProductName").val());
			$('.link-products .'+itemIndex+'.LinkProductName').attr('value', $("#field-fromLinkProductName").val());
			$('.link-products .'+itemIndex+'.LinkProductLink').val($("#field-fromLinkProductLink").val());
			$('.link-products .'+itemIndex+'.LinkProductLink').attr('value', $("#field-fromLinkProductLink").val());
		}

		$("#field-fromLinkProductName").val('');
		$("#field-fromLinkProductLink").val('');
		$('.products-form').hide();
		$('.products-list').fadeIn();
	});

	/**
	evento para editar el producto desde el listado de productos subidos desde pc
	*/
	$('body').on('click', '.step-1 .pc-product-edit', function(e){
		e.preventDefault();
		const pctab = document.querySelector('#myTab button[data-bs-target="#frompc"]');
		bootstrap.Tab.getInstance(pctab).show();

		var item = $(this);
		itemIndex = item.parents('li').attr('index');
		var index = itemIndex.split('item-pc-')[1];

		$('.field-preview-media').html('');

		//aki
		uploaderStep1ProductsFiles.updateIndex(index);

		var fields = $('.pc-products .'+itemIndex);
		if(fields.length > 0) {
			for(var i = 0; i < pcProductsList[index].length; i++){
				var file = pcProductsList[index][i];
	
				if(file.index == itemIndex){
					const previewHtml = `
						<div class="img-preview-container">
							<img src="${file.data}" class="img-preview" alt="">
						</div>
					`;
					$('.field-preview-media').append(previewHtml);
				}
	
			}

			$('.field-upload-products').hide();
			$('.field-preview-media').fadeIn();

			$('.field-upload-new-media').removeClass('d-none').addClass('d-block');

			$("#field-fromPCProductName").val($('.pc-products .'+itemIndex+'.PCProductName').val());
			document.getElementById("field-fromPCProductCurrecy").value = $('.pc-products .'+itemIndex+'.PCProductCurrecy').val();
			$("#field-fromPCProductPrice").val($('.pc-products .'+itemIndex+'.PCProductPrice').val());
			$("#field-fromPCProductSalePrice").val($('.pc-products .'+itemIndex+'.PCProductSalePrice').val());
			$("#field-fromPCProductDescription").val($('.pc-products .'+itemIndex+'.PCProductDescription').val());
		}

		$('.btn-cancel-pc-product').removeClass('d-none');
		$('.btn-save-pc-product').removeClass('d-none');
		$('.btn-add-pc-product').addClass('d-none');

		$('.products-list').hide();
		$('.products-form').fadeIn();
	});

	var editProductMedia = false;
	$('.field-upload-new-media').click(function(){
		$('.field-preview-media').html('').hide();
		$('.field-upload-products').fadeIn();
		$('.field-upload-new-media').addClass('d-none').removeClass('d-block');
		uploaderStep1ProductsFiles.clearFiles();
		editProductMedia = true;
	});

	/**
	evento para guardar los cambios realizados al producto desde pc
	*/
	$('body').on('click', '.step-1 .btn-save-pc-product', function(e) {
		var fields = $('.pc-products .'+itemIndex);
		var index = itemIndex.split('item-pc-')[1];
		if(fields.length > 0) {
			$('li[index="'+itemIndex+'"] h3 a').html($("#field-fromPCProductName").val());

			var productMedia = document.getElementById('field-fromPCProductMedia').files;

			if(productMedia.length > 0) {
				var firstMedia = my_ajax_obj.base_url+'/wp-content/themes/tiendas/img/image-placeholder.png';
				var done = false;

				// Create a new DataTransfer object to hold the files for this input
				const dataTransfer = new DataTransfer();
				for (let i = 0; i < productMedia.length; i++) {
					dataTransfer.items.add(productMedia[i]); // Add each file to the DataTransfer

					var file = productMedia[i];
					if((file.type == 'image/jpeg' || file.type == 'image/png' || file.type == 'image/gif') && !done){
						firstMedia = file.data;
						done = true;
					}
				}
				document.querySelector('.pc-products .'+itemIndex+'.PCProductMedia').files = dataTransfer.files;

				// Store the files in the products list
				pcProductsList[index] = dataTransfer.files;

				$('li[index="'+itemIndex+'"] .product-thumb').attr('src', firstMedia);

				$('li[index="'+itemIndex+'"] .product-images').html('<span class="product-images-close"><img src="'+my_ajax_obj.base_url+'/wp-content/themes/tiendas/img/close-dark.svg" alt="close preview"></span>');

				for(var i = 0; i < pcProductsList[index].length; i++){
					var file = pcProductsList[index][i];
		
					if(file.index == itemIndex){
						const previewHtml = `
							<div class="img-preview-container">
								<img src="${file.data}" class="img-preview" alt="">
							</div>
						`;
						$('li[index="'+itemIndex+'"] .product-images').append(previewHtml);
					}
		
				}
			}

			$('.pc-products .'+itemIndex+'.PCProductName').val($("#field-fromPCProductName").val());
			$('.pc-products .'+itemIndex+'.PCProductCurrecy').attr('value', $("#field-fromPCProductCurrecy").val());
			$('.pc-products .'+itemIndex+'.PCProductPrice').val($("#field-fromPCProductPrice").val());
			$('.pc-products .'+itemIndex+'.PCProductSalePrice').val($("#field-fromPCProductSalePrice").val());
			$('.pc-products .'+itemIndex+'.PCProductDescription').attr('value', $("#field-fromPCProductDescription").val());
		}

		$("#field-fromPCProductMedia").val('');
		$("#field-fromPCProductName").val('');
		$("#field-fromPCProductPrice").val('');
		$("#field-fromPCProductSalePrice").val('');
		$("#field-fromPCProductDescription").val('');

		$('.products-form').hide();
		$('.products-list').fadeIn();
	});

	/**
	evento para cancelar la edicion del producto desde pc
	*/
	$('body').on('click', '.step-1 .btn-cancel-pc-product', function(e) {
		$("#field-fromPCProductMedia").val('');
		$("#field-fromPCProductName").val('');
		$("#field-fromPCProductPrice").val('');
		$("#field-fromPCProductSalePrice").val('');
		$("#field-fromPCProductDescription").val('');

		editProductMedia = false;

		$('.products-form').hide();
		$('.products-list').fadeIn();
	});

	//============================================================================================================================================================
	//file uploads product
	//============================================================================================================================================================
	/**
	aqui valido y creo los eventos que tienen que ver con las imagenes de los productos, ya sea mediante click o dropp
	*/
    const uploaderStep1ProductsFiles = new UploadController('.field-upload-products', ['item-pc-', pcProductsIndex], null);

	//====================================================================================================================================================================================
	//====================================================================================================================================================================================
	//====================================================================================================================================================================================

	if($('.btn-finish').length){
		$('.btn-finish').click(function(){
			$('.user-form-modal').fadeIn();
			$('.price-box').fadeOut();
			$('#field-firstname').focus();
		});
	}

	// if($('.btn-confirm-without-payment').length){
		var confirm_without_payment = (form) => {
			var actionUrl = my_ajax_obj.ajax_url;

			form.find('.form-loader').fadeIn();

			var data = {
				'actual_shopify_store': $('input[name="field-storeActualShopifyAccount"]:checked').val(),
				'actual_store_link': $('#field-storeActualLink').val(),
				'actual_store_improve': $('#field-storeImproveDesc').val(),
				'actual_store_client_name': $('#field-storeClientName').val(),
				'actual_store_client_email': $('#field-storeClientEmail').val(),
			};

			$.post(actionUrl, {action: 'ws', wsa: 'create_user_no_payment', data: data}, function(response){
				if(response.error != false){
					alert(response.message);
				}
				form.find('.form-loader').fadeOut();
			});
		};
	// }

	if($('.btn-confirm').length){
		$('.btn-confirm').click(function(){
			$(this).parents('.resume-box').hide();
			$(this).parents('.resume-box').next('.price-box').fadeIn();
			gsap.to(window, {duration: 0, scrollTo:{ y: 0, offsetY: 0}, ease: "power2.outIn"});
		});
	}

	$('.checkbox-more').blur(function(){
		var value = $(this).val();
		$(this).parent().find('.form-check-input').val(value);
	});

	if($('.btn-create').length){

		// .keyup(function(){
		// 	if(isValidEmail($(this).val())){
		// 		$(this).next('.error').html('');
		// 	}else{
		// 		$(this).next('.error').html('Escribe un email v&aacute;lido.');
		// 	}
		// })

		$('#field-useremail').blur(function(){
			var fieldusername = $(this);
			if(isValidEmail(fieldusername.val())){
				fieldusername.next('.error').html('');
				$.post(my_ajax_obj.ajax_url, {action: 'ws', wsa: 'verify_user', useremail: fieldusername.val()}, function(response){
					// console.log(response.exists);
					// console.log(fieldusername)
					if(response.exists == true){
						fieldusername.next('.error').html(my_ajax_obj.email_registered);
					}
				});
			}else{
				fieldusername.next('.error').html(my_ajax_obj.valid_email);
			}
		});

		// .keyup(function(){
		// 	// console.log($(this).val(), $('#field-userpass-confirm').val());
		// 	if($(this).val().length > 3){
		// 		if($(this).val() == $('#field-userpass-confirm').val()){
		// 			$(this).next('.error').html('');
		// 			$('#field-userpass-confirm').next('.error').html('');
		// 		}else{
		// 			$(this).next('.error').html('La contrase&ntilde;a y confirmar contrase&ntilde;a no coinciden.');
		// 		}
		// 	}else{
		// 		$(this).next('.error').html('Contrase&ntilde;a muy corta.');
		// 	}
		// })

		$('#field-userpass').focus(function(){
			$(this).next('.error').html('');
		});

		$('#field-userpass').blur(function(){
			// console.log($(this).val(), $('#field-userpass-confirm').val());
			if($(this).val().length == 0){
				$(this).next('.error').html(my_ajax_obj.enter_pass);
			}else if($(this).val().length > 3){
				if($(this).val() == $('#field-userpass-confirm').val()){
					$(this).next('.error').html('');
					$('#field-userpass-confirm').next('.error').html('');
				}else if($('#field-userpass-confirm').val() != ''){
					$(this).next('.error').html(my_ajax_obj.match_pass);
				}
			}else{
				$(this).next('.error').html(my_ajax_obj.short_pass);
			}
		});

		// keyup(function(){
		// 	// console.log($(this).val(), $('#field-userpass').val());
		// 	if($(this).val().length > 3){
		// 		if($(this).val() == $('#field-userpass').val()){
		// 			$(this).next('.error').html('');
		// 			$('#field-userpass').next('.error').html('');
		// 		}else{
		// 			$(this).next('.error').html('La contrase&ntilde;a y confirmar contrase&ntilde;a no coinciden.');
		// 		}
		// 	}else{
		// 		$(this).next('.error').html('Contrase&ntilde;a muy corta.');
		// 	}
		// })

		$('#field-userpass-confirm').focus(function(){
			$(this).next('.error').html('');
		});

		$('#field-userpass-confirm').blur(function(){
			// console.log($(this).val(), $('#field-userpass').val());
			if($(this).val().length == 0){
				$(this).next('.error').html(my_ajax_obj.match_pass);
			}else if($(this).val().length > 3){
				if($(this).val() == $('#field-userpass').val()){
					$(this).next('.error').html('');
					$('#field-userpass').next('.error').html('');
				}else{
					$(this).next('.error').html(my_ajax_obj.match_pass);
				}
			}else{
				$(this).next('.error').html(my_ajax_obj.short_pass);
			}
		});

		$("#form-step-1").submit(function(e){
			e.preventDefault();
			var actionUrl = my_ajax_obj.ajax_url;

			$(this).find('.form-loader').fadeIn();

			var ok = true;
			$("#form-step-1").find('.error').each(function(){
				if($(this).html() != ''){
					ok = false;
				}
			});

			if(ok){
				$.ajax({
					method: "POST",
					url: actionUrl,
					crossDomain: true,
					data: new FormData(this),
					dataType: "json",
					contentType: "multipart/form-data",
					processData: false,
					contentType: false,
					headers: {
						"Accept": "application/json"
					}
				}).done(function(response) {
					if(response.error == false){
						$(window).unbind("beforeunload");
						window.location = my_ajax_obj.base_url+"/pagar/";
					}else{
						alert(response.message);
						$(this).find('.form-loader').fadeOut();
					}
				}).fail(function() {
					alert(my_ajax_obj.error)
					$(this).find('.form-loader').fadeOut();
				});

			}else{
				alert(my_ajax_obj.error)	
				$(this).find('.form-loader').fadeOut();
			}
		});
	}

    

	//============================================================================================================================================================
	//============================================================================================================================================================
	//============================================================================================================================================================
});

(function($){
    $(window).on("beforeunload", function() { 
        return true;
    })
})(jQuery);
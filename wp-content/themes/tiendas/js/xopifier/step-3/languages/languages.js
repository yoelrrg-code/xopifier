var unsaved_lang_changes = false;

jQuery(document).ready(function ($) {

    $('.btn-toggle-languages-section').click(function(){
        var storeForm = $('#store-languages-data');
        var btn = $(this);
        if(btn.hasClass('is_inactive')){
            // btn.parents('.side-column').prev('.main-column').removeClass('disabled');
            // var btninclude = btn.find('.include');
            // var btnexclude = btn.find('.exclude');
            // btninclude.addClass('d-none');
            // btnexclude.removeClass('d-none');
            // btn.find('svg.remove').removeClass('d-none');
            // btn.find('svg.add').addClass('d-none');
            // btn.removeClass('is_inactive').addClass('is_active');
            storeForm.find('input[name="disable"]').val('false');
            // btn.parent().find('.message-on').show();
            // btn.parent().find('.message-off').hide();
            // storeForm.find('.bottom-column').fadeIn();
        }else if(btn.hasClass('is_active')){
            // btn.parents('.side-column').prev('.main-column').addClass('disabled');
            // var btninclude = btn.find('.include');
            // var btnexclude = btn.find('.exclude');
            // btninclude.removeClass('d-none');
            // btnexclude.addClass('d-none');
            // btn.find('svg.remove').addClass('d-none');
            // btn.find('svg.add').removeClass('d-none');
            // btn.removeClass('is_active').addClass('is_inactive');
            storeForm.find('input[name="disable"]').val('true');
            // storeForm.find('.bottom-column').fadeOut();
            // btn.parent().find('.message-on').hide();
            // btn.parent().find('.message-off').show();

            clear_languages();

            $('.lang-extra-form-modal').fadeOut();
            $('.leyend').hide();

            storeForm.find('.btn-continue').show();
            storeForm.find('.btn-lang-open-modal').hide();

            $.post(my_ajax_obj.ajax_url, {action: 'ws', wsa: 'save-store-languages-data', disable: true, store_id: storeForm.find('input[name="store_id"]').val()}, function(resp){
                $('#myTabStep3').find('#other-tab').removeClass('working').addClass('done');
                
                if(resp.total_price != undefined){
                    update_prices(resp.total_price)
                }
                
                check_step3_status();
                update_popup(storeForm);
            });
        }
    });

    $('body').on('click', '#language-selector-box .language-selector-div .lang-div', function(){
		var lang_div = $(this);
        var storeForm = $('#store-languages-data');
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

			new_language_check_field.appendTo($(this).parents('.languages-selector-container'));
			
			lang_div.addClass('active');
            $('.languages-selector-container').find('.form-check-input').each(function(){
                $(this).attr('disabled', false);
            });
			language_check_field.find('.form-check-input').val(lang_div.attr('value')).attr('checked', 'checked').prop('checked', 'checked');
			language_check_field.find('.form-check-label').html(lang_div.html());
			language_check_field.find('#language-selector-box').fadeOut();

			if(!language_check_field.hasClass('base-lang')){
				selected_languages.push(lang_div.attr('value'));
			}

            // var default_options = '';
            var default_lang = $('#language-service-default').val();
            
            // selected_languages.forEach((lang) => {
                $.post(my_ajax_obj.ajax_url, {action: 'ws', wsa: 'get-selected-languages-translations', selected_languages: selected_languages, default_lang: default_lang}, function(resp){
                    $('#language-service-default').html(resp.selected_languages);
                })
            // });
            

            $('.language-service-default-container').fadeIn();

            //muestro el mensaje de aumento de precio
            $('.popover').remove();

            var total_price = storeForm.find('input[name="total_price"]').val();
            var language_price = storeForm.find('input[name="language_price"]').val();

            var new_total_price = Number(total_price) + Number(language_price);
            storeForm.find('input[name="total_price"]').val(new_total_price);

            $('.btn-save-store-languages-data').removeClass('disabled');

            var c = 0;
            storeForm.find('.form-check-input').each(function(){
                if($(this).is(':checked')){
                    c++;
                }
            });

            $('.new-price').html(language_price*c);

            storeForm.find('.form-tip').find('.fw-semibold').show();

            $('.leyend').show();
            $('.btn-save-store-languages-data').show();

            $('.form-loader').fadeOut(200, function(){
                // gsap.to($('.overlay-bg'), .3, {delay: .1, zIndex: '99999', opacity: 1, ease: Power2.easeOut, onComplete: function() {}});
            });

            // $('#howto-popover').attr('data-bs-content', my_ajax_obj.add_lang_1+' '+lang_div.html()+my_ajax_obj.add_lang_2+new_total_price+"</b>");
            // message('howto-popover');
            // setTimeout(function(){
            //     gsap.to($('.overlay-bg'), .2, {zIndex: '-1', opacity: 0, ease: Power2.easeIn, onComplete: function() {
            //         jQuery('.popover').fadeOut(200, function(){
            //             jQuery(this).remove();
            //         });
            //     }}); 
            // }, 2000);
		}
	});

    $('.btn-lang-open-modal').click(function(){
        $('.lang-extra-form-modal').fadeIn();
    })

    $('.btn-lang-close-modal').click(function(){
        $('.lang-extra-form-modal').fadeOut();
    })

    var clear_languages = () => {
        var lang_qty = $('.languages-selector-container').find('.language-form-check').find('.form-check-input:checked').length;
        if(lang_qty > 0){
            var storeForm = $('#store-languages-data');
            var total_price = storeForm.find('input[name="total_price"]').val();
            var language_price = storeForm.find('input[name="language_price"]').val();

            storeForm.find('input[name="total_price"]').val(total_price - (language_price*lang_qty));
        }
        
        $('.languages-selector-container').find('.language-form-check').each(function(){
            $(this).remove();
        });

        $('.language-service-default-container').hide();

        $('.btn-save-store-languages-data').addClass('disabled');

        var language_selector_box_placeholder = $('#language-selector-box-placeholder').clone();

        var new_language_check_field = '<div class="form-check language-form-check base-lang w-100 position-relative">'+
                '<input class="form-check-input" type="checkbox" value="Inglés" id="language-service-0" name="field-languages[]">'+
                '<label class="form-check-label" for="language-service-0">'+
                    my_ajax_obj.english+
                '</label>'+
            '</div><div class="form-check language-form-check w-100 position-relative">'+
            '<input class="form-check-input" type="checkbox" value="" id="language-service-1" name="field-languages[]">'+
            '<label class="form-check-label" for="language-service-1">'+
                my_ajax_obj.other+
            '</label>'+
            '<div class="position-absolute" id="language-selector-box" style="display: none;">'+
                language_selector_box_placeholder.html()+
            '</div>'+
        '</div>';

        $(new_language_check_field).appendTo($('.languages-selector-container'));

        selected_languages = ['Español'];

        $('#language-service-default').html('');
        
        $('.lang-div').each(function(){
            $(this).removeClass('active');
        });

        $('#store-languages-data .form-check-input').each(function(){
            $(this).unbind('click');
            $(this).click(function(){
                input_check_click($(this));
            })
        });
    }

    $('#store-languages-data .form-check-input').each(function(){
        $(this).unbind('click');
		$(this).click(function(){
			input_check_click($(this));
		})
	});

    var selected_languages = ['Español'];

    $('.language-form-check').find('.form-check-input').each(function(){
        if($(this).is(':checked')){
            selected_languages.push($(this).val());
            $('.language-service-default-container').fadeIn();
            $('.btn-save-store-languages-data').removeClass('disabled');
        }
    });

    $('#language-service-default').change(function(){
        var storeForm = $('#store-languages-data');
        if(storeForm.find('.btn-save-store-languages-data').css('display') == 'none'){
            storeForm.find('.btn-save-store-languages-data-2').show();
            unsaved_lang_changes = true;
        }
    });

    var input_check_click = (checkbox_input) => {
        var storeForm = $('#store-languages-data');
        unsaved_lang_changes = true;

		if(checkbox_input.is(':checked')){//marco o activo el idioma
            $('#myTabStep3').find('#other-tab').addClass('working').removeClass('done');
			if(checkbox_input.parent().hasClass('language-form-check')){
				var c = 0;
				checkbox_input.parents('#store-languages-data').find('.form-check-input').each(function(){
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
				checkbox_input.parent().find('#language-selector-box').fadeIn();
			}
			
			checkbox_input.parents('#store-languages-data').next().find('h4').removeClass('d-none');
			checkbox_input.parent().next().addClass('show');
            if(!checkbox_input.parent().hasClass('base-lang')){
                checkbox_input.attr('disabled', true);
                $('.form-loader').fadeIn();
                $('.btn-save-store-languages-data').addClass('disabled').show();
                storeForm.find('.btn-save-store-languages-data-2').hide();
            }else{
                checkbox_input.attr('checked', true);
                
                selected_languages.push(checkbox_input.val());
                var default_options = '';
                var default_lang = $('#language-service-default').val();
                
                // selected_languages.forEach((lang) => {
                //     if(lang == default_lang)
                //         default_options += '<option selected="selected" value="'+lang+'">'+lang+'</option>';
                //     else
                //         default_options += '<option value="'+lang+'">'+lang+'</option>';
                // });

                // console.log(selected_languages);
                
                
                $.post(my_ajax_obj.ajax_url, {action: 'ws', wsa: 'get-selected-languages-translations', selected_languages: selected_languages, default_lang: default_lang}, function(resp){
                    $('#language-service-default').html(resp.selected_languages);
                })
                
                // $('#language-service-default').html(default_options);
                $('.language-service-default-container').fadeIn();

                //muestro el mensaje de aumento de precio
                $('.popover').remove();

                var storeForm = $('#store-languages-data');

                var total_price = storeForm.find('input[name="total_price"]').val();
                var language_price = storeForm.find('input[name="language_price"]').val();

                var new_total_price = Number(total_price) + Number(language_price);
                storeForm.find('input[name="total_price"]').val(new_total_price);

                $('.btn-save-store-languages-data').removeClass('disabled');

                $('.new-price').html(language_price*c);

                $('.leyend').show();
                $('.btn-save-store-languages-data').show();
                storeForm.find('.btn-save-store-languages-data-2').hide();

                storeForm.find('.form-tip').find('.fw-semibold').show();

                // gsap.to($('.overlay-bg'), .3, {zIndex: '99999', opacity: 1, ease: Power2.easeOut, onComplete: function() {}});

                // $('#howto-popover').attr('data-bs-content', my_ajax_obj.add_extra_lang_1+' '+checkbox_input.val()+my_ajax_obj.add_extra_lang_2+new_total_price+"</b>");
                // message('howto-popover');
                // setTimeout(function(){
                //     gsap.to($('.overlay-bg'), .2, {zIndex: '-1', opacity: 0, ease: Power2.easeIn, onComplete: function() {
                //         jQuery('.popover').fadeOut(200, function(){
                //             jQuery(this).remove();
                //         });
                //     }}); 
                // }, 2000);
            }
		}else{//desmarco o desactivo el idioma
			if(checkbox_input.parent().hasClass('language-form-check')){
				if(checkbox_input.val() != undefined && checkbox_input.val() != '' && !checkbox_input.parent().hasClass('base-lang')){
					selected_languages.splice(selected_languages.indexOf(checkbox_input.val()), 1);
				}else{
					checkbox_input.parent().find('#language-selector-box').fadeOut();
				}
				var c = 0;
				checkbox_input.parents('#store-languages-data').find('.form-check-input').each(function(){
					if($(this).is(':checked')){
						c++;
					}
				});
				if(c == 0){
					checkbox_input.parents('#store-languages-data').next().find('h4').addClass('d-none');
					checkbox_input.parent().next().removeClass('show');
				}
			}else{
				checkbox_input.parents('#store-languages-data').next().find('h4').addClass('d-none');
				checkbox_input.parent().next().removeClass('show');
			}

			if(checkbox_input.parent().hasClass('language-form-check') && !checkbox_input.parent().hasClass('base-lang')){
				checkbox_input.parent().find('.lang-div').each(function() {
					if($(this).attr('value') == checkbox_input.val()){
						$(this).removeClass('active');
					}
				});
				checkbox_input.val('').attr('checked', false).prop('checked', false);
				checkbox_input.next().html(my_ajax_obj.other);
				checkbox_input.parent().find('#language-selector-box').fadeOut();

				var checked_fields = checkbox_input.parents('#store-languages-data').find('.language-form-check:not(.checked):not(.base-lang)');
                
				if(checked_fields.length > 1){
                    checkbox_input.parent().remove();
                    
                    var default_options = '';
                    var default_lang = $('#language-service-default').val();
                    
                    // selected_languages.forEach((lang) => {
                    //     if(lang == default_lang)
                    //         default_options += '<option selected="selected" value="'+lang+'">'+lang+'</option>';
                    //     else
                    //         default_options += '<option value="'+lang+'">'+lang+'</option>';
                    // });
                    
                    // $('#language-service-default').html(default_options);
                    $.post(my_ajax_obj.ajax_url, {action: 'ws', wsa: 'get-selected-languages-translations', selected_languages: selected_languages, default_lang: default_lang}, function(resp){
                        $('#language-service-default').html(resp.selected_languages);
                    })
				}
			}else if(checkbox_input.parent().hasClass('base-lang')){
                // checkbox_input.attr('checked', false).prop('checked', false);
                // checkbox_input.parent().remove();
                selected_languages.splice(selected_languages.indexOf(checkbox_input.val()), 1);
                var default_options = '';
                var default_lang = $('#language-service-default').val();
                
                // selected_languages.forEach((lang) => {
                //     if(lang == default_lang)
                //         default_options += '<option selected="selected" value="'+lang+'">'+lang+'</option>';
                //     else
                //         default_options += '<option value="'+lang+'">'+lang+'</option>';
                // });
                
                // $('#language-service-default').html(default_options);

                $.post(my_ajax_obj.ajax_url, {action: 'ws', wsa: 'get-selected-languages-translations', selected_languages: selected_languages, default_lang: default_lang}, function(resp){
                    $('#language-service-default').html(resp.selected_languages);
                })
            }

            if(selected_languages.length == 1){
                $('.language-service-default-container').hide();
            }
            //muestro el mensaje de aumento de precio
            $('.popover').remove();
            var storeForm = $('#store-languages-data');

            var total_price = storeForm.find('input[name="total_price"]').val();
            var language_price = storeForm.find('input[name="language_price"]').val();
            var new_total_price = Number(total_price) - Number(language_price);
            storeForm.find('input[name="total_price"]').val(new_total_price);

            $('.new-price').html(language_price*c);

            // console.log(c);

            if(c == 0){
                storeForm.find('.form-tip').find('.fw-semibold').hide();
                $('.leyend').hide();
                $('.btn-save-store-languages-data').hide();
            }else{
                $('.leyend').show();
                $('.btn-save-store-languages-data').hide();
                storeForm.find('.btn-save-store-languages-data-2').show();
            }

            gsap.to($('.overlay-bg'), .3, {zIndex: '99999', opacity: 1, ease: Power2.easeOut, onComplete: function() {}});

            if(storeForm.find('.btn-save-store-languages-data').css('display') == 'none'){
                storeForm.find('.btn-save-store-languages-data-2').show();
            }

            $('#howto-popover').attr('data-bs-content', my_ajax_obj.add_lang_3+new_total_price+"</b>");
            message('howto-popover');
            setTimeout(function(){
                gsap.to($('.overlay-bg'), .2, {zIndex: '-1', opacity: 0, ease: Power2.easeIn, onComplete: function() {
                    jQuery('.popover').fadeOut(200, function(){
                        jQuery(this).remove();
                    });
                }}); 
            }, 2000);
		}
	};

    $('#store-languages-data .btn-continue').click(function(){
        var actionUrl = my_ajax_obj.ajax_url;
        var storeForm = $('#store-languages-data');
        clear_languages();
        $('.leyend').hide();
        storeForm.find('.form-tip').find('.fw-semibold').hide();
        $('.btn-save-store-languages-data').hide();
        $.post(actionUrl, {action: 'ws', wsa: 'save-store-languages-data', ignore: true, store_id: storeForm.find('input[name="store_id"]').val()}, function(resp){
            $('#myTabStep3').find('#other-tab').removeClass('working').addClass('done');
            update_tab_status('other-tab', 'main', 'done');
            
            if(resp.total_price != undefined){
                update_prices(resp.total_price)
            }

            check_tab_products_status();
        });
    });

    $('#store-languages-data').submit(function(e) {
        e.preventDefault();
        var actionUrl = my_ajax_obj.ajax_url;
        var storeForm = $('#store-languages-data');
        var scrollTop = jQuery(window).scrollTop();

        gsap.to(window, {duration: .2, scrollTo:{ y: (scrollTop/2), offsetY: 0}, ease: "power2.outIn", onComplete: function(){}});

        storeForm.find('input[name="disable"]').val('false');

        var total_price = storeForm.find('input[name="total_price"]').val();
        var language_price = storeForm.find('input[name="language_price"]').val();

        var new_total_price = Number(total_price);

        gsap.to($('.overlay-bg'), .3, {zIndex: '99999', opacity: 1, ease: Power2.easeOut, onComplete: function() {}});
        $('#howto-popover').attr('data-bs-content', my_ajax_obj.add_lang_4+new_total_price+"</b>");
        message('howto-popover');

        var formdata = new FormData(this);

        setTimeout(function(){
            $.ajax({
                method: "POST",
                url: actionUrl,
                data: formdata,
                dataType: "json",
                processData: false,
                contentType: false,
                headers: {
                    "Accept": "application/json"
                }
            }).done(function(response) {
                storeForm.find('.form-loader').fadeOut();
                gsap.to(window, {duration: .2, scrollTo:{ y: 0, offsetY: 0}, ease: "power2.outIn"});

                $('.popover').remove();

                // gsap.to($('.overlay-bg'), .3, {zIndex: '99999', opacity: 1, ease: Power2.easeOut, onComplete: function() {}});

                storeForm.find('.form-tip').find('.fw-semibold').hide();
                storeForm.find('.btn-save-store-languages-data').hide();
                storeForm.find('.btn-save-store-languages-data-2').hide();

                storeForm.find('.btn-continue').hide();
                storeForm.find('.btn-lang-open-modal').show();

                // $('#howto-popover').attr('data-bs-content', response.msg);
                // message('howto-popover');
                // setTimeout(function(){
                //     gsap.to($('.overlay-bg'), .2, {zIndex: '-1', opacity: 0, ease: Power2.easeIn, onComplete: function() {
                //         jQuery('.popover').fadeOut(200, function(){
                //             jQuery(this).remove();
                //         });
                //     }}); 
                // }, 2000);

                popup_message(response.msg, 'info', 2000);

                storeForm.find('.msg').removeClass('error').removeClass('success').html('').fadeOut();
                $('#myTabStep3').find('#other-tab').removeClass('working').removeClass('visited').addClass('done');
                update_tab_status('other-tab', 'main', 'done');

                if(response.total_price != undefined){
                    update_prices(response.total_price)
                }

                update_popup(storeForm);

                check_step3_status();
            }).fail(function() {
                // storeForm.find('.msg').addClass('error').removeClass('success').html(my_ajax_obj.error).fadeIn();
                popup_message(my_ajax_obj.error, 'error', 2000);
                storeForm.find('.form-loader').fadeOut();
            });
        }, 2000);
    });
});
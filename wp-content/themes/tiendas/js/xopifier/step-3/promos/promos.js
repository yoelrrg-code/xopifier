jQuery(document).ready(function ($) {

    $('.btn-toggle-promos-ads-section').click(function(){
        var storeForm = $('#store-promos-ads-data');
        var btn = $(this);
        if(btn.hasClass('is_inactive')){
            btn.parents('.side-column').prev('.main-column').removeClass('disabled');
            var btninclude = btn.find('.include');
            var btnexclude = btn.find('.exclude');
            btninclude.addClass('d-none');
            btnexclude.removeClass('d-none');
            btn.find('svg.remove').removeClass('d-none');
            btn.find('svg.add').addClass('d-none');
            btn.removeClass('is_inactive').addClass('is_active');
            storeForm.find('input[name="disable"]').val('false');
            btn.parent().find('.message-on').show();
            btn.parent().find('.message-off').hide();
            storeForm.find('.bottom-column').fadeIn();

            //muestro el mensaje de aumento de precio
            // $('.popover').remove();

            // var total_price = storeForm.find('input[name="total_price"]').val();
            // var service_price = storeForm.find('input[name="service_price"]').val();

            // $('#howto-popover').attr('data-bs-content', "Estás agregando un extra. El costo de tu tienda se actualizará a <b>$"+(Number(total_price) + Number(service_price))+"</b>");
            // message('howto-popover');
            // setTimeout(function(){
            //     $('.popover').fadeOut(200, function(){
            //         $(this).remove();
            //     });
            // }, 7000);
        }else if(btn.hasClass('is_active')){
            btn.parents('.side-column').prev('.main-column').addClass('disabled');
            var btninclude = btn.find('.include');
            var btnexclude = btn.find('.exclude');
            btninclude.removeClass('d-none');
            btnexclude.addClass('d-none');
            btn.find('svg.remove').addClass('d-none');
            btn.find('svg.add').removeClass('d-none');
            btn.removeClass('is_active').addClass('is_inactive');
            storeForm.find('input[name="disable"]').val('true');
            storeForm.find('.bottom-column').fadeOut();
            btn.parent().find('.message-on').hide();
            btn.parent().find('.message-off').show();

            $.post(my_ajax_obj.ajax_url, {action: 'ws', wsa: 'save-store-promos-ads-data', disable: true, store_id: storeForm.find('input[name="store_id"]').val()}, function(resp){
                console.log(resp);

                clear_ads_form();

                $('#myTabPromos').find('#promos-ads-tab').removeClass('working').removeClass('done');

                if(!$('#myTabPromos').find('#promos-discount-tab').hasClass('done') && !$('#myTabPromos').find('#promos-ads-tab').hasClass('done')){
                    $('#myTabStep3').find('#promos-tab').removeClass('working').removeClass('done');
                }else{
                    $('#myTabStep3').find('#promos-tab').addClass('working').removeClass('done');
                }
            });
        }
    });

    $('.btn-toggle-promos-discount-section').click(function(){
        var storeForm = $('#store-promos-discount-data');
        var btn = $(this);
        if(btn.hasClass('is_inactive')){
            btn.parents('.side-column').prev('.main-column').removeClass('disabled');
            var btninclude = btn.find('.include');
            var btnexclude = btn.find('.exclude');
            btninclude.addClass('d-none');
            btnexclude.removeClass('d-none');
            btn.find('svg.remove').removeClass('d-none');
            btn.find('svg.add').addClass('d-none');
            btn.removeClass('is_inactive').addClass('is_active');
            storeForm.find('input[name="disable"]').val('false');
            btn.parent().find('.message-on').show();
            btn.parent().find('.message-off').hide();
            storeForm.find('.bottom-column').fadeIn();
        }else if(btn.hasClass('is_active')){
            btn.parents('.side-column').prev('.main-column').addClass('disabled');
            var btninclude = btn.find('.include');
            var btnexclude = btn.find('.exclude');
            btninclude.removeClass('d-none');
            btnexclude.addClass('d-none');
            btn.find('svg.remove').addClass('d-none');
            btn.find('svg.add').removeClass('d-none');
            btn.removeClass('is_active').addClass('is_inactive');
            storeForm.find('input[name="disable"]').val('true');
            storeForm.find('.bottom-column').fadeOut();
            btn.parent().find('.message-on').hide();
            btn.parent().find('.message-off').show();

            $.post(my_ajax_obj.ajax_url, {action: 'ws', wsa: 'save-store-promos-discount-data', disable: true, store_id: storeForm.find('input[name="store_id"]').val()}, function(resp){
                console.log(resp);

                clear_discount_form();

                $('#myTabPromos').find('#promos-discount-tab').removeClass('working').removeClass('done');
                
                if(!$('#myTabPromos').find('#promos-discount-tab').hasClass('done') && !$('#myTabPromos').find('#promos-ads-tab').hasClass('done')){
                    $('#myTabStep3').find('#promos-tab').removeClass('working').removeClass('done');
                }else{
                    $('#myTabStep3').find('#promos-tab').addClass('working').removeClass('done');
                }
            });
        }
    });

    var clear_discount_form = () => {
        $("#store-promos-discount-data").find('.form-control').each(function(i){
            $(this).val('');
        });
        $('.btn-save-store-promos-discount').addClass('disabled');
    }

    var clear_ads_form = () => {
        $("#store-promos-ads-data").find('.form-control').each(function(i){
            $(this).val('');
        });

        $('.btn-save-store-promos-ads').addClass('disabled');
    }

    $('body').on('keydown', '#field-store-promos-discount', function(event){
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

    $('body').on('keyup', '#field-store-promos-discount', function(event){
        if($(this).val() > 100){
            $(this).val(100);
        }
        if($(this).val() < 0){
            $(this).val(0);
        }
    });

    var promosadsOk = [];

    var validate_promos_ads_form = () => {
        $("#store-promos-ads-data").find('textarea').each(function(i){
            if($(this).val() != '' && $(this).val() != undefined){
                $('.btn-save-store-promos-ads').removeClass('disabled');
            }else{
                $('.btn-save-store-promos-ads').addClass('disabled');
            }
        });
    }

    validate_promos_ads_form();

    var validate_promos_discount_form = () => {
        var field = $("#field-store-promos-discount");
        
        if(field.val() != '' && field.val() != undefined){
            $('.btn-save-store-promos-discount').removeClass('disabled');
        }else{
            $('.btn-save-store-promos-discount').addClass('disabled');
        }
    }

    validate_promos_discount_form();

    $("#field-store-promos-discount").blur(function(){
        var field = $(this);
        var errorLabel = field.parents('.field').find('.error');
        console.log(field.val());
        
        if(field.val() != '' && field.val() != undefined){
            errorLabel.html('');
            $('.btn-save-store-promos-discount').removeClass('disabled');
        }else{
            errorLabel.html(my_ajax_obj.prospects_discount);
            $('.btn-save-store-promos-discount').addClass('disabled');
        }
    });

    $("#store-promos-ads-data").find('textarea').each(function(i){
        var index = i;
        $(this).blur(function(){
            if($(this).val() != '' && $(this).val() != undefined){
                $(this).next('.error').html('');
                promosadsOk[index] = true;
            }else{
                if($(this).attr('id') == 'field-store-promos-ad')
                    $(this).next('.error').html(my_ajax_obj.enter_promo);
                else{
                    $(this).next('.error').html(my_ajax_obj.enter_promo_indications);
                }
                promosadsOk[index] = false;
            }
            validate_promos_ads_form();
        });
    });

    $('a[href="#howto"]').unbind('click');
    $('a[href="#howto"]').click(function (e) {
		e.preventDefault();
        var image = $(this).attr('image-content');
        var message = $(this).attr('message');
        var message_pos = $(this).attr('message-position');
        var modal_width = $(this).attr('modal-width');
        $('.how-to-modal .how-to-modal-box').css('width', modal_width).append('<div class="img-preview"><div class="message '+message_pos+'"><div class="message-arrow"></div><img class="d-block mb-2" src="'+my_ajax_obj.base_url+'/wp-content/themes/tiendas/img/info.svg"/>'+message+'</div><img src="'+image+'" class="img-fluid"/></div>');

        $('.how-to-modal').fadeIn();
	});

    $('.how-to-modal-box-close').unbind('click');
    $('.how-to-modal-box-close').click(function (){
        $('.how-to-modal .how-to-modal-box').find('.img-preview').remove();
		$('.how-to-modal').fadeOut();
	});

    $('body').on('keydown', '#field-store-promos-ad', function(event){
        var wordCount = $(event.target).val().length;
        $('.word-counter span').html(wordCount);

        if ((event.keyCode == 65 && event.ctrlKey === true) || event.keyCode == 190 || event.keyCode == 8 ||
			// Allow: home, end, left, right
		   (event.keyCode >= 35 && event.keyCode <= 39)) {
				// let it happen, don't do anything
				return;
	   }else{
           if(wordCount >= 80){
               return false;
           }
       }
    })

    $('#store-promos-ads-data .btn-continue').click(function(){
        var actionUrl = my_ajax_obj.ajax_url;
        var storeForm = $('#store-promos-ads-data');
        $.post(actionUrl, {action: 'ws', wsa: 'save-store-promos-ads-data', ignore: true, store_id: storeForm.find('input[name="store_id"]').val()}, function(resp){
            $('#myTabPromos').find('#promos-ads-tab').removeClass('working').removeClass('visited').addClass('done');
            check_tab_promos_status();
        });
    });

    $('#store-promos-ads-data').submit(function(e) {
        e.preventDefault();
        var actionUrl = my_ajax_obj.ajax_url;
        var storeForm = $('#store-promos-ads-data');
        var scrollTop = jQuery(window).scrollTop();

        gsap.to(window, {duration: .2, scrollTo:{ y: (scrollTop/2), offsetY: 0}, ease: "power2.outIn", onComplete: function(){}});
        storeForm.find('.form-loader').show();

        $.ajax({
            method: "POST",
            url: actionUrl,
            data: new FormData(this),
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

            $('#howto-popover').attr('data-bs-content', response.msg);
            message('howto-popover');

            storeForm.find('.msg').removeClass('error').removeClass('success').html('').fadeOut();

            navigateNextTab(storeForm);

            $('#myTabPromos').find('#promos-ads-tab').removeClass('working').removeClass('visited').addClass('done');
            check_tab_promos_status();

        }).fail(function() {
            storeForm.find('.msg').addClass('error').removeClass('success').html(my_ajax_obj.error).fadeIn();
            storeForm.find('.form-loader').fadeOut();
        });
    });

    $('#store-promos-discount-data .btn-continue').click(function(){
        var actionUrl = my_ajax_obj.ajax_url;
        var storeForm = $('#store-promos-discount-data');
        $.post(actionUrl, {action: 'ws', wsa: 'save-store-promos-discount-data', ignore: true, store_id: storeForm.find('input[name="store_id"]').val()}, function(resp){
            $('#myTabPromos').find('#promos-discount-tab').removeClass('working').removeClass('visited').addClass('done');
            check_tab_promos_status();
        });
    });

    $('#store-promos-discount-data').submit(function(e) {
        e.preventDefault();
        var actionUrl = my_ajax_obj.ajax_url;
        var storeForm = $('#store-promos-discount-data');
        var scrollTop = jQuery(window).scrollTop();

        gsap.to(window, {duration: .2, scrollTo:{ y: (scrollTop/2), offsetY: 0}, ease: "power2.outIn", onComplete: function(){}});
        storeForm.find('.form-loader').show();

        $.ajax({
            method: "POST",
            url: actionUrl,
            data: new FormData(this),
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

            $('#howto-popover').attr('data-bs-content', response.msg);
            message('howto-popover');

            storeForm.find('.msg').removeClass('error').removeClass('success').html('').fadeOut();

            navigateNextTab(storeForm);

            $('#myTabPromos').find('#promos-discount-tab').removeClass('working').removeClass('visited').addClass('done');
            check_tab_promos_status();

        }).fail(function() {
            storeForm.find('.msg').addClass('error').removeClass('success').html(my_ajax_obj.error).fadeIn();
            storeForm.find('.form-loader').fadeOut();
        });
    });
});
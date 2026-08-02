jQuery(document).ready(function ($) {

    $('.btn-toggle-contact-section').click(function(){
        var storeForm = $('#store-info-contact-data');
        var btn = $(this);
        if(btn.hasClass('is_inactive')){
            console.log(btn);
            
            btn.parents('.side-column').prev('.main-column').removeClass('disabled');
            // btn.parents('.side-column').next('.bottom-column').find('.btn-save-store-about-info').attr('disabled', true).prop('disabled', true);
        
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
            // btn.parents('.side-column').next('.bottom-column').find('.btn-save-store-about-info').attr('disabled', false).prop('disabled', false).removeClass('disabled');
            
            var btninclude = btn.find('.include');
            var btnexclude = btn.find('.exclude');

            btninclude.removeClass('d-none');
            btnexclude.addClass('d-none');
            
            btn.find('svg.remove').addClass('d-none');
            btn.find('svg.add').removeClass('d-none');

            btn.removeClass('is_active').addClass('is_inactive');

            storeForm.find('input[name="disable"]').val('true');

            btn.parent().find('.message-on').hide();
            btn.parent().find('.message-off').show();

            storeForm.find('.bottom-column').fadeOut();

            $.post(my_ajax_obj.ajax_url, {action: 'ws', wsa: 'save-store-info-contact-data', disable: true, store_id: storeForm.find('input[name="store_id"]').val()}, function(resp){
                // console.log(resp);
                $('#myTabInfo').find('#info-contact-tab').removeClass('done').addClass('working');
                // $('#myTabStep3').find('#info-tab').addClass('working').removeClass('done');
                check_tab_info_status();
            });
        }
    });

    var infocontact = false;
    var checks = false;

    var validate_contact_form = () => {

        checks = false;

        $('#store-info-contact-data .form-check-input').each(function(){
            if($(this).is(":checked")){
                checks = true;
            }
        });

        if($('#field-store-info-contact-display-info').val() != '' && checks){
            infocontact = true;
        }else{
            infocontact = false;
        }

        if(infocontact){
            $("#store-info-contact-data").find('input[type="checkbox"], textarea').each(function(){
                $(this).next('.error').html('');
            });

            $('.btn-save-store-contact-info').removeClass('disabled');
        }else{
            $('.btn-save-store-contact-info').addClass('disabled');
        }
    }

    $("#store-info-contact-data").find('input[type="checkbox"]').each(function(){
        $(this).click(function(){
            validate_contact_form();

            if($(this).is(":checked")){
                $(this).parents('.input-group').next('.error').html('');
            }else{
                if(!checks)
                    $(this).parents('.input-group').next('.error').html(my_ajax_obj.select_one_field);
            }
        });
    });

    $("#store-info-contact-data").find('textarea').each(function(){
        $(this).blur(function(){
            validate_contact_form();

            if($(this).val() != ''){
                $(this).next('.error').html('');
            }else{
                $(this).next('.error').html(my_ajax_obj.contact_info);
            }
        });
    });

    validate_contact_form();

    $('#store-info-contact-data .btn-continue').click(function(){
        var actionUrl = my_ajax_obj.ajax_url;
        var storeForm = $('#store-info-contact-data');
        $.post(actionUrl, {action: 'ws', wsa: 'save-store-info-contact-data', ignore: true, store_id: storeForm.find('input[name="store_id"]').val()}, function(resp){
            $('#myTabInfo').find('#info-contact-tab').removeClass('working').addClass('done');
            check_tab_info_status();
        });
    });

    $('#store-info-contact-data').submit(function(e) {
        e.preventDefault();
        var actionUrl = my_ajax_obj.ajax_url;
        var storeForm = $('#store-info-contact-data');
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

            // $('.popover').remove();
            // $('#howto-popover').attr('data-bs-content', response.msg);
            // message('howto-popover');

            popup_message(response.msg, 'info', 2000);

            storeForm.find('.msg').removeClass('error').removeClass('success').html('').fadeOut();

            $('#myTabInfo').find('#info-contact-tab').removeClass('working').removeClass('visited').addClass('done');
            check_tab_info_status();

            navigateNextTab(storeForm);

        }).fail(function() {
            // storeForm.find('.msg').addClass('error').removeClass('success').html(my_ajax_obj.error).fadeIn();
            popup_message(my_ajax_obj.error, 'error', 2000);
            storeForm.find('.form-loader').fadeOut();
        });
    });

});
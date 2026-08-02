jQuery(document).ready(function ($) {

    /**
    ================================================================================================================================
    ================================================================================================================================
    UPLOAD shipping policy files
    ================================================================================================================================
    ================================================================================================================================
    */

    var storeForm = $('#store-info-policy');
    const uploaderPolicyFiles = new UploadController('.field-upload-info-policy-files', 'policy-files', storeForm.find('input[name="store_id"]').val());

    //====================================================================================================================================================================================
    //====================================================================================================================================================================================
    //====================================================================================================================================================================================

    $('.btn-toggle-policy-section').click(function(){
        var storeForm = $('#store-info-policy');
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
            btn.parent().find('.message-on').hide();
            btn.parent().find('.message-off').show();
            storeForm.find('.bottom-column').fadeOut();

            $.post(my_ajax_obj.ajax_url, {action: 'ws', wsa: 'save-store-info-policy', disable: true, store_id: storeForm.find('input[name="store_id"]').val()}, function(resp){
                // console.log(resp);
                $('#myTabInfo').find('#info-policy-tab').removeClass('done').addClass('working');
                // $('#myTabStep3').find('#info-tab').addClass('working').removeClass('done');
                check_tab_info_status();
            });
        }
    });

    $('input[name="field-store-info-policy-option"]').click(function() {
        var field = $(this);

        $('.policy-group').hide();
        $('.'+field.val()).fadeIn();
    });

    $('input[name="field-store-info-policy-devolutions"]').click(function() {
        if($(this).val() == 'Si'){
            $('.devolutions-conditions').fadeIn();
        }else{
            $('.devolutions-conditions').fadeOut();
        }
    });

    var validate_policy_form = () => {

        var infopolicy = false;

        if($("#field-store-info-policy-active").is(":checked")) {
            const filePolicy = $('#field-store-info-policy-files');
            
            if((isValidUrl($('#field-store-info-policy-privacy-url').val()) || filePolicy[0].files.length !== 0) && 
            $('#field-store-info-policy-email').val() != '' && 
            $('#field-store-info-policy-name').val() != '' && 
            $('#field-store-info-policy-address').val() != ''){
                infopolicy = true;
            }else{
                infopolicy = false;
            }
        }else if($("#field-store-info-policy-inactive").is(":checked")) {
        
            if($('#field-store-info-policy-email').val() != '' && 
            $('#field-store-info-policy-name').val() != '' && 
            $('#field-store-info-policy-address').val() != '' &&
            $('#field-store-info-policy-proccess-time').val() != '*' &&
            $('#field-store-info-policy-delivery-time').val() != '*' &&
            $('#field-store-info-policy-taxes').val() != '*'){
                var conditios_value = '';
                $('input[name="field-store-info-policy-devolutions"]').each(function(){
                    if($(this).is(":checked")){
                        conditios_value = $(this).val();
                    }
                })

                console.log(conditios_value);
                
                if(conditios_value == 'Si' && $('#field-store-info-policy-devolutions-conditions').val() != ''){
                    infopolicy = true;
                }else if(conditios_value == 'No'){
                    infopolicy = true;
                }else{
                    infopolicy = false;
                }
            }else{
                infopolicy = false;
            }

        }else{
            infopolicy = false;
        }

        if(infopolicy){

            $("#store-info-policy").find('input[type="text"]').each(function(){
                $(this).next('.error').html('');
            });

            $('.btn-save-store-policy-info').removeClass('disabled');
        }else{
            $('.btn-save-store-policy-info').addClass('disabled');
        }
    }

    $('body').on('click', 'input[name="field-store-info-policy-devolutions"]', function(){
        validate_policy_form();
    })

    $('body').on('keyup', '#field-store-info-policy-devolutions-conditions', function(){
        validate_policy_form();
    })

    $("#store-info-policy").find('input[type="text"]').each(function(){
        $(this).blur(function(){
            if($(this).hasClass('email')){
                if(isValidEmail($(this).val())){
                    $(this).next('.error').html('');
                }else{
                    var fieldname = $(this).prev().html().replace(":", "");
                    $(this).next('.error').html(fieldname+my_ajax_obj.not_valid);
                }
            }else if($(this).hasClass('url')){
                if(isValidUrl($(this).val())){
                    $(this).next('.error').html('');
                }else{
                    var fieldname = $(this).prev().html().replace(":", "");
                    $(this).next('.error').html(fieldname+my_ajax_obj.not_valid);
                }
            }else{
                if($(this).val() != ''){
                    $(this).next('.error').html('');
                }else{
                    $(this).next('.error').html(my_ajax_obj.required_field);
                }
            }

            validate_policy_form();
        });
    });

    $("#store-info-policy").find('select').each(function(){
        $(this).change(function(){
            if($(this).val() != '*'){
                $(this).next('.error').html('');
            }else{
                $(this).next('.error').html(my_ajax_obj.required_field);
            }

            validate_policy_form();
        });
    });

    $('#field-store-info-policy-inactive, #field-store-info-policy-active').change(function(){
        validate_policy_form();
    });

    $('#store-info-policy .btn-continue').click(function(){
        var actionUrl = my_ajax_obj.ajax_url;
        var storeForm = $('#store-info-policy');
        $.post(actionUrl, {action: 'ws', wsa: 'save-store-info-policy', ignore: true, store_id: storeForm.find('input[name="store_id"]').val()}, function(resp){
            $('#myTabInfo').find('#info-policy-tab').removeClass('working').addClass('done');
            check_tab_info_status();
        });
    });

    $('#store-info-policy').submit(function(e) {
        e.preventDefault();
        var actionUrl = my_ajax_obj.ajax_url;
        var storeForm = $('#store-info-policy');
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

            $('#myTabInfo').find('#info-policy-tab').removeClass('working').removeClass('visited').addClass('done');
            check_tab_info_status();
            
            navigateNextTab(storeForm);

        }).fail(function() {
            // storeForm.find('.msg').addClass('error').removeClass('success').html(my_ajax_obj.error).fadeIn();
            popup_message(my_ajax_obj.error, 'error', 2000);
            storeForm.find('.form-loader').fadeOut();
        });
    });

});


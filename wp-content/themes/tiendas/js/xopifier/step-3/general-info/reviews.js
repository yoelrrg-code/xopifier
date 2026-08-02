var step3_input_info_reviews_files = [];

jQuery(document).ready(function ($) {

    Fancybox.bind('[data-fancybox]', {
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

    /**
    ================================================================================================================================
    ================================================================================================================================
    UPLOAD reviews file
    ================================================================================================================================
    ================================================================================================================================
    */

    var storeForm = $('#store-info-reviews-data');
    const uploaderReviewFiles = new UploadController('.field-upload-info-reviews-files', 'reviews-files', storeForm.find('input[name="store_id"]').val());

    //====================================================================================================================================================================================
    //====================================================================================================================================================================================
    //====================================================================================================================================================================================

    $('.btn-toggle-reviews-section').click(function(){
        var storeForm = $('#store-info-reviews-data');
        var btn = $(this);
        if(btn.hasClass('is_inactive')){
            btn.parents('.main-column').removeClass('disabled');
            btn.removeClass('is_inactive').addClass('is_active');
            storeForm.find('input[name="disable"]').val('false');

            var form_tips = $('.side-column').find('.form-tip');
            form_tips.each(function(){
                if($(this).hasClass('extra')){
                    $(this).hide();
                }else{
                    $(this).fadeIn();
                }
            });

            //muestro el mensaje de aumento de precio
            $('.popover').remove();

            $('.reviews-extra-form-modal').fadeOut();

            var total_price = storeForm.find('input[name="total_price"]').val();
            var service_price = storeForm.find('input[name="service_price"]').val();

            gsap.to($('.overlay-bg'), .3, {zIndex: '99999', opacity: 1, ease: Power2.easeOut, onComplete: function() {}});

            $('#howto-popover').attr('data-bs-content', my_ajax_obj.add_extra+(Number(total_price) + Number(service_price))+"</b>");
            message('howto-popover');
            setTimeout(function(){
                gsap.to($('.overlay-bg'), .2, {zIndex: '-1', opacity: 0, ease: Power2.easeIn, onComplete: function() {
                    jQuery('.popover').fadeOut(200, function(){
                        jQuery(this).remove();
                    });
                }}); 
            }, 2000);
        }else if(btn.hasClass('is_active')){
            storeForm.find('.main-column').addClass('disabled');
            $('.btn-toggle-reviews-section').removeClass('is_active').addClass('is_inactive');
            storeForm.find('input[name="disable"]').val('true');

            $('.reviews-extra-form-modal').fadeOut();

            var form_tips = $('.side-column').find('.form-tip');
            form_tips.each(function(){
                if($(this).hasClass('extra')){
                    $(this).fadeIn();
                }else{
                    $(this).hide();
                }
            })

            storeForm.find('#btn-save-store-reviews-info').addClass('disabled');

            $.post(my_ajax_obj.ajax_url, {action: 'ws', wsa: 'save-store-info-reviews-data', disable: true, store_id: storeForm.find('input[name="store_id"]').val()}, function(resp){
                $('#myTabInfo').find('#'+storeForm.find('input[name="tab_id"]').val()).removeClass('done').addClass('working');
                $('#myTabStep3').find('#info-tab').addClass('working').removeClass('done');

                if(resp.total_price != undefined){
                    update_prices(resp.total_price)
                }

                update_popup(storeForm);
                navigateNextTab(storeForm);
            });
        }
    });

    $('.btn-reviews-open-modal').click(function(){
        $('.reviews-extra-form-modal').fadeIn();
    })

    $('.btn-reviews-close-modal').click(function(){
        $('.reviews-extra-form-modal').fadeOut();
    })

    var validate_reviews_form = () => {
        var inforeviews = false;

        const fileReviews = $('#field-upload-info-reviews-files');

        if($('#field-store-info-reviews-url').val() != '' && isValidUrl($('#field-store-info-reviews-url').val())){
            inforeviews = true;
        }else if($('#field-store-info-reviews-url').val() != '' && !isValidUrl($('#field-store-info-reviews-url').val())){
            inforeviews = false;
        }else if(fileReviews[0].files.length !== 0){
            inforeviews = true;
        }else{
            inforeviews = false;
        }

        if(inforeviews){
            $("#store-info-reviews-data").find('input[type="text"]').each(function(){
                $(this).next('.error').html('');
            });

            $('.btn-save-store-reviews-info').removeClass('disabled');
        }else{
            $('.btn-save-store-reviews-info').addClass('disabled');
        }
    }

    $("#store-info-reviews-data").find('input[type="text"]').each(function(){
        $(this).blur(function(){
            if($(this).hasClass('url')){
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
                    var fieldname = $(this).prev().html().replace(":", "");
                    $(this).next('.error').html(fieldname+my_ajax_obj.not_valid);
                }
            }

            validate_reviews_form();
        });
    });

    $('#store-info-reviews-data .btn-continue').click(function(){
        var actionUrl = my_ajax_obj.ajax_url;
        var storeForm = $('#store-info-reviews-data');
        $.post(actionUrl, {action: 'ws', wsa: 'save-store-info-reviews-data', ignore: true, tab_id: storeForm.find('input[name="tab_id"]').val(), store_id: storeForm.find('input[name="store_id"]').val()}, function(resp){
            $('#myTabInfo').find('#'+storeForm.find('input[name="tab_id"]').val()).removeClass('working').addClass('done');
            check_tab_info_status();
        });
    });

    $('#store-info-reviews-data').submit(function(e) {
        e.preventDefault();
        var actionUrl = my_ajax_obj.ajax_url;
        var storeForm = $('#store-info-reviews-data');
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

            if(response.error != 'false'){
                if(response.total_price != undefined){
                    update_prices(response.total_price);
                }

                storeForm.find('.msg').removeClass('error').removeClass('success').html('').fadeOut();
                $('#myTabInfo').find('#'+storeForm.find('input[name="tab_id"]').val()).removeClass('working').removeClass('visited').addClass('done');
                check_tab_info_status();

                update_popup(storeForm);

                navigateNextTab(storeForm);
            }else{
                popup_message(response.msg, 'error', 2000);
            }
        }).fail(function() {
            // storeForm.find('.msg').addClass('error').removeClass('success').html(my_ajax_obj.error).fadeIn();
            popup_message(my_ajax_obj.error, 'info', 2000);
            storeForm.find('.form-loader').fadeOut();
        });
    });

});
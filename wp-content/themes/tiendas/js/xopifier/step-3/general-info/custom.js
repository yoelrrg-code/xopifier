var step3_input_info_custom_page_files = [];

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
    UPLOAD blog files
    ================================================================================================================================
    ================================================================================================================================
    */
    var storeForm = $('#store-info-custom-data');
    const uploaderCustomFiles = new UploadController('.field-store-info-custom-page-files', 'custom-files', storeForm.find('input[name="store_id"]').val());

    //====================================================================================================================================================================================
    //====================================================================================================================================================================================
    //====================================================================================================================================================================================

    $('.btn-toggle-custom-page-section').click(function(){
        var storeForm = $('#store-info-custom-data');
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
            $('.btn-toggle-custom-section').removeClass('is_active').addClass('is_inactive');
            storeForm.find('input[name="disable"]').val('true');

            $('.custom-extra-form-modal').fadeOut();

            var form_tips = $('.side-column').find('.form-tip');
            form_tips.each(function(){
                if($(this).hasClass('extra')){
                    $(this).fadeIn();
                }else{
                    $(this).hide();
                }
            })

            storeForm.find('#btn-save-store-custom-page-info').addClass('disabled');

            $.post(my_ajax_obj.ajax_url, {action: 'ws', wsa: 'save-store-info-custom-data', disable: true, store_id: storeForm.find('input[name="store_id"]').val()}, function(resp){
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

    if($('#custom-page-editor').length){
        const quill = new Quill('#custom-page-editor', {
            modules: {
                toolbar: false    // Snow includes toolbar by default
            },
            theme: 'snow'
        });

        var quillHtml = quill.getSemanticHTML();
        $('#field-store-info-custom-content').val(quillHtml);

        quill.on('text-change', (delta, oldDelta, source) => {
            if (source == 'user') {
                var quillHtml = quill.getSemanticHTML();
                // console.log(quillHtml);
                
                $('#field-store-info-custom-content').val(quillHtml);
                validate_custom_page_form();
            }
        });
    }

    $('.btn-custom-open-modal').click(function(){
        $('.custom-extra-form-modal').fadeIn();
    })

    $('.btn-custom-close-modal').click(function(){
        $('.custom-extra-form-modal').fadeOut();
    })

    var validate_custom_page_form = () => {

        var infoblog = false;
        const filescustompage = $('#field-store-info-custom-page-files');

        if(($('#field-store-info-custom-content').val() != '' && $('#field-store-info-custom-content').val() != '<p></p>' && $('#field-store-info-custom-content').val() != '<p>&nbsp;</p>') ||  filescustompage[0].files.length !== 0){
            infoblog = true;
        }else{
            infoblog = false;
        }

        if(infoblog){
            $("#store-info-custom-data").find('input[type="text"], textarea').each(function(){
                $(this).next('.error').html('');
            });

            $('.btn-save-store-custom-page-info').removeClass('disabled');
        }else{
            $('.btn-save-store-custom-page-info').addClass('disabled');
        }
    }

    validate_custom_page_form();

    $('#store-info-custom-data .btn-continue').click(function(){
        var actionUrl = my_ajax_obj.ajax_url;
        var storeForm = $('#store-info-custom-data');
        $.post(actionUrl, {action: 'ws', wsa: 'save-store-info-custom-data', ignore: true, tab_id: storeForm.find('input[name="tab_id"]').val(), store_id: storeForm.find('input[name="store_id"]').val()}, function(resp){
            $('#myTabInfo').find('#'+storeForm.find('input[name="tab_id"]').val()).removeClass('working').removeClass('visited').addClass('done');
            check_tab_info_status();
        });
    });

    $('#store-info-custom-data').submit(function(e) {
        e.preventDefault();
        var actionUrl = my_ajax_obj.ajax_url;
        var storeForm = $('#store-info-custom-data');
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

            $('#myTabInfo').find('#'+storeForm.find('input[name="tab_id"]').val()).removeClass('working').removeClass('visited').addClass('done');
            check_tab_info_status();

            update_popup(storeForm);

            navigateNextTab(storeForm);

        }).fail(function() {
            // storeForm.find('.msg').addClass('error').removeClass('success').html(my_ajax_obj.error).fadeIn();
            popup_message(my_ajax_obj.error, 'error', 2000);
            storeForm.find('.form-loader').fadeOut();
        });
    });

});
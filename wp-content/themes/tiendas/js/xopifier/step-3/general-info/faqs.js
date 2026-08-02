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

    if($('#faqs-editor').length){
        const quill = new Quill('#faqs-editor', {
            modules: {
                toolbar: false    // Snow includes toolbar by default
            },
            theme: 'snow'
        });

        var quillHtml = quill.getSemanticHTML();
        $('#field-store-info-faqs').val(quillHtml);

        quill.on('text-change', (delta, oldDelta, source) => {
            if (source == 'user') {
                var quillHtml = quill.getSemanticHTML();
                console.log(quillHtml)
                $('#field-store-info-faqs').val(quillHtml);
            }
        });
    }

    $('.btn-toggle-faqs-section').click(function(){
        var storeForm = $('#store-info-faqs-data');
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

            $('.faqs-extra-form-modal').fadeOut();

            var total_price = storeForm.find('input[name="total_price"]').val();
            var service_price = storeForm.find('input[name="service_price"]').val();

            gsap.to($('.overlay-bg'), .3, {zIndex: '99999', opacity: 1, ease: Power2.easeOut, onComplete: function() {}});

            // console.log(Number(total_price));
            // console.log(Number(service_price));

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
            $('.btn-toggle-faqs-section').removeClass('is_active').addClass('is_inactive');
            storeForm.find('input[name="disable"]').val('true');

            $('.faqs-extra-form-modal').fadeOut();

            var form_tips = $('.side-column').find('.form-tip');
            form_tips.each(function(){
                if($(this).hasClass('extra')){
                    $(this).fadeIn();
                }else{
                    $(this).hide();
                }
            })

            storeForm.find('#btn-save-store-faqs-info').addClass('disabled');

            $.post(my_ajax_obj.ajax_url, {action: 'ws', wsa: 'save-store-info-faqs-data', disable: true, store_id: storeForm.find('input[name="store_id"]').val()}, function(resp){
                $('#myTabInfo').find('#'+storeForm.find('input[name="tab_id"]').val()).removeClass('done').addClass('working');
                $('#myTabStep3').find('#info-tab').addClass('working').removeClass('done');

                if(resp.total_price != undefined){
                    update_prices(resp.total_price);
                }

                update_popup(storeForm);
                navigateNextTab(storeForm);
            });
        }
    });

    $('.btn-faqs-open-modal').click(function(){
        $('.faqs-extra-form-modal').fadeIn();
    })

    $('.btn-faqs-close-modal').click(function(){
        $('.faqs-extra-form-modal').fadeOut();
    })

    var validate_faqs_form = () => {

        var infofaqs = false;

        if($('#field-store-info-faqs-url').val() != '' && isValidUrl($('#field-store-info-faqs-url').val())){
            infofaqs = true;
        }else if($('#field-store-info-faqs-url').val() != '' && !isValidUrl($('#field-store-info-faqs-url').val())){
            infofaqs = false;
        }else{
            infofaqs = false;
        }

        if(infofaqs){
            $("#store-info-faqs-data").find('input[type="text"], textarea').each(function(){
                $(this).next('.error').html('');
            });

            $('.btn-save-store-faqs-info').removeClass('disabled');
        }else{
            $('.btn-save-store-faqs-info').addClass('disabled');
        }
    }

    validate_faqs_form();

    $("#store-info-faqs-data").find('input[type="text"]').each(function(){
        $(this).blur(function(){
            if($(this).hasClass('url')){
                if(isValidUrl($(this).val())){
                    $(this).next('.error').html('');
                }else{
                    $(this).next('.error').html(my_ajax_obj.valid_url);
                }
            }else{
                if($(this).val() != ''){
                    $(this).next('.error').html('');
                }else{
                    $(this).next('.error').html(my_ajax_obj.not_valid_text);
                }
            }

            validate_faqs_form();
        });
    });

    $('#store-info-faqs-data .btn-continue').click(function(){
        var actionUrl = my_ajax_obj.ajax_url;
        var storeForm = $('#store-info-faqs-data');
        $.post(actionUrl, {action: 'ws', wsa: 'save-store-info-faqs-data', ignore: true, tab_id: storeForm.find('input[name="tab_id"]').val(), store_id: storeForm.find('input[name="store_id"]').val()}, function(resp){
            $('#myTabInfo').find('#'+storeForm.find('input[name="tab_id"]').val()).removeClass('working').addClass('done');
            check_tab_info_status();
        });
    });

    $('#store-info-faqs-data').submit(function(e) {
        e.preventDefault();
        var actionUrl = my_ajax_obj.ajax_url;
        var storeForm = $('#store-info-faqs-data');
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

            if(response.error != 'false'){
                if(response.total_price != undefined){
                    update_prices(response.total_price);
                }

                $('#myTabInfo').find('#'+storeForm.find('input[name="tab_id"]').val()).removeClass('working').removeClass('visited').addClass('done');
                check_tab_info_status();

                update_popup(storeForm);

                navigateNextTab(storeForm);
            }else{
                popup_message(response.msg, 'error', 2000);
            }

        }).fail(function() {
            // storeForm.find('.msg').addClass('error').removeClass('success').html(my_ajax_obj.error).fadeIn();
            popup_message(my_ajax_obj.error, 'error', 2000);
            storeForm.find('.form-loader').fadeOut();
        });
    });

});
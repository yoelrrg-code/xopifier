jQuery(document).ready(function ($) {

    /**
        ================================================================================================================================
        ================================================================================================================================
        UPLOAD images or videos in general information
        ================================================================================================================================
        ================================================================================================================================
    */

    var storeForm = $('#store-about-info-form');
    const uploaderAboutFiles = new UploadController('.field-upload-images-videos', 'about-files', storeForm.find('input[name="store_id"]').val());

    //====================================================================================================================================================================================
    //====================================================================================================================================================================================
    //====================================================================================================================================================================================

    $('#field-store-featured-options').change(function(){
        if($(this).val() != 0){
            if($(this).val() == 1){
                $('.field.featured-option-1').fadeIn();
                $('.field.featured-option-2').hide();
                uploaderAboutFiles.clearFiles();
                $('.field.featured-option-3').fadeIn();
                $('.field.featured-option-4').hide().find('input').val('');
            }else if($(this).val() == 5){
                $('.field.featured-option-1').hide();
                $('.field.featured-option-2').fadeIn();
                $('.field.featured-option-3').fadeIn();
                $('.field.featured-option-4').fadeIn();
            }else{
                $('.field.featured-option-1').hide();
                $('.field.featured-option-2').fadeIn();
                $('.field.featured-option-3').fadeIn();
                $('.field.featured-option-4').hide().find('input').val('');
            }
        }else{
            $('.field.featured-option').fadeOut();
            $('.field.featured-option').find('input').val('');
            $('.field.featured-option').find('textarea').val('');
            uploaderAboutFiles.clearFiles();
        }
    });

    $('body').on('keyup', '#field-store-description', function(){
		verify_about_form_data();
	});

    $('body').on('change', '#field-store-featured-options', function(){
		verify_about_form_data();
	});

    var verify_about_form_data = () => {
        var storedesc = $('#field-store-description').val();
        var storefeatured = $('#field-store-featured-options').val();

        if(storedesc != '' && storefeatured != 0){
            $('.btn-save-store-about-info').removeClass('disabled');
        }else{
            $('.btn-save-store-about-info').addClass('disabled');
        }
    }

    verify_about_form_data();

    $('#store-about-info-form').validate({
        lang: 'en',
        rules: {
            "field-store-description": {
              required: true
            }
        },
        submitHandler: function(form) {
            var actionUrl = my_ajax_obj.ajax_url;
            var storeForm = $('#store-about-info-form');

            var scrollTop = jQuery(window).scrollTop();
            gsap.to(window, {duration: .2, scrollTo:{ y: (scrollTop/2), offsetY: 0}, ease: "power2.outIn", onComplete: function(){}});
            storeForm.find('.form-loader').show();
            
            $.ajax({
                method: "POST",
                url: actionUrl,
                crossDomain: true,
                data: new FormData(document.getElementById('store-about-info-form')),
                dataType: "json",
                contentType: "multipart/form-data",
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

                $('#myTabInfo').find('#info-store-tab').removeClass('working').removeClass('visited').addClass('done');
                check_tab_info_status();

                navigateNextTab(storeForm);

            }).fail(function() {
                popup_message(my_ajax_obj.error, 'error', 2000);
                storeForm.find('.form-loader').fadeOut();
            });

            return false;
        }
    });

});
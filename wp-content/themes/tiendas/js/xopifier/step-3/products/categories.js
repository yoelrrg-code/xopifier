
jQuery(document).ready(function ($) {
    
    $('#myTabContentProducts').find('.btn-plus-categ').click(function(e){
        e.preventDefault();

        var btnplus = $(this);

        var repeater_field = $('#myTabContentProducts').find('.category-repeater-fields').find('.repeater-field');
        
        var field = $('<div class="repeater-field mb-2">'+$(repeater_field[0]).html()+'</div>');

        field.find('.field').each(function(){
            var id = $(this).find('.form-control').attr('id');
            id = id.replace('-1', '-'+(repeater_field.length + 1));
            $(this).find('.form-control').attr('id', id);
            $(this).find('.form-control').val('asdfgasd').attr('value', '');

            var label = $(this).find('.form-label');
            var label_for = label.attr('for').replace('-1', '-'+(repeater_field.length + 1));
            var label_text = 'Categor&iacute;a '+(repeater_field.length + 1)+':';
            $(this).find('.form-label').attr('for', label_for).html(label_text);

        });

        $('#myTabContentProducts').find('.category-repeater-fields').append('<div class="repeater-field mb-2">'+field.html()+'</div>');

        if(repeater_field.length + 1 == 10){
            btnplus.hide();
        }
    });

    $('#store-products-categories-form').find('.btn-save-categories').click(function(e){
        e.preventDefault();

        var actionUrl = my_ajax_obj.ajax_url;
        var storeForm = $('#store-products-categories-form');

        var scrollTop = jQuery(window).scrollTop();

        gsap.to(window, {duration: .2, scrollTo:{ y: (scrollTop/2), offsetY: 0}, ease: "power2.outIn", onComplete: function(){
            storeForm.find('.form-loader').show();
            $.ajax({
                method: "POST",
                url: actionUrl,
                crossDomain: true,
                data: new FormData(document.getElementById('store-products-categories-form')),
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

                // $('#howto-popover').attr('data-bs-content', response.msg);
                // message('howto-popover');

                popup_message(response.msg, 'info', 2000);

                var storeID = $('.products-tab-new-product').attr('store-id');
                $.post(my_ajax_obj.ajax_url, {action: 'ws', wsa: 'get-store-categories', storeID: storeID}, function(response){
                    $('#field-fromLinkProductCategory').html(response.categories);
                });

                $('#myTabProducts').find('#products-categories-tab').removeClass('working').removeClass('visited').addClass('done');
                check_tab_products_status();

                navigateNextTab(storeForm);

            }).fail(function() {
                // storeForm.find('.msg').addClass('error').removeClass('success').html(my_ajax_obj.error).fadeIn();
                popup_message(my_ajax_obj.error, 'error', 2000);
                storeForm.find('.form-loader').fadeOut();
            });
        }});
    });

});
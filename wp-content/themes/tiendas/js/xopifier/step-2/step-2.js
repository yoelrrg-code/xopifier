jQuery(document).ready(function ($) {

    const $loginForm = $("#form-login-step-2");

    $loginForm.submit(function(e){
        e.preventDefault();
        var actionUrl = my_ajax_obj.ajax_url;
        var $form = $(this);

        $form.find('.form-loader').show();

        var ok = true;
        $form.find('.error:not(.msg)').each(function(){
            if($(this).html() != ''){
                ok = false;
            }
        });

        if(ok){
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
                if(response.loggedid == false){
                    $('.error.msg').html(response.message).fadeIn();
                    $form.find('.form-loader').fadeOut();
                }else{
                    window.location.reload();
                }
            }).fail(function() {
                $('.error.msg').html(my_ajax_obj.error).fadeIn();
                $form.find('.form-loader').fadeOut();
            });

        }else{
            $('.error.msg').html(my_ajax_obj.error1).fadeIn();
            $form.find('.form-loader').fadeOut();
        }
    });

    $('button[href="#continue-step-2"]').click(function(){
        $('.welcome-step-2').hide();
        $('.tabs-step-2').fadeIn();
    });

    $('a[href="#goto-design"]').click(function(){
        $('.designs-popups-container, .designs-popups-container .design-popup').hide();

        $('body').css({
            'overflow-y': 'unset',
            'overflow-x': 'hidden',
        });
    });
    
    $('.btn-view-store').click(function(){
        var url = $(this).attr('store-url');
        if (url) {
            window.open(url, '_blank');
        }
    });

    $('.btn-open-sidebar').click(function() {
        $('#popup-resume').fadeIn(200, function() {
            gsap.to($('#popup-resume #popup-resume-box'), .5, {right: '0px', ease: Power2.easeOut, onComplete: function() {}});
        });
    });

    $('.btn-select-no-design').click(function() {
        $('.unselect-design-form-modal').fadeIn();
        $('#field-unselect-design-text').focus();
    });

    $('.close-unselect-design-form-modal').click(function() {
        $('#field-unselect-design-text').val('');
        $('.unselect-design-form-modal').fadeOut();
    });

    $('.close-select-design-form-modal').click(function() {
        $('#field-select-design-text').val('');
        $('.select-design-form-modal').fadeOut();
        $('#field-select-design-text').parents('.field').next('.error').html('').hide();
    });

    $('.close-select-design-comment').click(function(){
        $('.select-design-comment').hide();
        $('.select-design-question').fadeIn();
    });

    $('.popup-resume-close').click(function() {
        gsap.to($('#popup-resume #popup-resume-box'), .5, {right: '-100vw', ease: Power2.easeIn, onComplete: function() {
            $('#popup-resume').fadeOut();
        }});
    });

    $('.designs-popups-container .design-popup-images').each(function(){
        $(this).scroll(function () {
            var scrollTop = $(this).scrollTop();
            var designImagesHeight = $(this).find('.design-image.active')[0].scrollHeight - 630;
        });
    });

    $('.designs-popups-container .design-popup').each(function(){
        var design_popup = $(this);
        design_popup.find('.arrow-left').click(function(){
            if(!$(this).hasClass('disabled')){
                var active_image = design_popup.find('.design-popup-images').find('.design-image.active');
                active_image.removeClass('active');
                active_image.prev().addClass('active');
                check_nav_arrows(design_popup.attr('design'));
                design_popup.find('.current-design').html(active_image.prev().attr('title'));
                design_popup.find('.active-design').html(active_image.prev().attr('alt'));
            }
        });

        design_popup.find('.arrow-right').click(function(){
            if(!$(this).hasClass('disabled')){
                var active_image = design_popup.find('.design-popup-images').find('.design-image.active');
                active_image.removeClass('active');
                active_image.next().addClass('active');
                check_nav_arrows(design_popup.attr('design'));
                design_popup.find('.current-design').html(active_image.next().attr('title'));
                design_popup.find('.active-design').html(active_image.next().attr('alt'));
            }
        });
    });

    var check_nav_arrows = (design) => {

        var images = $('.'+design).find('.design-popup-images').find('.design-image');
        
        if($(images[0]).hasClass('active')){
            $('.'+design).find('.arrow-left').addClass('disabled');
            $('.'+design).find('.arrow-right').removeClass('disabled');
        }else if($(images[images.length - 1]).hasClass('active')){
            $('.'+design).find('.arrow-right').addClass('disabled');
            $('.'+design).find('.arrow-left').removeClass('disabled');
        }else{
            $('.'+design).find('.arrow-right').removeClass('disabled');
            $('.'+design).find('.arrow-left').removeClass('disabled');
        }
    }

    $('body').on('click', '.btn-select-design-modal', function(){
        var design = $(this).attr('design');
        var product_image = $(this).attr('product-image-id');
        var product_name = $(this).attr('product-name');
        var store_id = $(this).attr('store-id');

        $('.select-design-form-modal').find('.btn-select-design')
            .attr('store-id', store_id)
            .attr('product-image-id', product_image)
            .attr('product-name', product_name)
            .attr('design', design);

        $('.select-design-form-modal').find('.btn-select-design-comment')
            .attr('store-id', store_id)
            .attr('product-image-id', product_image)
            .attr('product-name', product_name)
            .attr('design', design);

        $('.select-design-question').show();
        $('.select-design-comment').hide();
        $('.select-design-form-modal').fadeIn();
    });

    $('body').on('click', '.btn-comment-select-design', function(){
        $('.select-design-form-modal .select-design-question').hide();
        $('.select-design-form-modal .select-design-comment').fadeIn();
    });

    $('body').on('click', '.btn-select-design', function(){
        var design = $(this).attr('design');
        var product_image = $(this).attr('product-image-id');
        var product_name = $(this).attr('product-name');
        var store_id = $(this).attr('store-id');
        var design_comment = $('#field-select-design-text').val();

        $('#select-design-form').find('.form-loader').fadeIn();

        $('#set-percent-payment').find('input[name="product-featured-image"]').val(product_image);
        $('#set-percent-payment').find('input[name="product-name"]').val(product_name);
        $('#set-percent-payment').find('input[name="product-store-id"]').val(store_id);
        $('#set-percent-payment').find('input[name="product-approved-design"]').val(design);
        $('#set-percent-payment').find('input[name="product-approved-design-comment"]').val(design_comment);
        
        var actionUrl = my_ajax_obj.ajax_url;
        var storeForm = $('#set-percent-payment');

        $.ajax({
            method: "POST",
            url: actionUrl,
            crossDomain: true,
            data: new FormData(document.getElementById('set-percent-payment')),
            dataType: "json",
            contentType: "multipart/form-data",
            processData: false,
            contentType: false,
            headers: {
                "Accept": "application/json"
            }
        }).done(function(response) {
            if(response.error == false){
                $('#select-design-form').find('.form-loader').fadeOut();

                $('.select-design-question').hide();
                $('.select-design-normal-finish').fadeIn();
            }else{
                $('.select-design-question').find('.alert').html(response.message).removeClass('d-none');
                $('#select-design-form').find('.form-loader').fadeOut();
            }
        }).fail(function() {
            $('.select-design-question').find('.alert').removeClass('d-none');
            $('#select-design-form').find('.form-loader').fadeOut();
        });
    });

    $('#field-select-design-option').change(function(){
        if($(this).val() != ''){
            $('#field-select-design-text').parent().removeClass('disabled');
            $('#field-select-design-text').val('');
            $('#field-select-design-text').removeAttr('disabled');
        }else{
            $('#field-select-design-text').parent().addClass('disabled');
            $('#field-select-design-text').val('');
            $('#field-select-design-text').attr('disabled', 'disabled');
        }
    });

    $('body').on('click', '.btn-select-design-comment', function(){
        var design = $(this).attr('design');
        var product_image = $(this).attr('product-image-id');
        var product_name = $(this).attr('product-name');
        var store_id = $(this).attr('store-id');
        var design_comment = $('#field-select-design-text').val();
        var design_comment_option = $('#field-select-design-option').val();

        $('#select-design-form').find('.form-loader').fadeIn();

        if(design_comment == ''){
            $('#field-select-design-text').parents('.field').next('.error').html(my_ajax_obj.adjust_proposal).show();
            $('#select-design-form').find('.form-loader').fadeOut();
            return;
        }else{
            $('#field-select-design-text').parents('.field').next('.error').html('').hide();
        }

        $('#set-percent-payment').find('input[name="product-featured-image"]').val(product_image);
        $('#set-percent-payment').find('input[name="product-name"]').val(product_name);
        $('#set-percent-payment').find('input[name="product-store-id"]').val(store_id);
        $('#set-percent-payment').find('input[name="product-approved-design"]').val(design);
        $('#set-percent-payment').find('input[name="product-approved-design-comment"]').val(design_comment_option+': '+design_comment);
        $('#set-percent-payment').attr('select-status', 'comment');

        var actionUrl = my_ajax_obj.ajax_url;

        $.ajax({
            method: "POST",
            url: actionUrl,
            crossDomain: true,
            data: new FormData(document.getElementById('set-percent-payment')),
            dataType: "json",
            contentType: "multipart/form-data",
            processData: false,
            contentType: false,
            headers: {
                "Accept": "application/json"
            }
        }).done(function(response) {
            if(response.error == false){
                $('#select-design-form').find('.form-loader').fadeOut();

                $('.select-design-comment').hide();
                $('.select-design-comment-finish').fadeIn();
            }else{
                $('.select-design-comment').find('.alert').html(response.message).removeClass('d-none');
                $('#select-design-form').find('.form-loader').fadeOut();
            }
        }).fail(function() {
            $('.select-design-comment').find('.alert').removeClass('d-none');
            $('#select-design-form').find('.form-loader').fadeOut();
        });
    });

    $('#unselect-design-form').submit(function(e){
        e.preventDefault();
        var actionUrl = my_ajax_obj.ajax_url;
        var form = $(this);
        form.find('.form-loader').fadeIn();

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
                window.location = my_ajax_obj.base_url+"/paso-2-rechazado/";
            }else{
                form.find('.alert').html(response.message).removeClass('d-none');
                form.find('.form-loader').fadeIn();
            }
        }).fail(function() {
            form.find('.alert').removeClass('d-none');
            form.find('.form-loader').fadeIn();
        });
    });

    $('body').on('submit', '#set-percent-payment', function(e) {
        e.preventDefault();
        var actionUrl = my_ajax_obj.ajax_url;
        var form = $(this);
        gsap.to($('.overlay-bg'), .5, {zIndex: '9999', opacity: 1, ease: Power2.easeOut, onComplete: function() {}});

        $.ajax({
            method: "POST",
            url: actionUrl,
            crossDomain: true,
            data: new FormData(document.getElementById('set-percent-payment')),
            dataType: "json",
            contentType: "multipart/form-data",
            processData: false,
            contentType: false,
            headers: {
                "Accept": "application/json"
            }
        }).done(function(response) {
            if(response.error == false){
                gsap.to($('.overlay-bg'), .5, {zIndex: '-1', opacity: 0, ease: Power2.easeOut, onComplete: function() {}});
                if(form.attr('select-status') == 'normal'){
                    $('.select-design-question').hide();
                    $('.select-design-normal-finish').fadeIn();
                }else{
                    $('.select-design-comment').hide();
                    $('.select-design-comment-finish').fadeIn();
                }
            }else{
                form.find('.alert').html(response.message).removeClass('d-none');
                gsap.to($('.overlay-bg'), .5, {zIndex: '-1', opacity: 0, ease: Power2.easeOut, onComplete: function() {}});
            }
        }).fail(function() {
            form.find('.alert').removeClass('d-none');
            gsap.to($('.overlay-bg'), .5, {zIndex: '-1', opacity: 0, ease: Power2.easeOut, onComplete: function() {}});
        });
    });

    $('.btn-view-store').hover(function() {
        $(this).html($(this).attr('hover-text'));
    }, function() {
        $(this).html($(this).attr('text'));
    });

    $('body').on('keydown', '#field-unselect-design-text', function(event){
        var wordCount = $(event.target).val().length;
        var counter = $(this).parent('.field').find('.word-counter').find('span');
        var max = Number($(this).parent('.field').find('.word-counter').attr('max'));
        
        counter.html(max - wordCount);

        if ((event.keyCode == 65 && event.ctrlKey === true) || event.keyCode == 190 || event.keyCode == 8 ||
			// Allow: home, end, left, right
		   (event.keyCode >= 35 && event.keyCode <= 39)) {
				// let it happen, don't do anything
			    return;
	   }else{
           if(wordCount >= max){
               return false;
           }
       }
    })

    $('body').on('keydown', '#field-select-design-text', function(event){
        var wordCount = $(event.target).val().length;
        var counter = $(this).parent('.field').find('.word-counter').find('span');
        var max = Number($(this).parent('.field').find('.word-counter').attr('max'));
        
        counter.html(max - wordCount);

        if ((event.keyCode == 65 && event.ctrlKey === true) || event.keyCode == 190 || event.keyCode == 8 ||
			// Allow: home, end, left, right
		   (event.keyCode >= 35 && event.keyCode <= 39)) {
				// let it happen, don't do anything
			    return;
	   }else{
           if(wordCount >= max){
               return false;
           }
       }
    })

});
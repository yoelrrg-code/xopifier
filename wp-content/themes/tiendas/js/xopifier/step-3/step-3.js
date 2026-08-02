jQuery(document).ready(function ($) {

    $('.main-tab').each(function(){
        var maintab = $(this);
        maintab.click(function(){
            if(!maintab.hasClass('done') || !maintab.hasClass('working')){
                update_tab_status(maintab.attr('id'), 'main', 'visited');
                maintab.addClass('visited');
            }

            maintab.parents('.designs').find('.tab-pane[tab="'+maintab.attr('id')+'"]').find('.nav-link.sub-item').each(function(){
                var subtab = $(this);
                
                if(subtab.hasClass('active') && !subtab.hasClass('working') && !subtab.hasClass('done')){
                    subtab.addClass('visited');
                    update_tab_status(maintab.attr('id'), subtab.attr('id'), 'visited');
                }
            });
        });
    });

    $('.sub-tab').each(function(){
        var subtab = $(this);
        subtab.click(function(){
            if(!subtab.hasClass('done') && !subtab.hasClass('working')){
                update_tab_status(subtab.parents('.tab-pane').attr('tab'), subtab.attr('id'), 'visited');
                subtab.addClass('visited');
            }
        });
    });

    $('.btn-continue').click(function(){
        navigateNextTab($(this).parents('form'));
    });

    $('#store-about-info-form').find('.form-control').each(function () {
        var field = $(this);
        field.blur(function(e){
            if($(e.target).val() == '') {
                $(e.target).removeClass('valid');
            }
            if($(e.target).hasClass('valid')){
            }
        });
    });

    $('.btn-store-info-finish').click(function(e){
        e.preventDefault();
        $('.welcome-step-3').hide();
        $('.tabs-step-3').hide();
        $('.finish-step-3').fadeIn();
        var storeId = $('.finish-step-3').attr('store-id');
        $.post(my_ajax_obj.ajax_url, {action: 'ws', wsa: 'finish-step-3', store_id: storeId}, function(response){
            popup_message(response.msg, 'info', 2000);
        });
    });

    $('button[href="#continue-step-3"]').click(function(e){
        e.preventDefault();
        $('.welcome-step-3').hide();
        $('.tabs-step-3').fadeIn();
    })

    $("a[href='#gotocategories']").click(function(e){
        e.preventDefault();
        $('.welcome-step-3').fadeOut();
        $('#products-tab').trigger('click');
    });

    $("button[href='#gotoproducts']").click(function(e){
        e.preventDefault();
        console.log('products');
        $('.welcome-step-3').fadeOut();
        var categoriesForm = $('#store-products-categories-form');
        navigateNextTab(categoriesForm);
        $('#products-tab').trigger('click');
    });

    $('.close-modal-box').click(function(){
        if($(this).parents('.finish-step3-form-modal').length > 0){
            gsap.to(window, {duration: .3, scrollTo:{ y: $('.finish-step3'), offsetY: 100}, ease: Power2.easeOut});
            $('.popover').hide();
            gsap.fromTo($('.btn-store-info-finish'), .6, {scale: '1.5', ease: Power2.easeOut, onComplete: function() {}}, {scale: '1', ease: Power2.easeOut, onComplete: function() {}});
        }
        $(this).parents('.extra-form-modal').fadeOut();
    });

    $('.finish-step3-arrow-down').click(function(e){
        e.preventDefault();
        $(this).parents('.extra-form-modal').fadeOut();
        gsap.to(window, {duration: .3, scrollTo:{ y: $('.finish-step3'), offsetY: 100}, ease: Power2.easeOut});
        $('.popover').hide();
        gsap.fromTo($('.btn-store-info-finish'), .6, {scale: '1.5', ease: Power2.easeOut, onComplete: function() {}}, {scale: '1', ease: Power2.easeOut, onComplete: function() {}});
    });
});

const update_tab_status = (mainTab, subTab, status = '') => {
    const $step3 = jQuery("#steps.step-3");
    var designId = $step3.attr('design-id');
    var storeId = $step3.attr('store-id');

    if(!status){
        status = 'visited';
    }

    jQuery.post(my_ajax_obj.ajax_url, {
        action: 'ws', 
        wsa: 'update-tab-status', 
        designid: designId, 
        storeid: storeId, 
        maintab: mainTab, 
        subtab: subTab, 
        status: status,
        nonce: typeof my_ajax_obj !== 'undefined' ? my_ajax_obj.nonce : ''
    });
}

const update_prices = (total_price) => {
    jQuery('input[name="total_price"]').val(total_price);
}

const check_tab_info_status = () => {
    if(jQuery('#myTabInfo').find('#info-store-tab').hasClass('done') 
        && jQuery('#myTabInfo').find('#info-contact-tab').hasClass('done')
        && jQuery('#myTabInfo').find('#info-policy-tab').hasClass('done')
        && jQuery('#myTabInfo').find('#info-service-reviews-tab').hasClass('done') 
        && jQuery('#myTabInfo').find('#info-service-faqs-tab').hasClass('done')
        && jQuery('#myTabInfo').find('#info-service-custom-tab').hasClass('done')){
            jQuery('#myTabStep3').find('#info-tab').removeClass('working').addClass('done');
            update_tab_status('info-tab', 'main', 'done');
    }else{
        jQuery('#myTabStep3').find('#info-tab').addClass('working').removeClass('done');
        update_tab_status('info-tab', 'main', 'working');
    }

    check_step3_status();
}

const check_tab_products_status = () => {
    if(jQuery('#myTabProducts').find('#products-extra-info-tab').hasClass('done') 
        && jQuery('#myTabProducts').find('#products-products-tab').hasClass('done')
        && jQuery('#myTabProducts').find('#products-categories-tab').hasClass('done')){
            jQuery('#myTabStep3').find('#products-tab').removeClass('working').addClass('done');  
            update_tab_status('products-tab', 'main', 'done');     
    }else{
        jQuery('#myTabStep3').find('#products-tab').addClass('working').removeClass('done');
        update_tab_status('products-tab', 'main', 'working');
    }

    check_step3_status();
}

const check_tab_promos_status = () => {
    if(jQuery('#myTabPromos').find('#promos-discount-tab').hasClass('done') && jQuery('#myTabPromos').find('#promos-ads-tab').hasClass('done')){
        jQuery('#myTabStep3').find('#promos-tab').removeClass('working').addClass('done');
        update_tab_status('promos-tab', 'main', 'done');
    }else{
        jQuery('#myTabStep3').find('#promos-tab').addClass('working').removeClass('done');
        update_tab_status('promos-tab', 'main', 'working');
    }

    check_step3_status();
}

const check_step3_status = () => {
    if(jQuery('#myTabStep3').find('#info-tab').hasClass('done') 
        && jQuery('#myTabStep3').find('#products-tab').hasClass('done') 
        && jQuery('#myTabStep3').find('#promos-tab').hasClass('done')
        && jQuery('#myTabStep3').find('#other-tab').hasClass('done')){
            jQuery('.progress-dots .store-info').removeClass('current').addClass('done');
            jQuery('.finish-step3 .btn').removeClass('disabled').css('z-index', '99999999');
            jQuery('.finish-step3-form-modal').fadeIn();
    }else{
        jQuery('.progress-dots .store-info').addClass('current').removeClass('done');
    }
}

check_step3_status();

const navigateNextTab = (storeForm) => {

    storeForm.find('.msg').removeClass('error').removeClass('success').html('').fadeOut();

    var navTab = storeForm.parent().parent().prev('.nav-tabs');
    var nextTabId = storeForm.parent().next().attr('id');
    var nexttab = document.querySelector('#'+navTab.attr('id')+' button[data-bs-target="#'+nextTabId+'"]');
    
    if(nexttab != null){
        bootstrap.Tab.getOrCreateInstance(nexttab).show();
    }else{
        navTab = navTab.parents('.tab-content').prev('.nav-tabs');
        paneId = storeForm.parent().parents('.tab-pane').attr('id');
        nextTabId = navTab.find('button[data-bs-target="#'+paneId+'"').parent().next().find('button').attr('id');
        nexttab = document.querySelector('#'+navTab.attr('id')+' button[id="'+nextTabId+'"]');

        if(nexttab != null){
            bootstrap.Tab.getOrCreateInstance(nexttab).show();
        }
    }
};

(function($){
    $(window).on("beforeunload", function() { 
        return false;
    });
})(jQuery);
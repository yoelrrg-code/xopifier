jQuery(document).ready(function ($) {

    const batchImportExcel = new UploadController('.field-upload-batch-files', '', null);

    // $('body').on('click', '#field-batch-import-type-file', function(e){
    //     $('.excel-form').fadeIn();
    //     $('.sheet-form').hide();
    // });

    // $('body').on('click', '#field-batch-import-type-gsheet', function(e){
    //     $('.excel-form').hide();
    //     $('.sheet-form').fadeIn();
    // });

    $('body').on('click', '.btn-batch-close-modal', function(e){
        $('.batch-import-form-modal').fadeOut(200, function(){
            // $('.excel-form').hide();
            // $('.sheet-form').hide();
            batchImportExcel.clearFiles();
            $('#field-batch-import-type-file').attr('checked', false).removeAttr('checked');
            $('#field-batch-import-type-gsheet').attr('checked', false).removeAttr('checked');
        });
    });

    $('body').on('click', '.btn-batch-import-products', function(e){
        $('.batch-import-form-modal').fadeIn();
    });

    $('body').on('click', '.product-trash.step3:not(.disabled)', function(e){
        e.preventDefault();
        var list_container = $(this).parents('.products-list-container');
        var index = $(this).parents('li').attr('index');
        $('.'+index).remove();

        var total_products_qty = 0;

        if(list_container.find('li').length < 10) {
            var new_empty_li = '<li class="product-placeholder d-flex align-items-center justify-content-start py-3">'+
                '<span class="counter disabled me-3">0</span>'+
                '<div class="d-flex align-items-center justify-content-start gap-3 w-75">'+
                    '<img src="'+my_ajax_obj.base_url+'/wp-content/themes/tiendas/img/image-placeholder.png" class="product-thumb me-0 img-fluid" />'+
                    '<div class="d-flex flex-column align-items-start justify-content-center w-100">'+
                        '<div class="gray-bar w-75"></div>'+
                        '<div class="gray-bar w-50 dark-gray"></div>'+ 
                        '<div class="gray-bar w-25"></div>'+
                    '</div>'+
                '</div>'+
                '<a class="featured me-3 ms-auto disabled" disabled href="javascript:void(0)"><i class="star off disabled"></i></a>'+
                '<a class="trash me-0 ms-0 disabled" disabled href="javascript:void(0)"><img src="'+my_ajax_obj.base_url+'/wp-content/themes/tiendas/img/trash.svg" class="product-trash step3 img-fluid me-3 disabled" disabled /></a>'+
            '</li>';

            $(new_empty_li).appendTo(list_container);
        }

        list_container.find('li').each(function(index){
            $(this).find('.counter').text(index + 1);
            if(!$(this).hasClass('product-placeholder')){
                total_products_qty++;
            }
        });

        if(total_products_qty > 10){
            $('.base-cost-included').hide();
        }else{
            $('.base-cost-included').show();
        }

        $('.total-products-qty').html(total_products_qty);

        // $('.popover').remove();
        // $('#howto-popover').attr('data-bs-content', my_ajax_obj.product_extra_9);
        // message('howto-popover');

        popup_message(my_ajax_obj.product_extra_9, 'info', 2000);

        if($('#store-products-form').find('.products-list-container').children('.product-placeholder').length == 10){
            $('#store-products-form').find('.btn-save-products').addClass('disabled');
        }
    });

    var confirm_aditional_product = false;

    $('body').on('click', '.btn-close-aditional-products', function(){
        $('.aditional-product-popup').fadeOut(200, function(){
            $(this).remove();
        });
    });

    $('body').on('click', '.btn-confirm-aditional-product', function(){
        confirm_aditional_product = true;
        $('.aditional-product-popup').fadeOut(200, function(){
            $(this).remove();
        });
        $('.step-3 .btn-plus-product').trigger('click');
    });

    $('body').on('click', '.step-3 .btn-plus-product', function(){
        
        pcProductsIndex = $('.step-3 .products-tab-list .products-list-container .pc-product').length;
        var pcprods_qty = pcProductsIndex;
        while($('.item-pc-'+pcProductsIndex).length > 0){
            pcProductsIndex++;
        }

        clearLinkFormFields();
        clearFormFields();

        uploaderProductsFiles.updateIndex(pcProductsIndex);
        
        linkProductsIndex = $('.step-3 .products-tab-list .products-list-container .link-product').length;
        var linkprods_qty = linkProductsIndex;
        while($('.item-pc-'+linkProductsIndex).length > 0){
            linkProductsIndex++;
        }
        
        if((linkprods_qty + pcprods_qty) >= 10 && !confirm_aditional_product){
            $('<div class="aditional-product-popup" style="display: none;">'+
                '<div class="aditional-product-popup-box">'+
                    '<img src="'+my_ajax_obj.base_url+'/wp-content/themes/tiendas/img/close-dark.svg" class="btn-close-aditional-products" alt="close preview" />'+
                    '<div class="d-flex gap-3 align-items-center justify-content-between">'+
                        '<h3><small>'+my_ajax_obj.product_extra_1+'</small></h3>'+
                        '<img src="'+my_ajax_obj.base_url+'/wp-content/themes/tiendas/img/mona-aditional-products.svg" class="mona-aditional-products img-fluid" />'+
                    '</div>'+
                    '<div class="bordered-box">'+
                        '<p>'+my_ajax_obj.product_extra_2+'</p>'+
                        '<p class="mb-0">'+my_ajax_obj.product_extra_3+'</p>'+
                    '</div>'+
                    '<div class="d-flex align-items-center justify-content-center">'+
                        '<button type="button" class="btn btn-primary btn-confirm-aditional-product">'+my_ajax_obj.product_extra_4+'</button>'+
                    '</div>'+
                '</div>'+
            '</div>').appendTo('body');

            $('.aditional-product-popup').fadeIn(600);
        }

        if(((linkprods_qty + pcprods_qty) >= 10 && confirm_aditional_product) || ((linkprods_qty + pcprods_qty) < 10)){
            $('.step-3 .btn-add-pc-product').addClass('disabled');
            $('.step-3 .btn-add-link-product').addClass('disabled');

            const linktab = document.querySelector('.step-3 #myTabAddEditProduct button[data-bs-target="#fromlink"]')
            bootstrap.Tab.getInstance(linktab).show();

            var storeID = $('.products-tab-new-product').attr('store-id');
            $.post(my_ajax_obj.ajax_url, {action: 'ws', wsa: 'get-store-categories', storeID: storeID}, function(response){
                $('#field-fromLinkProductCategory').html(response.categories);
                $('#field-fromPCProductCategory').html(response.categories);
            });

            $(".step-3 #myTabContentAddEditProduct #fromlink #field-fromLinkProductName").val('');
            $(".step-3 #myTabContentAddEditProduct #fromlink #field-fromLinkProductLink").val('');

            $(".step-3 #myTabContentAddEditProduct #frompc #field-fromPCProductMedia").val('');
            $(".step-3 #myTabContentAddEditProduct #frompc #field-fromPCProductName").val('');
            $(".step-3 #myTabContentAddEditProduct #frompc #field-fromPCProductPrice").val('');
            $(".step-3 #myTabContentAddEditProduct #frompc #field-fromPCProductSalePrice").val('');
            $(".step-3 #myTabContentAddEditProduct #frompc #field-fromPCProductDescription").val('');

            $('.step-3 #myTabContentAddEditProduct #frompc .field-upload-products').find('.field-upload-content').hide();
            $('.step-3 #myTabContentAddEditProduct #frompc .field-upload-products').find('.field-upload-content').find('.image-preview-close').hide();
            $('.step-3 #myTabContentAddEditProduct #frompc .field-upload-products').find('.field-upload-field').fadeIn();
            $('.step-3 #myTabContentAddEditProduct #frompc .field-upload-products').find('.field-upload-content').find('.img-preview-container').remove();
            $('.step-3 #myTabContentAddEditProduct #frompc .field-upload-products').find('.field-upload-input')[0].value = null;

            $('.step-3 #myTabContentAddEditProduct #frompc .field-preview-media').html('').hide();
            $('.step-3 #myTabContentAddEditProduct #frompc .field-upload-products').fadeIn();
            $('.step-3 #myTabContentAddEditProduct #frompc .field-upload-new-media').addClass('d-none').removeClass('d-block');
            editProductMedia = false;

            $('.step-3 #myTabContentAddEditProduct #fromlink .btn-cancel-link-product').removeClass('d-none');
            $('.step-3 #myTabContentAddEditProduct #fromlink .btn-save-link-product').addClass('d-none');
            $('.step-3 #myTabContentAddEditProduct #fromlink .btn-add-link-product').removeClass('d-none');

            $('.step-3 #myTabContentAddEditProduct #frompc .btn-cancel-pc-product').removeClass('d-none');
            $('.step-3 #myTabContentAddEditProduct #frompc .btn-save-pc-product').addClass('d-none');
            $('.step-3 #myTabContentAddEditProduct #frompc .btn-add-pc-product').removeClass('d-none');

            $('.step-3 .products-tab-list').hide();
            $('.step-3 .products-tab-new-product').fadeIn();
            gsap.to(window, {duration: .2, scrollTo:{ y: 0, offsetY: 0}, ease: "power2.outIn"});
            confirm_aditional_product = false;
        }
    });

    $('body').on('click', '.step-3 .btn-cancel-link-product', function(e) {
        $(".step-3 #myTabContentAddEditProduct #fromlink #field-fromLinkProductName").val('');
        $(".step-3 #myTabContentAddEditProduct #fromlink #field-fromLinkProductLink").val('');
        
        $('.step-3 .products-tab-list').fadeIn();
        $('.step-3 .products-tab-new-product').hide();
        gsap.to(window, {duration: .2, scrollTo:{ y: 0, offsetY: 0}, ease: "power2.outIn"});
    });

    $('body').on('click', '.step-3 .btn-cancel-pc-product', function(e) {
        $('.step-3 #myTabContentAddEditProduct #frompc .field-upload-products').find('.field-upload-content').hide();
        $('.step-3 #myTabContentAddEditProduct #frompc .field-upload-products').find('.field-upload-content').find('.image-preview-close').hide();
        $('.step-3 #myTabContentAddEditProduct #frompc .field-upload-products').find('.field-upload-field').fadeIn();
        $('.step-3 #myTabContentAddEditProduct #frompc .field-upload-products').find('.field-upload-content').find('.img-preview-container').remove();
        $('.step-3 #myTabContentAddEditProduct #frompc .field-upload-products').find('.field-upload-input')[0].value = null;

        $('.step-3 #myTabContentAddEditProduct #frompc .field-preview-media').html('').hide();
        $('.step-3 #myTabContentAddEditProduct #frompc .field-upload-products').fadeIn();
        $('.step-3 #myTabContentAddEditProduct #frompc .field-upload-new-media').addClass('d-none').removeClass('d-block');

        editProductMedia = false;

        $('.step-3 .products-tab-list').fadeIn();
        $('.step-3 .products-tab-new-product').hide();
        gsap.to(window, {duration: .2, scrollTo:{ y: 0, offsetY: 0}, ease: "power2.outIn"});
    });

    $('.step-3 #field-fromLinkProductName').keyup(function(){
        if($(this).val() != '' && isValidUrl($('.step-3 #field-fromLinkProductLink').val())){
            $('.step-3 .btn-add-link-product').removeClass('disabled');
        }
    });

    /**
	evento keyup de validacion para el campo enlace de los productos por referencia
	*/
	$('.step-3 #field-fromLinkProductLink').keyup(function(){
		if(isValidUrl($(this).val())){
			$(this).next('.error').html('');
		}else{
			$(this).next('.error').html(my_ajax_obj.valid_url);
		}

		if($('.step-3 #field-fromLinkProductName').val() != '' && isValidUrl($(this).val())){
			$('.step-3 .btn-add-link-product').removeClass('disabled');
		}
	});

    //============================================================================================================================================================
	//file uploads product animations
	//============================================================================================================================================================
	
    var storeForm = $('#store-products-extra');
    const uploaderExtraAnimationsFiles = new UploadController('.field-upload-animations', 'product-extra-files', storeForm.find('input[name="store_id"]').val());

	//====================================================================================================================================================================================
	//====================================================================================================================================================================================
	//====================================================================================================================================================================================
    

    /**
    ================================================================================================================================
    ================================================================================================================================
    UPLOAD images
    ================================================================================================================================
    ================================================================================================================================
    */

    var linkProductsIndex = 0;
    var pcProductsIndex = 0;

    var _products_uploaded_files = [];
    var ProductsList = [];

    pcProductsIndex = $('.step-3 .products-tab-list .products-list-container .pc-product').length;
    while($('.item-pc-'+pcProductsIndex).length > 0){
        pcProductsIndex++;
    }

    const uploaderProductsFiles = new UploadController('#frompc .field-upload-products', ['item-pc-', pcProductsIndex], null);

    var pcnameok = false; 
    var pcpriceok = false;
    var pcdescok = false;
    var pccategoryok = false;
    var allVariationsChecked = false;

    //====================================================================================================================================================================================
    //====================================================================================================================================================================================
    //====================================================================================================================================================================================
    
    $('.products-list-container').on('click', '.star', function(e){
        e.preventDefault();

        if($(this).hasClass('disabled')){
            return;
        }

        var product = $(this).parents('li');
        var index = product.attr('index');
        var classname = '';

        var popover = message('howto-popover');

        var star_qty = $('.products-list-container').find('.star:not(.off)').length;

        if(product.hasClass('pc-product')){
            classname = 'PCProductFeatured';
        }else{
            classname = 'LinkProductFeatured';
        }

        if($(this).hasClass('off')){

            if(star_qty == 3){
                // $('.popover').remove();
                // $('#howto-popover').attr('data-bs-content', my_ajax_obj.product_extra_5);
                // popover = message('howto-popover');

                popup_message(my_ajax_obj.product_extra_5, 'warning', 2000);
    
                return;
            }

            $(this).removeClass('off');
            $('.'+classname+'.'+index).val(1).prop('value', 1).attr('value', 1);

            // $('.popover').remove();
            // $('#howto-popover').attr('data-bs-content', my_ajax_obj.product_extra_6);
            // popover = message('howto-popover');

            popup_message(my_ajax_obj.product_extra_6, 'info', 2000);

        }else{
            $(this).addClass('off');
            $('.'+classname+'.'+index).val(0).prop('value', 0).attr('value', 0);

            // $('.popover').remove();
            // $('#howto-popover').attr('data-bs-content', my_ajax_obj.product_extra_7);
            // popover = message('howto-popover');

            popup_message(my_ajax_obj.product_extra_7, 'info', 2000);
        }
    });

    /**
	evento click para el form de los productos por referencia, donde se agrega el producto al listado de productos
	*/
	$('body').on('click', '.step-3 .btn-add-link-product', function(){
		var productName = $('.step-3 #field-fromLinkProductName').val();
		var productLink = $('.step-3 #field-fromLinkProductLink').val();
        var productCategory = $('.step-3 #field-fromLinkProductCategory').val();

        linkProductsIndex = $('.step-3 .products-tab-list .products-list-container .link-product').length;
        while($('.item-link-'+linkProductsIndex).length > 0){
            linkProductsIndex++;
        }

        if($('.step-3 .products-tab-list .products-list-container li.product-placeholder').length > 0){
            $('.step-3 .products-tab-list .products-list-container li.product-placeholder').each(function(index){
                var product_placeholder = $(this);
                var counter = product_placeholder.find('.counter').text();
                
                if(index == 0){
                    $('<input type="text" class="LinkProductName item-link-'+linkProductsIndex+'" name="field-LinkProductName[]" value="'+productName+'" />').appendTo($('.link-products'));
                    $('<input type="text" class="LinkProductLink item-link-'+linkProductsIndex+'" name="field-LinkProductLink[]" value="'+productLink+'" />').appendTo($('.link-products'));
                    $('<input type="text" class="LinkProductCategory item-link-'+linkProductsIndex+'" name="field-LinkProductCategory[]" value="'+productCategory+'" />').appendTo($('.link-products'));
                    $('<input type="text" class="LinkProductFeatured item-link-'+linkProductsIndex+'" name="field-LinkProductFeatured[]" value="0" />').appendTo($('.link-products'));

                    $('<li class="link-product d-flex align-items-center justify-content-between py-2 item-link-'+linkProductsIndex+'" index="item-link-'+linkProductsIndex+'">'+
                        '<span class="counter me-3">'+counter+'</span>'+
                        '<div class="d-flex align-items-center justify-content-start gap-3 w-75">'+
                            '<img src="'+my_ajax_obj.base_url+'/wp-content/themes/tiendas/img/image-placeholder.png" class="product-thumb me-0 img-fluid" />'+
                            '<div class="d-flex flex-column align-items-start justify-content-center">'+
                                '<h3 class="d-flex"><a class="link-product-edit" href="javascript:void(0)">'+productName+'</a></h3>'+
                                '<a href="'+productLink+'" class="direct-link" target="_blank">'+productLink+'</a>'+
                                (productCategory != '*' ? '<p class="product-categories mb-0">'+productCategory+'</p>' : '')+
                            '</div>'+
                        '</div>'+
                        '<a class="featured me-3 ms-auto" href="javascript:void(0)"><i class="star off"></i></a>'+
                        '<a class="trash me-0 ms-0" href="javascript:void(0)">'+
                            '<span class="product-trash step3 img-fluid me-3"><svg width="19px" height="21px" viewBox="0 0 19 21" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g id="X---Cuestionario-1---Cambios" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><g id="X-Fase-1_Flujo-1y2_Paso-6-List" transform="translate(-769, -385)" fill="#A6A6A7" fill-rule="nonzero"><g id="trash" transform="translate(769, 385.0156)"><path d="M6.90909091,16.25 L6.90909091,7.08333333 C6.90909091,6.96180556 6.86860795,6.86197917 6.78764205,6.78385417 C6.70667614,6.70572917 6.6032197,6.66666667 6.47727273,6.66666667 L5.61363636,6.66666667 C5.48768939,6.66666667 5.38423295,6.70572917 5.30326705,6.78385417 C5.22230114,6.86197917 5.18181818,6.96180556 5.18181818,7.08333333 L5.18181818,16.25 C5.18181818,16.3715278 5.22230114,16.4713542 5.30326705,16.5494792 C5.38423295,16.6276042 5.48768939,16.6666667 5.61363636,16.6666667 L6.47727273,16.6666667 C6.6032197,16.6666667 6.70667614,16.6276042 6.78764205,16.5494792 C6.86860795,16.4713542 6.90909091,16.3715278 6.90909091,16.25 Z M10.3636364,16.25 L10.3636364,7.08333333 C10.3636364,6.96180556 10.3231534,6.86197917 10.2421875,6.78385417 C10.1612216,6.70572917 10.0577652,6.66666667 9.93181818,6.66666667 L9.06818182,6.66666667 C8.94223485,6.66666667 8.83877841,6.70572917 8.7578125,6.78385417 C8.67684659,6.86197917 8.63636364,6.96180556 8.63636364,7.08333333 L8.63636364,16.25 C8.63636364,16.3715278 8.67684659,16.4713542 8.7578125,16.5494792 C8.83877841,16.6276042 8.94223485,16.6666667 9.06818182,16.6666667 L9.93181818,16.6666667 C10.0577652,16.6666667 10.1612216,16.6276042 10.2421875,16.5494792 C10.3231534,16.4713542 10.3636364,16.3715278 10.3636364,16.25 Z M13.8181818,16.25 L13.8181818,7.08333333 C13.8181818,6.96180556 13.7776989,6.86197917 13.696733,6.78385417 C13.615767,6.70572917 13.5123106,6.66666667 13.3863636,6.66666667 L12.5227273,6.66666667 C12.3967803,6.66666667 12.2933239,6.70572917 12.212358,6.78385417 C12.131392,6.86197917 12.0909091,6.96180556 12.0909091,7.08333333 L12.0909091,16.25 C12.0909091,16.3715278 12.131392,16.4713542 12.212358,16.5494792 C12.2933239,16.6276042 12.3967803,16.6666667 12.5227273,16.6666667 L13.3863636,16.6666667 C13.5123106,16.6666667 13.615767,16.6276042 13.696733,16.5494792 C13.7776989,16.4713542 13.8181818,16.3715278 13.8181818,16.25 Z M6.47727273,3.33333333 L12.5227273,3.33333333 L11.875,1.80989583 C11.8120265,1.73177083 11.7355587,1.68402778 11.6455966,1.66666667 L7.36789773,1.66666667 C7.27793561,1.68402778 7.2014678,1.73177083 7.13849432,1.80989583 L6.47727273,3.33333333 Z M19,3.75 L19,4.58333333 C19,4.70486111 18.959517,4.8046875 18.8785511,4.8828125 C18.7975852,4.9609375 18.6941288,5 18.5681818,5 L17.2727273,5 L17.2727273,17.34375 C17.2727273,18.0642361 17.0613163,18.687066 16.6384943,19.2122396 C16.2156723,19.7374132 15.7073864,20 15.1136364,20 L3.88636364,20 C3.29261364,20 2.78432765,19.7460938 2.36150568,19.2382812 C1.93868371,18.7304688 1.72727273,18.1163194 1.72727273,17.3958333 L1.72727273,5 L0.431818182,5 C0.305871212,5 0.202414773,4.9609375 0.121448864,4.8828125 C0.0404829545,4.8046875 0,4.70486111 0,4.58333333 L0,3.75 C0,3.62847222 0.0404829545,3.52864583 0.121448864,3.45052083 C0.202414773,3.37239583 0.305871212,3.33333333 0.431818182,3.33333333 L4.6015625,3.33333333 L5.54616477,1.15885417 C5.68110795,0.837673611 5.92400568,0.564236111 6.27485795,0.338541667 C6.62571023,0.112847222 6.98106061,0 7.34090909,0 L11.6590909,0 C12.0189394,0 12.3742898,0.112847222 12.725142,0.338541667 C13.0759943,0.564236111 13.318892,0.837673611 13.4538352,1.15885417 L14.3984375,3.33333333 L18.5681818,3.33333333 C18.6941288,3.33333333 18.7975852,3.37239583 18.8785511,3.45052083 C18.959517,3.52864583 19,3.62847222 19,3.75 Z" id="Shape"></path></g></g></g></svg></span>'+
                        '</a>'+
                    '</li>').insertAfter(product_placeholder);
                    product_placeholder.remove();

                    $(".step-3 #myTabContentAddEditProduct #fromlink #field-fromLinkProductName").val('');
                    $(".step-3 #myTabContentAddEditProduct #fromlink #field-fromLinkProductLink").val('');
                    
                    $('.step-3 .products-tab-list').fadeIn();
                    $('.step-3 .products-tab-new-product').hide();
                    gsap.to(window, {duration: .2, scrollTo:{ y: 0, offsetY: 0}, ease: "power2.outIn"});

                    linkProductsIndex++;
                }
            });
        }else{
            var counter = $('.step-3 .products-tab-list .products-list-container li').length + 1;
            var list_container = $('.step-3 .products-tab-list .products-list-container');

            $('<input type="text" class="LinkProductName item-link-'+linkProductsIndex+'" name="field-LinkProductName[]" value="'+productName+'" />').appendTo($('.link-products'));
            $('<input type="text" class="LinkProductLink item-link-'+linkProductsIndex+'" name="field-LinkProductLink[]" value="'+productLink+'" />').appendTo($('.link-products'));
            $('<input type="text" class="LinkProductCategory item-link-'+linkProductsIndex+'" name="field-LinkProductCategory[]" value="'+productCategory+'" />').appendTo($('.link-products'));

            $('<li class="link-product d-flex align-items-center justify-content-between py-2 item-link-'+linkProductsIndex+'" index="item-link-'+linkProductsIndex+'">'+
                '<span class="counter me-3">'+counter+'</span>'+
                '<div class="d-flex align-items-center justify-content-start gap-3 w-75">'+
                    '<img src="'+my_ajax_obj.base_url+'/wp-content/themes/tiendas/img/image-placeholder.png" class="product-thumb me-0 img-fluid" />'+
                    '<div class="d-flex flex-column align-items-start justify-content-center">'+
                        '<h3 class="d-flex"><a class="link-product-edit" href="javascript:void(0)">'+productName+'</a></h3>'+
                        '<a href="'+productLink+'" class="direct-link" target="_blank">'+productLink+'</a>'+
                        (productCategory != '*' ? '<p class="product-categories mb-0">'+productCategory+'</p>' : '')+
                    '</div>'+
                '</div>'+
                '<a class="featured me-3 ms-auto" href="javascript:void(0)"><i class="star off"></i></a>'+
                '<a class="trash me-0 ms-0" href="javascript:void(0)">'+
                    '<span class="product-trash step3 img-fluid me-3"><svg width="19px" height="21px" viewBox="0 0 19 21" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g id="X---Cuestionario-1---Cambios" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><g id="X-Fase-1_Flujo-1y2_Paso-6-List" transform="translate(-769, -385)" fill="#A6A6A7" fill-rule="nonzero"><g id="trash" transform="translate(769, 385.0156)"><path d="M6.90909091,16.25 L6.90909091,7.08333333 C6.90909091,6.96180556 6.86860795,6.86197917 6.78764205,6.78385417 C6.70667614,6.70572917 6.6032197,6.66666667 6.47727273,6.66666667 L5.61363636,6.66666667 C5.48768939,6.66666667 5.38423295,6.70572917 5.30326705,6.78385417 C5.22230114,6.86197917 5.18181818,6.96180556 5.18181818,7.08333333 L5.18181818,16.25 C5.18181818,16.3715278 5.22230114,16.4713542 5.30326705,16.5494792 C5.38423295,16.6276042 5.48768939,16.6666667 5.61363636,16.6666667 L6.47727273,16.6666667 C6.6032197,16.6666667 6.70667614,16.6276042 6.78764205,16.5494792 C6.86860795,16.4713542 6.90909091,16.3715278 6.90909091,16.25 Z M10.3636364,16.25 L10.3636364,7.08333333 C10.3636364,6.96180556 10.3231534,6.86197917 10.2421875,6.78385417 C10.1612216,6.70572917 10.0577652,6.66666667 9.93181818,6.66666667 L9.06818182,6.66666667 C8.94223485,6.66666667 8.83877841,6.70572917 8.7578125,6.78385417 C8.67684659,6.86197917 8.63636364,6.96180556 8.63636364,7.08333333 L8.63636364,16.25 C8.63636364,16.3715278 8.67684659,16.4713542 8.7578125,16.5494792 C8.83877841,16.6276042 8.94223485,16.6666667 9.06818182,16.6666667 L9.93181818,16.6666667 C10.0577652,16.6666667 10.1612216,16.6276042 10.2421875,16.5494792 C10.3231534,16.4713542 10.3636364,16.3715278 10.3636364,16.25 Z M13.8181818,16.25 L13.8181818,7.08333333 C13.8181818,6.96180556 13.7776989,6.86197917 13.696733,6.78385417 C13.615767,6.70572917 13.5123106,6.66666667 13.3863636,6.66666667 L12.5227273,6.66666667 C12.3967803,6.66666667 12.2933239,6.70572917 12.212358,6.78385417 C12.131392,6.86197917 12.0909091,6.96180556 12.0909091,7.08333333 L12.0909091,16.25 C12.0909091,16.3715278 12.131392,16.4713542 12.212358,16.5494792 C12.2933239,16.6276042 12.3967803,16.6666667 12.5227273,16.6666667 L13.3863636,16.6666667 C13.5123106,16.6666667 13.615767,16.6276042 13.696733,16.5494792 C13.7776989,16.4713542 13.8181818,16.3715278 13.8181818,16.25 Z M6.47727273,3.33333333 L12.5227273,3.33333333 L11.875,1.80989583 C11.8120265,1.73177083 11.7355587,1.68402778 11.6455966,1.66666667 L7.36789773,1.66666667 C7.27793561,1.68402778 7.2014678,1.73177083 7.13849432,1.80989583 L6.47727273,3.33333333 Z M19,3.75 L19,4.58333333 C19,4.70486111 18.959517,4.8046875 18.8785511,4.8828125 C18.7975852,4.9609375 18.6941288,5 18.5681818,5 L17.2727273,5 L17.2727273,17.34375 C17.2727273,18.0642361 17.0613163,18.687066 16.6384943,19.2122396 C16.2156723,19.7374132 15.7073864,20 15.1136364,20 L3.88636364,20 C3.29261364,20 2.78432765,19.7460938 2.36150568,19.2382812 C1.93868371,18.7304688 1.72727273,18.1163194 1.72727273,17.3958333 L1.72727273,5 L0.431818182,5 C0.305871212,5 0.202414773,4.9609375 0.121448864,4.8828125 C0.0404829545,4.8046875 0,4.70486111 0,4.58333333 L0,3.75 C0,3.62847222 0.0404829545,3.52864583 0.121448864,3.45052083 C0.202414773,3.37239583 0.305871212,3.33333333 0.431818182,3.33333333 L4.6015625,3.33333333 L5.54616477,1.15885417 C5.68110795,0.837673611 5.92400568,0.564236111 6.27485795,0.338541667 C6.62571023,0.112847222 6.98106061,0 7.34090909,0 L11.6590909,0 C12.0189394,0 12.3742898,0.112847222 12.725142,0.338541667 C13.0759943,0.564236111 13.318892,0.837673611 13.4538352,1.15885417 L14.3984375,3.33333333 L18.5681818,3.33333333 C18.6941288,3.33333333 18.7975852,3.37239583 18.8785511,3.45052083 C18.959517,3.52864583 19,3.62847222 19,3.75 Z" id="Shape"></path></g></g></g></svg></span>'+
                '</a>'+
            '</li>').appendTo(list_container);

            $(".step-3 #myTabContentAddEditProduct #fromlink #field-fromLinkProductName").val('');
            $(".step-3 #myTabContentAddEditProduct #fromlink #field-fromLinkProductLink").val('');
            $(".step-3 #myTabContentAddEditProduct #fromlink #field-fromLinkProductCategory").val('*');
            
            $('.step-3 .products-tab-list').fadeIn();
            $('.step-3 .products-tab-new-product').hide();
            gsap.to(window, {duration: .2, scrollTo:{ y: 0, offsetY: 0}, ease: "power2.outIn"});

            linkProductsIndex++;
        }

        var total_products_qty = Number($('.total-products-qty').text());
        total_products_qty++;
        if(total_products_qty > 10){
            $('.base-cost-included').hide();
        }else{
            $('.base-cost-included').show();
        }
        $('.total-products-qty').text(total_products_qty);

        $('.popover').remove();
        $('#howto-popover').attr('data-bs-content', my_ajax_obj.product_extra_8);
        message('howto-popover');

        $('#store-products-form').find('.btn-save-products').removeClass('disabled');

        verify_products_qty();
	});

    	/**
	evento click para cerrar el popup de las imagenes del producto
	*/
	$('body').on('click', '.product-images-close', function (e) {
		$('.product-images-modal').fadeOut(200, function(){
            $(this).remove();
        });
        gsap.to($('.overlay-bg'), .3, {zIndex: '-1', opacity: 0, ease: Power2.easeOut, onComplete: function() {}});
	});

	/**
	evento click para mostrar el popup de las imagenes del producto
	*/
	$('body').on('click', '.product-more-images', function(e){
		e.preventDefault();
        var productMore = $(this);
        gsap.to($('.overlay-bg'), .3, {zIndex: '9999999', opacity: 1, ease: Power2.easeOut, onComplete: function() {
            productMore.next().clone().addClass('product-images-modal').appendTo('body').css('z-index', 99999999).fadeIn();
        }});
	});

    /**
	evento keyup de validacion para el campo nombre de los productos subidos desde la pc
	*/
	$('body').on('keyup', '#field-fromPCProductName', function(){
		verify_product_form_data();
	});

     /**
	evento keyup de validacion para el campo categoria de los productos subidos desde la pc
	*/
	$('body').on('change', '#field-fromPCProductCategory', function(){
		verify_product_form_data();
	});

	/**
	evento keyup de validacion para el campo precio de los productos subidos desde la pc
	*/
	$('body').on('keyup', '#field-fromPCProductPrice', function(){
		verify_product_form_data();
	});

    $('body').on('keydown', '#field-fromPCProductPrice', function(event){
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

    /**
	evento keyup de validacion para el campo precio de referencia de los productos subidos desde la pc
	*/
	$('body').on('keyup', '#field-fromPCProductSalePrice', function(){
		if($(this).val() == '.'){
			$(this).val('0.');
		}
	});

	$('body').on('keydown', '#field-fromPCProductSalePrice', function(event){
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

	/**
	evento keyup de validacion para el campo descripcion de los productos subidos desde la pc
	*/
	$('body').on('keyup', '#field-fromPCProductDescription', function(){
		verify_product_form_data();
	});

    const verify_product_form_data = () => {
        var pcname = $('#field-fromPCProductName').val();
        var pcdesc = $('#field-fromPCProductDescription').val();
        var pcprice = $('#field-fromPCProductPrice').val();
        var pccategory = $('#field-fromPCProductCategory').val();

        if(pcname != '' && pcprice != '' && pcdesc != '' && pccategory != ''){
            $('.btn-add-pc-product').removeClass('disabled');
            $('.btn-save-pc-product').removeClass('disabled');
        }else{
            $('.btn-add-pc-product').addClass('disabled');
            $('.btn-save-pc-product').addClass('disabled');
        }
    }
    
    $('body').on('click', '.step-3 .btn-add-pc-product', function(){
		//obtengo los datos de los inputs
		var productName = $('#field-fromPCProductName').val();
		var productCurrency = $('#field-fromPCProductCurrecy').val();
		var productPrice = $('#field-fromPCProductPrice').val();
        var productSalePrice = $('#field-fromPCProductSalePrice').val();
		var productDesc = $('#field-fromPCProductDescription').val();
		var productMedia = $('#field-fromPCProductMedia');
        var productCategory = $('#field-fromPCProductCategory').val();
        const productMediaFiles = document.getElementById('field-fromPCProductMedia').files;

        var productVariationsColor = $('#field-fromPCProductVariationsColor').val();
        var productVariationsColorDesc = $('#field-fromPCProductVariationsColorDesc').val();
        var productVariationsSize = $('#field-fromPCProductVariationsSize').val();
        var productVariationsSizeDesc = $('#field-fromPCProductVariationsSizeDesc').val();
        var productVariationsComment = $('#field-fromPCProductVariationsComment').val();

        var productVariations = [];

        if(productVariationsColor != '' && productVariationsColorDesc != ''){
            productVariations.push({
                "attribute":productVariationsColor,
                "description":productVariationsColorDesc
            });
        }

        if(productVariationsSize != '' && productVariationsSizeDesc != ''){
            productVariations.push({
                "attribute":productVariationsSize,
                "description":productVariationsSizeDesc
            });
        }

        $('.variation').each(function(){
            let attribute = $(this).find('.variation-name').val();
            let description = $(this).find('.variation-value').val(); 

            if(attribute != '' && description != ''){
                productVariations.push({
                    "attribute":attribute,
                    "description":description
                });
            }
        });

        pcProductsIndex = $('.step-3 .products-tab-list .products-list-container .pc-product').length;
        while($('.item-pc-'+pcProductsIndex).length > 0){
            pcProductsIndex++;
        }

        uploaderProductsFiles.updateIndex(pcProductsIndex);

        //aki entra si hay menos de 10 products en la list
        if($('.step-3 .products-tab-list .products-list-container li.product-placeholder').length > 0){
            $('.step-3 .products-tab-list .products-list-container li.product-placeholder').each(function(index){
                var product_placeholder = $(this);
                var counter = product_placeholder.find('.counter').text();
                
                if(index == 0){
            
                    //agrego los inputs en modo arreglo en el listado de productos
                    $('<input type="text" class="PCProductMediaDB item-pc-'+pcProductsIndex+'" name="field-PCProductMediaDB['+pcProductsIndex+'][]" />').appendTo($('.pc-products'));

                    productMedia
                        .clone()
                        .attr('class', 'PCProductMedia item-pc-'+pcProductsIndex)
                        .prop('class', 'PCProductMedia item-pc-'+pcProductsIndex)
                        .attr('name', 'field-PCProductMedia['+pcProductsIndex+'][]')
                        .prop('name', 'field-PCProductMedia['+pcProductsIndex+'][]')
                        .attr('id', '')
                        .prop('id', '')
                        .appendTo($('.pc-products'));

                    // Create a new DataTransfer object to hold the files for this input
                    const dataTransfer = new DataTransfer();
                    for (let i = 0; i < productMediaFiles.length; i++) {
                        dataTransfer.items.add(productMediaFiles[i]); // Add each file to the DataTransfer
                    }

                    ProductsList[pcProductsIndex] = dataTransfer.files;

                    $('<input type="text" class="PCProductName item-pc-'+pcProductsIndex+'" name="field-PCProductName[]" value="'+productName+'" />').appendTo($('.pc-products'));
                    $('<input type="text" class="PCProductCurrecy item-pc-'+pcProductsIndex+'" name="field-PCProductCurrecy[]" value="'+productCurrency+'" />').appendTo($('.pc-products'));
                    $('<input type="text" class="PCProductPrice item-pc-'+pcProductsIndex+'" name="field-PCProductPrice[]" value="'+productPrice+'" />').appendTo($('.pc-products'));
                    $('<input type="text" class="PCProductSalePrice item-pc-'+pcProductsIndex+'" name="field-PCProductSalePrice[]" value="'+productSalePrice+'" />').appendTo($('.pc-products'));
                    $('<input type="text" class="PCProductDescription item-pc-'+pcProductsIndex+'" name="field-PCProductDescription[]" value="'+productDesc+'" />').appendTo($('.pc-products'));
                    $('<input type="text" class="PCProductCategory item-pc-'+pcProductsIndex+'" name="field-PCProductCategory[]" value="'+productCategory+'" />').appendTo($('.pc-products'));
                    $('<input type="text" class="PCProductVariationsComment item-pc-'+pcProductsIndex+'" name="field-PCProductVariationsComment[]" value="'+productVariationsComment+'" />').appendTo($('.pc-products'));
                    $('<input type="text" class="PCProductVariations item-pc-'+pcProductsIndex+'" name="field-PCProductVariations[]" value="'+btoa(JSON.stringify(productVariations))+'" />').val(btoa(JSON.stringify(productVariations))).appendTo($('.pc-products'));
                    $('<input type="text" class="PCProductFeatured item-pc-'+pcProductsIndex+'" name="field-PCProductFeatured[]" value="0" />').appendTo($('.pc-products'));

                    var firstMedia = my_ajax_obj.base_url+'/wp-content/themes/tiendas/img/image-placeholder.png';
                    var done = false;

                    _products_uploaded_files = uploaderProductsFiles.getUploadedFiles();

                    //obtengo la primera imagen subida para mostrar en el listado
                    _products_uploaded_files.forEach(function(file){
                        if(file.index == 'item-pc-'+pcProductsIndex){
                            if((file.type == 'image/jpeg' || file.type == 'image/png' || file.type == 'image/gif') && !done){
                                firstMedia = file.data;
                                done = true;
                            }
                        }
                    });

                    var image_qty = _products_uploaded_files.length;

                    //agrego los datos del producto al listado de productos
                    $('<li class="pc-product d-flex align-items-center justify-content-between py-2 item-pc-'+pcProductsIndex+'" index="item-pc-'+pcProductsIndex+'">'+
                        '<span class="counter me-3">'+counter+'</span>'+
                        '<div class="d-flex align-items-center justify-content-start gap-3 w-75">'+
                            '<img src="'+firstMedia+'" class="product-thumb me-0 img-fluid" />'+
                            '<div class="d-flex flex-column align-items-start justify-content-center">'+
                                '<h3 class="d-flex"><a class="pc-product-edit" href="javascript:void(0)">'+productName+'</a></h3>'+
                                '<a class="product-more-images" href="javascript:void(0)">'+image_qty+' '+(image_qty == 1 ? 'imágen' : 'imágenes')+'</a>'+
                                '<div class="product-images product-images-'+pcProductsIndex+'" style="display: none;">'+
                                    '<span class="product-images-close"><img src="'+my_ajax_obj.base_url+'/wp-content/themes/tiendas/img/close-dark.svg" alt="close preview" /></span>'+
                                '</div>'+
                                (productCategory != '*' ? '<p class="product-categories mb-0">'+productCategory+'</p>' : '')+
                            '</div>'+
                        '</div>'+
                        '<a class="featured me-3 ms-auto" href="javascript:void(0)"><i class="star off"></i></a>'+
                        '<a class="trash me-0 ms-0" href="javascript:void(0)">'+
                            '<span class="product-trash step3 img-fluid me-3"><svg width="19px" height="21px" viewBox="0 0 19 21" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g id="X---Cuestionario-1---Cambios" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><g id="X-Fase-1_Flujo-1y2_Paso-6-List" transform="translate(-769, -385)" fill="#A6A6A7" fill-rule="nonzero"><g id="trash" transform="translate(769, 385.0156)"><path d="M6.90909091,16.25 L6.90909091,7.08333333 C6.90909091,6.96180556 6.86860795,6.86197917 6.78764205,6.78385417 C6.70667614,6.70572917 6.6032197,6.66666667 6.47727273,6.66666667 L5.61363636,6.66666667 C5.48768939,6.66666667 5.38423295,6.70572917 5.30326705,6.78385417 C5.22230114,6.86197917 5.18181818,6.96180556 5.18181818,7.08333333 L5.18181818,16.25 C5.18181818,16.3715278 5.22230114,16.4713542 5.30326705,16.5494792 C5.38423295,16.6276042 5.48768939,16.6666667 5.61363636,16.6666667 L6.47727273,16.6666667 C6.6032197,16.6666667 6.70667614,16.6276042 6.78764205,16.5494792 C6.86860795,16.4713542 6.90909091,16.3715278 6.90909091,16.25 Z M10.3636364,16.25 L10.3636364,7.08333333 C10.3636364,6.96180556 10.3231534,6.86197917 10.2421875,6.78385417 C10.1612216,6.70572917 10.0577652,6.66666667 9.93181818,6.66666667 L9.06818182,6.66666667 C8.94223485,6.66666667 8.83877841,6.70572917 8.7578125,6.78385417 C8.67684659,6.86197917 8.63636364,6.96180556 8.63636364,7.08333333 L8.63636364,16.25 C8.63636364,16.3715278 8.67684659,16.4713542 8.7578125,16.5494792 C8.83877841,16.6276042 8.94223485,16.6666667 9.06818182,16.6666667 L9.93181818,16.6666667 C10.0577652,16.6666667 10.1612216,16.6276042 10.2421875,16.5494792 C10.3231534,16.4713542 10.3636364,16.3715278 10.3636364,16.25 Z M13.8181818,16.25 L13.8181818,7.08333333 C13.8181818,6.96180556 13.7776989,6.86197917 13.696733,6.78385417 C13.615767,6.70572917 13.5123106,6.66666667 13.3863636,6.66666667 L12.5227273,6.66666667 C12.3967803,6.66666667 12.2933239,6.70572917 12.212358,6.78385417 C12.131392,6.86197917 12.0909091,6.96180556 12.0909091,7.08333333 L12.0909091,16.25 C12.0909091,16.3715278 12.131392,16.4713542 12.212358,16.5494792 C12.2933239,16.6276042 12.3967803,16.6666667 12.5227273,16.6666667 L13.3863636,16.6666667 C13.5123106,16.6666667 13.615767,16.6276042 13.696733,16.5494792 C13.7776989,16.4713542 13.8181818,16.3715278 13.8181818,16.25 Z M6.47727273,3.33333333 L12.5227273,3.33333333 L11.875,1.80989583 C11.8120265,1.73177083 11.7355587,1.68402778 11.6455966,1.66666667 L7.36789773,1.66666667 C7.27793561,1.68402778 7.2014678,1.73177083 7.13849432,1.80989583 L6.47727273,3.33333333 Z M19,3.75 L19,4.58333333 C19,4.70486111 18.959517,4.8046875 18.8785511,4.8828125 C18.7975852,4.9609375 18.6941288,5 18.5681818,5 L17.2727273,5 L17.2727273,17.34375 C17.2727273,18.0642361 17.0613163,18.687066 16.6384943,19.2122396 C16.2156723,19.7374132 15.7073864,20 15.1136364,20 L3.88636364,20 C3.29261364,20 2.78432765,19.7460938 2.36150568,19.2382812 C1.93868371,18.7304688 1.72727273,18.1163194 1.72727273,17.3958333 L1.72727273,5 L0.431818182,5 C0.305871212,5 0.202414773,4.9609375 0.121448864,4.8828125 C0.0404829545,4.8046875 0,4.70486111 0,4.58333333 L0,3.75 C0,3.62847222 0.0404829545,3.52864583 0.121448864,3.45052083 C0.202414773,3.37239583 0.305871212,3.33333333 0.431818182,3.33333333 L4.6015625,3.33333333 L5.54616477,1.15885417 C5.68110795,0.837673611 5.92400568,0.564236111 6.27485795,0.338541667 C6.62571023,0.112847222 6.98106061,0 7.34090909,0 L11.6590909,0 C12.0189394,0 12.3742898,0.112847222 12.725142,0.338541667 C13.0759943,0.564236111 13.318892,0.837673611 13.4538352,1.15885417 L14.3984375,3.33333333 L18.5681818,3.33333333 C18.6941288,3.33333333 18.7975852,3.37239583 18.8785511,3.45052083 C18.959517,3.52864583 19,3.62847222 19,3.75 Z" id="Shape"></path></g></g></g></svg></span>'+
                        '</a>'+
                    '</li>').insertAfter(product_placeholder);
                    product_placeholder.remove();

                    //aqui agrego todas los previews de las imagenes de los productos al popup de las imagenes del prod
                    for(var i = 0; i < ProductsList[pcProductsIndex].length; i++){
                        var file = ProductsList[pcProductsIndex][i];
            
                        if(file.index == 'item-pc-'+pcProductsIndex){
                            const previewHtml = `
                                <div class="img-preview-container">
                                    <img src="${file.data}" class="img-preview" alt="">
                                </div>
                            `;
                            $('.product-images-'+pcProductsIndex).append(previewHtml);
                        }
                    }

                    clearLinkFormFields();
                    clearFormFields();
            
                    pcProductsIndex++;

                    $('.step-3 .products-tab-list').fadeIn();
                    $('.step-3 .products-tab-new-product').hide();
                    gsap.to(window, {duration: .2, scrollTo:{ y: 0, offsetY: 0}, ease: "power2.outIn"});
                
                }
            });
        }else{

            //aki si ya hay 10 o mas productos, entonces tengo que sumar el costo adicional por producto al precio total

            var counter = $('.step-3 .products-tab-list .products-list-container li').length + 1;
            var list_container = $('.step-3 .products-tab-list .products-list-container');

            $('<input type="text" class="PCProductMediaDB item-pc-'+pcProductsIndex+'" name="field-PCProductMediaDB['+pcProductsIndex+'][]" />').appendTo($('.pc-products'));

            productMedia
                .clone()
                .attr('class', 'PCProductMedia item-pc-'+pcProductsIndex)
                .prop('class', 'PCProductMedia item-pc-'+pcProductsIndex)
                .attr('name', 'field-PCProductMedia['+pcProductsIndex+'][]')
                .prop('name', 'field-PCProductMedia['+pcProductsIndex+'][]')
                .attr('id', '')
                .prop('id', '')
                .appendTo($('.pc-products'));

            // Create a new DataTransfer object to hold the files for this input
            const dataTransfer = new DataTransfer();
            for (let i = 0; i < productMediaFiles.length; i++) {
                dataTransfer.items.add(productMediaFiles[i]); // Add each file to the DataTransfer
            }

            ProductsList[pcProductsIndex] = dataTransfer.files;

            $('<input type="text" class="PCProductName item-pc-'+pcProductsIndex+'" name="field-PCProductName[]" value="'+productName+'" />').appendTo($('.pc-products'));
            $('<input type="text" class="PCProductCurrecy item-pc-'+pcProductsIndex+'" name="field-PCProductCurrecy[]" value="'+productCurrency+'" />').appendTo($('.pc-products'));
            $('<input type="text" class="PCProductPrice item-pc-'+pcProductsIndex+'" name="field-PCProductPrice[]" value="'+productPrice+'" />').appendTo($('.pc-products'));
            $('<input type="text" class="PCProductSalePrice item-pc-'+pcProductsIndex+'" name="field-PCProductSalePrice[]" value="'+productSalePrice+'" />').appendTo($('.pc-products'));
            $('<input type="text" class="PCProductDescription item-pc-'+pcProductsIndex+'" name="field-PCProductDescription[]" value="'+productDesc+'" />').appendTo($('.pc-products'));
            $('<input type="text" class="PCProductCategory item-pc-'+pcProductsIndex+'" name="field-PCProductCategory[]" value="'+productCategory+'" />').appendTo($('.pc-products'));
            $('<input type="text" class="PCProductVariationsComment item-pc-'+pcProductsIndex+'" name="field-PCProductVariationsComment[]" value="'+productVariationsComment+'" />').appendTo($('.pc-products'));
            $('<input type="text" class="PCProductVariations item-pc-'+pcProductsIndex+'" name="field-PCProductVariations[]" value="'+btoa(JSON.stringify(productVariations))+'" />').val(btoa(JSON.stringify(productVariations))).appendTo($('.pc-products'));
            $('<input type="text" class="PCProductFeatured item-pc-'+pcProductsIndex+'" name="field-PCProductFeatured[]" value="0" />').appendTo($('.pc-products'));

            var firstMedia = my_ajax_obj.base_url+'/wp-content/themes/tiendas/img/image-placeholder.png';
            var done = false;

            _products_uploaded_files = uploaderProductsFiles.getUploadedFiles();

            //obtengo la primera imagen subida para mostrar en el listado
            _products_uploaded_files.forEach(function(file){
                if(file.index == 'item-pc-'+pcProductsIndex){
                    if((file.type == 'image/jpeg' || file.type == 'image/png' || file.type == 'image/gif') && !done){
                        firstMedia = file.data;
                        done = true;
                    }
                }
            });

            var image_qty = _products_uploaded_files.length;

            //agrego los datos del producto al listado de productos
            $('<li class="pc-product d-flex align-items-center justify-content-between py-2 item-pc-'+pcProductsIndex+'" index="item-pc-'+pcProductsIndex+'">'+
                '<span class="counter me-3">'+counter+'</span>'+
                '<div class="d-flex align-items-center justify-content-start gap-3 w-75">'+
                    '<img src="'+firstMedia+'" class="product-thumb me-0 img-fluid" />'+
                    '<div class="d-flex flex-column align-items-start justify-content-center">'+
                        '<h3 class="d-flex"><a class="pc-product-edit" href="javascript:void(0)">'+productName+'</a></h3>'+
                        '<a class="product-more-images" href="javascript:void(0)">'+image_qty+' '+(image_qty == 1 ? 'imágen' : 'imágenes')+'</a>'+
                        '<div class="product-images product-images-'+pcProductsIndex+'" style="display: none;">'+
                            '<span class="product-images-close"><img src="'+my_ajax_obj.base_url+'/wp-content/themes/tiendas/img/close-dark.svg" alt="close preview" /></span>'+
                        '</div>'+
                        (productCategory != '*' ? '<p class="product-categories mb-0">'+productCategory+'</p>' : '')+
                    '</div>'+
                '</div>'+
                '<a class="featured me-3 ms-auto" href="javascript:void(0)"><i class="star off"></i></a>'+
                '<a class="trash me-0 ms-0" href="javascript:void(0)">'+
                    '<span class="product-trash step3 img-fluid me-3"><svg width="19px" height="21px" viewBox="0 0 19 21" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g id="X---Cuestionario-1---Cambios" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><g id="X-Fase-1_Flujo-1y2_Paso-6-List" transform="translate(-769, -385)" fill="#A6A6A7" fill-rule="nonzero"><g id="trash" transform="translate(769, 385.0156)"><path d="M6.90909091,16.25 L6.90909091,7.08333333 C6.90909091,6.96180556 6.86860795,6.86197917 6.78764205,6.78385417 C6.70667614,6.70572917 6.6032197,6.66666667 6.47727273,6.66666667 L5.61363636,6.66666667 C5.48768939,6.66666667 5.38423295,6.70572917 5.30326705,6.78385417 C5.22230114,6.86197917 5.18181818,6.96180556 5.18181818,7.08333333 L5.18181818,16.25 C5.18181818,16.3715278 5.22230114,16.4713542 5.30326705,16.5494792 C5.38423295,16.6276042 5.48768939,16.6666667 5.61363636,16.6666667 L6.47727273,16.6666667 C6.6032197,16.6666667 6.70667614,16.6276042 6.78764205,16.5494792 C6.86860795,16.4713542 6.90909091,16.3715278 6.90909091,16.25 Z M10.3636364,16.25 L10.3636364,7.08333333 C10.3636364,6.96180556 10.3231534,6.86197917 10.2421875,6.78385417 C10.1612216,6.70572917 10.0577652,6.66666667 9.93181818,6.66666667 L9.06818182,6.66666667 C8.94223485,6.66666667 8.83877841,6.70572917 8.7578125,6.78385417 C8.67684659,6.86197917 8.63636364,6.96180556 8.63636364,7.08333333 L8.63636364,16.25 C8.63636364,16.3715278 8.67684659,16.4713542 8.7578125,16.5494792 C8.83877841,16.6276042 8.94223485,16.6666667 9.06818182,16.6666667 L9.93181818,16.6666667 C10.0577652,16.6666667 10.1612216,16.6276042 10.2421875,16.5494792 C10.3231534,16.4713542 10.3636364,16.3715278 10.3636364,16.25 Z M13.8181818,16.25 L13.8181818,7.08333333 C13.8181818,6.96180556 13.7776989,6.86197917 13.696733,6.78385417 C13.615767,6.70572917 13.5123106,6.66666667 13.3863636,6.66666667 L12.5227273,6.66666667 C12.3967803,6.66666667 12.2933239,6.70572917 12.212358,6.78385417 C12.131392,6.86197917 12.0909091,6.96180556 12.0909091,7.08333333 L12.0909091,16.25 C12.0909091,16.3715278 12.131392,16.4713542 12.212358,16.5494792 C12.2933239,16.6276042 12.3967803,16.6666667 12.5227273,16.6666667 L13.3863636,16.6666667 C13.5123106,16.6666667 13.615767,16.6276042 13.696733,16.5494792 C13.7776989,16.4713542 13.8181818,16.3715278 13.8181818,16.25 Z M6.47727273,3.33333333 L12.5227273,3.33333333 L11.875,1.80989583 C11.8120265,1.73177083 11.7355587,1.68402778 11.6455966,1.66666667 L7.36789773,1.66666667 C7.27793561,1.68402778 7.2014678,1.73177083 7.13849432,1.80989583 L6.47727273,3.33333333 Z M19,3.75 L19,4.58333333 C19,4.70486111 18.959517,4.8046875 18.8785511,4.8828125 C18.7975852,4.9609375 18.6941288,5 18.5681818,5 L17.2727273,5 L17.2727273,17.34375 C17.2727273,18.0642361 17.0613163,18.687066 16.6384943,19.2122396 C16.2156723,19.7374132 15.7073864,20 15.1136364,20 L3.88636364,20 C3.29261364,20 2.78432765,19.7460938 2.36150568,19.2382812 C1.93868371,18.7304688 1.72727273,18.1163194 1.72727273,17.3958333 L1.72727273,5 L0.431818182,5 C0.305871212,5 0.202414773,4.9609375 0.121448864,4.8828125 C0.0404829545,4.8046875 0,4.70486111 0,4.58333333 L0,3.75 C0,3.62847222 0.0404829545,3.52864583 0.121448864,3.45052083 C0.202414773,3.37239583 0.305871212,3.33333333 0.431818182,3.33333333 L4.6015625,3.33333333 L5.54616477,1.15885417 C5.68110795,0.837673611 5.92400568,0.564236111 6.27485795,0.338541667 C6.62571023,0.112847222 6.98106061,0 7.34090909,0 L11.6590909,0 C12.0189394,0 12.3742898,0.112847222 12.725142,0.338541667 C13.0759943,0.564236111 13.318892,0.837673611 13.4538352,1.15885417 L14.3984375,3.33333333 L18.5681818,3.33333333 C18.6941288,3.33333333 18.7975852,3.37239583 18.8785511,3.45052083 C18.959517,3.52864583 19,3.62847222 19,3.75 Z" id="Shape"></path></g></g></g></svg></span>'+
                '</a>'+
            '</li>').appendTo(list_container);

            //aqui agrego todas los previews de las imagenes de los productos al popup de las imagenes del prod
            for(var i = 0; i < ProductsList[pcProductsIndex].length; i++){
                var file = ProductsList[pcProductsIndex][i];
    
                if(file.index == 'item-pc-'+pcProductsIndex){
                    const previewHtml = `
                        <div class="img-preview-container">
                            <img src="${file.data}" class="img-preview" alt="">
                        </div>
                    `;
                    $('.product-images-'+pcProductsIndex).append(previewHtml);
                }
    
            }

            clearLinkFormFields();
            clearFormFields();
    
            pcProductsIndex++;

            $('.step-3 .products-tab-list').fadeIn();
            $('.step-3 .products-tab-new-product').hide();
            gsap.to(window, {duration: .2, scrollTo:{ y: 0, offsetY: 0}, ease: "power2.outIn"});
        }

        var total_products_qty = Number($('.total-products-qty').text());
        total_products_qty++;
        if(total_products_qty > 10){
            $('.base-cost-included').hide();
        }else{
            $('.base-cost-included').show();
        }
        $('.total-products-qty').text(total_products_qty);
        
        // $('.popover').remove();
        // $('#howto-popover').attr('data-bs-content', my_ajax_obj.product_extra_8);
        // message('howto-popover');

        popup_message(my_ajax_obj.product_extra_8, 'info', 2000);

        $('#store-products-form').find('.btn-save-products').removeClass('disabled');

        verify_products_qty();
	});

    var pcProductIndex = -1;
    var linkProductIndex = -1;
    var isFeatured = false;

    /**
	evento para editar el producto desde el listado de productos subidos desde link
	*/
	$('body').on('click', '.step-3 .link-product-edit', function(e){
		e.preventDefault();

		var item = $(this);
		itemIndex = item.parents('li').attr('index');
		var index = itemIndex.split('item-link-')[1];

        linkProductIndex = index;
        isFeatured = !item.parent().parent().parent().next('.featured').find('.star').hasClass('off');

        clearLinkFormFields();
        clearFormFields();

        $('.step-3 .btn-add-pc-product').addClass('disabled');
        $('.step-3 .btn-add-link-product').addClass('disabled');

        const linktab = document.querySelector('.step-3 #myTabAddEditProduct button[data-bs-target="#fromlink"]');
        if(linktab != null){
            bootstrap.Tab.getOrCreateInstance(linktab).show();
        }

        var storeID = $('.products-tab-new-product').attr('store-id');
        $.post(my_ajax_obj.ajax_url, {action: 'ws', wsa: 'get-store-categories', storeID: storeID}, function(response){
            $('.step-3 #myTabContentAddEditProduct #fromlink #field-fromLinkProductCategory').html(response.categories);

            $('.step-3 #myTabContentAddEditProduct #fromlink #field-fromLinkProductCategory').find('option').each(function(){
                if($(this).val() == $('.link-products .'+itemIndex+'.LinkProductCategory').val())
                    $(this).attr('selected', 'selected');
            });
        });

        $(".step-3 #myTabContentAddEditProduct #fromlink #field-fromLinkProductName").val($('.link-products .'+itemIndex+'.LinkProductName').val());
        $(".step-3 #myTabContentAddEditProduct #fromlink #field-fromLinkProductLink").val($('.link-products .'+itemIndex+'.LinkProductLink').val());

        $('.step-3 #myTabContentAddEditProduct #fromlink .btn-cancel-link-product').removeClass('d-none');
        $('.step-3 #myTabContentAddEditProduct #fromlink .btn-save-link-product').removeClass('d-none');
        $('.step-3 #myTabContentAddEditProduct #fromlink .btn-add-link-product').addClass('d-none');

        $('.step-3 .products-tab-list').hide();
        $('.step-3 .products-tab-new-product').fadeIn();
        gsap.to(window, {duration: .2, scrollTo:{ y: 0, offsetY: 0}, ease: "power2.outIn"});
        confirm_aditional_product = false;
	});

    /**
	evento para editar el producto desde el listado de productos subidos desde pc
	*/
	$('body').on('click', '.step-3 .pc-product-edit', function(e){
		e.preventDefault();

		var item = $(this);
		itemIndex = item.parents('li').attr('index');
		var index = itemIndex.split('item-pc-')[1];

        pcProductIndex = index;
        isFeatured = !item.parent().parent().parent().next('.featured').find('.star').hasClass('off');

        clearLinkFormFields();
        clearFormFields();

        $('.field-preview-media').html('');

        $('.step-3 .btn-add-pc-product').addClass('disabled');
        $('.step-3 .btn-add-link-product').addClass('disabled');

        const pctab = document.querySelector('.step-3 #myTabAddEditProduct button[data-bs-target="#frompc"]');
        if(pctab != null){
            bootstrap.Tab.getOrCreateInstance(pctab).show();
        }

        var storeID = $('.products-tab-new-product').attr('store-id');
        $.post(my_ajax_obj.ajax_url, {action: 'ws', wsa: 'get-store-categories', storeID: storeID}, function(response){
            $('.step-3 #myTabContentAddEditProduct #frompc #field-fromPCProductCategory').html(response.categories);

            $('.step-3 #myTabContentAddEditProduct #frompc #field-fromPCProductCategory').find('option').each(function(){
                if($(this).val() == $('.pc-products .'+itemIndex+'.PCProductCategory').val()){
                    $(this).attr('selected', 'selected');
                }
            });
        });

        var fields = $('.pc-product.'+itemIndex);
        if(fields.length > 0) {
            uploaderProductsFiles.updateIndex(index);
            uploaderProductsFiles.updateStoreId(storeID);
            uploaderProductsFiles.fetchExistingFiles();
        }

        $(".step-3 #myTabContentAddEditProduct #frompc #field-fromPCProductName").val($('.pc-products .'+itemIndex+'.PCProductName').val());
        $(".step-3 #myTabContentAddEditProduct #frompc #field-fromPCProductPrice").val($('.pc-products .'+itemIndex+'.PCProductPrice').val());
        $(".step-3 #myTabContentAddEditProduct #frompc #field-fromPCProductSalePrice").val($('.pc-products .'+itemIndex+'.PCProductSalePrice').val());
        $(".step-3 #myTabContentAddEditProduct #frompc #field-fromPCProductDescription").val($('.pc-products .'+itemIndex+'.PCProductDescription').val());

        var variations = $('.pc-products .'+itemIndex+'.PCProductVariations').val();
        if(variations != ''){
            variations = atob(variations);
            variations = JSON.parse(variations);

            if(variations != false){
                
                if(!$(".step-3 #myTabContentAddEditProduct #frompc #field-fromPCProductVariable").is(":checked")){
                    $(".step-3 #myTabContentAddEditProduct #frompc #field-fromPCProductVariable").trigger('click');
                }
                $(".step-3 #myTabContentAddEditProduct #frompc #field-fromPCProductVariationsComment").val($('.pc-products .'+itemIndex+'.PCProductVariationsComment').val());

                var otherIndex = 1;
                variations.forEach((variation) => {
                    if(variation.attribute == 'Color'){
                        if(!$(".step-3 #myTabContentAddEditProduct #frompc #field-fromPCProductVariationsColor").is(":checked")){
                            $(".step-3 #myTabContentAddEditProduct #frompc #field-fromPCProductVariationsColor").trigger('click');
                        }
                        $(".step-3 #myTabContentAddEditProduct #frompc #field-fromPCProductVariationsColorDesc").val(variation.description);
                    }else if(variation.attribute == 'Talla' || variation.attribute == 'Size'){
                        if(!$(".step-3 #myTabContentAddEditProduct #frompc #field-fromPCProductVariationsSize").is(":checked")){
                            $(".step-3 #myTabContentAddEditProduct #frompc #field-fromPCProductVariationsSize").trigger('click');
                        }
                        $(".step-3 #myTabContentAddEditProduct #frompc #field-fromPCProductVariationsSizeDesc").val(variation.description);
                    }else{
                        if(!$(".step-3 #myTabContentAddEditProduct #frompc #field-fromPCProductVariationsOther-index-"+otherIndex).is(":checked")){
                            $(".step-3 #myTabContentAddEditProduct #frompc #field-fromPCProductVariationsOther-index-"+otherIndex).trigger('click');
                        }
                        $(".step-3 #myTabContentAddEditProduct #frompc #field-fromPCProductVariationsOtherName-index-"+otherIndex).val(variation.attribute);
                        $(".step-3 #myTabContentAddEditProduct #frompc #field-fromPCProductVariationsOtherDesc-index-"+otherIndex).val(variation.description);
                        otherIndex++;
                    }
                });
            }
        }
        editProductMedia = true;

        $('.step-3 .products-tab-list').hide();
        $('.step-3 .products-tab-new-product').fadeIn();
        gsap.to(window, {duration: .2, scrollTo:{ y: 0, offsetY: 0}, ease: "power2.outIn"});
        confirm_aditional_product = false;
	});

    const clearLinkFormFields = () => {
        $(".step-3 #myTabContentAddEditProduct #fromlink #field-fromLinkProductName").val('');
        $(".step-3 #myTabContentAddEditProduct #fromlink #field-fromLinkProductLink").val('');

        $('.step-3 #myTabContentAddEditProduct #fromlink .btn-cancel-link-product').removeClass('d-none');
        $('.step-3 #myTabContentAddEditProduct #fromlink .btn-save-link-product').removeClass('d-none');
        $('.step-3 #myTabContentAddEditProduct #fromlink .btn-add-link-product').addClass('d-none');
    }

    const clearFormFields = () => {
        if($('.step-3 #field-fromPCProductVariationsColor').is(":checked")){
            $('.step-3 #field-fromPCProductVariationsColor').trigger('click');
        }
        $('.step-3 #field-fromPCProductVariationsColorDesc').val('');

        if($('.step-3 #field-fromPCProductVariationsSize').is(":checked")){
            $('.step-3 #field-fromPCProductVariationsSize').trigger('click');
        }
        $('.step-3 #field-fromPCProductVariationsSizeDesc').val('');

        if($('.step-3 #field-fromPCProductVariable').is(":checked")){
            $('.step-3 #field-fromPCProductVariable').trigger('click');
        }

        $('.step-3 #field-fromPCProductVariationsComment').val('');

        $('.step-3 #myTabContentAddEditProduct #frompc #field-fromPCProductName').val('');
        $('.step-3 #myTabContentAddEditProduct #frompc #field-fromPCProductPrice').val('');
        $('.step-3 #myTabContentAddEditProduct #frompc #field-fromPCProductSalePrice').val('');
        $('.step-3 #myTabContentAddEditProduct #frompc #field-fromPCProductDescription').val('');

        $('.variation').each(function(i){
            $(this).remove();
        });

        $('<div class="variation variation-index-1">'+
            '<div class="form-check">'+
                '<input type="checkbox" id="field-fromPCProductVariationsOther-index-1" name="field-fromPCProductVariationsOther[]" class="form-check-input field-fromPCProductVariationsOther" value="'+my_ajax_obj.other_variation+'">'+
                '<label class="form-check-label" for="field-fromPCProductVariationsOther-index-1">'+
                    my_ajax_obj.other_variation+' <index></index>'+
                '</label>'+
            '</div>'+
            '<div class="other-variation-desc-container-index-1" style="display: none;">'+
                '<div class="field mt-2 mb-0 ps-4">'+
                    '<label class="form-label small" for="field-fromPCProductVariationsOtherName-index-1">'+my_ajax_obj.variation_name+'</label>'+
                    '<input type="text" class="form-control variation-name" id="field-fromPCProductVariationsOtherName-index-1" name="field-fromPCProductVariationsOtherName-1" placeholder="'+my_ajax_obj.variation_name_placeholder+'">'+
                    '<span class="error"></span>'+
                '</div>'+
                '<div class="field mt-0 mb-0 ps-4">'+
                    '<label class="form-label small" for="field-fromPCProductVariationsOtherDesc-index-1">'+my_ajax_obj.variation_desc+'</label>'+
                    '<textarea class="form-control variation-value" id="field-fromPCProductVariationsOtherDesc-index-1" name="field-fromPCProductVariationsOtherDesc-1" placeholder="'+my_ajax_obj.variation_desc_placeholder+'"></textarea>'+
                    '<span class="error"></span>'+
                '</div>'+
            '</div>'+
        '</div>').appendTo($('.other-variation'));

        $('.step-3 #myTabContentAddEditProduct #frompc .btn-cancel-pc-product').removeClass('d-none');
        $('.step-3 #myTabContentAddEditProduct #frompc .btn-save-pc-product').removeClass('d-none');
        $('.step-3 #myTabContentAddEditProduct #frompc .btn-add-pc-product').addClass('d-none');

        //============================================================================================================================================
        //limpio el upload field
        $('.step-3 #myTabContentAddEditProduct #frompc .field-upload-products').find('.field-upload-content').hide();
        $('.step-3 #myTabContentAddEditProduct #frompc .field-upload-products').find('.field-upload-content').find('.image-preview-close').hide();
        $('.step-3 #myTabContentAddEditProduct #frompc .field-upload-products').find('.field-upload-field').fadeIn();
        // field_upload.find('.field-upload-overlay').fadeIn();
        $('.step-3 #myTabContentAddEditProduct #frompc .field-upload-products').find('.field-upload-content').find('.img-preview-container').remove();
        
        uploaderProductsFiles.clearFiles();
        //============================================================================================================================================
    }

    /**
	evento para guardar los cambios realizados al producto desde link
	*/
	$('body').on('click', '.step-3 .btn-save-link-product', function(e) {
		//obtengo los datos de los inputs
		var productName = $('#field-fromLinkProductName').val();
		var productLink = $('#field-fromLinkProductLink').val();
        var productCategory = $('#field-fromLinkProductCategory').val();
        
        $('.LinkProductName.item-link-'+linkProductIndex).val(productName);
        $('.LinkProductLink.item-link-'+linkProductIndex).val(productLink);
        $('.LinkProductCategory.item-link-'+linkProductIndex).val(productCategory);
        $('.LinkProductFeatured.item-link-'+linkProductIndex).val(Number(isFeatured));

        $('.link-product.item-link-'+linkProductIndex).find('.link-product-edit').html(productName);
        $('.link-product.item-link-'+linkProductIndex).find('.direct-link').html(productLink).attr('href', productLink);
        $('.link-product.item-link-'+linkProductIndex).find('.product-categories').html(productCategory);
        
        if(isFeatured)
            $('.link-product.item-link-'+linkProductIndex).find('.featured').find('.star').removeClass('off').addClass('on');
        else
            $('.link-product.item-link-'+linkProductIndex).find('.featured').find('.star').removeClass('on').addClass('off');

        clearLinkFormFields();
        clearFormFields();

        $('.step-3 .products-tab-list').fadeIn();
        $('.step-3 .products-tab-new-product').hide();
        gsap.to(window, {duration: .2, scrollTo:{ y: 0, offsetY: 0}, ease: "power2.outIn"});
	});

    /**
	evento para guardar los cambios realizados al producto desde pc
	*/
	$('body').on('click', '.step-3 .btn-save-pc-product', function(e) {
		//obtengo los datos de los inputs
		var productName = $('#field-fromPCProductName').val();
		var productCurrency = $('#field-fromPCProductCurrecy').val();
		var productPrice = $('#field-fromPCProductPrice').val();
        var productSalePrice = $('#field-fromPCProductSalePrice').val();
		var productDesc = $('#field-fromPCProductDescription').val();
		var productMedia = $('#field-fromPCProductMedia');
        var productCategory = $('#field-fromPCProductCategory').val();
        const productMediaFiles = document.getElementById('field-fromPCProductMedia').files;

        var productVariationsColor = $('#field-fromPCProductVariationsColor').val();
        var productVariationsColorDesc = $('#field-fromPCProductVariationsColorDesc').val();
        var productVariationsSize = $('#field-fromPCProductVariationsSize').val();
        var productVariationsSizeDesc = $('#field-fromPCProductVariationsSizeDesc').val();
        var productVariationsComment = $('#field-fromPCProductVariationsComment').val();

        var productVariations = [];

        if(productVariationsColor != '' && productVariationsColorDesc != ''){
            productVariations.push({
                "attribute":productVariationsColor,
                "description":productVariationsColorDesc
            });
        }

        if(productVariationsSize != '' && productVariationsSizeDesc != ''){
            productVariations.push({
                "attribute":productVariationsSize,
                "description":productVariationsSizeDesc
            });
        }

        $('.variation').each(function(){
            let attribute = $(this).find('.variation-name').val();
            let description = $(this).find('.variation-value').val(); 

            if(attribute != '' && attribute != undefined && description != '' && description != undefined){
                productVariations.push({
                    "attribute":attribute,
                    "description":description
                });
            }
        });

        // console.log(productVariations)

        // Create a new DataTransfer object to hold the files for this input
        const dataTransfer = new DataTransfer();
        for (let i = 0; i < productMediaFiles.length; i++) {
            dataTransfer.items.add(productMediaFiles[i]); // Add each file to the DataTransfer
        }

        ProductsList[pcProductIndex] = dataTransfer.files;

        // console.log(dataTransfer.files)

        $('.PCProductName.item-pc-'+pcProductIndex).val(productName);
        $('.PCProductCurrecy.item-pc-'+pcProductIndex).val(productCurrency);
        $('.PCProductPrice.item-pc-'+pcProductIndex).val(productPrice);
        $('.PCProductSalePrice.item-pc-'+pcProductIndex).val(productSalePrice);
        $('.PCProductDescription.item-pc-'+pcProductIndex).val(productDesc);
        $('.PCProductCategory.item-pc-'+pcProductIndex).val(productCategory);
        $('.PCProductVariationsComment.item-pc-'+pcProductIndex).val(productVariationsComment);
        $('.PCProductVariations.item-pc-'+pcProductIndex).val(btoa(JSON.stringify(productVariations)));
        $('.PCProductFeatured.item-pc-'+pcProductIndex).val(Number(isFeatured));

        $('.PCProductMedia.item-pc-'+pcProductIndex).remove();
        $('.PCProductMediaDB.item-pc-'+pcProductIndex).val('');

        productMedia
            .clone()
            .attr('class', 'PCProductMedia item-pc-'+pcProductIndex)
            .prop('class', 'PCProductMedia item-pc-'+pcProductIndex)
            .attr('name', 'field-PCProductMedia['+pcProductIndex+'][]')
            .prop('name', 'field-PCProductMedia['+pcProductIndex+'][]')
            .attr('id', '')
            .prop('id', '')
            .appendTo($('.pc-products'));

        var firstMedia = my_ajax_obj.base_url+'/wp-content/themes/tiendas/img/image-placeholder.png';
        var done = false;

        _products_uploaded_files = uploaderProductsFiles.getUploadedFiles();

        // console.log(_products_uploaded_files)

        var image_qty = _products_uploaded_files.length;

        //obtengo la primera imagen subida para mostrar en el listado
        _products_uploaded_files.forEach(function(file){
            // console.log(file.url, file.data)
            if((file.type == 'image/jpeg' || file.type == 'image/png' || file.type == 'image/gif') && !done){
                firstMedia = file.url ? file.url : file.data;
                done = true;
            }
        });

        // console.log(firstMedia)

        $('.pc-product.item-pc-'+pcProductIndex).find('.product-thumb').attr('src', firstMedia);
        $('.pc-product.item-pc-'+pcProductIndex).find('.pc-product-edit').html(productName);
        $('.pc-product.item-pc-'+pcProductIndex).find('.product-more-images').html(image_qty+' '+(image_qty == 1 ? 'imágen' : 'imágenes'));
        $('.pc-product.item-pc-'+pcProductIndex).find('.product-categories').html(productCategory);
        
        if(isFeatured)
            $('.pc-product.item-pc-'+pcProductIndex).find('.featured').find('.star').removeClass('off').addClass('on');
        else
            $('.pc-product.item-pc-'+pcProductIndex).find('.featured').find('.star').removeClass('on').addClass('off');

        $('.pc-product.item-pc-'+pcProductIndex).find('.product-images').find('.img-preview-container').remove();

        for(var i = 0; i < ProductsList[pcProductIndex].length; i++){
            var file = ProductsList[pcProductIndex][i];
            // console.log(file)
            const previewHtml = `
                <div class="img-preview-container">
                    <img src="${file.data ? file.data : file.url}" class="img-preview" alt="">
                </div>
            `;
            $('.pc-product.item-pc-'+pcProductIndex).find('.product-images').append(previewHtml);
        }

        clearLinkFormFields();
        clearFormFields();

        $('.step-3 .products-tab-list').fadeIn();
        $('.step-3 .products-tab-new-product').hide();
        gsap.to(window, {duration: .2, scrollTo:{ y: 0, offsetY: 0}, ease: "power2.outIn"});
	});

    $('body').on('click', '#field-fromPCProductVariable', function(e){
        if($(this).is(':checked')){
            $('.product-variations').fadeIn();
        }else{
            $('.product-variations').fadeOut();
        }
    });

    $('body').on('click', '.color-variation input', function(e){
        if($(this).is(':checked')){
            $('.color-variation-desc').fadeIn();
        }else{
            $('.color-variation-desc').fadeOut();
        }
    });

    $('body').on('click', '.size-variation input', function(e){
        if($(this).is(':checked')){
            $('.size-variation-desc').fadeIn();
        }else{
            $('.size-variation-desc').fadeOut();
        }
    });

    $('body').on('click', '.size-variation input', function(e){
        if($(this).is(':checked')){
            $('.size-variation-desc').fadeIn();
        }else{
            $('.size-variation-desc').fadeOut();
        }
    });

    $('body').on('click', '.field-fromPCProductVariationsOther', function(e){
        let id = $(this).attr('id');
        let index = id.split('-')[3];

        // console.log(index)

        if($(this).is(':checked')){
            $('.other-variation-desc-container-index-'+index).fadeIn();
            addOtherVariation(Number(index) + 1);
        }else{
            $('.other-variation-desc-container-index-'+index).fadeOut();
            deleteOtherVariation(Number(index));
        }
    });

    const deleteOtherVariation = (deleteIndex) => {
        $('.variation-index-'+deleteIndex).remove();

        $('.variation').each(function(i){
            let variation = $(this);

            if(i == 0){
                variation.find('index').html('');
            }else{
                variation.find('index').html(i + 1);
            }
        });
    }

    const checkOtherVariations = () => {
        let allVariationsChecked = true;
        $('.variation').each(function(i){
            $(this).find('.form-control').each(function(){
                if($(this).val() == ''){
                    allVariationsChecked = false;
                    return;
                }
            });
            if(!allVariationsChecked){
                return;
            }else{
                $(this).find('.form-check-input').attr('disabled', 'false');
            }
        });

        if(pcnameok && pcpriceok && pcdescok && pccategoryok){
            $('.btn-add-pc-product').removeClass('disabled');
            $('.btn-save-pc-product').removeClass('disabled');
        }else{
            $('.btn-add-pc-product').addClass('disabled');
            $('.btn-save-pc-product').addClass('disabled');
        }
    }

    const addOtherVariation = (index) => {
        if($('.variation').length == 5){
            return;
        }
        let html = ''+
            '<div class="variation variation-index-'+index+'">'+
                '<div class="form-check">'+
                    '<input type="checkbox" id="field-fromPCProductVariationsOther-index-'+index+'" name="field-fromPCProductVariationsOther[]" class="form-check-input field-fromPCProductVariationsOther" value="'+my_ajax_obj.other_variation+'">'+
                    '<label class="form-check-label" for="field-fromPCProductVariationsOther-index-'+index+'">'+
                        my_ajax_obj.other_variation+' <index>'+($('.variation').length + 1)+'</index>'+
                    '</label>'+
                '</div>'+
                '<div class="other-variation-desc-container-index-'+index+'" style="display: none;">'+
                    '<div class="field mt-2 mb-0 ps-4">'+
                        '<label class="form-label small" for="field-fromPCProductVariationsOtherName-index-'+index+'">'+my_ajax_obj.variation_name+'</label>'+
                        '<input type="text" class="form-control" id="field-fromPCProductVariationsOtherName-index-'+index+'" name="field-fromPCProductVariationsOtherName-index-'+index+'" placeholder="'+my_ajax_obj.variation_name_placeholder+'">'+
                        '<span class="error"></span>'+
                    '</div>'+
                    '<div class="field mt-0 mb-0 ps-4">'+
                        '<label class="form-label small" for="field-fromPCProductVariationsOtherDesc-index-'+index+'">'+my_ajax_obj.variation_desc+'</label>'+
                        '<textarea class="form-control" id="field-fromPCProductVariationsOtherDesc-index-'+index+'" name="field-fromPCProductVariationsOtherDesc-index-'+index+'" placeholder="'+my_ajax_obj.variation_desc_placeholder+'"></textarea>'+
                        '<span class="error"></span>'+
                    '</div>'+
                '</div>'+
            '</div>'+
        '';

        $(html).appendTo('.other-variation');

        // $('.other-variation').find('.form-control').each(function() {
        //     $(this).keyup(function() {
        //         checkOtherVariations();
        //     });
        // });
    }

    // $('.other-variation').find('.form-control').each(function() {
    //     $(this).keyup(function() {
    //         checkOtherVariations();
    //     });
    // });

    const verify_products_qty = () => {
        var store_id = $('#store-products-form').find('input[name="store_id"]').val();
        var base_price = $('#store-products-form').find('input[name="base_price"]').val();
        var aditional_product_price = $('#store-products-form').find('input[name="aditional_product_price"]').val();
        var products_qty_included = $('#store-products-form').find('input[name="products_qty_included"]').val();

        var products_qty = pcProductsIndex + linkProductsIndex;

        if(products_qty > products_qty_included){
            var new_price = Number(base_price) + ((Number(products_qty) - Number(products_qty_included)) * Number(aditional_product_price));
            new_price = new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(new_price);

            var popover = {};

            $('.popover').remove();
            $('#howto-popover').attr('data-bs-content', my_ajax_obj.add_extra_product+new_price+'</b>');
            popover = message('howto-popover');

            $('.base-cost-included').hide();
        }else{
            $('.base-cost-included').show();
        }
    };

    $('body').on('click', '.btn-toggle-section', function(){
        var storeForm = $('#store-products-extra');
        var btn = $(this);
        if(!btn.hasClass('toggled')){
            btn.parents('.side-column').prev('.main-column').addClass('disabled');
            btn.parents('.side-column').next('.bottom-column').find('.btn-save-store-products-extra').attr('disabled', true).prop('disabled', true);
            var btnon = btn.find('.include.on');
            var btnoff = btn.find('.include.off');

            btnon.removeClass('on').addClass('off').removeClass('d-none');
            btnoff.removeClass('off').addClass('on').addClass('d-none');
            
            btn.find('svg.off').addClass('d-none');
            btn.find('svg.on').removeClass('d-none');
           
            btn.addClass('toggled');
            btn.parent().find('.message-on').hide();
            btn.parent().find('.message-off').show();

            storeForm.find('.bottom-column').fadeOut();

            storeForm.find('input[name="field-store-products-extra-featured"]').val('');
            var field_upload = storeForm.find('.field-upload-animations');
            field_upload.find('.field-upload-field').show();
            field_upload.find('.field-upload-content').hide();
            field_upload.find('.field-upload-content').find('.image-preview-close').hide();
            field_upload.find('.img-preview-container').remove();

            field_upload.find('#field-store-animations')[0].value = null;
            step3_input_extra_animations = [];

            $.post(my_ajax_obj.ajax_url, {action: 'ws', wsa: 'toggle-aditional-information', activated: false, store_id: storeForm.find('input[name="store_id"]').val()}, function(resp){
                // console.log(resp);
                $('#myTabProducts').find('#products-extra-info-tab').removeClass('done').addClass('working');
                $('#myTabStep3').find('#products-tab').addClass('working').removeClass('done');
            });
        }else{
            btn.parents('.side-column').prev('.main-column').removeClass('disabled');
            btn.parents('.side-column').next('.bottom-column').find('.btn-save-store-products-extra').attr('disabled', false).prop('disabled', false).removeClass('disabled');
            var btnon = btn.find('.include.on');
            var btnoff = btn.find('.include.off');

            btnon.removeClass('on').addClass('off').removeClass('d-none');
            btnoff.removeClass('off').addClass('on').addClass('d-none');
            
            btn.find('svg.on').addClass('d-none');
            btn.find('svg.off').removeClass('d-none');
            
            btn.removeClass('toggled');
            btn.parent().find('.message-on').show();
            btn.parent().find('.message-off').hide();

            storeForm.find('.bottom-column').fadeIn();
        }
    });

    //submit aditional information form
    $('body').on('submit', '#store-products-extra', function(e) {
        e.preventDefault();
        var actionUrl = my_ajax_obj.ajax_url;
        var storeForm = $('#store-products-extra');
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

            $('#myTabProducts').find('#products-extra-info-tab').removeClass('working').removeClass('visited').addClass('done');

            check_tab_products_status();

            navigateNextTab(storeForm);

        }).fail(function() {
            popup_message(my_ajax_obj.error, 'error', 2000);
            storeForm.find('.form-loader').fadeOut();
        });
    });

    $('#store-products-extra .btn-continue').click(function(){
        var actionUrl = my_ajax_obj.ajax_url;
        var storeForm = $('#store-products-form');
        $.post(actionUrl, {action: 'ws', wsa: 'save-store-products-extra', ignore: true, store_id: storeForm.find('input[name="store_id"]').val()}, function(resp){
            $('#myTabProducts').find('#products-extra-info-tab').removeClass('working').addClass('done');
            check_tab_products_status();
        });
    });

    //submit added products form
    $('body').on('submit', '#store-products-form', function(e) {
        e.preventDefault();
        var actionUrl = my_ajax_obj.ajax_url;
        var storeForm = $('#store-products-form');
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

            update_popup(storeForm);

            storeForm.find('.msg').removeClass('error').removeClass('success').html('').fadeOut();
            $('#myTabProducts').find('#products-products-tab').removeClass('working').removeClass('visited').addClass('done');

            check_tab_products_status();

            navigateNextTab(storeForm);

        }).fail(function() {
            popup_message(my_ajax_obj.error, 'error', 2000);
            storeForm.find('.form-loader').fadeOut();
        });
    });

    $('#field-store-batch-file').change(function(){
        if($(this).length){
            if($(this)[0].files.length){
                $('.excel-form .btn-batch-import').removeClass('disabled');
            }else{
                $('.excel-form .btn-batch-import').addClass('disabled');
            }
        }
    });

    $('#field-store-batch-url').keyup(function(e){
        if($(this).val() != ''){
            $('.sheet-form .btn-batch-import').removeClass('disabled');
        }else{
            $('.sheet-form .btn-batch-import').addClass('disabled');
        }
    });

    //submit batch file import products
    $('body').on('submit', '#store-batch-file-import-products-form', function(e) {
        e.preventDefault();
        var actionUrl = my_ajax_obj.ajax_url;
        var storeForm = $('#store-batch-file-import-products-form');
        var scrollTop = jQuery(window).scrollTop();

        gsap.to(window, {duration: .2, scrollTo:{ y: (scrollTop/2), offsetY: 0}, ease: "power2.outIn", onComplete: function(){}});
            
        $('.batch-import-form-modal').find('.form-loader').show();

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
            $('.batch-import-form-modal').find('.form-loader').fadeOut();

            gsap.to(window, {duration: .2, scrollTo:{ y: 0, offsetY: 0}, ease: "power2.outIn"});

            if(response.error == false){
                $('#store-products-form').html(response.products);
            
                // $('.popover').remove();
                // $('#howto-popover').attr('data-bs-content', response.msg);
                // message('howto-popover');

                popup_message(response.msg, 'info', 2000);

                update_popup(storeForm);

                var total_products_qty = Number($('.total-products-qty').text());
                if(total_products_qty > 10){
                    $('.base-cost-included').hide();
                }else{
                    $('.base-cost-included').show();
                }

                $('.btn-batch-close-modal').trigger('click');
            }else{
                popup_message(response.msg, 'error', 2000);
            }

            // storeForm.find('.msg').removeClass('error').removeClass('success').html('').fadeOut();
            // $('#myTabProducts').find('#products-products-tab').removeClass('working').removeClass('visited').addClass('done');

            // check_tab_products_status();

            // navigateNextTab(storeForm);

        }).fail(function() {
            popup_message(my_ajax_obj.error, 'error', 2000);
            $('.batch-import-form-modal').find('.form-loader').fadeOut();
        });
    });

    //submit batch file import products
    $('body').on('submit', '#store-batch-sheet-import-products-form', function(e) {
        e.preventDefault();
        var actionUrl = my_ajax_obj.ajax_url;
        var storeForm = $('#store-batch-sheet-import-products-form');
        var scrollTop = jQuery(window).scrollTop();

        gsap.to(window, {duration: .2, scrollTo:{ y: (scrollTop/2), offsetY: 0}, ease: "power2.outIn", onComplete: function(){}});
            
        $('.batch-import-form-modal').find('.form-loader').show();

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
            $('.batch-import-form-modal').find('.form-loader').fadeOut();

            gsap.to(window, {duration: .2, scrollTo:{ y: 0, offsetY: 0}, ease: "power2.outIn"});

            if(response.error == false){
                $('#store-products-form').html(response.products);
            
                // $('.popover').remove();
                // $('#howto-popover').attr('data-bs-content', response.msg);
                // message('howto-popover');

                popup_message(response.msg, 'info', 2000);

                update_popup(storeForm);

                var total_products_qty = Number($('.total-products-qty').text());
                if(total_products_qty > 10){
                    $('.base-cost-included').hide();
                }else{
                    $('.base-cost-included').show();
                }

                $('.btn-batch-close-modal').trigger('click');
            }else{
                popup_message(response.msg, 'error', 2000);
            }

            // storeForm.find('.msg').removeClass('error').removeClass('success').html('').fadeOut();
            // $('#myTabProducts').find('#products-products-tab').removeClass('working').removeClass('visited').addClass('done');

            // check_tab_products_status();

            // navigateNextTab(storeForm);

        }).fail(function() {
            popup_message(my_ajax_obj.error, 'error', 2000);
            $('.batch-import-form-modal').find('.form-loader').fadeOut();
        });
    });
});
<?php
function step_3_tabs_products_extra($design_id) {
    if(isset($designId) && $designId != 0 && $designId != ''){
        $store = get_field('store', $designId);
    }else{
        $userid = get_current_user_id();
        $store = get_posts(array(
            'post_type' => 'initial-stage',
            'post_status' => 'publish',
            'posts_per_page' => 1,
            'meta_query' => array(
                array(
                    'key' => 'user',
                    'value' => $userid,
                    'compare' => '='
                )
            )
        ));
        $store = $store[0];
    }

    $additional_information = get_field('additional_information', $store->ID);

    $store_products_extra_featured = @$additional_information['products_featured_information'];
    $store_products_extra_animations = @$additional_information['images_or_animations'];
    
    $_files = '';

    if(is_array($store_products_extra_animations) && count($store_products_extra_animations) > 0){
        foreach($store_products_extra_animations as $file){
            $_files .= $file['file']['url'];
        }
    }
    
    return '
        <form id="store-products-extra" method="post" enctype="multipart/form-data">
            <div class="form-loader" style="display: none;"></div>
            <input type="hidden" name="action" value="ws" />
            <input type="hidden" name="lang" value="'.ICL_LANGUAGE_CODE.'" />
            <input type="hidden" name="wsa" value="save-store-products-extra" />
            <input type="hidden" name="disable" value="false" />
            <input type="hidden" name="store_id" value="'.$store->ID.'" />
            <div class="row">
                <div class="pe-md-5 pe-sm-auto pe-auto col-md-8 col-sm-12 col-12 main-column position-relative '.($additional_information['included'] ? '' : 'disabled').'">
                    <div class="disabled-overlay"></div>
                    <h3 class="mb-4"><small>'.__('Información complementaria', 'xopifier').'</small></h3>
                    <div class="field mb-3">
                        <label class="form-label w-100" for="field-store-products-extra-featured">'.__('¿Quieres destacar algo más de tus productos?', 'xopifier').'</label>
                        <textarea class="form-control mt-2 '.($store_products_extra_featured != '' ? 'valid' : '').'" id="field-store-products-extra-featured" name="field-store-products-extra-featured" placeholder="">'.$store_products_extra_featured.'</textarea>
                    </div>
                    <div class="field upload mb-4">
                        <label class="form-label" for="field-store-animations">'.__('Imagen(es) / animación(es)', 'xopifier').'</label>
                        <div class="field-upload-animations">
                            <div class="field-upload-overlay"></div>
                            <div class="field-upload-field">
                                <input type="file" accept=".jpg,.png,.gif,.jpeg,.pdf,.mp4,.mov" multiple class="field-upload-input d-none" id="field-store-animations" name="field-store-animations[]">
                                <input type="hidden" id="field-store-animations-from-server" name="field-store-animations-from-server" value="'.($_files != '' ? '1' : '0').'" />
                                <img src="'.get_template_directory_uri().'/img/upload-to-cloud.svg" class="field-upload-img" />
                                <p class="btn-choose">'.__('Arrastra aquí los archivos o <span>selecciónalos de tu computadora</span>', 'xopifier').'</p>
                                <p><small>'.__('Puedes subir hasta 5 archivos .PNG, .PDF, .JPG, .GIF, .MP4, .MOV', 'xopifier').'</small></p>
                            </div>
                            <div class="field-upload-content">
                                <span class="image-preview-close" style="display:none;"><img src="'.get_template_directory_uri().'/img/close.svg" alt="close preview" /></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-sm-12 col-12 side-column mt-md-0 mt-sm-4 mt-4">
                    <div class="sticky-top">
                        <button type="button" class="btn btn-primary btn-toggle-section d-flex flex-row align-items-center justify-content-center gap-2 '.($additional_information['included'] ? '' : 'toggled').'">
                            
                            <svg class="'.($additional_information['included'] ? 'on' : 'off d-none').'" width="18px" height="18px" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9 0C4.02944 0 0 4.02944 0 9C0 13.9706 4.02944 18 9 18C13.9706 18 18 13.9706 18 9C18 4.02944 13.9706 0 9 0Z" fill="#00B231"/>
                                <path d="M14 8V10H4V8H14Z" fill="white"/>
                            </svg>

                            <svg class="'.($additional_information['included'] ? 'off d-none' : 'on').'" width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9 0C4.02944 0 0 4.02944 0 9C0 13.9706 4.02944 18 9 18C13.9706 18 18 13.9706 18 9C18 4.02944 13.9706 0 9 0Z" fill="#00B231"/>
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M10.1667 4H8.16667V8.16667H4V10.1667H8.16667V14H10.1667V10.1667H14V8.16667H10.1667V4Z" fill="white"/>
                            </svg>

                            <span class="include on '.($additional_information['included'] ? '' : 'd-none').'">'.__('No incluir esta sección', 'xopifier').'</span>
                            <span class="include off '.($additional_information['included'] ? 'd-none' : '').'">'.__('Volver a incluir esta sección', 'xopifier').'</span>

                        </button>
                        <div class="message-on" '.($additional_information['included'] ? '' : 'style="display: none;"').'>
                            <p class="small text-center mt-2 px-lg-5 px-md-3 px-sm-0 px-0">'.__('En caso que prefieras crearla después por tu cuenta en Shopify', 'xopifier').'</p>
                            <div class="form-tip">
                                <img src="'.get_template_directory_uri().'/img/info.svg'.'" class="form-tip-img" />
                                <p>'.__('Te recomendamos comunicar aquello que crees que hace especial tu oferta de productos, en general. Ej: fabricación, materiales, responsabilidad social, impacto ambiental u otros.', 'xopifier').'</p>
                            </div>
                        </div>
                        <div class="message-off" '.($additional_information['included'] ? 'style="display: none;"' : '').'>
                            <div class="form-tip off text-center">
                                <p class="text-center">'.__('Estás decidiendo no incluir esta información con Xopifier. Puedes crearla luego directamente en Shopify si así lo deseas.', 'xopifier').'</p>
                                <button type="button" class="btn btn-primary btn-continue">'.__('Ok, continuar', 'xopifier').'</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-8 col-sm-12 col-12 bottom-column" '.(!$additional_information['included'] ? 'style="display: none;"' : '').'>
                    <hr class="my-5">
                    <div class="d-flex flex-column align-items-center justify-content-center text-center">
                        <button type="submit" class="btn btn-primary btn-save-store-products-extra w-auto '.($additional_information['included'] ? '' : 'disabled').'">'.__('Guardar y continuar', 'xopifier').'</button>
                        <span class="msg mt-3" style="display:none;"></span>
                    </div>
                </div>
            </div>
        </form>
    ';
}

function step_3_tabs_products($design_id) {

    if(isset($design_id) && $design_id != 0 && $design_id != ''){
        $store = get_field('store', $design_id);
    }else{
        $userid = get_current_user_id();
        $store = get_posts(array(
            'post_type' => 'initial-stage',
            'post_status' => 'publish',
            'posts_per_page' => 1,
            'meta_query' => array(
                array(
                    'key' => 'user',
                    'value' => $userid,
                    'compare' => '='
                )
            )
        ));
        $store = $store[0];
    }
    $storename = get_field('current_store_name', $store->ID);

    $total_price = get_field('total_price', $store->ID);

    $categories = get_field('current_store_product_categories', $store->ID) ? get_field('current_store_product_categories', $store->ID) : [];
    $categories_html = '';
    if(is_array($categories) and count($categories) > 0) {
        foreach($categories as $k => $category) {
            $categories_html .= '
                <div class="repeater-field mb-1">
                    <div class="field">
                        <label class="form-label" for="field-store-category-'.($k + 1).'">'.__('Categoría', 'xopifier').' '.($k + 1).':</label>
                        <input type="text" class="form-control valid" id="field-store-category-'.($k + 1).'" name="field-store-category[]" value="'.$category['category'].'" placeholder=""/>
                        <span class="error"></span>
                    </div>
                </div>
            ';
        }
    }
    for($i = count($categories) + 1; $i < 5; $i++){
        $categories_html .= '
            <div class="repeater-field mb-1">
                <div class="field mb-2">
                    <label class="form-label" for="field-store-category-'.($i).'">'.__('Categoría', 'xopifier').' '.($i).':</label>
                    <input type="text" class="form-control" id="field-store-category-'.($i).'" name="field-store-category[]" placeholder=""/>
                    <span class="error"></span>
                </div>
            </div>
        ';
    }

    $products_total_qty = 0;

    $step3_status = get_step3_status($store->ID);

    $reference_products = get_field('current_store_popular_products', $store->ID);
    $reference_products_html = '';
    $reference_products_inputs = '';

    if(is_array($reference_products) and count($reference_products) > 0) {
        foreach($reference_products as $k => $reference_product) {

            $reference_products_inputs .= '<input type="text" class="LinkProductName item-link-'.($k).'" name="field-LinkProductName[]" value="'.$reference_product['product_name'].'" />';
		    $reference_products_inputs .= '<input type="text" class="LinkProductLink item-link-'.($k).'" name="field-LinkProductLink[]" value="'.$reference_product['product_link'].'" />';
            $reference_products_inputs .= '<input type="text" class="LinkProductCategory item-link-'.($k).'" name="field-LinkProductCategory[]" value="'.$reference_product['product_categories'].'" />';
            $reference_products_inputs .= '<input type="text" class="LinkProductFeatured item-link-'.($k).'" name="field-LinkProductFeatured[]" value="'.$reference_product['product_featured'].'" />';

            $reference_products_html .= '
                <li class="link-product d-flex align-items-center justify-content-between py-2 item-link-'.($k).'" index="item-link-'.($k).'">
                    <span class="counter me-3">'.($products_total_qty + 1).'</span>
                    <div class="d-flex align-items-center justify-content-start gap-3 w-75">
                        <img src="'.get_template_directory_uri().'/img/image-placeholder.png" class="product-thumb me-0 img-fluid" />
                        <div class="d-flex flex-column align-items-start justify-content-center">
                            <h3 class="d-flex"><a class="link-product-edit" href="javascript:void(0)">'.$reference_product['product_name'].'</a></h3>
                            <a href="'.(strpos($reference_product['product_link'], 'http') !== false ? $reference_product['product_link'] : 'http://'.$reference_product['product_link']).'" class="direct-link" target="_blank">'.$reference_product['product_link'].'</a>
                            '.($reference_product['product_categories'] != '' ? '<p class="product-categories mb-0">'.$reference_product['product_categories'].'</p>' : '').'
                        </div>
                    </div>
                    <a class="featured me-3 ms-auto" href="javascript:void(0)"><i class="star '.($reference_product['product_featured'] == 1 ? '' : 'off').'"></i></a>
                    <a class="trash me-0 ms-0" href="javascript:void(0)">
                        <span class="product-trash step3 img-fluid me-3">'.file_get_contents(get_template_directory_uri().'/img/trash.svg').'</span>
                    </a>
                </li>
            ';
            $products_total_qty++;
        }
    }

    $pc_products = get_field('current_store_pc_products', $store->ID);
    $pc_products_html = '';
    $pc_products_inputs = '';

    if(is_array($pc_products) and count($pc_products) > 0) {
        foreach($pc_products as $k => $pc_product) {


            if($pc_product['product_media'] !== false){
                foreach($pc_product['product_media'] as $media){
                    $pc_products_inputs .= '<input type="text" class="PCProductMediaDB item-pc-'.($k).'" name="field-PCProductMediaDB['.$k.'][]" value="'.$media['media']['ID'].'"/>';
                }
            }else{
                $pc_products_inputs .= '<input type="text" class="PCProductMediaDB item-pc-'.($k).'" name="field-PCProductMediaDB['.$k.'][]" value=""/>';
            }
            $pc_products_inputs .= '<input type="file" multiple class="PCProductMedia item-pc-'.($k).'" name="field-PCProductMedia['.$k.'][]"/>';
            $pc_products_inputs .= '<input type="text" class="PCProductName item-pc-'.($k).'" name="field-PCProductName[]" value="'.$pc_product['product_name'].'" />';
            $pc_products_inputs .= '<input type="text" class="PCProductCurrecy item-pc-'.($k).'" name="field-PCProductCurrecy[]" value="'.$pc_product['product_currency'].'" />';
            $pc_products_inputs .= '<input type="text" class="PCProductPrice item-pc-'.($k).'" name="field-PCProductPrice[]" value="'.$pc_product['product_price'].'" />';
            $pc_products_inputs .= '<input type="text" class="PCProductSalePrice item-pc-'.($k).'" name="field-PCProductSalePrice[]" value="'.$pc_product['product_saleprice'].'" />';
            $pc_products_inputs .= '<input type="text" class="PCProductDescription item-pc-'.($k).'" name="field-PCProductDescription[]" value="'.$pc_product['product_description'].'" />';
            $pc_products_inputs .= '<input type="text" class="PCProductCategory item-pc-'.($k).'" name="field-PCProductCategory[]" value="'.$pc_product['product_categories'].'" />';
            $pc_products_inputs .= '<input type="text" class="PCProductFeatured item-pc-'.($k).'" name="field-PCProductFeatured[]" value="'.$pc_product['product_featured'].'" />';
            $pc_products_inputs .= '<input type="text" class="PCProductVariationsComment item-pc-'.($k).'" name="field-PCProductVariationsComment[]" value="'.$pc_product['product_variations_comments'].'" />';
            $pc_products_inputs .= '<input type="text" class="PCProductVariations item-pc-'.($k).'" name="field-PCProductVariations[]" value="'.base64_encode(json_encode($pc_product['product_variations'])).'" />';

            if(is_array($pc_product['product_media']) and count($pc_product['product_media']) > 0){
                $images_qty = count($pc_product['product_media']);
                $product_medias = '';
                foreach($pc_product['product_media'] as $media){
                    $product_medias .= '
                        <div class="img-preview-container text-center">
                            <a href="'.$media['media']['url'].'" data-fancybox="image" target="_blank">
                                <img src="'.$media['media']['url'].'" class="img-preview" alt="">
                                <small>'.substr($media['media']['title'], 0, 12).'...</small>
                            </a>
                        </div>
                    ';
                    
                }
            }else{
                $product_medias = '';
                $images_qty = 0;
            }
            
            $pc_products_html .= '
                <li class="pc-product d-flex align-items-center justify-content-between py-2 item-pc-'.($k).'" index="item-pc-'.($k).'">
                    <span class="counter me-3">'.($products_total_qty + 1).'</span>
                    <div class="d-flex align-items-center justify-content-start gap-3 w-75">
                        <img src="'.($images_qty > 0 ? $pc_product['product_media'][0]['media']['url'] : get_template_directory_uri().'/img/image-placeholder.png').'" class="product-thumb me-0 img-fluid" />
                        <div class="d-flex flex-column align-items-start justify-content-center">
                            <h3 class="d-flex"><a class="pc-product-edit" href="javascript:void(0)">'.$pc_product['product_name'].'</a></h3>
                            <a class="product-more-images" href="javascript:void(0)">'.$images_qty.' '.($images_qty == 1 ? __('imágen', 'xopifier') : __('imágenes', 'xopifier')).'</a>
                            <div class="product-images product-images-'.($k).'" style="display: none;">
                                <span class="product-images-close"><img src="'.get_template_directory_uri().'/img/close-dark.svg" alt="close preview" /></span>
                                '.$product_medias.'
                            </div>
                            '.($pc_product['product_categories'] != '' ? '<p class="product-categories mb-0">'.$pc_product['product_categories'].'</p>' : '').'
                        </div>
                    </div>
                    <a class="featured me-3 ms-auto" href="javascript:void(0)"><i class="star '.($pc_product['product_featured'] == 1 ? '' : 'off').'"></i></a>
                    <a class="trash me-0 ms-0" href="javascript:void(0)">
                        <span class="product-trash step3 img-fluid me-3">'.file_get_contents(get_template_directory_uri().'/img/trash.svg').'</span>
                    </a>
                </li>
            ';
            $products_total_qty++;
        }
    }

    $blank_products_html = '';
    for($i = $products_total_qty + 1; $i <= 10; $i++) {
        $blank_products_html .= '
            <li class="product-placeholder d-flex align-items-center justify-content-start py-3">
                <span class="counter disabled me-3">'.($i).'</span>
                <div class="d-flex align-items-center justify-content-start gap-3 w-75">
                    <img src="'.get_template_directory_uri().'/img/image-placeholder.png" class="product-thumb me-0 img-fluid" />
                    <div class="d-flex flex-column align-items-start justify-content-center w-100">
                        <div class="gray-bar w-75"></div>
                        <div class="gray-bar w-50 dark-gray"></div>
                        <div class="gray-bar w-25"></div>
                    </div>
                </div>
                <a class="featured me-3 ms-auto disabled" disabled href="javascript:void(0)"><i class="star off disabled"></i></a>
                <a class="trash me-0 ms-0 disabled" disabled href="javascript:void(0)">
                    <span class="product-trash step3 img-fluid me-3 disabled" disabled>'.file_get_contents(get_template_directory_uri().'/img/trash.svg').'</span>
                </a>
            </li>
        ';
    }

    $service_settings = get_field('service_settings', 'option');

    return '
        <div class="products">
            <ul class="nav nav-tabs d-flex justify-content-md-between justify-content-sm-start justify-content-start" id="myTabProducts" role="tablist">
                <li class="nav-item" role="presentation" style="width: 32%;">
                    <button class="nav-link sub-item sub-tab active w-100 '.get_step3_tab_status($step3_status, 'products-categories-tab').'" id="products-categories-tab" data-bs-toggle="tab" data-bs-target="#products-categories" type="button" role="tab" aria-controls="products-categories" aria-selected="true">
                        '.__('Categorías', 'xopifier').'
                        <img src="'.get_template_directory_uri().'/img/done.svg" />
                    </button>
                </li>
                <li class="nav-item" role="presentation" style="width: 32%;">
                    <button class="nav-link sub-item sub-tab w-100 '.get_step3_tab_status($step3_status, 'products-products-tab').'" id="products-products-tab" data-bs-toggle="tab" data-bs-target="#products-products" type="button" role="tab" aria-controls="products-products" aria-selected="false">
                        '.__('Detalle de productos', 'xopifier').'
                        <img src="'.get_template_directory_uri().'/img/done.svg" />
                    </button>
                </li>
                <li class="nav-item" role="presentation" style="width: 32%;">
                    <button class="nav-link sub-item sub-tab w-100 '.get_step3_tab_status($step3_status, 'products-extra-info-tab').'" id="products-extra-info-tab" data-bs-toggle="tab" data-bs-target="#products-extra-info" type="button" role="tab" aria-controls="products-extra-info" aria-selected="false">
                        '.__('Información complementaria', 'xopifier').'
                        <img src="'.get_template_directory_uri().'/img/done.svg" />
                    </button>
                </li>
            </ul>
            <div class="tab-content" id="myTabContentProducts">
                <div class="tab-pane fade show active" id="products-categories" role="tabpanel" aria-labelledby="products-categories-tab">
                    <form id="store-products-categories-form" method="post" enctype="multipart/form-data">
                        <div class="form-loader" style="display: none;"></div>
                        <input type="hidden" name="action" value="ws" />
                        <input type="hidden" name="lang" value="'.ICL_LANGUAGE_CODE.'" />
                        <input type="hidden" name="wsa" value="save-store-products-categories" />
                        <input type="hidden" name="store_id" value="'.$store->ID.'" />
                        <div class="row">
                            <div class="pe-md-5 pe-sm-auto pe-auto col-md-8 col-sm-12 col-12">
                                <h3 class="mb-4"><small>'.__('Categorías ("Colecciones") de productos', 'xopifier').'</small></h3>
                                <div class="category-repeater-fields">
                                    '.$categories_html.'
                                </div>
                                <div class="d-flex align-items-center justify-content-start">
                                    <button class="w-auto btn btn-secondary btn-plus-categ">'.__('Agregar otra categoría', 'xopifier').'</button>
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-12 col-12">
                                <div class="form-tip sticky-top">
                                    <img src="'.get_template_directory_uri().'/img/info.svg'.'" class="form-tip-img" />
                                    <p>'.__('Las categorías o "Colecciones" (así se les llama en Shopify) son las grandes líneas o "familias" de productos para separar tu oferta según sus características.', 'xopifier').'</p>
                                </div>
                            </div>
                            <div class="col-md-8 col-sm-12 col-12 pe-md-5 pe-sm-auto p-auto">
                                <hr class="my-5">
                                <div class="d-flex flex-column align-items-center justify-content-center text-center">
                                    <button type="button" class="btn btn-primary btn-save-categories w-auto">'.__('Guardar y continuar', 'xopifier').'</button>
                                    <span class="msg mt-3" style="display:none;"></span>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="tab-pane fade" id="products-products" role="tabpanel" aria-labelledby="products-products-tab">

                    <form id="store-products-form" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="lang" value="'.ICL_LANGUAGE_CODE.'" />
                        <input type="hidden" name="action" value="ws" />
                        <input type="hidden" name="wsa" value="save-store-products" />
                        <input type="hidden" name="store_id" value="'.$store->ID.'" />
                        <input type="hidden" name="base_price" value="'.$service_settings['base_services_price'].'" />
                        <input type="hidden" name="aditional_product_price" value="'.$service_settings['base_service_aditional_products_price'].'" />
                        <input type="hidden" name="products_qty_included" value="'.$service_settings['base_service_products_qty_included'].'" />

                        <div class="form-loader" style="display: none;"></div>
                        
                        <div class="row products-tab-list" total-price="'.$total_price.'">
                            <div class="pe-md-5 pe-sm-auto pe-auto col-md-8 col-sm-12 col-12">

                                <div class="d-flex gap-2 align-items-center">
                                    <h3 class="mb-2"><small>'.__('Productos agregados:', 'xopifier').' <div class="d-inline-block total-products-qty">'.$products_total_qty.'</div></small></h3> <p class="small base-cost-included mb-0" '.($products_total_qty <= 10 ? '' : 'style="display:none;"').'>'.__('(incluidos en costo base)', 'xopifier').'</p>
                                </div>

                                <div class="products-list">
                                   
                                    <div class="d-none link-products">
                                        '.$reference_products_inputs.'
                                    </div>

                                    <div class="d-none pc-products">
                                        '.$pc_products_inputs.'
                                    </div>

                                    <ul class="products-list-container">
                                        '.$reference_products_html.'
                                        '.$pc_products_html.'
                                        '.$blank_products_html.'
                                    </ul>

                                    <div class="d-flex align-items-center justify-content-between text-center mt-4">
                                        <button type="button" class="mt-3 btn btn-secondary btn-plus-product w-auto">'.__('Agregar producto', 'xopifier').'</button>
                                        <button type="button" class="mt-3 btn btn-secondary btn-batch-import-products w-auto">'.__('Importar en bloque', 'xopifier').'</button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-12 col-12">
                                <div class="form-tip sticky-top">
                                    <img src="'.get_template_directory_uri().'/img/info.svg'.'" class="form-tip-img" />
                                    <p>'.__('Los productos que agregues se sumarán a los que ya creaste en el SmartQuiz inicial.', 'xopifier').'</p>
                                    <p>'.__('Recuerda que tu Tienda 1.0 incluye de 1 a 10 productos. Puedes incluir más por un costo extra que te iremos informando en la medida que agregues productos.', 'xopifier').'</p>
                                </div>
                            </div>
                            <div class="col-md-8 col-sm-12 col-12 pe-md-5 pe-sm-auto p-auto">
                                <hr class="my-5">
                                <div class="d-flex flex-column align-items-center justify-content-center text-center">
                                    <button type="submit" class="btn btn-primary btn-save-products w-auto">'.__('Guardar y continuar', 'xopifier').'</button>
                                    <span class="msg mt-3" style="display:none;"></span>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="row products-tab-new-product" store-id="'.$store->ID.'" style="display: none;">
                        <div class="pe-md-5 pe-sm-auto pe-auto col-md-8 col-sm-12 col-12">
                            <ul class="mt-4 nav nav-tabs d-flex gap-2 justify-content-between" id="myTabAddEditProduct" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active w-100" id="fromlink-tab" data-bs-toggle="tab" data-bs-target="#fromlink" type="button" role="tab" aria-controls="fromlink" aria-selected="true">'.__('Agregar desde enlace', 'xopifier').'</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link w-100" id="frompc-tab" data-bs-toggle="tab" data-bs-target="#frompc" type="button" role="tab" aria-controls="frompc" aria-selected="false">'.__('Agregar manualmente', 'xopifier').'</button>
                                </li>
                            </ul>
                            <div class="tab-content" id="myTabContentAddEditProduct">
                                <div class="tab-pane fade show active" id="fromlink" role="tabpanel" aria-labelledby="fromlink-tab">
                                    <div class="field mb-3">
                                        <label class="form-label" for="field-fromLinkProductName">'.__('Nombre del producto:', 'xopifier').'</label>
                                        <input type="text" class="form-control" id="field-fromLinkProductName" name="field-fromLinkProductName" placeholder="">
                                        <span class="error"></span>
                                    </div>
                                    <div class="field mb-3">
                                        <label class="form-label" for="field-fromLinkProductLink">'.__('Link al producto:', 'xopifier').'</label>
                                        <input type="text" class="form-control" id="field-fromLinkProductLink" name="field-fromLinkProductLink" placeholder="'.__('Ej: https://www.link.com', 'xopifier').'">
                                        <span class="error"></span>
                                    </div>
                                    <div class="field mb-3">
                                        <label class="form-label" for="field-fromLinkProductCategory">'.__('Categoría:', 'xopifier').'</label>
                                        <select class="form-select form-control" id="field-fromLinkProductCategory" name="field-fromLinkProductCategory">
                                        </select>
                                        <span class="error"></span>
                                    </div>
                                    <div class="d-flex gap-3 justify-content-center align-items-center flex-wrap mt-0">
                                        <button class="btn btn-secondary btn-cancel-link-product me-2" href="" type="button">'.__('Cancelar', 'xopifier').'</button>
                                        <button class="btn btn-primary btn-save-link-product d-none" href="" type="button">'.__('Guardar y volver a productos', 'xopifier').'</button>
                                        <button class="btn btn-primary btn-add-link-product disabled" href="" type="button">'.__('Guardar y volver a productos', 'xopifier').'</button>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="frompc" role="tabpanel" aria-labelledby="frompc-tab">
                                    <form id="frm-add-new-product" method="post" enctype="multipart/form-data">
                                        <input type="hidden" name="lang" value="'.ICL_LANGUAGE_CODE.'" />
                                        <div class="field upload mb-3 w-100">
                                            <label class="form-label" for="field-fromPCProductMedia">'.__('Imágenes o animaciones del producto:', 'xopifier').'</label>
                                            <a href="javascript:void(0)" class="d-none field-upload-new-media">'.__('Subir nuevas imágenes o animaciones del producto:', 'xopifier').'</a>
                                            <div class="field-upload-products">
                                                <div class="field-upload-overlay"></div>
                                                <div class="field-upload-field">
                                                    <input type="file" accept=".jpg,.png,.gif,.jpeg,.mp4,.pdf,.mov" multiple class="field-upload-input d-none" id="field-fromPCProductMedia" name="field-fromPCProductMedia[]">
                                                    <input type="hidden" id="field-fromPCProductMedia-from-server" name="field-fromPCProductMedia-from-server" value="0" />
                                                    <img src="'.get_template_directory_uri().'/img/upload-to-cloud.svg" class="field-upload-img" />
                                                    <p class="btn-choose">'.__('Arrastra aquí los archivos o <span>selecciónalos de tu computadora</span>', 'xopifier').'</p>
                                                    <p><small>'.__('Puedes subir hasta 5 archivos .PNG, .PDF, .JPG, .GIF, .MP4, .MOV', 'xopifier').'</small></p>
                                                </div>
                                                <div class="field-upload-content">
                                                    <span class="image-preview-close" style="display: none;"><img src="'.get_template_directory_uri().'/img/close.svg" alt="close preview" /></span>
                                                </div>
                                            </div>
                                            <div class="field-preview-media" style="display: none;"></div>
                                        </div>
                                        <div class="field mb-3 pt-3">
                                            <label class="form-label" for="field-fromPCProductName">'.__('Nombre del producto:', 'xopifier').'</label>
                                            <input type="text" class="form-control" id="field-fromPCProductName" name="field-fromPCProductName" placeholder="">
                                            <span class="error"></span>
                                        </div>
                                        <div class="d-flex gap-2 price-field-container flex-wrap">
                                            <div class="field currency mb-3">
                                                <label class="form-label" for="field-fromPCProductPrice">'.__('Precio:', 'xopifier').'</label>
                                                <select class="form-select form-control currency" id="field-fromPCProductCurrecy" name="field-fromPCProductCurrecy">
                                                    <option value="CLP">CLP</option>
                                                    <option value="MXN">MXN</option>
                                                    <option value="USD" selected>USD</option>
                                                    <option value="EUR">EUR</option>
                                                </select>
                                                <span class="error"></span>
                                            </div>
                                            <div class="field price mb-3">
                                                <label class="form-label" for="field-fromPCProductPrice">&nbsp;</label>
                                                <input type="text" class="form-control" id="field-fromPCProductPrice" name="field-fromPCProductPrice" placeholder="$">
                                                <span class="error"></span>
                                            </div>
                                            <div class="field sale-price mb-3 ms-md-3 ms-sm-0 ms-0">
                                                <div class="d-flex flex-row mb-1 justify-content-start align-items-center">
                                                    <label class="form-label mb-0 me-2" for="field-fromPCProductSalePrice">'.__('Precio referencial:', 'xopifier').'</label> 
                                                    <img src="'.get_template_directory_uri().'/img/help-with-circle.svg'.'" data-bs-toggle="tooltip" data-bs-html="true" data-bs-title="'.__('Opcional. Para mostrar un precio rebajado, esta es una referencia al precio original del producto. Ej: $9.99', 'xopifier').' '.htmlentities('<span style="text-decoration: line-through;">$12.99</span>').'" />
                                                </div>
                                                <input type="text" class="form-control" id="field-fromPCProductSalePrice" name="field-fromPCProductSalePrice" placeholder="$">
                                                <span class="error"></span>
                                            </div>
                                        </div>
                                        <div class="field mb-3">
                                            <label class="form-label" for="field-fromPCProductCategory">'.__('Categoría:', 'xopifier').'</label>
                                            <select class="form-select form-control" id="field-fromPCProductCategory" name="field-fromPCProductCategory">
                                            </select>
                                            <span class="error"></span>
                                        </div>
                                        <div class="field mb-3">
                                            <label class="form-label" for="field-fromPCProductDescription">'.__('Descripción:', 'xopifier').'</label>
                                            <textarea class="form-control" id="field-fromPCProductDescription" name="field-fromPCProductDescription" placeholder="'.__('Ej: Este plato está hecho de arcilla. Soporta altas temperaturas directamente, etc.', 'xopifier').'"></textarea>
                                            <span class="error"></span>
                                        </div>
                                        <hr>
                                        <label class="form-label">'.__('¿Este producto tiene variaciones u opciones?', 'xopifier').'</label>
                                        <div class="form-check">
                                            <input type="checkbox" id="field-fromPCProductVariable" name="field-fromPCProductVariable" class="form-check-input field-fromPCProductVariable" value="1">
                                            <label class="form-check-label" for="field-fromPCProductVariable">
                                                '.__('Sí tiene variaciones u opciones', 'xopifier').'
                                            </label>
                                        </div>
                                        <p class="small ms-4">'.__('Ej: Colores, dimensiones, peso, versiones, etc.', 'xopifier').'</p>
                                        <div class="product-variations" style="display: none;">
                                            <label class="form-label">'.__('Seleciona la(s) variación(es) a agregar', 'xopifier').'</label>
                                            <div class="product-variations-container ps-3 mb-4">
                                                <div class="form-check color-variation">
                                                    <input type="checkbox" id="field-fromPCProductVariationsColor" name="field-fromPCProductVariationsColor" class="form-check-input field-fromPCProductVariationsColor" value="'.__('Color', 'xopifier').'">
                                                    <label class="form-check-label" for="field-fromPCProductVariationsColor">
                                                        '.__('Color', 'xopifier').'
                                                    </label>
                                                </div>
                                                <div class="field mt-2 mb-0 ps-4 color-variation-desc" style="display: none;">
                                                    <label class="form-label small" for="field-fromPCProductVariationsColorDesc">'.__('Escribe los colores (texto, códigos o como te resulte más fácil) del producto', 'xopifier').'</label>
                                                    <p class="small">'.__('Si el color afecta el precio u otro comportamiento, explícalo también aquí', 'xopifier').'</p>
                                                    <textarea class="form-control" id="field-fromPCProductVariationsColorDesc" name="field-fromPCProductVariationsColorDesc" placeholder="'.__('Ej: Rojo, amarillo y blanco. El rojo tiene un valor mas elevado, de $55.', 'xopifier').'"></textarea>
                                                    <span class="error"></span>
                                                </div>
                                                <div class="form-check size-variation">
                                                    <input type="checkbox" id="field-fromPCProductVariationsSize" name="field-fromPCProductVariationsSize" class="form-check-input field-fromPCProductVariationsSize" value="'.__('Talla', 'xopifier').'">
                                                    <label class="form-check-label" for="field-fromPCProductVariationsSize">
                                                        '.__('Tama&ntilde;o / talla', 'xopifier').'
                                                    </label>
                                                </div>
                                                <div class="field mt-2 mb-0 ps-4 size-variation-desc" style="display: none;">
                                                    <label class="form-label small" for="field-fromPCProductVariationsSizeDesc">'.__('Escribe los tamaños o tallas del producto', 'xopifier').'</label>
                                                    <p class="small">'.__('Si el tamaño/talla afecta el precio u otro comportamiento, explícalo también aquí', 'xopifier').'</p>
                                                    <textarea class="form-control" id="field-fromPCProductVariationsSizeDesc" name="field-fromPCProductVariationsSizeDesc" placeholder="'.__('Ej: S, M, L, XL, XXL', 'xopifier').'"></textarea>
                                                    <span class="error"></span>
                                                </div>
                                                <div class="other-variation">
                                                    <div class="variation variation-index-1">
                                                        <div class="form-check">
                                                            <input type="checkbox" id="field-fromPCProductVariationsOther-index-1" name="field-fromPCProductVariationsOther[]" class="form-check-input field-fromPCProductVariationsOther" value="'.__('Otra variación', 'xopifier').'">
                                                            <label class="form-check-label" for="field-fromPCProductVariationsOther-index-1">
                                                                '.__('Otra variación', 'xopifier').' <index></index>
                                                            </label>
                                                        </div>
                                                        <div class="other-variation-desc-container-index-1" style="display: none;">
                                                            <div class="field mt-2 mb-0 ps-4">
                                                                <label class="form-label small" for="field-fromPCProductVariationsOtherName-index-1">'.__('Nombre de la variación:', 'xopifier').'</label>
                                                                <input type="text" class="form-control variation-name" id="field-fromPCProductVariationsOtherName-index-1" name="field-fromPCProductVariationsOtherName-1" placeholder="Ej: Edad">
                                                                <span class="error"></span>
                                                            </div>
                                                            <div class="field mt-0 mb-0 ps-4">
                                                                <label class="form-label small" for="field-fromPCProductVariationsOtherDesc-index-1">'.__('Describe sus componentes y variaciones:', 'xopifier').'</label>
                                                                <textarea class="form-control variation-value" id="field-fromPCProductVariationsOtherDesc-index-1" name="field-fromPCProductVariationsOtherDesc-1" placeholder="'.__('Ej: Bebés, niños, adolescentes, adultos', 'xopifier').'"></textarea>
                                                                <span class="error"></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="field mb-3">
                                                <label class="form-label" for="field-fromPCProductVariationsComment">'.__('¿Algo más que desees comentar sobre este producto y sus variaciones?', 'xopifier').' <span>('.__('Opcional', 'xopifier').')</span></label>
                                                <textarea class="form-control" id="field-fromPCProductVariationsComment" name="field-fromPCProductVariationsComment" placeholder="'.__('Ej: Para los colores, considerar que el blanco tiene un costo mayor, debido al material.', 'xopifier').'"></textarea>
                                                <span class="error"></span>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="d-flex gap-3 justify-content-center align-items-center flex-wrap mt-0">
                                            <button class="btn btn-secondary btn-cancel-pc-product me-2" href="" type="button">'.__('Cancelar', 'xopifier').'</button>
                                            <button class="btn btn-primary btn-save-pc-product d-none" href="" type="button">'.__('Guardar y volver a productos', 'xopifier').'</button>
                                            <button class="btn btn-primary btn-add-pc-product disabled" href="" type="button">'.__('Guardar y volver a productos', 'xopifier').'</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-12 col-12">
                            <div class="form-tip sticky-top">
                                <img src="'.get_template_directory_uri().'/img/info.svg'.'" class="form-tip-img" />
                                <p>'.__('Si ya vendes en línea y tu producto tiene url, elige <strong>"Desde referencia (url)"</strong>. Usaremos la información y fotos que allí existan.', 'xopifier').'</p>
                                <p>'.__('Si aún no vendes en línea o prefieres subir la información desde cero, usa <strong>"Desde mi computadora / Dispositivo"</strong>.', 'xopifier').'</p>
                            </div>
                        </div>
                        <div class="d-none col-md-8 col-sm-12 col-12 pe-md-5 pe-sm-auto p-auto">
                            <hr class="my-5">
                            <div class="d-flex align-items-center justify-content-center text-center">
                                <button type="button" class="btn btn-primary btn-save-products w-auto">'.__('Guardar y continuar', 'xopifier').'</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="products-extra-info" role="tabpanel" aria-labelledby="products-extra-info-tab">
                    '.step_3_tabs_products_extra($design_id).'
                </div>
            </div>
        </div>
    ';
}

function step_3_list_products($design_id) {
    // var_dump(ICL_LANGUAGE_CODE);
    if(isset($design_id) && $design_id != 0 && $design_id != ''){
        $store = get_field('store', $design_id);
    }else{
        $userid = get_current_user_id();
        $store = get_posts(array(
            'post_type' => 'initial-stage',
            'post_status' => 'publish',
            'posts_per_page' => 1,
            'meta_query' => array(
                array(
                    'key' => 'user',
                    'value' => $userid,
                    'compare' => '='
                )
            )
        ));
        $store = $store[0];
    }

    $total_price = get_field('total_price', $store->ID);
    $products_total_qty = 0;
    $reference_products = get_field('current_store_popular_products', $store->ID);
    $reference_products_html = '';
    $reference_products_inputs = '';

    if(is_array($reference_products) and count($reference_products) > 0) {
        foreach($reference_products as $k => $reference_product) {

            $reference_products_inputs .= '<input type="text" class="LinkProductName item-link-'.($k).'" name="field-LinkProductName[]" value="'.$reference_product['product_name'].'" />';
		    $reference_products_inputs .= '<input type="text" class="LinkProductLink item-link-'.($k).'" name="field-LinkProductLink[]" value="'.$reference_product['product_link'].'" />';
            $reference_products_inputs .= '<input type="text" class="LinkProductCategory item-link-'.($k).'" name="field-LinkProductCategory[]" value="'.$reference_product['product_categories'].'" />';
            $reference_products_inputs .= '<input type="text" class="LinkProductFeatured item-link-'.($k).'" name="field-LinkProductFeatured[]" value="'.$reference_product['product_featured'].'" />';

            $reference_products_html .= '
                <li class="link-product d-flex align-items-center justify-content-between py-2 item-link-'.($k).'" index="item-link-'.($k).'">
                    <span class="counter me-3">'.($products_total_qty + 1).'</span>
                    <div class="d-flex align-items-center justify-content-start gap-3 w-75">
                        <img src="'.get_template_directory_uri().'/img/image-placeholder.png" class="product-thumb me-0 img-fluid" />
                        <div class="d-flex flex-column align-items-start justify-content-center">
                            <h3 class="d-flex"><a class="link-product-edit" href="javascript:void(0)">'.$reference_product['product_name'].'</a></h3>
                            <a href="'.(strpos($reference_product['product_link'], 'http') !== false ? $reference_product['product_link'] : 'http://'.$reference_product['product_link']).'" class="direct-link" target="_blank">'.$reference_product['product_link'].'</a>
                            '.($reference_product['product_categories'] != '' ? '<p class="product-categories mb-0">'.$reference_product['product_categories'].'</p>' : '').'
                        </div>
                    </div>
                    <a class="featured me-3 ms-auto" href="javascript:void(0)"><i class="star '.($reference_product['product_featured'] == 1 ? '' : 'off').'"></i></a>
                    <a class="trash me-0 ms-0" href="javascript:void(0)">
                        <span class="product-trash step3 img-fluid me-3">'.file_get_contents(get_template_directory_uri().'/img/trash.svg').'</span>
                    </a>
                </li>
            ';
            $products_total_qty++;
        }
    }

    $pc_products = get_field('current_store_pc_products', $store->ID);
    $pc_products_html = '';
    $pc_products_inputs = '';

    if(is_array($pc_products) and count($pc_products) > 0) {
        foreach($pc_products as $k => $pc_product) {


            if($pc_product['product_media'] !== false){
                foreach($pc_product['product_media'] as $media){
                    $pc_products_inputs .= '<input type="text" class="PCProductMediaDB item-pc-'.($k).'" name="field-PCProductMediaDB['.$k.'][]" value="'.$media['media']['ID'].'"/>';
                }
            }else{
                $pc_products_inputs .= '<input type="text" class="PCProductMediaDB item-pc-'.($k).'" name="field-PCProductMediaDB['.$k.'][]" value=""/>';
            }
            $pc_products_inputs .= '<input type="file" multiple class="PCProductMedia item-pc-'.($k).'" name="field-PCProductMedia['.$k.'][]"/>';
            $pc_products_inputs .= '<input type="text" class="PCProductName item-pc-'.($k).'" name="field-PCProductName[]" value="'.$pc_product['product_name'].'" />';
            $pc_products_inputs .= '<input type="text" class="PCProductCurrecy item-pc-'.($k).'" name="field-PCProductCurrecy[]" value="'.$pc_product['product_currency'].'" />';
            $pc_products_inputs .= '<input type="text" class="PCProductPrice item-pc-'.($k).'" name="field-PCProductPrice[]" value="'.$pc_product['product_price'].'" />';
            $pc_products_inputs .= '<input type="text" class="PCProductSalePrice item-pc-'.($k).'" name="field-PCProductSalePrice[]" value="'.$pc_product['product_saleprice'].'" />';
            $pc_products_inputs .= '<input type="text" class="PCProductDescription item-pc-'.($k).'" name="field-PCProductDescription[]" value="'.$pc_product['product_description'].'" />';
            $pc_products_inputs .= '<input type="text" class="PCProductCategory item-pc-'.($k).'" name="field-PCProductCategory[]" value="'.$pc_product['product_categories'].'" />';
            $pc_products_inputs .= '<input type="text" class="PCProductFeatured item-pc-'.($k).'" name="field-PCProductFeatured[]" value="'.$pc_product['product_featured'].'" />';
            $pc_products_inputs .= '<input type="text" class="PCProductVariationsComment item-pc-'.($k).'" name="field-PCProductVariationsComment[]" value="'.$pc_product['product_variations_comments'].'" />';
            $pc_products_inputs .= '<input type="text" class="PCProductVariations item-pc-'.($k).'" name="field-PCProductVariations[]" value="'.base64_encode(json_encode($pc_product['product_variations'])).'" />';

            if(is_array($pc_product['product_media']) and count($pc_product['product_media']) > 0){
                $images_qty = count($pc_product['product_media']);
                $product_medias = '';
                foreach($pc_product['product_media'] as $media){
                    $product_medias .= '
                        <div class="img-preview-container text-center">
                            <a href="'.$media['media']['url'].'" data-fancybox="image" target="_blank">
                                <img src="'.$media['media']['url'].'" class="img-preview" alt="">
                                <small>'.substr($media['media']['title'], 0, 12).'...</small>
                            </a>
                        </div>
                    ';
                    
                }
            }else{
                $product_medias = '';
                $images_qty = 0;
            }
            
            $pc_products_html .= '
                <li class="pc-product d-flex align-items-center justify-content-between py-2 item-pc-'.($k).'" index="item-pc-'.($k).'">
                    <span class="counter me-3">'.($products_total_qty + 1).'</span>
                    <div class="d-flex align-items-center justify-content-start gap-3 w-75">
                        <img src="'.($images_qty > 0 ? $pc_product['product_media'][0]['media']['url'] : get_template_directory_uri().'/img/image-placeholder.png').'" class="product-thumb me-0 img-fluid" />
                        <div class="d-flex flex-column align-items-start justify-content-center">
                            <h3 class="d-flex"><a class="pc-product-edit" href="javascript:void(0)">'.$pc_product['product_name'].'</a></h3>
                            <a class="product-more-images" href="javascript:void(0)">'.$images_qty.' '.($images_qty == 1 ? __('imágen', 'xopifier') : __('imágenes', 'xopifier')).'</a>
                            <div class="product-images product-images-'.($k).'" style="display: none;">
                                <span class="product-images-close"><img src="'.get_template_directory_uri().'/img/close-dark.svg" alt="close preview" /></span>
                                '.$product_medias.'
                            </div>
                            '.($pc_product['product_categories'] != '' ? '<p class="product-categories mb-0">'.$pc_product['product_categories'].'</p>' : '').'
                        </div>
                    </div>
                    <a class="featured me-3 ms-auto" href="javascript:void(0)"><i class="star '.($pc_product['product_featured'] == 1 ? '' : 'off').'"></i></a>
                    <a class="trash me-0 ms-0" href="javascript:void(0)">
                        <span class="product-trash step3 img-fluid me-3">'.file_get_contents(get_template_directory_uri().'/img/trash.svg').'</span>
                    </a>
                </li>
            ';
            $products_total_qty++;
        }
    }

    $blank_products_html = '';
    for($i = $products_total_qty + 1; $i <= 10; $i++) {
        $blank_products_html .= '
            <li class="product-placeholder d-flex align-items-center justify-content-start py-3">
                <span class="counter disabled me-3">'.($i).'</span>
                <div class="d-flex align-items-center justify-content-start gap-3 w-75">
                    <img src="'.get_template_directory_uri().'/img/image-placeholder.png" class="product-thumb me-0 img-fluid" />
                    <div class="d-flex flex-column align-items-start justify-content-center w-100">
                        <div class="gray-bar w-75"></div>
                        <div class="gray-bar w-50 dark-gray"></div>
                        <div class="gray-bar w-25"></div>
                    </div>
                </div>
                <a class="featured me-3 ms-auto disabled" disabled href="javascript:void(0)"><i class="star off disabled"></i></a>
                <a class="trash me-0 ms-0 disabled" disabled href="javascript:void(0)">
                    <span class="product-trash step3 img-fluid me-3 disabled" disabled>'.file_get_contents(get_template_directory_uri().'/img/trash.svg').'</span>
                </a>
            </li>
        ';
    }

    $service_settings = get_field('service_settings', 'option');

    return '
        <input type="hidden" name="action" value="ws" />
        <input type="hidden" name="wsa" value="save-store-products" />
        <input type="hidden" name="lang" value="'.ICL_LANGUAGE_CODE.'" />
        <input type="hidden" name="store_id" value="'.$store->ID.'" />
        <input type="hidden" name="base_price" value="'.$service_settings['base_services_price'].'" />
        <input type="hidden" name="aditional_product_price" value="'.$service_settings['base_service_aditional_products_price'].'" />
        <input type="hidden" name="products_qty_included" value="'.$service_settings['base_service_products_qty_included'].'" />

        <div class="form-loader" style="display: none;"></div>
        
        <div class="row products-tab-list" total-price="'.$total_price.'">
            <div class="pe-md-5 pe-sm-auto pe-auto col-md-8 col-sm-12 col-12">

                <div class="d-flex gap-2 align-items-center">
                    <h3 class="mb-2"><small>'.__('Productos agregados:', 'xopifier').' <div class="d-inline-block total-products-qty">'.$products_total_qty.'</div></small></h3> <p class="small base-cost-included mb-0" '.($products_total_qty <= 10 ? '' : 'style="display:none;"').'>'.__('(incluidos en costo base)', 'xopifier').'</p>
                </div>

                <div class="products-list">
                    
                    <div class="d-none link-products">
                        '.$reference_products_inputs.'
                    </div>

                    <div class="d-none pc-products">
                        '.$pc_products_inputs.'
                    </div>

                    <ul class="products-list-container">
                        '.$reference_products_html.'
                        '.$pc_products_html.'
                        '.$blank_products_html.'
                    </ul>

                    <div class="d-flex align-items-center justify-content-between text-center mt-4">
                        <button type="button" class="mt-3 btn btn-secondary btn-plus-product w-auto">'.__('Agregar producto', 'xopifier').'</button>
                        <button type="button" class="mt-3 btn btn-secondary btn-batch-import-products w-auto">'.__('Importar en bloque', 'xopifier').'</button>
                    </div>
                </div>

            </div>
            <div class="col-md-4 col-sm-12 col-12">
                <div class="form-tip sticky-top">
                    <img src="'.get_template_directory_uri().'/img/info.svg'.'" class="form-tip-img" />
                    <p>'.__('Los productos que agregues se sumarán a los que ya creaste en el SmartQuiz inicial.', 'xopifier').'</p>
                    <p>'.__('Recuerda que tu Tienda 1.0 incluye de 1 a 10 productos. Puedes incluir más por un costo extra que te iremos informando en la medida que agregues productos.', 'xopifier').'</p>
                </div>
            </div>
            <div class="col-md-8 col-sm-12 col-12 pe-md-5 pe-sm-auto p-auto">
                <hr class="my-5">
                <div class="d-flex flex-column align-items-center justify-content-center text-center">
                    <button type="submit" class="btn btn-primary btn-save-products w-auto">'.__('Guardar y continuar', 'xopifier').'</button>
                    <span class="msg mt-3" style="display:none;"></span>
                </div>
            </div>
        </div>
    ';
}
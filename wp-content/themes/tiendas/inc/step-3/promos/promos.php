<?php
function step_3_tabs_promos($design_id) {

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

    $step3_status = get_step3_status($store->ID);
    // var_dump($step3_status);

    return '
        <div class="promos">
            <ul class="nav nav-tabs d-flex justify-content-md-between justify-content-sm-start justify-content-start" id="myTabPromos" role="tablist">
                <li class="nav-item" style="width: 49.5%" role="presentation">
                    <button class="nav-link sub-item sub-tab active w-100 '.get_step3_tab_status($step3_status, 'promos-ads-tab').'" id="promos-ads-tab" data-bs-toggle="tab" data-bs-target="#promos-ads" type="button" role="tab" aria-controls="promos-ads" aria-selected="true">
                        '.__('Barra de anuncios', 'xopifier').'
                        <img src="'.get_template_directory_uri().'/img/done.svg" />
                    </button>
                </li>
                <li class="nav-item" style="width: 49.5%" role="presentation">
                    <button class="nav-link sub-item sub-tab w-100 '.get_step3_tab_status($step3_status, 'promos-discount-tab').'" id="promos-discount-tab" data-bs-toggle="tab" data-bs-target="#promos-discount" type="button" role="tab" aria-controls="promos-discount" aria-selected="false">
                        '.__('Suscriptor de E-mail', 'xopifier').'
                        <img src="'.get_template_directory_uri().'/img/done.svg" />
                    </button>
                </li>
            </ul>
            <div class="tab-content" id="myTabContentInfo">
                <div class="tab-pane fade show active" id="promos-ads" role="tabpanel" aria-labelledby="promos-ads-tab">
                    '.step_3_tabs_promos_ads($design_id).'
                </div>
                <div class="tab-pane fade" id="promos-discount" role="tabpanel" aria-labelledby="promos-discount-tab">
                    '.step_3_tabs_promos_subscriber($design_id).'
                </div>
            </div>
        </div>
    ';
}

function step_3_tabs_promos_ads($design_id){
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

    $exists = get_posts(array('post_type' => 'store-promo-data', 'meta_key' => 'store', 'meta_value' => $store->ID));
    $is_active = false;
    if(is_array($exists) && count($exists) > 0){
        $store_data_id = $exists[0]->ID;

        $ad = get_field('store_ad', $store_data_id);
        $ad_indications = get_field('store_ad_indications', $store_data_id);
        if($ad != '' && $ad_indications != ''){
            $is_active = true;
        }
    }else{
        $ad = '';
        $ad_indications = '';
    }
    
    return '
        <form id="store-promos-ads-data" method="post" enctype="multipart/form-data">
            <div class="form-loader" style="display: none;"></div>
            <input type="hidden" name="action" value="ws" />
            <input type="hidden" name="lang" value="'.ICL_LANGUAGE_CODE.'" />
            <input type="hidden" name="disable" value="false" />
            <input type="hidden" name="wsa" value="save-store-promos-ads-data" />
            <input type="hidden" name="store_id" value="'.$store->ID.'" />
            <input type="hidden" name="nonce" value="'.wp_create_nonce(xopifier_TITLE_FOR_NONCE).'" />
            <div class="row">
                <div class="pe-md-5 pe-sm-auto pe-auto col-md-8 col-sm-12 col-12 main-column position-relative '.($is_active ? '' : 'disabled').'">
                    <div class="disabled-overlay"></div>
                    <h3 class="mb-4"><small>'.__('Barra de anuncios', 'xopifier').'</small></h3>
                    <div class="field mb-3">
                        <div class="field mb-2 position-relative">
                            <label class="form-label small" for="field-store-promos-ad">'.__('Indica qué anuncio o promoción quieres incluir en la barra de anuncios:', 'xopifier').'</label>
                            <textarea class="form-control" style="height: 75px!important;" maxlength="80" id="field-store-promos-ad" name="field-store-promos-ad" placeholder="'.__('Ej: despacho gratis en compras sobre U$ 100.00', 'xopifier').'">'.$ad.'</textarea>
                            <span class="error"></span>
                            <div class="word-counter"><span>0</span> '.__('de 80 caracteres', 'xopifier').'</div>
                        </div>
                    </div>
                    <div class="field mb-3">
                        <label class="form-label small" for="field-store-promos-indications">'.__('Si el anuncio o promoción debe llevar un link, indícanos qué palabra y a hacia qué página o sección debe llevar el link: ', 'xopifier').'</label>
                        <textarea class="form-control" style="height: 150px!important;" id="field-store-promos-indications" name="field-store-promos-indications" placeholder="'.__('Ej: desde &laquo;gratis&raquo; y debe llevar a la sección &laquo;promoción de temporada&raquo;', 'xopifier').'">'.$ad_indications.'</textarea>
                        <span class="error"></span>
                    </div>
                </div>
                <div class="col-md-4 col-sm-12 col-12 side-column mt-md-0 mt-sm-4 mt-4">
                    <div class="sticky-top">
                        <button type="button" class="btn btn-primary btn-toggle-promos-ads-section d-flex flex-row align-items-center justify-content-center gap-2 '.($is_active ? 'is_active' : 'is_inactive').'">
                            
                            <svg class="remove '.($is_active ? '' : 'd-none').'" width="18px" height="18px" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9 0C4.02944 0 0 4.02944 0 9C0 13.9706 4.02944 18 9 18C13.9706 18 18 13.9706 18 9C18 4.02944 13.9706 0 9 0Z" fill="#00B231"/>
                                <path d="M14 8V10H4V8H14Z" fill="white"/>
                            </svg>

                            <svg class="add '.($is_active ? 'd-none' : '').'" width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9 0C4.02944 0 0 4.02944 0 9C0 13.9706 4.02944 18 9 18C13.9706 18 18 13.9706 18 9C18 4.02944 13.9706 0 9 0Z" fill="#00B231"/>
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M10.1667 4H8.16667V8.16667H4V10.1667H8.16667V14H10.1667V10.1667H14V8.16667H10.1667V4Z" fill="white"/>
                            </svg>

                            <span class="exclude '.($is_active ? '' : 'd-none').'">'.__('No incluir Barra de anuncios', 'xopifier').'</span>
                            <span class="include '.($is_active ? 'd-none' : '').'">'.__('Agregar Barra de anuncios', 'xopifier').'</span>

                        </button>

                        <div class="message-on" '.($is_active ? '' : 'style="display: none;"').'>
                            <p class="small text-center mt-2 px-lg-5 px-md-3 px-sm-0 px-0">'.__('En caso que prefieras crearla después por tu cuenta en Shopify', 'xopifier').'</p>
                            <div class="form-tip">
                                <img src="'.get_template_directory_uri().'/img/info.svg'.'" class="form-tip-img" />
                                <p class="form-label small mb-1">'.__('¿Cómo funciona la Barra de Anuncios?', 'xopifier').'</p>
                                <p>'.__('Es un mensaje en una franja anclada al extremo superior de todas las pantallas de tu tienda. Funciona muy bien para promocionar algo importante, como un aviso de temporada.', 'xopifier').'</p>
                                <div class="d-flex justify-content-end align-items-center"><a href="#howto" modal-width="90%" message-position="centertop" message="'.__('La barra de anuncios te permite anclar un mensaje puntual importante (ej: una oferta) para que esté en todas las páginas de tu tienda.', 'xopifier').'" image-content="'.get_template_directory_uri().'/img/barra-anuncios.jpg" class="d-flex justify-content-end align-items-center gap-2">'.__('Ver ejemplo', 'xopifier').' <img src="'.get_template_directory_uri().'/img/open.svg" /></a></div>
                            </div>
                        </div>
                        <div class="message-off" '.($is_active ? 'style="display: none;"' : '').'>
                            <div class="form-tip off text-center">
                                <p class="text-center">'.__('Estás decidiendo no incluir esta información con Xopifier. Puedes crearla luego directamente en Shopify si así lo deseas.', 'xopifier').'</p>
                                <button type="button" class="btn btn-primary btn-continue">'.__('Ok, continuar', 'xopifier').'</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 bottom-column" '.($is_active ? '' : 'style="display:none;"').'>
                    <hr class="my-5">
                    <div class="d-flex flex-column align-items-center justify-content-center text-center">
                        <button type="submit" class="btn btn-primary btn-save-store-promos-ads w-auto '.($is_active ? '' : 'disabled').'">'.__('Guardar y continuar', 'xopifier').'</button>
                        <span class="msg mt-3" style="display:none;"></span>
                    </div>
                </div>
            </div>
        </form>
    ';
}

function step_3_tabs_promos_subscriber($design_id){
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

    $exists = get_posts(array('post_type' => 'store-promo-data', 'meta_key' => 'store', 'meta_value' => $store->ID));
    $is_active = false;
    if(is_array($exists) && count($exists) > 0){
        $store_data_id = $exists[0]->ID;

        $discount = get_field('store_discount', $store_data_id);
        $discount_indications = get_field('store_discount_indications', $store_data_id);
        if($discount != '' && $discount_indications != ''){
            $is_active = true;
        }
    }else{
        $discount = '';
        $discount_indications = '';
    }
    
    return '
        <form id="store-promos-discount-data" method="post" enctype="multipart/form-data">
            <div class="form-loader" style="display: none;"></div>
            <input type="hidden" name="action" value="ws" />
            <input type="hidden" name="disable" value="false" />
            <input type="hidden" name="lang" value="'.ICL_LANGUAGE_CODE.'" />
            <input type="hidden" name="wsa" value="save-store-promos-discount-data" />
            <input type="hidden" name="store_id" value="'.$store->ID.'" />
            <input type="hidden" name="nonce" value="'.wp_create_nonce(xopifier_TITLE_FOR_NONCE).'" />
            <div class="row">
                <div class="pe-md-5 pe-sm-auto pe-auto col-md-8 col-sm-12 col-12 main-column position-relative '.($is_active ? '' : 'disabled').'">
                    <div class="disabled-overlay"></div>
                    <h3 class="mb-4"><small>'.__('Suscriptor de E-mail', 'xopifier').'</small></h3>
                    <div class="field mb-3">
                        <label class="form-label small d-block" for="field-store-promos-discount">'.__('Ofrece un descuento y captura el E-mail de prospectos, para que puedas venderles más a futuro:', 'xopifier').'</label>
                        '.__('Daré el', 'xopifier').' <input type="number" class="d-inline-block form-control mx-2 ps-2 pe-1 py-2" maxlength="3" max="100" min="0" style="height: 40px;width: 60px!important;" id="field-store-promos-discount" value="'.$discount.'" name="field-store-promos-discount" /> <h3 class="small d-inline-block mb-0 position-relative" style="bottom: -2px;">%</h3> '.__('de descuento a cambio de que el prospecto comparta su E-Mail.', 'xopifier').'
                        <span class="error d-block"></span>
                    </div>
                    <div class="field mb-3">
                        <label class="form-label small mb-0" for="field-store-promos-discount-indications">'.__('¿Deseas hacer alguna precisión o comentario sobre el alcance de tu promoción?', 'xopifier').'</label>
                        <p class="small mb-1">'.__('Indica lo que consideres oportuno, como por ejemplo los límites o restricciones del descuento.', 'xopifier').'</p>
                        <textarea class="form-control" style="height: 150px!important;" id="field-store-promos-discount-indications" name="field-store-promos-discount-indications" placeholder="'.__('Ej: aplica sólo a productos de "X categoría" o con un máximo de X monto por descuento', 'xopifier').'">'.$discount_indications.'</textarea>
                        <span class="error"></span>
                    </div>
                </div>
                <div class="col-md-4 col-sm-12 col-12 side-column mt-md-0 mt-sm-4 mt-4">
                    <div class="sticky-top">
                        <button type="button" class="btn btn-primary btn-toggle-promos-discount-section d-flex flex-row align-items-center justify-content-center gap-2 '.($is_active ? 'is_active' : 'is_inactive').'">
                            
                            <svg class="remove '.($is_active ? '' : 'd-none').'" width="18px" height="18px" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9 0C4.02944 0 0 4.02944 0 9C0 13.9706 4.02944 18 9 18C13.9706 18 18 13.9706 18 9C18 4.02944 13.9706 0 9 0Z" fill="#00B231"/>
                                <path d="M14 8V10H4V8H14Z" fill="white"/>
                            </svg>

                            <svg class="add '.($is_active ? 'd-none' : '').'" width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9 0C4.02944 0 0 4.02944 0 9C0 13.9706 4.02944 18 9 18C13.9706 18 18 13.9706 18 9C18 4.02944 13.9706 0 9 0Z" fill="#00B231"/>
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M10.1667 4H8.16667V8.16667H4V10.1667H8.16667V14H10.1667V10.1667H14V8.16667H10.1667V4Z" fill="white"/>
                            </svg>

                            <span class="exclude '.($is_active ? '' : 'd-none').'">'.__('No incluir Captura de E-Mail', 'xopifier').'</span>
                            <span class="include '.($is_active ? 'd-none' : '').'">'.__('Volver a incluir suscriptor de E-Mail', 'xopifier').'</span>

                        </button>

                        <div class="message-on" '.($is_active ? '' : 'style="display: none;"').'>
                            <p class="small text-center mt-2 px-lg-5 px-md-3 px-sm-0 px-0">'.__('En caso que prefieras crearla después por tu cuenta en Shopify', 'xopifier').'</p>
                            <div class="form-tip">
                                <img src="'.get_template_directory_uri().'/img/info.svg'.'" class="form-tip-img" />
                                <p class="form-label small mb-1">'.__('¿Por qué este descuento?', 'xopifier').'</p>
                                <p>'.__('Es un descuento único que se recomienda aplicar en la primera compra a cambio de que el comprador te deje su E-Mail para que puedas enviarle novedades, promociones o descuentos. Es recomendable para atraer y fidelizar a nuevos clientes. Suele aplicarse un descuento que va del 5 al 20% del valor de tus productos.', 'xopifier').'</p>
                                <p class="form-label small mb-1">'.__('¿Si creo el descuento después lo puedo quitar?', 'xopifier').'</p>
                                <p>'.__('Por supuesto, cuando lo desees, directamente en Shopify, pero te habremos dejado creado el formato.', 'xopifier').'</p>
                                <div class="d-flex justify-content-end align-items-center"><a href="#howto" modal-width="90%" message-position="rightcenter" message="'.__('Este es un ejemplo de suscriptor de E-mail. Se activa cuando un nuevo cliente visita tu tienda.<br>Puedes aplicar la promoción que desees.<br>Te recomendamos usarlo si planeas hacer promociones, campañas o enviar un Newsletter al correo de tus clientes.', 'xopifier').'" image-content="'.get_template_directory_uri().'/img/suscripcion.jpg" class="d-flex justify-content-end align-items-center gap-2">'.__('Ver ejemplo', 'xopifier').' <img src="'.get_template_directory_uri().'/img/open.svg" /></a></div>
                            </div>
                        </div>
                        <div class="message-off" '.($is_active ? 'style="display: none;"' : '').'>
                            <div class="form-tip off text-center">
                                <p class="text-center">'.__('Estás decidiendo no incluir esta información con Xopifier. Puedes crearla luego directamente en Shopify si así lo deseas.', 'xopifier').'</p>
                                <button type="button" class="btn btn-primary btn-continue">'.__('Ok, continuar', 'xopifier').'</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 bottom-column" '.($is_active ? '' : 'style="display:none;"').'>
                    <hr class="my-5">
                    <div class="d-flex flex-column align-items-center justify-content-center text-center">
                        <button type="submit" class="btn btn-primary btn-save-store-promos-discount w-auto '.($is_active ? '' : 'disabled').'">'.__('Guardar y continuar', 'xopifier').'</button>
                        <span class="msg mt-3" style="display:none;"></span>
                    </div>
                </div>
            </div>
        </form>
    ';
}
<?php 
function step_3_tabs_info_faqs($design_id, $tab_id, $is_active, $service_price) {

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

    $exists = get_posts(array('post_type' => 'store-faqs-data', 'meta_key' => 'store', 'meta_value' => $store->ID));
    if(is_array($exists) && count($exists) > 0){
        $store_data_id = $exists[0]->ID;

        $url_faqs = get_field('url_faqs', $store_data_id);
        $faqs = get_field('faqs', $store_data_id);
    }else{
        $url_faqs = '';
        $faqs = '';
    }
    
    return '
        <form id="store-info-faqs-data" method="post" enctype="multipart/form-data">
            <div class="form-loader" style="display: none;"></div>
            <input type="hidden" name="action" value="ws" />
            <input type="hidden" name="disable" value="false" />
            <input type="hidden" name="lang" value="'.ICL_LANGUAGE_CODE.'" />
            <input type="hidden" name="wsa" value="save-store-info-faqs-data" />
            <input type="hidden" name="store_id" value="'.$store->ID.'" />
            <input type="hidden" name="tab_id" value="'.$tab_id.'" />
            <input type="hidden" name="service_price" value="'.$service_price.'" />
            <input type="hidden" name="total_price" value="'.$total_price.'" />
            <input type="hidden" name="nonce" value="'.wp_create_nonce(xopifier_TITLE_FOR_NONCE).'" />

            <div class="row">
                <div class="pe-md-5 pe-sm-auto pe-auto col-md-7 col-sm-12 col-12 main-column position-relative '.($is_active ? '' : 'disabled').'">
                    <div class="disabled-overlay">
                        <h3 class="mb-4"><small>'.__('Preguntas Frecuentes (FAQs)', 'xopifier').'</small></h3>
                        <p class="large">'.__('Las FAQs (por “Frequently Asked Questions” en inglés) son preguntas y respuestas comunes que ayudan a tus clientes a resolver dudas sobre tus productos antes de comprar.', 'xopifier').'</p>
                        <p class="large">'.__('Sirven para aclarar temas como envíos, devoluciones, formas de pago o características del producto, evitando mensajes repetitivos y generando más confianza al comprar.', 'xopifier').'</p>
                        <a class="pb-5 large direct-link d-flex align-items-center gap-2" href="'.get_template_directory_uri().'/img/barra-anuncios.jpg" data-fancybox>
                            '.__('Ver ejemplo de FAQs', 'xopifier').'
                            <img src="'.get_template_directory_uri().'/img/ver-ejemplo.svg" />
                        </a>
                        <hr class="mt-5 pb-3">
                        <div class="gap-3 d-flex flex-row align-items-center justify-content-center">
                            <button type="button" class="btn btn-secondary btn-continue">'.__('No incluir FAQs', 'xopifier').'</button>
                            <button type="button" class="btn btn-primary btn-toggle-faqs-section '.($is_active ? 'is_active' : 'is_inactive').'">'.__('Agregar FAQs por ', 'xopifier').'$'.$service_price.'</button>
                        </div>
                        <p class="leyend mt-3 mb-5 text-center">'.__('Si cambias de idea luego las puedes descartar.', 'xopifier').'</p>
                    </div>
                    <h3 class="mb-4"><small>'.__('FAQs (Preguntas Frecuentes)', 'xopifier').'</small></h3>
                    <div class="field mb-3">
                        <p class="form-label mb-3 small w-100">'.__('¿Ya tienes preguntas frecuentes? Comparte el enlace para agregarlas a tu tienda:', 'xopifier').'</p>
                        <div class="field mb-2">
                            <label class="form-label small d-none" for="field-store-info-faqs-url">'.__('Enlace de FAQs:', 'xopifier').'</label>
                            <input type="text" class="form-control url" value="'.(!empty($url_faqs) ? $url_faqs : '').'" id="field-store-info-faqs-url" name="field-store-info-faqs-url" placeholder="'.__('Ej: https://www.link.com/faq', 'xopifier').'">
                            <span class="error"></span>
                        </div>
                    </div>
                    <div class="field mb-3">
                        <label for="faqs-editor" class="form-label small w-100">'.__('También puedes copiarlas y pegarlas directamente aquí abajo:', 'xopifier').'</label>
                        <div id="faqs-editor" class="form-control mt-2" placeholder="adgasd">'.($faqs).'</div>
                        <input type="hidden" id="field-store-info-faqs" name="field-store-info-faqs" value="'.$faqs.'" />
                        <span class="error"></span>
                    </div>
                    <hr class="mt-5 pb-3">
                    <div class="gap-3 d-flex flex-row align-items-center justify-content-center">
                        <button type="button" class="btn btn-secondary btn-faqs-open-modal is_active">'.__('No incluir FAQs', 'xopifier').'</button>
                        <button type="submit" class="btn btn-primary btn-save-store-faqs-info '.($is_active ? '' : 'disabled').'">'.__('Guardar y continuar', 'xopifier').'</button>
                    </div>
                    <div class="d-flex justify-content-center">
                        <span class="msg mt-3" style="display:none;"></span>
                    </div>
                </div>
                <div class="offset-md-1 offset-sm-0 offset-0 col-md-4 col-sm-12 col-12 side-column mt-md-0 mt-sm-4 mt-4">
                    <div class="sticky-top">
                        <div class="form-tip extra mb-4" '.($is_active ? 'style="display:none;"' : '').'>
                            <p class="fw-semibold">'.__('Costo de este extra: ', 'xopifier').'$'.$service_price.'</p>
                            <p class="mb-1 small">'.__('Incluye:', 'xopifier').'</p>
                            <ul class="mb-0">
                                <li class="small">'.__('Instalación del módulo.', 'xopifier').'</li>
                                <li class="small">'.__('Diseño en una sección de la tienda (página o plantilla de producto).', 'xopifier').'</li>
                                <li class="small">'.__('Carga inicial y corrección de estilo de hasta 10 preguntas y respuestas.', 'xopifier').'</li>
                            </ul>
                        </div>
                        <div class="form-tip mb-4" '.($is_active ? '' : 'style="display:none;"').'>
                            <img src="'.get_template_directory_uri().'/img/info.svg'.'" class="form-tip-img" />
                            <p class="mb-1 small">'.__('Es mejor tener varias preguntas simples con respuestas claras que pocas muy largas o complejas. Te ayudamos a darles buen formato si lo necesitas.', 'xopifier').'</p>
                        </div>
                    </div>
                </div>
                <div class="col-12 bottom-column d-none">
                    <hr class="my-5">
                    <div class="d-flex flex-column align-items-center justify-content-center text-center">
                        <button type="submit" class="btn btn-primary btn-save-store-faqs-info w-auto '.($is_active ? '' : 'disabled').'">'.__('Guardar y continuar', 'xopifier').'</button>
                        <span class="msg mt-3" style="display:none;"></span>
                    </div>
                </div>
            </div>
        </form>
    ';
}
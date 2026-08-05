<?php 
function step_3_tabs_info_reviews($design_id, $tab_id, $is_active, $service_price) {

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

    $exists = get_posts(array('post_type' => 'store-reviews-data', 'meta_key' => 'store', 'meta_value' => $store->ID));
    if(is_array($exists) && count($exists) > 0){
        $store_data_id = $exists[0]->ID;

        $url_reviews = get_field('url_reviews', $store_data_id);
        $files_reviews = get_field('files_reviews', $store_data_id);
    }else{
        $url_reviews = '';
        $files_reviews = '';
    }

    $_files = '';

    if(is_array($files_reviews) && count($files_reviews) > 0){
        if(is_array($files_reviews) && count($files_reviews) > 0){
            foreach($files_reviews as $file){
                if($file['file'] != false){
                    $_files .= $file['file']['url'];
                }
            }
        }
    }
    
    return '
        <form id="store-info-reviews-data" method="post" enctype="multipart/form-data">
            <div class="form-loader" style="display: none;"></div>
            <input type="hidden" name="action" value="ws" />
            <input type="hidden" name="disable" value="false" />
            <input type="hidden" name="lang" value="'.ICL_LANGUAGE_CODE.'" />
            <input type="hidden" name="wsa" value="save-store-info-reviews-data" />
            <input type="hidden" name="store_id" value="'.$store->ID.'" />
            <input type="hidden" name="tab_id" value="'.$tab_id.'" />
            <input type="hidden" name="service_price" value="'.$service_price.'" />
            <input type="hidden" name="total_price" value="'.$total_price.'" />
            <input type="hidden" name="nonce" value="'.wp_create_nonce(xopifier_TITLE_FOR_NONCE).'" />

            <div class="row">
                <div class="pe-md-5 pe-sm-auto pe-auto col-md-7 col-sm-12 col-12 main-column position-relative '.($url_reviews != '' || $files_reviews != '' || $is_active ? '' : 'disabled').'">
                    <div class="disabled-overlay">
                        <h3 class="mb-4"><small>'.__('Reseñas de Productos', 'xopifier').'</small></h3>
                        <p class="large">'.__('Las reseñas son los comentarios y calificaciones (como “estrellitas”) que tus clientes dejan sobre tus productos.', 'xopifier').'</p>
                        <p class="large">'.__('Ayudan a generar confianza, empatía y credibilidad para tu tienda.', 'xopifier').'</p>
                        <p class="large">'.__('Los productos con buenas reseñas se venden más. Mostrar lo que otros opinan puede ser lo que convenza a un nuevo cliente a comprar.', 'xopifier').'</p>
                        <a class="pb-5 large direct-link d-flex align-items-center gap-2" href="'.get_template_directory_uri().'/img/barra-anuncios.jpg" data-fancybox>
                            '.__('Ver ejemplo de reseñas de productos', 'xopifier').'
                            <img src="'.get_template_directory_uri().'/img/ver-ejemplo.svg" />
                        </a>
                        <hr class="mt-5 pb-3">
                        <div class="gap-3 d-flex flex-row align-items-center justify-content-center">
                            <button type="button" class="btn btn-secondary btn-continue">'.__('No incluir Reseñas', 'xopifier').'</button>
                            <button type="button" class="btn btn-primary btn-toggle-reviews-section '.($is_active ? 'is_active' : 'is_inactive').'">'.__('Agregar Reseñas por ', 'xopifier').'$'.$service_price.'</button>
                        </div>
                        <p class="leyend mt-3 mb-5 text-center">'.__('Si cambias de idea luego las puedes descartar.', 'xopifier').'</p>
                    </div>
                    <h3 class="mb-4"><small>'.__('Reseñas de Productos', 'xopifier').'</small></h3>
                    <div class="field mb-3">
                        <p class="form-label small w-100">'.__('Si ya cuentas con reseñas en algún marketplace, indícanos de dónde copiarlas para agregarlas a tu tienda:', 'xopifier').'</p>
                        <p class="mb-3 small">'.__('Si las tienes en Google Docs o en Google Keep también puedes compartirnos al mail developer@xopifier.com', 'xopifier').'</p>
                        <div class="field mb-2">
                            <label class="form-label small d-none" for="field-store-info-reviews-url">Enlace de reseñas:</label>
                            <input type="text" class="form-control url" value="'.(!empty($url_reviews) ? $url_reviews : '').'" id="field-store-info-reviews-url" name="field-store-info-reviews-url" placeholder="Ej: https://www.link.com/reseñas">
                            <span class="error"></span>
                        </div>
                    </div>
                    <div class="field upload mb-2">
                        <label class="form-label small" for="field-upload-info-reviews-files">'.__('Si tienes las reseñas en uno o más archivo agrégalo(s) aquí:', 'xopifier').'</label>
                        <div class="field-upload-info-reviews-files">
                            <div class="field-upload-overlay"></div>
                            <div class="field-upload-field">
                                <input type="file" accept=".doc,.docx,.pages,.odt,.txt" multiple class="field-upload-input d-none" id="field-upload-info-reviews-files" name="field-upload-info-reviews-files[]">
                                <input type="hidden" id="field-upload-info-reviews-files-from-server" name="field-upload-info-reviews-files-from-server" value="'.($_files != '' ? '1' : '0').'">
                                <img src="'.get_template_directory_uri().'/img/upload-to-cloud.svg" class="field-upload-img" />
                                <p class="btn-choose">'.__('Arrastra aquí los archivos o <span>selecciónalos de tu computadora</span>', 'xopifier').'</p>
                                <p><small>'.__('Puedes subir hasta 5 archivos .DOC, .DOCX, .PAGES, .ODT o .TXT', 'xopifier').'</small></p>
                            </div>
                            <div class="field-upload-content">
                                <span class="image-preview-close" style="display:none;"><img src="'.get_template_directory_uri().'/img/close.svg" alt="close preview" /></span>
                            </div>
                        </div>
                    </div>
                    <hr class="mt-5 pb-3">
                    <div class="gap-3 d-flex flex-row align-items-center justify-content-center">
                        <button type="button" class="btn btn-secondary btn-reviews-open-modal is_active">'.__('No incluir Reseñas', 'xopifier').'</button>
                        <button type="submit" class="btn btn-primary btn-save-store-reviews-info '.($is_active ? '' : 'disabled').'">'.__('Guardar y continuar', 'xopifier').'</button>
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
                                <li class="small">'.__('Instalación y configuración de app/plugin de reseñas en tienda.', 'xopifier').'</li>
                                <li class="small">'.__('Configuración de módulo de reseñas en el template del producto.', 'xopifier').'</li>
                                <li class="small">'.__('Carga de 1 hasta 5 reseñas iniciales (si ya tienes reseñas).', 'xopifier').'</li>
                                <li class="small">'.__('Ajustes visuales para que combine con el diseño de la tienda', 'xopifier').'</li>
                            </ul>
                        </div>
                        <div class="form-tip mb-4" '.($is_active ? '' : 'style="display:none;"').'>
                            <img src="'.get_template_directory_uri().'/img/info.svg'.'" class="form-tip-img" />
                            <p class="mb-1 small">'.__('Si todavía no tienes reseñas, simplemente continúa. Instalaremos el plugin y tus clientes podrán empezar a comentar y valorar tus productos cuando lancemos tu tienda.', 'xopifier').'</p>
                        </div>
                    </div>
                </div>
                <div class="col-12 bottom-column d-none">
                    <hr class="my-5">
                    <div class="d-flex flex-column align-items-center justify-content-center text-center">
                        <button type="submit" class="btn btn-primary btn-save-store-reviews-info w-auto '.($is_active ? '' : 'disabled').'">'.__('Guardar y continuar', 'xopifier').'</button>
                        <span class="msg mt-3" style="display:none;"></span>
                    </div>
                </div>
            </div>
        </form>
    ';
}
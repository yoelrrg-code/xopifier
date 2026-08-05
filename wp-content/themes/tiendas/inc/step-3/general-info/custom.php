<?php 
function step_3_tabs_info_custom($design_id, $tab_id, $is_active, $service_price) {

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

    $exists = get_posts(array('post_type' => 'store-custom-data', 'meta_key' => 'store', 'meta_value' => $store->ID));
    if(is_array($exists) && count($exists) > 0){
        $store_data_id = $exists[0]->ID;

        $custom_page_content = get_field('custom_page_content', $store_data_id);
        $custom_page_files = get_field('custom_page_files', $store_data_id);
    }else{
        $custom_page_content = '';
        $custom_page_files = '';
    }

    $_files = '';
    
    if(is_array($custom_page_files) && count($custom_page_files) > 0){

        foreach($custom_page_files as $file){
            
            if($file['file'] != false){
                if($file['file']['mime_type'] == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' || $file['file']['mime_type'] == 'application/msword' || $file['file']['mime_type'] == 'text/plain' || $file['file']['mime_type'] == 'application/vnd.oasis.opendocument.text' || $file['file']['mime_type'] == 'application/pages' || $file['file']['mime_type'] == 'application/pdf') {

                    $filetype = '';
                    switch($file['file']['mime_type']){
                        case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
                            $filetype = 'docx'; break;
                        case 'application/msword':
                            $filetype = 'doc'; break;
                        case 'text/plain':
                            $filetype = 'txt'; break;
                        case 'application/vnd.oasis.opendocument.text':
                            $filetype = 'odt'; break;
                        case 'application/pages':
                            $filetype = 'pages'; break;
                        case 'application/pdf':
                            $filetype = 'pdf'; break;
                    }

                    $_files .= '<div class="img-preview-container">
                        <a href="'.$file['file']['url'].'" target="_blank">
                            <img src="'.get_template_directory_uri().'/img/'.$filetype.'.svg" class="img-preview icon" alt="">
                            <small>'.truncateString($file['file']['filename'], 18).'</small>
                        </a>
                    </div>';
                }
            }

        }

    }
    
    return '
        <form id="store-info-custom-data" method="post" enctype="multipart/form-data">
            <div class="form-loader" style="display: none;"></div>
            <input type="hidden" name="action" value="ws" />
            <input type="hidden" name="disable" value="false" />
            <input type="hidden" name="lang" value="'.ICL_LANGUAGE_CODE.'" />
            <input type="hidden" name="wsa" value="save-store-info-custom-data" />
            <input type="hidden" name="store_id" value="'.$store->ID.'" />
            <input type="hidden" name="tab_id" value="'.$tab_id.'" />
            <input type="hidden" name="service_price" value="'.$service_price.'" />
            <input type="hidden" name="total_price" value="'.$total_price.'" />
            <input type="hidden" name="nonce" value="'.wp_create_nonce(xopifier_TITLE_FOR_NONCE).'" />

            <div class="row">
                <div class="pe-md-5 pe-sm-auto pe-auto col-md-7 col-sm-12 col-12 main-column position-relative '.($is_active ? '' : 'disabled').'">
                    <div class="disabled-overlay">
                        <h3 class="mb-4"><small>'.__('Página personalizada adicional', 'xopifier').'</small></h3>
                        <p class="large">'.__('Ideal para incluir contenido especial que no encaja en las secciones estándar de la configuración base de Tienda 1.0.', 'xopifier').'</p>
                        <p class="large">'.__('Puedes usarla, por ejemplo, para mostrar tu proceso de fabricación, misión y valores, impacto social, colaboraciones o cualquier otro contenido que le dé más profundidad y personalidad a tu tienda.', 'xopifier').'</p>
                        <a class="pb-5 large direct-link w-auto d-flex align-items-center gap-2" href="'.get_template_directory_uri().'/img/barra-anuncios.jpg" data-fancybox>
                            '.__('Ver ejemplo de página adicional', 'xopifier').'
                            <img src="'.get_template_directory_uri().'/img/ver-ejemplo.svg" />
                        </a>
                        <hr class="mt-5 pb-3">
                        <div class="gap-3 d-flex flex-row align-items-center justify-content-center">
                            <button type="button" class="btn btn-secondary btn-continue">'.__('No incluir página adicional', 'xopifier').'</button>
                            <button type="button" class="btn btn-primary btn-toggle-custom-page-section '.($is_active ? 'is_active' : 'is_inactive').'">'.__('Agregar página adicional por ', 'xopifier').'$'.$service_price.'</button>
                        </div>
                        <p class="leyend mt-3 mb-5 text-center">'.__('Si cambias de idea luego las puedes descartar.', 'xopifier').'</p>
                    </div>
                    <h3 class="mb-4"><small>'.__('Página personalizada adicional', 'xopifier').'</small></h3>
                    <div class="field mb-3">
                        <label for="custom-page-editor" class="form-label small mb-2 w-100">'.__('¿Qué quieres incluir en esta página personalizada?', 'xopifier').'</label>
                        <p class="mb-3 small">'.__('Puedes escribir el contenido tal como lo tengas o compartir un link de referencia. También cuéntanos qué título te gustaría que tenga la página.', 'xopifier').'</p>
                        <div id="custom-page-editor" class="form-control mt-2">'.($custom_page_content).'</div>
                        <input type="hidden" id="field-store-info-custom-content" name="field-store-info-custom-content" />
                        <span class="error"></span>
                    </div>
                    <div class="field upload mb-2">
                        <label class="form-label small" for="field-store-info-custom-page-files">'.__('Si el post del blog tiene imágenes o videos, agrégalos aquí:', 'xopifier').'</label>
                        <div class="field-store-info-custom-page-files">
                            <div class="field-upload-overlay"></div>
                            <div class="field-upload-field">
                                <input type="file" accept=".jpg,.png,.gif,.jpeg,.mp4,.pdf,.mov" multiple class="field-upload-input d-none" id="field-store-info-custom-page-files" name="field-store-info-custom-page-files[]">
                                <input type="hidden" id="field-store-info-custom-page-files-from-server" name="field-store-info-custom-page-files-from-server" value="'.($_files != '' ? '1' : '0').'">
                                <img src="'.get_template_directory_uri().'/img/upload-to-cloud.svg" class="field-upload-img" />
                                <p class="btn-choose">'.__('Arrastra aquí los archivos o <span>selecciónalos de tu computadora</span>', 'xopifier').'</p>
                                <p><small>'.__('Puedes subir hasta 5 archivos .PNG, .PDF, .JPG, .GIF, .MP4, .MOV', 'xopifier').'</small></p>
                            </div>
                            <div class="field-upload-content">
                                <span class="image-preview-close" style="display:none;"><img src="'.get_template_directory_uri().'/img/close.svg" alt="close preview" /></span>
                            </div>
                        </div>
                    </div>
                    <hr class="mt-5 pb-3">
                    <div class="gap-3 d-flex flex-row align-items-center justify-content-center">
                        <button type="button" class="btn btn-secondary btn-custom-open-modal is_active">'.__('No incluir página adicional', 'xopifier').'</button>
                        <button type="submit" class="btn btn-primary btn-save-store-custom-page-info '.($is_active ? '' : 'disabled').'">'.__('Guardar y continuar', 'xopifier').'</button>
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
                                <li class="small">'.__('Diseño personalizado de una página o sección adicional.', 'xopifier').'</li>
                                <li class="small">'.__('Integración de hasta 5 bloques de contenido que tú nos proporciones (texto, imágenes, etc.).', 'xopifier').'</li>
                            </ul>
                        </div>
                        <div class="form-tip mb-4" '.($is_active ? '' : 'style="display:none;"').'>
                            <img src="'.get_template_directory_uri().'/img/info.svg'.'" class="form-tip-img" />
                            <p class="mb-1 small">'.__('No te preocupes por escribir un texto perfecto. Puedes compartir tus ideas con tus propias palabras y nosotros te ayudamos a pulir el estilo. Si ya tienes un texto listo, también lo puedes incluir tal cual.', 'xopifier').'</p>
                        </div>
                    </div>
                </div>
                <div class="col-12 bottom-column d-none">
                    <hr class="my-5">
                    <div class="d-flex flex-column align-items-center justify-content-center text-center">
                        <button type="submit" class="btn btn-primary btn-save-store-custom-page-info w-auto '.($is_active ? '' : 'disabled').'">'.__('Guardar y continuar', 'xopifier').'</button>
                        <span class="msg mt-3" style="display:none;"></span>
                    </div>
                </div>
            </div>
        </form>
    ';
}
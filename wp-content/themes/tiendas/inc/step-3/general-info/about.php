<?php
function step_3_tabs_info_about($design_id){
    $store = null;
    if (!empty($design_id)) {
        $store = get_field('store', $design_id);
    }
    
    if (!$store) {
        $userid = get_current_user_id();
        $stores = get_posts(array(
            'post_type'      => 'initial-stage',
            'post_status'    => 'publish',
            'posts_per_page' => 1,
            'meta_query'     => array(
                array(
                    'key'     => 'user',
                    'value'   => $userid,
                    'compare' => '='
                )
            )
        ));
        $store = !empty($stores) ? $stores[0] : null;
    }

    if (!$store) {
        return '';
    }

    $storename = get_field('current_store_name', $store->ID);

    $langs = get_field('languages', 'option');
    $languages = is_array($langs) ? wp_list_pluck($langs, 'language') : array();
    $aditional_services = get_field('aditional_services', $store->ID);
    $tab_count = 3;

    if (is_array($aditional_services)) {
        foreach ($aditional_services as $service) {
            $srv = isset($service['service']) ? $service['service'] : '';
            if (!in_array($srv, $languages) && $srv != 'Inglés' && $srv != 'Cantidad de productos' && $srv != 'Agrega productos') {
                $tab_count++;
            }
        }
    }

    $exists = get_posts(array('post_type' => 'store-data', 'posts_per_page' => 1, 'meta_key' => 'store', 'meta_value' => $store->ID));
    $featured_option = get_field('general_info_featured_options', 'option');

    if(is_array($exists) && count($exists) > 0){
        $store_data_id = $exists[0]->ID;
        $store_description = get_field('store_description', $store_data_id);
        $store_slogan_or_phrase = get_field('store_slogan_or_phrase',  $store_data_id);
        $selected_option = get_field('featured_option', $store_data_id);
        
        $store_particular_description = get_field('general_directions', $store_data_id);
        $featured_images_or_videos = get_field('featured_images_or_videos', $store_data_id);
    }else{
        $store_description = get_field('current_store_description', $store->ID);
        $store_slogan_or_phrase = '';
        $selected_option = '';
        
        $store_particular_description = '';
        $featured_images_or_videos = '';
    }

    $featured_options = '';

    foreach ($featured_option as $featured_key => $featured_opt){
        if(is_array($selected_option)){
            $featured_options .= '<option '.($featured_opt['value'] == $selected_option['value'] ? 'selected="selected"' : '').' value="'.$featured_opt['value'].'">'.$featured_opt['label'].'</option>';
        }else{
            $featured_options .= '<option value="'.$featured_opt['value'].'">'.$featured_opt['label'].'</option>';
        }
    }

    $_files = '';
    
    if(is_array($featured_images_or_videos) && count($featured_images_or_videos) > 0){
        foreach($featured_images_or_videos as $animation){
            $_files .= $animation['file']['url'];
        }
    }

    return '
        <form id="store-about-info-form" method="post" enctype="multipart/form-data">
            <div class="form-loader" style="display: none;"></div>
            <input type="hidden" name="action" value="ws" />
            <input type="hidden" name="lang" value="'.ICL_LANGUAGE_CODE.'" />
            <input type="hidden" name="wsa" value="save-store-about-info" />
            <input type="hidden" name="store_id" value="'.$store->ID.'" />
            <div class="row">
                <div class="pe-md-5 pe-sm-auto pe-auto col-md-8 col-sm-12 col-12">
                    <h3 class="mb-4"><small>'.__('Sobre mi tienda', 'xopifier').'</small></h3>
                    <div class="field mb-3">
                        <label class="form-label w-100" for="field-store-description">'.__('Describe tu tienda', 'xopifier').'</label>
                        <textarea class="form-control mt-2 '.($store_description != '' ? 'valid' : '').'" id="field-store-description" name="field-store-description" placeholder="">'.$store_description.'</textarea>
                    </div>
                    <div class="field mb-3">
                        <label class="form-label w-100 mb-2" for="field-store-phrase">'.__('¿Tienes algún eslogan o frase que defina tu tienda?', 'xopifier').' <span>(Opcional)</span></label>
                        <p class="mb-3 small">'.__('Lo usaremos para un mensaje de bienvenida. Si no tienes nada, puedes escribir ideas en tus propias palabras.', 'xopifier').'</p>
                        <textarea class="form-control mt-2 '.($store_slogan_or_phrase != '' ? 'valid' : '').'" id="field-store-phrase" name="field-store-phrase" placeholder="">'.$store_slogan_or_phrase.'</textarea>
                    </div>
                    <div class="field mb-4">
                        <label class="form-label w-100 mb-2" for="field-store-featured-options">'.__('De las siguientes opciones ¿qué es lo que más quieres destacar?', 'xopifier').'</label>
                        <select class="form-control w-auto pe-5 mt-2 form-select" id="field-store-featured-options" name="field-store-featured-options">
                            '.$featured_options.'
                        </select>
                    </div>
                    <div class="field mb-5 mt-5 featured-option featured-option-1" '.($selected_option == 1 ? '' : 'style="display: none;"').'>
                        <h4>'.__('Usaremos las fotos que incluyas en "Productos".', 'xopifier').'</h4>
                    </div>
                    <div class="field mb-4 featured-option featured-option-4" '.($selected_option != '' && $selected_option != 0 && $selected_option == 5 ? '' : 'style="display: none;"').'>
                        <label class="form-label small" for="field-store-featured-other">'.__('Escribe lo que quieras que destaquemos:', 'xopifier').'</label>
                        <input type="text" class="form-control" value="" id="field-store-featured-other" name="field-store-featured-other" placeholder="'.__('Ej: fotos de los fundadores', 'xopifier').'">
                        <span class="error"></span>
                    </div>
                    <div class="field upload mb-4 featured-option featured-option-2" '.($selected_option != '' && $selected_option != 0 && $selected_option != 1 ? '' : 'style="display: none;"').'>
                        <label class="form-label" for="field-store-featured-images">'.__('Proporciona imágenes o videos de apoyo', 'xopifier').'</label>
                        <p class="mb-3 small d-none">'.__('Puede ser la imagen de tu producto estrella o alguna imagen característica de tu tienda o producto que hayas usado por ejemplo en Facebook, Instagram, etc.', 'xopifier').'</p>
                        <div class="field-upload-images-videos">
                            <div class="field-upload-overlay"></div>
                            <div class="field-upload-field" >
                                <input type="file" accept=".jpg,.png,.gif,.jpeg,.pdf,.mp4,.mov" multiple class="field-upload-input d-none" id="field-store-featured-images" name="field-store-featured-images[]">
                                <input type="hidden" id="field-store-featured-images-from-server" name="field-store-featured-images-from-server" value="'.($_files != '' ? '1' : '0').'">
                                <img src="'.get_template_directory_uri().'/img/upload-to-cloud.svg" class="field-upload-img" />
                                <p class="btn-choose">'.__('Arrastra aquí los archivos o <span>selecciónalos de tu computadora</span>', 'xopifier').'</p>
                                <p><small>'.__('Puedes subir hasta 5 archivos .PNG, .PDF, .JPG, .GIF, .MP4, .MOV', 'xopifier').'</small></p>
                            </div>
                            <div class="field-upload-content">
                                <span class="image-preview-close" style="display: none;"><img src="'.get_template_directory_uri().'/img/close.svg" alt="close preview" /></span>
                            </div>
                        </div>
                    </div>
                    <div class="field mb-3 featured-option featured-option-3" '.($selected_option != '' && $selected_option != 0 ? '' : 'style="display: none;"').'>
                        <label class="form-label w-100 mb-2" for="field-store-particular-description">'.__('¿Alguna instrucción o algo en particular que quieras que consideremos?', 'xopifier').'</label>
                        <textarea class="form-control mt-2 '.($store_particular_description != '' ? 'valid' : '').'" id="field-store-particular-description" name="field-store-particular-description" placeholder="">'.$store_particular_description.'</textarea>
                    </div>
                </div>
                <div class="col-md-4 col-sm-12 col-12">
                    <div class="form-tip sticky-top">
                        <img src="'.get_template_directory_uri().'/img/info.svg'.'" class="form-tip-img" />
                        <p>'.__('<b>Relax.</b> No te preocupes por tener toda la información definida. Cuéntanos lo que quieras y te ayudaremos a crear excelentes textos para que puedas diferenciarte y vender.', 'xopifier').'</p>
                    </div>
                </div>
                <div class="col-12">
                    <hr class="my-5">
                    <div class="d-flex flex-column align-items-center justify-content-center text-center">
                        <button type="submit" class="btn btn-primary btn-save-store-about-info w-auto disabled">'.__('Guardar y continuar', 'xopifier').'</button>
                        <span class="msg mt-3" style="display:none;"></span>
                    </div>
                </div>
            </div>
        </form>
    ';
}
<?php
function step_3_tabs_other_languages($design_id) {
    return '
        <div class="languages py-4">
            '.step_3_tabs_languages_form($design_id).'
        </div>
    ';
}

function get_language_translation($langs, $lang){
    $translation = '';
    
    if($lang == 'Spanish' || $lang == 'Español'){
        if(ICL_LANGUAGE_CODE == 'en')
            return 'Spanish';
        else
            return 'Español';
    }

    if(is_array($langs) and count($langs) > 0){
        foreach($langs as $l => $language){
            if($language['language'] == $lang){
                if(ICL_LANGUAGE_CODE == 'en')
                    $translation = $language['language_translation'];
                else
                    $translation = $language['language'];
            }
        }
    }

    return $translation;
}

function step_3_tabs_languages_form($design_id){
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
    $is_active = false;

    $aditional_services = get_field('aditional_services', $store->ID);
    $total_price = get_field('total_price', $store->ID);
    $default_language = get_field('default_language', $store->ID);
    $service_settings = get_field('service_settings', 'option');
    $language_selector = '';

    $base_language_price = $service_settings['base_services_price']*$service_settings['additional_language_price']/100;

    $langs = get_field('languages', 'option');
    $languages = [];
    foreach($langs as $lang){
        $languages[] = $lang['language'];
    }
    $active_languages = [];
    $index = 0;

    $languages_select_div = '';
    $done = false;
    if(is_array($langs) and count($langs) > 0){
        foreach($langs as $l => $language){
            if(!$language['featured'] and !$done){
                $languages_select_div .= '<div class="lang-div separator"></div>';
                $done = true;
            }
            if($language['language'] != 'Inglés' && $language['language'] != 'English'){
                $languages_select_div .= '<div class="lang-div" id="lang-'.$l.'" value="'.$language['language'].'">'.(ICL_LANGUAGE_CODE == 'en' ? $language['language_translation'] : $language['language']).'</div>';
            }
        }
    }
    
    $base_lang = false;

    if(is_array($aditional_services) && count($aditional_services) > 0){
        $index = 0;
        foreach($aditional_services as $k => $service){
            if($service != ''){
                if($service['type'] == 'lang'){
                    $is_active = true;
                    $active_languages[] = $service['service'];
                    if($service['service'] == 'Inglés' || $service['service'] == 'English'){
                        $base_lang = true;
                        $language_selector .= '
                            <div class="form-check language-form-check base-lang w-100 position-relative">
                                <input class="form-check-input" type="checkbox" value="'.$service['service'].'" checked="true" id="language-service-'.$index.'" name="field-languages[]">
                                <label class="form-check-label" for="language-service-'.$index.'">
                                    '.get_language_translation($langs, $service['service']).'
                                </label>
                            </div>
                        ';    
                    }else{
                        $language_selector .= '
                            <div class="form-check language-form-check w-100 position-relative">
                                <input class="form-check-input" type="checkbox" value="'.$service['service'].'" checked="true" id="language-service-'.$index.'" name="field-languages[]">
                                <label class="form-check-label" for="language-service-'.$index.'">
                                    '.get_language_translation($langs, $service['service']).'
                                </label>
                            </div>
                        ';
                    }
                    $index += 1;
                }
            }
        }
    }

    if(!$base_lang){
        $language_selector = '
            <div class="form-check language-form-check base-lang w-100 position-relative">
                <input class="form-check-input" type="checkbox" value="Inglés" id="language-service-'.$index.'" name="field-languages[]">
                <label class="form-check-label" for="language-service-'.$index.'">
                    '.__('Inglés', 'xopifier').'
                </label>
            </div>
        '.$language_selector;
        $index += 1;
    }

    $language_selector .= '
        <div class="form-check language-form-check w-100 position-relative">
            <input class="form-check-input" type="checkbox" value="" id="language-service-'.$index.'" name="field-languages[]">
            <label class="form-check-label" for="language-service-'.$index.'">
                '.__('Otro', 'xopifier').'
            </label>
            <div class="position-absolute" id="language-selector-box" style="display: none;">
                <div class="language-selector-div">
                    '.$languages_select_div.'
                </div>
            </div>
        </div>
    ';

    $default_language_options = '';
    $default_language_options = '<option '.($default_language == 'Español' || $default_language == 'Spanish' ? 'selected="selected"' : '').' value="Español">'.__('Español', 'xopifier').'</option>';
    foreach($active_languages as $l){
        $default_language_options .= '<option '.($default_language == $l ? 'selected="selected"' : '').' value="'.$l.'">'.get_language_translation($langs, $l).'</option>';
    }
    
    return '
        <form id="store-languages-data" method="post" enctype="multipart/form-data">
            <div class="form-loader" style="display: none;"></div>
            <input type="hidden" name="action" value="ws" />
            <input type="hidden" name="lang" value="'.ICL_LANGUAGE_CODE.'" />
            <input type="hidden" name="disable" value="false" />
            <input type="hidden" name="wsa" value="save-store-languages-data" />
            <input type="hidden" name="store_id" value="'.$store->ID.'" />
            <input type="hidden" name="total_price" value="'.$total_price.'" />
            <input type="hidden" name="language_price" value="'.$base_language_price.'" />

            <div class="row">
                <div class="pe-md-5 pe-sm-auto pe-auto col-md-8 col-sm-12 col-12 main-column position-relative '.($is_active ? '' : 'disabled').'">
                    <div class="disabled-overlay d-none"></div>
                    <h3 class="mb-4"><small>'.__('Tienda en otros idiomas', 'xopifier').'</small></h3>
                    <p class="mb-4 large">'.__('Ideal para ofrecer tu tienda en más de un idioma y conectar con nuevos clientes y mercados.', 'xopifier').'</p>
                    <p class="mb-4 large">'.__('Selecciona el o los idiomas en los que deseas mostrar tu tienda y haremos una traducción fiel del contenido.', 'xopifier').'</p>
                    <div class="languages-selector-container">
                        '.$language_selector.'
                    </div>
                    <div class="position-absolute" id="language-selector-box-placeholder" style="display: none;">
                        <div class="language-selector-div">
                            '.$languages_select_div.'
                        </div>
                    </div>
                    <div class="language-service-default-container" style="display: none;">
                        <hr>
                        <p>'.__('Selecciona el idioma principal:', 'xopifier').' <select class="form-control form-select d-inline-block w-auto" style="padding:5px 35px 5px 10px;height:auto;" id="language-service-default" name="language-service-default">'.$default_language_options.'</select></p>
                    </div>
                    <hr class="mt-5 pb-3">
                    <div class="gap-3 d-flex flex-row align-items-center justify-content-center">
                        <button type="button" class="btn btn-secondary btn-lang-open-modal is_active" '.($is_active ? '' : 'style="display:none;"').'>'.__('No incluir otros idiomas', 'xopifier').'</button>
                        <button type="button" class="btn btn-secondary btn-continue is_inactive" '.($is_active ? 'style="display:none;"' : '').'>'.__('No incluir otros idiomas', 'xopifier').'</button>
                        <button type="submit" class="btn btn-primary flex-row btn-save-store-languages-data w-auto" style="display:none;">'.__('Agregar idioma por', 'xopifier').'<span class="ms-1 fw-bold">$</span><span class="new-price fw-bold"></span></button>
                        <button type="submit" class="btn btn-primary flex-row btn-save-store-languages-data-2 w-auto" style="display:none;">'.__('Guardar cambios', 'xopifier').'</button>
                    </div>
                    <p class="leyend mt-3 mb-5 text-center" '.($is_active ? '' : 'style="display:none;"').'>'.__('Si cambias de idea luego las puedes descartar.', 'xopifier').'</p>
                    <div class="d-flex justify-content-center">
                        <span class="msg mt-3" style="display:none;"></span>
                    </div>
                </div>
                <div class="col-md-4 col-sm-12 col-12 side-column mt-md-0 mt-sm-4 mt-4">
                    <div class="sticky-top">
                        <div class="form-tip extra mb-4">
                            <p class="fw-semibold" style="display:none;">'.__('Costo de este extra:', 'xopifier').' $<span class="new-price"></span></p>
                            <p class="mb-1 small">'.__('Incluye:', 'xopifier').'</p>
                            <p class="small">'.__('Traducción de todos los contenidos de productos y demás secciones de tu Tienda 1.0 al(los) idioma(s) que elijas, incluyendo menús, botones, flujo de pago y comunicación, en general.', 'xopifier').'</p>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    ';
}
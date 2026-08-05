<?php
function step_1_shortcode() {

    // var_dump($_FILES);
    $forms_html = '';

    $forms = get_field('forms', 'option');

    /**
     * aqui creo genero todos los forms
     */
    foreach($forms as $key => $form){
        if(!$form['resume_form'] && !$form['products_form']){//si el form NO es el de resumen
            $forms_html .= step_1_generate_fields_and_forms($key, $form);
        }elseif($form['resume_form'] && !$form['products_form']){//SI es el form de resumen
            $forms_html .= step_1_generate_resume_form($key, $form);
        }elseif($form['products_form'] && !$form['resume_form']){//si el form de product
            $forms_html .= step_1_generate_product_form($key, $form);
        }
    }


    $html = '
        <div id="steps" class="container-fluid px-0">
            <div class="row">
                <div class="col-12">
                    <div class="container p-0">
                        <div class="step step-1">
                            <form autocomplete="false" id="form-step-1" action="" method="POST" enctype="multipart/form-data">
                                <input name="action" type="hidden" value="ws">
                                <input name="wsa" type="hidden" value="create_user_account">
                                <input type="hidden" name="lang" value="'.ICL_LANGUAGE_CODE.'" />
                                <input type="hidden" name="nonce" value="'.wp_create_nonce(xopifier_TITLE_FOR_NONCE).'" />
                                <div class="progressbar" style="display: none;">
                                    <div class="row w-100">
                                        <div class="col-12 progress-box">
                                            <div class="row">
                                                <div class="col-12 d-flex align-items-center justify-content-between">
                                                    <div class="on" percent="0"></div>
                                                    <div class="off" percent="100"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                '.$forms_html.'
                                '.step_1_generate_create_user_modal_form().'
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    ';
    return $html;
}
add_shortcode("step1", "step_1_shortcode");

function step_11_completed_shortcode(){
    $form = get_field('message_no_payment', 'option');
    $form_html = '';
    $form_html .= '
        <div class="sub-step">
            <div class="row">
                <div class="col-12">
                    <div class="row box">
                        <div class="text-center col-12">
                            '.($form['image'] != '' ? '<img src="'.$form['image']['url'].'" alt="main image" class="step-main-image" />' : '').'
                            '.($form['title'] != '' ? '<h3 class="step-title">'.$form['title'].'</h3>' : '').'
                            '.($form['description'] != '' ? '<div class="step-description">'.$form['description'].'</div>' : '').'
                            '.($form['button'] != '' ? '<a class="btn btn-primary" href="'.$form['button']['url'].'" target="'.$form['button']['target'].'" title="'.$form['button']['title'].'">'.$form['button']['title'].'</a>' : '').'
                        </div>
                    </div>
                </div>
            </div>
        </div>
    ';

    $html = '
        <div id="steps" class="container-fluid px-0">
            <div class="row">
                <div class="col-12">
                    <div class="container p-0">
                        <div class="step">
                            '.$form_html.'
                        </div>
                    </div>
                </div>
            </div>
        </div>
    ';

    return $html;
}
add_shortcode("step11completed", "step_11_completed_shortcode");

function step_1_completed_shortcode(){
    $form = get_field('message', 'option');
    $form_html = '';
    $form_html .= '
        <div class="sub-step">
            <div class="row">
                <div class="col-12">
                    <div class="row box">
                        <div class="text-center col-12">
                            '.($form['image'] != '' ? '<img src="'.$form['image']['url'].'" alt="main image" class="step-main-image" />' : '').'
                            '.($form['title'] != '' ? '<h3 class="step-title">'.$form['title'].'</h3>' : '').'
                            '.($form['description'] != '' ? '<div class="step-description">'.$form['description'].'</div>' : '').'
                            '.($form['button'] != '' ? '<a class="btn btn-primary" href="'.$form['button']['url'].'" target="'.$form['button']['target'].'" title="'.$form['button']['title'].'">'.$form['button']['title'].'</a>' : '').'
                        </div>
                    </div>
                </div>
            </div>
        </div>
    ';

    $html = '
        <div id="steps" class="container-fluid px-0">
            <div class="row">
                <div class="col-12">
                    <div class="container p-0">
                        <div class="step">
                            '.$form_html.'
                        </div>
                    </div>
                </div>
            </div>
        </div>
    ';

    return $html;
}
add_shortcode("step1completed", "step_1_completed_shortcode");

function step_1_generate_resume_form($key, $form){
    $price_form = $form['price_form'];
    $optional_services = '';
    $optional_service_form = '';

    $forms_html = '';

    $service_settings = get_field('service_settings', 'option');
    $template_uri = get_template_directory_uri();

    // Hoist languages calculation outside of the service loop (N+1 query & string concat optimization)
    $languages = get_field('languages', 'option');
    $languages_select_div = '';
    if (is_array($languages) && count($languages) > 0) {
        $done = false;
        foreach ($languages as $l => $language) {
            if (!$language['featured'] && !$done) {
                $languages_select_div .= '<div class="lang-div separator"></div>';
                $done = true;
            }
            if ($language['language'] != 'Inglés' && $language['language'] != 'English') {
                $languages_select_div .= '<div class="lang-div" id="lang-'.$l.'" value="'.esc_attr($language['language']).'">'.esc_html($language['language']).'</div>';
            }
        }
    }

    if (is_array($service_settings['services']) && count($service_settings['services']) > 0) {
        foreach ($service_settings['services'] as $service_id => $service) {

            if ($service['editable']) {
                $optional_service_form = '
                    <div class="update-form">
                        <input type="text" min="'.$service['qty'].'" name="update-service-price" class="update-service-price" previous-qty="'.$service['qty'].'" value="'.$service['qty'].'" price-per-unit="'.$service['aditional_price'].'" /> 
                        <button class="btn-update" name="update-service" service-id="'.$service_id.'">'.__('Actualizar', 'xopifier').'</button>
                    </div>
                ';
            } else {
                $optional_service_form = '';
            }

            if ($service['language_selector']) {
                $language_selector = '
                    <div class="form-check language-form-check w-100 position-relative">
                        <input class="form-check-input" type="checkbox" value="" id="language-service-'.$service_id.'" name="field-services[]">
                        <label class="form-check-label" for="language-service-'.$service_id.'">
                            '.__('Otro', 'xopifier').'
                        </label>
                        <div class="position-absolute" id="language-selector-box" style="display: none;">
                            <div class="language-selector-div">
                                '.$languages_select_div.'
                            </div>
                        </div>
                    </div>
                ';
            } else {
                $language_selector = '';
            }

            $optional_services .= '
                '.($service['is_subtitle'] ? '
                    <h4 class="service-subtitle mb-0 '.($service_id == 0 ? '' : 'border-top border-1 mt-3 pt-3').' mb-1">
                        '.$service['subtitle'].' '.($service['tip'] != '' ? '<div class="d-inline-block" title="'.$service['tip'].'" data-bs-toggle="tooltip"><img class="alignnone size-full wp-image-207" src="'.get_template_directory_uri().'/img/help-with-circle.svg" alt="" width="14" height="14" /></div>' : '').'
                    </h4>
                ' : '
                    <div class="row py-1">
                        <div class="col-9 base-service-description optional-services position-relative">
                            <div class="form-check '.($optional_service_form != '' ? 'w-auto' : '').' '.($language_selector ? 'language-form-check base-lang' : '').' '.($service['id'] == 'custom' ? 'custom-box' : '').'">
                                <input class="form-check-input '.($language_selector ? 'language-selector' : '').'" type="checkbox" value="'.$service['id'].'|'.$service['title'].'" id="service-'.$service_id.'" name="field-services[]">
                                <label class="form-check-label '.($service['is_bold'] == 'extra' ? 'fw-semibold' : '').'" for="service-'.$service_id.'">
                                    '.$service['title'].' '.($service['tip'] != '' ? '<div class="d-inline-block" title="'.$service['tip'].'" data-bs-toggle="tooltip"><img class="alignnone size-full wp-image-207" src="'.get_template_directory_uri().'/img/help-with-circle.svg" alt="" width="14" height="14" /></div>' : '').'
                                </label>
                            </div>
                            '.$language_selector.'
                            '.$optional_service_form.'
                            '.($service['id'] == 'custom' ? '
                                <div class="custom-box-container ps-4" style="display:none;">
                                    <p class="mb-2"><small>'.__('Describe brevemente qué contendrá esta página adicional (más adelante podrás dar más detalles)', 'xopifier').'</small></p>
                                    <textarea class="form-control" style="height: auto!important;" rows="2" name="field-custom-page" placeholder="'.__('Ej: Queremos incluir una sección con imágenes y descripciones de nuestro proceso artesanal.', 'xopifier').'"></textarea>
                                </div>
                            ' : '').'
                        </div>
                        <div class="col-3 text-end d-flex '.($service['language_selector'] ? 'align-items-start' : 'align-items-start').' justify-content-end">
                            <input type="hidden" class="'.($language_selector ? 'language-selector' : '').' service-price service-price-'.$service_id.'" name="service-price-'.$service_id.'" value="'.$service['price'].'" />
                            <h4 class="d-none fw-semibold mb-0">$'.$service['price'].'</h4>
                        </div>
                    </div>
                ').'
            ';
        }
    }

    $forms_html .= '
        <div id="'.$form['form_unique_id'].'" stage="'.$form['filter_by_stage'].'" class="sub-step '.($form['resume_form'] ? 'resume-form' : '').' sub-step-'.$key.' '.($form['include_in_progress_bar'] ? 'use-progress-bar' : '').'" '.($form['hide_form'] ? 'style="display: none;"' : '').'>
            <div class="row resume-box">
                <div class="form-loader" style="display: none;"></div>
                <div class="col-12 px-md-4 px-sm-3 px-3">
                    
                    <div class="row box">
                        <div class="'.($form['center_content'] ? 'text-center' : '').' col-12 position-relative">
                            '.($form['form_title'] != '' ? '<h3 class="resume-title">'.$form['form_title'].'</h3>' : '').'
                            '.($form['form_description'] != '' ? '<div class="resume-description mt-3 pe-md-5 pe-sm-0 pe-0"><p>'.$form['form_description'].'</p></div>' : '').'
                            '.($form['main_image'] != '' ? '<img src="'.$form['main_image']['url'].'" alt="main image" class="step-main-image resume-image" />' : '').'
                        </div>
                        <div class="bordered-box col-12">
                            <div class="row">
                                '.step_1_generate_resume_fields().'
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 text-center mt-5">
                            <button class="btn btn-primary ms-3 btn-confirm" type="button">'.__('Confirmar', 'xopifier').'</button>
                        </div>
                    </div>

                </div>
            </div>
            <div class="row price-box" style="display:none;">
                <div class="col-12 px-0">

                    <div class="row px-md-1 px-sm-0 px-0">
                        <div class="col-md-8 col-sm-12 col-12 position-relative">
                            '.($price_form['form_title'] != '' ? '<h3 class="resume-title">'.$price_form['form_title'].'</h3>' : '').'
                            '.($price_form['form_description'] != '' ? '<div class="resume-description mt-3 pe-md-5 pe-sm-0 pe-0"><p>'.$price_form['form_description'].'</p></div>' : '').'
                        </div>
                        <div class="col-md-4 col-sm-12 col-12 mt-md-0 mt-sm-5 mt-5 position-relative">
                            '.($price_form['main_image'] != '' ? '<img src="'.$price_form['main_image']['url'].'" alt="main image" class="step-main-image resume-image" />' : '').'
                        </div>
                    </div>
                
                    <div class="row box mx-1">
                        <div class="bordered-box col-md-7 col-sm-12 col-12 order-md-1 order-sm-2 order-2">
                            
                            <h3 class="border-top pt-3 mb-3 small">'.$service_settings['base_services_title'].'</h3>

                            <div class="row">
                                <div class="col-md-9 col-sm-9 col-9 base-service-description">
                                    '.$service_settings['base_services_description'].'
                                </div>
                                <div class="col-md-3 col-sm-3 col-3 text-end">
                                    <input type="hidden" class="base-price" name="base-price" value="'.$service_settings['base_services_price'].'" />
                                    <h4 class="fw-semibold mt-3">$'.number_format($service_settings['base_services_price'], 0).'</h4>
                                </div>
                            </div>

                            <h4 class="border-top pt-3 mb-3 mt-4">'.$service_settings['optional'].'</h4>

                            '.$optional_services.'
                            <input type="hidden" value="" class="field-services-prices" name="field-services-price">

                            <h4 class="border-bottom pb-0 mb-3 mt-0">&nbsp;</h4>

                            <div class="row py-1">
                                <div class="col-8">
                                    
                                </div>
                                <div class="col-4 text-end">
                                    <input type="hidden" class="selected-scenery" name="field-selected-scenery" value="" />
                                    <input type="hidden" class="products-qty" name="field-products-qty" value="10" />
                                    <input type="hidden" class="total-price" name="field-total-price" value="'.$service_settings['base_services_price'].'" />
                                    <span>'.__('Total: ', 'xopifier').'</span> 
                                    <h3 class="d-inline-block mb-0"><small class="display-total-price">$'.number_format($service_settings['base_services_price'], 0).'</small></h3>
                                </div>
                            </div>

                        </div>

                        <div class="col-md-5 col-sm-12 col-12 order-md-2 order-sm-1 order-1 px-md-3 px-sm-0 px-0">
                            <div class="payment-box sticky-top ms-md-3 ms-sm-0 ms-0 mb-md-0 mb-sm-5 mb-5">
                                <div class="total-cost d-flex justify-content-between mb-2">
                                    <p class="mb-0"><small>'.__('Costo total de tu tienda', 'xopifier').'</small> <site_version></site_version></p>
                                    <span class="display-total-price fw-semibold">$'.number_format($service_settings['base_services_price'], 0).'</span>
                                </div>
                                <div class="first-payment d-flex justify-content-between mb-2">
                                    <p class="mb-0"><small>'.__('Adelanto para activar Xopifier', 'xopifier').'</small></p>
                                    <span class="display-first-price fw-semibold" price="'.$service_settings['base_services_price_percent'].'">$'.number_format($service_settings['base_services_price_percent'], 0).'</span>
                                </div>
                                <div class="payment-description mb-3">
                                    <p class="mb-0">'.str_replace('{{future-payment}}', '<span class="remainder-price">'.number_format($service_settings['base_services_price'] - $service_settings['base_services_price_percent']).'</span>', $service_settings['form_payment_description']).'</p>
                                </div>
                                <button type="button" class="btn btn-primary btn-finish w-100">'.__('Pagar adelanto de', 'xopifier').' $'.number_format($service_settings['base_services_price_percent'], 0).' '.__('y continuar', 'xopifier').'</button>
                                <p class="mt-3 text-center px-5"><small>'.__('El siguiente paso es registrarte y realizar tu pago de forma segura.', 'xopifier').'</small></p>
                            </div>
                        </div>
                    </div>

                    <div class="row d-md-flex d-sm-none d-none">
                        <div class="col-lg-6 col-md-8 col-sm-12 col-12 offset-lg-3 offset-md-2 offset-sm-0 offset-0 text-center mt-4 pt-2">
                            <p class="text-center mt-3">'.__('El adelanto de $120 nos confirma el servicio y el inicio del diseño de las propuestas de tu tienda. Si no quedas satisfecho con las propuestas, puedes solicitar un reembolso.', 'xopifier').'</p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    ';

    return $forms_html;
}

function step_1_generate_resume_fields(){
    $forms = get_field('forms', 'option');

    $form_fields_resume_col = "";

    foreach($forms as $key => $form){
        $fields_resume = '';
        $field_store_name = '';

        if($form['products_form']){
            $fields_resume .= '
                <ul class="resume-field storeProducts" field="storeProducts"></ul>
            ';
        }

        /**
         * aqui creo el listado que se muestra en el form de resumen al final del paso 1
         */
        if(!$form['resume_form'] && $form['include_in_progress_bar']){

            $has_category = false;

            if(is_array($form['form_fields']) && count($form['form_fields']) > 0){
                foreach($form['form_fields'] as $id => $field){
                    // if($form['form_unique_id'] == 'step-1-form-3'){//es el formmulario inicial
                    //     if($id == 0){
                    //         $field_store_name = $field['form_field_name'];
                    //     }else{
                    //         $fields_resume .= '<p class="pt-1">'.__('Referencia actual:', 'xopifier').' <a class="resume-field url" field="'.$field['form_field_name'].'" href="" target="_blank" alt="'.__('Referencia actual:', 'xopifier').'">'.__('Referencia actual: ', 'xopifier').'</a></p>';
                    //     }
                    // }else{
                        if($field['form_field_type'] == 'textarea' || $field['form_field_type'] == 'text'){
                            if($field['form_field_name'] == 'currentStoreProductCategory' && !$has_category){
                                $fields_resume .= '
                                    <ul class="resume-field text" field="'.$field['form_field_name'].'"></ul>
                                ';
                                $has_category = true;    
                            }elseif($field['form_field_name'] != 'currentStoreProductCategory'){
                                $fields_resume .= '
                                    <div class="resume-field text" field="'.$field['form_field_name'].'"></div>
                                ';
                            }
                        }elseif($field['form_field_type'] == 'upload'){
                            $fields_resume .= '
                                <div class="resume-field uploads mt-2" field="'.$field['form_field_name'].'"></div>
                            ';
                        }elseif($field['form_field_type'] == 'email'){
                            $fields_resume .= '
                                <ul class="resume-field text" field="'.$field['form_field_name'].'"></ul>
                            ';
                        }elseif($field['form_field_type'] == 'url'){
                            $fields_resume .= '
                                <a href="" class="resume-field url" target="_blank" field="'.$field['form_field_name'].'"></a>
                            ';
                        }elseif($field['form_field_type'] == 'checkboxes'){
                            $fields_resume .= '
                                <ul class="resume-field checkboxes" field="'.$field['form_field_name'].'"></ul>
                            ';
                        }elseif($field['form_field_type'] == 'radios'){
                            $fields_resume .= '
                                <ul class="resume-field radios" field="'.$field['form_field_name'].'"></ul>
                            ';
                        }
                    // }
                }
            }

            $form_fields_resume_col .= '
                <div class="col-sm-12 col-12 form-item">
                    <div class="bordered-box-inner">
                        <div class="d-flex justify-content-between align-items-start gap-3">
                            <h4 class="mb-3">'.$form['resume_field_title'].' '.($form['form_unique_id'] == 'step-1-form-12' ? '<span class="resume-field text" field="'.$field_store_name.'"></span>' : '').'</h4>
                        </div>
                        '.$fields_resume.'
                    </div>
                </div>
            ';
            //<a class="btn-edit" href="#'.$form['form_unique_id'].'" title="'.__('Editar', 'xopifier').'">'.__('Editar', 'xopifier').'</a>
        }
    }

    /**
     * aqui guardo el html del form de resumen
     */
    $form_fields_resume = '
        <div class="col-12">
            <div class="row masonry-container">
                '.$form_fields_resume_col.'
            </div>
        </div>
    ';

    return $form_fields_resume;
}

function step_1_generate_fields_and_forms($key, $form){
    $forms_html = '';

    $form_options = $form['form_options'];
    $form_options_list = '';
    if(is_array($form_options) && count($form_options) > 0){
        foreach($form_options as $k => $option){
            $form_options_list .= '
                <li class="stage" id="'.$option['id'].'">
                    <a class="btn-next" href="'.$option['link'].'" target="" title="">
                        <p class="option-title">'.$option['title'].'</p>
                        <p class="option-desc">'.$option['description'].'</p>
                    </a>
                </li>
            ';
        }
    }

    $form_fields = '';

    /**
     * aqui creo todos los fields de los forms
     */
    if(is_array($form['form_fields']) && count($form['form_fields']) > 0){
        if($form['repeater_field'] && !$form['category_repeater_field']){//solo si es un campo repeater
            $form_fields .= '
                <div class="repeater-fields"><div class="repeater-field mb-4">
            ';
        }
        if($form['repeater_field'] && $form['category_repeater_field']){//solo si es un campo repeater de categorias
            $form_fields .= '
                <div class="category-repeater-fields">
            ';
        }

        /**
         * aqui genero todos los fields del form actual
         */
        foreach($form['form_fields'] as $id => $field){
            if($form['repeater_field'] && $form['category_repeater_field']){
                $form_fields .= '
                    <div class="repeater-field mb-4">
                ';
            }
            if($field['form_field_type'] == 'upload'){//genero el campo upload
                $form_fields .= '
                    <div class="field upload'.($field['form_field_top_separator'] ? 'top-sep' : '').' mb-4">
                        <label class="form-label" for="field-'.$field['form_field_name'].'-from-server'.($form['repeater_field'] && !$form['category_repeater_field'] ? '-0' : ($form['repeater_field'] && $form['category_repeater_field'] ? '-'.$id : '')).'">'.$field['form_field_label'].' '.(!$field['required'] && $field['form_field_display_optional'] ? '<span>('.__('Opcional', 'xopifier').')</span>' : '').'</label>
                        <div class="field-upload">
                            <div class="field-upload-overlay"></div>
                            <div class="field-upload-field">
                                <input type="file" accept=".jpg,.png,.gif,.jpeg,.mp4,.pdf,.mov" multiple class="field-upload-input d-none '.($field['required'] ? 'required notverified' : 'verified').'" id="field-'.$field['form_field_name'].'" name="field-'.$field['form_field_name'].'[]" '.($field['required'] ? 'required' : '').'>
                                <input type="hidden" id="field-'.$field['form_field_name'].'-from-server" name="field-'.$field['form_field_name'].'-from-server" value="0" />
                                <img src="'.get_template_directory_uri().'/img/upload-to-cloud.svg" class="field-upload-img" />
                                <p class="btn-choose">'.$field['form_field_description'].'</p>
                                <p><small>'.$field['form_field_placeholder'].'</small></p>
                            </div>
                            <div class="field-upload-content">
                                <span class="image-preview-close" style="display: none;"><img src="'.get_template_directory_uri().'/img/close.svg" alt="close preview" /></span>
                            </div>
                        </div>
                    </div>
                ';
            }elseif($field['form_field_type'] == 'text'){//genero el campo text
                $form_fields .= '
                    <div class="field '.($field['form_field_top_separator'] ? 'top-sep' : '').' mb-4" '.($field['default_visibility'] ? 'style="display:none;"' : '').'>
                        <label class="form-label" for="field-'.$field['form_field_name'].($form['repeater_field'] && !$form['category_repeater_field'] ? '-0' : ($form['repeater_field'] && $form['category_repeater_field'] ? '-'.$id : '')).'">'.$field['form_field_label'].' '.(!$field['required'] && $field['form_field_display_optional'] ? '<span>('.__('Opcional', 'xopifier').')</span>' : '').'</label>
                        '.($field['form_field_description'] != '' ? '<span class="d-block info">'.$field['form_field_description'].'</span>' : '').'
                        <input type="text" class="form-control '.($field['required'] ? 'required notverified' : 'verified').'" id="field-'.$field['form_field_name'].($form['repeater_field'] && !$form['category_repeater_field'] ? '-0' : ($form['repeater_field'] && $form['category_repeater_field'] ? '-'.$id : '')).'" name="field-'.$field['form_field_name'].($form['repeater_field'] ? '[]' : '').'" placeholder="'.$field['form_field_placeholder'].'" '.($field['required'] ? 'required' : '').'>
                        <span class="error"></span>
                    </div>
                ';
            }elseif($field['form_field_type'] == 'checkboxes'){//genero el campo del grupo de checkboxes
                $values = $field['form_field_values'];
                $checkboxes = '';
                if(is_array($values) && count($values) > 0){
                    foreach($values as $k => $value){
                        $checkboxes .= '
                            <div class="form-check form-checks '.($value['display_textarea_on_click'] ? 'has-more' : '').'">
                                <input class="form-check-input" type="checkbox" value="'.$value['value'].'" id="field-'.$field['form_field_name'].'-'.$k.'" name="field-'.$field['form_field_name'].($value['display_textarea_on_click'] ? '-has-more' : '').'[]">
                                <label class="form-check-label" for="field-'.$field['form_field_name'].'-'.$k.'">
                                    '.$value['value'].'
                                </label>
                                '.($value['display_textarea_on_click'] ? '<textarea class="form-control checkbox-more" style="display: none;" id="field-'.$field['form_field_name'].'-more" name="field-'.$field['form_field_name'].'-more"></textarea>' : '').'
                            </div>
                        ';
                    }
                    
                    $form_fields .= '
                        <div class="field mb-4" max-selected="'.$field['form_field_max_selection'].'" '.($field['default_visibility'] ? 'style="display:none;"' : '').'>
                            '.$checkboxes.'
                        </div>
                    ';
                }
            }elseif($field['form_field_type'] == 'radios'){//genero el campo del grupo de radios
                $values = $field['form_field_values'];
                $radios = '';
                $mores = '';
                if(is_array($values) && count($values) > 0){
                    foreach($values as $k => $value){
                        $radios .= '
                            <div class="form-check form-radio '.($value['display_textarea_on_click'] ? 'has-more' : '').'" '.($field['form_field_orientation'] ? '' : 'style="width: 50% !important;"').'>
                                '.($value['tag'] ? '<div class="form-radio-tag">'.str_replace('[star]', '<img src="'.get_template_directory_uri().'/img/star.svg" />', $value['tag']).'</div>' : '').'
                                <input class="form-radio-input '.($field['required'] ? 'required notverified' : 'verified').'" target="'.($value['target'] ? $value['target'] : '').'" type="radio" value="'.$value['value'].'" id="field-'.$field['form_field_name'].'-'.$k.'" name="field-'.$field['form_field_name'].'">
                                <label class="form-radio-label" for="field-'.$field['form_field_name'].'-'.$k.'">
                                    '.$value['value'].'
                                </label>
                            </div>
                        ';
                    }

                    foreach($values as $k => $value){
                        $mores .= $value['display_textarea_on_click'] ? '<div id="field-'.$field['form_field_name'].'-'.$k.'-more" class="more-info-block" style="display: none;"><p>'.$value['target_description'].'</p></div>' : '';
                    }
                    
                    $form_fields .= '
                        <div class="field mb-4 '.($field['form_field_orientation'] ? '' : 'd-flex flex-wrap').'" max-selected="'.$field['form_field_max_selection'].'" '.($field['default_visibility'] ? 'style="display:none!important;"' : '').'>
                            <label class="form-label w-100 d-block mb-3" for="field-'.$field['form_field_name'].($form['repeater_field'] && !$form['category_repeater_field'] ? '-0' : ($form['repeater_field'] && $form['category_repeater_field'] ? '-'.$id : '')).'">'.$field['form_field_label'].' '.(!$field['required'] && $field['form_field_display_optional'] ? '<span>('.__('Opcional', 'xopifier').')</span>' : '').'</label>
                            '.($field['form_field_description'] != '' ? '<span class="d-block info w-100 mb-4">'.$field['form_field_description'].'</span>' : '').'
                            '.$radios.'
                        </div>
                        '.$mores.'
                    ';
                }
            }elseif($field['form_field_type'] == 'content'){//genero el campo content
                $form_fields .= '
                    <div class="field mb-4" '.($field['default_visibility'] ? 'style="display:none;"' : '').'>
                        <div class="content w-100 d-block">'.$field['form_field_content'].'</div>
                    </div>
                ';
            }elseif($field['form_field_type'] == 'email'){//genero el campo de url 
                $form_fields .= '
                    <div class="field '.($field['form_field_top_separator'] ? 'top-sep' : '').' mb-4" '.($field['default_visibility'] ? 'style="display:none;"' : '').'>
                        <label class="form-label" for="field-'.$field['form_field_name'].($form['repeater_field'] && !$form['category_repeater_field'] ? '-0' : ($form['repeater_field'] && $form['category_repeater_field'] ? '-'.$id : '')).'">'.$field['form_field_label'].' '.(!$field['required'] && $field['form_field_display_optional'] ? '<span>('.__('Opcional', 'xopifier').')</span>' : '').'</label>
                        '.($field['form_field_description'] != '' ? '<span class="d-block info">'.$field['form_field_description'].'</span>' : '').'
                        <input type="email" class="form-control validate-email '.($field['unique'] ? 'unique' : '').' '.($field['required'] ? 'required notverified' : 'verified').'" id="field-'.$field['form_field_name'].($form['repeater_field'] && !$form['category_repeater_field'] ? '-0' : ($form['repeater_field'] && $form['category_repeater_field'] ? '-'.$id : '')).'" name="field-'.$field['form_field_name'].($form['repeater_field'] ? '[]' : '').'" placeholder="'.$field['form_field_placeholder'].'" '.($field['required'] ? 'required' : '').'>
                        <span class="error"></span>
                    </div>
                ';
            }elseif($field['form_field_type'] == 'url'){//genero el campo de url 
                $form_fields .= '
                    <div class="field '.($field['form_field_top_separator'] ? 'top-sep' : '').' mb-4" '.($field['default_visibility'] ? 'style="display:none;"' : '').'>
                        <label class="form-label" for="field-'.$field['form_field_name'].($form['repeater_field'] && !$form['category_repeater_field'] ? '-0' : ($form['repeater_field'] && $form['category_repeater_field'] ? '-'.$id : '')).'">'.$field['form_field_label'].' '.(!$field['required'] && $field['form_field_display_optional'] ? '<span>('.__('Opcional', 'xopifier').')</span>' : '').'</label>
                        '.($field['form_field_description'] != '' ? '<span class="d-block info">'.$field['form_field_description'].'</span>' : '').'
                        '.($field['form_field_multiples_links'] ? '
                            <textarea class="form-control '.($field['required'] ? 'required notverified' : 'verified').'" id="field-'.$field['form_field_name'].($form['repeater_field'] && !$form['category_repeater_field'] ? '-0' : ($form['repeater_field'] && $form['category_repeater_field'] ? '-'.$id : '')).'" name="field-'.$field['form_field_name'].($form['repeater_field'] ? '[]' : '').'" placeholder="'.$field['form_field_placeholder'].'" '.($field['required'] ? 'required' : '').'></textarea>
                        ' : '
                            <input type="text" class="form-control validate-url '.($field['required'] ? 'required notverified' : 'verified').'" id="field-'.$field['form_field_name'].($form['repeater_field'] && !$form['category_repeater_field'] ? '-0' : ($form['repeater_field'] && $form['category_repeater_field'] ? '-'.$id : '')).'" name="field-'.$field['form_field_name'].($form['repeater_field'] ? '[]' : '').'" placeholder="'.$field['form_field_placeholder'].'" '.($field['required'] ? 'required' : '').'>
                        ').'
                        <span class="error"></span>
                    </div>
                ';
            }elseif($field['form_field_type'] == 'textarea'){//genero el campo text area 
                $form_fields .= '
                    <div class="field '.($field['form_field_top_separator'] ? 'top-sep' : '').' mb-4" '.($field['default_visibility'] ? 'style="display:none;"' : '').'>
                        <label class="form-label" for="field-'.$field['form_field_name'].($form['repeater_field'] && !$form['category_repeater_field'] ? '-0' : ($form['repeater_field'] && $form['category_repeater_field'] ? '-'.$id : '')).'">'.$field['form_field_label'].' '.(!$field['required'] && $field['form_field_display_optional'] ? '<span>('.__('Opcional', 'xopifier').')</span>' : '').'</label>
                        '.($field['form_field_description'] != '' ? '<span class="d-block info">'.$field['form_field_description'].'</span>' : '').'
                        <textarea class="form-control '.($field['required'] ? 'required notverified' : 'verified').'" id="field-'.$field['form_field_name'].($form['repeater_field'] && !$form['category_repeater_field'] ? '-0' : ($form['repeater_field'] && $form['category_repeater_field'] ? '-'.$id : '')).'" name="field-'.$field['form_field_name'].($form['repeater_field'] ? '[]' : '').'" placeholder="'.$field['form_field_placeholder'].'" '.($field['required'] ? 'required' : '').'></textarea>
                        <span class="error"></span>
                    </div>
                ';
            }
            if($form['repeater_field'] && $form['category_repeater_field']){//fin del campo repeater de categorias
                $form_fields .= '
                    </div>
                ';
            }
        }
        if($form['repeater_field'] && !$form['category_repeater_field']){//fin del campo repeater
            $form_fields .= '
                </div></div><div class="pt-2"><button class="mt-3 btn btn-secondary btn-plus disabled">'.$form['repeater_button_label'].'</button></div>
            ';
        }
        if($form['repeater_field'] && $form['category_repeater_field']){
            $form_fields .= '
                </div><div class="d-flex pt-2 align-items-center justify-content-start"><button class="w-auto btn btn-secondary btn-plus-categ disabled">'.$form['repeater_button_label'].'</button></div>
            ';
        }
    }

    $form_tip = '';
    if($form['has_tip']){//muestro la columna derecha para los tips del formulario
        $form_tip .= '
            <div class="col-md-4 col-sm-12 col-12">
                <div class="form-tip sticky-top">
                    <img src="'.get_template_directory_uri().'/img/info.svg'.'" class="form-tip-img" />
                    '.($form['form_tip']['title'] != '' ? '<p class="form-tip-title">'.$form['form_tip']['title'].'</p>' : '').'
                    '.$form['form_tip']['description'].'
                </div>
            </div>
        ';
    }

    /**
     * conformo la estructura del formulario
     */
    $forms_html .= '
        <div id="'.$form['form_unique_id'].'" stage="'.$form['filter_by_stage'].'" class="sub-step '.($form['end_form'] ? 'end-form' : '').' sub-step-'.$key.' '.($form['include_in_progress_bar'] ? 'use-progress-bar' : '').'" '.($form['hide_form'] ? 'style="display: none;"' : '').'>
            <div class="row form-box">
                <div class="form-loader" style="display: none;"></div>
                <div class="col-12 px-md-4 px-sm-3 px-3">
                    
                    <div class="row box">
                        <div class="'.($form['center_content'] ? 'col-12 text-center' : 'pe-md-4 pe-sm-auto pe-auto col-md-8 col-sm-12 col-12').'">
                            '.($form['main_image'] != '' ? '<img src="'.$form['main_image']['url'].'" alt="main image" class="step-main-image" />' : '').'
                            '.($form['form_title'] != '' ? '<h3 class="step-title">'.$form['form_title'].'</h3>' : '').'
                            '.($form['form_description'] != '' ? '<div class="step-description"><p>'.$form['form_description'].'</p></div>' : '').'
                            '.($form_options_list != '' ? '
                                <ul class="stages">
                                    '.$form_options_list.'
                                </ul>
                                <a href="#step-1-form-'.$key.'" class="step-back d-none"><img src="'.get_template_directory_uri().'/img/back.svg'.'" /></a>' : '').'
                            '.($form['form_button'] != '' && !$form['end_form'] ? '<a class="btn btn-primary btn-next" href="'.$form['form_button']['url'].'" target="'.$form['form_button']['target'].'" title="'.$form['form_button']['title'].'">'.$form['form_button']['title'].'</a>' : '').'
                            '.($form_fields != '' ? $form_fields : '').'
                        </div>
                        
                        '.$form_tip.'
                    </div>

                    '.($form_options_list != '' ? '
                        <div class="row d-none">
                            <div class="col-12">
                                <p class="text-center my-4">'.__('* El servicio de diseño y construcción de tiendas de Xopifier no incluye el costo de tu suscripción en Shopify.', 'xopifier').'</p>
                            </div>
                        </div>
                    ' : '').'

                    '.($form['end_form'] ? '
                        <div class="row bottom-buttons">
                            <div class="col-12 text-center mt-md-5 mt-sm-0 mt-0">
                                <a class="btn btn-primary ms-3 direct-link" href="'.$form['form_button']['url'].'">'.$form['form_button']['title'].'</a>
                            </div>
                        </div>
                    ' : '').'

                    '.($form_fields != '' ? '
                        <div class="row bottom-buttons">
                            <div class="col-12 text-center mt-md-5 mt-sm-0 mt-0 box-continue">
                                <a href="#step-1-form-'.$key.'" class="step-back"><img src="'.get_template_directory_uri().'/img/back.svg'.'" /></a>
                                <a class="btn btn-primary btn-next ms-3 disabled" href="#step-1-form-'.($key + 2).'">'.$form['form_button_label'].'</a>
                            </div>
                            <div class="col-12 text-center mt-md-5 mt-sm-0 mt-0 box-save" style="display:none;">
                                <button class="btn btn-primary btn-save ms-3" href="" type="button">'.__('Guardar', 'xopifier').'</button>
                            </div>
                        </div>
                    ' : '').'

                </div>
            </div>
        </div>
    ';

    return $forms_html;
}

function step_1_generate_product_form($key, $form){
    $form_tip = '';
    $forms_html = '';

    if($form['has_tip']){//muestro la columna derecha para los tips del formulario
        $form_tip .= '
            <div class="col-md-4 col-sm-12 col-12">
                <div class="form-tip sticky-top">
                    '.($form['form_tip']['style'] == 'tip' ? '<img src="'.get_template_directory_uri().'/img/info.svg'.'" class="form-tip-img" />' : '<img src="'.get_template_directory_uri().'/img/question.svg'.'" class="form-tip-img" />').'
                    '.($form['form_tip']['title'] != '' ? '<p class="form-tip-title">'.$form['form_tip']['title'].'</p>' : '').'
                    '.$form['form_tip']['description'].'
                </div>
            </div>
        ';
    }

    $blank_products_html = '';
    for($i = 2; $i <= 3; $i++) {
        $blank_products_html .= '
            <li class="product-placeholder d-flex align-items-center justify-content-between w-100 py-3">
                <span class="counter disabled me-3">'.($i).'</span>
                <div class="me-auto ms-0 d-flex align-items-center justify-content-start gap-3 w-75">
                    <img src="'.get_template_directory_uri().'/img/image-placeholder.png" class="product-thumb me-0 img-fluid" />
                    <div class="d-flex flex-column align-items-start justify-content-center w-100">
                        <div class="gray-bar w-75"></div>
                        <div class="gray-bar w-50 dark-gray"></div>
                        <div class="gray-bar w-25"></div>
                    </div>
                </div>
                <a class="trash me-0 ms-0 disabled" disabled href="javascript:void(0)"><img src="'.get_template_directory_uri().'/img/trash.svg" class="product-trash step3 img-fluid me-3 disabled" disabled /></a>
            </li>
        ';
    }

    $form_fields = '
        <div class="products-form" style="">
            <ul class="mt-4 nav nav-tabs d-flex gap-2 justify-content-between" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active w-100" id="frompc-tab" data-bs-toggle="tab" data-bs-target="#frompc" type="button" role="tab" aria-controls="frompc" aria-selected="true">'.__('Desde mi computadora', 'xopifier').'</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link w-100" id="fromlink-tab" data-bs-toggle="tab" data-bs-target="#fromlink" type="button" role="tab" aria-controls="fromlink" aria-selected="false">'.__('Desde referencia (link)', 'xopifier').'</button>
                </li>
            </ul>
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade" id="fromlink" role="tabpanel" aria-labelledby="fromlink-tab">
                    <div class="field mb-3">
                        <label class="form-label" for="field-fromLinkProductName">'.__('Nombre del producto:', 'xopifier').'</label>
                        <input type="text" class="form-control" id="field-fromLinkProductName" name="field-fromLinkProductName" placeholder="">
                        <span class="error"></span>
                    </div>
                    <div class="field mb-3">
                        <label class="form-label" for="field-fromLinkProductLink">'.__('Link al producto:', 'xopifier').'</label>
                        <p class="mb-3 small">'.__('Comparte el link en donde se vea el producto, sea en redes sociales, una tienda o un marketplace. Es la referencia que usaremos para el diseño.', 'xopifier').'</p>
                        <input type="text" class="form-control" id="field-fromLinkProductLink" name="field-fromLinkProductLink" placeholder="'.__('Ej: https://www.link.com', 'xopifier').'">
                        <span class="error"></span>
                    </div>
                    <div class="field mb-3">
                        <label class="form-label" for="field-fromLinkProductCategory">'.__('Categoría:', 'xopifier').'</label>
                        <select class="form-select form-control" id="field-fromLinkProductCategory" name="field-fromLinkProductCategory">
                        </select>
                        <span class="error"></span>
                    </div>
                    <div class="d-flex flex-md-row flex-sm-column flex-column align-items-center justify-content-md-start justify-content-sm-center justify-content-center mt-0 gap-md-0 gap-sm-2 gap-2">
                        <button class="btn btn-secondary btn-cancel-link-product d-none me-2" href="" type="button">'.__('Cancelar', 'xopifier').'</button>
                        <button class="btn btn-primary btn-save-link-product d-none" href="" type="button">'.__('Guardar', 'xopifier').'</button>
                        <button class="btn btn-primary btn-add-link-product disabled" href="" type="button">'.__('Agregar producto', 'xopifier').'</button>
                    </div>
                </div>
                <div class="tab-pane fade show active" id="frompc" role="tabpanel" aria-labelledby="frompc-tab">
                    <div class="field upload mb-3 w-100">
                        <label class="form-label" for="field-fromPCProductMedia-from-server">'.__('Imágenes o animaciones del producto:', 'xopifier').'</label>
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
                            <label class="form-label" for="field-fromPCProductCurrecy">'.__('Precio:', 'xopifier').'</label>
                            <select class="form-select form-control currecy" id="field-fromPCProductCurrecy" name="field-fromPCProductCurrecy">
                                <option value="CLP">CLP</option>
                                <option value="MXN">MXN</option>
                                <option value="USD" selected>USD</option>
                                <option value="EUR">EUR</option>
                            </select>
                            <span class="error"></span>
                        </div>
                        <div class="field price mb-3">
                            <label class="form-label" for="field-fromPCProductPrice">&nbsp;</label>
                            <input type="text" class="form-control" id="field-fromPCProductPrice" name="field-fromPCProductPrice" placeholder="">
                            <span class="error"></span>
                        </div>
                        <div class="field sale-price mb-3 ms-md-3 ms-sm-0 ms-0">
                            <div class="d-flex flex-row mb-1 justify-content-start align-items-center">
                                <label class="form-label mb-0 me-2" for="field-fromPCProductSalePrice">'.__('Precio referencial:', 'xopifier').'</label> 
                                <img src="'.get_template_directory_uri().'/img/info.svg'.'" data-bs-toggle="tooltip" title="'.__('Opcional, para destacar oferta', 'xopifier').'" />
                            </div>
                            <input type="text" class="form-control" id="field-fromPCProductSalePrice" name="field-fromPCProductSalePrice" placeholder="">
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
                        <textarea class="form-control" id="field-fromPCProductDescription" name="field-fromPCProductDescription" placeholder=""></textarea>
                        <span class="error"></span>
                    </div>
                    '.($form['form_bottom_description'] != '' ? '
                        <div class="mb-4 bottom-description">
                            '.$form['form_bottom_description'].'
                        </div>
                    ' : '').'
                    <div class="d-flex flex-md-row flex-sm-column flex-column align-items-center justify-content-md-start justify-content-sm-center justify-content-center mt-0 gap-md-0 gap-sm-2 gap-2">
                        <button class="btn btn-secondary btn-cancel-pc-product d-none me-2" href="" type="button">'.__('Cancelar', 'xopifier').'</button>
                        <button class="btn btn-primary btn-save-pc-product d-none" href="" type="button">'.__('Guardar', 'xopifier').'</button>
                        <button class="btn btn-primary btn-add-pc-product disabled" href="" type="button">'.__('Agregar producto', 'xopifier').'</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="products-list" style="display: none;">
            <div class="d-none link-products"></div>
            <div class="d-none pc-products"></div>

            <ul class="products-list-container" qty="0">
                '.$blank_products_html.'
            </ul>

            <div class="mt-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="1" id="field-fromPCProductLessThanThree" name="field-fromPCProductLessThanThree">
                    <label class="form-check-label" for="field-fromPCProductLessThanThree">
                        '.__('Mi tienda tiene menos de 3 productos', 'xopifier').'
                    </label>
                </div>
            </div>

            <div class="d-flex align-items-center justify-content-center text-center mt-4">
                <button type="button" class="mt-3 btn btn-secondary btn-plus-product w-auto">'.__('Agregar producto', 'xopifier').'</button>
            </div>
        </div>
    ';
    /**
     * conformo la estructura del formulario
     */
    $forms_html .= '
        <div id="'.$form['form_unique_id'].'" stage="'.$form['filter_by_stage'].'" class="sub-step '.($form['end_form'] ? 'end-form' : '').' sub-step-'.$key.' '.($form['include_in_progress_bar'] ? 'use-progress-bar' : '').'" '.($form['hide_form'] ? 'style="display: none;"' : '').'>
            <div class="row">
                <div class="col-12 px-md-4 px-sm-3 px-3">
                    
                    <div class="row box">
                        <div class="'.($form['center_content'] ? 'col-12 text-center' : 'pe-md-5 pe-sm-auto pe-auto col-md-8 col-sm-12 col-12').'">
                            '.($form['main_image'] != '' ? '<img src="'.$form['main_image']['url'].'" alt="main image" class="step-main-image" />' : '').'
                            '.($form['form_title'] != '' ? '<h3 class="step-title">'.$form['form_title'].'</h3>' : '').'
                            '.($form['form_description'] != '' ? '<div class="step-description"><p>'.$form['form_description'].'</p></div>' : '').'
                            '.($form_fields != '' ? $form_fields : '').'
                        </div>
                        
                        '.$form_tip.'
                    </div>

                    '.($form_fields != '' ? '
                        <div class="row bottom-buttons">
                            <div class="col-12 text-center mt-md-5 mt-sm-0 mt-0 box-continue">
                                <a href="#step-1-form-'.$key.'" class="step-back"><img src="'.get_template_directory_uri().'/img/back.svg'.'" /></a>
                                <a class="btn btn-primary btn-next ms-3 disabled" href="#step-1-form-'.($key + 2).'">'.$form['form_button_label'].'</a>
                            </div>
                            <div class="col-12 text-center mt-md-5 mt-sm-0 mt-0 box-save" style="display:none;">
                                <button class="btn btn-primary btn-save ms-3" href="" type="button">'.__('Guardar', 'xopifier').'</button>
                            </div>
                        </div>
                    ' : '').'

                </div>
            </div>
        </div>
    ';

    return $forms_html;
}

function step_1_generate_create_user_modal_form(){
    
    $logged_in = is_user_logged_in() ? 1 : 0;
    $service_settings = get_field('service_settings', 'option');

    return '
        <div class="user-form-modal" logged-in="'.$logged_in.'" style="display: none;">
            <div class="user-form-modal-box">
                <div class="form-loader" style="display: none;"></div>
                <div class="row">
                    <div class="col-md-6 col-sm-12 col-12">
                        <h3><small>'.__('Registro', 'xopifier').'</small></h3>
                        <p class="mt-2 mb-3">'.__('Con estos mismos datos podrás ingresar a Xopifier para revisar las propuestas de diseño que crearemos para tu tienda.', 'xopifier').'</p>
                        
                        <div class="field mb-3">
                            <label class="form-label" for="field-firstname">'.__('Nombre(s):', 'xopifier').'</label>
                            <input type="text" autocomplete="false" class="form-control required notverified" id="field-firstname" name="field-firstname" required />
                            <span class="error"></span>
                        </div>

                        <div class="field mb-3">
                            <label class="form-label" for="field-lastname">'.__('Apellido(s):', 'xopifier').'</label>
                            <input type="text" autocomplete="false" class="form-control required notverified" id="field-lastname" name="field-lastname" required />
                            <span class="error"></span>
                        </div>

                        <div class="field mb-3">
                            <label class="form-label" for="field-useremail">'.__('Email:', 'xopifier').'</label>
                            <input type="email" autocomplete="false" class="form-control required notverified" id="field-useremail" name="field-useremail" required />
                            <span class="error"></span>
                        </div>

                        <div class="mb-md-3 mb-sm-3 mb-2 pb-2">
                            <div class="field mb-3">
                                <label class="form-label" for="field-userpass">'.__('Contraseña:', 'xopifier').'</label>
                                <input type="password" autocomplete="new-password" class="form-control required notverified" id="field-userpass" name="field-userpass" required />
                                <span class="error"></span>
                            </div>

                            <div class="field mb-3">
                                <label class="form-label" for="field-userpass-confirm">'.__('Confirmar contraseña:', 'xopifier').'</label>
                                <input type="password" autocomplete="new-password" class="form-control required notverified" id="field-userpass-confirm" name="field-userpass-confirm" required />
                                <span class="error"></span>
                            </div>
                        </div>

                        <div class="text-center">
                            <button class="btn btn-primary btn-create" type="submit">'.__('Continuar con el pago', 'xopifier').'</button>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12 col-12">
                        <h3 class="mb-3"><small><strong>'.__('Adelanto para activar Xopifier', 'xopifier').'</strong></small></h3>
                        <div class="payment-box">
                            <h4 class="fw-bold mb-0">'.__('Total a pagar:', 'xopifier').'</h4>
                            <h4 class="mb-0 fw-semibold">$'.number_format($service_settings['base_services_price_percent'], 2).'</h4>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    ';
}
<?php 
function profile_shortcode() {
    $html = '
        <div id="steps" class="step-2 container-fluid px-0">
            <div class="row">
                <div class="col-12">
                    <div class="container">
                        <div class="step">
                            <a href="'.wp_logout_url(apply_filters( 'wpml_permalink', site_url(), ICL_LANGUAGE_CODE )).'">logout</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    ';
    return $html;
}
add_shortcode("profile", "profile_shortcode");

function popup_resume($design_id){
    $html = '';

    $logo_data = get_field('logo_simple', 'option');
    $logo = isset($logo_data['url']) ? esc_url($logo_data['url']) : '';

    $resume_settings = get_field('resume_settings', 'option');
    $service_settings = get_field('service_settings', 'option');

    $design = get_post($design_id);
    $designs = $design ? get_field('designs', $design->ID) : array();

    if(!empty($design_id)){
        $store = get_field('store', $design_id);
    }else{
        $userid = get_current_user_id();
        $stores = get_posts(array(
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
        $store = !empty($stores) ? $stores[0] : null;
    }

    if (!$store) {
        return '';
    }

    $designs_thumbs = [];

    $approved_design = get_field('approved-design-id', $store->ID);

    //recorro el repeater de los disennos para generar los tabs y los contenidos
    if(is_array($designs) and count($designs) > 0){
        foreach($designs as $k => $d){
            $tip = isset($d['tip']) ? $d['tip'] : '';

            $homepage = isset($d['homepage']) ? $d['homepage'] : array();
            $img_url  = isset($homepage['image']['url']) ? esc_url($homepage['image']['url']) : '';
            $img_desc = isset($homepage['description']) ? esc_attr($homepage['description']) : '';

            $designs_thumbs[] = '
                <div class="design-thumb design-thumb-'.($k+1).'" '.($k+1 != $approved_design ? 'style="display:none;"' : '').'>
                    <div class="thumb-img">
                        <img src="'.$img_url.'" alt="'.$img_desc.'" />
                    </div>
                    <div class="thumb-content d-flex flex-column">
                        <p>'.__('Propuesta', 'xopifier').' '.($k+1).': "'.esc_html($d['title']).'"</p>
                        <small>'.__('Vista parcial del diseño disponible. Una vez que hagas el primer pago podrás ver los diseños completos.', 'xopifier').'</small>
                    </div>
                </div>
            ';
        }
    }

    $total_price = (float)get_field('total_price', $store->ID);
    $percent_price = isset($service_settings['base_services_price_percent']) ? (float)$service_settings['base_services_price_percent'] : 0;

    $aditional_services = get_field('aditional_services', $store->ID);
    $langs = get_field('languages', 'option');
    $languages = is_array($langs) ? wp_list_pluck($langs, 'language') : array();

    $selected_languages = '';
    $selected_languages_price = 0;
    $other_services = '';
    $other_services_price = 0;

    $aditional_products_price = 0;

    if(is_array($aditional_services) and count($aditional_services) > 0){
        foreach ($aditional_services as $service) {
            if(in_array($service['service'], $languages) || $service['service'] == 'Inglés') {
                if($selected_languages == '')
                    $selected_languages .= $service['service'];
                else
                    $selected_languages .= ', '.$service['service'];

                $selected_languages_price += $service['price'];
            }elseif($service['service'] == 'Cantidad de productos' || $service['service'] == 'Agrega productos'){
                $aditional_products_price += $service['price'];
            }else{
                if($service['active']){
                    if($other_services == '')
                        $other_services .= $service['service'];
                    else
                        $other_services .= ', '.$service['service'];

                    $other_services_price += $service['price'];
                }
            }
        }
    }

    $extras = '';

    $total_products = get_field('products_qty', $store->ID);
    if($total_products > 10) {
        $extras .= '
            <li>
                <div class="row">
                    <div class="col-9">
                        '.__('<strong>Más de 10 productos</strong> creados en Xopifier', 'xopifier').'
                        <div class="d-inline-block" title="'.$total_products.' productos" data-bs-toggle="tooltip">
                            <img class="alignnone size-full wp-image-207" src="'.get_template_directory_uri().'/img/help-with-circle.svg" alt="" width="14" height="14" />
                        </div>
                    </div>
                    <div class="col-3 text-end">
                        $'.$aditional_products_price.'
                    </div>
                </div>  
            </li>
        ';
    }

    if($other_services != ''){
        $extras .= '
            <li>
                <div class="row">
                    <div class="col-9">
                        '.__('Secciones adicionales:', 'xopifier').' <strong>'.esc_html($other_services).'</strong>
                    </div>
                    <div class="col-3 text-end">
                        $'.$other_services_price.'
                    </div>
                </div>
            </li>
        ';
    }
    

    $service_thumb = '';
    foreach($designs_thumbs as $thumb){
        $service_thumb .= $thumb;
    }

    $base_services_description = isset($resume_settings['base_services_description']) ? $resume_settings['base_services_description'] : '';
    $base_services_price = isset($service_settings['base_services_price']) ? $service_settings['base_services_price'] : 0;
    $optional_heading = isset($resume_settings['optional']) ? $resume_settings['optional'] : '';
    $form_payment_description = isset($resume_settings['form_payment_description']) ? $resume_settings['form_payment_description'] : '';

    $html .= '
        <section id="popup-resume" style="display: none;">
            <div id="popup-resume-box">
                <img src="'.get_template_directory_uri().'/img/close-popup.svg" class="popup-resume-close"/>
                
                <div class="popup-resume-logo">
                    <span class="mi-xopifier">'.__('Mi Xopifier', 'xopifier').'</span>
                    <img src="'.$logo.'"/>
                </div>

                <ul class="nav nav-tabs" id="myResumeTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="store-tab" data-bs-toggle="tab" data-bs-target="#store-tab-pane" type="button" role="tab" aria-controls="store-tab-pane" aria-selected="true">'.__('Mi tienda', 'xopifier').'</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile-tab-pane" type="button" role="tab" aria-controls="profile-tab-pane" aria-selected="false">'.__('Usuario y contraseña', 'xopifier').'</button>
                    </li>
                </ul>
                <div class="tab-content" id="myResumeTabContent">
                    <div class="tab-pane fade show active" id="store-tab-pane" role="tabpanel" aria-labelledby="store-tab" tabindex="0">
                        <div id="steps">
                            <div class="step">
                                <div class="sub-step">
                                    <div class="container-fluid price-box">
                                        <div class="row box">
                                            <div class="bordered-box col-12 pb-3">
                                                            
                                                <h4 class="border-bottom pb-2 mb-3">'.__('Mi tienda', 'xopifier').' <site_version>{{site_version}}</site_version></h4>

                                                <div class="row">
                                                    <div class="col-9 base-service-description">
                                                        '.str_replace('{{design}}', '<div class="design-thumbs">'.($service_thumb != '' ? '' : '<div class="design-not-selected py-2 px-3">'.__('El diseño aún no ha sido seleccionado.', 'xopifier').'</div>').$service_thumb.'</div>', $base_services_description).'
                                                    </div>
                                                    <div class="col-3 text-end">
                                                        <h4 class="fw-normal">$'.number_format((float)$base_services_price, 0).'</h4>
                                                    </div>
                                                </div>


                                                '.($selected_languages == '' ? '' : '
                                                    <h4 class="border-bottom pb-2 mb-3 mt-4">'.$optional_heading.'</h4>

                                                    <div class="row">
                                                        <div class="col-12 optional-service-description">
                                                            <ul>
                                                                <li>
                                                                    <div class="row">
                                                                        <div class="col-9">
                                                                            '.__('Tu tienda en <strong>otros idiomas</strong>', 'xopifier').'
                                                                            <div class="d-inline-block" title="'.esc_attr($selected_languages).'" data-bs-toggle="tooltip">
                                                                                <img class="alignnone size-full wp-image-207" src="'.get_template_directory_uri().'/img/help-with-circle.svg" alt="" width="14" height="14" />
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-3 text-end">
                                                                            $'.floor($selected_languages_price).'
                                                                        </div>
                                                                    </div>
                                                                </li>
                                                                '.$extras.'
                                                            </ul>
                                                        </div>
                                                    </div>
                                                ').'

                                                <h4 class="border-bottom pb-0 mb-3 mt-0">&nbsp;</h4>

                                                <div class="row py-1">
                                                    <div class="col-9">
                                                        
                                                    </div>
                                                    <div class="col-3 text-end">
                                                        <span>'.__('Total: ', 'xopifier').'</span> <h3 class="d-inline-block mb-0 ps-2"><small class="display-total-price">$'.floor($total_price).'</small></h3>
                                                    </div>
                                                </div>

                                                <div class="row py-1">
                                                    <div class="col-12">
                                                        '.(get_field('status', $store->ID) == 'approved_design' ? '
                                                            <div class="payment-message p-3 mt-3">
                                                                <div class="d-flex align-items-center justify-content-end partial-payment">
                                                                    <p class="p-0 m-0" style="color:rgba(18,18,20,0.4);">'.__('Pago adelantado:', 'xopifier').'</p><h3 class="d-inline-block mb-0"><small class="display-total-price ps-2" style="color:rgba(18,18,20,0.4);">-$'.$percent_price.'</small></h3>
                                                                </div>
                                                                <div class="d-flex align-items-center justify-content-end">
                                                                    <p class="fw-semibold p-0 m-0">'.__('Por pagar<sup>*</sup>:', 'xopifier').'</p><h3 class="d-inline-block mb-0"><small class="display-total-price ps-2">$'.floor($total_price - $percent_price).'</small></h3>
                                                                </div>
                                                                <p class="small text-end mb-0">'.__('* Cuando tu tienda esté aprobada por ti lista para instalarse en tu cuenta de Shopify.', 'xopifier').'</p>
                                                            </div>
                                                        ' : '
                                                            <div class="payment-message text-end py-3 mt-3">
                                                                '.str_replace('{{price}}', '<h3 class="d-inline-block mb-0"><small class="display-total-price ps-2">$'.$percent_price.'</small></h3>', $resume_settings['form_payment_description']).'
                                                            </div>
                                                        ').'
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="profile-tab-pane" role="tabpanel" aria-labelledby="profile-tab" tabindex="0">
                        <a class="btn btn-secondary d-block mt-4" href="'.wp_logout_url(apply_filters( 'wpml_permalink', site_url(), ICL_LANGUAGE_CODE )).'">Logout</a>
                    </div>
                </div>

            </div>
        </section>
    ';

    return $html;
}

function store_resume($design_id){
    $html = '';

    $resume_settings = get_field('resume_settings', 'option');
    $service_settings = get_field('service_settings', 'option');

    $design = get_post($design_id);
    $designs = get_field('designs', $design->ID);

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

    $approved_design = get_field('approved-design-id', $store->ID);

    $designs_thumbs = [];

    //recorro el repeater de los disennos para generar los tabs y los contenidos
    if(is_array($designs) and count($designs) > 0){
        foreach($designs as $k => $d){
            $tip = $d['tip'];

            $homepage = $d['homepage'];

            $designs_thumbs[] = '
                <div class="design-thumb design-thumb-'.($k+1).'" '.($k+1 != $approved_design ? 'style="display:none;"' : '').'>
                    <div class="thumb-img">
                        <img src="'.$homepage['image']['url'].'" alt="" />
                    </div>
                    <div class="thumb-content d-flex flex-column">
                        <p>'.__('Opción', 'xopifier').' '.($k+1).': "'.$d['title'].'"</p>
                        <small>'.__('Vista parcial del diseño disponible. Una vez que hagas el primer pago podrás ver los diseños completos.', 'xopifier').'</small>
                    </div>
                </div>
            ';
        }
    }

    $total_price = get_field('total_price', $store->ID);
    $percent_price = $service_settings['base_services_price_percent'];

    $aditional_services = get_field('aditional_services', $store->ID);
    $langs = get_field('languages', 'option');
    $languages = [];
    foreach($langs as $lang){
        $languages[] = $lang['language'];
    }
    $selected_languages = '';
    $selected_languages_price = 0;
    $other_services = '';
    $other_services_price = 0;

    $aditional_products_price = 0;

    if(is_array($aditional_services) and count($aditional_services) > 0){    
        foreach ($aditional_services as $service) {
            if(in_array($service['service'], $languages) || $service['service'] == 'Inglés') {
                if($selected_languages == '')
                    $selected_languages .= $service['service'];
                else
                    $selected_languages .= ', '.$service['service'];

                $selected_languages_price += $service['price'];
            }elseif($service['service'] == 'Cantidad de productos' || $service['service'] == 'Agrega productos'){
                $aditional_products_price += $service['price'];
            }else{
                if($other_services == '')
                    $other_services .= $service['service'];
                else
                    $other_services .= ', '.$service['service'];

                $other_services_price += $service['price'];
            }
        }
    }

    $extras = '';

    $total_products = get_field('products_qty', $store->ID);
    if($total_products > 10) {
        $extras .= '
            <li>
                <div class="row">
                    <div class="col-9">
                        '.__('<strong>Más de 10 productos</strong> creados en Xopifier', 'xopifier').'
                        <div class="d-inline-block" title="'.$total_products.' productos" data-bs-toggle="tooltip">
                            <img class="alignnone size-full wp-image-207" src="'.site_url().'/wp-content/uploads/2024/05/help-with-circle.svg" alt="" width="14" height="14" />
                        </div>
                    </div>
                    <div class="col-3 text-end">
                        $'.$aditional_products_price.'
                    </div>
                </div>  
            </li>
        ';
    }

    if($other_services != ''){
        $extras .= '
            <li>
                <div class="row">
                    <div class="col-9">
                        '.__('Secciones adicionales:', 'xopifier').' <strong>'.$other_services.'</strong>
                    </div>
                    <div class="col-3 text-end">
                        $'.$other_services_price.'
                    </div>
                </div>
            </li>
        ';
    }
    

    $service_thumb = '';
    foreach($designs_thumbs as $thumb){
        $service_thumb .= $thumb;
    }

    $html .= '    
        
        <h4 class="border-bottom pb-2 mb-3">'.__('Mi tienda Xopifier 1.0', 'xopifier').'</h4>

        <div class="row">
            <div class="col-9 base-service-description">
                '.(get_field('status', $store->ID) != 'pending' && get_field('status', $store->ID) != 'complete_info'
                    ? 
                        str_replace('{{design}}', '<div class="design-thumbs">'.$service_thumb.'</div>', $resume_settings['base_services_description']) 
                    :   
                        str_replace('{{design}}', 'SIN SELECCIONAR', $resume_settings['base_services_description'])
                ).'
            </div>
            <div class="col-3 text-end">
                <h4 class="fw-semibold mt-4">$'.number_format($service_settings['base_services_price'], 0).'</h4>
            </div>
        </div>

        '.($selected_languages == '' ? '' : '
            <h4 class="border-bottom pb-2 mb-3 mt-4">'.$resume_settings['optional'].'</h4>

            <div class="row">
                <div class="col-12 optional-service-description">
                    <ul>
                        <li>
                            <div class="row">
                                <div class="col-9">
                                    '.__('Tu tienda en <strong>otros idiomas</strong>', 'xopifier').'
                                    <div class="d-inline-block" title="'.$selected_languages.'" data-bs-toggle="tooltip">
                                        <img class="alignnone size-full wp-image-207" src="'.site_url().'/wp-content/uploads/2024/05/help-with-circle.svg" alt="" width="14" height="14" />
                                    </div>
                                </div>
                                <div class="col-3 text-end">
                                    $'.floor($selected_languages_price).'
                                </div>
                            </.div>
                        </li>
                        '.$extras.'
                    </ul>
                </div>
            </div>
        ').'

        <h4 class="border-bottom pb-0 mb-3 mt-0">&nbsp;</h4>

        <div class="row py-1">
            <div class="col-9">
                
            </div>
            <div class="col-3 text-end">
                <span>'.__('Total: ', 'xopifier').'</span> <h3 class="d-inline-block mb-0 ps-2"><small class="display-total-price">$'.floor($total_price).'</small></h3>
            </div>
        </div>

        <div class="row py-1">
            <div class="col-12">
                '.(get_field('status', $store->ID) == 'approved_design' ? '
                    <div class="payment-message p-3 mt-3">
                        <div class="d-flex align-items-center justify-content-end partial-payment">
                            <p class="p-0 m-0">'.__('Pago parcial:', 'xopifier').'</p><h3 class="d-inline-block mb-0"><small class="display-total-price ps-2">-$'.$percent_price.'</small></h3>
                        </div>
                        <div class="d-flex align-items-center justify-content-end">
                            <p class="fw-semibold p-0 m-0">'.__('Por pagar<sup>*</sup>:', 'xopifier').'</p><h3 class="d-inline-block mb-0"><small class="display-total-price ps-2">$'.floor($total_price - $percent_price).'</small></h3>
                        </div>
                        <p class="small text-end mb-0">'.__('* Cuando tu tienda esté aprobada por ti lista para instalarse en tu cuenta de Shopify.', 'xopifier').'</p>
                    </div>
                    <div class="text-center">
                        <a href="'.apply_filters( 'wpml_permalink', site_url('/paso-3/'), ICL_LANGUAGE_CODE ).'" class="btn btn-primary mt-4">'.__('Ir al Paso 3', 'xopifier').'</a>
                    </div>
                ' : '
                    '.(get_field('status', $store->ID) == 'complete_info' ? '
                        <div class="text-center">
                            <a href="'.apply_filters( 'wpml_permalink', site_url('/paso-2/'), ICL_LANGUAGE_CODE ).'" class="btn btn-primary mt-4">'.__('Ir al Paso 2', 'xopifier').'</a>
                        </div>
                    ' : '').'
                ').'
            </div>
        </div>
    ';

    return $html;
}

function generate_progress_dots(){
    global $designId;

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

    $store_id = $store->ID;

    $currentStep = get_current_step(get_current_user_id());

    $proposaldone = false;
    if($currentStep == 'approved_design'){
        $step3_tab_statuses = get_step3_status($store_id);

        $proposaldone = true;
        if(is_array($step3_tab_statuses[$store_id])){
            foreach($step3_tab_statuses[$store_id] as $tab => $subtabs){
                if(is_array($subtabs)){
                    foreach($subtabs as $subtab){
                        if($subtab != 'done'){
                            $proposaldone = false;
                        }
                    }
                }
            }
        }
    }

    $html = '
        <ul class="progress-dots">
            <li class="dot done">'.__('Definición de tienda', 'xopifier').'</li>
            <li class="store-info dot '.($currentStep == 'complete_info' ? 'current' : ($currentStep == 'design_sent' ? 'done' : ($currentStep == 'store_in_review' ? 'done' : ''))).'">'.__('Información de tienda', 'xopifier').'</li>
            <li class="proposal dot '.($currentStep == 'design_sent' ? 'current' : '').'">'.__('Selección de propuesta', 'xopifier').'</li>
            <li class="final-approve dot '.($currentStep == 'store_in_review' ? 'current' : '').'">'.__('Aprobación final', 'xopifier').'</li>
            <li class="shopify-store dot">'.__('Mi tienda en Shopify', 'xopifier').'</li>
        </ul>
    ';

    echo $html;
}
<?php
function step_3_tabs_info($design_id) {

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

    $langs = get_field('languages', 'option');
    $languages = [];
    foreach($langs as $lang){
        $languages[] = $lang['language'];
    }
    $aditional_services = get_field('aditional_services', $store->ID);
    $tab_count = 3;
    
    $services_tabs = '';
    $services_contents = '';

    $step3_status = get_step3_status($store->ID);

    // var_dump($step3_status);exit;

    $service_settings = get_field('service_settings', 'option')['services'];

    if(is_array($service_settings) and count($service_settings) > 0){
        foreach ($service_settings as $k => $service) {
            if($service['type'] == 'extra' && $service['id'] != 'payment_methods') {
                $tab_count++;
            }
        }
    }

    $tab_width = (100/$tab_count) - 1;

    $active_services = [];

    // var_dump($store->ID, $aditional_services);

    if(is_array($aditional_services) and count($aditional_services) > 0){
        foreach ($aditional_services as $k => $service) {
            if($service['type'] == 'extra') {
                $active_services[] = [
                    'id' => $service['id'],
                    'service' => $service['service'],
                    'active' => $service['active']
                ];
            }
        }
    }

    function find_active_service($service, $active_services){
        // var_dump($service, $active_services);
        foreach($active_services as $serv){
            if($serv['id'] == $service && $serv['active'] == 1){
                return 1;
            }
        }
        return 0;
    }
    
    if(is_array($service_settings) and count($service_settings) > 0){
        foreach ($service_settings as $k => $service) {
            if($service['type'] == 'extra' && $service['id'] != 'payment_methods') {

                // $tab = remove_accents(str_replace(' ', '-', strtolower($service['title'])));
                $tab = $service['id'];
                // $tab = str_replace('(', '', $tab);
                // $tab = str_replace(')', '', $tab);

                $service_title = $service['title'];
                if($service['id'] == 'reviews'){
                    $service_title = __('Reseñas', 'xopifier');
                }
                if($service['id'] == 'faqs'){
                    $service_title = __('FAQs', 'xopifier');
                }
                if($service['id'] == 'custom'){
                    $service_title = __('Página adicional', 'xopifier');
                }

                $services_tabs .= '
                    <li class="nav-item service-tab" role="presentation" style="width: '.$tab_width.'%;">
                        <button class="nav-link w-100 sub-item sub-tab extra '.get_step3_tab_status($step3_status, 'info-service-'.$tab.'-tab').'" sub-tab="info-service-'.$tab.'-tab" id="info-service-'.$tab.'-tab" data-bs-toggle="tab" data-bs-target="#info-service-'.$tab.'" type="button" role="tab" aria-controls="service-'.$tab.'" aria-selected="false">
                            '.$service_title.'
                            <img src="'.get_template_directory_uri().'/img/done.svg" />
                        </button>
                    </li>
                ';

                switch($service['id']){
                    case 'reviews':{
                        $services_contents .= '
                            <div class="tab-pane fade" id="info-service-'.$tab.'" role="tabpanel" aria-labelledby="service-'.$tab.'-tab">
                                '.step_3_tabs_info_reviews($design_id, 'info-service-'.$tab.'-tab', find_active_service($service['id'], $active_services), $service['price']).'
                            </div>
                        ';
                        break;
                    }
                    case 'faqs':{
                        $services_contents .= '
                            <div class="tab-pane fade" id="info-service-'.$tab.'" role="tabpanel" aria-labelledby="service-'.$tab.'-tab">
                                '.step_3_tabs_info_faqs($design_id, 'info-service-'.$tab.'-tab', find_active_service($service['id'], $active_services), $service['price']).'
                            </div>
                        ';
                        break;
                    }
                    case 'custom':{
                        $services_contents .= '
                            <div class="tab-pane fade" id="info-service-'.$tab.'" role="tabpanel" aria-labelledby="service-'.$tab.'-tab">
                                '.step_3_tabs_info_custom($design_id, 'info-service-'.$tab.'-tab', find_active_service($service['id'], $active_services), $service['price']).'
                            </div>
                        ';
                        break;
                    }
                }
            }
        }
    }

    return '
        <div class="info">
            <ul class="nav nav-tabs d-flex justify-content-md-between justify-content-sm-start justify-content-start" id="myTabInfo" role="tablist">
                <li class="nav-item" role="presentation" style="width: '.$tab_width.'%;">
                    <button class="nav-link sub-item sub-tab active w-100 '.get_step3_tab_status($step3_status, 'info-store-tab').'" sub-tab="info-store-tab" id="info-store-tab" data-bs-toggle="tab" data-bs-target="#info-store" type="button" role="tab" aria-controls="info-store" aria-selected="true">
                        '.__('Sobre mi tienda', 'xopifier').'
                        <img src="'.get_template_directory_uri().'/img/done.svg" />
                    </button>
                </li>
                <li class="nav-item" role="presentation" style="width: '.$tab_width.'%;">
                    <button class="nav-link sub-item sub-tab w-100 '.get_step3_tab_status($step3_status, 'info-contact-tab').'" sub-tab="info-contact-tab" id="info-contact-tab" data-bs-toggle="tab" data-bs-target="#info-contact" type="button" role="tab" aria-controls="info-contact" aria-selected="false">
                        '.__('Datos de contacto', 'xopifier').'
                        <img src="'.get_template_directory_uri().'/img/done.svg" />
                    </button>
                </li>
                <li class="nav-item" role="presentation" style="width: '.$tab_width.'%;">
                    <button class="nav-link sub-item sub-tab w-100 '.get_step3_tab_status($step3_status, 'info-policy-tab').'" sub-tab="info-policy-tab" id="info-policy-tab" data-bs-toggle="tab" data-bs-target="#info-policy" type="button" role="tab" aria-controls="info-policy" aria-selected="false">
                        '.__('Políticas', 'xopifier').'
                        <img src="'.get_template_directory_uri().'/img/done.svg" />
                    </button>
                </li>
                '.$services_tabs.'
            </ul>
            <div class="tab-content" id="myTabContentInfo">
                <div class="tab-pane fade show active" id="info-store" role="tabpanel" aria-labelledby="info-store-tab">
                    '.step_3_tabs_info_about($design_id).'
                </div>
                <div class="tab-pane fade" id="info-contact" role="tabpanel" aria-labelledby="info-contact-tab">
                    '.step_3_tabs_info_contact($design_id).'
                </div>
                <div class="tab-pane fade" id="info-policy" role="tabpanel" aria-labelledby="info-policy-tab">
                    '.step_3_tabs_info_policy($design_id).'
                </div>
                '.$services_contents.'
            </div>
        </div>
    ';
}
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

    $tab_count = 0;
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
            </div>
        </div>
    ';
}
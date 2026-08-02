<?php 
function step_3_tabs_info_contact($design_id) {
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

    $exists = get_posts(array('post_type' => 'store-contact-data', 'posts_per_page' => 1, 'meta_key' => 'store', 'meta_value' => $store->ID));
    if(is_array($exists) && count($exists) > 0){
        $store_data_id = $exists[0]->ID;
        $contact_page_fields = get_field('contact_page_fields', $store_data_id);
        $display_info_field = get_field('display_info_field',  $store_data_id);
    }else{
        $contact_page_fields = '';
        $display_info_field = '';
    }

    $general_info_contact_data_fields = get_field('general_info_contact_data_fields', 'option');
    $contact_fields = '';
    foreach ($general_info_contact_data_fields as $k => $f){
        if($contact_page_fields != ''){
            $splitted_fields = explode(',', $contact_page_fields);
            if(in_array($f, $splitted_fields)){
                $contact_fields .= '
                    <div class="form-check">
                        <input type="checkbox" id="field-info-general-contact-field-'.$k.'" checked name="field-info-general-contact-field[]" class="form-check-input field-info-general-contact-field" value="'.$f.'">
                        <label class="form-check-label" for="field-info-general-contact-field-'.$k.'">
                            '.$f.'
                        </label>
                    </div>
                ';
            }else{
                $contact_fields .= '
                    <div class="form-check">
                        <input type="checkbox" id="field-info-general-contact-field-'.$k.'" name="field-info-general-contact-field[]" class="form-check-input field-info-general-contact-field" value="'.$f.'">
                        <label class="form-check-label" for="field-info-general-contact-field-'.$k.'">
                            '.$f.'
                        </label>
                    </div>
                ';
            }
        }else{
            $contact_fields .= '
                <div class="form-check">
                    <input type="checkbox" id="field-info-general-contact-field-'.$k.'" name="field-info-general-contact-field[]" class="form-check-input field-info-general-contact-field" value="'.$f.'">
                    <label class="form-check-label" for="field-info-general-contact-field-'.$k.'">
                        '.$f.'
                    </label>
                </div>
            ';
        }
    }

    // delete_option('contact-info-'.$store->ID);
    $is_disabled = get_option('contact-info-'.$store->ID);

    return '
        <form id="store-info-contact-data" method="post" enctype="multipart/form-data">
            <div class="form-loader" style="display: none;"></div>
            <input type="hidden" name="action" value="ws" />
            <input type="hidden" name="disable" value="false" />
            <input type="hidden" name="lang" value="'.ICL_LANGUAGE_CODE.'" />
            <input type="hidden" name="wsa" value="save-store-info-contact-data" />
            <input type="hidden" name="store_id" value="'.$store->ID.'" />

            <div class="row">
                <div class="pe-md-5 pe-sm-auto pe-auto col-md-8 col-sm-12 col-12 main-column position-relative '.($is_disabled === false || $is_disabled == 0 ? '' : 'disabled').'">
                    <div class="disabled-overlay"></div>
                    <h3 class="mb-4"><small>'.__('Datos de contacto', 'xopifier').'</small></h3>
                    <div class="field mb-3">
                        <p class="form-label mb-3 w-100">'.__('¿Qué información vas a pedir a tus clientes cuando te contacten?', 'xopifier').'</p>
                        <div class="input-group flex-column">
                            '.$contact_fields.'
                        </div>
                        <span class="error"></span>
                    </div>
                    <div class="field mb-3">
                        <label class="form-label w-100" for="field-store-info-contact-display-info">'.__('¿Qué información de contacto de tu tienda deseas mostrar a tus clientes?', 'xopifier').'</label>
                        <p class="mb-3 small d-none">'.__('Adicional a la forma de contacto, dinos si quieres mostrar alguna dirección física de tu tienda, teléfono y/o E-mail. Si quieres mostrar el mapa de ubicación, agrega el link de Google Maps.', 'xopifier').'</p>
                        <textarea class="form-control mt-2 '.($display_info_field != '' ? 'valid' : '').'" id="field-store-info-contact-display-info" name="field-store-info-contact-display-info" placeholder="'.__('Ej: teléfono, E-mail o dirección física. Escribe lo que desees aquí', 'xopifier').'">'.$display_info_field.'</textarea>
                        <span class="error"></span>
                    </div>
                </div>
                <div class="col-md-4 col-sm-12 col-12 side-column mt-md-0 mt-sm-4 mt-4n">
                    <div class="sticky-top">
                        <button type="button" class="btn btn-primary btn-toggle-contact-section d-flex flex-row align-items-center justify-content-center gap-2 '.($is_disabled === false || $is_disabled == 0 ? 'is_active' : 'is_inactive').'">
                            
                            <svg class="remove '.($is_disabled === false || $is_disabled == 0 ? '' : 'd-none').'" width="18px" height="18px" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9 0C4.02944 0 0 4.02944 0 9C0 13.9706 4.02944 18 9 18C13.9706 18 18 13.9706 18 9C18 4.02944 13.9706 0 9 0Z" fill="#00B231"/>
                                <path d="M14 8V10H4V8H14Z" fill="white"/>
                            </svg>

                            <svg class="add '.($is_disabled === false || $is_disabled == 0 ? 'd-none' : '').'" width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9 0C4.02944 0 0 4.02944 0 9C0 13.9706 4.02944 18 9 18C13.9706 18 18 13.9706 18 9C18 4.02944 13.9706 0 9 0Z" fill="#00B231"/>
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M10.1667 4H8.16667V8.16667H4V10.1667H8.16667V14H10.1667V10.1667H14V8.16667H10.1667V4Z" fill="white"/>
                            </svg>

                            <span class="exclude '.($is_disabled === false || $is_disabled == 0 ? '' : 'd-none').'">'.__('No incluir Datos de contacto', 'xopifier').'</span>
                            <span class="include '.($is_disabled === false || $is_disabled == 0 ? 'd-none' : '').'">'.__('Incluir Datos de contacto', 'xopifier').'</span>

                        </button>
                        <div class="message-on" '.($is_disabled === false || $is_disabled == 0 ? '' : 'style="display: none;"').'>
                            <p class="small text-center mt-2 px-lg-5 px-md-3 px-sm-0 px-0">'.__('En caso no te interese o prefieras crearla después por tu cuenta en Shopify.', 'xopifier').'</p>
                        </div>
                        <div class="message-off" '.($is_disabled === false || $is_disabled == 0 ? 'style="display: none;"' : '').'>
                            <div class="form-tip off text-center">
                                <p class="text-center">'.__('Estás decidiendo no incluir esta información con Xopifier. Puedes crearla luego directamente en Shopify si así lo deseas.', 'xopifier').'</p>
                                <button type="button" class="btn btn-primary btn-continue">'.__('Ok, continuar', 'xopifier').'</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 bottom-column" '.($is_disabled === false || $is_disabled == 0 ? '' : 'style="display:none;"').'>
                    <hr class="my-5">
                    <div class="d-flex flex-column align-items-center justify-content-center text-center">
                        <button type="submit" class="btn btn-primary btn-save-store-contact-info w-auto '.($is_disabled === false || $is_disabled == 0 ? '' : 'disabled').'">'.__('Guardar y continuar', 'xopifier').'</button>
                        <span class="msg mt-3" style="display:none;"></span>
                    </div>
                </div>
            </div>
        </form>
    ';
}
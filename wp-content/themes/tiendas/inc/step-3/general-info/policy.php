<?php
function step_3_tabs_info_policy($design_id) {

    global $wpdb;

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
    
    $exists = get_posts(array('post_type' => 'store-policy-data', 'meta_key' => 'store', 'meta_value' => $store->ID));
    if(is_array($exists) && count($exists) > 0){
        $store_data_id = $exists[0]->ID;
        $empty = false;
        
        //privacy policy
        $policy_active = get_field('policy_active', $store_data_id);
        $policy_active_group = get_field('policy_active_group', $store_data_id);

        $policy_inactive = get_field('policy_inactive', $store_data_id);
        $policy_inactive_group = get_field('policy_inactive_group', $store_data_id);

        $billing_address = get_field('billing_address', $store_data_id);
    }else{
        $empty = true;
        
        //privacy policy
        $policy_active = '';
        $policy_active_group = '';

        $policy_inactive = '';
        $policy_inactive_group = '';

        $billing_address = '';
    }

    $user = get_field('user', $store->ID);
    $email = $user['user_email'];
    $storeName = get_field('current_store_name', $store->ID);

    $_files = '';

    if(is_array($policy_active_group) && count($policy_active_group) > 0){
        if(is_array($policy_active_group['files_policy_terms']) && count($policy_active_group['files_policy_terms']) > 0){
            foreach($policy_active_group['files_policy_terms'] as $file){
                if($file['file'] !== false){
                    $_files .= $file['file']['url'];
                }
            }
        }
    }

    // delete_option('policy-info-'.$store->ID);
    $is_disabled = get_option('policy-info-'.$store->ID);
    
    return '
        <form id="store-info-policy" method="post" enctype="multipart/form-data">
            <div class="form-loader" style="display: none;"></div>
            <input type="hidden" name="action" value="ws" />
            <input type="hidden" name="disable" value="false" />
            <input type="hidden" name="lang" value="'.ICL_LANGUAGE_CODE.'" />
            <input type="hidden" name="wsa" value="save-store-info-policy" />
            <input type="hidden" name="store_id" value="'.$store->ID.'" />

            <div class="row">
                <div class="pe-md-5 pe-sm-auto pe-auto col-md-8 col-sm-12 col-12 main-column position-relative '.($is_disabled === false || $is_disabled == 0 ? '' : 'disabled').'">
                    <div class="disabled-overlay"></div>
                    <h3 class="mb-4"><small>'.__('Políticas y términos de tu tienda', 'xopifier').'</small></h3>
                    <p class="form-label w-100">'.__('Como parte de nuestro servicio, crearemos por ti la siguiente información:', 'xopifier').'</p>
                    <ul class="mb-4 checks">
                        <li>
                            '.__('Política de privacidad', 'xopifier').'
                        </li>
                        <li>
                            '.__('Términos del servicio', 'xopifier').'
                        </li>
                    </ul>
                    <p class="form-label mb-0 w-100">'.__('Confirma o actualiza los datos de contacto que se usarán para políticas y términos:', 'xopifier').'</p>
                    <p class="mb-3 small">'.__('Se usarán para las comunicaciones de carácter oficial establecida por la Ley', 'xopifier').'</p>
                    <div class="field mb-0">
                        <label class="form-label small" for="field-store-info-policy-email">'.__('E-Mail:', 'xopifier').'</label>
                        <input type="text" class="form-control email" value="'.$email.'" id="field-store-info-policy-email" name="field-store-info-policy-email">
                        <span class="error"></span>
                    </div>
                    <div class="field mb-0">
                        <label class="form-label small" for="field-store-info-policy-name">'.__('Nombre de la tienda:', 'xopifier').'</label>
                        <input type="text" class="form-control" value="'.$storeName.'" id="field-store-info-policy-name" name="field-store-info-policy-name">
                        <span class="error"></span>
                    </div>
                    <div class="field mb-0">
                        <label class="form-label small" for="field-store-info-policy-address">'.__('Dirección de facturación:', 'xopifier').'</label>
                        <input type="text" class="form-control" value="'.$billing_address.'" id="field-store-info-policy-address" name="field-store-info-policy-address">
                        <span class="error"></span>
                    </div>
                    <div class="field mb-3">
                        <p class="form-label mb-3 w-100">'.__('Adicionalmente te recomendamos definir Políticas de Envíos y Devoluciones.', 'xopifier').'</p>
                        <div class="d-flex align-items-start mb-4">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" '.($policy_active ? 'checked' : '').' name="field-store-info-policy-option" id="field-store-info-policy-active" value="active-policy-group">
                                <label class="form-check-label" for="field-store-info-policy-active">'.__('Ya tengo mis políticas de Envíos y Devoluciones.', 'xopifier').'</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" '.($policy_inactive ? 'checked' : '').' name="field-store-info-policy-option" id="field-store-info-policy-inactive" value="inactive-policy-group">
                                <label class="form-check-label" for="field-store-info-policy-inactive">'.__('Aún no las tengo y deseo que las creen por mí.', 'xopifier').'</label>
                            </div>
                        </div>
                        <div class="policy-group active-policy-group" '.($policy_active ? '' : 'style="display: none;').'">
                            <div class="field mb-2">
                                <label class="form-label small" for="field-store-info-policy-privacy-url">'.__('Escribe la dirección (url) de donde podamos extraerlas:', 'xopifier').'</label>
                                <input type="text" class="form-control url" value="'.(is_array($policy_active_group) && !empty($policy_active_group) ? $policy_active_group['url_privacy_policy'] : '').'" id="field-store-info-policy-privacy-url" name="field-store-info-policy-privacy-url" placeholder="Ej: https://www.link.com/politicas-de-privacidad">
                                <span class="error"></span>
                            </div>
                            <div class="field upload mb-2">
                                <label class="form-label small" for="field-store-info-policy-files">'.__('O, si las tienes en uno o más archivos:', 'xopifier').'</label>
                                <div class="field-upload-container field-upload-info-policy-files">
                                    <div class="field-upload-overlay"></div>
                                    <div class="field-upload-field">
                                        <input type="file" accept=".doc,.docx,.pages,.odt,.txt" multiple class="field-upload-input d-none" id="field-store-info-policy-files" name="field-store-info-policy-files[]">
                                        <input type="hidden" id="field-store-policy-files-from-server" name="field-store-policy-files-from-server" value="'.($_files != '' ? '1' : '0').'">
                                        <img src="'.get_template_directory_uri().'/img/upload-to-cloud.svg" class="field-upload-img" />
                                        <p class="btn-choose">'.__('Arrastra aquí los archivos o <span>selecciónalos de tu computadora</span>', 'xopifier').'</p>
                                        <p><small>'.__('Puedes subir hasta 5 archivos .DOC, .DOCX, .PAGES, .ODT o .TXT', 'xopifier').'</small></p>
                                    </div>
                                    <div class="field-upload-content">
                                        <span class="image-preview-close" style="display:none;"><img src="'.get_template_directory_uri().'/img/close.svg" alt="close preview" /></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="policy-group inactive-policy-group" '.($policy_inactive ? '' : 'style="display: none;margin-bottom: -30px;').'">
                            <p class="form-label mb-4">'.__('Responde estas preguntas y crearemos esta información por ti:', 'xopifier').'</p>
                            <div class="row">
                                <div class="col-12">
                                    <div class="field mb-2">
                                        <label class="form-label small" for="field-store-info-policy-proccess-time">'.__('¿Cuándo se procesa un pedido?', 'xopifier').'</label>
                                        <select class="form-control w-100 pe-5 mt-0 form-select" id="field-store-info-policy-proccess-time" name="field-store-info-policy-proccess-time">
                                            <option value="*">'.__('Selecciona una opción', 'xopifier').'</option>
                                            <option '.(($is_disabled === false || $is_disabled == 0) && is_array($policy_inactive_group) ? ($policy_inactive_group['process_time'] == 'El mismo día de la compra' ? 'selected' : '') : '').' value="El mismo día de la compra">'.__('El mismo día de la compra', 'xopifier').'</option>
                                            <option '.(($is_disabled === false || $is_disabled == 0) && is_array($policy_inactive_group) ? ($policy_inactive_group['process_time'] == 'Al día siguiente de la compra' ? 'selected' : '') : '').' value="Al día siguiente de la compra">'.__('Al día siguiente de la compra', 'xopifier').'</option>
                                            <option '.(($is_disabled === false || $is_disabled == 0) && is_array($policy_inactive_group) ? ($policy_inactive_group['process_time'] == 'Dos días después de la compra' ? 'selected' : '') : '').' value="Dos días después de la compra">'.__('Dos días después de la compra', 'xopifier').'</option>
                                        </select>
                                        <span class="error"></span>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="field mb-2">
                                        <label class="form-label small" for="field-store-info-policy-delivery-time">'.__('¿Cuánto tiempo le toma a tu cliente recibir su pedido?', 'xopifier').'</label>
                                        <select class="form-control w-100 pe-5 mt-0 form-select" id="field-store-info-policy-delivery-time" name="field-store-info-policy-delivery-time">
                                            <option value="*">'.__('Selecciona una opción', 'xopifier').'</option>
                                            <option '.(($is_disabled === false || $is_disabled == 0) && is_array($policy_inactive_group) ? ($policy_inactive_group['delivery_time'] == 'Menos de 1 día' ? 'selected' : '') : '').' value="Menos de 1 día">'.__('Menos de 1 día', 'xopifier').'</option>
                                            <option '.(($is_disabled === false || $is_disabled == 0) && is_array($policy_inactive_group) ? ($policy_inactive_group['delivery_time'] == 'Entre 1 y 2 días' ? 'selected' : '') : '').' value="Entre 1 y 2 días">'.__('Entre 1 y 2 días', 'xopifier').'</option>
                                            <option '.(($is_disabled === false || $is_disabled == 0) && is_array($policy_inactive_group) ? ($policy_inactive_group['delivery_time'] == 'Entre 3 y 4 días' ? 'selected' : '') : '').' value="Entre 3 y 4 días">'.__('Entre 3 y 4 días', 'xopifier').'</option>
                                            <option '.(($is_disabled === false || $is_disabled == 0) && is_array($policy_inactive_group) ? ($policy_inactive_group['delivery_time'] == '5 días o más' ? 'selected' : '') : '').' value="5 días o más">'.__('5 días o más', 'xopifier').'</option>
                                            <option '.(($is_disabled === false || $is_disabled == 0) && is_array($policy_inactive_group) ? ($policy_inactive_group['delivery_time'] == 'Otro' ? 'selected' : '') : '').' value="Otro">'.__('Otro', 'xopifier').'</option>
                                        </select>
                                        <span class="error"></span>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="field mb-2">
                                        <label class="form-label small" for="field-store-info-policy-taxes">'.__('¿Se le cobrarán impuestos y aduanas?', 'xopifier').'</label>
                                        <select class="form-control w-100 pe-5 mt-0 form-select" id="field-store-info-policy-taxes" name="field-store-info-policy-taxes">
                                            <option value="*">'.__('Selecciona una opción', 'xopifier').'</option>
                                            <option '.(($is_disabled === false || $is_disabled == 0) && is_array($policy_inactive_group) ? ($policy_inactive_group['taxes'] == 'No. Bajo ninguna circunstancia' ? 'selected' : '') : '').' value="No. Bajo ninguna circunstancia">'.__('No. Bajo ninguna circunstancia', 'xopifier').'</option>
                                            <option '.(($is_disabled === false || $is_disabled == 0) && is_array($policy_inactive_group) ? ($policy_inactive_group['taxes'] == 'Sí. Siempre' ? 'selected' : '') : '').' value="Sí. Siempre">'.__('Sí. Siempre', 'xopifier').'</option>
                                            <option '.(($is_disabled === false || $is_disabled == 0) && is_array($policy_inactive_group) ? ($policy_inactive_group['taxes'] == 'Sí. Pero sólo por compras superiores a XX' ? 'selected' : '') : '').' value="Sí. Pero sólo por compras superiores a XX">'.__('Sí. Pero sólo por compras superiores a XX', 'xopifier').'</option>
                                            <option '.(($is_disabled === false || $is_disabled == 0) && is_array($policy_inactive_group) ? ($policy_inactive_group['taxes'] == 'Sí. Pero sólo en productos tipo ZZZ' ? 'selected' : '') : '').' value="Sí. Pero sólo en productos tipo ZZZ">'.__('Sí. Pero sólo en productos tipo ZZZ', 'xopifier').'</option>
                                            <option '.(($is_disabled === false || $is_disabled == 0) && is_array($policy_inactive_group) ? ($policy_inactive_group['taxes'] == 'Otro' ? 'selected' : '') : '').' value="Otro">'.__('Otro', 'xopifier').'</option>
                                        </select>
                                        <span class="error"></span>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label small mb-3 d-block">'.__('¿Aceptas devoluciones?', 'xopifier').'</label>
                                    <div class="d-flex align-items-start mb-4">
                                        <div class="form-check form-check-inline" style="width: 50px;">
                                            <input class="form-check-input" type="radio" '.(($is_disabled === false || $is_disabled == 0) && is_array($policy_inactive_group) ? ($policy_inactive_group['devolutions'] == 'Si' ? 'checked' : '') : '').' name="field-store-info-policy-devolutions" id="field-store-info-policy-devolutions-1" value="Si">
                                            <label class="form-check-label" for="field-store-info-policy-devolutions-1">'.__('Si', 'xopifier').'</label>
                                        </div>
                                        <div class="form-check form-check-inline" style="width: 50px;">
                                            <input class="form-check-input" type="radio" '.(($is_disabled === false || $is_disabled == 0) && is_array($policy_inactive_group) ? ($policy_inactive_group['devolutions'] == 'No' ? 'checked' : '') : '').' name="field-store-info-policy-devolutions" id="field-store-info-policy-devolutions-2" value="No">
                                            <label class="form-check-label" for="field-store-info-policy-devolutions-2">'.__('No', 'xopifier').'</label>
                                        </div>
                                    </div>
                                    <div class="field devolutions-conditions" style="display: none;">
                                        <label class="form-label small w-100 mb-2" for="field-store-info-policy-devolutions-conditions">'.__('Describe las condiciones y plazos para devolver un producto:', 'xopifier').'</label>
                                        <textarea class="form-control mt-2" id="field-store-info-policy-devolutions-conditions" name="field-store-info-policy-devolutions-conditions" placeholder="">'.(($is_disabled === false || $is_disabled == 0) && is_array($policy_inactive_group) ? $policy_inactive_group['devolutions_conditions'] : '').'</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-sm-12 col-12 side-column mt-md-0 mt-sm-4 mt-4">
                    <div class="sticky-top">
                        <button type="button" class="btn btn-primary btn-toggle-policy-section d-flex flex-row align-items-center justify-content-center gap-2 '.(($is_disabled === false || $is_disabled == 0) ? 'is_active' : 'is_inactive').'">
                            
                            <svg class="remove '.($is_disabled === false || $is_disabled == 0 ? '' : 'd-none').'" width="18px" height="18px" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9 0C4.02944 0 0 4.02944 0 9C0 13.9706 4.02944 18 9 18C13.9706 18 18 13.9706 18 9C18 4.02944 13.9706 0 9 0Z" fill="#00B231"/>
                                <path d="M14 8V10H4V8H14Z" fill="white"/>
                            </svg>

                            <svg class="add '.($is_disabled === false || $is_disabled == 0 ? 'd-none' : '').'" width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9 0C4.02944 0 0 4.02944 0 9C0 13.9706 4.02944 18 9 18C13.9706 18 18 13.9706 18 9C18 4.02944 13.9706 0 9 0Z" fill="#00B231"/>
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M10.1667 4H8.16667V8.16667H4V10.1667H8.16667V14H10.1667V10.1667H14V8.16667H10.1667V4Z" fill="white"/>
                            </svg>

                            <span class="exclude '.($is_disabled === false || $is_disabled == 0 ? '' : 'd-none').'">'.__('No incluir Políticas y términos', 'xopifier').'</span>
                            <span class="include '.($is_disabled === false || $is_disabled == 0 ? 'd-none' : '').'">'.__('Incluir Políticas y términos', 'xopifier').'</span>

                        </button>
                        <div class="message-on" '.($is_disabled === false || $is_disabled == 0 ? '' : 'style="display: none;"').'>
                            <p class="small text-center mt-2 px-lg-5 px-md-3 px-sm-0 px-0">'.__('En caso que prefieras crearla después por tu cuenta en Shopify', 'xopifier').'</p>
                            <div class="form-tip">
                                <img src="'.get_template_directory_uri().'/img/info.svg'.'" class="form-tip-img" />
                                <p class="form-label small mb-1">'.__('¿Por qué incluir esta información?', 'xopifier').'</p>
                                <p>'.__('Es información importante de carácter legal. Como parte de nuestro servicio, crearemos por ti un contenido estándar, a partir de los datos que te pedimos.', 'xopifier').'</p>
                                <p class="form-label small mb-1">'.__('Muy importante:', 'xopifier').'</p>
                                <p>'.__('Revisa este texto y contrástalo luego con las exigencias legales de tu rubro y región. Se trata de un estándar base, que no incluye un análisis legal caso a caso. Y recuerda que siempre lo podrás modificar luego directamente en Shopify.', 'xopifier').'</p>
                            </div>
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
                        <button type="submit" class="btn btn-primary btn-save-store-policy-info w-auto disabled">'.__('Guardar y continuar', 'xopifier').'</button>
                        <span class="msg mt-3" style="display:none;"></span>
                    </div>
                </div>
            </div>
        </form>
    ';
}
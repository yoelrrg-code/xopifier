<?php
autoloadPHP(__DIR__ . '/general-info');
autoloadPHP(__DIR__ . '/products');
autoloadPHP(__DIR__ . '/promos');
autoloadPHP(__DIR__ . '/languages');

function step_3_welcome($form, $design_id) {

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
    $user = get_field('user', $store->ID);

    $title = str_replace('{{clientname}}', $user['user_firstname'], $form['title']);

    $products_qty = get_field('products_qty', $store->ID);

    $categories = get_field('current_store_product_categories', $store->ID);
    $categories_html = '<ul class="ps-4 mb-3">';
    if(is_array($categories) and count($categories) > 0) {
        foreach($categories as $k => $category) {
            $categories_html .= '<li>'.$category['category'].'</li>';
        }
    }
    $categories_html .= '</ul>';

    $step3_status = get_step3_status($store->ID);
    $tab_status = get_step3_tab_status($step3_status, 'products-tab', true);

    // var_dump($step3_status);

    if($tab_status != ''){
        return '';
    }else{
        return '
            <div class="sub-step welcome-step-3 d-none">
                <div class="row">
                    <div class="col-12 px-0">
                        
                        <div class="row box">
                            <div class="text-center col-12 px-0">
                                '.($form['image'] != '' ? '<img src="'.$form['image']['url'].'" alt="main image" class="step-main-image mb-3" />' : '').'
                                '.($form['title'] != '' ? '<h3 class="small step-title">'.$title.'</h3>' : '').'
                                '.($form['description'] != '' ? '<div class="step-description">'.$form['description'].'</div>' : '').'
                                <div class="cp-contaniner text-start">
                                    <div class="c-container">
                                        <h3 class="smaller">'.__('Categorías', 'xopifier').'</h3>
                                        <p class="mb-1">'.__('Estas son las categorías que definiste:', 'xopifier').'</p>
                                        '.($categories_html != '<ul class="ps-4 mb-3"></ul>' ? $categories_html : '<p class="fw-bold text-danger mt-3">'.__('No has definido categorías', 'xopifier').'</p>').'
                                        <p>'.__('Si lo necesitas, puedes', 'xopifier').' <a href="#gotocategories" class="direct-link fw-semibold">'.__('cambiar o agregar categorías', 'xopifier').'</a></p>
                                    </div>
                                    <div class="p-container">
                                        <h3 class="smaller">'.__('Productos', 'xopifier').'</h3>
                                        <p class="mb-1">'.__('Actualmente tienes <b>', 'xopifier').' '.$products_qty.' '.__('productos</b> en tu lista inicial. <br>Ahora podrás:', 'xopifier').'</p>
                                        <ul class="mb-4 ps-4">
                                            <li>'.__('Agregar más productos uno por uno', 'xopifier').'</li>
                                            <li>'.__('O cargarlos en bloque con un archivo Excel', 'xopifier').'</li>
                                        </ul>
                                        <button class="btn btn-primary" href="#gotoproducts" title="'.__('Agregar productos', 'xopifier').'">'.__('Agregar productos', 'xopifier').'</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        ';
    }
}

function step_3_finish($form, $design_id) {

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

    return '
        <div class="sub-step finish-step-3" design-id="'.$design_id.'" store-id="'.$store->ID.'" style="display:none;">
            <div class="row">
                <div class="col-12">
                    
                    <div class="row box">
                        <div class="text-center col-12">
                            '.($form['image'] != '' ? '<img src="'.$form['image']['url'].'" alt="main image" class="step-main-image" />' : '').'
                            '.($form['title'] != '' ? '<h3 class="step-title">'.$form['title'].'</h3>' : '').'
                            '.($form['description'] != '' ? '<div class="step-description">'.$form['description'].'</div>' : '').'
                            <div class="d-flex gap-5 justify-content-center align-items-center">
                                '.($form['button'] != '' ? '<button class="btn btn-primary" href="'.$form['button']['url'].'" target="'.$form['button']['target'].'" title="'.$form['button']['title'].'">'.$form['button']['title'].'</button>' : '').'
                                '.($form['button_2'] != '' ? '<button class="btn btn-primary" href="'.$form['button_2']['url'].'" target="'.$form['button_2']['target'].'" title="'.$form['button_2']['title'].'">'.$form['button_2']['title'].'</button>' : '').'
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    ';

}

function get_step3_status($store_id){
    $service_settings = get_field('service_settings', 'option');
    $services_list = isset($service_settings['services']) && is_array($service_settings['services']) ? $service_settings['services'] : array();
    
    $services_tabs = array();
    foreach ($services_list as $service) {
        if (isset($service['type'], $service['id']) && $service['type'] == 'extra' && $service['id'] != 'payment_methods') {
            $services_tabs['info-service-' . $service['id'] . '-tab'] = '';
        }
    }

    $step3_status = get_option('step3_status', array());
    if (!is_array($step3_status)) {
        $step3_status = array();
    }

    if (!isset($step3_status[$store_id])) {
        $step3_status[$store_id] = array(
            'info-tab' => array(
                'main' => '',
                'info-store-tab' => '',
                'info-contact-tab' => '',
                'info-policy-tab' => '',
            ) + $services_tabs,
            'products-tab' => array(
                'main' => '',
                'products-categories-tab' => '',
                'products-products-tab' => '',
                'products-extra-info-tab' => '',
            ),
            'promos-tab' => array(
                'main' => '',
                'promos-ads-tab' => '',
                'promos-discount-tab' => '',
            ),
            'other-tab' => array(
                'main' => '',
                'other-lang-tab' => '',
            ),
        );

        update_option('step3_status', $step3_status);
    }

    return $step3_status;
}

function get_step3_tab_status($step3_status, $tab, $main = false) {
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

    // var_dump($step3_status[$store_id]);

    if($main){
        $tab_statuses = @$step3_status[$store_id][$tab];
        if(is_array($tab_statuses) and count($tab_statuses) > 0){
            if(isset($tab_statuses['main'])){
                return $tab_statuses['main'];
            }else{
                return '';
            }
        }else{
            return '';
        }
    }else{
        if(isset($step3_status[$store_id]) and is_array($step3_status[$store_id]) && count($step3_status[$store_id]) > 0){
            foreach($step3_status[$store_id] as $k => $status) {
                foreach($status as $l => $sta) {
                    if($l == $tab){
                        return $sta;
                    }
                }
            }
        }
    }
}

function step_3_tabs($design_id){
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

    $step3_status = get_step3_status($store_id);

    // echo "<pre style='margin-top:200px;'>";
    // var_dump($step3_status);
    // var_dump(get_step3_tab_status($step3_status, 'products-tab', true));
    // echo "</pre>";

    return '
        <div class="sub-step tabs-step-3">
            <div class="row">
                <div class="col-12">
                    
                    <div class="designs">

                        <ul class="nav nav-tabs d-flex gap-1 justify-content-between" id="myTabStep3" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="main-tab nav-link active w-100 '.get_step3_tab_status($step3_status, 'info-tab', true).'" id="info-tab" data-bs-toggle="tab" data-bs-target="#info" type="button" role="tab" aria-controls="info" aria-selected="true">
                                    '.__('Información general', 'xopifier').'
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="main-tab nav-link w-100 '.get_step3_tab_status($step3_status, 'products-tab', true).'" id="products-tab" data-bs-toggle="tab" data-bs-target="#products" type="button" role="tab" aria-controls="products" aria-selected="false">
                                    '.__('Productos', 'xopifier').'
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="main-tab nav-link w-100 '.get_step3_tab_status($step3_status, 'promos-tab', true).'" id="promos-tab" data-bs-toggle="tab" data-bs-target="#promos" type="button" role="tab" aria-controls="promos" aria-selected="false">
                                    '.__('Promos y descuentos', 'xopifier').'
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="main-tab nav-link w-100 extra '.get_step3_tab_status($step3_status, 'other-tab', true).'" id="other-tab" data-bs-toggle="tab" data-bs-target="#other" type="button" role="tab" aria-controls="other" aria-selected="false">
                                    '.__('Adicionales', 'xopifier').'
                                </button>
                            </li>
                        </ul>
                        <div class="tab-content" id="myTabContentStep3">
                            <div class="tab-pane fade show" id="products" role="tabpanel" aria-labelledby="products-tab" tab="products-tab">
                                '.step_3_tabs_products($design_id).'
                            </div>
                            <div class="tab-pane fade show active" id="info" role="tabpanel" aria-labelledby="info-tab" tab="info-tab">
                                '.step_3_tabs_info($design_id).'
                            </div>
                            <div class="tab-pane fade" id="promos" role="tabpanel" aria-labelledby="promos-tab" tab="promos-tab">
                                '.step_3_tabs_promos($design_id).'
                            </div>
                            <div class="tab-pane fade" id="other" role="tabpanel" aria-labelledby="other-tab" tab="other-tab">
                                '.step_3_tabs_other_languages($design_id).'
                            </div>
                        </div>

                    </div>

                    <div class="extra-form-modal finish-step3-form-modal" style="display:none;">
                        <div class="extra-form-modal-box position-relative">
                            <img src="'.get_template_directory_uri().'/img/close-gray.svg" class="close-modal-box" />
                            <img src="'.get_template_directory_uri().'/img/mona-step3-done.png" class="w-100 mb-4" />
                            <div class="text-center mb-3 px-5">
                                <h3 class="small">'.__('¡Felicidades! <br>Ya completaste la información de tu tienda', 'xopifier').'</h3>
                                <p class="px-md-5 px-sm-0 px-0">'.__('Ahora puedes enviarla usando el botón <b>“Enviar información de mi tienda”</b>, que ha quedado activo.', 'xopifier').'</p>
                            </div>
                            <div class="text-center d-flex align-items-center justify-content-center mb-4 pb-3">
                                <img class="finish-step3-arrow-down" src="'.get_template_directory_uri().'/img/down-arrow.svg" />
                            </div>
                        </div>
                    </div>

                    <div class="extra-form-modal batch-import-form-modal" style="display: none;">
                        <div class="extra-form-modal-box position-relative">
                            <div class="form-loader" style="display: none;"></div>
                            <div class="mb-3">
                                <h3 class="small mb-1">'.__('Importar productos en bloque', 'xopifier').'</h3>
                                <p class="small mb-3"><a href="'.site_url('/plantilla-de-referencia-xopifier.xlsx').'" class="small direct-link" target="_blank">'.__('Descarga nuestra plantilla de Excel', 'xopifier').'</a> '.__('y completa la información de tus productos siguiendo estos requisitos.', 'xopifier').'</p>
                                <div class="d-flex align-items-start justify-content-start flex-wrap mb-2 w-100 gap-5 d-none">
                                    <div class="form-check form-check-inline w-auto m-0 align-items-center">
                                        <input class="form-check-input" type="radio" name="field-batch-import-type" id="field-batch-import-type-file" value="file">
                                        <label class="form-check-label w-100 small fw-semibold" for="field-batch-import-type-file">'.__('Archivo Excel (.xlsx, .csv)', 'xopifier').'</label>
                                    </div>
                                    <div class="form-check form-check-inline w-auto m-0 align-items-center">
                                        <input class="form-check-input" type="radio" name="field-batch-import-type" id="field-batch-import-type-gsheet" value="sheet">
                                        <label class="form-check-label w-100 small fw-semibold" for="field-batch-import-type-gsheet">'.__('Link al documento de Google Sheet', 'xopifier').'</label>
                                    </div>
                                </div>
                            </div>

                            <div class="excel-form">
                                <form id="store-batch-file-import-products-form" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="action" value="ws" />
                                    <input type="hidden" name="wsa" value="batch-file-import-store-products" />
                                    <input type="hidden" name="store_id" value="'.$store_id.'" />
                                    <input type="hidden" name="design_id" value="'.$design_id.'" />
                                    <input type="hidden" name="lang" value="'.ICL_LANGUAGE_CODE.'" />
                                    <input type="hidden" name="batch-import-type" value="file" />
                                    <input type="hidden" name="nonce" value="'.wp_create_nonce(xopifier_TITLE_FOR_NONCE).'" />

                                    <div class="field upload mb-2">
                                        <div class="field-upload-container field-upload-batch-files mt-0 px-5">
                                            <div class="field-upload-overlay"></div>
                                            <div class="field-upload-field">
                                                <input type="file" accept=".xlsx,.xls,.csv" class="field-upload-input d-none" id="field-store-batch-file" name="field-store-batch-file">
                                                <img src="'.get_template_directory_uri().'/img/upload-to-cloud.svg" class="field-upload-img" />
                                                <p class="btn-choose mb-0">'.__('Arrastra aquí tu archivo Excel o <span>selecciónalo de tu computadora</span>', 'xopifier').'</p>
                                                <p><small>'.__('(.XLXS, .CSV)', 'xopifier').'</small></p>
                                            </div>
                                            <div class="field-upload-content">
                                                <span class="image-preview-close" style="display:none;"><img src="'.get_template_directory_uri().'/img/close.svg" alt="close preview" /></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="file-requirements">
                                        <h3 class="mb-1"><span class="fw-semibold">'.__('Requisitos del archivo', 'xopifier').'</span></h3>
                                        <p class="small">'.__('Tu archivo Excel debe incluir estas columnas:', 'xopifier').'</p>
                                        <img src="'.get_template_directory_uri().'/img/excel-example.png" class="img-fluid" />
                                        <div class="d-flex align-items-center justify-content-end">
                                            <div class="d-flex align-items-center justify-content-end gap-2 mt-3 mb-0">
                                                <img src='.get_template_directory_uri().'/img/download-icon.svg" /> <a href="'.site_url('/plantilla-de-referencia-xopifier.xlsx').'" class="mb-0 fw-semibold direct-link">'.__('Descarga nuestra plantilla de Excel (.xlsx) de referencia', 'xopifier').'</a>
                                            </div>
                                        </div>
                                        <h3 class="mb-1"><span class="fw-semibold">'.__('Imágenes de productos', 'xopifier').'</span></h3>
                                        <ul class="ps-3">
                                            <li class="small">'.__('Todas las imágenes deben estar online (Google Drive, Dropbox o iCloud) y coloca el link de cada imagen en la columna “Imágenes” del Excel.', 'xopifier').'</li>
                                            <li class="small">'.__('Si un producto tiene varias imágenes, sepáralas con comas (,) en la misma celda.', 'xopifier').'</li>
                                            <li class="small">'.__('Si no tienes las imágenes publicadas online:', 'xopifier').'
                                                <ol class="ps-3">
                                                    <li class="small">'.__('Sube las imágenes a una carpeta en Google Drive, Dropbox o iCloud.', 'xopifier').'</li>
                                                    <li class="small">'.__('En la columna “Imágenes”, agrega el link directo a cada archivo.', 'xopifier').'</li>
                                                    <li class="small">'.__('Asegúrate de que cada link esté configurado como “Cualquiera con el enlace puede acceder”.', 'xopifier').'</li>
                                                </ol>
                                            </li>
                                        </ul>
                                    </div>

                                    <div class="d-flex align-items-center justify-content-center gap-3 mt-4 pt-2">
                                        <button class="btn btn-secondary btn-batch-close-modal" type="button">'.__('No importar productos en bloque', 'xopifier').'</button>
                                        <button type="submit" class="btn btn-primary btn-batch-import disabled">'.__('Guardar y continuar', 'xopifier').'</button>
                                    </div>
                                </form>
                            </div>

                            <div class="sheet-form" style="display:none;">
                                <form id="store-batch-sheet-import-products-form" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="action" value="ws" />
                                    <input type="hidden" name="wsa" value="batch-sheet-import-store-products" />
                                    <input type="hidden" name="store_id" value="'.$store_id.'" />
                                    <input type="hidden" name="design_id" value="'.$design_id.'" />
                                    <input type="hidden" name="lang" value="'.ICL_LANGUAGE_CODE.'" />
                                    <input type="hidden" name="batch-import-type" value="sheet" />
                                    <input type="hidden" name="nonce" value="'.wp_create_nonce(xopifier_TITLE_FOR_NONCE).'" />
                                
                                    <div class="field upload mb-0">
                                        <label class="form-label small" for="field-store-batch-url">'.__('Link al Google Sheet:', 'xopifier').'</label>
                                        <input type="text" class="form-control url" value="" id="field-store-batch-url" name="field-store-batch-url" placeholder="Ej: https://docs.google.com/spreadsheets/d/e/FILEID/pub?output=csv">
                                        <span class="error"></span>
                                    </div>

                                    <div class="file-requirements">
                                        <h3 class="mb-1"><span class="fw-semibold">'.__('Requisitos del archivo', 'xopifier').'</span></h3>
                                        <p class="small">'.__('Tu archivo debe incluir las siguientes columnas obligatorias y opcionales:', 'xopifier').'</p>
                                        <img src="'.get_template_directory_uri().'/img/excel-example.png" class="img-fluid" />
                                        <div class="d-flex align-items-center justify-content-end">
                                            <div class="d-flex align-items-center justify-content-end gap-2 mt-3 mb-0">
                                                <img src='.get_template_directory_uri().'/img/download-icon.svg" /> <a href="'.site_url('/plantilla-de-productos.xlsx').'" class="mb-0 fw-semibold">'.__('Descarga nuestra plantilla de Excel (.xlsx) de referencia', 'xopifier').'</a>
                                            </div>
                                        </div>
                                        <h3 class="mb-1"><span class="fw-semibold">'.__('Imágenes de productos', 'xopifier').'</span></h3>
                                        <ul class="ps-3">
                                            <li class="small">'.__('Todas las imágenes deben estar online y su link debe ir en la columna “Imágenes” del Excel/Google Sheet.', 'xopifier').'</li>
                                            <li class="small">'.__('Si ya tienes imágenes publicadas (ej: marketplace, RRSS, fabricante, etc.), copia el link de cada producto en la columna “Imágenes”.', 'xopifier').'</li>
                                            <li class="small">'.__('Si no tienes las imágenes online:', 'xopifier').'
                                                <ol class="ps-3">
                                                    <li class="small">'.__('Sube las imágenes a una carpeta en Google Drive, Dropbox o iCloud.', 'xopifier').'</li>
                                                    <li class="small">'.__('Comparte la carpeta con developer@xopifier.com (asegúrate de que el link sea accesible).', 'xopifier').'</li>
                                                    <li class="small">'.__('En la columna “Imágenes”, pon el link directo al archivo do a la carpeta compartida.', 'xopifier').'</li>
                                                </ol>
                                            </li>
                                            <li class="small">'.__('Alternativa: en lugar del link, puedes escribir solo el nombre exacto del archivo, siempre que coincida con el nombre de las imágenes en la carpeta compartida.', 'xopifier').'</li>
                                        </ul>
                                    </div>

                                    <div class="d-flex align-items-center justify-content-center gap-3 mt-4 pt-2">
                                        <button class="btn btn-secondary btn-batch-close-modal" type="button">'.__('No importar productos en bloque', 'xopifier').'</button>
                                        <button type="submit" class="btn btn-primary btn-batch-import disabled">'.__('Guardar y continuar', 'xopifier').'</button>
                                    </div>
                                </form>
                            </div>

                        </div>
                    </div>

                    <div class="finish-step3 d-flex align-items-center justify-content-center mt-5 mb-4">
                        <button class="btn btn-primary btn-store-info-finish d-flex pe-3 ps-5 align-items-center justify-content-center gap-4 disabled" type="button">
                            '.__('Enviar información de mi tienda', 'xopifier').'
                            <img src="'.get_template_directory_uri().'/img/step-done-white.svg" />
                        </button>
                    </div>

                </div>
            </div>
        </div>

        <div class="extra-form-modal reviews-extra-form-modal" style="display: none;">
            <div class="extra-form-modal-box position-relative">
                <div class="text-center mb-4">
                    <h3 class="small">'.__('¿Quieres quitar este extra?', 'xopifier').'</h3>
                    <p class="small">'.__('Si decides no incluirlo, se eliminará la información que hayas agregado.', 'xopifier').'</p>
                </div>
                <div class="text-center d-flex align-items-center justify-content-center gap-3">
                    <button class="btn btn-secondary btn-reviews-close-modal" type="button">'.__('Mantener este extra', 'xopifier').'</button>
                    <button type="button" class="btn btn-primary btn-toggle-reviews-section is_active">'.__('Sí, quitar Reseñas', 'xopifier').'</button>
                </div>
            </div>
        </div>

        <div class="extra-form-modal faqs-extra-form-modal" style="display: none;">
            <div class="extra-form-modal-box position-relative">
                <div class="text-center mb-4">
                    <h3 class="small">'.__('¿Quieres quitar este extra?', 'xopifier').'</h3>
                    <p class="small">'.__('Si decides no incluirlo, se eliminará la información que hayas agregado.', 'xopifier').'</p>
                </div>
                <div class="text-center d-flex align-items-center justify-content-center gap-3">
                    <button class="btn btn-secondary btn-faqs-close-modal" type="button">'.__('Mantener este extra', 'xopifier').'</button>
                    <button type="button" class="btn btn-primary btn-toggle-faqs-section is_active">'.__('Sí, quitar FAQs', 'xopifier').'</button>
                </div>
            </div>
        </div>

        <div class="extra-form-modal custom-extra-form-modal" style="display: none;">
            <div class="extra-form-modal-box position-relative">
                <div class="text-center mb-4">
                    <h3 class="small">'.__('¿Quieres quitar este extra?', 'xopifier').'</h3>
                    <p class="small">'.__('Si decides no incluirlo, se eliminará la información que hayas agregado.', 'xopifier').'</p>
                </div>
                <div class="text-center d-flex align-items-center justify-content-center gap-3">
                    <button class="btn btn-secondary btn-custom-close-modal" type="button">'.__('Mantener este extra', 'xopifier').'</button>
                    <button type="button" class="btn btn-primary btn-toggle-custom-page-section is_active">'.__('Sí, quitar Página perzonalizada', 'xopifier').'</button>
                </div>
            </div>
        </div>

        <div class="extra-form-modal lang-extra-form-modal" style="display: none;">
            <div class="extra-form-modal-box position-relative">
                <div class="text-center mb-4">
                    <h3 class="small">'.__('¿Quieres quitar este extra?', 'xopifier').'</h3>
                    <p class="small">'.__('Si decides no incluirlo, se eliminará la información que hayas agregado.', 'xopifier').'</p>
                </div>
                <div class="text-center d-flex align-items-center justify-content-center gap-3">
                    <button class="btn btn-secondary btn-lang-close-modal" type="button">'.__('Mantener este extra', 'xopifier').'</button>
                    <button type="button" class="btn btn-primary btn-toggle-languages-section is_active">'.__('Sí, quitar Otros Idiomas', 'xopifier').'</button>
                </div>
            </div>
        </div>
    ';

}

function step_3_shortcode(){
    global $current_user, $wpdb, $designId;

    $form_html = '';

    if(!is_user_logged_in() || in_array('administrator', $current_user->roles)){
        //formulario de login
        $form_html = step_2_generate_login_form($designId);
    }else{
        $form = get_field('message_step_3_1', 'option');
        $form_finish = get_field('message_step_3_completed', 'option');

        if(isset($designId)){            
            //mensage de bienvenida al paso 3
            $form_html = step_3_welcome($form, $designId);
            $form_html .= step_3_tabs($designId);
            $form_html .= step_3_finish($form_finish, $designId);
        }
    }

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

    $html = '
        <div id="steps" class="step-3 container-fluid px-0" design-id="'.$designId.'" store-id="'.$store->ID.'">
            <div class="row">
                <div class="col-12">
                    <div class="container">
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
add_shortcode("step3", "step_3_shortcode");

function find_active_service($service, $active_services){
    // var_dump($service, $active_services);
    foreach($active_services as $serv){
        if($serv['id'] == $service && $serv['active'] == 1){
            return 1;
        }
    }
    return 0;
}
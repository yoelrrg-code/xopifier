<?php
function step_2_generate_login_form($design_id) {
    return '
        <form autocomplete="false" id="form-login-step-2" action="" method="POST" enctype="multipart/form-data">
            <input name="action" type="hidden" value="ws">
            <input name="wsa" type="hidden" value="login">
            <input type="hidden" name="lang" value="'.ICL_LANGUAGE_CODE.'" />
            <input name="design_id" type="hidden" value="'.$design_id.'">
            <input type="hidden" name="nonce" value="'.wp_create_nonce(xopifier_TITLE_FOR_NONCE).'">
            <div class="user-form-modal">
                <div class="user-form-modal-box">
                    <div class="form-loader" style="display: none;"></div>
                    <h3><small>'.__('Accede a tu cuenta Xopifier').'</small></h3>
                    <p class="mt-2 mb-4">'.__('Accede para que puedas ver las propuestas de diseño de tu tienda y seleccionar la que más se acomode a tus necesidades.', 'xopifier').'</p>

                    <div class="field mb-3">
                        <label class="form-label" for="field-useremail">'.__('Email:', 'xopifier').'</label>
                        <input type="email" autocomplete="false" class="form-control required notverified" id="field-useremail" name="field-useremail" required />
                        <span class="error"></span>
                    </div>

                    <div class="field mb-3">
                        <label class="form-label" for="field-userpass">'.__('Contraseña:', 'xopifier').'</label>
                        <input type="password" autocomplete="new-password" class="form-control required notverified" id="field-userpass" name="field-userpass" required />
                        <span class="error"></span>
                    </div>

                    <div class="text-center">
                        <button class="btn btn-primary btn-login" name="" type="submit">'.__('Acceder a mi cuenta', 'xopifier').'</button>
                        <span class="error msg mt-4"></span>
                    </div>

                </div>
            </div>
        </form>
    ';
}

function step_2_completed_shortcode(){
    $form = get_field('message_step_2_completed', 'option');

    $form_html = '
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
add_shortcode("step2completed", "step_2_completed_shortcode");

function step_2_declined_shortcode(){
    $form_html = '
        <div class="sub-step">
            <div class="row">
                <div class="col-12">
                    <div class="row box">
                        <div class="text-center col-12">
                            <img src="'.get_template_directory_uri().'/img/design-declined.png" alt="main image" class="step-main-image" />
                            <h3 class="step-title">'.__('Diseño rechazado.', 'xopifier').'</h3>
                            <div class="step-description">'.__('Has rechazado la propuesta de diseño. Por favor, ponte en contacto con nuestro equipo si deseas solicitar ajustes o iniciar una nueva propuesta.', 'xopifier').'</div>
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
add_shortcode("step2declined", "step_2_declined_shortcode");

function step_2_shortcode(){
    global $current_user, $wpdb, $designId;

    if(!is_user_logged_in() || in_array('administrator', $current_user->roles)){
        // var_dump($current_user);
        //formulario de login
        $form_html = step_2_generate_login_form($designId);
    }else{
        $form = get_field('message_step_2_1', 'option');

        if(isset($designId)){
 	    	//obtengo los datos del diseno de la tienda
            $design = get_post($designId);
            $designs = get_field('designs', $design->ID);
            $password = get_field('store_password', $design->ID);
            $store = get_field('store', $design->ID);
            $storename = get_field('current_store_name', $store->ID);
            $tabs = '';
            $contents = '';
            $form_tip = '';

            $designs_popups = '';

            $title = str_replace("{{firstname}}", $current_user->first_name, $form['title']);
            $description = str_replace("{{storepassword}}", "<span class='store-password'>".$password."</span>", $form['description']);
            
            //mensage de bienvenida al paso 2
            $form_html = '
                <div class="sub-step welcome-step-2">
                    <div class="row">
                        <div class="col-12">
                            
                            <div class="row box">
                                <div class="text-center col-12">
                                    '.($form['image'] != '' ? '<img src="'.$form['image']['url'].'" alt="main image" class="step-main-image" />' : '').'
                                    '.($form['title'] != '' ? '<h3 class="step-title mb-3"><small>'.$title.'</small></h3>' : '').'
                                    '.($form['description'] != '' ? '<div class="step-description mb-5">'.$description.'</div>' : '').'
                                    '.($form['button'] != '' ? '<button class="btn btn-primary" href="'.$form['button']['url'].'" target="'.$form['button']['target'].'" title="'.$form['button']['title'].'">'.$form['button']['title'].'</button>' : '').'
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            ';

            $designs_thumbs = [];

            // if(ICL_LANGUAGE_CODE == 'en'){
            //     $faq_cat = 'step-2-en';
            // }else{
            //     $faq_cat = 'step-2';
            // }

            $faqs = get_posts(array('post_type' => 'faq', 'post_status' => 'publish', 'posts_per_page' => -1, 'tax_query' => array(
                array(
                    'taxonomy' => 'faq-category',
                    'field' => 'slug',
                    'terms'    => 'step-2',
                )
            )));

            $faqs_html = '';
			if(is_array($faqs) && count($faqs) > 0){
				foreach ($faqs as $key => $faq){
					$faqs_html .= '
						<div class="accordion-item">
							<h2 class="accordion-header" id="heading-'.$key.'">
								<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-'.$key.'" aria-expanded="false" aria-controls="collapse-'.$key.'">
									'.get_the_title($faq->ID).'
								</button>
							</h2>
							<div id="collapse-'.$key.'" class="accordion-collapse collapse aria-labelledby="heading-'.$key.'" data-bs-parent="#accordionFAQs">
								<div class="accordion-body">
									'.apply_filters('the_content', $faq->post_content).'
								</div>
							</div>
						</div>
					';
				}
			}

            // Hoist logo_simple fetch outside of the loop
            $logo_data = get_field('logo_simple', 'option');
            $logo = isset($logo_data['url']) ? $logo_data['url'] : '';

            //recorro el repeater de los disennos para generar los tabs y los contenidos
            if(is_array($designs) && count($designs) > 0){
                foreach($designs as $k => $d){
                    $tip = $d['tip'];

                    $homepage = $d['homepage'];

                    $designs_thumbs[] = '
                        <div class="design-thumb design-thumb-'.($k+1).'" style="display:none;">
                            <div class="thumb-img">
                                '.($homepage['image'] != '' ? '<img src="'.$homepage['image']['url'].'" alt="" />' : '').'
                            </div>
                            <div class="thumb-content d-flex flex-column">
                                <p>'.__('Propuesta', 'xopifier').' '.($k+1).': "'.$d['title'].'"</p>
                                <small>'.__('Vista parcial del diseño disponible. Una vez que hagas el primer pago podrás ver los diseños completos.', 'xopifier').'</small>
                            </div>
                        </div>
                    ';
                    
                    // $designs_popups .= '
                    //     <div class="design-popup design-popup-'.($k+1).'" design-popup="'.($k+1).'" design="design-popup-'.($k+1).'" style="display:none;">
                    //         <div class="design-popup-images">
                    //             <iframe src="'.$d['store_url'].'" class="design-popup-store"></iframe>
                    //         </div>
                    //         <div class="design-popup-navigation d-flex align-items-center justify-content-start">
                    //             <div class="d-flex align-items-center justify-content-end gap-2 text-end">
                    //                 <img src="'.get_template_directory_uri().'/img/down.svg" class="close-design"/>
                    //                 <a href="#goto-design">'.__('Regresar', 'xopifier').'</a>
                    //             </div>
                    //             <div class="d-flex align-items-center gap-2">
                    //                 <a href="#goto-design" class="goto-design d-flex align-items-center gap-2 fw-bold">
                    //                     <img src="'.$logo.'" alt="logo" class=""><div class="fw-bold">'.__('Propuesta', 'xopifier').' <span>1</span></div>
                    //                 </a> 
                    //                 <div class="d-flex align-items-center gap-3 ms-2 design-description"> <span class="v-separator"></span>  <span class="active-design">'.$homepage['description'].'</span></div>
                    //             </div>
                    //             <div class="d-flex align-items-center m-0 ms-auto">
                    //                 <button type="button" class="btn btn-primary btn-select-design" product-image-id="'.$homepage['image']['ID'].'" store-id="'.$store->ID.'" product-name="'.get_field('current_store_name', $store->ID).' ['.$d['title'].']" design="'.($k+1).'">'.__('Me quedo con esta opción', 'xopifier').'</button>
                    //             </div>
                    //         </div>
                    //     </div>
                    // ';

                    //genero el box del mensaje del tip
                    if($tip['title'] != ''){
                        $links = '';
                        if(is_array($tip['links'])){
                            foreach($tip['links'] as $link){
                                if($link['link'] != ''){
                                    $links .= '
                                        <p><b class="btn-view-store" design="design-popup-'.($k+1).'" store-url="'.$link['link']['url'].'">'.$link['link']['title'].'</b><br>'.$link['description'].'</p>
                                    ';
                                }
                            }
                        }
                        $tiptitle = str_replace("{{storepassword}}", "<span class='store-password-transparent'>".$password."</span>", $tip['title']);
                        $form_tip = '
                            <div class="form-tip">
                                <p class="form-tip-title mb-0">'.$tiptitle.'</p>
                                '.($tip['description'] != '' ? $tip['description'] : '').'
                                '.($links != '' ? $links : '').'
                                <div class="d-flex align-items-center gap-2 mt-4">
                                    <img src="'.get_template_directory_uri().'/img/lock.svg"/>
                                    <p class="mb-0">'.__('Usa el password:', 'xopifier').'</p>
                                    <span class="store-password-transparent">'.$password.'</span>
                                </div>
                            </div>
                        ';
                    }

                    //genero los tabs
                    if($k == 0){
                        $tabs .= '
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active w-100" id="design'.($k+1).'-tab" data-bs-toggle="tab" data-bs-target="#design'.($k+1).'" type="button" role="tab" aria-controls="design'.($k+1).'" aria-selected="true">'.__('Propuesta', 'xopifier').' '.($k+1).' </button>
                            </li>
                        ';

                        $content_class = 'show active';
                    }else{
                        $tabs .= '
                            <li class="nav-item" role="presentation">
                                <button class="nav-link w-100" id="design'.($k+1).'-tab" data-bs-toggle="tab" data-bs-target="#design'.($k+1).'" type="button" role="tab" aria-controls="design'.($k+1).'" aria-selected="false">'.__('Propuesta', 'xopifier').' '.($k+1).'</button>
                            </li>
                        ';

                        $content_class = '';
                    }

                    //genero los contenidos de los tabs
                    $contents .= '
                        <div class="tab-pane fade '.$content_class.'" id="design'.($k+1).'" role="tabpanel" aria-labelledby="design'.($k+1).'-tab">
                            <h3 class="smaller pb-3 mb-0 border-bottom border-bottom-2"><b>'.__('Propuesta', 'xopifier').' '.($k+1).':</b>&nbsp;'.$d['title'].'</h3>
                            <div class="tab-body-top">
                                <div class="row">
                                    <div class="col-lg-8 col-md-7 col-sm-12 col-12 pe-lg-5 pe-md-3 pe-sm-3 pe-3 order-md-1 order-sm-2 order-2">
                                        <figure class="btn-view-store" design="design-popup-'.($k+1).'" store-url="'.$d['store_url'].'">
                                            <img src="'.$homepage['image']['url'].'" class="img-fluid"/>
                                        </figure>
                                    </div>
                                    <div class="col-lg-4 col-md-5 col-sm-12 col-12 order-md-2 order-sm-1 order-1">
                                        <div class="sticky-top">
                                            '.$form_tip.'

                                            <div class="d-flex flex-column gap-3 mt-4 pt-2">
                                                <button type="button" class="btn btn-secondary btn-view-store view-more d-none fw-normal flex-row" design="design-popup-'.($k+1).'" store-url="'.$d['store_url'].'" hover-text="'.__('Utiliza el password:', 'xopifier').'&nbsp;<b>'.$password.'</b>" text="'.__('Ver esta propuesta', 'xopifier').'">'.__('Ver esta propuesta', 'xopifier').'</button>
                                                <button type="button" class="btn btn-primary btn-select-design-modal" product-image-id="'.$homepage['image']['ID'].'" store-id="'.$store->ID.'" product-name="'.get_field('current_store_name', $store->ID).' ['.$d['title'].']" design="'.($k+1).'">'.__('Me gusta esta propuesta', 'xopifier').'</button>
                                            </div>

                                            <p class="text-light text-center mt-4">'.__('<strong>¿Te gusta pero quieres hacer ajustes?</strong> No te preocupes, podrás comentar en el siguiente paso.', 'xopifier').'</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    ';
                }
            }

            // var_dump($designs);

            $form_html .= '
                <div class="sub-step tabs-step-2" style="display: none;">
                    <div class="row">
                        <div class="col-12">
                            
                            <div class="designs">
                                <ul class="nav nav-tabs d-flex justify-content-md-between justify-content-sm-start justify-content-start" id="myTabDesign" role="tablist">
                                    '.$tabs.'
                                </ul>
                                <div class="tab-content" id="myTabContent">
                                    '.$contents.'

                                    <div class="faqs">
                                        <h3 class="small text-center justify-content-center fw-semibold">'.get_field('title', 'faq_options').'</h3>
                                        <p class="text-center">'.get_field('subtitle', 'faq_options').'</p>
                                        <div class="accordion accordion-flush" id="accordionFAQs">
                                            '.$faqs_html.'
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="my-5 d-md-flex d-sm-block d-block text-center align-content-center justify-content-center gap-1 flex-wrap" style="line-height: 1.2;">
                                '.__('¿Ninguna opción te convenció? No hay problema, solo te pedimos que nos cuentes', 'xopifier').' <a class="btn-select-no-design direct-link" product-image-id="'.$homepage['image']['ID'].'" store-id="'.$store->ID.'" product-name="'.get_field('current_store_name', $store->ID).' ['.$d['title'].']" design="'.$designId.'">'.__('qué crees que debemos mejorar a futuro', 'xopifier').'.</a>
                            </div>

                            '.step_2_generate_unselect_design_modal_form($designId).'
                            '.step_2_generate_select_design_modal_form($designId).'

                            <div class="designs-popups-container" style="display:none;">
                                '.$designs_popups.'
                            </div>

                        </div>
                    </div>
                </div>
            ';

            $resume_settings = get_field('resume_settings', 'option');
            $service_settings = get_field('service_settings', 'option');
            $extras = '';

            $total_products = get_field('products_qty', $store->ID);
            if($total_products > 10) {
                $extras = '
                    <li>
                       <strong>Más de 10 productos</strong> creados en Xopifier
                        <div class="d-inline-block" title="'.$total_products.' productos" data-bs-toggle="tooltip">
                            <img class="alignnone size-full wp-image-207" src="'.site_url().'/wp-content/uploads/2024/05/help-with-circle.svg" alt="" width="14" height="14" />
                        </div>
                    </li>
                ';
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
            $other_services = '';

            if(is_array($aditional_services) && count($aditional_services) > 0){
                foreach ($aditional_services as $service) {
                    if(in_array($service['service'], $languages) || $service['service'] == 'Inglés') {
                        if($selected_languages == '')
                            $selected_languages .= $service['service'];
                        else
                            $selected_languages .= ', '.$service['service'];
                    }else{
                        if($other_services == '')
                            $other_services .= $service['service'];
                        else
                            $other_services .= ', '.$service['service'];
                    }
                }
            }

            if($other_services != ''){
                $extras .= '
                    <li>
                       Secciones adicionales: <strong>'.$other_services.'</strong>
                        <div class="d-none" title="'.$total_products.' productos" data-bs-toggle="tooltip">
                            <img class="alignnone size-full wp-image-207" src="'.site_url().'/wp-content/uploads/2024/05/help-with-circle.svg" alt="" width="14" height="14" />
                        </div>
                    </li>
                ';
            }

            $service_thumb = '';
            if(is_array($designs_thumbs) && count($designs_thumbs) > 0){
                foreach($designs_thumbs as $thumb){
                    $service_thumb .= $thumb;
                }
            }
        }else{
            $form_html = __('NO SE HA ESPECIFICADO EL ID DEL DISEÑO!', 'xopifier');
        }
    }

    $html = '
        <div id="steps" class="step-2 container-fluid px-0">
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
add_shortcode("step2", "step_2_shortcode");

function step_2_generate_unselect_design_modal_form($design_id){
    return '
        <div class="unselect-design-form-modal" style="display: none;">
            <div class="unselect-design-form-modal-box position-relative">
                <form id="unselect-design-form" method="post" class="text-center">    
                    <div class="form-loader" style="display: none;"></div>
                    <img src="'.get_template_directory_uri().'/img/close-gray.svg" class="close-unselect-design-form-modal position-absolute"/>
                    <img src="'.get_template_directory_uri().'/img/pers-3.png" class="mb-3 pers"/>
                    <h3 class="mb-3 text-center"><small>'.__('¡Cuéntanos que crees que debemos mejorar!', 'xopifier').'</small></h3>
                    <div class="d-flex gap-3 mb-4">
                        <div class="field mb-3 w-100 position-relative">
                            <label class="form-label d-none" for="field-unselect-design-text">'.__('Motivos', 'xopifier').'</label>
                            <textarea class="form-control required w-100" id="field-unselect-design-text" name="field-unselect-design-text" required></textarea>
                            <div class="word-counter" max="240">'.__('Caracteres disponibles:', 'xopifier').' <span>240</span></div>
                        </div>
                        <span class="error"></span>
                    </div>

                    <label class="alert alert-danger d-none my-3">'.__('Ha ocurrido un error, intente nuevamente, si persiste contactenos por favor!', 'xopifier').'</label>

                    <div class="text-center">
                        <input type="hidden" name="action" value="ws" />
                        <input type="hidden" name="wsa" value="unselect_design_form" />
                        <input type="hidden" name="lang" value="'.ICL_LANGUAGE_CODE.'" />
                        <input name="design_id" type="hidden" value="'.$design_id.'">
                        <input type="hidden" name="nonce" value="'.wp_create_nonce(xopifier_TITLE_FOR_NONCE).'" />
                        <button class="btn btn-primary btn-unselect-design" type="submit">'.__('Enviar comentario', 'xopifier').'</button>
                    </div>
                </form>

            </div>
        </div>
    ';
}

function step_2_generate_select_design_modal_form($design_id){

    return '
        <form id="set-percent-payment" method="post" action="" class="d-flex flex-column align-items-center">
            <input type="hidden" name="action" value="ws" />
            <input type="hidden" name="wsa" value="select-design" />
            <input type="hidden" name="product-featured-image" value="" />
            <input type="hidden" name="product-name" value="" />
            <input type="hidden" name="lang" value="'.ICL_LANGUAGE_CODE.'" />
            <input type="hidden" name="product-approved-design" value="" />
            <input type="hidden" name="product-store-id" value="" />
            <input type="hidden" name="product-approved-design-comment" value="" />
            <input type="hidden" name="nonce" value="'.wp_create_nonce(xopifier_TITLE_FOR_NONCE).'" />
        </form>
        <div class="select-design-form-modal" style="display: none;">
            <div class="select-design-form-modal-box position-relative">
                <form id="select-design-form" method="post">    
                    <input type="hidden" name="lang" value="'.ICL_LANGUAGE_CODE.'" />
                    <div class="form-loader" style="display: none;"></div>
                    <img src="'.get_template_directory_uri().'/img/close-gray.svg" class="close-select-design-form-modal position-absolute"/>

                    <div class="select-design-question w-100 mx-auto text-center">
                        <img src="'.get_template_directory_uri().'/img/pers-0.png" class="mb-3 pers"/>
                        <h3 class="mb-2 text-center"><small>'.__('¿Te gustaría ajustar algo a la propuesta selecionada?', 'xopifier').'</small></h3>
                        <p>'.__('Si tienes comentarios, los tomaremos en cuenta al configurar tu tienda en Shopify. Puedes dejarnos tus ajustes aquí o seguir tal como está.', 'xopifier').'</p>
                        <div class="mt-4 mb-3 w-100 mx-auto d-flex flex-md-row flex-sm-column flex-column gap-4 align-items-center justify-content-center">
                            <button class="btn btn-secondary btn-comment-select-design" type="button">'.__('Quiero hacer ajustes', 'xopifier').'</button>
                            <button class="btn btn-primary btn-select-design" type="button">'.__('Me gusta así, sigamos', 'xopifier').'</button>
                        </div>

                        <span class="alert alert-danger d-none my-3">'.__('Ha ocurrido un error, intente nuevamente, si persiste contactenos por favor!', 'xopifier').'</span>
                    </div>

                    <div class="select-design-comment text-center" style="display:none;">
                        <img src="'.get_template_directory_uri().'/img/pers-3.png" class="mb-3 pers"/>
                        <h3 class="mb-2 text-center"><small>'.__('¿Qué te gustaría ajustar de esta propuesta?', 'xopifier').'</small></h3>
                        <p class="text-center">'.__('Cuéntanos si tienes sugerencias o ajustes antes de continuar. Nuestro equipo los revisará y, si necesitamos más detalles, nos pondremos en contacto contigo.', 'xopifier').'</p>
                        <div class="d-flex flex-column gap-3 mb-4">
                            <div class="field mb-0 w-100 position-relative text">
                                <label class="form-label w-100 text-start mt-3" for="field-select-design-option">'.__('Ámbito del ajuste', 'xopifier').'</label>
                                <select class="form-select form-control required w-100" id="field-select-design-option" name="field-select-design-option" required placeholder="">
                                    <option value="">'.__('Selecciona una opción', 'xopifier').'</option>
                                    <option value="'.__('Colores', 'xopifier').'">'.__('Colores', 'xopifier').'</option>
                                    <option value="'.__('Tipografía (tipo de letra)', 'xopifier').'">'.__('Tipografía (tipo de letra)', 'xopifier').'</option>
                                    <option value="'.__('Encabezado y menú principal', 'xopifier').'">'.__('Encabezado y menú principal', 'xopifier').'</option>
                                    <option value="'.__('Banner principal de homepage', 'xopifier').'">'.__('Banner principal de homepage', 'xopifier').'</option>
                                    <option value="'.__('Secciones de homepage', 'xopifier').'">'.__('Secciones de homepage', 'xopifier').'</option>
                                    <option value="'.__('Cambio de imagen', 'xopifier').'">'.__('Cambio de imagen', 'xopifier').'</option>
                                    <option value="'.__('Otro', 'xopifier').'">'.__('Otro', 'xopifier').'</option>
                                </select>
                            </div>
                            <div class="field mb-3 w-100 position-relative text disabled">
                                <textarea disabled="true" class="form-control required w-100" id="field-select-design-text" name="field-select-design-text" required placeholder="'.__('Escribe aquí lo que deseas ajustar', 'xopifier').'"></textarea>
                                <div class="word-counter" max="240">'.__('Caracteres disponibles:', 'xopifier').' <span>240</span></div>
                            </div>
                            <span class="error"></span>
                        </div>

                        <span class="alert alert-danger d-none my-3">'.__('Ha ocurrido un error, intente nuevamente, si persiste contactenos por favor!', 'xopifier').'</span>

                        <div class="d-flex align-items-center justify-content-center gap-3">
                            <button type="button" class="btn close-select-design-comment w-auto h-auto" style="right:auto;top:auto;"><img src="'.get_template_directory_uri().'/img/back.svg'.'" /></button>
                            <button class="btn btn-primary btn-select-design-comment" type="button">'.__('Enviar comentarios y continuar', 'xopifier').'</button>
                        </div>
                    </div>

                    <div class="select-design-normal-finish text-center" style="display:none;">
                        <img src="'.get_template_directory_uri().'/img/pers-11.png" class="mb-3 pers"/>
                        <h3 class="mb-2 text-center"><small>'.__('¡Perfecto! Nos encanta que te haya gustado esta propuesta', 'xopifier').'</small></h3>
                        <p class="text-center mb-4">'.__('Usaremos esta propuesta como base para configurar tu tienda 1.0 en Shopify. El siguiente paso es completar la información de tu tienda. Puedes hacerlo ahora o cuando te sientas listo. También recibirás un correo con el enlace.', 'xopifier').'</p>
                        <div class="d-flex align-items-center justify-content-center gap-3">
                            <a href="'.apply_filters( 'wpml_permalink', site_url('/paso-3/'), ICL_LANGUAGE_CODE ).'" class="btn btn-primary direct-link" type="button">'.__('Completar información de mi tienda', 'xopifier').'</a>
                        </div>
                    </div>

                    <div class="select-design-comment-finish text-center" style="display:none;">
                        <img src="'.get_template_directory_uri().'/img/pers-11.png" class="mb-3 pers"/>
                        <h3 class="mb-2 text-center"><small>'.__('¡Gracias por tus comentarios!', 'xopifier').'</small></h3>
                        <p class="text-center mb-4">'.__('Tu mensaje fue recibido correctamente y lo tomaremos en cuenta al configurar tu tienda en Shopify. El siguiente paso es completar la información de tu tienda. Puedes hacerlo ahora o más tarde desde el correo que te enviaremos con el enlace.', 'xopifier').'</p>
                        <div class="d-flex align-items-center justify-content-center gap-3">
                            <a href="'.apply_filters( 'wpml_permalink', site_url('/paso-3/'), ICL_LANGUAGE_CODE ).'" class="btn btn-primary direct-link" type="button">'.__('Completar información de mi tienda', 'xopifier').'</a>
                        </div>
                    </div>

                </form>

            </div>
        </div>
    ';
}
<?php

// File Security Check
defined('ABSPATH') or die('No script kiddies please!');

if (!defined('xopifier_TITLE_FOR_NONCE')) {
    define('xopifier_TITLE_FOR_NONCE', "dT5#kLSoy4!4Nfqr");
}

/*
  Including style and js files
 */
add_action("wp_enqueue_scripts", "enqueue_files");

function enqueue_files() {
    global $wp_query; 
    $theme_url = get_template_directory_uri();

    // styles

    wp_enqueue_style('bootstrap', $theme_url . "/js/bootstrap-5.2.3-dist/css/bootstrap.min.css", array(), null);

    wp_enqueue_style('slick', $theme_url . "/js/slick/slick.css", array(), null);
    wp_enqueue_style('slick-theme', $theme_url . "/js/slick/slick-theme.css", array(), null);
    wp_enqueue_style('slick-mix', $theme_url . "/js/slick/slick-mix.css", array(), null);

    // wp_enqueue_style('animate', $theme_url . "/js/wow/css/animate.css", array(), null);

    wp_enqueue_style('quill', $theme_url . "/js/quill/quill.snow.css", array(), null);

    wp_enqueue_style('aos', $theme_url . "/sass/aos.css", array(), null);

    wp_enqueue_style('fancybox', $theme_url . "/sass/fancybox.css", array(), null);
    
    $theme_dir = get_template_directory();
    $theme_version = wp_get_theme()->get('Version');

    $basic_css_ver = file_exists($theme_dir . '/sass/basic-styles.css') ? filemtime($theme_dir . '/sass/basic-styles.css') : $theme_version;
    $resp_css_ver  = file_exists($theme_dir . '/sass/responsive-styles.css') ? filemtime($theme_dir . '/sass/responsive-styles.css') : $theme_version;
    $app_js_ver    = file_exists($theme_dir . '/js/app.js') ? filemtime($theme_dir . '/js/app.js') : $theme_version;
    $uploader_ver  = file_exists($theme_dir . '/js/jquery.uploader.control.js') ? filemtime($theme_dir . '/js/jquery.uploader.control.js') : $theme_version;

    wp_enqueue_style('basic-styles', $theme_url . "/sass/basic-styles.css", array(), $basic_css_ver);
    wp_enqueue_style('responsive-styles', $theme_url . "/sass/responsive-styles.css", array(), $resp_css_ver);

    // scripts

    wp_enqueue_script('jquery');

    wp_enqueue_script( 'jquery-ui-core');
    wp_enqueue_script( 'jquery-ui-tooltip');

    wp_register_script('jquery.validation', $theme_url . '/js/jquery.validate/jquery.validate.min.js', array('jquery'), '', true);
    wp_enqueue_script('jquery.validation');

    wp_register_script('additional-methods', $theme_url . '/js/jquery.validate/additional-methods.min.js', array('jquery'), '', true);
    wp_enqueue_script('additional-methods');

    wp_register_script('jquery.validation-es', $theme_url . '/js/jquery.validate/messages_es.min.js', array('jquery'), '', true);
    wp_enqueue_script('jquery.validation-es');

    wp_register_script('pdf', $theme_url . '/js/pdfjs/build/pdf.js', array('jquery'), '', false);
    wp_enqueue_script('pdf');   

    wp_register_script('pdfworker', $theme_url . '/js/pdfjs/build/pdf.worker.js', array('jquery'), '', false);
    wp_enqueue_script('pdfworker');   

    wp_register_script('bootstrap', $theme_url . '/js/bootstrap-5.2.3-dist/js/bootstrap.bundle.min.js', array('jquery'), '', true);
    wp_enqueue_script('bootstrap'); 

    wp_register_script('slick.js', $theme_url . '/js/slick/slick.js', array('jquery'), '', true);
    wp_enqueue_script('slick.js'); 

    wp_register_script('aos', $theme_url . '/js/aos.js', array('jquery'), '', true);
    wp_enqueue_script('aos');

    wp_register_script('quill', $theme_url . '/js/quill/quill.js', array('jquery'), '', true);
    wp_enqueue_script('quill');

    wp_register_script('gsap', $theme_url . '/js/gsap.min.js', array('jquery'), '', true);
    wp_enqueue_script('gsap'); 

    wp_register_script('masonry', $theme_url . '/js/masonry.pkgd.min.js', array('jquery'), '', true);
    wp_enqueue_script('masonry');
    
    wp_register_script('ScrollTrigger', $theme_url . '/js/ScrollTrigger.min.js', array('jquery'), '', true);
    wp_enqueue_script('ScrollTrigger'); 

    wp_register_script('ScrollToPlugin', $theme_url . '/js/ScrollToPlugin3.min.js', array('jquery'), '', true);
    wp_enqueue_script('ScrollToPlugin');

    wp_register_script('fancybox', $theme_url . '/js/fancybox.umd.js', array('jquery'), '', true);
    wp_enqueue_script('fancybox');

    wp_register_script('mammoth', $theme_url . '/js/mammoth.browser.min.js', array('jquery'), '', true);
    // wp_enqueue_script('mammoth');

    wp_register_script('app', $theme_url . '/js/app.js', array('jquery'), $app_js_ver, true);
    wp_enqueue_script('app');

    wp_register_script('jquery.uploader', $theme_url . '/js/jquery.uploader.control.js', array('jquery'), $uploader_ver, true);
    wp_enqueue_script('jquery.uploader');

    $title_nonce = wp_create_nonce(xopifier_TITLE_FOR_NONCE);
    wp_localize_script('app', 'my_ajax_obj', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        // 'base_url' => apply_filters( 'wpml_permalink', site_url(), ICL_LANGUAGE_CODE ),
        'base_url' => site_url(),
        'theme_url' => get_template_directory_uri(),
        'other' => __('Otro', 'xopifier'),
        'nonce' => $title_nonce,
        'included_products_qty' => get_field('base_service_products_qty_included', 'option'),
        'aditional_products_price' => get_field('base_service_aditional_products_price', 'option'),
        'category' => __('Categor&iacute;a', 'xopifier'),
        'valid_url' => __('Escribe una URL v&aacute;lida', 'xopifier'),
        'store_registered' => __('Ya hay una tienda registrada con este Email', 'xopifier'),
        'valid_email' => __('Escribe un EMAIL v&aacute;lido.', 'xopifier'),
        'email_registered' => __('El email que escribi&oacute; ya est&aacute; registrado.', 'xopifier'),
        'enter_pass' => __('Especif&iacute;que una contrase&ntilde;a.', 'xopifier'),
        'match_pass' => __('La contrase&ntilde;a y confirmar contrase&ntilde;a no coinciden.', 'xopifier'),
        'short_pass' => __('Contrase&ntilde;a muy corta!', 'xopifier'),
        'error' => __('Ha ocurrido un error, por favor, intente nuevamente.', 'xopifier'),
        'error1' => __('Ha ocurrido un error, por favor, verifique los datos de los campos.', 'xopifier'),
        'adjust_proposal' => __('Por favor, dinos lo que te gustaría ajustar de esta propuesta.', 'xopifier'),
        'add_extra' => __('Estás agregando un extra. El costo de tu tienda se actualizará a <b>$', 'xopifier'),
        'select_one_field' => __('Debes seleccionar al menos un campo.', 'xopifier'),
        'contact_info' => __('Especifica la información de contacto.', 'xopifier'),
        'not_valid_text' => __('Texto no v&aacute;lido.', 'xopifier'),
        'not_valid' => __(' no v&aacute;lido.', 'xopifier'),
        'required_field' => __('Este campo es obligatorio.', 'xopifier'),
        'add_lang_1' => __("Estás agregando <b>versión en ", 'xopifier'),
        'add_lang_2' => __("</b>. El costo de tu tienda se actualizará a <b>$", 'xopifier'),
        'add_lang_3' => __("El costo de tu tienda se actualizará a <b>$", 'xopifier'),
        'add_lang_4' => __("Estás agregando <b>versión en otros idiomas</b>. El costo de tu tienda se actualizará a <b>$", 'xopifier'),
        'english' => __('Ingles', 'xopifier'),
        'spanish' => __('Español', 'xopifier'),
        'add_extra_lang_1' => __("Estás agregando <b>versión en ", 'xopifier'),
        'add_extra_lang_2' => __("</b>. El costo de tu tienda se actualizará a <b>$", 'xopifier'),
        'product_extra_1' => __('Vas a agregar un producto adicional', 'xopifier'),
        'product_extra_2' => __('Perfecto, puedes agregar los que quieras, por un precio de <b>$12</b> por producto.', 'xopifier'),
        'product_extra_3' => __('Puedes revisar en todo momento el total actualizado de tu compra en <b>"Mi Xopifier"</b>.', 'xopifier'),
        'product_extra_4' => __('Ok, agregar producto adicional', 'xopifier'),
        'product_extra_5' => __('Solo puedes agregar 3 productos a favoritos!', 'xopifier'),
        'product_extra_6' => __('Producto agregado a favoritos! Debes guardar los cambios en el botón “Guardar y continuar“ para que se hagan efectivos!', 'xopifier'),
        'product_extra_7' => __('Producto quitado de favoritos! Debes guardar los cambios en el botón “Guardar y continuar“ para que se hagan efectivos!', 'xopifier'),
        'product_extra_8' => __('Has agregado un producto nuevo, debes guardar los cambios en el botón “Guardar y continuar“ para que se hagan efectivos!', 'xopifier'),
        'product_extra_9' => __('Has eliminado un producto, debes guardar los cambios en el botón “Guardar y continuar“ para que se hagan efectivos!', 'xopifier'),
        'other_variation' => __('Otra variaci&oacute;n', 'xopifier'),
        'variation_name' => __('Nombre de la variación:', 'xopifier'),
        'variation_name_placeholder' => __('Ej: Edad', 'xopifier'),
        'variation_desc' => __('Describe sus componentes y variaciones:', 'xopifier'),
        'variation_desc_placeholder' => __('Ej: Bebés, niños, adolescentes, adultos', 'xopifier'),
        'add_extra_product' => __('Has agregado un producto adicional. El costo de tu tienda será actualizado a <b>', 'xopifier'),
        'prospects_discount' => __('Debe especificar el descuento que desea hacer a los prospectos.', 'xopifier'),
        'enter_promo' => __('Debe especificar el anuncio o promoci&oacute;n.', 'xopifier'),
        'enter_promo_indications' => __('Debe especificar las indicaciones del anuncio o promoci&oacute;n.', 'xopifier'),
        'msg_0' => __('Estamos preparando las dos propuestas de diseño personalizado para tu tienda, recibiras un mail cuando esten listas.', 'xopifier'),
        'msg_1' => __('El diseño especificado no está disponible!', 'xopifier'),
        'msg_2' => __('Ya aprovaste el diseño especificado! Entra a tu cuenta <b>Mi Xopifier</b> para solicitar cambios.', 'xopifier'),
        'msg_3' => __('Has rechazado el diseño especificado, puedes crear una nueva tienda si así lo deseas.', 'xopifier'),
        'msg_4' => __('El adelanto de pago de tu tienda esta pendiente, por favor realice el pago para continuar.', 'xopifier'),
        'msg_5' => __('Los datos de tu tienda están en revisión, por favor espere a que un asesor se ponga en contacto con usted.', 'xopifier'),
        'popup_info' => __('Información', 'xopifier'),
        'popup_success' => __('Éxito', 'xopifier'),
        'popup_error' => __('Error', 'xopifier'),
        'popup_warning' => __('Advertencia', 'xopifier'),
        'error_db_files' => __('Error obteniendo los ficheros desde la base de datos:', 'xopifier'),
        'network_failure' => __('Respuesta de red fallida.', 'xopifier'),
        'error_link_file' => __('Error creando el fichero desde la URL:', 'xopifier'),
        'max_files' => __('Solo puedes subir un máximo de 5 ficheros.', 'xopifier'),
        'unsupported_file_type' => __('Tipo de fichero no soportado!', 'xopifier'),
    ));

    global $post;

    if(@$post->post_name == 'paso-1'){
        $step_1_js = __DIR__ . '/../js/xopifier/step-1';
        autoloadJS($step_1_js);
    }

    if(@$post->post_name == 'paso-2'){
        $step_2_js = __DIR__ . '/../js/xopifier/step-3';
        autoloadJS($step_2_js);
    }

    if(@$post->post_name == 'paso-3'){
        $step_3_js = __DIR__ . '/../js/xopifier/step-2';
        autoloadJS($step_3_js);
    }

    if(@$post->post_name == 'paso-4'){
        $step_4_js = __DIR__ . '/../js/xopifier/step-4';
        autoloadJS($step_4_js);
    }
}

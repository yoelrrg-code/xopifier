<?php
/**
 * Dispatcher modularizado para AJAX en Xopifier
 */

defined('ABSPATH') or die('No script kiddies please!');

/**
 * Función principal AJAX (Hook wp_ajax_ws y wp_ajax_nopriv_ws)
 */
function MyAjaxFunctions() {
	// 1. Verificación de Nonce de seguridad (CSRF protection)
	$nonce = isset($_POST['nonce']) ? $_POST['nonce'] : (isset($_SERVER['HTTP_X_WP_NONCE']) ? $_SERVER['HTTP_X_WP_NONCE'] : '');
	if (!wp_verify_nonce($nonce, xopifier_TITLE_FOR_NONCE)) {
		wp_send_json_error(array('message' => __('Petición no autorizada o sesión expirada.', 'xopifier')), 403);
		wp_die();
	}

	$wsa = isset($_POST["wsa"]) ? sanitize_key($_POST["wsa"]) : '';
	global $current_user, $wpdb, $sitepress;
    
	$result = array();

	if (isset($_POST['lang']) && !empty($_POST['lang']) && isset($sitepress)) {
		$sitepress->switch_lang(sanitize_text_field($_POST['lang']));
	}

	// 2. Enrutado modular por WSA (Action Handler)
	switch ($wsa) {
		// --- AUTH & USER HANDLERS ---
		case 'create_user_account':
		case 'create_user_no_payment':
		case 'verify_user':
		case 'login':
			$result = xopifier_handle_ajax_auth($wsa);
			break;

		// --- STEP 3 & STORE HANDLERS ---
		case 'update-tab-status':
		case 'finish-step-3':
		case 'get-store-categories':
		case 'update-popup-resume':
		case 'save-store-languages-data':
		case 'get-selected-languages-translations':
		case 'batch-file-import-store-products':
		case 'batch-sheet-import-store-products':
		case 'save-store-products':
		case 'save-store-info-reviews-data':
		case 'save-store-info-policy':
		case 'get-section-files':
		case 'save-store-info-custom-data':
		case 'save-store-info-faqs-data':
		case 'save-store-info-contact-data':
		case 'save-store-about-info':
		case 'save-store-promos-discount-data':
		case 'save-store-promos-ads-data':
		case 'toggle-aditional-information':
		case 'save-store-products-extra':
		case 'save-store-products-categories':
			$result = xopifier_handle_ajax_store_step3($wsa);
			break;

		// --- DESIGN HANDLERS ---
		case 'select-design':
		case 'unselect_design_form':
		case 'send_design_email':
			$result = xopifier_handle_ajax_design($wsa);
			break;

		default:
			$result = apply_filters('xopifier_custom_ajax_wsa_' . $wsa, array(
				'error' => true,
				'message' => __('Acción AJAX no válida o desconocida.', 'xopifier')
			));
			break;
	}

	wp_send_json($result);
}

add_action('wp_ajax_nopriv_ws', 'MyAjaxFunctions');  
add_action('wp_ajax_ws', 'MyAjaxFunctions');

/**
 * Handler modular para autenticación y usuarios
 */
function xopifier_handle_ajax_auth($wsa) {
	$result = array('error' => true);

	if ($wsa == 'verify_user') {
		$email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
		$exists = email_exists($email);
		$result = array(
			'error' => false,
			'exists' => (bool)$exists
		);
	} elseif ($wsa == 'login') {
		$creds = array(
			'user_login'    => isset($_POST['user_email']) ? sanitize_email($_POST['user_email']) : '',
			'user_password' => isset($_POST['user_pass']) ? $_POST['user_pass'] : '',
			'remember'      => true
		);
		$user = wp_signon($creds, false);
		if (is_wp_error($user)) {
			$result = array('error' => true, 'message' => $user->get_error_message());
		} else {
			$result = array('error' => false, 'redirect' => site_url());
		}
	}

	return $result;
}

/**
 * Handler modular para Paso 3 y gestión de Tienda
 */
function xopifier_handle_ajax_store_step3($wsa) {
	$result = array('error' => false);

	if ($wsa == 'update-tab-status') {
		$store_id = isset($_POST['storeid']) ? absint($_POST['storeid']) : 0;
		$maintab  = isset($_POST['maintab']) ? sanitize_key($_POST['maintab']) : '';
		$subtab   = isset($_POST['subtab']) ? sanitize_key($_POST['subtab']) : '';
		$status   = isset($_POST['status']) ? sanitize_text_field($_POST['status']) : '';

		$step3_status = get_step3_status($store_id);

		if (isset($step3_status[$store_id][$maintab][$subtab]) && $step3_status[$store_id][$maintab][$subtab] != 'done') {
			$step3_status[$store_id][$maintab][$subtab] = $status;
			update_option('step3_status', $step3_status);
		}

		$result = array(
			'error' => false,
			'tab-status' => $step3_status
		);
	} elseif ($wsa == 'finish-step-3') {
		$store_id = isset($_POST['store_id']) ? absint($_POST['store_id']) : 0;
		if ($store_id > 0) {
			update_field('status', 'review_info', $store_id);
		}
		$result = array(
			'error' => false,
			'msg' => __('Ya tenemos toda la información clave para completar tu Tienda 1.0', 'xopifier'),
		);
	} elseif ($wsa == 'get-store-categories') {
		$storeID = isset($_POST['storeID']) ? absint($_POST['storeID']) : 0;
		$categories = get_field('current_store_product_categories', $storeID);
		$categories_html = '<option value="*">'.__('Selecciona una categoría', 'xopifier').'</option>';
		if (is_array($categories) && count($categories) > 0) {
			foreach ($categories as $categ) {
				$cat_name = esc_html($categ['category']);
				$categories_html .= '<option value="'.esc_attr($cat_name).'">'.$cat_name.'</option>';
			}
		}
		$result = array(
			'error' => false,
			'categories' => $categories_html
		);
	} elseif ($wsa == 'update-popup-resume') {
		$store_id = isset($_POST['store_id']) ? absint($_POST['store_id']) : 0;
		$designs = get_posts(array('post_type' => 'design', 'post_status' => 'publish', 'meta_query' => array(
			array(
				'key' => 'store',
				'value' => $store_id,
			)
		)));
		$design_id = !empty($designs) ? $designs[0]->ID : 0;

		$result = array(
			'error' => false,
			'popup_resume' => popup_resume($design_id)
		);
	}

	return $result;
}

/**
 * Handler modular para Diseños
 */
function xopifier_handle_ajax_design($wsa) {
	$result = array('error' => false);

	if ($wsa == 'select-design') {
		$design_id = isset($_POST['design_id']) ? absint($_POST['design_id']) : 0;
		if ($design_id > 0) {
			$design = get_post($design_id);
			if ($design) {
				$store = get_field('store', $design->ID);
				if ($store) {
					update_field('status', 'aproved_design', $store->ID);
					update_field('selected_design', $design->ID, $store->ID);
				}
			}
		}
	}

	return $result;
}
<?php 
// File Security Check
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

require __DIR__.'/phpoffice/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;
/*
	Ajax Functions
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

	if($wsa == 'update-tab-status'){
		$store_id = $_POST['storeid'];

		$step3_status = get_step3_status($store_id);

		if($step3_status[$store_id][$_POST['maintab']][$_POST['subtab']] != 'done'){
			$step3_status[$store_id][$_POST['maintab']][$_POST['subtab']] = $_POST['status'];
			update_option('step3_status', $step3_status);
		}

		$result = array(
			'error' => false,
			'tab-status' => $step3_status
		);
	}elseif($wsa == 'get-selected-languages-translations'){
		$selected_languages = $_POST['selected_languages'];
		$default_lang = $_POST['default_lang'];
		$default_options = '';

		$langs = get_field('languages', 'option');

		foreach($selected_languages as $selected_lang){
			if($selected_lang == $default_lang)
				$default_options .= '<option selected="selected" value="'.$selected_lang.'">'.get_language_translation($langs, $selected_lang).'</option>';
			else
				$default_options .= '<option value="'.$selected_lang.'">'.get_language_translation($langs, $selected_lang).'</option>';
		}

		$result = array(
			'error' => false,
			'selected_languages' => $default_options
		);
	}elseif($wsa == 'finish-step-3'){
		$store_id = $_POST['store_id'];
		update_field('status', 'review_info', $store_id);
		$result = array(
			'error' => false,
			'msg' => __('Ya tenemos toda la información clave para completar tu Tienda 1.0', 'xopifier'),
		);
	}elseif($wsa == 'get-store-categories'){
		$storeID = $_POST['storeID'];
		$categories = get_field('current_store_product_categories', $storeID);
		$categories_html = '<option value="*">'.__('Selecciona una categoría', 'xopifier').'</option>';
		if(is_array($categories) and count($categories) > 0){
			foreach($categories as $categ){
				$categories_html .= '
					<option value="'.$categ['category'].'">'.$categ['category'].'</option>
				';
			}
		}
		$result = array(
			'error' => false,
			'categories' => $categories_html
		);
	}elseif($wsa == 'update-popup-resume'){
		$store_id = $_POST['store_id'];
		$designs = get_posts(array('post_type' => 'design', 'post_status' => 'publish', 'meta_query' => array(
			array(
				'key' => 'store',
				'value' => $store_id,
			)
		)));
		$design_id = $designs[0]->ID;

		$result = array(
			'error' => false,
			'popup_resume' => popup_resume($design_id)
		);
	}elseif($wsa == 'save-store-languages-data') {
		$store_id = $_POST['store_id'];

		$ignore = @isset($_POST['ignore']) ? $_POST['ignore'] : false;

		if($ignore){
			$step3_status = get_step3_status($store_id);
			$step3_status[$store_id]['other-tab']['other-lang-tab'] = 'done';
			update_option('step3_status', $step3_status);

			$result = array(
				'error' => false,
				'msg' => __('Información adicional guardada correctamente!', 'xopifier'),
			);
		}else{
			$total_price = get_field('total_price', $store_id);
			$language_price = $_POST['language_price'];
			$languages = $_POST['field-languages'];
			$default_language = $_POST['language-service-default'];
			$disable = $_POST['disable'];

			$aditional_services = get_field('aditional_services', $store_id);
			$services_array = array();

			$lang_total_price = 0;

			if($disable == 'true'){
				if(is_array($aditional_services) and count($aditional_services) > 0){    
					foreach($aditional_services as $service){
						if($service != '' && $service['type'] != 'lang'){
							$services_array[] = array(
								'id' => $service['id'],
								'service' => $service['service'],
								'price' => $service['price'],
								'type' => $service['type'],
								'active' => $service['active'],
							);
						}elseif($service['type'] == 'lang'){
							$lang_total_price += $service['price'];
						}
					}
				}

				$step3_status = get_step3_status($store_id);
				$step3_status[$store_id]['other-tab']['other-lang-tab'] = '';
				update_option('step3_status', $step3_status);

				update_field('aditional_services', $services_array, $store_id);
				update_field('total_price', ($total_price - $lang_total_price), $store_id);
				update_field('default_language', __('Español', 'xopifier'), $store_id);

				$result = array(
					'error' => false,
					'total_price' => $total_price - $lang_total_price,
					'msg' => __('Otros idiomas actualizados correctamente!', 'xopifier'),
				);
			}else{
				$total_price = $_POST['total_price'];
				if(is_array($aditional_services) and count($aditional_services) > 0){    
					foreach($aditional_services as $service){
						if($service != '' && $service['type'] != 'lang'){
							$services_array[] = array(
								'id' => $service['id'],
								'service' => $service['service'],
								'price' => $service['price'],
								'type' => $service['type'],
								'active' => $service['active'],
							);
						}
					}
				}
		
				if(is_array($languages) and count($languages) > 0){
					foreach($languages as $language){
						$services_array[] = array(
							'id' => 'lang',
							'service' => $language,
							'price' => $language_price,
							'type' => 'lang',
							'active' => 1,
						);

						$lang_total_price += $language_price;
					}
				}

				$step3_status = get_step3_status($store_id);
				$step3_status[$store_id]['other-tab']['other-lang-tab'] = 'done';
				update_option('step3_status', $step3_status);

				update_field('aditional_services', $services_array, $store_id);
				update_field('total_price', $total_price, $store_id);
				update_field('default_language', $default_language, $store_id);

				$result = array(
					'error' => false,
					'total_price' => $total_price,
					'msg' => __('Otros idiomas actualizados correctamente!', 'xopifier'),
				);
			}
		}
	}elseif($wsa == 'batch-file-import-store-products'){
		$store_id = $_POST['store_id'];
		$design_id = $_POST['design_id'];
		$batch_import_type = $_POST['batch-import-type'];
		$file = $_FILES['field-store-batch-file'];

		try{
			// upload files
			$upload_dir       = wp_upload_dir();

			//HANDLE UPLOADED FILE
			require_once(ABSPATH . "wp-admin" . '/includes/image.php');
			require_once(ABSPATH . "wp-admin" . '/includes/file.php');
			require_once(ABSPATH . "wp-admin" . '/includes/media.php');

			// Without that I'm getting a debug error!?
			if( !function_exists( 'wp_get_current_user' ) ) {
				require_once( ABSPATH . 'wp-includes/pluggable.php' );
			}

			if($file['name'] != ''){
				$products = [];
				$errors = [];
				if($file['type'] == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' || $file['type'] == 'application/vnd.ms-excel'){
					//si es un fichero xlsx o xls
					
					$inputFileName = $file['tmp_name'];

					// Cargar el archivo Excel
					$spreadsheet = IOFactory::load($inputFileName);

					// Seleccionar la hoja activa
					$sheet = $spreadsheet->getActiveSheet();

					// Leer filas y columnas
					$data = [];
					foreach ($sheet->getRowIterator() as $row) {
						$cellIterator = $row->getCellIterator();
						$cellIterator->setIterateOnlyExistingCells(false); // Para leer todas las celdas, incluso vacías
						$rowData = [];
						foreach ($cellIterator as $cell) {
							$rowData[] = $cell->getValue();
						}
						if($rowData[0] != NULL){
							$data[] = $rowData;
						}
					}
					
					// $data ahora contiene las filas del Excel como arrays
					if(count($data) > 1){//verifico que el array tengo al menos un row con datos, pq el 1er row es el header en el excel
						foreach($data as $k => $product){
							if($k > 1){
								$images = $product[3];
								$files = [];
								if($images != '' && $images != NULL){
									$images = explode(',', $images);
									foreach($images as $image_url){
										$image_content = file_get_contents($image_url);
										// Get the filename and extension
										if(strpos($image_url, 'google.com')){//verifico si es una imagen de google
											$file_name = get_google_image_file_name($image_content);
										}else{
											$path_info = pathinfo($image_url);
											$file_name = $path_info['basename']; // e.g., image.jpg
										}
										// Create a temporary file
										$temp_file = tempnam(sys_get_temp_dir(), 'img_');
										file_put_contents($temp_file, $image_content);
										// Use wp_upload_bits to upload the file
										$upload = wp_upload_bits($file_name, null, file_get_contents($temp_file));
										if (isset($upload['error']) && $upload['error'] != 0) {
											// Handle error
											$errors[] = '<br>Error uploading image: ' . $upload['error']. ' | Producto: '.$product[0];
										} else {
											// Successfully uploaded
											$attachment = array(
												'guid' => $upload['url'], 
												'post_mime_type' => $upload['type'],
												'post_title' => sanitize_file_name($file_name),
												'post_content' => '',
												'post_status' => 'inherit'
											);
											// Insert the attachment into the database
											$attach_id = wp_insert_attachment($attachment, $upload['file']);
											
											$files[] = array(
												'media' => $attach_id
											);
										}
										// Clean up the temporary file
										unlink($temp_file);
									}
								}

								$variations = [];
								if($product[6] != '' && $product[6] != NULL && $product[7] != '' && $product[7] != NULL){
									$variations[] = array(
										'attribute' => $product[6],
										'description' => $product[7]
									);
								}

								if($product[8] != '' && $product[8] != NULL && $product[9] != '' && $product[9] != NULL){
									$variations[] = array(
										'attribute' => $product[8],
										'description' => $product[9]
									);
								}

								if(!empty($files)){
									$products[] = array(
										'product_name' => $product[0],
										'product_currency' => 'USD',
										'product_price' => $product[2],
										'product_saleprice' => $product[4],
										'product_description' => $product[1],
										'product_categories' => $product[5],
										'product_featured' => 0,
										'product_variations' => $variations,
										'product_media' => $files,
										'product_variations_comments' => ''
									);
								}else{
									$products[] = array(
										'product_name' => $product[0],
										'product_currency' => 'USD',
										'product_price' => $product[2],
										'product_saleprice' => $product[4],
										'product_description' => $product[1],
										'product_categories' => $product[5],
										'product_featured' => 0,
										'product_variations' => $variations,
										'product_variations_comments' => ''
									);
								}
							}
						}
					}

					if(count($products) > 0){
						$db_products = get_field('current_store_pc_products', $store_id);
						$products = array_merge($db_products, $products);
						update_field('current_store_pc_products', $products, $store_id);
					}
				}elseif($file['type'] == 'text/csv'){
					//si es un fichero csv

					$inputFileName = $file['tmp_name'];

					$data = [];
					if (($handle = fopen($inputFileName, "r")) !== FALSE) {
						$headers = fgetcsv($handle);  // Leer la primera fila como encabezados
						while (($row = fgetcsv($handle)) !== FALSE) {
							// $data[] = array_combine($headers, $row);  // Combinar encabezados y valores
							$data[] = $row;  // Combinar encabezados y valores
						}
						fclose($handle);
					}

					// $data ahora contiene un array asociativo con los datos del CSV
					// $data ahora contiene las filas del Excel como arrays
					if(count($data) > 1){//verifico que el array tengo al menos un row con datos, pq el 1er row es el header en el excel
						foreach($data as $k => $product){
							if($k > 0){
								$images = $product[3];
								$files = [];
								if($images != '' && $images != NULL){
									$images = explode(',', $images);
									foreach($images as $image_url){
										$image_content = file_get_contents($image_url);
										// Get the filename and extension
										if(strpos($image_url, 'google.com')){//verifico si es una imagen de google
											$file_name = get_google_image_file_name($image_content);
										}else{
											$path_info = pathinfo($image_url);
											$file_name = $path_info['basename']; // e.g., image.jpg
										}
										// Create a temporary file
										$temp_file = tempnam(sys_get_temp_dir(), 'img_');
										file_put_contents($temp_file, $image_content);
										// Use wp_upload_bits to upload the file
										$upload = wp_upload_bits($file_name, null, file_get_contents($temp_file));
										if (isset($upload['error']) && $upload['error'] != 0) {
											// Handle error
											$errors[] = '<br>Error uploading image: ' . $upload['error']. ' | Producto: '.$product[0];
										} else {
											// Successfully uploaded
											$attachment = array(
												'guid' => $upload['url'], 
												'post_mime_type' => $upload['type'],
												'post_title' => sanitize_file_name($file_name),
												'post_content' => '',
												'post_status' => 'inherit'
											);
											// Insert the attachment into the database
											$attach_id = wp_insert_attachment($attachment, $upload['file']);
											
											$files[] = array(
												'media' => $attach_id
											);
										}
										// Clean up the temporary file
										unlink($temp_file);
									}
								}

								$variations = [];
								if($product[6] != '' && $product[6] != NULL && $product[7] != '' && $product[7] != NULL){
									$variations[] = array(
										'attribute' => $product[6],
										'description' => $product[7]
									);
								}

								if($product[8] != '' && $product[8] != NULL && $product[9] != '' && $product[9] != NULL){
									$variations[] = array(
										'attribute' => $product[8],
										'description' => $product[9]
									);
								}

								if(!empty($files)){
									$products[] = array(
										'product_name' => $product[0],
										'product_currency' => 'USD',
										'product_price' => $product[2],
										'product_saleprice' => $product[4],
										'product_description' => $product[1],
										'product_categories' => $product[5],
										'product_featured' => 0,
										'product_variations' => $variations,
										'product_media' => $files,
										'product_variations_comments' => ''
									);
								}else{
									$products[] = array(
										'product_name' => $product[0],
										'product_currency' => 'USD',
										'product_price' => $product[2],
										'product_saleprice' => $product[4],
										'product_description' => $product[1],
										'product_categories' => $product[5],
										'product_featured' => 0,
										'product_variations' => $variations,
										'product_variations_comments' => ''
									);
								}
							}
						}
					}


					if(count($products) > 0){
						$db_products = get_field('current_store_pc_products', $store_id);
						$products = array_merge($db_products, $products);
						update_field('current_store_pc_products', $products, $store_id);
					}
				}

				if(count($products) > 0){
					$list_products = step_3_list_products($design_id);//obtengo el listado de productos actualizado

					$db_link_products = get_field('current_store_popular_products', $store_id);//obtengo los productos de link desde la bd
					$linkproductqty = is_array($db_link_products) && count($db_link_products) > 0 ? count($db_link_products) : 0;//cuento los productos link
					$pcproductqty = is_array($products) && count($products) > 0 ? count($products) : 0;//cuento los productos de pc
					$products_qty = $linkproductqty + $pcproductqty;//obtengo la cantidad total de productos

					$service_settings = get_field('service_settings', 'option');

					$products_qty_included = $service_settings['base_service_products_qty_included'];
					$base_price = $service_settings['base_services_price'];
					$aditional_product_price = $service_settings['base_service_aditional_products_price'];

					$aditional_services = get_field('aditional_services', $store_id);

					$payment_methods = get_field('aditional_setup_payment_methods', $store_id);

					$services_array = array();
					$new_price = 0;

					$more_than_10 = false;

					if($products_qty > $products_qty_included){

						$more_than_10 = true;

						$new_price = $base_price + (($products_qty - $products_qty_included) * $aditional_product_price);

						if(is_array($aditional_services) && count($aditional_services) > 0){    
							foreach ($aditional_services as $service) {
								if($service['service'] != 'Cantidad de productos' && $service['service'] != 'Productos extra'){
									$new_price += $service['price'];
								}
							}
						}

						if(is_array($aditional_services) && count($aditional_services) > 0){    
							foreach($aditional_services as $service){
								if($service != '' && ($service['service'] != 'Cantidad de productos' && $service['service'] != 'Productos extra')){
									$services_array[] = array(
										'id' => $service['id'],
										'service' => $service['service'],
										'price' => $service['price'],
										'type' => $service['type'],
										'active' => $service['active'],
									);
								}
							}
						}

						$services_array[] = array(
							'id' => 'extra_products',
							'service' => 'Productos extra',
							'price' => ($products_qty - $products_qty_included) * $aditional_product_price
						);

					}else{

						$new_price = $base_price;

						if(is_array($aditional_services) && count($aditional_services) > 0){    
							foreach ($aditional_services as $service) {
								if($service['service'] != 'Cantidad de productos' && $service['service'] != 'Productos extra'){
									$new_price += $service['price'];
								}
							}
						}

						$services_array = array();
						if(is_array($aditional_services) && count($aditional_services) > 0){    
							foreach($aditional_services as $k => $service){
								if($service != '' && ($service['service'] != 'Cantidad de productos' && $service['service'] != 'Productos extra')){
									$services_array[] = array(
										'id' => $service['id'],
										'service' => $service['service'],
										'price' => $service['price'],
										'type' => $service['type'],
										'active' => $service['active'],
									);
								}
							}
						}

					}

					if($payment_methods){
						$service_settings = get_field('service_settings', 'option');
						$services = $service_settings['services'];
						$service_price = 0;
						foreach($services as $service){
							if($service['id'] == 'payment_methods'){
								$service_price = $service['price'];
							}
						}

						$new_price = $new_price + $service_price;
					}

					update_field('aditional_services', $services_array, $store_id);
					update_field('total_price', $new_price, $store_id);
					update_field('products_qty', $products_qty, $store_id);

					//verifico la cantidad de productos para definir el mensaje a mostrar si se agregaron o no mas de 10 productos
					if($more_than_10){
						$msg = __('Productos actualizados correctamente. Agregó <b>más de '.$products_qty_included.' productos</b> por lo que el costo de su Tienda 1.0 aunmentó a <b>$12 por cada producto extra</b>.', 'xopifier');
					}else{
						$msg = __('Productos actualizados correctamente!', 'xopifier');
					}
				}else{
					$msg = __('No hubo cambios en los productos!', 'xopifier');
				}

				if(!empty($errors)){
					$msg = __('Productos actualizados, pero con problema en las imágenes:', 'xopifier').implode(' ', $errors);
					$result = array(
						'error' => false,
						'msg' => $msg,
						'products' => $list_products
					);	
				}else{
					$result = array(
						'error' => false,
						'msg' => $msg,
						'products' => $list_products
					);
				}
			}else{
				$result = array(
					'error' => false,
					'msg' => __('No se subió el fichero XLSX/CSV correctamente!', 'xopifier'),
				);
			}
		}catch(Exception $e) {
			$result = array(
				'error' => true,
				'msg' => $e->getMessage(),
			);
		}
	}elseif($wsa == 'batch-sheet-import-store-products'){
		$store_id = $_POST['store_id'];
		$design_id = $_POST['design_id'];
		$batch_import_type = $_POST['batch-import-type'];
		$sheet_url = $_POST['field-store-batch-url'];

		try{
			// upload files
			$upload_dir       = wp_upload_dir();

			//HANDLE UPLOADED FILE
			require_once(ABSPATH . "wp-admin" . '/includes/image.php');
			require_once(ABSPATH . "wp-admin" . '/includes/file.php');
			require_once(ABSPATH . "wp-admin" . '/includes/media.php');

			// Without that I'm getting a debug error!?
			if( !function_exists( 'wp_get_current_user' ) ) {
				require_once( ABSPATH . 'wp-includes/pluggable.php' );
			}

			if($sheet_url != ''){
				$products = [];
					
				// Obtener contenido CSV
				$csvData = file_get_contents($sheet_url);

				if ($csvData === FALSE) {
					$result = array(
						'error' => true,
						'msg' => __('Error al obtener los datos del Google Sheet.', 'xopifier'),
					);
				}else{
					// Dividir contenido en filas y parsear CSV
					$rows = array_filter(array_map('str_getcsv', explode("\n", $csvData)));

					// echo "<pre>";
					// var_dump($rows);
					// echo "</pre>";
					
					// $data ahora contiene las filas del Excel como arrays
					if(count($rows) > 1){//verifico que el array tengo al menos un row con datos, pq el 1er row es el header en el excel
						foreach($rows as $k => $product){
							if($k > 0){
								$images = $product[3];
								$files = [];
								if($images != '' && $images != NULL){
									$images = explode(',', $images);
									foreach($images as $image_url){
										$image_content = file_get_contents($image_url);
										// Get the filename and extension
										$path_info = pathinfo($image_url);
										$file_name = $path_info['basename']; // e.g., image.jpg
										$file_extension = $path_info['extension']; // e.g., jpg
										// Create a temporary file
										$temp_file = tempnam(sys_get_temp_dir(), 'img_');
										file_put_contents($temp_file, $image_content);
										// Use wp_upload_bits to upload the file
										$upload = wp_upload_bits($file_name, null, file_get_contents($temp_file));
										if (isset($upload['error']) && $upload['error'] != 0) {
											// Handle error
											echo 'Error uploading image: ' . $upload['error'];
										} else {
											// Successfully uploaded
											$attachment = array(
												'guid' => $upload['url'], 
												'post_mime_type' => $upload['type'],
												'post_title' => sanitize_file_name($file_name),
												'post_content' => '',
												'post_status' => 'inherit'
											);
											// Insert the attachment into the database
											$attach_id = wp_insert_attachment($attachment, $upload['file']);
											
											$files[] = array(
												'media' => $attach_id
											);
										}
										// Clean up the temporary file
										unlink($temp_file);
									}
								}

								$variations = [];
								if($product[6] != '' && $product[6] != NULL && $product[7] != '' && $product[7] != NULL){
									$variations[] = array(
										'attribute' => $product[6],
										'description' => $product[7]
									);
								}

								if($product[8] != '' && $product[8] != NULL && $product[9] != '' && $product[9] != NULL){
									$variations[] = array(
										'attribute' => $product[8],
										'description' => $product[9]
									);
								}

								if(!empty($files)){
									$products[] = array(
										'product_name' => $product[0],
										'product_currency' => 'USD',
										'product_price' => $product[2],
										'product_saleprice' => $product[4],
										'product_description' => $product[1],
										'product_categories' => $product[5],
										'product_featured' => 0,
										'product_variations' => $variations,
										'product_media' => $files,
										'product_variations_comments' => ''
									);
								}else{
									$products[] = array(
										'product_name' => $product[0],
										'product_currency' => 'USD',
										'product_price' => $product[2],
										'product_saleprice' => $product[4],
										'product_description' => $product[1],
										'product_categories' => $product[5],
										'product_featured' => 0,
										'product_variations' => $variations,
										'product_variations_comments' => ''
									);
								}
							}
						}
					}


					if(count($products) > 0){
						$db_products = get_field('current_store_pc_products', $store_id);
						$products = array_merge($db_products, $products);
						update_field('current_store_pc_products', $products, $store_id);

						// var_dump(ICL_LANGUAGE_CODE);

						$list_products = step_3_list_products($design_id);//obtengo el listado de productos actualizado

						$db_link_products = get_field('current_store_popular_products', $store_id);//obtengo los productos de link desde la bd
						$linkproductqty = is_array($db_link_products) && count($db_link_products) > 0 ? count($db_link_products) : 0;//cuento los productos link
						$pcproductqty = is_array($products) && count($products) > 0 ? count($products) : 0;//cuento los productos de pc
						$products_qty = $linkproductqty + $pcproductqty;//obtengo la cantidad total de productos

						$service_settings = get_field('service_settings', 'option');

						$products_qty_included = $service_settings['base_service_products_qty_included'];
						$base_price = $service_settings['base_services_price'];
						$aditional_product_price = $service_settings['base_service_aditional_products_price'];

						$aditional_services = get_field('aditional_services', $store_id);

						$payment_methods = get_field('aditional_setup_payment_methods', $store_id);

						$services_array = array();
						$new_price = 0;

						$more_than_10 = false;

						if($products_qty > $products_qty_included){

							$more_than_10 = true;

							$new_price = $base_price + (($products_qty - $products_qty_included) * $aditional_product_price);

							if(is_array($aditional_services) && count($aditional_services) > 0){    
								foreach ($aditional_services as $service) {
									if($service['service'] != 'Cantidad de productos' && $service['service'] != 'Productos extra'){
										$new_price += $service['price'];
									}
								}
							}

							if(is_array($aditional_services) && count($aditional_services) > 0){    
								foreach($aditional_services as $service){
									if($service != '' && ($service['service'] != 'Cantidad de productos' && $service['service'] != 'Productos extra')){
										$services_array[] = array(
											'id' => $service['id'],
											'service' => $service['service'],
											'price' => $service['price'],
											'type' => $service['type'],
											'active' => $service['active'],
										);
									}
								}
							}

							$services_array[] = array(
								'id' => 'extra_products',
								'service' => 'Productos extra',
								'price' => ($products_qty - $products_qty_included) * $aditional_product_price
							);

						}else{

							$new_price = $base_price;

							if(is_array($aditional_services) && count($aditional_services) > 0){    
								foreach ($aditional_services as $service) {
									if($service['service'] != 'Cantidad de productos' && $service['service'] != 'Productos extra'){
										$new_price += $service['price'];
									}
								}
							}

							$services_array = array();
							if(is_array($aditional_services) && count($aditional_services) > 0){    
								foreach($aditional_services as $k => $service){
									if($service != '' && ($service['service'] != 'Cantidad de productos' && $service['service'] != 'Productos extra')){
										$services_array[] = array(
											'id' => $service['id'],
											'service' => $service['service'],
											'price' => $service['price'],
											'type' => $service['type'],
											'active' => $service['active'],
										);
									}
								}
							}

						}

						if($payment_methods){
							$service_settings = get_field('service_settings', 'option');
							$services = $service_settings['services'];
							$service_price = 0;
							foreach($services as $service){
								if($service['id'] == 'payment_methods'){
									$service_price = $service['price'];
								}
							}

							$new_price = $new_price + $service_price;
						}

						update_field('aditional_services', $services_array, $store_id);
						update_field('total_price', $new_price, $store_id);
						update_field('products_qty', $products_qty, $store_id);

						//verifico la cantidad de productos para definir el mensaje a mostrar si se agregaron o no mas de 10 productos
						if($more_than_10){
							$msg = __('Productos actualizados correctamente. Agregó <b>más de '.$products_qty_included.' productos</b> por lo que el costo de su Tienda 1.0 aunmentó a <b>$12 por cada producto extra</b>.', 'xopifier');
						}else{
							$msg = __('Productos actualizados correctamente!', 'xopifier');
						}
					}else{
						$msg = __('No hubo cambios en los productos!', 'xopifier');
					}

					$result = array(
						'error' => false,
						'msg' => $msg,
						'products' => $list_products
					);
				}
			}else{
				$result = array(
					'error' => true,
					'msg' => __('No se especificó la url del Google Sheet correctamente!', 'xopifier'),
				);
			}
		}catch(Exception $e) {
			$result = array(
				'error' => true,
				'msg' => $e->getMessage(),
			);
		}
	}elseif($wsa == 'save-store-products') {
		try{
			// upload files
			$upload_dir       = wp_upload_dir();

			//HANDLE UPLOADED FILE
			require_once(ABSPATH . "wp-admin" . '/includes/image.php');
			require_once(ABSPATH . "wp-admin" . '/includes/file.php');
			require_once(ABSPATH . "wp-admin" . '/includes/media.php');

			// Without that I'm getting a debug error!?
			if( !function_exists( 'wp_get_current_user' ) ) {
				require_once( ABSPATH . 'wp-includes/pluggable.php' );
			}

			$store_id = $_POST['store_id'];

			$LinkProductName = $_POST['field-LinkProductName'];
			$LinkProductLink = $_POST['field-LinkProductLink'];
			$LinkProductCategory = $_POST['field-LinkProductCategory'];
			$LinkProductFeatured = $_POST['field-LinkProductFeatured'];

			$PCProductName = $_POST['field-PCProductName'];
			$PCProductCurrecy = $_POST['field-PCProductCurrecy'];
			$PCProductPrice = $_POST['field-PCProductPrice'];
			$PCProductSalePrice = $_POST['field-PCProductSalePrice'];
			$PCProductDescription = $_POST['field-PCProductDescription'];
			$PCProductCategory = $_POST['field-PCProductCategory'];
			$PCProductMedia = $_FILES['field-PCProductMedia'];
			$PCProductMediaDB = $_POST['field-PCProductMediaDB'];
			$PCProductFeatured = $_POST['field-PCProductFeatured'];
			$PCProductVariations = $_POST['field-PCProductVariations'];
			$PCProductVariationsComment = $_POST['field-PCProductVariationsComment'];

			$variations = [];
			if(is_array($PCProductVariations) && count($PCProductVariations) > 0){
				foreach($PCProductVariations as $k => $variation){
					$var = base64_decode($variation);
					// var_dump($var);
					if($var !== false)
						$variations[] = json_decode($var, true);
				}
			}

			// var_dump($variations);
			// exit;

			$linkproductqty = is_array($LinkProductName) && count($LinkProductName) > 0 ? count($LinkProductName) : 0;
			$pcproductqty = is_array($PCProductName) && count($PCProductName) > 0 ? count($PCProductName) : 0;

			$products_qty = $linkproductqty + $pcproductqty;
			$products_qty_included = $_POST['products_qty_included'];
			$base_price = $_POST['base_price'];
			$aditional_product_price = $_POST['aditional_product_price'];
			$aditional_services = get_field('aditional_services', $store_id);

			$payment_methods = get_field('aditional_setup_payment_methods', $store_id);

			$services_array = array();
			$new_price = 0;

			if($products_qty > $products_qty_included){

				$new_price = $base_price + (($products_qty - $products_qty_included) * $aditional_product_price);

				if(is_array($aditional_services) && count($aditional_services) > 0){    
					foreach ($aditional_services as $service) {
						if($service['service'] != 'Cantidad de productos' && $service['service'] != 'Productos extra'){
							$new_price += $service['price'];
						}
					}
				}

				if(is_array($aditional_services) && count($aditional_services) > 0){    
					foreach($aditional_services as $service){
						if($service != '' && ($service['service'] != 'Cantidad de productos' && $service['service'] != 'Productos extra')){
							$services_array[] = array(
								'id' => $service['id'],
								'service' => $service['service'],
								'price' => $service['price'],
								'type' => $service['type'],
								'active' => $service['active'],
							);
						}
					}
				}

				$services_array[] = array(
					'id' => 'extra_products',
					'service' => 'Productos extra',
					'price' => ($products_qty - $products_qty_included) * $aditional_product_price
				);

			}else{

				$new_price = $base_price;

				if(is_array($aditional_services) && count($aditional_services) > 0){    
					foreach ($aditional_services as $service) {
						if($service['service'] != 'Cantidad de productos' && $service['service'] != 'Productos extra'){
							$new_price += $service['price'];
						}
					}
				}

				$services_array = array();
				if(is_array($aditional_services) && count($aditional_services) > 0){    
					foreach($aditional_services as $k => $service){
						if($service != '' && ($service['service'] != 'Cantidad de productos' && $service['service'] != 'Productos extra')){
							$services_array[] = array(
								'id' => $service['id'],
								'service' => $service['service'],
								'price' => $service['price'],
								'type' => $service['type'],
								'active' => $service['active'],
							);
						}
					}
				}

			}

			if($payment_methods){
				$service_settings = get_field('service_settings', 'option');
				$services = $service_settings['services'];
				$service_price = 0;
				foreach($services as $service){
					if($service['id'] == 'payment_methods'){
						$service_price = $service['price'];
					}
				}

				$new_price = $new_price + $service_price;
			}

			update_field('aditional_services', $services_array, $store_id);
			update_field('total_price', $new_price, $store_id);
			update_field('products_qty', $products_qty, $store_id);

			//=================================================================================
			//products by reference
			//=================================================================================
			$products = array();
			if(is_array($LinkProductName) && count($LinkProductName) > 0){
				foreach($LinkProductName as $k => $productname){
					$products[] = array(
						'product_name' => $productname,
						'product_link' => is_array($LinkProductLink) && count($LinkProductLink) > 0 ? $LinkProductLink[$k] : '',
						'product_categories' => is_array($LinkProductCategory) && count($LinkProductCategory) > 0 ? $LinkProductCategory[$k] : '',
						'product_featured' => is_array($LinkProductFeatured) && count($LinkProductFeatured) > 0 ? $LinkProductFeatured[$k] : '',
					);
				}
			}
			update_field('current_store_popular_products', $products, $store_id);
			//=================================================================================

			//=================================================================================
			//products fron pc
			//=================================================================================

			$products = array();
			if(is_array($PCProductName) and count($PCProductName) > 0){
				foreach($PCProductName as $k => $productname){

					$cu = is_array($PCProductCurrecy) && count($PCProductCurrecy) > 0 ? $PCProductCurrecy[$k] : '';
					$p = is_array($PCProductPrice) && count($PCProductPrice) > 0 ? $PCProductPrice[$k] : '';
					$sp = is_array($PCProductSalePrice) && count($PCProductSalePrice) > 0 ? $PCProductSalePrice[$k] : '';
					$d = is_array($PCProductDescription) && count($PCProductDescription) > 0 ? $PCProductDescription[$k] : '';
					$c = is_array($PCProductCategory) && count($PCProductCategory) > 0 ? $PCProductCategory[$k] : '';
					$f = is_array($PCProductFeatured) && count($PCProductFeatured) > 0 ? $PCProductFeatured[$k] : '';
					$v = is_array($variations) && count($variations) > 0 ? $variations[$k] : '';
					$pvc = is_array($PCProductVariationsComment) && count($PCProductVariationsComment) > 0 ? $PCProductVariationsComment[$k] : '';

					$files = array();

					if($PCProductMediaDB[$k][0] == ""){
						if(is_array($PCProductMedia) && count($PCProductMedia) > 0){
							if($PCProductMedia['name'][$k][0] != ''){
								foreach($PCProductMedia['name'][$k] as $j => $storefilename){
									$file             = array();
									$file['error']    = '';
									$file['tmp_name'] = $PCProductMedia['tmp_name'][$k][$j];
									$file['name']     = $storefilename;
									$file['type']     = $PCProductMedia['type'][$k][$j];
									$file['size']     = $PCProductMedia['size'][$k][$j];

									// upload file to server
									// @new use $file instead of $image_upload
									$file_return      = wp_handle_sideload( $file, array( 'test_form' => false ) );

									$filename = $file_return['file'];
									$attachment = array(
										'post_mime_type' => $file_return['type'],
										'post_title' => preg_replace('/\.[^.]+$/', '', $storefilename),
										'post_content' => '',
										'post_status' => 'inherit',
										'guid' => $upload_dir['url'] . '/' . basename($filename)
									);
									$attach_id = wp_insert_attachment( $attachment, $filename);

									$files[] = array(
										'media' => $attach_id
									);
								}
							}
						}

						if(!empty($files)){
							$products[] = array(
								'product_name' => $productname,
								'product_currency' => $cu,
								'product_price' => $p,
								'product_saleprice' => $sp,
								'product_description' => $d,
								'product_categories' => $c,
								'product_featured' => $f,
								'product_variations' => $v,
								'product_media' => $files,
								'product_variations_comments' => $pvc
							);
						}else{
							$products[] = array(
								'product_name' => $productname,
								'product_currency' => $cu,
								'product_price' => $p,
								'product_saleprice' => $sp,
								'product_description' => $d,
								'product_categories' => $c,
								'product_featured' => $f,
								'product_variations' => $v,
								'product_variations_comments' => $pvc
							);
						}
					}else{
						if(is_array($PCProductMediaDB) and count($PCProductMediaDB) > 0){
							foreach($PCProductMediaDB[$k] as $media){
								$files[] = array(
									'media' => $media
								);
							}
						}

						$products[] = array(
							'product_name' => $productname,
							'product_currency' => $cu,
							'product_price' => $p,
							'product_saleprice' => $sp,
							'product_description' => $d,
							'product_categories' => $c,
							'product_featured' => $f,
							'product_variations' => $v,
							'product_media' => $files,
							'product_variations_comments' => $pvc
						);
					}

				}
			}
			
			update_field('current_store_pc_products', $products, $store_id);
			//=================================================================================

			$step3_status = get_step3_status($store_id);
			$step3_status[$store_id]['products-tab']['products-products-tab'] = 'done';
			update_option('step3_status', $step3_status);

			$result = array(
				'error' => false,
				'msg' => __('Productos actualizados correctamente!', 'xopifier'),
			);

		}catch(Exception $e) {
			$result = array(
				'error' => true,
				'msg' => $e->getMessage(),
			);
		}

	}elseif($wsa == 'save-store-info-reviews-data'){
		$store_id = $_POST["store_id"];

		$ignore = @isset($_POST['ignore']) ? $_POST['ignore'] : false;

		if($ignore){
			$step3_status = get_step3_status($store_id);
			$step3_status[$store_id]['info-tab'][$_POST['tab_id']] = 'done';
			update_option('step3_status', $step3_status);

			$result = array(
				'error' => false,
				'msg' => __('Información adicional guardada correctamente!', 'xopifier'),
			);
		}else{

			if($_POST['disable'] == 'true'){
				$exists = get_posts(array('post_type' => 'store-reviews-data', 'meta_key' => 'store', 'meta_value' => $store_id));
		
				if($exists){
					$store_data_id = $exists[0]->ID;
					$res = wp_delete_post($store_data_id, true);

					if($res != false){

						$reviews_service_price = 0;
						$new_services = [];
						$_service_status = false;

						$aditional_services = get_field('aditional_services', $store_id);
						$total_price = get_field('total_price', $store_id);

						foreach ($aditional_services as $k => $service) {
							if($service['id'] == 'reviews') {
								$reviews_service_price = $service['price'];
								$_service_status = $service['active'];

								$new_services[] = array(
									'id' => $service['id'],
									'service' => $service['service'],
									'price' => $service['price'],
									'type' => $service['type'],
									'active' => false,
								);
							}else{
								$new_services[] = array(
									'id' => $service['id'],
									'service' => $service['service'],
									'price' => $service['price'],
									'type' => $service['type'],
									'active' => $service['active'],
								);
							}
						}

						if($_service_status){
							update_field('total_price', $total_price - $reviews_service_price, $store_id);
							update_field('aditional_services', $new_services, $store_id);

							$result = array(
								'error' => false,
								'total_price' => $total_price - $reviews_service_price,
								'msg' => __('Sección desactivada correctamente!', 'xopifier'),
							);
						}else{
							$result = array(
								'error' => false,
								'msg' => __('Sección desactivada correctamente!', 'xopifier'),
							);
						}
					}else{
						$result = array(
							'error' => true,
							'msg' => __('Ocurrió un error desactivando la sección!', 'xopifier'),
						);
					}
				}
			}else{
				// upload files
				$upload_dir       = wp_upload_dir();

				//HANDLE UPLOADED FILE
				require_once(ABSPATH . "wp-admin" . '/includes/image.php');
				require_once(ABSPATH . "wp-admin" . '/includes/file.php');
				require_once(ABSPATH . "wp-admin" . '/includes/media.php');

				// Without that I'm getting a debug error!?
				if( !function_exists( 'wp_get_current_user' ) ) {
					require_once( ABSPATH . 'wp-includes/pluggable.php' );
				}
				
				$exists = get_posts(array('post_type' => 'store-reviews-data', 'meta_key' => 'store', 'meta_value' => $store_id));
		
				if($exists){
					$store_data_id = $exists[0]->ID;
					//files
					$files = [];

					if(is_array($_FILES['field-upload-info-reviews-files']) && count($_FILES['field-upload-info-reviews-files']) > 0){
						if($_FILES['field-upload-info-reviews-files']['name'][0] != ''){
							foreach($_FILES['field-upload-info-reviews-files']['name'] as $j => $storefilename){
								$file             = array();
								$file['error']    = '';
								$file['tmp_name'] = $_FILES['field-upload-info-reviews-files']['tmp_name'][$j];
								$file['name']     = $storefilename;
								$file['type']     = $_FILES['field-upload-info-reviews-files']['type'][$j];
								$file['size']     = $_FILES['field-upload-info-reviews-files']['size'][$j];
		
								// upload file to server
								// @new use $file instead of $image_upload
								$file_return      = wp_handle_sideload( $file, array( 'test_form' => false ) );
		
								$filename = $file_return['file'];
								$attachment = array(
									'post_mime_type' => $file_return['type'],
									'post_title' => preg_replace('/\.[^.]+$/', '', $storefilename),
									'post_content' => '',
									'post_status' => 'inherit',
									'guid' => $upload_dir['url'] . '/' . basename($filename)
								);
								$attach_id = wp_insert_attachment( $attachment, $filename);
		
								$files[] = array(
									'file' => $attach_id
								);
							}
						}
					}

					update_field('url_reviews', $_POST['field-store-info-reviews-url'], $store_data_id);

					if($_POST['field-upload-info-reviews-files-from-server'] != '1'){
						update_field('files_reviews', $files, $store_data_id);
					}
					
					$step3_status = get_step3_status($store_id);
					$step3_status[$store_id]['info-tab'][$_POST['tab_id']] = 'done';
					update_option('step3_status', $step3_status);
		
					$result = array(
						'error' => false,
						'msg' => __('Datos de reseñas guardados correctamente!', 'xopifier'),
					);
				}else{
					$args = array(
						'post_type' => 'store-reviews-data',
						'post_status' => 'publish',
						'post_title' => get_the_title($store_id),
					);
		
					$store_data_id = wp_insert_post($args, true);
		
					if( ! is_wp_error($store_data_id) ){
						update_field('store', $store_id, $store_data_id);

						//files
						$files = [];

						if(is_array($_FILES['field-upload-info-reviews-files']) && count($_FILES['field-upload-info-reviews-files']) > 0){
							if($_FILES['field-upload-info-reviews-files']['name'][0] != ''){
								foreach($_FILES['field-upload-info-reviews-files']['name'] as $j => $storefilename){
									$file             = array();
									$file['error']    = '';
									$file['tmp_name'] = $_FILES['field-upload-info-reviews-files']['tmp_name'][$j];
									$file['name']     = $storefilename;
									$file['type']     = $_FILES['field-upload-info-reviews-files']['type'][$j];
									$file['size']     = $_FILES['field-upload-info-reviews-files']['size'][$j];
			
									// upload file to server
									// @new use $file instead of $image_upload
									$file_return      = wp_handle_sideload( $file, array( 'test_form' => false ) );
			
									$filename = $file_return['file'];
									$attachment = array(
										'post_mime_type' => $file_return['type'],
										'post_title' => preg_replace('/\.[^.]+$/', '', $storefilename),
										'post_content' => '',
										'post_status' => 'inherit',
										'guid' => $upload_dir['url'] . '/' . basename($filename)
									);
									$attach_id = wp_insert_attachment( $attachment, $filename);
			
									$files[] = array(
										'file' => $attach_id
									);
								}
							}
						}

						update_field('url_reviews', $_POST['field-store-info-reviews-url'], $store_data_id);

						if($_POST['field-upload-info-reviews-files-from-server'] != '1'){
							update_field('files_reviews', $files, $store_data_id);
						}

						$step3_status = get_step3_status($store_id);
						$step3_status[$store_id]['info-tab'][$_POST['tab_id']] = 'done';
						update_option('step3_status', $step3_status);
		
						$result = array(
							'error' => false,
							'msg' => __('Datos de reseñas guardados correctamente!', 'xopifier'),
						);
					}else{
						$result = array(
							'error' => true,
							'msg' => $store_data_id->get_error_message(),
						);
					}
				}	

				$reviews_service_price = 0;
				$new_services = [];

				$aditional_services = get_field('aditional_services', $store_id);
				$total_price = get_field('total_price', $store_id);

				$new_total_price = 0;
				
				$active_reviews = false;
				$service_settings = get_field('service_settings', 'option')['services'];

				foreach ($aditional_services as $k => $service) {
					if($service['id'] == 'reviews') {
						$active_reviews = true;
						$reviews_service_price = $service['price'];

						if(!$service['active']){
							$new_services[] = array(
								'id' => $service['id'],
								'service' => $service['service'],
								'price' => $service['price'],
								'type' => $service['type'],
								'active' => true,
							);	

							update_field('total_price', $total_price + $reviews_service_price, $store_id);
							$new_total_price = $total_price + $reviews_service_price;
						}else{
							$new_services[] = array(
								'id' => $service['id'],
								'service' => $service['service'],
								'price' => $service['price'],
								'type' => $service['type'],
								'active' => $service['active'],
							);	
						}
					}else{
						$new_services[] = array(
							'id' => $service['id'],
							'service' => $service['service'],
							'price' => $service['price'],
							'type' => $service['type'],
							'active' => $service['active'],
						);
					}
				}

				if(!$active_reviews){
					foreach($service_settings as $service){
						if($service['id'] == 'reviews'){
							$new_services[] = array(
								'id' => $service['id'],
								'service' => $service['title'],
								'price' => $service['price'],
								'type' => $service['type'],
								'active' => true
							);

							update_field('total_price', $total_price + $service['price'], $store_id);
							$new_total_price = $total_price + $service['price'];
						}
					}
				}

				update_field('aditional_services', $new_services, $store_id);

				$result = array(
					'error' => false,
					'total_price' => $new_total_price,
					'msg' => __('Datos de reseñas guardados correctamente!', 'xopifier'),
				);
			}
		}
	}elseif($wsa == 'save-store-info-policy'){
		$store_id = $_POST["store_id"];

		$ignore = @isset($_POST['ignore']) ? $_POST['ignore'] : false;

		if($ignore){
			$step3_status = get_step3_status($store_id);
			$step3_status[$store_id]['info-tab']['info-policy-tab'] = 'done';
			update_option('step3_status', $step3_status);

			update_option('policy-info-'.$store_id, 1);

			$result = array(
				'error' => false,
				'msg' => __('Información adicional guardada correctamente!', 'xopifier'),
			);
		}else{

			// var_dump($_POST);
			if($_POST['disable'] == 'true'){
				$exists = get_posts(array('post_type' => 'store-policy-data', 'meta_key' => 'store', 'meta_value' => $store_id));
		
				if($exists){
					$store_data_id = $exists[0]->ID;
					$res = wp_delete_post($store_data_id, true);
					if($res != false){
						$step3_status = get_step3_status($store_id);
						$step3_status[$store_id]['info-tab']['info-policy-tab'] = '';
						update_option('step3_status', $step3_status);

						update_option('policy-info-'.$store_id, 0);

						$result = array(
							'error' => false,
							'msg' => __('Sección desactivada correctamente!', 'xopifier'),
						);
					}else{
						$result = array(
							'error' => true,
							'msg' => __('Ocurrió un error desactivando la sección!', 'xopifier'),
						);
					}
				}else{
					$result = array(
						'error' => false,
						'msg' => __('La sección aún no ha sido activada!', 'xopifier'),
					);
				}
			}else{
				$exists = get_posts(array('post_type' => 'store-policy-data', 'meta_key' => 'store', 'meta_value' => $store_id));
		
				// upload files
				$upload_dir       = wp_upload_dir();

				//HANDLE UPLOADED FILE
				require_once(ABSPATH . "wp-admin" . '/includes/image.php');
				require_once(ABSPATH . "wp-admin" . '/includes/file.php');
				require_once(ABSPATH . "wp-admin" . '/includes/media.php');

				// Without that I'm getting a debug error!?
				if( !function_exists( 'wp_get_current_user' ) ) {
					require_once( ABSPATH . 'wp-includes/pluggable.php' );
				}

				if($exists){
					
					$store_data_id = $exists[0]->ID;
					update_field('billing_address', sanitize_text_field( wp_unslash( $_POST['field-store-info-policy-address'] ?? '' ) ), $store_data_id);
		
					//politicas de privacidad y terminos
					if($_POST['field-store-info-policy-option'] == 'active-policy-group'){
						update_field('policy_active', true, $store_data_id);
						update_field('policy_inactive', false, $store_data_id);

						//files
						$files = [];

						if(is_array($_FILES['field-store-info-policy-files']) && count($_FILES['field-store-info-policy-files']) > 0){
							if($_FILES['field-store-info-policy-files']['name'][0] != ''){
								foreach($_FILES['field-store-info-policy-files']['name'] as $j => $storefilename){
									$file             = array();
									$file['error']    = '';
									$file['tmp_name'] = $_FILES['field-store-info-policy-files']['tmp_name'][$j];
									$file['name']     = $storefilename;
									$file['type']     = $_FILES['field-store-info-policy-files']['type'][$j];
									$file['size']     = $_FILES['field-store-info-policy-files']['size'][$j];
			
									// upload file to server
									// @new use $file instead of $image_upload
									$file_return      = wp_handle_sideload( $file, array( 'test_form' => false ) );
			
									$filename = $file_return['file'];
									$attachment = array(
										'post_mime_type' => $file_return['type'],
										'post_title' => preg_replace('/\.[^.]+$/', '', $storefilename),
										'post_content' => '',
										'post_status' => 'inherit',
										'guid' => $upload_dir['url'] . '/' . basename($filename)
									);
									$attach_id = wp_insert_attachment( $attachment, $filename);
			
									$files[] = array(
										'file' => $attach_id
									);
								}
							}
						}

						if($_POST['field-store-policy-files-from-server'] == '1'){
							update_field('policy_active_group', array(
								'url_privacy_policy' => $_POST['field-store-info-policy-privacy-url'],
							), $store_data_id);
						}else{
							update_field('policy_active_group', array(
								'url_privacy_policy' => $_POST['field-store-info-policy-privacy-url'],
								'files_policy_terms' => $files
							), $store_data_id);
						}

					}else{
						update_field('policy_inactive', true, $store_data_id);
						update_field('policy_active', false, $store_data_id);

						update_field('policy_inactive_group', array(
							'process_time' => sanitize_text_field( wp_unslash( $_POST['field-store-info-policy-proccess-time'] ?? '' ) ),
							'delivery_time' => sanitize_text_field( wp_unslash( $_POST['field-store-info-policy-delivery-time'] ?? '' ) ),
							'taxes' => sanitize_text_field( wp_unslash( $_POST['field-store-info-policy-taxes'] ?? '' ) ),
							'devolutions' => sanitize_text_field( wp_unslash( $_POST['field-store-info-policy-devolutions'] ?? '' ) ),
							'devolutions_conditions' => sanitize_text_field( wp_unslash( $_POST['field-store-info-policy-devolutions-conditions'] ?? '' ) ),
						), $store_data_id);
					}
		
					$step3_status = get_step3_status($store_id);
					$step3_status[$store_id]['info-tab']['info-policy-tab'] = 'done';
					update_option('step3_status', $step3_status);

					update_option('policy-info-'.$store_id, 0);
		
					$result = array(
						'error' => false,
						'msg' => __('Datos de las políticas guardados correctamente!', 'xopifier'),
					);
				}else{
					$args = array(
						'post_type' => 'store-policy-data',
						'post_status' => 'publish',
						'post_title' => get_the_title($store_id),
					);
		
					$store_data_id = wp_insert_post($args, true);
		
					if( ! is_wp_error($store_data_id) ){
						update_field('store', $store_id, $store_data_id);

						update_field('billing_address', sanitize_text_field( wp_unslash( $_POST['field-store-info-policy-address'] ?? '' ) ), $store_data_id);

						//politicas de privacidad y terminos
						if($_POST['field-store-info-policy-option'] == 'active-policy-group'){
							update_field('policy_active', true, $store_data_id);

							//files
							$files = [];

							if(is_array($_FILES['field-store-info-policy-files']) && count($_FILES['field-store-info-policy-files']) > 0){
								if($_FILES['field-store-info-policy-files']['name'][0] != ''){
									foreach($_FILES['field-store-info-policy-files']['name'] as $j => $storefilename){
										$file             = array();
										$file['error']    = '';
										$file['tmp_name'] = $_FILES['field-store-info-policy-files']['tmp_name'][$j];
										$file['name']     = $storefilename;
										$file['type']     = $_FILES['field-store-info-policy-files']['type'][$j];
										$file['size']     = $_FILES['field-store-info-policy-files']['size'][$j];
				
										// upload file to server
										// @new use $file instead of $image_upload
										$file_return      = wp_handle_sideload( $file, array( 'test_form' => false ) );
				
										$filename = $file_return['file'];
										$attachment = array(
											'post_mime_type' => $file_return['type'],
											'post_title' => preg_replace('/\.[^.]+$/', '', $storefilename),
											'post_content' => '',
											'post_status' => 'inherit',
											'guid' => $upload_dir['url'] . '/' . basename($filename)
										);
										$attach_id = wp_insert_attachment( $attachment, $filename);
				
										$files[] = array(
											'file' => $attach_id
										);
									}
								}
							}

							update_field('policy_active_group', array(
								'url_privacy_policy' => $_POST['field-store-info-policy-privacy-url'],
								'files_policy_terms' => $files
							), $store_data_id);

						}else{
							update_field('policy_inactive', true, $store_data_id);

							update_field('policy_inactive_group', array(
								'process_time' => sanitize_text_field( wp_unslash( $_POST['field-store-info-policy-proccess-time'] ?? '' ) ),
								'delivery_time' => sanitize_text_field( wp_unslash( $_POST['field-store-info-policy-delivery-time'] ?? '' ) ),
								'taxes' => sanitize_text_field( wp_unslash( $_POST['field-store-info-policy-taxes'] ?? '' ) ),
								'devolutions' => sanitize_text_field( wp_unslash( $_POST['field-store-info-policy-devolutions'] ?? '' ) ),
								'devolutions_conditions' => sanitize_text_field( wp_unslash( $_POST['field-store-info-policy-devolutions-conditions'] ?? '' ) ),
							), $store_data_id);
						}
		
						$step3_status = get_step3_status($store_id);
						$step3_status[$store_id]['info-tab']['info-policy-tab'] = 'done';
						update_option('step3_status', $step3_status);

						update_option('policy-info-'.$store_id, 0);
		
						$result = array(
							'error' => false,
							'msg' => __('Datos de las políticas guardados correctamente!', 'xopifier'),
						);
					}else{
						$result = array(
							'error' => true,
							'msg' => $store_data_id->get_error_message(),
						);
					}
				}	
			}
		}
	}elseif($wsa == 'get-section-files'){
		$store_id = $_POST['store_id'];

		if(is_array($_POST['from'])){
			$index = $_POST['from'][1];

			$exists = get_posts(array('post_type' => 'initial-stage', 'p' => $store_id));
			if($exists){
				$store_data_id = $exists[0]->ID;

				$_files = get_field('current_store_pc_products', $store_data_id)[$index]['product_media'];
				
				if(is_array($_files) && count($_files) > 0){
					foreach($_files as $_file){

						if($_file != null){
							$result['files'][] = array(
								'name' => $_file['media']['filename'],
								'url' => $_file['media']['url'],
								'type' => $_file['media']['mime_type'],
							);
						}
					}
				}
			}

		}elseif($_POST['from'] == 'custom-files'){
			$exists = get_posts(array('post_type' => 'store-custom-data', 'meta_key' => 'store', 'meta_value' => $store_id));
			if($exists){
				$store_data_id = $exists[0]->ID;

				$_files = get_field('custom_page_files', $store_data_id);
				
				if(is_array($_files) && count($_files) > 0){
					foreach($_files as $_file){
						$result['files'][] = array(
							'name' => $_file['file']['filename'],
							'url' => $_file['file']['url'],
							'type' => $_file['file']['mime_type'],
						);
					}
				}
			}
		}elseif($_POST['from'] == 'about-files'){
			$exists = get_posts(array('post_type' => 'store-data', 'meta_key' => 'store', 'meta_value' => $store_id));
			if($exists){
				$store_data_id = $exists[0]->ID;

				$_files = get_field('featured_images_or_videos', $store_data_id);
				
				if(is_array($_files) && count($_files) > 0){
					foreach($_files as $_file){
						$result['files'][] = array(
							'name' => $_file['file']['filename'],
							'url' => $_file['file']['url'],
							'type' => $_file['file']['mime_type'],
						);
					}
				}
			}
		}elseif($_POST['from'] == 'policy-files'){
			$exists = get_posts(array('post_type' => 'store-policy-data', 'meta_key' => 'store', 'meta_value' => $store_id));
			if($exists){
				$store_data_id = $exists[0]->ID;

				$_files = get_field('policy_active_group', $store_data_id)['files_policy_terms'];
				
				if(is_array($_files) && count($_files) > 0){
					foreach($_files as $_file){
						$result['files'][] = array(
							'name' => $_file['file']['filename'],
							'url' => $_file['file']['url'],
							'type' => $_file['file']['mime_type'],
						);
					}
				}
			}
		}elseif($_POST['from'] == 'shipping-files'){
			$exists = get_posts(array('post_type' => 'store-policy-data', 'meta_key' => 'store', 'meta_value' => $store_id));
			if($exists){
				$store_data_id = $exists[0]->ID;

				$_files = get_field('shipping_policy_active_group', $store_data_id)['files_shipping_policy'];
				
				if(is_array($_files) && count($_files) > 0){
					foreach($_files as $_file){

						if($_file != null){
							$result['files'][] = array(
								'name' => $_file['file']['filename'],
								'url' => $_file['file']['url'],
								'type' => $_file['file']['mime_type'],
							);
						}
					}
				}
			}
		}elseif($_POST['from'] == 'reviews-files'){
			$exists = get_posts(array('post_type' => 'store-reviews-data', 'meta_key' => 'store', 'meta_value' => $store_id));
			if($exists){
				$store_data_id = $exists[0]->ID;

				$_files = get_field('files_reviews', $store_data_id);
				
				if(is_array($_files) && count($_files) > 0){
					foreach($_files as $_file){

						if($_file != null){
							$result['files'][] = array(
								'name' => $_file['file']['filename'],
								'url' => $_file['file']['url'],
								'type' => $_file['file']['mime_type'],
							);
						}
					}
				}
			}
		}elseif($_POST['from'] == 'product-extra-files'){
			$exists = get_posts(array('post_type' => 'initial-stage', 'p' => $store_id));
			if($exists){
				$store_data_id = $exists[0]->ID;

				$_files = get_field('additional_information', $store_data_id)['images_or_animations'];
				
				if(is_array($_files) && count($_files) > 0){
					foreach($_files as $_file){

						if($_file != null){
							$result['files'][] = array(
								'name' => $_file['file']['filename'],
								'url' => $_file['file']['url'],
								'type' => $_file['file']['mime_type'],
							);
						}
					}
				}
			}
		}
	}elseif($wsa == 'save-store-info-custom-data'){
		$store_id = $_POST["store_id"];

		$ignore = @isset($_POST['ignore']) ? $_POST['ignore'] : false;

		if($ignore){
			$step3_status = get_step3_status($store_id);
			$step3_status[$store_id]['info-tab'][$_POST['tab_id']] = 'done';
			update_option('step3_status', $step3_status);

			$result = array(
				'error' => false,
				'msg' => __('Información adicional guardada correctamente!', 'xopifier'),
			);
		}else{

			if($_POST['disable'] == 'true'){
				$exists = get_posts(array('post_type' => 'store-custom-data', 'meta_key' => 'store', 'meta_value' => $store_id));
		
				if($exists){
					$store_data_id = $exists[0]->ID;
					$res = wp_delete_post($store_data_id, true);

					if($res != false){
						$_service_price = 0;
						$new_services = [];
						$_service_status = false;

						$aditional_services = get_field('aditional_services', $store_id);
						$total_price = get_field('total_price', $store_id);

						foreach ($aditional_services as $k => $service) {
							if($service['id'] == 'custom') {
								$_service_price = $service['price'];
								$_service_status = $service['active'];

								$new_services[] = array(
									'id' => $service['id'],
									'service' => $service['service'],
									'price' => $service['price'],
									'type' => $service['type'],
									'active' => false,
								);
							}else{
								$new_services[] = array(
									'id' => $service['id'],
									'service' => $service['service'],
									'price' => $service['price'],
									'type' => $service['type'],
									'active' => $service['active'],
								);
							}
						}
						if($_service_status){
							update_field('total_price', $total_price - $_service_price, $store_id);
							update_field('aditional_services', $new_services, $store_id);

							$result = array(
								'error' => false,
								'total_price' => $total_price - $_service_price,
								'msg' => __('Sección desactivada correctamente!', 'xopifier'),
							);
						}
					}else{
						$result = array(
							'error' => true,
							'msg' => __('Ocurrió un error desactivando la sección!', 'xopifier'),
						);
					}
				}
			}else{
				$exists = get_posts(array('post_type' => 'store-custom-data', 'meta_key' => 'store', 'meta_value' => $store_id));
		
				// upload files
				$upload_dir       = wp_upload_dir();

				//HANDLE UPLOADED FILE
				require_once(ABSPATH . "wp-admin" . '/includes/image.php');
				require_once(ABSPATH . "wp-admin" . '/includes/file.php');
				require_once(ABSPATH . "wp-admin" . '/includes/media.php');

				// Without that I'm getting a debug error!?
				if( !function_exists( 'wp_get_current_user' ) ) {
					require_once( ABSPATH . 'wp-includes/pluggable.php' );
				}

				if($exists){
					$store_data_id = $exists[0]->ID;
		
					update_field('store', $store_id, $store_data_id);
					update_field('custom_page_content', $_POST['field-store-info-custom-content'], $store_data_id);

					//files
					$files = [];

					if(is_array($_FILES['field-store-info-custom-page-files']) && count($_FILES['field-store-info-custom-page-files']) > 0){

						if($_FILES['field-store-info-custom-page-files']['name'][0] != ''){
							foreach($_FILES['field-store-info-custom-page-files']['name'] as $j => $storefilename){
								$file             = array();
								$file['error']    = '';
								$file['tmp_name'] = $_FILES['field-store-info-custom-page-files']['tmp_name'][$j];
								$file['name']     = $storefilename;
								$file['type']     = $_FILES['field-store-info-custom-page-files']['type'][$j];
								$file['size']     = $_FILES['field-store-info-custom-page-files']['size'][$j];
		
								// upload file to server
								// @new use $file instead of $image_upload
								$file_return      = wp_handle_sideload( $file, array( 'test_form' => false ) );
		
								$filename = $file_return['file'];
								$attachment = array(
									'post_mime_type' => $file_return['type'],
									'post_title' => preg_replace('/\.[^.]+$/', '', $storefilename),
									'post_content' => '',
									'post_status' => 'inherit',
									'guid' => $upload_dir['url'] . '/' . basename($filename)
								);

								$attach_id = wp_insert_attachment( $attachment, $filename);
		
								$files[] = array(
									'file' => $attach_id
								);
							}
						}
					}

					if($_POST['field-store-info-custom-page-files-from-server'] != '1'){
						update_field('custom_page_files', $files, $store_data_id);
					}
		
					$step3_status = get_step3_status($store_id);
					$step3_status[$store_id]['info-tab'][$_POST['tab_id']] = 'done';
					update_option('step3_status', $step3_status);
		
					$result = array(
						'error' => false,
						'msg' => __('Datos de Página adicional guardados correctamente!', 'xopifier'),
					);
				}else{
					$args = array(
						'post_type' => 'store-custom-data',
						'post_status' => 'publish',
						'post_title' => get_the_title($store_id),
					);
		
					$store_data_id = wp_insert_post($args, true);
		
					if( ! is_wp_error($store_data_id) ){
						update_field('store', $store_id, $store_data_id);

						update_field('custom_page_content', $_POST['field-store-info-custom-content'], $store_data_id);

						//files
						$files = [];

						if(is_array($_FILES['field-store-info-custom-page-files']) && count($_FILES['field-store-info-custom-page-files']) > 0){
							if($_FILES['field-store-info-custom-page-files']['name'][0] != ''){
								foreach($_FILES['field-store-info-custom-page-files']['name'] as $j => $storefilename){
									$file             = array();
									$file['error']    = '';
									$file['tmp_name'] = $_FILES['field-store-info-custom-page-files']['tmp_name'][$j];
									$file['name']     = $storefilename;
									$file['type']     = $_FILES['field-store-info-custom-page-files']['type'][$j];
									$file['size']     = $_FILES['field-store-info-custom-page-files']['size'][$j];
			
									// upload file to server
									// @new use $file instead of $image_upload
									$file_return      = wp_handle_sideload( $file, array( 'test_form' => false ) );
			
									$filename = $file_return['file'];
									$attachment = array(
										'post_mime_type' => $file_return['type'],
										'post_title' => preg_replace('/\.[^.]+$/', '', $storefilename),
										'post_content' => '',
										'post_status' => 'inherit',
										'guid' => $upload_dir['url'] . '/' . basename($filename)
									);
									$attach_id = wp_insert_attachment( $attachment, $filename);
			
									$files[] = array(
										'file' => $attach_id
									);
								}
							}
						}

						update_field('custom_page_files', $files, $store_data_id);
		
						$step3_status = get_step3_status($store_id);
						$step3_status[$design_id]['info-tab'][$_POST['tab_id']] = 'done';
						update_option('step3_status', $step3_status);
		
						$result = array(
							'error' => false,
							'msg' => __('Datos de la Página adicional guardados correctamente!', 'xopifier'),
						);
					}else{
						$result = array(
							'error' => true,
							'msg' => $store_data_id->get_error_message(),
						);
					}
				}	

				$_service_price = 0;
				$new_services = [];

				$aditional_services = get_field('aditional_services', $store_id);
				$total_price = get_field('total_price', $store_id);

				$new_total_price = 0;

				$active_blog = false;
				$service_settings = get_field('service_settings', 'option')['services'];

				foreach ($aditional_services as $k => $service) {
					if($service['id'] == 'custom') {
						$active_blog = true;
						$_service_price = $service['price'];

						if(!$service['active']){
							$new_services[] = array(
								'id' => $service['id'],
								'service' => $service['service'],
								'price' => $service['price'],
								'type' => $service['type'],
								'active' => true,
							);	
							update_field('total_price', $total_price + $_service_price, $store_id);
							$new_total_price = $total_price + $_service_price;
						}else{
							$new_services[] = array(
								'id' => $service['id'],
								'service' => $service['service'],
								'price' => $service['price'],
								'type' => $service['type'],
								'active' => $service['active'],
							);
						}
					}else{
						$new_services[] = array(
							'id' => $service['id'],
							'service' => $service['service'],
							'price' => $service['price'],
							'type' => $service['type'],
							'active' => $service['active'],
						);
					}
				}

				if(!$active_blog){
					foreach($service_settings as $service){
						if($service['id'] == 'custom'){
							$new_services[] = array(
								'id' => $service['id'],
								'service' => $service['title'],
								'price' => $service['price'],
								'type' => $service['type'],
								'active' => true
							);

							update_field('total_price', $total_price + $service['price'], $store_id);
							$new_total_price = $total_price + $service['price'];
						}
					}
				}

				update_field('aditional_services', $new_services, $store_id);

				$result = array(
					'error' => false,
					'total_price' => $new_total_price,
					'msg' => __('Datos de la página adicional guardados correctamente!', 'xopifier'),
				);
			}
		}
	}elseif($wsa == 'save-store-info-faqs-data'){
		$store_id = $_POST["store_id"];

		if($_POST['disable'] == 'true'){
			$exists = get_posts(array('post_type' => 'store-faqs-data', 'meta_key' => 'store', 'meta_value' => $store_id));
	
			if($exists){
				$store_data_id = $exists[0]->ID;
				$res = wp_delete_post($store_data_id, true);

				if($res != false){
					$_service_price = 0;
					$new_services = [];
					$_service_status = false;

					$aditional_services = get_field('aditional_services', $store_id);
					$total_price = get_field('total_price', $store_id);

					foreach ($aditional_services as $k => $service) {
						if($service['id'] == 'faqs') {
							$_service_price = $service['price'];
							$_service_status = $service['active'];

							$new_services[] = array(
								'id' => $service['id'],
								'service' => $service['service'],
								'price' => $service['price'],
								'type' => $service['type'],
								'active' => false,
							);
						}else{
							$new_services[] = array(
								'id' => $service['id'],
								'service' => $service['service'],
								'price' => $service['price'],
								'type' => $service['type'],
								'active' => $service['active'],
							);
						}
					}

					if($_service_status){
						update_field('total_price', $total_price - $_service_price, $store_id);
						update_field('aditional_services', $new_services, $store_id);

						$result = array(
							'error' => false,
							'total_price' => $total_price - $_service_price,
							'msg' => __('Sección desactivada correctamente!', 'xopifier'),
						);
					}else{
						$result = array(
							'error' => false,
							'msg' => __('Sección desactivada correctamente!', 'xopifier'),
						);
					}
				}else{
					$result = array(
						'error' => true,
						'msg' => __('Ocurrió un error desactivando la sección!', 'xopifier'),
					);
				}
			}
		}else{
			$exists = get_posts(array('post_type' => 'store-faqs-data', 'meta_key' => 'store', 'meta_value' => $store_id));
	
			if($exists){
				$store_data_id = $exists[0]->ID;
	
				update_field('store', $store_id, $store_data_id);

				// var_dump(html_entity_decode($_POST['field-store-info-faqs']), $store_data_id);

				update_field('url_faqs', $_POST['field-store-info-faqs-url'], $store_data_id);
				// update_field('faqs', html_entity_decode($_POST['field-store-info-faqs']), $store_data_id);
				update_post_meta($store_data_id, 'faqs', html_entity_decode($_POST['field-store-info-faqs']));
	
				$step3_status = get_step3_status($store_id);
				$step3_status[$store_id]['info-tab'][$_POST['tab_id']] = 'done';
				update_option('step3_status', $step3_status);
	
				$result = array(
					'error' => false,
					'msg' => __('Datos de FAQs guardados correctamente!', 'xopifier'),
				);
			}else{
				$args = array(
					'post_type' => 'store-faqs-data',
					'post_status' => 'publish',
					'post_title' => get_the_title($store_id),
				);
	
				$store_data_id = wp_insert_post($args, true);
	
				if( ! is_wp_error($store_data_id) ){
					update_field('store', $store_id, $store_data_id);

					update_field('url_faqs', $_POST['field-store-info-faqs-url'], $store_data_id);
					// update_field('faqs', html_entity_decode($_POST['field-store-info-faqs']), $store_data_id);
					update_post_meta($store_data_id, 'faqs', html_entity_decode($_POST['field-store-info-faqs']));
	
					$step3_status = get_step3_status($store_id);
					$step3_status[$store_id]['info-tab'][$_POST['tab_id']] = 'done';
					update_option('step3_status', $step3_status);
	
					$result = array(
						'error' => false,
						'msg' => __('Datos de FAQs guardados correctamente!', 'xopifier'),
					);
				}else{
					$result = array(
						'error' => true,
						'msg' => $store_data_id->get_error_message(),
					);
				}
			}	

			$_service_price = 0;
			$new_services = [];

			$aditional_services = get_field('aditional_services', $store_id);
			$total_price = get_field('total_price', $store_id);

			$new_total_price = 0;

			$active_faqs = false;
			$service_settings = get_field('service_settings', 'option')['services'];

			foreach ($aditional_services as $k => $service) {
				if($service['id'] == 'faqs') {
					$active_faqs = true;
					$_service_price = $service['price'];

					if(!$service['active']){
						$new_services[] = array(
							'id' => $service['id'],
							'service' => $service['service'],
							'price' => $service['price'],
							'type' => $service['type'],
							'active' => true,
						);	
						update_field('total_price', $total_price + $_service_price, $store_id);
						$new_total_price = $total_price + $_service_price;
					}else{
						$new_services[] = array(
							'id' => $service['id'],
							'service' => $service['service'],
							'price' => $service['price'],
							'type' => $service['type'],
							'active' => $service['active'],
						);
					}
				}else{
					$new_services[] = array(
						'id' => $service['id'],
						'service' => $service['service'],
						'price' => $service['price'],
						'type' => $service['type'],
						'active' => $service['active'],
					);
				}
			}

			if(!$active_faqs){
				foreach($service_settings as $service){
					if($service['id'] == 'faqs'){
						$new_services[] = array(
							'id' => $service['id'],
							'service' => $service['title'],
							'price' => $service['price'],
							'type' => $service['type'],
							'active' => true
						);

						update_field('total_price', $total_price + $service['price'], $store_id);
						$new_total_price = $total_price + $service['price'];
					}
				}
			}

			update_field('aditional_services', $new_services, $store_id);

			$result = array(
				'error' => false,
				'total_price' => $new_total_price,
				'msg' => __('Datos de FAQs guardados correctamente!', 'xopifier'),
			);
		}
	}elseif($wsa == 'save-store-info-contact-data'){
		$store_id = $_POST["store_id"];

		$ignore = @isset($_POST['ignore']) ? $_POST['ignore'] : false;

		if($ignore){
			$step3_status = get_step3_status($store_id);
			$step3_status[$store_id]['info-tab']['info-contact-tab'] = 'done';
			update_option('step3_status', $step3_status);

			update_option('contact-info-'.$store_id, 1);

			$result = array(
				'error' => false,
				'msg' => __('Información adicional guardada correctamente!', 'xopifier'),
			);
		}else{

			if($_POST['disable'] == 'true'){
				$exists = get_posts(array('post_type' => 'store-contact-data', 'meta_key' => 'store', 'meta_value' => $store_id));
		
				if($exists){
					$store_data_id = $exists[0]->ID;
					$res = wp_delete_post($store_data_id, true);

					if($res != false){
						$result = array(
							'error' => false,
							'msg' => __('Sección desactivada correctamente!', 'xopifier'),
						);
					}else{
						$result = array(
							'error' => true,
							'msg' => __('Ocurrió un error desactivando la sección!', 'xopifier'),
						);
					}
				}else{
					$result = array(
						'error' => false,
						'msg' => __('La sección aún no ha sido activada!', 'xopifier'),
					);
				}
			}else{
				$contact_page_fields = '';

				if($_POST['field-info-general-contact-field'] != ''){
					foreach($_POST['field-info-general-contact-field'] as $field){
						if($contact_page_fields == '')
							$contact_page_fields .= $field;
						else
						$contact_page_fields .= ','.$field;
					}
				}
		
				$exists = get_posts(array('post_type' => 'store-contact-data', 'meta_key' => 'store', 'meta_value' => $store_id));
		
				if($exists){
					$store_data_id = $exists[0]->ID;
		
					update_field('store', $store_id, $store_data_id);
					update_field('contact_page_fields', sanitize_text_field( wp_unslash( $contact_page_fields ?? '' ) ), $store_data_id);
					update_field('display_info_field', sanitize_text_field( wp_unslash( $_POST['field-store-info-contact-display-info'] ?? '' ) ), $store_data_id);
		
					$step3_status = get_step3_status($store_id);
					$step3_status[$store_id]['info-tab']['info-contact-tab'] = 'done';
					update_option('step3_status', $step3_status);

					update_option('contact-info-'.$store_id, 0);
		
					$result = array(
						'error' => false,
						'msg' => __('Datos de contacto guardados correctamente!', 'xopifier'),
					);
				}else{
					$args = array(
						'post_type' => 'store-contact-data',
						'post_status' => 'publish',
						'post_title' => get_the_title($store_id),
					);
		
					$store_data_id = wp_insert_post($args, true);
		
					if( ! is_wp_error($store_data_id) ){
						update_field('store', $store_id, $store_data_id);
						update_field('contact_page_fields', sanitize_text_field( wp_unslash( $contact_page_fields ?? '' ) ), $store_data_id);
						update_field('display_info_field', sanitize_text_field( wp_unslash( $_POST['field-store-info-contact-display-info'] ?? '' ) ), $store_data_id);
		
						$step3_status = get_step3_status($store_id);
						$step3_status[$store_id]['info-tab']['info-contact-tab'] = 'done';
						update_option('step3_status', $step3_status);

						update_option('contact-info-'.$store_id, 0);
		
						$result = array(
							'error' => false,
							'msg' => __('Datos de contacto guardados correctamente!', 'xopifier'),
						);
					}else{
						$result = array(
							'error' => true,
							'msg' => $store_data_id->get_error_message(),
						);
					}
				}	
			}
		}
	}elseif($wsa == 'save-store-about-info') {

		$store_id = $_POST["store_id"];

		$exists = get_posts(array('post_type' => 'store-data', 'meta_key' => 'store', 'meta_value' => $store_id));

		// upload files
		$upload_dir       = wp_upload_dir();

		//HANDLE UPLOADED FILE
		require_once(ABSPATH . "wp-admin" . '/includes/image.php');
		require_once(ABSPATH . "wp-admin" . '/includes/file.php');
		require_once(ABSPATH . "wp-admin" . '/includes/media.php');

		// Without that I'm getting a debug error!?
		if( !function_exists( 'wp_get_current_user' ) ) {
			require_once( ABSPATH . 'wp-includes/pluggable.php' );
		}

		if($exists){
			$store_data_id = $exists[0]->ID;

			update_field('store', $store_id, $store_data_id);

			update_field('current_store_name', sanitize_text_field( wp_unslash( $_POST['field-store-name'] ?? '' ) ), $store_data_id);
			update_field('current_store_link', sanitize_text_field( wp_unslash( $_POST['field-store-link'] ?? '' ) ), $store_data_id);

			update_field('store_description', sanitize_text_field( wp_unslash( $_POST['field-store-description'] ?? '' ) ), $store_data_id);
			update_field('store_slogan_or_phrase', sanitize_text_field( wp_unslash( $_POST['field-store-phrase'] ?? '' ) ), $store_data_id);
			update_field('featured_option', sanitize_text_field( wp_unslash( $_POST['field-store-featured-options'] ?? '' ) ), $store_data_id);
			update_field('featured_phrase', sanitize_text_field( wp_unslash( $_POST['field-store-featured-other'] ?? '' ) ), $store_data_id);
			
			$files = [];
			
			if($_POST['field-store-particular-description'] != '*'){
				update_field('general_directions', sanitize_text_field( wp_unslash( $_POST['field-store-particular-description'] ?? '' ) ), $store_data_id);

				if(is_array($_FILES['field-store-featured-images']) && count($_FILES['field-store-featured-images']) > 0){
					if($_FILES['field-store-featured-images']['name'][0] != ''){
						foreach($_FILES['field-store-featured-images']['name'] as $j => $storefilename){
							$file             = array();
							$file['error']    = '';
							$file['tmp_name'] = $_FILES['field-store-featured-images']['tmp_name'][$j];
							$file['name']     = $storefilename;
							$file['type']     = $_FILES['field-store-featured-images']['type'][$j];
							$file['size']     = $_FILES['field-store-featured-images']['size'][$j];
	
							// upload file to server
							// @new use $file instead of $image_upload
							$file_return      = wp_handle_sideload( $file, array( 'test_form' => false ) );
	
							$filename = $file_return['file'];
							$attachment = array(
								'post_mime_type' => $file_return['type'],
								'post_title' => preg_replace('/\.[^.]+$/', '', $storefilename),
								'post_content' => '',
								'post_status' => 'inherit',
								'guid' => $upload_dir['url'] . '/' . basename($filename)
							);
							$attach_id = wp_insert_attachment( $attachment, $filename);
	
							$files[] = array(
								'file' => $attach_id
							);
						}
						update_field('featured_images_or_videos', $files, $store_data_id);
					}
				}

				if($_POST['field-store-featured-images-from-server'] != '1'){
					update_field('featured_images_or_videos', $files, $store_data_id);
				}
			}else{
				update_field('general_directions', '', $store_data_id);
				update_field('featured_images_or_videos', $files, $store_data_id);
			}

			$step3_status = get_step3_status($store_id);
			$step3_status[$store_id]['info-tab']['info-store-tab'] = 'done';
			update_option('step3_status', $step3_status);

			$result = array(
				'error' => false,
				'msg' => __('Datos de información general guardados!', 'xopifier'),
			);
		}else{
			$args = array(
				'post_type' => 'store-data',
				'post_status' => 'publish',
				'post_title' => get_the_title($store_id),
			);

			$store_data_id = wp_insert_post($args, true);

			if( ! is_wp_error($store_data_id) ){
				update_field('store', $store_id, $store_data_id);
				update_field('store_description', sanitize_text_field( wp_unslash( $_POST['field-store-description'] ?? '' ) ), $store_data_id);
				update_field('store_slogan_or_phrase', sanitize_text_field( wp_unslash( $_POST['field-store-phrase'] ?? '' ) ), $store_data_id);
				update_field('featured_option', sanitize_text_field( wp_unslash( $_POST['field-store-featured-options'] ?? '' ) ), $store_data_id);
				update_field('general_directions', sanitize_text_field( wp_unslash( $_POST['field-store-particular-description'] ?? '' ) ), $store_data_id);
				update_field('featured_phrase', sanitize_text_field( wp_unslash( $_POST['field-store-featured-other'] ?? '' ) ), $store_data_id);

				$files = array();
			
				if(is_array($_FILES['field-store-featured-images']) && count($_FILES['field-store-featured-images']) > 0){
					if($_FILES['field-store-featured-images']['name'][0] != ''){
						foreach($_FILES['field-store-featured-images']['name'] as $j => $storefilename){
							$file             = array();
							$file['error']    = '';
							$file['tmp_name'] = $_FILES['field-store-featured-images']['tmp_name'][$j];
							$file['name']     = $storefilename;
							$file['type']     = $_FILES['field-store-featured-images']['type'][$j];
							$file['size']     = $_FILES['field-store-featured-images']['size'][$j];

							// upload file to server
							// @new use $file instead of $image_upload
							$file_return      = wp_handle_sideload( $file, array( 'test_form' => false ) );

							$filename = $file_return['file'];
							$attachment = array(
								'post_mime_type' => $file_return['type'],
								'post_title' => preg_replace('/\.[^.]+$/', '', $storefilename),
								'post_content' => '',
								'post_status' => 'inherit',
								'guid' => $upload_dir['url'] . '/' . basename($filename)
							);
							$attach_id = wp_insert_attachment( $attachment, $filename);

							$files[] = array(
								'file' => $attach_id
							);
						}
						update_field('featured_images_or_videos', $files, $store_data_id);
					}
				}

				$step3_status = get_step3_status($store_id);
				$step3_status[$store_id]['info-tab']['info-store-tab'] = 'done';
				update_option('step3_status', $step3_status);

				$result = array(
					'error' => false,
					'msg' => __('Datos de información general guardados!', 'xopifier'),
				);
			}else{
				$result = array(
					'error' => true,
					'msg' => $store_data_id->get_error_message(),
				);
			}
		}
	}elseif($wsa == 'save-store-promos-discount-data') {
		$store_id = $_POST["store_id"];
		$ignore = @isset($_POST['ignore']) ? $_POST['ignore'] : false;

		if($ignore){
			$step3_status = get_step3_status($store_id);
			$step3_status[$store_id]['promos-tab']['promos-discount-tab'] = 'done';
			update_option('step3_status', $step3_status);

			$result = array(
				'error' => false,
				'msg' => __('Información adicional guardada correctamente!', 'xopifier'),
			);
		}else{

			$exists = get_posts(array('post_type' => 'store-promo-data', 'meta_key' => 'store', 'meta_value' => $store_id));
			$ok = false;

			if($exists){
				$store_data_id = $exists[0]->ID;
				if($_POST['disable'] == 'true'){
					update_field('store_discount', '', $store_data_id);
					update_field('store_discount_indications', '', $store_data_id);

					$step3_status = get_step3_status($store_id);
					$step3_status[$store_id]['promos-tab']['promos-discount-tab'] = '';
					update_option('step3_status', $step3_status);

					if(get_field('store_ad', $store_data_id) == '' && get_field('store_ad_indications', $store_data_id) == ''){
						$res = wp_delete_post($store_data_id, true);
					}
				}else{
					$ok = true;
				}
			}else{
				if($_POST['disable'] == 'false'){
					$args = array(
						'post_type' => 'store-promo-data',
						'post_status' => 'publish',
						'post_title' => get_the_title($store_id),
					);

					$store_data_id = wp_insert_post($args, true);

					if( ! is_wp_error($store_data_id) ){
						$ok = true;
					}else{
						$result = array(
							'error' => true,
							'msg' => $store_data_id->get_error_message(),
						);
					}
				}
			}
			if($ok){
				update_field('store', $store_id, $store_data_id);
				update_field('store_discount', sanitize_text_field( wp_unslash( $_POST['field-store-promos-discount'] ?? '' ) ), $store_data_id);
				update_field('store_discount_indications', sanitize_text_field( wp_unslash( $_POST['field-store-promos-discount-indications'] ?? '' ) ), $store_data_id);

				$step3_status = get_step3_status($store_id);
				$step3_status[$store_id]['promos-tab']['promos-discount-tab'] = 'done';
				update_option('step3_status', $step3_status);

				$result = array(
					'error' => false,
					'msg' => __('Datos de la suscriptor de E-mail guardados correctamente!', 'xopifier'),
				);
			}
		}
	}elseif($wsa == 'save-store-promos-ads-data') {
		$store_id = $_POST["store_id"];
		$ignore = @isset($_POST['ignore']) ? $_POST['ignore'] : false;

		if($ignore){
			$step3_status = get_step3_status($store_id);
			$step3_status[$store_id]['promos-tab']['promos-ads-tab'] = 'done';
			update_option('step3_status', $step3_status);

			$result = array(
				'error' => false,
				'msg' => __('Información adicional guardada correctamente!', 'xopifier'),
			);
		}else{

			$exists = get_posts(array('post_type' => 'store-promo-data', 'meta_key' => 'store', 'meta_value' => $store_id));
			$ok = false;

			if($exists){
				$store_data_id = $exists[0]->ID;
				if($_POST['disable'] == 'true'){
					update_field('store_ad', '', $store_data_id);
					update_field('store_ad_indications', '', $store_data_id);

					$step3_status = get_step3_status($store_id);
					$step3_status[$store_id]['promos-tab']['promos-ads-tab'] = '';
					update_option('step3_status', $step3_status);

					if(get_field('store_discount', $store_data_id) == '' && get_field('store_discount_indications', $store_data_id) == ''){
						$res = wp_delete_post($store_data_id, true);
					}
				}else{
					$ok = true;
				}
			}else{
				if($_POST['disable'] == 'false'){
					$args = array(
						'post_type' => 'store-promo-data',
						'post_status' => 'publish',
						'post_title' => get_the_title($store_id),
					);

					$store_data_id = wp_insert_post($args, true);

					if( ! is_wp_error($store_data_id) ){
						$ok = true;
					}else{
						$result = array(
							'error' => true,
							'msg' => $store_data_id->get_error_message(),
						);
					}
				}
			}
			if($ok){
				update_field('store', $store_id, $store_data_id);
				update_field('store_ad', sanitize_text_field( wp_unslash( $_POST['field-store-promos-ad'] ?? '' ) ), $store_data_id);
				update_field('store_ad_indications', sanitize_text_field( wp_unslash( $_POST['field-store-promos-indications'] ?? '' ) ), $store_data_id);

				$step3_status = get_step3_status($store_id);
				$step3_status[$store_id]['promos-tab']['promos-ads-tab'] = 'done';
				update_option('step3_status', $step3_status);

				$result = array(
					'error' => false,
					'msg' => __('Datos del anuncio o promoci&oacute;n guardados correctamente!', 'xopifier'),
				);
			}
		}
	}elseif($wsa == 'toggle-aditional-information') {
		$store_id = $_POST['store_id'];

		// $res = update_field('additional_information', array('included' => false), $store_id);

		$res = update_field('additional_information', array(
			'included' => false,
			'products_featured_information' => '',
			'images_or_animations' => array()
		), $store_id);

		$step3_status = get_step3_status($store_id);
		$step3_status[$store_id]['products-tab']['products-extra-info-tab'] = '';
		update_option('step3_status', $step3_status);

		$result = array(
			'error' => false,
			'msg' => $res,
		);

	}elseif($wsa == 'save-store-products-extra') {
		$store_id = $_POST['store_id'];
		$ignore = @isset($_POST['ignore']) ? $_POST['ignore'] : false;

		if($ignore){
			$step3_status = get_step3_status($store_id);
			$step3_status[$store_id]['products-tab']['products-extra-info-tab'] = 'done';
			update_option('step3_status', $step3_status);

			$result = array(
				'error' => false,
				'msg' => __('Información adicional guardada correctamente!', 'xopifier'),
			);
		}else{
			// upload files
			$upload_dir       = wp_upload_dir();

			//HANDLE UPLOADED FILE
			require_once(ABSPATH . "wp-admin" . '/includes/image.php');
			require_once(ABSPATH . "wp-admin" . '/includes/file.php');
			require_once(ABSPATH . "wp-admin" . '/includes/media.php');

			// Without that I'm getting a debug error!?
			if( !function_exists( 'wp_get_current_user' ) ) {
				require_once( ABSPATH . 'wp-includes/pluggable.php' );
			}
			
			$productFiles = $_FILES['field-store-animations'];
			$additional_information = get_field('additional_information', $store_id);
	
			$files = [];
	
			try{
				if(is_array($productFiles['name']) && count($productFiles['name']) > 0 && $productFiles['name'][0] != ''){
					foreach($productFiles['name'] as $j => $storefilename){
	
						$file             = array();
						$file['error']    = '';
						$file['tmp_name'] = $productFiles['tmp_name'][$j];
						$file['name']     = $storefilename;
						$file['type']     = $productFiles['type'][$j];
						$file['size']     = $productFiles['size'][$j];
	
						// upload file to server
						// @new use $file instead of $image_upload
						$file_return      = wp_handle_sideload( $file, array( 'test_form' => false ) );
	
						$filename = $file_return['file'];
						$attachment = array(
							'post_mime_type' => $file_return['type'],
							'post_title' => preg_replace('/\.[^.]+$/', '', $storefilename),
							'post_content' => '',
							'post_status' => 'inherit',
							'guid' => $upload_dir['url'] . '/' . basename($filename)
						);
						$attach_id = wp_insert_attachment( $attachment, $filename);
	
						$files[] = array(
							'file' => $attach_id
						);
					}
	
					$res = update_field('additional_information', array(
						'included' => true,
						'products_featured_information' => $_POST['field-store-products-extra-featured'],
						'images_or_animations' => $files
					), $store_id);
				}elseif($_POST['field-store-animations-from-server'] == 1){
					$res = update_field('additional_information', array(
						'included' => true,
						'products_featured_information' => $_POST['field-store-products-extra-featured'],
					), $store_id);
				}else{
					if(is_array($additional_information['images_or_animations']) && count($additional_information['images_or_animations']) > 0){
						$res = update_field('additional_information', array(
							'included' => true,
							'products_featured_information' => $_POST['field-store-products-extra-featured'],
							'images_or_animations' => array()
						), $store_id);	
					}else{
						$res = update_field('additional_information', array(
							'included' => true,
							'products_featured_information' => $_POST['field-store-products-extra-featured'],
						), $store_id);
					}
				}
	
				$step3_status = get_step3_status($store_id);
				// var_dump($step3_status);
				$step3_status[$store_id]['products-tab']['products-extra-info-tab'] = 'done';
				// var_dump($step3_status);
				update_option('step3_status', $step3_status);
	
				$result = array(
					'error' => false,
					'msg' => __('Información adicional guardada correctamente!', 'xopifier'),
				);
			}catch (Exception $ex) {
				$result = array(
					'error' => true,
					'msg' => $ex->getMessage(),
				);
			}	
		}
	}elseif($wsa == 'save-store-products-categories') {

		$currentStoreProductCategory = $_POST['field-store-category'];
		$store_id = $_POST['store_id'];

		$categories = array();
		foreach($currentStoreProductCategory as $k => $categ){
			if($categ != ''){
				$categories[] = array(
					'category' => $categ,
				);
			}
		}
		update_field('current_store_product_categories', $categories, $store_id);

		$step3_status = get_step3_status($store_id);
		$step3_status[$store_id]['products-tab']['products-categories-tab'] = 'done';
		update_option('step3_status', $step3_status);

		$result = array(
			'error' => false,
			'msg' => __('Categorías guardadas correctamente!', 'xopifier'),
		);

	}elseif($wsa == 'select-design') {
		try{
			update_field('status', 'approved_design', $_POST['product-store-id']);
			update_field('approved-design', $_POST['product-name'], $_POST['product-store-id']);
			update_field('approved-design-id', $_POST['product-approved-design'], $_POST['product-store-id']);
			update_field('comment_approved', $_POST['product-approved-design-comment'], $_POST['product-store-id']);

			if($_POST['product-approved-design-comment'] == '')
				$email_select_design = get_field('email_select_design_no_comments', 'option');
			else
				$email_select_design = get_field('email_select_design_with_comments', 'option');
			
			$user = wp_get_current_user();

			$title = str_replace('{{client_name}}', $user->first_name, __('¡Gracias por elegir tu diseño, {{client_name}}!', 'xopifier'));
			$html_title = str_replace('{{client_name}}', $user->first_name, $email_select_design['title']);
			$title = array(
				'text' => $title,
				'html' => $html_title
			);

			if(send_mail('generic-2', $user->user_email, $email_select_design['image']['url'], $title, $email_select_design['description'], $email_select_design['description_2'], $email_select_design['button']['url'], $email_select_design['button']['title'])){
				
				//enviar correo al administrador con los datos del nuevo cliente
				$email_select_design_admin = get_field('email_admin_select_design', 'option');
				
				$store = get_post($_POST['product-store-id']);

				$title = $email_select_design_admin['title'];
				$title = str_replace('{{client_name}}', $user->user_email, $title);

				$description = $email_select_design_admin['description'];
				$description = str_replace('{{client_name}}', $user->user_email, $description);
				$description = str_replace('{{store}}', '<stron>'.$store->post_title.'</stron>', $description);
				$description = str_replace('{{selecteddesign}}', $_POST['product-approved-design'], $description);
				$description = str_replace('{{storeurl}}', site_url('/wp-admin/post.php?post='.$_POST['product-store-id'].'&action=edit'), $description);
				
				send_mail('generic', get_field('emails_to', 'option'), $email_select_design_admin['image']['url'], $title, $description);
			}

			$result = array(
				'error' => false,
			);
		}catch( Exception $e ){
			$result = array(
				'error' => true,
				'message' => $e->getMessage()
			);
		}
	}elseif($wsa == 'create_user_no_payment'){
		$userdata = array(
			'user_login' => $_POST['data']['actual_store_client_email'],
			'user_url'   => '',
			'first_name' => $_POST['data']['actual_store_client_name'],
			'last_name' => '',
			'user_email' => $_POST['data']['actual_store_client_email'],
			'user_pass'  => 'qwerty@135'
		);
		$user_id = wp_insert_user( $userdata ) ;

		// On success.
		if ( ! is_wp_error( $user_id ) ) {
			//user created successfully

			$user = get_user_by( 'id', $user_id ); 
			$useremail = $user->user_email;

			wp_set_current_user( $user_id, $user->user_login );
			wp_set_auth_cookie( $user_id );

			$storeName = str_replace('https://', '', str_replace('http://', '', $_POST['data']['actual_store_link']));
			$storeName = $storeName != '' ? $useremail.' ['.$storeName.']' : $useremail;

			$stage_args = array(
				'post_type' => 'initial-stage',
				'post_status' => 'publish',
				'post_title' => $storeName
			);

			$stage_id = wp_insert_post($stage_args, true);

			if( ! is_wp_error($stage_id) ){
				update_field('user', $user_id, $stage_id);
				update_field('status', 'pending', $stage_id);
				update_field('current_store_name', $storeName, $stage_id);
				update_field('current_store_link', $_POST['data']['actual_store_link'], $stage_id);

				update_field('current_store_actual_shopify_account', $_POST['data']['actual_shopify_store'], $stage_id);
				update_field('current_store_actual_shopify_account_link', $_POST['data']['actual_store_link'], $stage_id);
				update_field('current_store_improve_desc', $_POST['data']['actual_store_improve'], $stage_id);
				update_field('current_store_client_name', $_POST['data']['actual_store_client_name'], $stage_id);
				update_field('current_store_client_email', $_POST['data']['actual_store_client_email'], $stage_id);

				$result = array(
					'error' => false,
					'message' => 'OK'
				);
			}else{
				$result = array(
					'error' => true,
					'message' => __('Error creando la tienda.', 'xopifier')
				);
			}
		}else{
			$result = array(
				'error' => true,
				'message' => __('Error creando el usuario, posiblemente ya haya un usuario con el mismo email.', 'xopifier')
			);
		}
	}elseif($wsa == 'create_user_account'){
		// upload files
		$upload_dir       = wp_upload_dir();

		//HANDLE UPLOADED FILE
		require_once(ABSPATH . "wp-admin" . '/includes/image.php');
		require_once(ABSPATH . "wp-admin" . '/includes/file.php');
		require_once(ABSPATH . "wp-admin" . '/includes/media.php');

		// Without that I'm getting a debug error!?
		if( !function_exists( 'wp_get_current_user' ) ) {
			require_once( ABSPATH . 'wp-includes/pluggable.php' );
		}

		$userdata = array(
			'user_login' => $_POST['field-useremail'],
			'user_url'   => '',
			'first_name' => $_POST['field-firstname'],
			'last_name' => $_POST['field-lastname'],
			'user_email' => $_POST['field-useremail'],
			'user_pass'  => $_POST['field-userpass']
		);

		$currentStoreName = isset($_POST['field-currentStoreName']) ? $_POST['field-currentStoreName'] : '';
		$currentStoreLink = isset($_POST['field-currentStoreLink']) ? $_POST['field-currentStoreLink'] : '';
		$currentStoreDescription = isset($_POST['field-currentStoreDescription']) ? $_POST['field-currentStoreDescription'] : '';
		$currentStoreClients = isset($_POST['field-currentStoreClients']) ? $_POST['field-currentStoreClients'] : '';

		//productos de referenca
		$currentStoreLinkProductName = isset($_POST['field-LinkProductName']) ? $_POST['field-LinkProductName'] : '';
		$currentStoreLinkProductLink = isset($_POST['field-LinkProductLink']) ? $_POST['field-LinkProductLink'] : '';
		$currentStoreLinkProductCategory = isset($_POST['field-LinkProductCategory']) ? $_POST['field-LinkProductCategory'] : '';

		//productos desde PC
		$currentStorePCProductName = isset($_POST['field-PCProductName']) ? $_POST['field-PCProductName'] : '';
		$currentStorePCProductCurrecy = isset($_POST['field-PCProductCurrecy']) ? $_POST['field-PCProductCurrecy'] : '';
		$currentStorePCProductPrice = isset($_POST['field-PCProductPrice']) ? $_POST['field-PCProductPrice'] : '';
		$currentStorePCProductSalePrice = isset($_POST['field-PCProductSalePrice']) ? $_POST['field-PCProductSalePrice'] : '';
		$currentStorePCProductDescription = isset($_POST['field-PCProductDescription']) ? $_POST['field-PCProductDescription'] : '';
		$currentStorePCProductCategory = isset($_POST['field-PCProductCategory']) ? $_POST['field-PCProductCategory'] : '';
		$currentStorePCProductMedia = isset($_FILES['field-PCProductMedia']) ? $_FILES['field-PCProductMedia'] : '';

		$currentStoreProductCategory = isset($_POST['field-currentStoreProductCategory']) ? $_POST['field-currentStoreProductCategory'] : '';
		$currentStorePhrases = isset($_POST['field-currentStorePhrases']) ? $_POST['field-currentStorePhrases'] : '';
		$currentStorePhrasesMore = isset($_POST['field-currentStorePhrases-more']) ? $_POST['field-currentStorePhrases-more'] : '';
		$storeLikeWhat = isset($_POST['field-storeLikeWhat']) ? $_POST['field-storeLikeWhat'] : '';
		$storeLikeWhy = isset($_POST['field-storeLikeWhy']) ? $_POST['field-storeLikeWhy'] : '';
		$storeLikePages = isset($_POST['field-storeLikePages']) ? $_POST['field-storeLikePages'] : '';
		$currentStoreLogo = isset($_FILES['field-currentStoreLogo']) ? $_FILES['field-currentStoreLogo'] : '';

		$currentStoreClientFormality = isset($_POST['field-currentStoreClientFormality']) ? $_POST['field-currentStoreClientFormality'] : '';//current_store_client_formality
		$storeShopifyAccount = isset($_POST['field-storeShopifyAccount']) ? $_POST['field-storeShopifyAccount'] : '';//current_store_shopify_account
		$storeShopifyAccountDesc = isset($_POST['field-storeShopifyAccountDesc']) ? $_POST['field-storeShopifyAccountDesc'] : '';//current_store_shopify_account_desc
		$storeActualShopifyAccount = isset($_POST['field-storeActualShopifyAccount']) ? $_POST['field-storeActualShopifyAccount'] : '';//current_store_actual_shopify_account
		$storeActualLink = isset($_POST['field-storeActualLink']) ? $_POST['field-storeActualLink'] : '';//current_store_actual_shopify_account_link
		$storeImproveDesc = isset($_POST['field-storeImproveDesc']) ? $_POST['field-storeImproveDesc'] : '';//current_store_improve_desc
		$storeClientName = isset($_POST['field-storeClientName']) ? $_POST['field-storeClientName'] : '';//current_store_client_name
		$storeClientEmail = isset($_POST['field-storeClientEmail']) ? $_POST['field-storeClientEmail'] : '';//current_store_client_email

		$selectedScenery = isset($_POST['field-selected-scenery']) ? $_POST['field-selected-scenery'] : '';
		$productsQty = isset($_POST['field-products-qty']) ? $_POST['field-products-qty'] : '';
		$totalPrice = isset($_POST['field-total-price']) ? $_POST['field-total-price'] : '';
		$services = isset($_POST['field-services']) ? $_POST['field-services'] : '';
		$services_price = isset($_POST['field-services-price']) ? explode(',', $_POST['field-services-price']) : [];

		$service_custom_page = isset($_POST['field-custom-page']) ? $_POST['field-custom-page'] : '';

		$store_country = isset($_POST['field-storeCountry']) ? $_POST['field-storeCountry'] : '';
		$store_shopify_account = isset($_POST['field-storeShopifyAccount']) ? $_POST['field-storeShopifyAccount'] : '';
		$store_shopify_account_options = isset($_POST['field-storeShopifyYESAccount']) ? $_POST['field-storeShopifyYESAccount'] : '';
		
		$user_id = wp_insert_user( $userdata ) ;

		$useremail = '';
		
		// On success.
		if ( ! is_wp_error( $user_id ) ) {
			
			//user created successfully
			$user = get_user_by( 'id', $user_id ); 
			$useremail = $user->user_email;

			wp_set_current_user( $user_id, $user->user_login );
			wp_set_auth_cookie( $user_id );
			// do_action( 'wp_login', $user->user_login, $user );

			$storeName = str_replace('https://', '', str_replace('http://', '', $_POST['data']['actual_store_link']));
			$storeName = $storeName != '' ? $useremail.' ['.$storeName.']' : $useremail;

			$stage_args = array(
				'post_type' => 'initial-stage',
				'post_status' => 'publish',
				'post_title' => $storeName
			);

			$stage_id = wp_insert_post($stage_args, true);

			if( ! is_wp_error($stage_id) ){

				update_field('user', $user_id, $stage_id);

				update_field('status', 'payment_pending', $stage_id);

				update_field('current_store_name', $currentStoreName != '' ? $currentStoreName : $storeName, $stage_id);
				update_field('current_store_link', $currentStoreLink, $stage_id);
				update_field('current_store_description', $currentStoreDescription, $stage_id);
				update_field('current_store_client_description', $currentStoreClients, $stage_id);

				update_field('current_store_reference_design', $storeLikeWhat, $stage_id);
				update_field('current_store_reference_design_likes', $storeLikeWhy, $stage_id);
				update_field('current_store_other_pages', $storeLikePages, $stage_id);

				update_field('current_store_scenery', $selectedScenery, $stage_id);
				update_field('products_qty', $productsQty, $stage_id);
				update_field('total_price', $totalPrice, $stage_id);

				update_field('store_country', $store_country, $stage_id);
				update_field('shopify_account', $store_shopify_account, $stage_id);
				update_field('shopify_account_options', $store_shopify_account_options, $stage_id);

				update_field('current_store_client_formality', $currentStoreClientFormality, $stage_id);
				update_field('current_store_shopify_account', $storeShopifyAccount, $stage_id);
				update_field('current_store_shopify_account_desc', $storeShopifyAccountDesc, $stage_id);
				update_field('current_store_actual_shopify_account', $storeActualShopifyAccount, $stage_id);
				update_field('current_store_actual_shopify_account_link', $storeActualLink, $stage_id);
				update_field('current_store_improve_desc', $storeImproveDesc, $stage_id);
				update_field('current_store_client_name', $storeClientName, $stage_id);
				update_field('current_store_client_email', $storeClientEmail, $stage_id);

				$langs = get_field('languages', 'option');
				$languages = [];
				foreach($langs as $lang){
					$languages[] = $lang['language'];
				}

				$custom_page = false;
				$payment_methods = false;

				$services_array = array();
				foreach($services as $k => $service){
					$serv = explode('|', $service);
					if(is_array($serv)){
						$service_id = $serv[0];
						$service = $serv[1];
					}else{
						$service_id = 'lang';
						$service = $serv;
					}

					if($service != ''){

						if($service_id == 'payment_methods'){
							$payment_methods = true;
						}else{
							$type = in_array($service, $languages) ? 'lang' : 'extra';
						
							if($service_id == 'custom'){
								$custom_page = true;
							}

							$services_array[] = array(
								'id' => $service_id,
								'service' => $service,
								'price' => $services_price[$k],
								'type' => $type
							);
						}
					}
				}
				update_field('aditional_services', $services_array, $stage_id);

				if($payment_methods){
					update_field('aditional_setup_payment_methods', true, $stage_id);
				}

				//si marcaron la opcion de custom page entonces creo la opcion del post_type de custom page
				if($custom_page){
					$_args = array(
						'post_type' => 'store-custom-data',
						'post_status' => 'publish',
						'post_title' => $useremail.' ['.$currentStoreName != '' ? $currentStoreName : str_replace('https://', '', str_replace('http://', '', $storeActualLink)).']'
					);

					$custom_page_id = wp_insert_post($_args, true);
					if( ! is_wp_error($custom_page_id) ){
						update_field('store', $stage_id, $custom_page_id);
						update_field('custom_page_content', $service_custom_page, $custom_page_id);
					}
				}

				//=================================================================================
				//products by reference
				//=================================================================================
				$products = array();
				foreach($currentStoreLinkProductName as $k => $productname){
					$products[] = array(
						'product_name' => $productname,
						'product_link' => $currentStoreLinkProductLink[$k],
						'product_categories' => $currentStoreLinkProductCategory[$k]
					);
				}
				update_field('current_store_popular_products', $products, $stage_id);
				//=================================================================================

				//=================================================================================
				//products fron pc
				//=================================================================================

				$products = array();
				foreach($currentStorePCProductName as $k => $productname){

					$files = array();

					if(is_array($currentStorePCProductMedia) && count($currentStorePCProductMedia) > 0){
						foreach($currentStorePCProductMedia['name'][$k] as $j => $storefilename){
							$file             = array();
							$file['error']    = '';
							$file['tmp_name'] = $currentStorePCProductMedia['tmp_name'][$k][$j];
							$file['name']     = $storefilename;
							$file['type']     = $currentStorePCProductMedia['type'][$k][$j];
							$file['size']     = $currentStorePCProductMedia['size'][$k][$j];
	
							// upload file to server
							// @new use $file instead of $image_upload
							$file_return      = wp_handle_sideload( $file, array( 'test_form' => false ) );
	
							$filename = $file_return['file'];
							$attachment = array(
								'post_mime_type' => $file_return['type'],
								'post_title' => preg_replace('/\.[^.]+$/', '', $storefilename),
								'post_content' => '',
								'post_status' => 'inherit',
								'guid' => $upload_dir['url'] . '/' . basename($filename)
							);
							$attach_id = wp_insert_attachment( $attachment, $filename);
	
							$files[] = array(
								'media' => $attach_id
							);
						}
					}

					$products[] = array(
						'product_name' => $productname,
						'product_currency' => $currentStorePCProductCurrecy[$k],
						'product_price' => $currentStorePCProductPrice[$k],
						'product_saleprice' => $currentStorePCProductSalePrice[$k],
						'product_description' => $currentStorePCProductDescription[$k],
						'product_categories' => $currentStorePCProductCategory[$k],
						'product_media' => $files
					);
				}
				update_field('current_store_pc_products', $products, $stage_id);
				//=================================================================================

				$categories = array();
				foreach($currentStoreProductCategory as $k => $categ){
					if($categ != ''){
						$categories[] = array(
							'category' => $categ,
						);
					}
				}
				update_field('current_store_product_categories', $categories, $stage_id);

				$phrases = array();
				foreach($currentStorePhrases as $k => $phrase){
					$phrases[] = array(
						'phrase' => $phrase,
					);
				}
				if($currentStorePhrasesMore != ''){
					$phrases[] = array(
						'phrase' => $currentStorePhrasesMore,
					);
				}
				update_field('current_store_phrases', $phrases, $stage_id);

				if(is_array($currentStoreLogo) && count($currentStoreLogo) > 0){

					$files = array();
					foreach($currentStoreLogo['name'] as $k => $storefilename){
						$file             = array();
						$file['error']    = '';
						$file['tmp_name'] = $currentStoreLogo['tmp_name'][$k];
						$file['name']     = $storefilename;
						$file['type']     = $currentStoreLogo['type'][$k];
						$file['size']     = $currentStoreLogo['size'][$k];

						// upload file to server
						// @new use $file instead of $image_upload
						$file_return      = wp_handle_sideload( $file, array( 'test_form' => false ) );

						$filename = $file_return['file'];
						$attachment = array(
							'post_mime_type' => $file_return['type'],
							'post_title' => preg_replace('/\.[^.]+$/', '', $storefilename),
							'post_content' => '',
							'post_status' => 'inherit',
							'guid' => $upload_dir['url'] . '/' . basename($filename)
						);
						$attach_id = wp_insert_attachment( $attachment, $filename);

						$files[] = array(
							'file' => $attach_id
						);
					}
					if(!empty($files)){
						update_field('current_store_files', $files, $stage_id);
					}
				}

				//=============================================================================
				//=============================================================================
				//=============================================================================

				try{

					$products = get_posts(array('post_type' => 'product', 'post_status' => 'publish', 'meta_query' => array(
						array(
							'key' => 'product-store-id',
							'value' => $_POST['product-store-id'],
						),
						array(
							'key' => 'product-type',
							'value' => 'advanced-payment',
						)
					)));

					if(is_array($products) and count($products) > 0){
						$result = array(
							'error' => true,
							'message' => __('Ya ha realizado el pago adelantado!', 'xopifier')
						);
					}else{
						$service_settings = get_field('service_settings', 'option');
						// aqui creo el producto
						$product = new WC_Product_Simple();
						$product->set_name( $currentStoreName != '' ? $currentStoreName : $useremail .' - Pago adelantado');
						$product->set_regular_price( $service_settings['base_services_price_percent'] );
						$product->set_description( 'Producto creado automáticamente para el pago por adelantado de la tienda: '.$currentStoreName);
				
						// $product->set_image_id( $_POST['product-featured-image'] );
						$product->set_virtual( true );
				
						$product->save();

						update_post_meta($product->get_id(), 'product-type', 'advanced-payment');
						update_post_meta($product->get_id(), 'product-store-id', $stage_id);

						WC()->cart->empty_cart();
						WC()->cart->add_to_cart($product->get_id());
						WC()->cart->calculate_totals();

						$email_step_completed = get_field('email_step_completed', 'option');
						$email_admin_new_client = get_field('email_admin_new_client', 'option');

						if(!isset($email_step_completed['image']) or !is_array($email_step_completed['image'])){
							$email_step_completed_image = get_template_directory_uri().'/img/pers-2.png';
						}else{
							$email_step_completed_image = $email_step_completed['image']['url'];
						}

						if(!isset($email_admin_new_client['image']) || !is_array($email_admin_new_client)){
							$email_admin_new_client_image = get_template_directory_uri().'/emails/img/pers-2.png';
						}else{
							$email_admin_new_client_image = $email_admin_new_client['image']['url'];
						}

						//enviar correo al cliente con los datos del nuevo cliente
						if(send_mail('generic', $_POST['field-useremail'], $email_step_completed_image, $email_step_completed['title'], $email_step_completed['description'])){
							//enviar correo al administrador con los datos del nuevo cliente
							$description = str_replace('{{client_email}}', $_POST['field-useremail'], $email_admin_new_client['description']);
							$description = str_replace('{{edit_link}}', site_url('/wp-admin/post.php?post='.$stage_id.'&action=edit'), $description);
							
							send_mail('generic', get_field('emails_to', 'option'), $email_admin_new_client_image, $email_admin_new_client['title'], $description);
						}

						$result = array(
							'error' => false,
						);
					}
				}catch( Exception $e ){
					$result = array(
						'error' => true,
						'message' => $e->getMessage()
					);
				}

				//=============================================================================
				//=============================================================================
				//=============================================================================

			}
		}else{
			$result = array(
				'error' => true,
				'msg' => 'Error creando el usuario.',
 			);
		}

	}elseif($wsa == 'verify_user'){
		$user = get_user_by('email', $_POST['useremail']);
		if($user !== false){
			$result = array(
				'exists' => true,
			);
		}else{
			$result = array(
				'exists' => false,
			);
		}
	}elseif($wsa == 'login'){
		$user = get_user_by('email', $_POST['field-useremail']);
		$exists = false;

		if($user !== false){
			$exists = true;
		}

		if($exists && wp_check_password($_POST['field-userpass'], $user->data->user_pass)){
			if(in_array('subscriber', $user->roles)){

				//hago el login del usuario
				wp_set_current_user( $user->ID, $user->user_login );
				wp_set_auth_cookie( $user->ID );
				do_action( 'wp_login', $user->user_login, $user );

                $result = array(
                    'message' => 'Bienvenido!',
                    'loggedid' => true,
                );
			}else{
				$result = array(
					'message' => 'El usuario no tiene acceso al paso 2!',
					'loggedid' => false,
				);
			}
		}else{
			$result = array(
				'message' => 'E-mail o contraseña incorrecto!',
				'loggedid' => false,
			);
		}
	}elseif($wsa == 'send_design_email'){
		$design = get_post($_POST['designID']);
		$store = get_field('store', $design->ID);
		$client = get_field('user', $store->ID);
		$store_name = get_the_title($store->ID);
		$client_email = $client['user_email'];
		$client_name = $client['user_firstname'];
		$password = '<span style="border-radius: 4px;border: 1px solid rgba(0, 178, 49, 1);background: rgba(255, 248, 186, 1);padding: 3px 10px;font-family: Inter;font-weight: 600;font-size: 18px;line-height: 28px;letter-spacing: -0.22px;text-align: center;">'.get_field('store_password', $design->ID).'</span>';
		
		$email_complete_info = get_field('email_complete_info', 'option');
		
		$title = str_replace('{{client_name}}', $client_name, strip_tags($email_complete_info['title']));
		$html_title = str_replace('{{client_name}}', $client_name, $email_complete_info['title']);
		$title = array(
			'text' => $title,
			'html' => $html_title
		);

		$description = str_replace('{{store_name}}', $store_name, $email_complete_info['description']);
		$description = str_replace('{{password}}', $password, $description);
		$button_url = str_replace('#design_id', '?design_id='.$design->ID, $email_complete_info['button']['url']);

		update_field('status', 'design_sent', $store->ID);

		if(send_mail('design', $client_email, $email_complete_info['image']['url'], $title, $description, $email_complete_info['subtitle'], $button_url, $email_complete_info['button']['title'])){
			$result = array(
				'message' => 'E-mail enviado!',
				'error' => false,
			);
		}else{
			$result = array(
				'message' => 'Error enviando el e-mail!',
				'error' => true,
			);
		}
	}elseif($wsa == 'unselect_design_form'){
		$design = get_post($_POST['design_id']);
		$store = get_field('store', $design->ID);
		$client = get_field('user', $store->ID);
		$store_name = get_field('current_store_name', $store->ID);

		$client_email = $client['user_email'];
		$client_name = $client['user_firstname'];

		$client_comment = 'Comentario del cliente: <br>'.$_POST['field-unselect-design-text'];

		$title = __('Dise&ntilde;o rechazado.', 'xopifier');
		$description = __('Sentimos que hayas rechazado el dise&ntilde;o.', 'xopifier');
		$mainimage = get_template_directory_uri().'/img/design-declined.png';

		update_field('status', 'declined_design', $store->ID);
		update_field('comment', $_POST['field-unselect-design-text'], $store->ID);

		$result = array(
			'message' => '',
			'error' => false,
		);

		if(send_mail('generic', $client_email, $mainimage, $title, $description)){
			$description = 'El cliente '.$clien_name.' con email '.$client_email.' ha rechazado el dise&ntilde;o de la propuesta de la tienda: '.$store_name;
			send_mail('generic', get_field('emails_to', 'option'), $mainimage, $title, $client_comment);
		}
	}

	wp_send_json($result);
}
// creating Ajax call for WordPress  
add_action( 'wp_ajax_nopriv_ws', 'MyAjaxFunctions' );  
add_action( 'wp_ajax_ws', 'MyAjaxFunctions' );
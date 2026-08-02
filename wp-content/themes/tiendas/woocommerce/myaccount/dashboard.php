<?php
/**
 * My Account Dashboard
 *
 * Shows the first intro screen on the account dashboard.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/dashboard.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 4.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

global $current_user;

$allowed_html = array(
	'a' => array(
		'href' => array(),
	),
);
?>

<p>
	<?php
	printf(
		/* translators: 1: user display name 2: logout url */
		wp_kses( __( 'Hola %1$s, aquí tienes el listado de tus tiendas en Xopifier.', 'xopifier' ), $allowed_html ),
		'<strong>' . esc_html( $current_user->display_name ) . '</strong>',
		esc_url( wc_logout_url() )
	);
	?>
</p>

<?php
	
	update_field('approved-design-id', '1', 655);
	
	$stores = get_posts(array('post_type' => 'initial-stage', 'post_status' => 'publish', 'meta_query' => array(
		array(
			'key' => 'user',
			'value'	=> $current_user->ID
		)
	)));

	// pending : Pendiente de revisión
	// complete_info : Diseño enviado al cliente
	// approved_design : Diseño aprobado por el cliente
	// declined_design : Diseño rechazado por el cliente
	// store_in_review : Tienda en revisión
	// store_approved : Tienda aprobada

	$status = array(
		'payment_pending' => __('Pendiente de pago', 'xopifier'),
		'pending' => __('Pendiente de revisión', 'xopifier'),
		'complete_info' => __('Diseño enviado', 'xopifier'),
		'approved_design' => __('Diseño aprobado', 'xopifier'),
		'declined_design' => __('Diseño rechazado', 'xopifier'),
		'store_in_review' => __('Tienda en revisión', 'xopifier'),
		'store_approved' => __('Tienda aprobada', 'xopifier'),
	);
?>

<?php if(is_array($stores) and count($stores) > 0):?>
	<ul class="nav nav-tabs justify-content-start" id="myResumeTab" role="tablist">
		<?php foreach($stores as $k => $store):?>
			<li class="nav-item w-auto pe-1" role="presentation">
				<button class="nav-link py-3 px-4 <?php if($k == 0):?>active<?php endif;?>" id="store-<?php echo $store->ID?>-tab" data-bs-toggle="tab" data-bs-target="#store-<?php echo $store->ID?>-tab-pane" type="button" role="tab" aria-controls="store-<?php echo $store->ID?>-tab-pane" aria-selected="<?php if($k == 0):?>true<?php endif;?>">
					<?php echo get_field('current_store_name', $store->ID);?>

					(<?php echo $status[is_null(get_field('status', $store->ID)) ? 'pending' : get_field('status', $store->ID)];?>)
				</button>
			</li>
		<?php endforeach;?>
	</ul>
	<div class="tab-content" id="myResumeTabContent">
		<?php foreach($stores as $store):?>
			<?php
				$design = get_posts(array('post_type' => 'design', 'post_status' => 'publish', 'meta_query' => array(
					array(
						'key' => 'store',
						'value'	=> $store->ID
					)
				)));
			?>
			<div class="tab-pane fade show active" id="store-<?php echo $store->ID?>-tab-pane" role="tabpanel" aria-labelledby="store-<?php echo $store->ID?>-tab" tabindex="0">
				<?php if(is_array($design) and count($design) > 0):?>
					<?php 
						$store = get_field('store', $design[0]->ID);
						if(get_field('status', $store->ID) == 'declined_design'){
							_e('Has rechazado este diseño. Puedes <a href="'.apply_filters( 'wpml_permalink', site_url('paso-1'), ICL_LANGUAGE_CODE ).'">crear una nueva tienda</a> si así lo deseas.', 'xopifier');
						}else{
							echo store_resume($design[0]->ID);
						}
					?>
				<?php elseif(get_field('status', $store->ID) == 'payment_pending'):?>
					<?php _e('Su solicitud est&aacute; pendiente de pago', 'xopifier');?>
				<?php else:?>
					<?php _e('Su solicitud est&aacute; en revisi&oacute;n', 'xopifier');?>
				<?php endif;?>
			</div>
		<?php endforeach;?>
	</div>
<?php else:?>
	<div class="alert alert-warning d-flex align-items-center justify-content-start gap-2">
		<img src="<?php echo get_template_directory_uri()?>/img/info.svg"/>
		<?php _e('No tiene tiendas registradas a su nombre todavía, para registrar una tienda haga click', 'xopifier')?> <a href="<?php echo apply_filters( 'wpml_permalink', site_url('/paso-1'), ICL_LANGUAGE_CODE )?>"><?php _e('aquí', 'xopifier')?></a>
	</div>
<?php endif;?>

<?php
	/**
	 * My Account dashboard.
	 *
	 * @since 2.6.0
	 */
	do_action( 'woocommerce_account_dashboard' );

	/**
	 * Deprecated woocommerce_before_my_account action.
	 *
	 * @deprecated 2.6.0
	 */
	do_action( 'woocommerce_before_my_account' );

	/**
	 * Deprecated woocommerce_after_my_account action.
	 *
	 * @deprecated 2.6.0
	 */
	do_action( 'woocommerce_after_my_account' );

/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */

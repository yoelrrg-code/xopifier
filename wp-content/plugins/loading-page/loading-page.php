<?php
/**
Plugin Name: Loading Page
Plugin URI: http://wordpress.dwbooster.com/content-tools/loading-page
Description: Loading Page plugin performs a pre-loading of images on your website and displays a loading progress screen with percentage of completion. Once everything is loaded, the screen disappears.
Version: 1.2.8
Author: CodePeople
Author URI: http://wordpress.dwbooster.com/content-tools/loading-page
License: GPLv2
Text Domain: loading-page
 */

require_once 'banner.php';
$codepeople_promote_banner_plugins['codepeople-loading-page'] = array(
	'plugin_name' => 'Loading Page',
	'plugin_url'  => 'https://wordpress.org/support/plugin/loading-page/reviews/#new-post',
);

// CONST.
define( 'LOADING_PAGE_PLUGIN_DIR', dirname( __FILE__ ) );
define( 'LOADING_PAGE_PLUGIN_URL', plugins_url( '', __FILE__ ) );
define( 'LOADING_PAGE_TD', 'loading-page' );

// Feedback system.
require_once 'feedback/cp-feedback.php';
new CP_FEEDBACK( plugin_basename( dirname( __FILE__ ) ), __FILE__, 'https://wordpress.dwbooster.com/contact-us' );

require LOADING_PAGE_PLUGIN_DIR . '/includes/admin_functions.php';

add_filter( 'option_sbp_settings', 'loading_page_troubleshoot' );
if ( ! function_exists( 'loading_page_troubleshoot' ) ) {
	/**
	 * Troubleshoot
	 *
	 * @param array $option checks values entered by third-party plugins.
	 */
	function loading_page_troubleshoot( $option ) {
		if ( ! is_admin() ) {
			// Solves a conflict caused by the "Speed Booster Pack" plugin.
			if ( is_array( $option ) && isset( $option['jquery_to_footer'] ) ) {
				unset( $option['jquery_to_footer'] );
			}
			if ( is_array( $option ) && isset( $option['defer_parsing'] ) ) {
				unset( $option['defer_parsing'] );
			}
		}
		return $option;
	} // End loading_page_troubleshoot.
}

/**
 * Plugin activation
 */
register_activation_hook( __FILE__, 'loading_page_install' );
if ( ! function_exists( 'loading_page_install' ) ) {
	/**
	 * Default options
	 */
	function _loading_page_options() {
		$loading_page_options = get_option( 'loading_page_options', array() );
		if ( ! empty( $loading_page_options ) ) {
			return;
		}

		// Set the default options here.
		$loading_page_options = array(
			'foregroundColor'                           => '#000000',
			'backgroundColor'                           => '#ffffff',
			'enabled_loading_screen'                    => true,
			'close_btn'                                 => true,
			'remove_in_on_load'                         => false,
			'loading_screen'                            => 'logo',
			'lp_loading_screen_display_in'              => 'all',
			'once_per_session'                          => 'always',
			'lp_loading_screen_display_in_pages'        => '',
			'lp_loading_screen_display_post_types'      => '',
			'lp_loading_screen_include_from_urls'       => array(),
			'lp_loading_screen_exclude_from_pages'      => '',
			'lp_loading_screen_exclude_from_post_types' => '',
			'lp_loading_screen_exclude_from_urls'       => array(),
			'displayPercent'                            => true,
			'backgroundImage'                           => '',
			'backgroundImageRepeat'                     => 'repeat',
			'fullscreen'                                => 0,
			'pageEffect'                                => 'none',
			'deepSearch'                                => true,
			'modifyDisplayRule'                         => false,
			// For the logo screen.
			'lp_ls'                                     => array( 'logo' => array( 'image' => plugins_url( 'loading-screens/logo/images/05.svg', __FILE__ ) ) ),
		);

		update_option( 'loading_page_options', $loading_page_options );
	}

	/**
	 * Install
	 *
	 * @param boolean $networkwide indicates if it is a multisite WordPress installation.
	 */
	function loading_page_install( $networkwide ) {
		global $wpdb;

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {
			if ( $networkwide ) {
				$old_blog = $wpdb->blogid;
				// Get all blog ids.
				$blogids = get_sites( array( 'fields' => 'ids' ) );
				foreach ( $blogids as $blog_id ) {
					switch_to_blog( $blog_id );
					_loading_page_options();
				}
				switch_to_blog( $old_blog );
				return;
			}
		}
		_loading_page_options();
	} // End loading_page_install.
} // End plugin activation.

/**
 * Patch to solve the conflict with the previous version that was using animated gifs.
 */
function loading_page_gifs_replacement_patch() {
	$opt = get_option( 'loading_page_options', array() );
	if (
		! empty( $opt ) &&
		! empty( $opt['lp_ls'] ) &&
		! empty( $opt['lp_ls']['logo'] ) &&
		! empty( $opt['lp_ls']['logo']['image'] )
	) {
		$path = $opt['lp_ls']['logo']['image'];
		// Only to solve an issue with the previous version of the plugin when where used .gif.
		$path                          = preg_replace( '/\/loading\-screens\/logo\/gifs\/(\d+)\.gif/i', '/loading-screens/logo/images/$1.svg', $path );
		$opt['lp_ls']['logo']['image'] = $path;
		update_option( 'loading_page_options', $opt );
	}
}

/**
 * Replaces the animated gifts
 *
 * @param object $upgrader_object unused parameter.
 * @param array  $options plugins identifiers.
 */
function loading_page_upgrade_completed_gifs_patch( $upgrader_object, $options ) {
	$our_plugin = plugin_basename( __FILE__ );
	if ( 'update' === $options['action'] && 'plugin' === $options['type'] && isset( $options['plugins'] ) ) {
		foreach ( $options['plugins'] as $plugin ) {
			if ( $plugin === $our_plugin ) {
				loading_page_gifs_replacement_patch();
			}
		}
	}
}
add_action( 'upgrader_process_complete', 'loading_page_upgrade_completed_gifs_patch', 10, 2 );
/**
 * Calls the gifs replacement patch
 *
 * @param string  $plugin plugin name.
 * @param boolean $network_activation plugins activate into a WordPress multisite installation.
 */
function loading_page_activated_gifs_patch( $plugin, $network_activation ) {
	if ( plugin_basename( __FILE__ ) === $plugin ) {
		loading_page_gifs_replacement_patch();
	}
}
add_action( 'activated_plugin', 'loading_page_activated_gifs_patch', 10, 2 );

/**
 *  A new blog has been created in a multisite WordPress
 */
add_action( 'wpmu_new_blog', 'loading_page_new_blog', 10, 6 );
/**
 * New blog creation
 *
 * @param integer $blog_id blog id.
 * @param integer $user_id user id.
 * @param string  $domain blog domain.
 * @param string  $path blog path.
 * @param integer $site_id site id.
 * @param array   $meta metadata.
 */
function loading_page_new_blog( $blog_id, $user_id, $domain, $path, $site_id, $meta ) {
	global $wpdb;
	$file = basename( __FILE__ );
	$dir  = basename( dirname( __FILE__ ) );
	if ( is_plugin_active_for_network( $dir . '/' . $file ) ) {
		$current_blog = $wpdb->blogid;
		switch_to_blog( $blog_id );
		_loading_page_options();
		switch_to_blog( $current_blog );
	}
} // End loading_page_new_blog.

// Redirecting the user to the settings page of the plugin.
add_action( 'activated_plugin', 'loading_page_redirect_to_settings', 10, 2 );
if ( ! function_exists( 'loading_page_redirect_to_settings' ) ) {
	/**
	 * Redirects the user to the settings page
	 *
	 * @param string  $plugin plugin name.
	 * @param boolean $network_activation multisite activation.
	 */
	function loading_page_redirect_to_settings( $plugin, $network_activation ) {
		if (
			empty( $_REQUEST['_ajax_nonce'] ) &&
			plugin_basename( __FILE__ ) === $plugin &&
			( ! isset( $_POST['action'] ) || 'activate-selected' !== $_POST['action'] ) && // phpcs:ignore WordPress.Security.NonceVerification
			( ! isset( $_POST['action2'] ) || 'activate-selected' !== $_POST['action2'] ) // phpcs:ignore WordPress.Security.NonceVerification
		) {
			wp_safe_redirect( admin_url( 'options-general.php?page=loading-page.php' ) );
			remove_all_actions( 'shutdown' );
			exit;
		}
	}
}

if ( ! function_exists( 'loading_page_get_settings' ) ) {
	function loading_page_get_settings() {
		global $wp, $_loading_page_settings_global_var;
		if ( empty( $_loading_page_settings_global_var ) ) {
			$current_url = home_url( add_query_arg( array(), $wp->request ) );
			$_loading_page_settings_global_var = apply_filters( 'loading_page_settings', get_option( 'loading_page_options', [] ), $current_url );
		}
		return $_loading_page_settings_global_var;
	} // End loading_page_get_settings
}

/*
*   Plugin initializing
*/
add_action( 'init', 'loading_page_init' );
if ( ! function_exists( 'loading_page_init' ) ) {
	/**
	 * Enqueuen script if loading page is enabled
	 */
	function loading_page_init() {
		if ( ! is_admin() && empty( $_REQUEST['elementor-preview'] ) ) {

			$op = loading_page_get_settings();

			if ( ! empty( $op ) && is_array( $op ) && ! empty( $op['enabled_loading_screen'] ) ) {
				if (
					! defined( 'DOING_AJAX' ) &&
					! empty( $op['once_per_session'] ) &&
					'always' != $op['once_per_session'] &&
					session_id() == '' &&
					! headers_sent()
				) {
					@session_start();
				}

				add_action( 'wp_enqueue_scripts', 'loading_page_enqueue_scripts', 1 );
			}
		}
	} // End loading_page_init.
}

/*
*   Admin initionalizing
*/
add_action( 'admin_init', 'loading_page_admin_init' );
if ( ! function_exists( 'loading_page_admin_init' ) ) {
	/**
	 * Initialize the plugin admin
	 */
	function loading_page_admin_init() {
		// Load the associated text domain.
		load_plugin_textdomain( 'loading-page', false, LOADING_PAGE_PLUGIN_DIR . '/languages/' );

		// Set plugin links.
		$plugin = plugin_basename( __FILE__ );
		add_filter( 'plugin_action_links_' . $plugin, 'loading_page_links' );

		// Load resources.
		add_action( 'admin_enqueue_scripts', 'loading_page_admin_resources' );
	} // End loading_page_admin_init.
}

if ( ! function_exists( 'loading_page_links' ) ) {
	/**
	 * Set plugins section links
	 *
	 * @param array $links links array.
	 */
	function loading_page_links( $links ) {
		// Custom link.
		$custom_link = '<a href="http://wordpress.dwbooster.com/contact-us" target="_blank">' . __( 'Request custom changes', 'loading-page' ) . '</a>';
		array_unshift( $links, $custom_link );

		// Settings link.
		$settings_link = '<a href="options-general.php?page=loading-page.php">' . __( 'Settings', 'loading-page' ) . '</a>';
		array_unshift( $links, $settings_link );

		return $links;
	} // End loading_page_links.
}

add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'loading_page_customization_link' );
if ( ! function_exists( 'loading_page_customization_link' ) ) {
	/**
	 * Redirects the user to the WordPress forum
	 *
	 * @param array $links links array.
	 */
	function loading_page_customization_link( $links ) {
		array_unshift( $links, '<a href="https://wordpress.org/support/plugin/loading-page/#new-post" target="_blank">' . __( 'Help', 'loading-page' ) . '</a>' );
		return $links;
	}
}

// Set the settings menu option.
add_action( 'admin_menu', 'loading_page_settings_menu' );
if ( ! function_exists( 'loading_page_settings_menu' ) ) {
	/**
	 * WordPress menu option
	 */
	function loading_page_settings_menu() {
		/**
		 *  Add to admin_menu
		 *  - The capability has been modified, from "edit_posts" to "manage_options"
		 *  - Now only the administrators can now edit the "Loading Page" settings.
		 */
		add_options_page( 'Loading Page', 'Loading Page', 'manage_options', 'loading-page.php', 'loading_page_settings_page' );
	} // End loading_page_settings_menu.
}

if ( ! function_exists( 'loading_page_add_code_block' ) ) {
	/**
	 * Embed the code block on page
	 */
	function loading_page_add_code_block() {
		$codeblock = '';
		$op        = loading_page_get_settings();
		if ( ! empty( $op ) && ! empty( $op['codeBlock'] ) ) {
			$codeblock = '<div id="loading_page_codeBlock">' . $op['codeBlock'] . '</div>';
		}
		return $codeblock;
	}
}

if ( ! function_exists( 'loading_page_admin_resources' ) ) {
	/**
	 * Enqueue admin resources
	 *
	 * @param string $hook hook name.
	 */
	function loading_page_admin_resources( $hook ) {
		if ( strpos( $hook, 'loading-page' ) !== false ) {
			wp_enqueue_media();
			wp_enqueue_style( 'farbtastic' );
			wp_enqueue_script( 'farbtastic' );
			wp_enqueue_style( 'thickbox' );
			wp_enqueue_script( 'thickbox' );

			wp_enqueue_script( 'lp-admin-script', LOADING_PAGE_PLUGIN_URL . '/js/loading-page-admin.js', array( 'jquery', 'thickbox', 'farbtastic' ), 'free-1.2.8', true );
		}
	} // End loading_page_admin_resources.
}

if ( ! function_exists( 'loading_page_coming_soon_active' ) ) {
	function loading_page_coming_soon_active() {
		// For Elementor coming soon and Maintenance mode.
		if (
			defined( 'ELEMENTOR_VERSION' ) &&
			false !== ( $elementor_mode = get_option( 'elementor_maintenance_mode_mode' ) ) &&
			in_array( $elementor_mode, ['coming_soon', 'maintenance'] )
		) {
			$elementor_exclude  = get_option( 'elementor_maintenance_mode_exclude_mode' );
			$current_user = wp_get_current_user();

			if ( $current_user->ID == 0 ) return true;

			if ( 'custom' == $elementor_exclude ) {
				$elementor_exclude_roles = get_option( 'elementor_maintenance_mode_exclude_roles' );

				if (
					! empty( $elementor_exclude_roles ) &&
					! count( array_intersect( $elementor_exclude_roles, $current_user->roles ) )
				) {
					return true;
				}
			}
		}
		return false;
	} // End loading_page_coming_soon_active.
}

if ( ! function_exists( 'loading_page_loading_screen' ) ) {
	/**
	 * Deciding to include the loading screen
	 */
	function loading_page_loading_screen() {
		global $post;

		$is_bot 		= function () {
			if ( ! empty( $_SERVER['HTTP_USER_AGENT'] ) ) {
				$system = sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) );
				if ( ! empty( $system ) ) {
					return preg_match( '/(Googlebot)|(Baiduspider)|(ia_archiver)|(R6_FeedFetcher)|(NetcraftSurveyAgent)|(Sogou web spider)|(bingbot)|(Yahoo\! Slurp)|(facebookexternalhit)|(PrintfulBot)|(msnbot)|(Twitterbot)|(UnwindFetchor)|(urlresolver)/i', $system );
				}
			}

			return false;
		};

		$op             = loading_page_get_settings();
		$loading_screen = 0;

		if ( $is_bot() ) {
			return $loading_screen;
		}

		if (
			! empty( $op['enabled_loading_screen'] )
		) {
			global $wp;
			$current_url = home_url( add_query_arg( array(), $wp->request ) );
			$_permalink  = md5( $current_url );

			if (
				empty( $op['lp_loading_screen_devices'] ) ||
				'both' == $op['lp_loading_screen_devices'] ||
				( 'desktop' == $op['lp_loading_screen_devices'] && ! wp_is_mobile() ) ||
				( 'mobile' == $op['lp_loading_screen_devices'] && wp_is_mobile() )
			) {
				if ( ! empty( $op['once_per_session'] ) && 'always' != $op['once_per_session'] ) {
					if(
						! empty( $_SESSION['loading_page_once_per_session'] ) &&
						(
							'site' == $op['once_per_session'] ||
							(
								'page' == $op['once_per_session'] &&
								is_array( $_SESSION['loading_page_once_per_session'] ) &&
								! empty( $_SESSION['loading_page_once_per_session'][ $_permalink ] )
							)
						)
					) {
						return $loading_screen;
					}

					if ( empty( $_SESSION['loading_page_once_per_session'] ) || ! is_array( $_SESSION['loading_page_once_per_session'] ) ) {
						$_SESSION['loading_page_once_per_session'] = array();
					}
					$_SESSION['loading_page_once_per_session'][ $_permalink ] = 1;
				}

				$pages = ( ! empty( $op['lp_loading_screen_display_in_pages'] ) ) ? $op['lp_loading_screen_display_in_pages'] : '';
				$pages = str_replace( ' ', '', $pages );
				$pages = explode( ',', $pages );

				$post_types = ( ! empty( $op['lp_loading_screen_display_post_types'] ) ) ? $op['lp_loading_screen_display_post_types'] : '';
				$post_types = str_replace( ' ', '', $post_types );
				$post_types = explode( ',', $post_types );

				$exclude_pages = ( ! empty( $op['lp_loading_screen_exclude_from_pages'] ) ) ? $op['lp_loading_screen_exclude_from_pages'] : '';
				$exclude_pages = str_replace( ' ', '', $exclude_pages );
				$exclude_pages = explode( ',', $exclude_pages );

				$exclude_post_types = ( ! empty( $op['lp_loading_screen_exclude_from_post_types'] ) ) ? $op['lp_loading_screen_exclude_from_post_types'] : '';
				$exclude_post_types = str_replace( ' ', '', $exclude_post_types );
				$exclude_post_types = explode( ',', $exclude_post_types );

				$exclude_pages_urls = ( ! empty( $op['lp_loading_screen_exclude_from_urls'] ) ) ? $op['lp_loading_screen_exclude_from_urls'] : array();

				$include_pages_urls = ( ! empty( $op['lp_loading_screen_include_from_urls'] ) ) ? $op['lp_loading_screen_include_from_urls'] : array();

				$exclude_elementor_maintenance = (
					! empty( $op['lp_loading_screen_exclude_from_elementor_maintenance'] ) &&
					loading_page_coming_soon_active()
				) ? true : false;

				// Initialize current URL
				$protocol    = ( ( ! empty( $_SERVER['HTTPS'] ) && 'off' !== $_SERVER['HTTPS'] ) || ( isset( $_SERVER['SERVER_PORT'] ) && 443 === $_SERVER['SERVER_PORT'] ) ) ? 'https://' : 'http://';
				$current_url = esc_url_raw( $protocol . ( isset( $_SERVER['HTTP_HOST'] ) ? wp_unslash( $_SERVER['HTTP_HOST'] ) : '' ) . ( isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '' ) );

				$match_url = function ( $urls_array ) use ( $current_url ) {
					foreach ( $urls_array as $url ) {
						$url = preg_quote( $url, '/' );
						$url = str_replace( '\*', '.*', $url );
						if ( preg_match( '/^' . $url . '$/i', $current_url ) ) {
							return true;
						}
					}

					return false;
				};

				if (
				! $exclude_elementor_maintenance &&
				( empty( $op['lp_loading_screen_display_in'] ) ||
					('all' === $op['lp_loading_screen_display_in'] && ( empty( $include_pages_urls ) || $match_url( $include_pages_urls ) ) ) ||
					( 'home' === $op['lp_loading_screen_display_in'] && ( is_home() || is_front_page() ) ) ||
					( 'pages' === $op['lp_loading_screen_display_in'] && is_singular() && isset( $post ) && in_array( $post->ID, $pages ) ) ||
					( 'post_type' === $op['lp_loading_screen_display_in'] && is_singular() && isset( $post ) && in_array( $post->post_type, $post_types, true ) )
				) &&
				( empty( $post ) ||
					empty( $post->ID ) ||
					(
						( empty( $exclude_pages ) ||
							! in_array( $post->ID, $exclude_pages )
						)
						&&
						( empty( $exclude_post_types ) ||
							! in_array( $post->post_type, $exclude_post_types, true )
						)
					)
				)
				) {
					if ( ! empty( $exclude_pages_urls ) && $match_url( $exclude_pages_urls ) ) return;

					add_filter( 'body_class', function( $classes ){ $classes[] = 'lp_loading_screen_body'; return $classes; } );
					$loading_screen = 1;
					add_action( 'wp_head', 'loading_page_replace_the_header', 99 );
					add_filter( 'autoptimize_filter_js_dontmove', 'loading_page_autoptimize_exclude', 10, 1 );
				}
			}
		}
		return $loading_screen;
	}
}

if ( ! function_exists( 'loading_page_autoptimize_exclude' ) ) {
	/**
	 * Excluding the plugin resources from Autoptimize
	 *
	 * @param array $to_exclude to exclude URLs.
	 */
	function loading_page_autoptimize_exclude( $to_exclude ) {
		$to_exclude[] = 'loading-page.min.js';
		$to_exclude[] = '/loading-screens/';
		return $to_exclude;
	}
}

if ( ! function_exists( 'loading_page_replace_the_header' ) ) {
	/**
	 * Including styles in the page header section
	 */
	function loading_page_replace_the_header() {
		$opt = loading_page_get_settings();
		if ( empty( $opt ) || empty( $opt['modifyDisplayRule'] ) ) {
			echo '<style id="loading-page-inline-style">body{visibility:hidden;}</style><noscript><style>body{visibility:visible;}</style></noscript>';
		} else {
			echo '<style id="loading-page-inline-style">body{display:none;}</style><noscript><style>body{display:block;}</style></noscript>';
		}

		if (
			! empty( $opt ) &&
			! empty( $opt['lp_ls'] ) &&
			! empty( $opt['lp_ls']['logo'] ) &&
			! empty( $opt['lp_ls']['logo']['image'] )
		) {
			$path = $opt['lp_ls']['logo']['image'];
			echo '<link rel="preload" href="' . esc_attr( $path ) . '" as="image" type="image/svg+xml">';
		}
	} // End loading_page_replace_the_header.
}

if ( ! function_exists( 'loading_page_hex2rgb' ) ) {
	/**
	 * Transform colors code
	 *
	 * @param array/string $colour color code.
	 */
	function loading_page_hex2rgb( $colour, $percentage = 80 ) {
		if ( '#' === $colour[0] ) {
			$colour = substr( $colour, 1 );
		}
		if ( 6 === strlen( $colour ) ) {
			list($r, $g, $b) = array( $colour[0] . $colour[1], $colour[2] . $colour[3], $colour[4] . $colour[5] );
		} elseif ( 3 === strlen( $colour ) ) {
			list($r, $g, $b) = array( $colour[0] . $colour[0], $colour[1] . $colour[1], $colour[2] . $colour[2] );
		} else {
			return $colour;
		}
		$r = hexdec( $r );
		$g = hexdec( $g );
		$b = hexdec( $b );

		return 'rgba(' . $r . ',' . $g . ',' . $b . ',' . ( $percentage / 100 ) . ')';
	}
}

if ( ! function_exists( 'loading_page_enqueue_scripts' ) ) {
	/**
	 * Enqueue public scripts
	 */
	function loading_page_enqueue_scripts() {
		global $post;

		$op             = loading_page_get_settings();
		$loading_screen = loading_page_loading_screen();
		if ( $loading_screen && ! empty( $op['from_trigger'] ) ) {
			wp_enqueue_script( 'codepeople-loading-page-link-script', LOADING_PAGE_PLUGIN_URL . '/js/links.min.js', array( 'jquery' ), 'free-1.2.8', false );
		}

		if ( $loading_screen ) {
			$loading_page_settings = array(
				'loadingScreen'     => $loading_screen,
				'closeBtn'          => ( ! isset( $op['close_btn'] ) || $op['close_btn'] ) ? true : false,
				'removeInOnLoad'    => ( ! empty( $op['remove_in_on_load'] ) ) ? $op['remove_in_on_load'] : false,
				'codeblock'         => loading_page_add_code_block(),
				'backgroundColor'   => ( ! isset( $op['transparency'] ) || $op['transparency'] )
					? loading_page_hex2rgb(
						$op['backgroundColor'],
						(
							! isset( $op['transparencyPercentage'] ) ||
							! is_numeric( $op['transparencyPercentage'] )
							? 80
							: max( min( intval( $op['transparencyPercentage'] ), 100 ), 0 )
						)
					)
					: $op['backgroundColor'],
				'foregroundColor'   => $op['foregroundColor'],
				'backgroundImage'   => $op['backgroundImage'],
				'additionalSeconds' => ( ! empty( $op['additionalSeconds'] ) ) ? $op['additionalSeconds'] : 0,
				'pageEffect'        => $op['pageEffect'],
				'backgroundRepeat'  => $op['backgroundImageRepeat'],
				'fullscreen'        => ( ( ! empty( $op['fullscreen'] ) ) ? 1 : 0 ),
				'graphic'           => $op['loading_screen'],
				'text'              => ( ( ! empty( $op['displayPercent'] ) ) ? $op['displayPercent'] : 0 ),
				'lp_ls'             => ( ( ! empty( $op['lp_ls'] ) ) ? $op['lp_ls'] : 0 ),
				'screen_size'       => ( ( ! empty( $op['screen_size'] ) ) ? $op['screen_size'] : 'all' ),
				'screen_width'      => ( ( ! empty( $op['screen_width'] ) ) ? $op['screen_width'] : 0 ),
				'deepSearch'        => ( ( empty( $op['deepSearch'] ) ) ? 0 : 1 ),
				'modifyDisplayRule' => ( ( empty( $op['modifyDisplayRule'] ) ) ? 0 : 1 ),
				'triggerLinkScreenNeverClose' => empty( $op['triggerLinkScreenNeverClose'] ) ? 0 : 1,
				'triggerLinkScreenCloseAfter' => ! empty( $op['triggerLinkScreenCloseAfter'] ) ? intval( $op['triggerLinkScreenCloseAfter'] ) : 4,
			);

			$required = array( 'jquery' );
			wp_enqueue_script( 'jquery' );
			wp_enqueue_style( 'codepeople-loading-page-style', LOADING_PAGE_PLUGIN_URL . '/css/loading-page.css', array(), 'free-1.2.8', false );
			wp_enqueue_style( 'codepeople-loading-page-style-effect', LOADING_PAGE_PLUGIN_URL . '/css/loading-page' . ( ( 'none' !== $op['pageEffect'] ) ? '-' . $op['pageEffect'] : '' ) . '.css', array(), 'free-1.2.8', false );

			$s = loading_page_get_screen( $op['loading_screen'] );
			if ( $s ) {
				if ( ! empty( $s['style'] ) ) {
					wp_enqueue_style( 'codepeople-loading-page-style-' . $s['id'], $s['style'], array(), 'free-1.2.8', false );
				}

				if ( ! empty( $s['script'] ) ) {
					wp_enqueue_script( 'codepeople-loading-page-script-' . $s['id'], $s['script'], array( 'jquery' ), 'free-1.2.8', false );
					$required[] = 'codepeople-loading-page-script-' . $s['id'];
				}
			}
			wp_enqueue_script( 'codepeople-loading-page-script', LOADING_PAGE_PLUGIN_URL . '/js/loading-page.min.js', $required, 'free-1.2.8', false );
			if ( function_exists( 'wp_add_inline_script' ) ) {
				wp_add_inline_script( 'codepeople-loading-page-script', 'loading_page_settings=' . wp_json_encode( $loading_page_settings ) . ';', 'before' );
			} else {
				wp_localize_script( 'codepeople-loading-page-script', 'loading_page_settings', $loading_page_settings );
			}
		}
	} // End loading_page_enqueue_scripts.
}

if ( ! function_exists( 'loading_page_sanitize_array' ) ) {
	/**
	 * Sanitize mutilevel array
	 *
	 * @param array/string $array values to sanitize.
	 */
	function loading_page_sanitize_array( $array ) {
		if ( is_array( $array ) ) {
			foreach ( $array as $key => $value ) {
				if ( is_array( $value ) ) {
					$array[ $key ] = loading_page_sanitize_array( $value );
				} else {
					$array[ $key ] = sanitize_text_field( wp_unslash( $value ) );
				}
			}
			return $array;
		} else {
			return sanitize_text_field( wp_unslash( $array ) );
		}
	} // End loading_page_sanitize_array.
}

if ( ! function_exists( 'loading_page_settings_page' ) ) {
	/**
	 * Plugin settings page
	 */
	function loading_page_settings_page() {
		if ( isset( $_POST['loading_page_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['loading_page_nonce'] ) ), __FILE__ ) ) {

			$allowed_tags = wp_kses_allowed_html( 'post' );

			$allowed_tags['source'] = array(
				'src' => true,
				'type' => true
			);

			if ( current_user_can( 'manage_options' ) ) {
				$allowed_tags['script'] = true;
				$allowed_tags['style'] = true;
				$allowed_tags['link'] = true;
			}

			$additional_seconds = isset( $_POST['lp_additionalSeconds'] ) && is_numeric( $_POST['lp_additionalSeconds'] ) ? intval( $_POST['lp_additionalSeconds'] ) : 0;
			$code_block         = isset( $_POST['lp_codeBlock'] ) ? wp_kses( wp_unslash( $_POST['lp_codeBlock'] ), $allowed_tags ) : '';

			$lp_loading_screen_exclude_from_urls = isset( $_POST['lp_loading_screen_exclude_from_urls'] ) ? sanitize_textarea_field( wp_unslash( $_POST['lp_loading_screen_exclude_from_urls'] ) ) : '';
			$lp_loading_screen_exclude_from_urls = explode( "\n", $lp_loading_screen_exclude_from_urls );
			foreach ( $lp_loading_screen_exclude_from_urls as $key => $url ) {
				$url = trim( $url );
				if ( '' === $url ) {
					unset( $lp_loading_screen_exclude_from_urls[ $key ] );
				} else {
					$lp_loading_screen_exclude_from_urls[ $key ] = $url;
				}
			}

			$lp_loading_screen_include_from_urls = isset( $_POST['lp_loading_screen_include_from_urls'] ) ? sanitize_textarea_field( wp_unslash( $_POST['lp_loading_screen_include_from_urls'] ) ) : '';
			$lp_loading_screen_include_from_urls = explode( "\n", $lp_loading_screen_include_from_urls );
			foreach ( $lp_loading_screen_include_from_urls as $key => $url ) {
				$url = trim( $url );
				if ( '' === $url ) {
					unset( $lp_loading_screen_include_from_urls[ $key ] );
				} else {
					$lp_loading_screen_include_from_urls[ $key ] = $url;
				}
			}

			$lp_screen_width = '';
			if ( ! empty( $_POST['lp_screen_width'] ) ) {
				$lp_screen_width = preg_replace( '/[^\\d\\.]/', '', sanitize_text_field( wp_unslash( $_POST['lp_screen_width'] ) ) );
			}

			// Set the default options here.
			$loading_page_options = array(
				'foregroundColor'                      => ( ! empty( $_POST['lp_foregroundColor'] ) ) ? sanitize_text_field( wp_unslash( $_POST['lp_foregroundColor'] ) ) : '#FFFFFF',
				'backgroundColor'                      => ( ! empty( $_POST['lp_backgroundColor'] ) ) ? sanitize_text_field( wp_unslash( $_POST['lp_backgroundColor'] ) ) : '#000000',
				'transparency'                         => ( ! empty( $_POST['lp_backgroundTransparency'] ) ) ? true : false,
				'transparencyPercentage'               => (
					! isset( $_POST['lp_backgroundTransparencyPercentage'] ) ||
					! is_numeric( $_POST['lp_backgroundTransparencyPercentage'] )
					? 80
					: max( min( intval( $_POST['lp_backgroundTransparencyPercentage'] ), 100 ), 0 )
				),
				'backgroundImage'                      => isset( $_POST['lp_backgroundImage'] ) ? esc_url_raw( wp_unslash( $_POST['lp_backgroundImage'] ) ) : '',
				'backgroundImageRepeat'                => ( isset( $_POST['lp_backgroundRepeat'] ) && in_array( $_POST['lp_backgroundRepeat'], array( 'repeat', 'no-repeat' ), true ) ) ? sanitize_text_field( wp_unslash( $_POST['lp_backgroundRepeat'] ) ) : 'repeat',
				'additionalSeconds'                    => $additional_seconds,
				'codeBlock'                            => wp_unslash( $code_block ),
				'fullscreen'                           => ( isset( $_POST['lp_fullscreen'] ) ) ? 1 : 0,
				'enabled_loading_screen'               => ( isset( $_POST['lp_enabled_loading_screen'] ) ) ? true : false,
				'from_trigger'                         => ( isset( $_POST['lp_from_trigger'] ) ) ? true : false,
				'close_btn'                            => ( isset( $_POST['lp_close_btn'] ) ) ? true : false,
				'remove_in_on_load'                    => ( isset( $_POST['lp_remove_in_on_load'] ) ) ? true : false,
				'screen_size'                          => ( isset( $_POST['lp_screen_size'] ) && in_array( $_POST['lp_screen_size'], array( 'all', 'greater', 'lesser' ), true ) ) ? sanitize_text_field( wp_unslash( $_POST['lp_screen_size'] ) ) : 'all',
				'screen_width'                         => is_numeric( $lp_screen_width ) ? $lp_screen_width : '',
				'lp_loading_screen_display_in'         => ( isset( $_POST['lp_loading_screen_display_in'] ) ) ? sanitize_text_field( wp_unslash( $_POST['lp_loading_screen_display_in'] ) ) : 'all',
				'once_per_session'                     => ( ! isset( $_POST['once_per_session'] ) ||
					! in_array( $_POST['once_per_session'], array( 'always', 'site', 'page' ), true )
				) ? 'always' : sanitize_text_field( wp_unslash( $_POST['once_per_session'] ) ),
				'lp_loading_screen_display_in_pages'   => isset( $_POST['lp_loading_screen_display_in_pages'] ) ? sanitize_text_field( wp_unslash( $_POST['lp_loading_screen_display_in_pages'] ) ) : '',
				'lp_loading_screen_display_post_types' => isset( $_POST['lp_loading_screen_display_post_types'] ) ? sanitize_text_field( wp_unslash( $_POST['lp_loading_screen_display_post_types'] ) ) : '',
				'lp_loading_screen_devices' => isset( $_POST['lp_loading_screen_devices'] ) && in_array( $_POST['lp_loading_screen_devices'], array( 'desktop', 'mobile', 'both' ) ) ? sanitize_text_field( wp_unslash( $_POST['lp_loading_screen_devices'] ) ) : 'both',
				'lp_loading_screen_exclude_from_pages' => isset( $_POST['lp_loading_screen_exclude_from_pages'] ) ? sanitize_text_field( wp_unslash( $_POST['lp_loading_screen_exclude_from_pages'] ) ) : '',
				'lp_loading_screen_exclude_from_post_types' => isset( $_POST['lp_loading_screen_exclude_from_post_types'] ) ? sanitize_text_field( wp_unslash( $_POST['lp_loading_screen_exclude_from_post_types'] ) ) : '',
				'lp_loading_screen_include_from_urls'  => $lp_loading_screen_include_from_urls,
				'lp_loading_screen_exclude_from_urls'  => $lp_loading_screen_exclude_from_urls,
				'lp_loading_screen_exclude_from_elementor_maintenance' => ( isset( $_POST['lp_loading_screen_exclude_from_elementor_maintenance'] ) ) ? true : false,
				'deepSearch'                           => ( isset( $_POST['lp_deactivateDeepSearch'] ) ) ? false : true,
				'modifyDisplayRule'                    => ( isset( $_POST['lp_modifyDisplayRule'] ) ) ? true : false,
				'loading_screen'                       => isset( $_POST['lp_loading_screen'] ) ? sanitize_text_field( wp_unslash( $_POST['lp_loading_screen'] ) ) : '',
				'displayPercent'                       => ( isset( $_POST['lp_displayPercent'] ) ) ? true : false,
				'pageEffect'                           => isset( $_POST['lp_pageEffect'] ) ? sanitize_text_field( wp_unslash( $_POST['lp_pageEffect'] ) ) : '',
				'triggerLinkScreenNeverClose'          => isset( $_POST['lp_triggerLinkScreenNeverClose'] ) ? true : false,
				'triggerLinkScreenCloseAfter'          => isset( $_POST['lp_triggerLinkScreenCloseAfter'] ) && is_numeric( $_POST['lp_triggerLinkScreenCloseAfter'] ) ? intval( $_POST['lp_triggerLinkScreenCloseAfter'] ) : '',
			);

			if ( isset( $_POST['lp_ls'] ) ) {
				$loading_page_options['lp_ls'] = loading_page_sanitize_array( $_POST['lp_ls'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
			}

			update_option( 'loading_page_video_tutorial', ( ! empty( $_POST['loading_page_video_tutorial'] ) && 'collapsed' === $_POST['loading_page_video_tutorial'] ) ? 'collapsed' : 'expanded' );

			if ( update_option( 'loading_page_options', $loading_page_options ) ) {
				print '<div class="updated">' . esc_html__( 'The Loading Page has been stored successfully', 'loading-page' ) . '</div>';
			} else {
				print '<div class="error">' . esc_html__( 'The Loading Page settings could not be stored', 'loading-page' ) . '</div>';
			}
		}

		$loading_page_options = get_option( 'loading_page_options', [] );

		$loading_page_video_tutorial            = get_option( 'loading_page_video_tutorial', 'expanded' );
		$loading_page_video_tutorial_open_close = ( 'expanded' === $loading_page_video_tutorial ) ? 'X' : '+';
		$loading_page_video_tutorial_status     = ( 'collapsed' === $loading_page_video_tutorial ) ? 'lp-video-collapsed' : '';

		?>
		<style>
			.lp-video-container {
				overflow: hidden;
				position: relative;
				width: 100%;
			}

			.lp-video-container::after {
				padding-top: 56.25%;
				display: block;
				content: '';
			}

			.lp-video-container iframe {
				position: absolute;
				top: 0;
				left: 0;
				width: 100%;
				height: 100%;
			}

			.lp-video-collapsed {
				display: none;
			}
			.lp-upgrade-message{
				border:3px solid #e5c012;
				margin-top:10px;
				margin-bottom:10px;
				padding:10px;
				display:block;
				width:100%;
				box-sizing:border-box;
				font-weight:500;
			}
		</style>
		<div class="wrap">
			<form method="post">
				<input type="hidden" name="loading_page_nonce" value="<?php print( esc_attr( wp_create_nonce( __FILE__ ) ) ); ?>" />
				<h2><?php esc_html_e( 'Loading Page Settings', 'loading-page' ); ?></h2>
				<div class="postbox">
					<h3 class='hndle' style="padding:5px;"><span><?php esc_html_e( 'Video Tutorial', 'loading-page' ); ?></span><a href="javascript:void(0);" onclick="loading_page_collapse_expand_video_tutorial(this);" style="float:right;font-size:1.3em;text-decoration:none;"><?php print esc_html( $loading_page_video_tutorial_open_close ); ?></a></h3>
					<div class="inside lp-video-tutorial <?php echo esc_attr( $loading_page_video_tutorial_status ); ?>" style="position:relative;">
						<input type="hidden" name="loading_page_video_tutorial" value="<?php echo esc_attr( $loading_page_video_tutorial ); ?>" />
						<div class="lp-video-container">
							<iframe title="<?php esc_attr_e( 'Video tutorial', 'loading-page' ); ?>" src="https://www.youtube.com/embed/5x_LtjoCFUY" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
						</div>
					</div>
				</div>
				<div class="postbox">
					<h3 class='hndle' style="padding:5px;"><span><?php esc_html_e( 'Loading Screen', 'loading-page' ); ?></span></h3>
					<div class="inside">
						<p style="border:1px solid #E6DB55;margin-bottom:10px;padding:5px;background-color: #FFFFE0;"><?php esc_html_e( 'If you want test the premium version of Loading Page go to the following links:', 'loading-page' ); ?><br /> <a href="https://demos.dwbooster.com/loading-page/wp-login.php" target="_blank"><?php esc_html_e( 'Administration area: Click to access the administration area demo', 'loading-page' ); ?></a><br />
							<a href="https://demos.dwbooster.com/loading-page/" target="_blank"><?php esc_html_e( 'Public page: Click to access the Loading Page', 'loading-page' ); ?></a><br />
							<a href="https://wordpress.org/support/plugin/loading-page/#new-post" target="_blank"><?php esc_html_e( 'If you need additional help', 'loading-page' ); ?></a>
						</p>
						<p>
						<?php
							esc_html_e(
								'Displays a loading screen until the webpage is ready, the screen shows the loading percent.',
								'loading-page'
							);
						?>
							</p>
						<table class="form-table">
							<tr>
								<th><?php esc_html_e( 'Enable loading screen', 'loading-page' ); ?></th>
								<td><input aria-label="<?php esc_attr_e( 'Enable loading screen', 'loading-page' ); ?>" type="checkbox" name="lp_enabled_loading_screen" <?php echo ( ( ! empty( $loading_page_options['enabled_loading_screen'] ) ) ? 'CHECKED' : '' ); ?> /></td>
							</tr>
							<tr>
								<th style="border-top:2px dashed green;border-left:2px dashed green;border-bottom:2px dashed green;padding-left:10px;"><?php esc_html_e( 'Show loading screen when clicking on link', 'loading-page' ); ?></th>
								<td style="border-top:2px dashed green;border-right:2px dashed green;border-bottom:2px dashed green;padding-left:10px;"><input aria-label="<?php esc_attr_e( 'Show loading screen when clicking on link', 'loading-page' ); ?>" type="checkbox" name="lp_from_trigger" <?php print ! empty( $loading_page_options['from_trigger'] ) ? 'CHECKED' : ''; ?> /> <i style="color:green;"><?php esc_html_e( 'Display the loading screen as soon as the link on the current page is clicked, and not only when the next page is loaded.', 'loading-page' ); ?></i><br><i style="color:green;"><?php esc_html_e( 'Apply the class name "no-loading-screen" to the links you want to exclude from this behavior.', 'loading-page' ); ?></i></td>
							</tr>
							<tr>
								<th><?php esc_html_e( 'Display a close screen button', 'loading-page' ); ?></th>
								<td><input aria-label="<?php esc_attr_e( 'Display a close screen button', 'loading-page' ); ?>" type="checkbox" name="lp_close_btn" <?php echo ( ( ! isset( $loading_page_options['close_btn'] ) || $loading_page_options['close_btn'] ) ? 'CHECKED' : '' ); ?> /></td>
							</tr>
							<tr>
								<th><?php esc_html_e( 'Hide the loading screen in the window onload event', 'loading-page' ); ?></th>
								<td><input aria-label="<?php esc_attr_e( 'Hide the loading screen in the onload event', 'loading-page' ); ?>" type="checkbox" name="lp_remove_in_on_load" <?php echo ( ( ! empty( $loading_page_options['remove_in_on_load'] ) ) ? 'CHECKED' : '' ); ?> /> <i><?php esc_html_e( 'Hides the loading screen in the onload event of window - or - as soon as possible', 'loading-page' ); ?></i></td>
							</tr>
							<tr>
								<th><?php esc_html_e( 'Display the loading screen on', 'loading-page' ); ?></th>
								<td>
									<select aria-label="<?php esc_attr_e( 'Screen size', 'loading-page' ); ?>" name="lp_screen_size" style="float:left;">
										<option value="all"
										<?php
										if (
																isset( $loading_page_options['screen_size'] ) &&
																'all' === $loading_page_options['screen_size']
															) {
											print 'SELECTED';
										}
										?>
															><?php esc_html_e( 'All Screens', 'loading-page' ); ?></option>
										<option value="greater"
										<?php
										if (
																	isset( $loading_page_options['screen_size'] ) &&
																	'greater' === $loading_page_options['screen_size']
																) {
											print 'SELECTED';
										}
										?>
																><?php esc_html_e( 'Greater Than', 'loading-page' ); ?></option>
										<option value="lesser"
										<?php
										if (
																	isset( $loading_page_options['screen_size'] ) &&
																	'lesser' === $loading_page_options['screen_size']
																) {
											print 'SELECTED';
										}
										?>
																><?php esc_html_e( 'Lesser Than', 'loading-page' ); ?></option>
									</select>
									<div id="lp_width_container" style="float:left; padding-left:10px;
									<?php
									if ( ! isset( $loading_page_options['screen_size'] ) || 'all' === $loading_page_options['screen_size'] ) {
										echo 'display:none;';
									} else {
										echo 'display:block;';
									}
									?>
																										">
										<?php esc_html_e( 'Width', 'loading-page' ); ?>:
										<input aria-label="<?php esc_attr_e( 'Screen width', 'loading-page' ); ?>" type="text" name="lp_screen_width" value="<?php
										if ( isset( $loading_page_options['screen_width'] ) ) {
											echo esc_attr( $loading_page_options['screen_width'] );
										}
										?>" /> px
									</div>
									<script>
										jQuery('[name="lp_screen_size"]').on('change',function() {
											jQuery('#lp_width_container')[(this.value == 'all') ? 'hide' : 'show']();
										})
									</script>
								</td>
							</tr>
							<tr>
								<th><?php esc_html_e( 'Display the loading screen', 'loading-page' ); ?></th>
								<td>
									<?php
									if (
										! isset( $loading_page_options['once_per_session'] ) ||
										false === $loading_page_options['once_per_session']
									) {
										$loading_page_options['once_per_session'] = 'always';
									} elseif ( true === $loading_page_options['once_per_session'] ) {
										$loading_page_options['once_per_session'] = 'site';
									}
									?>
									<input aria-label="<?php esc_attr_e( 'Always', 'loading-page' ); ?>" type="radio" value="always" name="once_per_session" <?php echo ( ( 'always' === $loading_page_options['once_per_session'] ) ? 'CHECKED' : '' ); ?> /> <span><?php esc_html_e( 'always', 'loading-page' ); ?></span><br />
									<input aria-label="<?php esc_attr_e( 'Once per session', 'loading-page' ); ?>" type="radio" value="site" name="once_per_session" <?php echo ( ( 'site' === $loading_page_options['once_per_session'] ) ? 'CHECKED' : '' ); ?> /> <span><?php esc_html_e( 'once per session', 'loading-page' ); ?></span><br />
									<input aria-label="<?php esc_attr_e( 'Once per page', 'loading-page' ); ?>" type="radio" value="page" name="once_per_session" <?php echo ( ( 'page' === $loading_page_options['once_per_session'] ) ? 'CHECKED' : '' ); ?> /> <span><?php esc_html_e( 'once per page', 'loading-page' ); ?></span>

								</td>
							</tr>
							<tr>
								<th><?php esc_html_e( 'Display loading screen in', 'loading-page' ); ?></th>
								<td>
									<div style="margin-bottom:10px;"><input aria-label="<?php esc_attr_e( 'Homepage', 'loading-page' ); ?>" type="radio" name="lp_loading_screen_display_in" value="home" <?php echo ( ( isset( $loading_page_options['lp_loading_screen_display_in'] ) && 'home' === $loading_page_options['lp_loading_screen_display_in'] ) ? 'CHECKED' : '' ); ?> /> <?php esc_html_e( 'homepage only', 'loading-page' ); ?></div>

									<div style="margin-bottom:10px;"><input aria-label="<?php esc_attr_e( 'All pages', 'loading-page' ); ?>" type="radio" name="lp_loading_screen_display_in" value="all" <?php echo ( ( isset( $loading_page_options['lp_loading_screen_display_in'] ) && 'all' === $loading_page_options['lp_loading_screen_display_in'] ) ? 'CHECKED' : '' ); ?> /> <?php esc_html_e( 'all pages', 'loading-page' ); ?>

										<div style="margin-left:20px;margin-top:10px;">
											<?php esc_html_e( 'Display the loading screen only on pages with the URLs (If this field is left empty, the loading screen will be displayed on all pages.)', 'loading-page' ); ?>
											<textarea aria-label="<?php esc_attr_e( 'URLs of the pages to include', 'loading-page' ); ?>" name="lp_loading_screen_include_from_urls" style="width:100%" rows="6"><?php
																																																					print esc_textarea(
																																																						( ! empty( $loading_page_options['lp_loading_screen_include_from_urls'] ) )
																																																							? implode( "\n", $loading_page_options['lp_loading_screen_include_from_urls'] ) : ''
																																																					);
																?></textarea>
											<i><?php esc_html_e( 'Enter a URL per row (use the * symbol as wildcard)', 'loading-page' ); ?></i>
										</div>
									</div>

									<div style="margin-bottom:20px;"><input aria-label="<?php esc_attr_e( 'Specific pages', 'loading-page' ); ?>" type="radio" name="lp_loading_screen_display_in" value="pages" <?php echo ( ( isset( $loading_page_options['lp_loading_screen_display_in'] ) && 'pages' === $loading_page_options['lp_loading_screen_display_in'] ) ? 'CHECKED' : '' ); ?> /> <?php esc_html_e( 'specific pages', 'loading-page' ); ?>
										<div style="margin-left:20px;margin-top:10px;">
											<input aria-label="<?php esc_attr_e( 'Pages/posts ids', 'loading-page' ); ?>" type="text" name="lp_loading_screen_display_in_pages" value="<?php
											if ( ! empty( $loading_page_options['lp_loading_screen_display_in_pages'] ) ) {
												print esc_attr( $loading_page_options['lp_loading_screen_display_in_pages'] );}
											?>" style="width:100%;">
											<i><?php esc_html_e( 'Type one, or more post/pages IDs, separated by commas ","', 'loading-page' ); ?></i>
										</div>
									</div>

									<div style="margin-bottom:20px;"><input aria-label="<?php esc_attr_e( 'specific post types', 'loading-page' ); ?>" type="radio" name="lp_loading_screen_display_in" value="post_type" <?php echo ( ( isset( $loading_page_options['lp_loading_screen_display_in'] ) && 'post_type' === $loading_page_options['lp_loading_screen_display_in'] ) ? 'CHECKED' : '' ); ?> /> <?php esc_html_e( 'specific post types', 'loading-page' ); ?>
										<div style="margin-left:20px;margin-top:10px;">
											<input aria-label="<?php esc_attr_e( 'Post types', 'loading-page' ); ?>" type="text" name="lp_loading_screen_display_post_types" value="<?php
											if ( ! empty( $loading_page_options['lp_loading_screen_display_post_types'] ) ) {
												print esc_attr( $loading_page_options['lp_loading_screen_display_post_types'] );}
											?>" style="width:100%;">
											<i><?php esc_html_e( 'Type one or more post types, separated by commas ","', 'loading-page' ); ?></i>
										</div>
									</div>
								</td>
							</tr>
							<tr>
								<th><?php esc_html_e( 'Devices', 'loading-page' ); ?></th>
								<td>
									<label><input aria-label="<?php esc_attr_e( 'Display the loading screen only on the desktop', 'loading-page' ); ?>" type="radio" name="lp_loading_screen_devices" value="desktop" <?php
									if ( ! empty( $loading_page_options['lp_loading_screen_devices'] ) && 'desktop' == $loading_page_options['lp_loading_screen_devices'] ) {
										print 'CHECKED';}
									?>> <?php esc_html_e( 'Display the loading screen only on desktops', 'loading-page' ); ?></label><br>
									<label><input aria-label="<?php esc_attr_e( 'Display the loading screen only on mobiles', 'loading-page' ); ?>" type="radio" name="lp_loading_screen_devices" value="mobile" <?php
									if ( ! empty( $loading_page_options['lp_loading_screen_devices'] ) && 'mobile' == $loading_page_options['lp_loading_screen_devices'] ) {
										print 'CHECKED';}
									?>> <?php esc_html_e( 'Display the loading screen only on mobiles', 'loading-page' ); ?></label><br>
									<label><input aria-label="<?php esc_attr_e( 'Display the loading screen on both desktops and mobiles', 'loading-page' ); ?>" type="radio" name="lp_loading_screen_devices" value="both" <?php
									if ( empty( $loading_page_options['lp_loading_screen_devices'] ) || 'both' == $loading_page_options['lp_loading_screen_devices'] ) {
										print 'CHECKED';}
									?>> <?php esc_html_e( 'Display the loading screen on both desktops and mobiles', 'loading-page' ); ?></label><br>
								</td>
							</tr>
							<tr>
								<td colspan="2">
									<hr />
								</td>
							</tr>
							<tr>
								<th><?php esc_html_e( 'Exclude loading screen from', 'loading-page' ); ?></th>
								<td><input aria-label="<?php esc_attr_e( 'Pages/posts ids separated by comma', 'loading-page' ); ?>" type="text" name="lp_loading_screen_exclude_from_pages" value="<?php
								if ( ! empty( $loading_page_options['lp_loading_screen_exclude_from_pages'] ) ) {
									print esc_attr( $loading_page_options['lp_loading_screen_exclude_from_pages'] );}
								?>"> <i><?php esc_html_e( 'Type one, or more post/pages IDs, separated by commas ","', 'loading-page' ); ?></i></td>
							</tr>
							<tr>
								<th><?php esc_html_e( 'Exclude loading screen from post types', 'loading-page' ); ?></th>
								<td><input aria-label="<?php esc_attr_e( 'Post types separated by comma', 'loading-page' ); ?>" type="text" name="lp_loading_screen_exclude_from_post_types" value="<?php
								if ( ! empty( $loading_page_options['lp_loading_screen_exclude_from_post_types'] ) ) {
									print esc_attr( $loading_page_options['lp_loading_screen_exclude_from_post_types'] );}
								?>"> <i><?php esc_html_e( 'Type one, or more post/pages types, separated by commas ","', 'loading-page' ); ?></i></td>
							</tr>
							<tr>
								<th><?php esc_html_e( 'Exclude loading screen from pages with the URL', 'loading-page' ); ?></th>
								<td><textarea aria-label="<?php esc_attr_e( 'URLs of the pages to exclude', 'loading-page' ); ?>" name="lp_loading_screen_exclude_from_urls" style="width:100%" rows="6"><?php
																																																				print esc_textarea(
																																																					( ! empty( $loading_page_options['lp_loading_screen_exclude_from_urls'] ) )
																																																						? implode( "\n", $loading_page_options['lp_loading_screen_exclude_from_urls'] ) : ''
																																																				);
															?></textarea>
									<i><?php esc_html_e( 'Enter a URL per row (use the * symbol as wildcard)', 'loading-page' ); ?></i>
								</td>
							</tr>
							<tr>
								<th colspan="2">
									<?php esc_html_e( 'Exclude the loading screen if Elementor Maintenance or Coming Soon modes are enabled', 'loading-page' ); ?>
									<input aria-label="<?php esc_attr_e( 'Disable the loading screen from Coming Soon or Maintenance modes', 'loading-page' ); ?>" type="checkbox" name="lp_loading_screen_exclude_from_elementor_maintenance" <?php echo ( ( ! empty( $loading_page_options['lp_loading_screen_exclude_from_elementor_maintenance'] ) ) ? 'CHECKED' : '' ); ?> />
								</th>
							</tr>
							<tr>
								<td colspan="2">
									<hr />
								</td>
							</tr>
							<tr>
								<?php $loading_screens = loading_page_get_screen_list(); ?>
								<th><?php esc_html_e( 'Select the loading screen', 'loading-page' ); ?></th>
								<td>
									<select aria-label="<?php esc_attr_e( 'Loading screen', 'loading-page' ); ?>" name="lp_loading_screen">
										<?php
										foreach ( $loading_screens as $screen ) {
											print '<option value="' . esc_attr( $screen['id'] ) . '" ' . ( ( isset( $loading_page_options['loading_screen'] ) && $loading_page_options['loading_screen'] === $screen['id'] ) ? 'SELECTED' : '' ) . ' title="' . ( ( ! empty( $screen['tips'] ) ) ? esc_attr( $screen['tips'] ) : '' ) . '" >' . esc_html( $screen['name'] ) . '</option>';
										}
										?>
									</select>
									<span class="lp-upgrade-message">
										<?php
											esc_html_e( 'The free version of the plugin includes the "Bar", "Logo", and "Text" screens. If they are not sufficient to your website, the commercial version of the plugin includes additional loading screens.', 'loading-page' );
										?> <a href="http://wordpress.dwbooster.com/content-tools/loading-page" target="_blank"><?php esc_html_e( 'CLICK HERE', 'loading-page' ); ?></a>
									</span>
								</td>
							</tr>
							<?php
							foreach ( $loading_screens as $screen ) {
								if ( ! empty( $screen['adminsection'] ) ) {
									include_once $screen['adminsection'];
								}
								if ( ! empty( $screen['adminscript'] ) ) {
									wp_enqueue_script( $screen['adminscript'], $screen['adminscript'], array(), 'free-1.2.8', true );
								}
							}
							?>
							<tr>
								<th><?php esc_html_e( 'Select foreground color', 'loading-page' ); ?></th>
								<td><input aria-label="<?php esc_attr_e( 'Foreground color', 'loading-page' ); ?>" type="text" name="lp_foregroundColor" id="lp_foregroundColor" value="<?php
								if ( isset( $loading_page_options['foregroundColor'] ) ) {
									print( esc_attr( $loading_page_options['foregroundColor'] ) );}
								?>" />
									<div id="lp_foregroundColor_picker"></div>
								</td>
							</tr>
							<tr>
								<th><?php esc_html_e( 'Select background color', 'loading-page' ); ?></th>
								<td>
									<div style="display: flex;flex-direction: row;align-items: center; gap: 10px;">
										<input aria-label="<?php esc_attr_e( 'Background color', 'loading-page' ); ?>" type="text" name="lp_backgroundColor" id="lp_backgroundColor" value="<?php
										if ( isset( $loading_page_options['backgroundColor'] ) ) {
											print( esc_attr( $loading_page_options['backgroundColor'] ) );}
										?>" />

										<input aria-label="<?php esc_attr_e( 'Apply transparency', 'loading-page' ); ?>" type="checkbox" name="lp_backgroundTransparency" <?php print ( ! isset( $loading_page_options['transparency'] ) || $loading_page_options['transparency'] ) ? 'CHECKED' : ''; ?> /><?php esc_html_e( 'Apply transparency', 'loading-page' ); ?>

										<input type="range" id="lp_backgroundTransparencyPercentage" name="lp_backgroundTransparencyPercentage" min="0" max="100" value="<?php print esc_attr(
											! isset( $loading_page_options['transparencyPercentage'] ) ||
											! is_numeric( $loading_page_options['transparencyPercentage'] )
											? 80
											: max( min( intval( $loading_page_options['transparencyPercentage'] ), 100 ), 0 )
										); ?>" style="flex-grow:1;visibility:hidden;" />

										<span id="lp_backgroundTransparencyPercentageCaption" style="visibility:hidden;"></span>
									</div>
									<div id="lp_backgroundColor_picker"></div>
								</td>
							</tr>
							<tr>
								<th><?php esc_html_e( 'Select image as background', 'loading-page' ); ?></th>
								<td>
									<input aria-label="<?php esc_attr_e( 'Background image', 'loading-page' ); ?>" type="text" name="lp_backgroundImage" id="lp_backgroundImage" value="<?php
									if ( isset( $loading_page_options['backgroundImage'] ) ) {
										print( esc_attr( $loading_page_options['backgroundImage'] ) );}
									?>" />
									<input type="button" value="Browse" onclick="loading_page_selected_image('lp_backgroundImage');" class="button-secondary" style="margin-left:10px;" />
									<select aria-label="<?php esc_attr_e( 'Background position', 'loading-page' ); ?>" id="lp_backgroundRepeat" name="lp_backgroundRepeat">
										<option value="repeat"
										<?php
										if ( isset( $loading_page_options['backgroundImageRepeat'] ) && 'repeat' === $loading_page_options['backgroundImageRepeat'] ) {
											echo 'SELECTED';}
										?>
										>Tile</option>
										<option value="no-repeat"
										<?php
										if ( isset( $loading_page_options['backgroundImageRepeat'] ) && 'no-repeat' === $loading_page_options['backgroundImageRepeat'] ) {
											echo 'SELECTED';}
										?>
										>Center</option>
									</select>

								</td>
							</tr>
							<tr>
								<th><?php esc_html_e( 'Display image in fullscreen', 'loading-page' ); ?></th>
								<td>
									<input aria-label="<?php esc_attr_e( 'Fullscreen', 'loading-page' ); ?>" type="checkbox" name="lp_fullscreen" id="lp_fullscreen" <?php echo ( ( isset( $loading_page_options['fullscreen'] ) && $loading_page_options['fullscreen'] ) ? 'CHECKED' : '' ); ?> />
									<i><?php esc_html_e( '(The fullscreen attribute can fail in some browsers)', 'loading-page' ); ?></i>
								</td>
							</tr>
							<tr>
								<th><?php esc_html_e( 'Additional seconds', 'loading-page' ); ?></th>
								<td>
									<input aria-label="<?php esc_attr_e( 'Additional seconds', 'loading-page' ); ?>" type="text" name="lp_additionalSeconds" id="lp_additionalSeconds" value="<?php
									if ( isset( $loading_page_options['additionalSeconds'] ) ) {
										print( esc_attr( $loading_page_options['additionalSeconds'] ) );}
									?>" />
									<i><?php esc_html_e( 'Show the loading screen some few seconds after loading the page', 'loading-page' ); ?></i>
								</td>
							</tr>
							<tr>
								<th><?php esc_html_e( 'Include an ad, or your own block of code', 'loading-page' ); ?></th>
								<td>
									<textarea aria-label="<?php esc_attr_e( 'Ad or block of code', 'loading-page' ); ?>" name="lp_codeBlock" id="lp_codeBlock" rows="6" style="width:80%;"><?php
									if ( isset( $loading_page_options['codeBlock'] ) ) {
										print( esc_textarea( $loading_page_options['codeBlock'] ) );}
									?></textarea>
									<p><i><b><?php esc_html_e( 'Trick', 'loading-page' ); ?></b>:
											<?php esc_html_e( 'As the block of code you can insert a pair of &lt;style&gt;&lt;/style&gt; tags to customize the appearance of loading screen. For example: for changing the font-family of the loading text:  <b>&lt;style&gt;.lp-screen-text{font-family:Georgia !important;}&lt;/style&gt;</b>', 'loading-page' ); ?></i></p>
								</td>
							</tr>
							<tr>
								<th><?php esc_html_e( 'Apply the effect on page', 'loading-page' ); ?></th>
								<td>
									<select aria-label="<?php esc_attr_e( 'Animation effect', 'loading-page' ); ?>" name="lp_pageEffect">
										<?php
										$page_effects = array( 'none', 'rotateInLeft' );

										foreach ( $page_effects as $value ) {
											print '<option value="' . esc_attr( $value ) . '" ' . ( ( isset( $loading_page_options['pageEffect'] ) && $loading_page_options['pageEffect'] === $value ) ? 'SELECTED' : '' ) . '>' . esc_html( $value ) . '</option>';
										}
										?>

									</select>
									<div class="lp-upgrade-message"><?php esc_html_e( 'The premium version of plugin add the following effects: collapseIn, risingFromBottom, expandIn, fadeIn, fallFromTop, rotateInLeft, rotateInRight, rotateInRightWithoutToKeyframe, slideInSkew, tumbleIn, whirlIn.', 'loading-page' ); ?><br><a href="http://wordpress.dwbooster.com/content-tools/loading-page" target="_blank"><?php esc_html_e( 'CLICK HERE',  'loading-page'); ?></a></div>
								</td>
							</tr>
							<tr>
								<th><?php esc_html_e( 'Display loading percent', 'loading-page' ); ?></th>
								<td><input aria-label="<?php esc_attr_e( 'Display loading percent', 'loading-page' ); ?>" type="checkbox" name="lp_displayPercent" <?php echo ( ( ! empty( $loading_page_options['displayPercent'] ) ) ? 'CHECKED' : '' ); ?> /></td>
							</tr>
						</table>
						<div style="border: 1px solid #DADADA; padding:10px;">
							<h3><?php esc_html_e( 'Troubleshoot Area - Loading Screen', 'loading-page' ); ?></h3>
							<table class="form-table">
								<tr>
									<th><?php esc_html_e( 'Disable the search in deep', 'loading-page' ); ?></th>
									<td>
										<input aria-label="<?php esc_attr_e( 'Disable search in deep', 'loading-page' ); ?>" type="checkbox" name="lp_deactivateDeepSearch" <?php echo ( ( empty( $loading_page_options['deepSearch'] ) ) ? 'CHECKED' : '' ); ?> /> <i><?php esc_html_e( 'If the loading screen stops in some percentage, tick the checkbox', 'loading-page' ); ?></i>
									</td>
								</tr>
								<tr>
									<th><?php esc_html_e( 'The pages\' background is visible', 'loading-page' ); ?></th>
									<td>
										<input aria-label="<?php esc_attr_e( 'Tick if page visible', 'loading-page' ); ?>" type="checkbox" name="lp_modifyDisplayRule" <?php echo ( ( ! empty( $loading_page_options['modifyDisplayRule'] ) ) ? 'CHECKED' : '' ); ?> /> <i><?php esc_html_e( 'If website\'s background is visible before the loading screen, tick the checkbox', 'loading-page' ); ?></i>
									</td>
								</tr>
								<tr>
									<td colspan="2" style="padding-left:0;">
										<?php esc_html_e( 'You have ticked the box "show loading screen on link click", but the loading screen disappears too quickly, before loading the content of the next page.', 'loading-page' ); ?><br /><br />
										<input aria-label="<?php esc_attr_e( 'Loading screen disappear too quickly', 'loading-page' ); ?>" type="checkbox" name="lp_triggerLinkScreenNeverClose" <?php echo ( ( ! empty( $loading_page_options['triggerLinkScreenNeverClose'] ) ) ? 'CHECKED' : '' ); ?>/> <?php esc_html_e( 'Do not hide the loading screen', 'loading-page' ); ?>&nbsp;
										<?php esc_html_e( '- or close after ', 'loading-page' ); ?>&nbsp;
										<input type="number" name="lp_triggerLinkScreenCloseAfter" aria-label="<?php esc_attr_e( 'Close after X seconds', 'loading-page' ); ?>" value="<?php print esc_attr( isset( $loading_page_options['triggerLinkScreenCloseAfter'] ) ? $loading_page_options['triggerLinkScreenCloseAfter'] : 4 ); ?>" />&nbsp;
										<?php esc_html_e( 'seconds', 'loading-page' ); ?>
									</td>
								</tr>
							</table>
						</div>
					</div>
				</div>
				<div class="postbox">
					<h3 class='hndle' style="padding:5px;"><span><?php esc_html_e( 'Lazy Loading', 'loading-page' ); ?></span></h3>
					<div class="inside">
						<p>
						<?php
							esc_html_e(
								'To load only the images visible in the viewport to improve the loading rate of your website and reduce the bandwidth consumption.',
								'loading-page'
							);
						?>
						</p>
						<p class="lp-upgrade-message">
							<?php esc_html_e( 'The lazy loading of images is available only in the commercial version of plugin.', 'loading-page' ); ?> <a href="http://wordpress.dwbooster.com/content-tools/loading-page" target="_blank"><?php esc_html_e( 'CLICK HERE', 'loading-page' ); ?></a>
						</p>
						<p><img alt="<?php esc_attr_e( 'Lazy loading chart', 'loading-page' ); ?>" src="<?php print( esc_attr( LOADING_PAGE_PLUGIN_URL . '/images/consumption_graph.png' ) ); ?>" style="max-width:100%;" /></p>
						<table class="form-table">
							<tr>
								<th><?php esc_html_e( 'Enable lazy loading', 'loading-page' ); ?></th>
								<td><input aria-label="<?php esc_attr_e( 'Enable lazy loading', 'loading-page' ); ?>" type="checkbox" DISABLED /></td>
							</tr>
							<tr>
								<th><?php esc_html_e( 'Select the image to load by default', 'loading-page' ); ?></th>
								<td>
									<input aria-label="<?php esc_attr_e( 'Image to load by default', 'loading-page' ); ?>" type="text" DISABLED /><input type="button" value="Browse" DISABLED class="button-secondary" style="margin-left:10px;" />
								</td>
							</tr>
							<tr>
								<th><?php esc_html_e( 'Exclude lazy loading from', 'loading-page' ); ?></th>
								<td><input aria-label="<?php esc_attr_e( 'Pages/posts ids separated by comma', 'loading-page' ); ?>" type="text" DISABLED /> <i><?php esc_html_e( 'Type one, or more post/pages IDs, separated by commas ","', 'loading-page' ); ?></i></td>
							</tr>
						</table>
						<div style="border: 1px solid #DADADA; padding:10px;">
							<h3><?php esc_html_e( 'Troubleshoot Area - Lazy Loading', 'loading-page' ); ?></h3>
							<table class="form-table">
								<tr>
									<th><?php esc_html_e( 'Exclude images whose tag includes the class or attribute', 'loading-page' ); ?></th>
									<td>
										<input aria-label="<?php esc_attr_e( 'Class or attributes names', 'loading-page' ); ?>" type="text" style="width:100%;" disabled /><br />
										<p><i><?php esc_html_e( 'Don\'t apply the lazy loading to the images with the classes or attributes (separated by commas ",")', 'loading-page' ); ?></i></p>
									</td>
								</tr>
							</table>
						</div>
					</div>
				</div>
				<div><input type="submit" value="Update Settings" class="button-primary" /></div>
			</form>
		</div>
		<?php
	} // End loading_page_settings_page.
}

/** ListeSpeed Cache integration **/

add_filter( 'litespeed_optimize_css_excludes', function( $p ){
	$p[] = LOADING_PAGE_PLUGIN_URL;
	return $p;
} );
add_filter( 'litespeed_optimize_js_excludes', function( $p ){
	$p[] = 'jquery.js';
	$p[] = 'jquery.min.js';
	$p[] = 'loading_page_settings';
	$p[] = LOADING_PAGE_PLUGIN_URL;
	return $p;
} );
add_filter( 'litespeed_optm_js_defer_exc', function( $p ){
	$p[] = 'jquery.js';
	$p[] = 'jquery.min.js';
	$p[] = 'loading_page_settings';
	$p[] = LOADING_PAGE_PLUGIN_URL;
	return $p;
} );
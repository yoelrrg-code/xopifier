<?php
/**
 * Plugin Name: Timeline Blocks for Gutenberg
 * Plugin URI: https://wordpress.org/plugins/timeline-blocks/
 * Description: A beautiful timeline block to showcase your posts in timeline presentation with multiple templates availability.
 * Author: Techeshta
 * Author URI: https://www.techeshta.com
 * Version: 2.0.0
 * Requires at least: 5.0
 * Requires PHP: 5.6
 * License: GPL2+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: timeline-blocks
 */

/**
 * Exit if accessed directly
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define plugin constants
define( 'TB_DOMAIN', 'timeline-blocks' );
define( 'TB_DIR', plugin_dir_path( __FILE__ ) );

/**
 * Initialize and load the plugin block functionality.
 *
 * @since 1.0.0
 */
function timeline_blocks_loader() {
	/**
	 * Load the blocks functionality
	 */
	require_once plugin_dir_path( __FILE__ ) . 'dist/init.php';

	/**
	 * Load Post Grid PHP
	 */
	require_once plugin_dir_path( __FILE__ ) . 'src/blocks/index.php';
}
add_action( 'plugins_loaded', 'timeline_blocks_loader' );

/**
 * Handle options and redirects on plugin activation.
 *
 * @since 1.0.0
 */
function timeline_blocks_activate() {
	add_option( 'tb_timeline_gutenberg_do_activation_redirect', true );
}
register_activation_hook( __FILE__, 'timeline_blocks_activate' );

/**
 * Register custom image sizes for the timeline layout templates.
 *
 * @since 1.0.0
 */
function timeline_blocks_image_sizes() {
	// Add custom landscape size for timeline images
	add_image_size( 'tb-timeline-landscape', 600, 400, true );
	// Add custom square size for timeline images
	add_image_size( 'tb-timeline-square', 600, 600, true );
}
add_action( 'after_setup_theme', 'timeline_blocks_image_sizes' );


<?php
/**
 * Blocks Initializer
 *
 * Enqueue CSS/JS of all the blocks.
 *
 * @since   1.0.0
 * @package Timeline Blocks for Gutenberg
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Compare PHP version to ensure compatibility.
if ( ! version_compare( PHP_VERSION, '5.6', '>=' ) ) {
	add_action( 'admin_notices', 'timeline_blocks_fail_php_version' );
} else {
	require_once TB_DIR . 'src/tb-helper/class-tb-loader.php';
}

/**
 * Display PHP version compatibility failure message in admin notice.
 *
 * @since 1.0.0
 */
function timeline_blocks_fail_php_version() {
	/* translators: %s: PHP version */
	$message      = sprintf( esc_html__( 'Timeline Block for Gutenberg requires PHP version %s+, plugin is currently NOT RUNNING.', 'timeline-blocks' ), '5.6' );
	$html_message = sprintf( '<div class="error">%s</div>', wpautop( $message ) );
	echo wp_kses( $html_message, wp_kses_allowed_html( 'post' ) );
}

/**
 * Enqueue assets for frontend and backend.
 *
 * @since   1.0.0
 */
function timeline_block_assets() {
	// Load the compiled styles.
	wp_enqueue_style(
		'tb-block-style-css',
		plugins_url( 'dist/blocks.style.build.css', dirname( __FILE__ ) ),
		array(),
		filemtime( plugin_dir_path( __FILE__ ) . 'blocks.style.build.css' )
	);

	// Load the FontAwesome icon library.
	wp_enqueue_style(
		'tb-block-fontawesome',
		plugins_url( 'dist/assets/fontawesome/css/all.css', dirname( __FILE__ ) ),
		array(),
		filemtime( plugin_dir_path( __FILE__ ) . 'assets/fontawesome/css/all.css' )
	);
}
add_action( 'enqueue_block_assets', 'timeline_block_assets' );

/**
 * Enqueue assets for backend editor.
 *
 * @since 1.0.0
 */
function timeline_block_editor_assets() {
	// Load the compiled blocks into the editor, loading it in the footer.
	wp_enqueue_script(
		'tb-block-js',
		plugins_url( '/dist/blocks.build.js', dirname( __FILE__ ) ),
		array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor', 'wp-api' ),
		filemtime( plugin_dir_path( __FILE__ ) . 'blocks.build.js' ),
		true
	);

	// Load the compiled styles into the editor.
	wp_enqueue_style(
		'tb-block-editor-css',
		plugins_url( 'dist/blocks.editor.build.css', dirname( __FILE__ ) ),
		array(),
		filemtime( plugin_dir_path( __FILE__ ) . 'blocks.editor.build.css' )
	);

	// Add script translations and locale configuration.
	if ( function_exists( 'wp_set_script_translations' ) ) {
		wp_add_inline_script(
			'timeline-blocks',
			sprintf(
				'var timeline_blocks = { localeData: %s };',
				wp_json_encode( wp_set_script_translations( 'timeline_blocks', 'timeline-blocks' ) )
			),
			'before'
		);
	} elseif ( function_exists( 'gutenberg_set_script_translations' ) ) {
		wp_add_inline_script(
			'timeline-blocks',
			sprintf(
				'var timeline_blocks = { localeData: %s };',
				wp_json_encode( gutenberg_set_script_translations( 'timeline_blocks', 'timeline-blocks' ) )
			),
			'before'
		);
	}
}
add_action( 'enqueue_block_editor_assets', 'timeline_block_editor_assets' );

// Add custom block category.
add_filter(
	'block_categories_all',
	function( $categories, $post ) {
		return array_merge(
			$categories,
			array(
				array(
					'slug'  => 'timeline-blocks',
					'title' => __( 'Timeline Blocks by Techeshta', 'timeline-blocks' ),
				),
			)
		);
	},
	10,
	2
);


<?php 
require "inc/enqueue-scripts.php";
require "inc/theme-customize.php";
require "inc/shortcodes.php";
require "inc/ajax-requests.php";
require "inc/mailing.php";
require "inc/step-1/funcs.php";
require "inc/step-2/funcs.php";
require "inc/step-3/funcs.php";
require "inc/step-4/funcs.php";

global $designId;

//require "widgets/autoload.php";

define('MONTHS', array(
    1 => 'Enero',
    2 => 'Febrero',
    3 => 'Marzo',
    4 => 'Abril',
    5 => 'Mayo',
    6 => 'Junio',
    7 => 'Julio',
    8 => 'Agosto',
    9 => 'Septiembre',
    10 => 'Octubre',
    11 => 'Noviembre',
    12 => 'Diciembre'
));

function isMobile() {
    return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|CriOS|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
}

add_action('admin_head', '_custom_styles');

function _custom_styles() {
  echo '
  <style>
    div.acf-admin-toolbar:nth-child(4){
        display: none!important;
    }
  </style>';
}

function _setup() {
    /*
     * Makes xopifier available for translation.
     *
     * Translations can be added to the /languages/ directory.
     * If you're building a theme based on xopifier, use a find and
     * replace to change 'xopifier' to the name of your theme in all
     * template files.
     */
    //load_theme_textdomain( 'xopifier', get_template_directory() . '/languages' );
    
    show_admin_bar(false);

    /*
     * This theme styles the visual editor to resemble the theme style,
     * specifically font, colors, icons, and column width.
     */                                                                                                    

    // Adds RSS feed links to <head> for posts and comments.
    add_theme_support( 'automatic-feed-links' );

    /*
     * Switches default core markup for search form, comment form,
     * and comments to output valid HTML5.
     */
    add_theme_support( 'html5', array(
        'comment-form', 'comment-list', 'search-form', 'gallery', 'caption'
    ) );
    
    /*** IMAGES SIZES ***/
    if ( function_exists( 'add_image_size' ) ) { 
        add_image_size( 'size_1', 620, 300,true); 
        add_image_size( 'size_2', 410, 300,true); 
        add_image_size( 'size_3', 410, 610,true); 
        add_image_size( 'size_4', 200, 300,true); 
        add_image_size( 'size_5', 200, 145,true);
        add_image_size( 'mobile', 280, 280,true); 
    } 

    /*
     * This theme supports all available post formats by default.
     * See http://codex.wordpress.org/Post_Formats
     */
    add_theme_support( 'post-formats', array(
        //'aside', 'audio', 'chat', 'gallery', 'image', 'link', 'quote', 'status', 'video'
    ) );

    // This theme uses wp_nav_menu() in one location.            
    register_nav_menu( 'primary', __( 'Menu Principal', 'xopifier' ) );
    // register_nav_menu( 'primary-mobile', __( 'Menu Principal Mobile', 'xopifier' ) );
    // register_nav_menu( 'wpml-mobile', __( 'Menu WPML Mobile', 'xopifier' ) );
    register_nav_menu( 'footer', __( 'Menu Footer', 'xopifier' ) );

    /*
     * This theme uses a custom image size for featured images, displayed on
     * "standard" posts and pages.
     */
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    set_post_thumbnail_size( 604, 270, true );

    add_theme_support( 'html5', array( 'search-form' ) );

    add_theme_support( 'woocommerce' );

    // This theme uses its own gallery styles.
    add_filter( 'use_default_gallery_style', '__return_false' );
}
add_action( 'after_setup_theme', '_setup' );

remove_post_type_support( 'post', 'revisions' );

/**
 * Filter the page title.
 *
 * Creates a nicely formatted and more specific title element text for output
 * in head of document, based on current view.
 *
 * @since xopifier 1.0
 *
 * @param string $title Default title text for current view.
 * @param string $sep   Optional separator.
 * @return string The filtered title.
 */
function _wp_title( $title, $sep ) {
	global $paged, $page;

	if ( is_feed() )
		return $title;
    
    $title = apply_filters('', $title, $sep );
    
    if( is_home() || is_front_page() )
	    $title = get_bloginfo( 'name' ). ' | ' .get_bloginfo( 'description' );
    elseif(is_archive())
        $title = wp_strip_all_tags(get_the_archive_title()). ' - '.get_bloginfo( 'description' ). ' - '.get_bloginfo( 'name' );    
    else
        $title = get_the_title(). ' - '.get_bloginfo( 'description' ). ' - '.get_bloginfo( 'name' );

	return $title;
}
add_filter( 'wp_title', '_wp_title', 10, 2 );

function _widgets_init() {
    register_sidebar( array(
        'name'          => __( 'Footer 1', 'xopifier' ),
        'id'            => 'footer-1',
        'before_widget' => '',
        'after_widget'  => '',
    ) );
    register_sidebar( array(
        'name'          => __( 'Footer 2', 'xopifier' ),
        'id'            => 'footer-2',
        'before_widget' => '',
        'after_widget'  => '',
    ) );
    register_sidebar( array(
        'name'          => __( 'Footer 3', 'xopifier' ),
        'id'            => 'footer-3',
        'before_widget' => '',
        'after_widget'  => '',
    ) );
    register_sidebar( array(
        'name'          => __( 'Footer 4', 'xopifier' ),
        'id'            => 'footer-4',
        'before_widget' => '',
        'after_widget'  => '',
    ) );
    register_sidebar( array(
        'name'          => __( 'Footer 5', 'xopifier' ),
        'id'            => 'footer-5',
        'before_widget' => '',
        'after_widget'  => '',
    ) );

    register_sidebar( array(
        'name'          => __( 'Footer Copyright', 'xopifier' ),
        'id'            => 'footer-copyright',
        'before_widget' => '',
        'after_widget'  => '',
    ) );
}
add_action( 'widgets_init', '_widgets_init' );

function time_ago( $time ){

    $TIMEBEFORE_NOW = __('now', 'xopifier');
    $TIMEBEFORE_MINUTE = '{num} '.__('minute ago', 'xopifier');
    $TIMEBEFORE_MINUTES = '{num} '.__('minutes ago', 'xopifier');
    $TIMEBEFORE_HOUR = '{num} '.__('hour ago', 'xopifier');
    $TIMEBEFORE_HOURS = '{num} '.__('hours ago', 'xopifier');
    $TIMEBEFORE_YESTERDAY =  __('yesterday', 'xopifier');
    $TIMEBEFORE_FORMAT = '%e %b';
    $TIMEBEFORE_FORMAT_YEAR = '%e %b, %Y';
    $TIMEBEFORE_DAYS = '{num} '.__('days ago', 'xopifier');
    $TIMEBEFORE_WEEK = '{num} '.__('week ago', 'xopifier');
    $TIMEBEFORE_WEEKS = '{num} '.__('weeks ago', 'xopifier');
    $TIMEBEFORE_MONTH = '{num} '.__('month ago', 'xopifier');
    $TIMEBEFORE_MONTHS = '{num} '.__('months ago', 'xopifier');

    $out    = ''; // what we will print out
    $now    = time(); // current time
    $diff   = $now - $time; // difference between the current and the provided dates

    if( $diff < 60 ) // it happened now
        return $TIMEBEFORE_NOW;

    elseif( $diff < 3600 ) // it happened X minutes ago
        return str_replace( '{num}', ( $out = round( $diff / 60 ) ), $out == 1 ? $TIMEBEFORE_MINUTE : $TIMEBEFORE_MINUTES );

    elseif( $diff < 3600 * 24 ) // it happened X hours ago
        return str_replace( '{num}', ( $out = round( $diff / 3600 ) ), $out == 1 ? $TIMEBEFORE_HOUR : $TIMEBEFORE_HOURS );

    elseif( $diff < 3600 * 24 * 2 ) // it happened yesterday
        return $TIMEBEFORE_YESTERDAY;

    elseif( $diff < 3600 * 24 * 7 )
        return str_replace( '{num}', round( $diff / ( 3600 * 24 ) ), $TIMEBEFORE_DAYS );

    elseif( $diff < 3600 * 24 * 7 * 4 )
        return str_replace( '{num}', ( $out = round( $diff / ( 3600 * 24 * 7 ) ) ), $out == 1 ? $TIMEBEFORE_WEEK : $TIMEBEFORE_WEEKS );

    elseif( $diff < 3600 * 24 * 7 * 4 * 12 )
        return str_replace( '{num}', ( $out = round( $diff / ( 3600 * 24 * 7 * 4 ) ) ), $out == 1 ? $TIMEBEFORE_MONTH : $TIMEBEFORE_MONTHS );

    else // falling back on a usual date format as it happened later than yesterday
        return strftime( date( 'Y', $time ) == date( 'Y' ) ? $TIMEBEFORE_FORMAT : $TIMEBEFORE_FORMAT_YEAR, $time );
}

remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head');

function pagination_bar($wp_query = null) {
    if(is_null($wp_query))
        global $wp_query;
        
    $total_pages = $wp_query->max_num_pages;
 
    if ($total_pages > 1){
        $current_page = max(1, get_query_var('paged'));
 
        echo paginate_links(
            array(
                'base' => get_pagenum_link(1) . '%_%',
                'format' => 'page/%#%',
                'current' => $current_page,
                'total' => $total_pages,
                'mid_size' => 3,
                'prev_text' => __('<i class="fa fa-arrow-left"></i>', 'xopifier'),
                'next_text' => __('<i class="fa fa-arrow-right"></i>', 'xopifier'),
            )
        );
    }
}
/**
 * Prints HTML with meta information for the current post-date/time.
 */
function _posted_on() {

    $time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
    if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
        $time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time>';
    }

    $time_string = sprintf( $time_string,
        esc_attr( get_the_date( DATE_W3C ) ),
        esc_html( get_the_date() )
    );

    $posted_on = sprintf(
    /* translators: %s: post date. */
        esc_html_x( '%s', 'post date', 'xopifier' ),
        '<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $time_string . '</a>'
    );

    echo '<span class="posted-on">' . $posted_on . '</span>'; // WPCS: XSS OK.

}

/**
 * Prints HTML with meta information for the current author.
 */
function _posted_in() {
    $categories_list = get_the_category_list( esc_html__( ' ', 'xopifier' ) );
    $posted_in = '';
    if ( $categories_list ) {
        /* translators: 1: list of categories. */
        $posted_in = sprintf( esc_html__( '%1$s', 'xopifier' ), $categories_list ); // WPCS: XSS OK.
    }

    echo '<div class="post-cat"><div class="posted-in">' . $posted_in . '</div></div>'; // WPCS: XSS OK.

}

/**
 * Prints HTML with meta information for the current author.
 */
function _posted_by() {
    $byline = sprintf(
        /* translators: %s: post author. */
        esc_html_x( '%s', 'post author', 'xopifier' ),
        '<a class="author url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a>'
    );

    return $byline; // WPCS: XSS OK.

}

function _related_posts() {

    global $post;

    $related = get_posts( array( 'category__in' => wp_get_post_categories($post->ID), 'numberposts' => 2, 'post__not_in' => array($post->ID) ) );
    if( $related ){

        echo '<div class="mt-5 related-posts inner-pages blog">';
            echo '<h3>'.esc_html__( 'Entradas relacionadas', 'xopifier' ).'</h3>';
            echo '<div class="row blog-list-masonry">';
            foreach( $related as $post ) {
                setup_postdata($post); ?>
                
                <div class="col-sm-6">
                    <?php get_template_part( 'template-parts/content-excerpt', get_post_format() );?>
                </div>

            <?php } wp_reset_postdata();

            echo '</div>';
        echo '</div>';

    }
}

function _single_post_nav(){
    echo '<div class="post-nav container"><div class="row">';                    

    if ( get_previous_post() ) {
        $ppost  = get_previous_post();
        $ptitle = get_the_title( $ppost->ID );

        if ( ! is_singular('post') ) {
            $pdate = '';
            $pcates = get_the_terms( $ppost->ID, 'portfolio_cat' );
            if ( ! is_wp_error( $pcates ) && ! empty( $pcates ) ) :            
                foreach ( $pcates as $pterm ) {
                    // The $pterm is an object, so we don't need to specify the $taxonomy.
                    $term_link = get_term_link( $pterm );
                    if ( is_wp_error( $term_link ) ) {
                        continue;
                    }
                    $pdate .= '[ ' . $pterm->name . ' ]' . '  ';
                }                                                   
            endif; 
        } else {
            $pdate  = get_the_time('d/m/Y', $ppost->ID );
        }    

        $pimage = get_the_post_thumbnail( $ppost->ID , 'thumbnail');
        
        echo '<div class="post-prev col-6"><i class="fa fa-arrow-left"></i>';
            previous_post_link( '%link', '
                <div class="d-flex">
                    <div class="thumb-post-prev">
                        '.$pimage.'
                        <span class="date">'.$pdate.'</span>    
                    </div>
                    <div class="info-post-prev">
                        <h6>
                            <span class="title-link">'.$ptitle.'</span>
                        </h6>
                    </div>
                </div>
            ' );
        echo '</div>';
    }

    if ( get_next_post() ) {
        $npost  = get_next_post();
        $ntitle = get_the_title( $npost->ID );
        
        if ( ! is_singular('post') ) {
            $ndate = '';
            $ncates = get_the_terms( $npost->ID, 'portfolio_cat' );
            if ( ! is_wp_error( $ncates ) && ! empty( $ncates ) ) :
                foreach ( $ncates as $nterm ) {
                    // The $nterm is an object, so we don't need to specify the $taxonomy.
                    $term_link = get_term_link( $pterm );                    
                    if ( is_wp_error( $term_link ) ) {
                        continue;
                    }
                    $ndate .= '[ ' . $nterm->name . ' ]' . '  ';
                }                                                   
            endif; 
        } else {
            $ndate  = get_the_time('d/m/Y', $npost->ID);
        }

        $nimage = get_the_post_thumbnail( $npost->ID , 'thumbnail');

        echo '<div class="post-next col-6">';            
            next_post_link( '%link', '
                <div class="d-flex align-items-start justify-content-start flex-row-reverse">
                    <div class="thumb-post-next">
                        '.$nimage.'
                        <span class="date">'.$ndate.'</span>
                    </div>
                    <div class="info-post-next">
                        <h6>
                            <span class="title-link">'.$ntitle.'</span
                        </h6>
                    </div>
                </div>
            ' );
        echo '<i class="fa fa-arrow-right"></i></div>';
    }
    echo '</div></div>';
}

function the_reverse_post_navigation( $args = array() ) {
    $args = wp_parse_args( $args, array(
        'prev_text'          => '%title',
        'next_text'          => '%title',
        'in_same_term'       => false,
        'excluded_terms'     => '',
        'taxonomy'           => 'category',
        'screen_reader_text' => __( 'Paginación' ),
    ) );

    $navigation = '';

    $previous = get_next_post_link(
        '<div class="nav-previous">%link</div>',
        $args['prev_text'],
        $args['in_same_term'],
        $args['excluded_terms'],
        $args['taxonomy']
    );

    $next = get_previous_post_link(
        '<div class="nav-next">%link</div>',
        $args['next_text'],
        $args['in_same_term'],
        $args['excluded_terms'],
        $args['taxonomy']
    );

    // Only add markup if there's somewhere to navigate to.
    if ( $previous || $next ) {
        $navigation = _navigation_markup( $previous . $next, 'post-navigation', $args['screen_reader_text'] );
    }

    echo $navigation;
}

function fe_comments_walker() {
    $fe_comment_email = get_comment_author_email();
    $fe_gravatar = get_avatar( $fe_comment_email, 160 ); ?>

    <li class="fe-comment" id="comment-<?php comment_ID() ?>">

        <div class="fe-comment-body comment-body">

            <?php if (get_option('show_avatars', true)) : ?>
                <div class="fe-comment-authorimage"><?php if ($fe_gravatar) { echo $fe_gravatar; } ?></div>
            <?php endif; ?>

            <div class="fe-comment-content">
                <p class="fe-comment-text"><?php echo get_comment_text(); ?></p>

                <div class="fe-comment-authorinfo fe-sans">
                    <p class="fe-comment-authorname mb-0 fw-bold">- <?php echo get_comment_author(); ?></p>
                    <!-- <p class="fe-comment-date"><?php echo get_comment_date(); ?></p> -->
                </div>
            </div>

            <div class="fe-comment-reply">
                    <?php    
                        comment_reply_link( [
                            'add_below' => true,
                            'depth'     => 20,
                            'max_depth' => 200,
                            'before'    => '<div class="reply">',
                            'after'     => '</div>'
                        ] ); 
                    ?>
            </div>
        </div>

    </li>

<?php }

add_filter( 'get_comment_author_link', 'fe_remove_comment_author_link', 10, 3 );
function fe_remove_comment_author_link( $return, $author, $comment_ID ) {
	return $author;
}

function fe_remove_comments_field( $fields ) {
	unset( $fields['url'] );
    $fields['cookies'] = '';
	return $fields;
}
add_filter( 'comment_form_default_fields', 'fe_remove_comments_field' );

/**
 * Filter to move comment field to the last
 */
add_filter( 'comment_form_fields', 'fe_comment_form_fields', 10, 1 );

/**
 * Move comment field to the last
 * @param $comment_fields
 * @return mixed
 */
function fe_comment_form_fields($comment_fields) {
    if (isset($comment_fields['comment'])) {
        $comment_field = $comment_fields['comment'];
        unset($comment_fields['comment']);
        $comment_fields['comment'] = $comment_field;
    }

    return $comment_fields;
}

function custom_excerpt_length($length){
    return 20;
}
add_filter('excerpt_length', 'custom_excerpt_length');

/**
 * This function modifies the main WordPress query to remove 
 * pages from search results.
 *
 * @param object $query The main WordPress query.
 */
function _exclude_pages_from_search_results( $query ) {
    if ( $query->is_main_query() && $query->is_search() && ! is_admin() ) {
        $query->set( 'post_type', array( 'post' ) );
    }    
}
add_action( 'pre_get_posts', '_exclude_pages_from_search_results' );

function mailing_actions( $post ) {

    $done = @$_GET['done'];

    // Show only for published pages.
    if ( ! $post
         || 'publish' !== $post->post_status
         || 'design' !== $post->post_type ) {
        return;
    }

    $store = get_field('store', $post->ID);

    $html = '<div class="emailing-loader" style="display:none;"></div><div id="emailing-actions">';
        
        if(get_field('status', $store->ID) == 'complete_info'){
            $html .= '<span class="notice notice-success">'.__('La propuesta de tienda ya ha sido enviada al cliente para la revisión y posterior aprobación.', 'xopifier').'</span>';
        }
        if(get_field('status', $store->ID) == 'approved_design') {
            $html .= '<span class="notice notice-success">'.__('La propuesta', 'xopifier').' <b>'.get_field('approved-design', $store->ID).'</b> '.__('ya ha sido aprobado por el cliente.', 'xopifier').'</span>';
        }

        if(get_field('status', $store->ID) != 'approved_design') {
            $html .= '<div id="email-action">';
                if(get_field('status', $store->ID) == 'complete_info'){
                    $html .= '<input type="submit" accesskey="p" tabindex="5" value="'.__('Reenviar email al cliente', 'xopifier').'" class="button-primary" id="send-email-button" name="send-email-button">';
                }else{
                    $html .= '<input type="submit" accesskey="p" tabindex="5" value="'.__('Enviar email al cliente', 'xopifier').'" class="button-primary" id="send-email-button" name="send-email-button">';
                }
                if($done){
                    $html .= '<span class="email-action-success">'.__('Email enviado correctamente!!!', 'xopifier').'</span>';
                }
            $html .= '</div>';
        }
    $html .= '</div>';

    // Customize button click event.
    ob_start(); ?>
    <style>
        #emailing-actions{
            margin: 20px 10px;
            text-align: left;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        #emailing-actions .notice{
            padding: 10px 10px 10px 10px;
            margin: 0;
        }
        #email-action{
            display: flex;
            flex-direction: row;
            justify-content: flex-start;
            align-items: center;
        }
        #email-action input{
            margin-top: 20px !important;
        }
        .emailing-loader{
            position: fixed;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            background: rgba(255,255,255,.2);
            backdrop-filter: blur(10px);
            z-index: 99999;
        }

        .emailing-loader::after{
            position: absolute;
            background: url('../wp-content/themes/tiendas/img/spinner.gif') center center no-repeat;
            background-size: contain;
            content: "";
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            width: 50px;
            height: 50px;
        }
        .email-action-success{
            font-weight: bold;
            color: green;
            margin-left: 15px;
        }
    </style>
    <script>
        window.addEventListener('load', function() {
            jQuery('input#send-email-button').click(function(event) {
                event.preventDefault();
                var actionUrl = "<?php echo admin_url('admin-ajax.php')?>";

                jQuery('.emailing-loader').fadeIn();

                jQuery.post(actionUrl, {
                    action: 'ws',
                    wsa: 'send_design_email',
                    designID: <?php echo $post->ID; ?>,
                }, function(response){
                    jQuery('.emailing-loader').fadeOut();
                    if(response.error == false){
                        window.location = "<?php echo admin_url('post.php?post='.$post->ID.'&action=edit&done=1')?>";
                    }else{
                        alert(response.message);
                    }
                });
            });
        });
    </script>
    <?php $html .= ob_get_clean();

    echo $html;
}

function adding_mailing_actions_meta_boxes( $post_type, $post ) {
    add_meta_box( 
        'mailing-actions',
        __( 'Acciones de envio de emails', 'xopifier' ),
        'mailing_actions',
        'design',
        'normal',
        'default'
    );
}
add_action( 'add_meta_boxes', 'adding_mailing_actions_meta_boxes', 10, 2 );

function in_array_r($needle, $haystack, $strict = false) {
    foreach ($haystack as $item) {
        if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && in_array_r($needle, $item, $strict))) {
            return true;
        }
    }

    return false;
}

add_filter('woocommerce_checkout_fields', 'add_bootstrap_class_to_checkout_fields' );
function add_bootstrap_class_to_checkout_fields($fields) {
    foreach ($fields as &$fieldset) {
        foreach ($fieldset as &$field) {
            // if you want to add the form-group class around the label and the input
            $field['class'][] = 'form-group'; 
            $field['class'][] = 'w-100'; 

            // add form-control to the actual input
            $field['input_class'][] = 'form-control';
            $field['input_class'][] = 'w-100';
        }
    }
    return $fields;
}

// add_action('woocommerce_cart_calculate_fees', 'add_initial_discounts');
function add_initial_discounts($cart) {
    if ( is_admin() && ! defined( 'DOING_AJAX' ) )
        return;

    if ( is_cart() )
        return;

    $items = WC()->cart->get_cart();
    $is_initial_step = false;

    foreach($items as $item){
        $product_type = get_post_meta($item['product_id'], 'product-type', true);
        if($product_type == 'advanced-payment'){
            $is_initial_step = true;
            break;
        }
    }

    if(!$is_initial_step)
        return;

    $service_settings = get_field('service_settings', 'option');
    $initial_price = $service_settings['base_services_price_percent'];
    $subtotal = (float) WC()->cart->subtotal;
    $discount = $subtotal - $initial_price;
        
    $cart->add_fee(__('Este monto lo pagas al entregarte la tienda', 'xopifier'), -$discount);
}

add_filter('woocommerce_form_field_args','wc_form_field_args',10,3);
function wc_form_field_args( $args, $key, $value = null ) {

    /*********************************************************************************************/
    /** This is not meant to be here, but it serves as a reference
    /** of what is possible to be changed. */
    $defaults = array(
        'type'              => 'text',
        'label'             => '',
        'description'       => '',
        'placeholder'       => '',
        'maxlength'         => false,
        'required'          => false,
        'id'                => $key,
        'class'             => array(),
        'label_class'       => array(),
        'input_class'       => array(),
        'return'            => false,
        'options'           => array(),
        'custom_attributes' => array(),
        'validate'          => array(),
        'default'           => '',
    );

    $args = wp_parse_args( $args, $defaults  );
    /*********************************************************************************************/

    // Start field type switch case

    switch ( $args['type'] ) {

        case "select" :  /* Targets all select input type elements, except the country and state select input types */
            $args['class'][] = 'form-group'; // Add a class to the field's html element wrapper - woocommerce input types (fields) are often wrapped within a <p></p> tag  
            $args['input_class'] = array('form-control', 'input-lg'); // Add a class to the form input itself
            $args['custom_attributes']['data-plugin'] = 'select2';
            $args['label_class'] = array('form-label');
            $args['custom_attributes'] = array( 'data-plugin' => 'select2', 'data-allow-clear' => 'true', 'aria-hidden' => 'true',  ); // Add custom data attributes to the form input itself
        break;

        case 'country' : /* By default WooCommerce will populate a select with the country names - $args defined for this specific input type targets only the country select element */
            $args['class'][] = 'form-group single-country';
            $args['label_class'] = array('form-label');
        break;

        case "state" : /* By default WooCommerce will populate a select with state names - $args defined for this specific input type targets only the country select element */
            $args['class'][] = 'form-group'; // Add class to the field's html element wrapper 
            $args['input_class'] = array('form-control', 'input-lg'); // add class to the form input itself
            $args['custom_attributes']['data-plugin'] = 'select2';
            $args['label_class'] = array('form-label');
            $args['custom_attributes'] = array( 'data-plugin' => 'select2', 'data-allow-clear' => 'true', 'aria-hidden' => 'true',  );
        break;


        case "password" :
        case "text" :
        case "email" :
        case "tel" :
        case "number" :
            $args['class'][] = 'form-group';
            $args['input_class'][] = 'form-control input-lg'; // will return an array of classes, the same as bellow
            $args['input_class'] = array('form-control', 'input-lg');
            $args['label_class'] = array('form-label');
        break;

        case 'textarea' :
            $args['input_class'] = array('form-control', 'input-lg');
            $args['label_class'] = array('form-label');
        break;

        case 'checkbox' :  
        break;

        case 'radio' :
        break;

        default :
            $args['class'][] = 'form-group';
            $args['input_class'] = array('form-control', 'input-lg');
            $args['label_class'] = array('form-label');
        break;
    }

    return $args;
    
    // only on the checkout page form
    // if ( !is_page('checkout') ) {
    //   add_filter('woocommerce_form_field_args','wc_form_field_args', 10, 3);
    // } else {
    //   remove_filter('woocommerce_form_field_args','wc_form_field_args', 10, 3);
    // }
}

function autoloadJS($directory) {
    
    // Check if the directory exists
    if (!is_dir($directory)) {
        throw new Exception("Directory does not exist: $directory");
    }

    $theme_url = get_template_directory_uri();

    // Scan the directory for files and subdirectories
    $files = scandir($directory);
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') {
            continue; // Skip current and parent directory entries
        }

        $filePath = $directory . '/' . $file;

        if (is_dir($filePath)) {
            // If it's a directory, call the function recursively
            autoloadJS($filePath);
        } elseif (pathinfo($filePath, PATHINFO_EXTENSION) === 'js') {
            // If it's a JS file, include it

            $path = explode("..", $filePath);

            wp_register_script($file, $theme_url . $path[1] . '?v='.time(), array('jquery'), '', true);
            wp_enqueue_script($file);
        }
    }
}

function autoloadPHP($directory) {
    // Check if the directory exists
    if (!is_dir($directory)) {
        throw new Exception("Directory does not exist: $directory");
    }

    // Scan the directory for files and subdirectories
    $files = scandir($directory);
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') {
            continue; // Skip current and parent directory entries
        }

        $filePath = $directory . '/' . $file;
        if (is_dir($filePath)) {
            // If it's a directory, call the function recursively
            autoloadPHP($filePath);
        } elseif (pathinfo($filePath, PATHINFO_EXTENSION) === 'php') {
            // If it's a PHP file, include it
            require_once $filePath;
        }
    }
}

function truncateString($str, $maxLength) {
    // Check if the string is longer than the maximum length
    if (strlen($str) <= $maxLength) {
        return $str; // Return the original string if it's within the limit
    }

    // Calculate the number of characters to keep from the start and end
    $keepChars = floor(($maxLength - 3) / 2); // 3 for the ellipsis

    // Create the truncated string
    $truncated = substr($str, 0, $keepChars) . '...' . substr($str, -$keepChars);

    return $truncated;
}

// if you don't add 3 as as 4th argument, this will not work as expected
add_action( 'save_post', 'reset_approved_design_id', 10, 3 );

function reset_approved_design_id( $post_ID, $post, $update ) {
    $status = get_field('status', $post_ID);
    if($status == 'pending' || $status == 'complete_info' || $status == 'declined_design' || $status == 'review_info'){
        update_field('approved-design-id', 0, $post_ID);
    }
}

global $allowed_pages;

$allowed_pages= array(
    'paso-2',
    'paso-3',
    'paso-4',
    'paso-5',
    'paso-2-completado',
    'mi-cuenta'
);

add_action('wp_head', 'check_user_store_status');
function check_user_store_status(){
    global $post;
    global $designId;
    global $allowed_pages;

    if(!is_user_logged_in()){
        $designId = 0;
        if($post->post_name != 'mi-cuenta' && in_array($post->post_name, $allowed_pages)){
            echo '<script>window.location = "'.site_url('/mi-cuenta/').'"</script>';
            exit;
        }
    }else{
        $userId = get_current_user_id();
        if(in_array($post->post_name, $allowed_pages)){

            if(current_user_can('manage_options')){
                echo "<script>window.location = '".site_url('/wp-admin/')."';</script>";
                exit;
            }

            $storeStatus = '';
            $designId = 0;
            $userStore = get_posts(array('post_type' => 'initial-stage', 'post_status' => 'publish', 'meta_key' => 'user', 'meta_value' => $userId));

            if(is_array($userStore) and count($userStore) > 0){
                $storeStatus = get_field('status', $userStore[0]->ID);
                $designs = get_posts(array('post_status' => 'published', 'post_type' => 'design', 'meta_key' => 'store', 'meta_value' => $userStore[0]->ID));
                if(is_array($designs) and count($designs) > 0){
                    $designId = $designs[0]->ID;
                }
            }

            if(in_array($post->post_name, $allowed_pages)){
                if($storeStatus == 'complete_info'){
                    if($post->post_name != 'paso-2' && $post->post_name != 'paso-1'){
                        echo '<script>window.location = "'.apply_filters( 'wpml_permalink', site_url('/paso-2'), ICL_LANGUAGE_CODE ).'"</script>';
                        exit;
                    }
                }elseif($storeStatus == 'review_info'){
                    if($post->post_name != 'paso-1'){
                        echo '<script>window.location = "'.apply_filters( 'wpml_permalink', site_url('?msg=5'), ICL_LANGUAGE_CODE ).'"</script>';
                        exit;
                    }
                }elseif($storeStatus == 'design_sent'){
                    if($post->post_name != 'paso-1' && $post->post_name != 'paso-3'){
                        echo '<script>window.location = "'.apply_filters( 'wpml_permalink', site_url('/paso-3'), ICL_LANGUAGE_CODE ).'"</script>';
                        exit;
                    }
                }elseif($storeStatus == 'pending'){
                    if($post->post_name != 'paso-1' && $post->post_name != 'paso-1-completado' && $post->post_name != 'paso-1-1-completado'){
                        echo '<script>window.location = "'.apply_filters( 'wpml_permalink', site_url('?msg=0'), ICL_LANGUAGE_CODE ).'"</script>';
                        exit;
                    }
                }elseif($storeStatus == 'approved_design'){
                    if($post->post_name != 'paso-3' && $post->post_name != 'paso-1' && $post->post_name != 'paso-2-completado'){
                        echo '<script>window.location = "'.apply_filters( 'wpml_permalink', site_url('/paso-3'), ICL_LANGUAGE_CODE ).'"</script>';
                        exit;
                    }
                }elseif($storeStatus == 'store_in_review'){
                    if($post->post_name != 'paso-4' && $post->post_name != 'paso-1'){
                        echo '<script>window.location = "'.apply_filters( 'wpml_permalink', site_url('/paso-4'), ICL_LANGUAGE_CODE ).'"</script>';
                        exit;
                    }
                }elseif($storeStatus == 'declined_design'){
                    if($post->post_name != 'paso-1'){
                        echo '<script>window.location = "'.apply_filters( 'wpml_permalink', site_url('?msg=3'), ICL_LANGUAGE_CODE ).'"</script>';
                        exit;
                    }
                }
            }
        }
    }
}

function get_current_step($userId){
    $storeStatus = '';
    $userStore = get_posts(array('post_type' => 'initial-stage', 'post_status' => 'publish', 'meta_key' => 'user', 'meta_value' => $userId));
    if(is_array($userStore) and count($userStore) > 0){
        $storeStatus = get_field('status', $userStore[0]->ID);
    }

    return $storeStatus;
}

function is_admin_user($user_id){
    $user_meta = get_userdata($user_id);
    $user_roles = $user_meta->roles;
    return in_array('administrator', $user_roles);
}

add_action('wp_login', 'redirect_to_step', 10, 2);
function redirect_to_step($user_login, $user){

    if(is_admin_user($user->ID)){
        echo '<script>window.location = "'.site_url('/wp-admin/').'"</script>';
        exit;
    }

    $storeStatus = get_current_step($user->ID);

    if($storeStatus == 'payment_pending'){
        echo '<script>window.location = "'.apply_filters( 'wpml_permalink', site_url('?msg=4'), ICL_LANGUAGE_CODE ).'"</script>';
    }elseif($storeStatus == 'complete_info'){
        echo '<script>window.location = "'.apply_filters( 'wpml_permalink', site_url('/paso-2/'), ICL_LANGUAGE_CODE ).'"</script>';
        exit;
    }elseif($storeStatus == 'pending'){
        echo '<script>window.location = "'.apply_filters( 'wpml_permalink', site_url('?msg=0'), ICL_LANGUAGE_CODE ).'"</script>';
        exit;
    }elseif($storeStatus == 'approved_design'){
        echo '<script>window.location = "'.apply_filters( 'wpml_permalink', site_url('/paso-3/'), ICL_LANGUAGE_CODE ).'"</script>';
        exit;
    }elseif($storeStatus == 'store_in_review'){
        echo '<script>window.location = "'.apply_filters( 'wpml_permalink', site_url('/paso-4/'), ICL_LANGUAGE_CODE ).'"</script>';
        exit;
    }elseif($storeStatus == 'declined_design'){
        echo '<script>window.location = "'.apply_filters( 'wpml_permalink', site_url('?msg=3'), ICL_LANGUAGE_CODE ).'"</script>';
        exit;
    }else{
        echo '<script>window.location = "'.apply_filters( 'wpml_permalink', site_url('?msg=0'), ICL_LANGUAGE_CODE ).'"</script>';
        exit;
    }
}

add_action('wp_footer', 'add_site_version_tags');
function add_site_version_tags() {
    echo '<input type="hidden" id="site_version" name="site_version" value="'.get_field('site_version', 'option').'" />';
}

add_action('woocommerce_order_status_changed', 'verify_order_status', 10, 3);
function verify_order_status($order_id, $old_status, $new_status){
    $order = wc_get_order($order_id);
    
    if($new_status == 'completed'){
        foreach($order->get_items() as $item){
            $storeId = get_post_meta($item['product_id'], 'product-store-id', true);
            update_field('status', 'pending', $storeId);
        }
    }

    if($new_status == 'on-hold' || $new_status == 'pending' || $new_status == 'processing' || $new_status == 'failed'){
        foreach($order->get_items() as $item){
            $storeId = get_post_meta($item['product_id'], 'product-store-id', true);
            update_field('status', 'payment_pending', $storeId);
        }
    }

    if($new_status == 'on-refunded' || $new_status == 'cancelled'){
        foreach($order->get_items() as $item){
            $storeId = get_post_meta($item['product_id'], 'product-store-id', true);
            update_field('status', 'cancelled', $storeId);
        }
    }
}

function get_field_wpml( $field_key, $post_id = false, $format_value = true ) {

    // see : http://support.advancedcustomfields.com/forums/topic/wpml-and-acf-options/

    global $sitepress;

    $is_cascade   = $post_id == 'option' && $format_value == true ? true : false;
    $format_value = $post_id == 'option' ? true : $format_value; // force $format_value = true for option

    // get field for default language
    if ( ( $sitepress->get_default_language() == ICL_LANGUAGE_CODE ) && ( $ret = get_field( $field_key, $post_id, $format_value ) ) ) {
       return $ret;
    }

    // get field for current language
    elseif ( $ret = get_field( $field_key . '_' . ICL_LANGUAGE_CODE, $post_id, $format_value ) ) {
        return $ret;
    }

    // get field when if not exists for locale by cascade
    elseif ( $is_cascade ) {
        return get_field( $field_key, $post_id, $format_value );
    }

    return false;
}

add_filter('woocommerce_available_payment_gateways', '_payment_gateways_by_country', 10, 1);
function _payment_gateways_by_country($available_gateways) {
    if(WC() != null && WC()->customer != null){
        $country = WC()->customer->get_billing_country();
        
        if ($country == 'US' || $country == 'MX') {
            unset($available_gateways['woo-mercado-pago-basic']); // Oculta Mercado Pago en Mexico y USA
        } else {
            unset($available_gateways['stripe']); // Oculta Stripe en paises que no sean Mexico y USA    
        }
    }
    return $available_gateways;
}

function get_google_image_file_name($image_content){
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime_type = $finfo->buffer($image_content);

    switch($mime_type) {
        case 'image/jpeg':
            $extension = 'jpg';
            break;
        case 'image/png':
            $extension = 'png';
            break;
        case 'image/gif':
            $extension = 'gif';
            break;
        default:
            $extension = 'unknown';
    }

    return 'google-image-'.time().'.'.$extension;
}
<!doctype html>
<html <?php language_attributes(); ?>>
    <head>
        <meta charset="<?php bloginfo( 'charset' ); ?>" /> 
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title><?php wp_title('|', true, 'right'); ?></title>
        <link rel="profile" href="http://gmpg.org/xfn/11" />
        <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
        
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no, user-scalable=no">

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Red+Hat+Text:ital,wght@0,300..700;1,300..700&display=swap" rel="stylesheet">

        <?php wp_head(); ?> 
    </head>

    <?php 
        $scrolled = !is_front_page() ? 'innerpage' : 'homepage scrolled'; 
        $post_name = is_singular() ? get_post_field('post_name', get_post()) : '';
    ?>
    
    <body <?php body_class('blur ' . (is_front_page() ? '' : get_post_type()) . ' ' . esc_attr($post_name) . ' page-' . esc_attr($post_name)); ?>>
        
        <div class="overlay-bg"></div>

        <div class="container p-0 position-relative">

            <section id="header" class="topheader topheader-steps row <?php echo esc_attr($scrolled); ?>">
                <div class="col-12 px-0">
                    <div class="mainbar mainbar-steps d-flex align-items-center justify-content-end p-0">
                        <div class="row m-0">
                            <div class="flex-row d-flex col-12 align-items-center justify-content-between px-0">
                                <a class="logo-link logo-color" href="<?php echo esc_url(home_url('/')); ?>">
                                    <?php 
                                        $logo_data = get_field('logo', 'option');
                                        $logo = isset($logo_data['url']) ? esc_url($logo_data['url']) : '';
                                    ?>
                                    <?php if(!empty($logo)): ?>
                                        <img src="<?php echo $logo; ?>" alt="logo" class="logo">
                                    <?php endif; ?>
                                </a>     
                            </div>
                        </div> 
                    </div>
                </div> 
            </section>

        </div>

        <div class="blur-section container-fluid <?php echo is_search() ? 'search-page' : ''; ?> <?php echo esc_attr($post_name); ?> p-0">
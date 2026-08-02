<!doctype html>
<html>
    <head>
        <base href="<?php wp_title() ?>/" >
        <meta charset="<?php bloginfo( 'charset' ); ?>" /> 
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title><?php wp_title()?></title>
        <link rel="profile" href="http://gmpg.org/xfn/11" />
        <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
        
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no, user-scalable=no">

        <!-- <script src="https://kit.fontawesome.com/c860b1bf5a.js" crossorigin="anonymous"></script> -->

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Red+Hat+Text:ital,wght@0,300..700;1,300..700&display=swap" rel="stylesheet">

        <?php  wp_head();  ?> 
        
        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>

    <?php 
        $scrolled = 'homepage'; 
    ?>
    
    <body <?php body_class('blur homepage '.@$post->post_name.' page-'.@$post->post_name.' show-topbar')?>>
        
        <div class="overlay-bg"></div>

        <div class="container-fluid p-0 position-relative">

            <section id="topbar" class="row d-flex align-items-center justify-content-center">
                <div class="col-12 d-flex align-items-center justify-content-center">
                    <?php if(ICL_LANGUAGE_CODE == 'es'):?>
                        <p>Prefer English? - <a href="<?php echo apply_filters( 'wpml_permalink', get_the_permalink( @$post->ID ), 'en' )?>">View Xopifier in English</a></p>
                    <?php else:?>
                        <p>¡Se habla español! - <a href="<?php echo apply_filters( 'wpml_permalink', get_the_permalink( @$post->ID ), 'es' )?>">Ver Xopifier en español</a></p>
                    <?php endif;?>
                </div>
            </section>

            <section id="header" class="topheader row <?php echo $scrolled?>">
                <div class="col-xxl-12 col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                    <div class="mainbar container d-flex align-items-center justify-content-end p-0">
                        <div class="row m-0">
                            <div id="home-message-container" class="flex-row d-flex col-12 align-items-center justify-content-between px-0">
                                <a class="logo-link logo-color" data-aos="fade-right" href="<?php echo home_url();?>" id="message-popover" data-bs-placement="bottom" data-bs-container="#home-message-container" data-bs-toggle="popover" data-bs-content="">
                                    <?php $logo = get_field('logo', 'option')['url'];?>
                                    <?php $logo_white = get_field('logo_white', 'option')['url'];?>
                                    <img src="<?php echo $logo?>" class="logo-black" alt="logo">
                                    <img src="<?php echo $logo_white?>" class="logo-white" alt="logo">
                                </a>     
                                <div class="menu-inner ms-auto me-0 d-flex align-items-center" data-aos="fade-left">
                                    <?php wp_nav_menu(array('theme_location' => 'primary', 'menu_class' => 'mainmenu desktop', 'container' => false)); ?>
                                    <?php if(is_user_logged_in()):?>
                                        <?php if(current_user_can('manage_options')):?>
                                            <a href="<?php echo site_url('wp-admin')?>" class="btn-login ms-4"><?php _e('Mi Xopifier', 'xopifier')?></a>
                                        <?php else:?>
                                            <a href="<?php echo apply_filters( 'wpml_permalink', site_url('mi-cuenta'), ICL_LANGUAGE_CODE )?>" class="btn-login ms-4"><?php _e('Mi Xopifier', 'xopifier')?></a>
                                        <?php endif;?>
                                    <?php else:?>
                                        <a href="<?php echo apply_filters( 'wpml_permalink', site_url('mi-cuenta'), ICL_LANGUAGE_CODE )?>" class="btn-login ms-4"><?php _e('Login', 'xopifier')?></a>
                                    <?php endif;?>
                                    <a href="<?php echo apply_filters( 'wpml_permalink', site_url('paso-1'), ICL_LANGUAGE_CODE )?>" class="btn btn-primary ms-4 px-4 py-2"><?php _e('Comienza ahora', 'xopifier')?></a>
                                </div>
                                <div class="burger">
                                    <span></span>
                                    <span></span>
                                    <span></span>
                                </div>
                            </div>
                        </div> 
                    </div>
                </div> 
            </section>

            <section id="mobile-menu" class="row mainbar">
                <div class="col-12 d-flex align-items-start justify-content-start">
                    <div class="container mainmenu-container">
                        <div class="col-12">
                            <!-- <i class="fas fa-arrow-right"></i> -->
                            <div class="icon-close-menu on">
                                <span></span>
                                <span></span>
                                <span></span>
                            </div>
                            <?php wp_nav_menu(array('theme_location' => 'primary', 'menu_class' => 'mainmenu mobile', 'container' => false)); ?>  
                            <ul class="d-flex flex-column mainmenu">
                                <?php if(is_user_logged_in()):?>
                                    <?php if(current_user_can('manage_options')):?>
                                        <li>
                                            <a href="<?php echo site_url('wp-admin')?>" class="btn-login"><?php _e('Mi Xopifier', 'xopifier')?></a>
                                        </li>
                                    <?php else:?>
                                        <li>
                                            <a href="<?php echo apply_filters( 'wpml_permalink', site_url('mi-cuenta'), ICL_LANGUAGE_CODE )?>" class="btn-login"><?php _e('Mi Xopifier', 'xopifier')?></a>
                                        </li>
                                    <?php endif;?>
                                <?php else:?>
                                    <li>
                                        <a href="<?php echo apply_filters( 'wpml_permalink', site_url('mi-cuenta'), ICL_LANGUAGE_CODE )?>" class="btn-login"><?php _e('Login', 'xopifier')?></a>
                                    </li>
                                <?php endif;?>
                                <li class="h-auto overflow-visible">
                                    <a href="<?php echo apply_filters( 'wpml_permalink', site_url('paso-1'), ICL_LANGUAGE_CODE )?>" class="btn btn-primary my-4 px-5 py-3"><?php _e('Comienza ahora', 'xopifier')?></a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </section>

        </div>

        <div class="blur-section container-fluid <?php echo is_search() ? 'search-page' : ''?> <?php echo @$post->post_name?> p-0">
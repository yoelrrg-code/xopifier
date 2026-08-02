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
    
    <body <?php body_class('blur '.(is_front_page() ? '' : get_post_type()).' '.@$post->post_name.' page-'.@$post->post_name)?>>

        <?php 
            global $designId;
            
            $scrolled = !is_front_page() ? 'innerpage' : 'homepage scrolled'; 
            if(isset($designId) && $designId != 0 && $designId != ''){
                $design_id = $designId;
                $store = get_field('store', $design_id);
            }elseif($designId == 0 || $designId = ''){
                $userid = get_current_user_id();
                $store = get_posts(array(
                    'post_type' => 'initial-stage',
                    'post_status' => 'publish',
                    'posts_per_page' => 1,
                    'meta_query' => array(
                        array(
                            'key' => 'user',
                            'value' => $userid,
                            'compare' => '='
                        )
                    )
                ));
                $store = $store[0];
                // var_dump($store);
            }else{
                echo "<script>window.location='".site_url(ICL_LANGUAGE_CODE)."?msg=1';</script>";
            }
        ?>
        
        <div class="overlay-bg"></div>

        <div class="container-fluid p-0 position-relative">

            <div id="popup-resume-container">
                <?php echo popup_resume($designId)?>
            </div>

            <section id="header" class="topheader topheader-steps row <?php echo $scrolled?>">
                <div class="col-12 px-md-5 px-sm-2 px-2">
                    <div class="container px-0">
                        <div class="row">
                            <div class="col-12">
                                <div class="mainbar mainbar-steps d-flex align-items-center justify-content-end p-0">
                                    <div class="row m-0">
                                        <div class="flex-row d-flex col-12 align-items-center justify-content-between px-md-0 px-sm-4 px-4">
                                            <a class="logo-link logo-simple" href="<?php echo home_url(ICL_LANGUAGE_CODE.'/paso-3/');?>">
                                                <?php 
                                                    if(get_field('current_store_logo', $store->ID) != ''){
                                                        $logo = get_field('current_store_logo', $store->ID)['url'];
                                                    }else{
                                                        $logo = get_field('logo_simple', 'option')['url'];
                                                    }
                                                ?>
                                                <img src="<?php echo $logo?>" alt="logo" class="">
                                                <?php if(isset($store->ID)):?>
                                                    <span><?php _e('Tienda', 'xopifier')?> <?php echo get_field('current_store_name', $store->ID)?></span>
                                                <?php else:?>
                                                    <span><?php _e('NO DEFINIDA', 'xopifier')?></span>
                                                <?php endif;?>
                                            </a>     
                                            <a class="logo-link logo-right btn-open-sidebar" href="#" id="howto-popover" data-bs-toggle="popover" data-bs-container="#message-container" data-bs-trigger="hover" data-bs-placement="bottom" data-bs-content="<?php _e('Haz clic aquí para ver el resumen de tu tienda.', 'xopifier')?>">
                                                <?php $logo = get_field('logo_simple', 'option')['url'];?>
                                                <span class="mi-xopifier"><?php _e('Mi Xopifier', 'xopifier')?></span>
                                                <img src="<?php echo $logo?>" alt="logo" class="">
                                            </a>  
                                        </div>
                                    </div> 
                                </div>
                            </div>
                        </div>
                    </div>
                </div> 
            </section>

            <?php generate_progress_dots();?>

            <div id="message-container" class="container"></div>

        </div>

        <div class="blur-section container-fluid <?php echo is_search() ? 'search-page' : ''?> <?php echo @$post->post_name?> p-0">
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

            if(isset($designId)){
                $design_id = $designId;
                $store = get_field('store', $design_id);
            }
        ?>
        
        <div class="overlay-bg"></div>

        <div class="container p-0 position-relative">

            <section id="header" class="topheader topheader-steps row <?php echo $scrolled?>">
                <div class="col-12 px-0">
                    <div class="mainbar mainbar-steps d-flex align-items-center justify-content-end p-0">
                        <div class="row m-0">
                            <div class="flex-row d-flex col-12 align-items-center justify-content-center px-0 pt-5">
                                <a class="logo-link logo-color" href="<?php echo home_url();?>">
                                    <?php $logo = get_field('logo', 'option')['url'];?>
                                    <img src="<?php echo $logo?>" alt="logo" class="logo larger">
                                </a>     
                            </div>
                        </div> 
                    </div>
                </div> 
            </section>

        </div>

        <div class="blur-section container-fluid <?php echo is_search() ? 'search-page' : ''?> <?php echo @$post->post_name?> p-0">
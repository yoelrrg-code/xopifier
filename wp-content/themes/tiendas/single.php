<?php get_header(); ?>

<div class="inner-pages single-blog pt-5">

    <div class="overlay"></div>
    
    <?php while ( have_posts() ) : the_post();?>

        <div id="page-title" class="container">

            <div class="row pt-5 pb-lg-5 pb-md-3 pb-sm-3 pb-3 justify-content-start">
                <div class="main-content pt-3 col-xl-8 col-lg-8 col-md-12 col-sm-12 col-12">
                    
                    <?php get_template_part( 'template-parts/content', get_post_format() );?>

                    <?php 

                        _single_post_nav();

                        _related_posts();

                        // If comments are open or there is at least one comment, load up the comment template.
                        if ( comments_open() || get_comments_number() ) {
                            comments_template();
                        }
                    ?>

                </div>

                <div class="sidebar col-xl-4 col-lg-4 col-md-12 col-sm-12 col-12 ps-xl-5 ps-lg-5 ps-md-4 ps-sm-auto ps-auto">
                    <div class="sticky-top">
                        <?php dynamic_sidebar( 'sidebar-blog' )?>
                    </div>
                </div>
            </div>

        </div>
        
    <?php endwhile;?>
</div>

<?php get_footer(); ?>  
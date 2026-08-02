<?php
/*
Template Name: Archive Portfolio
*/
?>

<?php get_header(); $description = get_the_archive_description();?>

<div class="inner-pages blog archive-portfolio pb-md-4 pb-sm-0 pb-0 mt-md-5 pt-mb-0 pt-sm-1 pt-1 mt-sm-4 mt-4">

    <div id="page-title" class="container-fluid mt-5">

        <div class="row mb-3 justify-content-center">
            <div class="mt-3 col-xl-5 col-lg-6 col-md-8 col-sm-12 col-12 text-center">
                <?php the_archive_title( '<h1 class="d-none">', '</h1>' );?>
                <?php the_archive_title( '<h2 class="mb-lg-5 text-dark">', '</h2>' );?>
                <?php if ( $description ) : ?>
                    <p class="desc mt-lg-5 mt-md-3 mt-sm-3 mt-3"><?php echo wp_kses_post( wpautop( $description ) ); ?></p>
                <?php endif; ?>
            </div>
        </div>

    </div>

    <div id="page-title" class="container">

        <div class="row mb-md-5 mb-sm-0 mb-0 pb-md-3 pb-sm-0 pb-0 justify-content-start">
            <div class="col-12">
                
                <div id="portfolio-grid-masonry" class="row portfolio-grid-style2">
                    <?php while ( have_posts() ) : the_post();?>
                        <?php get_template_part( 'template-parts/content-portfolio', get_post_format() );?>
                    <?php endwhile;?>
                </div>

            </div>
        </div>

    </div>
</div>

<?php get_footer(); ?>  
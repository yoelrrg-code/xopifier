<?php
/*
Template Name: Plantilla Woo
*/
?>

<?php get_header('wc-normal'); ?>

<div class="inner-pages">
    <?php while ( have_posts() ) : the_post();?>

    <div id="steps" class="my-account">
        <div class="step">
            <div class="sub-step">
                <div class="container price-box px-0">
                    <div class="row box">
                        <div class="bordered-box col-12 pb-3">
                        
                        <?php the_content(); ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
        
    <?php endwhile;?>
</div>

<?php get_footer('step2'); ?>  
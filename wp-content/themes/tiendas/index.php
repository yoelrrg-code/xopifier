<?php
/*
Template Name: Plantilla Generica
*/
?>

<?php get_header('wc'); ?>

<div class="inner-pages">
    <?php while ( have_posts() ) : the_post();?>

        <?php the_content(); ?>
        
    <?php endwhile;?>
</div>

<?php get_footer('step2'); ?>  
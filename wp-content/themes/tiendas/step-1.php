<?php
/*
Template Name: Step 1 template
*/
?>

<?php get_header(); ?>

<div class="inner-pages">
    <?php while ( have_posts() ) : the_post();?>

        <?php the_content(); ?>
        
    <?php endwhile;?>
</div>

<?php get_footer(); ?>  
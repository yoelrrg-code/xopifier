<?php
/*
Template Name: Step 3 template
*/
?>

<?php get_header('step3'); ?>

<div class="inner-pages">
    <?php while ( have_posts() ) : the_post();?>

        <?php the_content(); ?>
        
    <?php endwhile;?>
</div>

<?php get_footer('step2'); ?>  
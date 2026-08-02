<?php
/*
Template Name: Plantilla Interiores
*/
?>

<?php get_header('inner'); ?>

<?php while ( have_posts() ) : the_post();?>

	<?php the_content(); ?>
	
<?php endwhile;?>

<?php get_footer('home'); ?>  
<?php
/*
Template Name: Plantilla Inicio
*/
?>

<?php get_header('home'); ?>

<?php while ( have_posts() ) : the_post();?>

	<?php the_content(); ?>
	
<?php endwhile;?>

<?php get_footer('home'); ?>  
<?php
/*
Template Name: Plantilla Xopifier Generica
*/
?>

<?php get_header('generic'); ?>

<?php while ( have_posts() ) : the_post();?>

	<div id="generic" class="row">
		<div class="col-12">
			
			<div class="container p-0">

				<div class="col-12">

					<h1 class="text-start"><?php the_title()?></h1>

					<?php the_content(); ?>

				</div>
				
			</div>

		</div>
	</div>
	
<?php endwhile;?>

<?php get_footer('home'); ?>  
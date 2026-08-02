
			
			<section id="footer" class="row">
				<div class="col-12">
					<div class="container">
						<div class="row">

							<div class="mb-lg-0 mb-md-0 mb-sm-5 mb-5 col-xl-3 col-lg-3 col-md-3 col-sm-12 col-12 d-flex align-items-lg-center align-items-md-start align-items-sm-start align-items-start justify-content-lg-start justify-content-md-start justify-content-sm-center justify-content-center">
								<img src="<?php echo get_field('logo_footer', 'option')['url']?>" class="img-fluid logo-footer" alt="">
							</div>

							<div class="col-xl-7 col-lg-7 col-md-6 col-sm-12 col-12 d-flex align-items-center justify-content-lg-end justify-content-md-start justify-content-sm-center justify-content-center">
								<?php dynamic_sidebar( 'footer-1' )?>
							</div>

							<div class="mt-md-0 mt-sm-5 mt-5 mb-lg-0 mb-md-0 mb-sm-4 mb-4 col-xl-2 col-lg-2 col-md-3 col-sm-12 col-12 d-flex align-items-lg-center align-items-md-center align-items-sm-start align-items-start justify-content-lg-end justify-content-md-end justify-content-sm-center justify-content-center">
								<ul class="social-media">
									<?php if(is_array(get_field('social_media', 'option')) and count(get_field('social_media', 'option')) > 0):?>
										<?php foreach(get_field('social_media', 'option') as $link):?>
											<li>
												<a class="direct-link" href="<?php echo $link['link']['url']?>" target="<?php echo $link['link']['target']?>">
													<img src="<?php echo $link['icon']['url']?>" alt="">
												</a>
											</li>
										<?php endforeach;?>
									<?php endif;?>
								</ul>
							</div>

							<div class="col-12 d-flex align-items-center justify-content-lg-end justify-content-md-end justify-content-sm-center justify-content-center leyend">
								<?php dynamic_sidebar( 'footer-2' )?>
							</div>

						</div>
						<div class="row">
							<div class="col-12 copyright d-flex flex-md-row flex-sm-column flex-column align-items-center justify-content-between">
								<?php dynamic_sidebar( 'footer-copyright' )?>
								<div class="terms-menu mt-md-0 mt-sm-4 mt-4">
									<?php dynamic_sidebar( 'footer-3' )?>
								</div>
							</div>
						</div>
					</div>
				</div>
		    </section>

		</div>

    	<?php wp_footer(); ?>

    </body>
</html>
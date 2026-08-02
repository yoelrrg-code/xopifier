<div id="inner-hero" class="container-fluid px-0">

    <div class="row">

        <div class="col-12">
            <div class="container px-md-0 px-sm-auto px-auto">
                <div class="row position-relative">
                    <div class="offset-lg-1 offset-md-0 offset-sm-0 offset-0 col-lg-10 col-md-12 col-sm-12 col-12 d-flex flex-column justify-content-center align-items-start text-container">
                        <p data-aos="fade-up" class="title w-100 text-center fw-600 d-block"><?php echo get_field('title')?></p>
                        <h2 data-aos="fade-up" class="subtitle text-center"><?php echo get_field('subtitle')?></h2>
                        <div data-aos="fade-up" class="description mx-xl-5 mx-lg-3 mx-md-2 mx-sm-0 mx-0 px-xl-5 px-lg-3 px-md-2 px-sm-0 px-0"><?php echo get_field('description')?></div>
                        <div data-aos="fade-up" class="w-100 d-flex align-items-center justify-content-center gap-3">
                            <?php if(get_field('button')):?>
                                <a class="btn btn-primary" href="<?php echo get_field('button')['url']?>" target="<?php echo get_field('button')['target']?>" title="<?php echo (get_field('button')['title'])?>">
                                    <?php echo (get_field('button')['title'])?>
                                </a>
                            <?php endif;?>
                        </div>
                    </div>
                    <div data-aos="fade-up" class="col-12 d-flex align-items-center img-container">
                        <img src="<?php echo get_field('image')['url']?>" class="img-fluid" alt="">
                    </div>
                </div>
            </div>
        </div>

	</div>

</div>
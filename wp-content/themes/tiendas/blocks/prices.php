<div id="prices" class="container <?php echo get_field('price_color');?>">
    <div class="row">
        <div class="order-lg-1 order-md-2 order-sm-2 order-2 col-lg-8 col-md-12 col-sm-12 col-12">
            <h2 data-aos="fade-up"><?php echo get_field('title')?></h2>
            <div data-aos="fade-up" class="description">
                <?php echo get_field('description')?>
            </div>
            <?php $time = wp_generate_uuid4();?>
            <div class="accordion accordion-flush" id="accordionFullDesc<?php echo $time?>">
                <div class="accordion-item" data-aos="fade-up">
                    <h2 class="accordion-header" id="heading-<?php echo $time?>">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-<?php echo $time?>" aria-expanded="false" aria-controls="collapse-<?php echo $time?>">
                            <?php echo get_field('full_description_title')?>
                        </button>
                    </h2>
                    <div id="collapse-<?php echo $time?>" class="accordion-collapse collapse" aria-labelledby="heading-<?php echo $time?>" data-bs-parent="#accordionFullDesc<?php echo $time?>">
                        <div class="accordion-body">
                            <?php echo get_field('full_description')?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-5 extra-description">
                <?php if(get_field('extra_description') != ''):?>
                    <?php echo get_field('extra_description')?>
                <?php endif;?>
            </div>
        </div>
        <div class="order-lg-2 order-md-1 order-sm-1 order-1 mb-lg-0 mb-md-5 mb-sm-4 mb-4 col-lg-4 col-md-12 col-sm-12 col-12">
            <?php $price_box = get_field('price_box');?>
            <div data-aos="fade-up" class="price-box">
                <h3><?php echo $price_box['title']?></h3>
                <p class="subtitle"><?php echo $price_box['subtitle']?></p>
                <div class="prices">
                    <?php foreach($price_box['prices'] as $price):?>
                        <p class="price-from"><?php echo $price['from']?></p>
                        <?php if($price['from_sale_price']):?>
                            <p class="d-inline-block price-sale-price"><?php echo $price['from_sale_price']?></p>
                            <p class="d-inline-block price-price">
                                <span class="price"><?php echo $price['from_price']?></span>
                                <span class="launch-price"><?php _e('Precio de lanzamiento', 'xopifier')?></span>
                            </p>
                        <?php else:?>
                            <p class="d-inline-block price-price"><?php echo $price['from_price']?></p>
                        <?php endif;?>
                    <?php endforeach;?>
                </div>
                <p class="description"><?php echo $price_box['description']?></p>
            </div>
            <?php if(get_field('button')):?>
                <div data-aos="fade-up" class="mt-5 d-flex align-items-center justify-content-center">
                    <a class="btn btn-primary w-100" href="<?php echo get_field('button')['url']?>" target="<?php echo get_field('button')['target']?>" title="<?php echo htmlentities(get_field('button')['title'])?>">
                        <?php echo (get_field('button')['title'])?>
                    </a>
                </div>
            <?php endif;?>

            <?php if(get_field('link')):?>
                <div data-aos="fade-up" class="mt-5 d-flex align-items-center justify-content-center">
                    <a class="link fw-semibold" href="<?php echo get_field('link')['url']?>" target="<?php echo get_field('link')['target']?>" title="<?php echo htmlentities(get_field('link')['title'])?>">
                        <?php echo (get_field('link')['title'])?>
                    </a>
                </div>
            <?php endif;?>
        </div>
    </div>
</div>
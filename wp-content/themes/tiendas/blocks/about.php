<div id="about" class="container-fluid px-0">

    <div class="row">

        <div class="col-12">
            
            <?php if(get_field('title') != ''):?>
                <div class="container px-md-0 px-sm-auto px-auto d-flex flex-column justify-content-center align-items-center">
                    <div class="row">
                        <div data-aos="fade-up" class="offset-lg-1 offset-md-0 offset-sm-0 offset-0 col-lg-10 col-md-12 col-sm-12 col-12 text-center">
                            <h2><?php echo get_field('title')?></h2>
                        </div>
                    </div>
                </div>
            <?php endif;?>

            <?php $contents = get_field('contents')?>
            <?php if(is_array($contents) and count($contents) > 0):?>
                <?php foreach($contents as $k => $content):?>
                    <?php if($content['style'] == 2):?>
                        <div class="position-relative style-2 item-<?php echo $k?>">
                            <div class="container-fuild px-md-0 px-sm-auto px-auto d-md-flex d-sm-none d-none flex-column justify-content-center align-items-center">
                                <div <?php if($content['id'] != ''):?>id="<?php echo $content['id']?>"<?php endif;?> class="row py-xl-5 py-lg-3 py-md-0 py-sm-0 mt-5 w-100">
                                    <div data-aos="fade-right" class="order-md-1 order-sm-2 order-2 col-md-6 col-sm-12 col-12 d-flex align-items-center justify-content-end img-container py-md-0 py-sm-4 py-4 mt-md-0 mt-sm-5 mt-5">
                                        <img src="<?php echo $content['image']['url']?>" class="" alt="">
                                    </div>
                                    <div data-aos="fade-left" class="order-md-2 order-sm-1 order-1 col-md-6 col-sm-12 col-12 d-flex flex-column justify-content-center align-items-start text-container px-md-auto px-sm-0 px-0">

                                    </div>
                                </div>
                            </div>
                            <div class="container px-md-0 px-sm-auto px-auto d-flex flex-column justify-content-center align-items-center position-absolute w-100">
                                <div class="row py-xl-5 py-lg-3 py-md-0 py-sm-0 my-5 w-100">
                                    <div class="order-md-2 order-sm-1 order-1 col-md-6 col-sm-12 col-12 d-flex align-items-center justify-content-md-start justify-content-sm-center justify-content-end img-container py-md-0 py-sm-4 py-4 mt-md-0 mt-sm-4 mt-4">
                                        <img src="<?php echo $content['image']['url']?>" class="d-md-none d-sm-block d-block" alt="">
                                    </div>
                                    <div class="order-sm-2 order-2 col-md-6 col-sm-12 col-12 d-flex flex-column justify-content-center align-items-start text-container px-md-auto px-sm-0 px-0">
                                        <p class="fw-bold w-100"><?php echo $content['title']?></p>
                                        <div class="small"><?php echo $content['description']?></div>
                                        <div class="w-100 d-flex flex-lg-row flex-md-column flex-sm-column flex-column align-items-lg-center align-items-md-start align-items-sm-start align-items-center justify-content-start gap-3 mt-lg-5 mt-md-4 mt-sm-4 mt-4">
                                            <?php if(is_array($content['buttons'])):?>
                                                <?php foreach($content['buttons'] as $button):?>
                                                    <a class="btn btn-<?php echo $button['type']?>" href="<?php echo $button['button']['url']?>" target="<?php echo $button['button']['target']?>" title="<?php echo html_entity_decode($button['button']['title'])?>">
                                                        <?php echo html_entity_decode($button['button']['title'])?>
                                                    </a>
                                                <?php endforeach;?>
                                            <?php endif;?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php else:?>
                        <div class="style-1 item-<?php echo $k?> container px-md-0 px-sm-auto px-auto d-flex flex-column justify-content-center align-items-center">
                            <div <?php if($content['id'] != ''):?>id="<?php echo $content['id']?>"<?php endif;?> class="row py-xl-5 py-lg-3 py-md-0 py-sm-0 mt-5">
                                <div class="col-lg-8 col-md-7 col-sm-12 col-12 pe-lg-4 pe-md-3 pe-sm-auto pe-auto d-flex flex-column justify-content-center align-items-start text-container order-md-1 order-sm-2 order-2">
                                    <p class="fw-bold w-100"><?php echo $content['title']?></p>
                                    <div class="small"><?php echo $content['description']?></div>
                                    <div class="w-100 d-flex flex-lg-row flex-md-column flex-sm-column flex-column align-items-lg-center align-items-md-start align-items-sm-start align-items-center justify-content-start gap-3 mt-lg-5 mt-md-4 mt-sm-4 mt-4">
                                        <?php if(is_array($content['buttons'])):?>
                                            <?php foreach($content['buttons'] as $button):?>
                                                <a class="btn btn-<?php echo $button['type']?>" href="<?php echo $button['button']['url']?>" target="<?php echo $button['button']['target']?>" title="<?php echo html_entity_decode($button['button']['title'])?>">
                                                    <?php echo html_entity_decode($button['button']['title'])?>
                                                </a>
                                            <?php endforeach;?>
                                        <?php endif;?>
                                    </div>
                                </div>
                                <div data-aos="fade-left" class="col-lg-4 col-md-5 col-sm-12 col-12 d-flex align-items-center justify-content-md-center justify-content-sm-center justify-content-center img-container mb-md-0 mb-sm-5 mb-5 order-md-2 order-sm-1 order-1">
                                    <img src="<?php echo $content['image']['url']?>" style="max-width:<?php echo $content['image_max_width']?>;" class="" alt="">
                                </div>
                            </div>
                        </div>
                    <?php endif;?>
                <?php endforeach;?>
            <?php endif;?>

            <?php $buttons = get_field('buttons')?>
            <?php if(is_array($buttons) and count($buttons) > 0):?>
                <div class="container px-md-0 px-sm-auto px-auto d-flex flex-column justify-content-center align-items-center">
                    <div class="row pt-5">
                        <div data-aos="fade-up" class="col-12 text-center d-flex flex-md-row flex-sm-column flex-column align-content-center justify-content-center gap-4">
                            <?php foreach($buttons as $k => $button):?>
                                <a class="btn <?php if($k == 0):?>btn-secondary<?php else:?>btn-primary<?php endif;?>" href="<?php echo $button['button']['url']?>" target="<?php echo $button['button']['target']?>" title="<?php echo html_entity_decode($button['button']['title'])?>">
                                    <?php echo html_entity_decode($button['button']['title'])?>
                                </a>
                            <?php endforeach;?>
                        </div>
                    </div>
                </div>
            <?php endif;?>

        </div>

	</div>

</div>
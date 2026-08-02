<div id="<?php echo get_field('id')?>" class="price-block container-fluid px-0 style-<?php echo get_field('style')?>">

    <div class="row">

        <div class="col-12">
            <div class="container px-md-0 px-sm-auto px-auto">
                <div class="row position-relative">
                    <div data-aos="fade-up" class="col-md-10 col-sm-12 col-12 offset-md-1 offset-sm-0 offset-0 d-flex flex-column justify-content-center align-items-center text-center">
                        <h2><?php echo get_field('title')?></h2>
                        <?php if(get_field('description') != ''):?>
                            <p><?php echo get_field('description')?></p>
                        <?php endif;?>
                    </div>

                    <div data-aos="fade-up" class="col-12 d-flex flex-column justify-content-center align-items-center text-center">
                        <?php $columns = get_field('columns')?>
                        <?php if(is_array($columns) and count($columns) > 0):?>
                            <div class="row columns-group">
                                <?php foreach($columns as $k => $column):?>
                                    <?php if(get_field('style') == 2):?>
                                        <div class="col-lg-4 col-md-4 col-sm-12 col-12 d-flex flex-column align-items-center justify-content-start mb-lg-0 mb-md-4 mb-sm-4 mb-4 pb-2">
                                    <?php elseif(get_field('style') == 3):?>
                                        <div class="offset-lg-0 offset-md-3 offset-sm-0 offset-0 col-lg-4 col-md-6 col-sm-12 col-12 d-flex flex-column align-items-center justify-content-start mb-lg-0 mb-md-4 mb-sm-4 mb-4">
                                    <?php endif;?>
                                        <?php if($column['icon']):?>
                                            <img class="icon" src="<?php echo $column['icon']['url']?>" />
                                        <?php endif;?>
                                        <div class="column-item w-100" target="<?php echo site_url(ICL_LANGUAGE_CODE)?>/paso-1/?step=2&opt=<?php echo $k?>">
                                            <h3><?php echo $column['title']?></h3>
                                            <p><?php echo $column['description']?></p>
                                        </div>
                                    </div>
                                <?php endforeach;?>
                            </div>
                        <?php endif;?>
                    </div>

                    <div data-aos="fade-up" class="col-md-10 col-sm-12 col-12 offset-md-1 offset-sm-0 offset-0 d-flex flex-column justify-content-center align-items-center text-center">
                        <?php $buttons = get_field('buttons')?>
                        <?php if(is_array($buttons) and count($buttons) > 0):?>
                            <div class="row button-group w-100">
                                <div class="col-12 p-0 text-center d-flex flex-lg-row flex-md-column flex-sm-column flex-column align-content-center justify-content-center gap-4 mb-lg-0 mb-md-3 mb-sm-3 mb-3 mt-md-0 mt-sm-3 mt-3">
                                    <?php foreach($buttons as $k => $button):?>
                                        <a class="btn <?php if($k == 0):?>btn-secondary<?php else:?>btn-primary<?php endif;?>" href="<?php echo $button['button']['url']?>" target="<?php echo $button['button']['target']?>" title="<?php echo html_entity_decode($button['button']['title'])?>">
                                            <?php echo html_entity_decode($button['button']['title'])?>
                                        </a>
                                    <?php endforeach;?>
                                </div>
                            </div>
                        <?php endif;?>
                        <?php if(get_field('leyend') != ''):?>
                            <p class="small mt-4" data-aos="fade-up"><?php echo get_field('leyend')?></p>
                        <?php endif;?>
                    </div>
                </div>
            </div>
        </div>

	</div>

</div>
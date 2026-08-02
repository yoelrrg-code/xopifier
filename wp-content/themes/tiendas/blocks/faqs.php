<div id="faqs" class="container-fluid px-0 faqs">
    <div class="row">
        <div class="col-12">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <?php
                            $faqs = get_posts(array('post_type' => 'faq', 'post_status' => 'publish', 'posts_per_page' => -1, 'tax_query' => array(
                                array(
                                    'taxonomy' => 'faq-category',
                                    'field' => 'slug',
                                    'terms'    => 'home',
                                )
                            )));
                        ?>
                        <h3 class="small text-center justify-content-center fw-semibold" data-aos="fade-up"><?php echo get_field('title')?></h3>
                        <div class="accordion accordion-flush" id="accordionFAQs">
                            <?php if(is_array($faqs)):?>
                                <?php foreach ($faqs as $key => $faq):?>
                                        <div class="accordion-item" data-aos="fade-up">
                                            <h2 class="accordion-header" id="heading-<?php echo $key?>">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-<?php echo $key?>" aria-expanded="false" aria-controls="collapse-<?php echo $key?>">
                                                    <?php echo get_the_title($faq->ID)?>
                                                </button>
                                            </h2>
                                            <div id="collapse-<?php echo $key?>" class="accordion-collapse collapse" aria-labelledby="heading-<?php echo $key?>" data-bs-parent="#accordionFAQs">
                                                <div class="accordion-body">
                                                    <?php echo apply_filters('the_content', $faq->post_content)?>
                                                </div>
                                            </div>
                                        </div>
                                <?php endforeach; ?>
                            <?php endif;?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
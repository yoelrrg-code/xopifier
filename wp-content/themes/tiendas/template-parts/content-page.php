<?php global $post;?>
<div class="row">
    <?php if(get_the_post_thumbnail_url(null, 'large') != ''):?>
        <div class="col-lg-6 col-md-12 col-sm-12 col-12">
            <a href="<?php the_permalink(); ?>">
                <img src="<?php echo get_the_post_thumbnail_url(null, 'large')?>" alt="" class="w-100 mb-3 mt-0">
            </a>
        </div>
    <?php endif;?>
    <div class="<?php if(get_the_post_thumbnail_url(null, 'large') != ''):?>col-lg-6<?php endif;?> col-md-12 col-sm-12 col-12">
        <h2 class="mb-3 text-start"><a href="<?php the_permalink(); ?>"><?php the_title()?></a></h2>
        <?php the_excerpt(); ?>
    </div>
    <div class="col-12">
        <div class="author-box mt-2">
            <div class="author">
                <?php _e('Por', 'xopifier')?> <b><?php echo get_the_author()?></b>
            </div>
            <div class="date">
                <?php echo get_the_date('d F Y')?>
            </div>
            <div class="category">
                <?php 
                    $categories = get_the_terms(get_the_ID(), 'category');
                    $terms = array();
                    if(isset($categories) && is_array($categories) && count($categories) > 0){
                        foreach ($categories as $k => $category) {
                            $terms[] = $category->term_id;
                            echo '<a href="'.get_category_link($category).'" class="term">'.$category->name.'</a>';
                        }
                    }
                ?>
            </div>
            <a class="btn btn-primary ms-auto py-2" href="<?php the_permalink(); ?>"><?php _e('Leer más', 'xopifier')?></a>
        </div>
    </div>
</div>
<hr style="margin: 30px 0;border-color:#eee;opacity:1;">
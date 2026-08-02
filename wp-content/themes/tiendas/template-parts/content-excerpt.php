<?php global $post;?>

<div class="blog-list-item">
    <div class="inner bg-white p-3">
        <?php if ( get_the_post_thumbnail_url() ):?>
            <div class="post-image">
                <a href="<?php the_permalink(); ?>" title="<?php the_title()?>">
                    <img src="<?php echo get_the_post_thumbnail_url(null, 'large')?>" alt="" class="img-fluid w-100 mt-0">
                </a>
                <div class="categories">
                    <?php 
                        $categories = get_the_terms($post->ID, 'category');
                        $terms = array();
                        if(isset($categories) && is_array($categories) && count($categories) > 0){
                            foreach ($categories as $k => $category) {
                                $terms[] = $category->term_id;
                                echo '<a href="'.get_category_link($category).'" title="'.$category->name.'">'.$category->name.'</a>';
                            }
                        }
                    ?>
                </div>
            </div>
        <?php endif;?>
        <div class="post-content">
            <time class="post-date">
                <?php echo get_the_date('m/d/Y') . '<span class="mx-2">••</span> ' ._posted_by()?>
            </time>

            <h4 class="mb-3 text-start post-title"><a href="<?php the_permalink(); ?>" title="<?php the_title()?>"><?php the_title()?></a></h4>

            <div class="post-excerpt">
                <?php the_excerpt(); ?>
            </div>
        </div>
    </div>
</div>
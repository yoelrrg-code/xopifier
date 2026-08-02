<?php global $post;?>

<!-- <img src="<?php echo get_the_post_thumbnail_url(null, 'large')?>" alt="" class="post-image w-100 mb-3 mt-0"> -->
<?php if ( get_the_post_thumbnail_url() ):?>
    <div class="post-image">
        <img src="<?php echo get_the_post_thumbnail_url(null, 'full')?>" alt="" class="img-fluid w-100 mb-3 mt-0">
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

<div class="post-date">
    <?php echo get_the_date('m/d/Y')?>
</div>

<h1 class="post-title mb-4 text-start"><?php the_title()?></h1>

<?php the_content(); ?>

<div class="tags mt-5">
    <?php 
        $tags = get_the_terms(get_the_ID(), 'post_tag');
        $terms = array();
        if(isset($tags) && is_array($tags) && count($tags) > 0){
            foreach ($tags as $k => $tag) {
                echo '<a href="'.get_term_link($tag->term_id, 'post_tag').'" title="'.$tag->name.'">'.$tag->name.'</a>';
            }
        }
    ?>
</div>
<?php global $post;?>

<div class="portfolio-grid-item col-lg-4 col-md-6 col-sm-12 col-12">
    <div class="inner bg-light p-0">
        <div class="inner">
            <a class="link" href="<?php the_permalink(); ?>">&nbsp;</a>
            <div class="bg-image" style="background: url(<?php echo get_the_post_thumbnail_url(null, 'full')?>) center center no-repeat; background-size: cover;"></div>
            <div class="overlay" style="background: <?php echo get_field('overlay_color', get_the_ID())?>;"></div>
            <div class="text">
                <p><?php _e('Ver Portfolio', 'xopifier')?></p>
                <h4><?php echo get_the_title(get_the_ID())?></h4>
            </div>
        </div>
    </div>
</div>
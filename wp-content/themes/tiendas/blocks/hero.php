<?php 
$image             = get_field('image');
$title             = get_field('title');
$description       = get_field('description');
$button            = get_field('button');
$button_2          = get_field('button_2');
$background_image  = get_field('background_image');
$background_color  = get_field('background_color');
$title_color       = get_field('title_color');
$description_color = get_field('description_color');

$img_url   = isset($image['url']) ? esc_url($image['url']) : '';
$bg_url    = isset($background_image['url']) ? esc_url($background_image['url']) : '';
$bg_color  = !empty($background_color) ? esc_attr($background_color) : 'transparent';
$t_color   = !empty($title_color) ? esc_attr($title_color) : 'inherit';
$d_color   = !empty($description_color) ? esc_attr($description_color) : 'inherit';
?>

<div id="hero" class="container-fluid px-0">
    <div class="row">
        <div class="col-12">
            <div class="container px-md-0 px-sm-auto px-auto">
                <div class="row position-relative">
                    <div data-aos="fade-right" class="col-lg-4 col-md-5 col-sm-12 col-12 d-flex align-items-center img-container">
                        <?php if (!empty($img_url)): ?>
                            <img src="<?php echo $img_url; ?>" alt="" class="">
                        <?php endif; ?>
                    </div>
                    <div data-aos="fade-left" class="offset-lg-1 offset-md-1 offset-sm-0 offset-0 col-lg-7 col-md-6 col-sm-12 col-12 d-flex flex-column justify-content-center align-items-start text-container">
                        <?php if (!empty($title)): ?>
                            <h2><?php echo esc_html($title); ?></h2>
                        <?php endif; ?>

                        <?php if (!empty($description)): ?>
                            <p class=""><?php echo esc_html($description); ?></p>
                        <?php endif; ?>

                        <div class="w-100 d-flex flex-lg-row flex-md-column flex-sm-column flex-column align-items-lg-center align-items-md-start align-items-sm-start align-items-center justify-content-start gap-3">
                            <?php if ($button && !empty($button['url'])): ?>
                                <a class="btn btn-secondary" href="<?php echo esc_url($button['url']); ?>" target="<?php echo !empty($button['target']) ? esc_attr($button['target']) : '_self'; ?>" title="<?php echo esc_attr($button['title']); ?>">
                                    <?php echo esc_html($button['title']); ?>
                                </a>
                            <?php endif; ?>

                            <?php if ($button_2 && !empty($button_2['url'])): ?>
                                <a class="btn btn-primary" href="<?php echo esc_url($button_2['url']); ?>" target="<?php echo !empty($button_2['target']) ? esc_attr($button_2['target']) : '_self'; ?>" title="<?php echo esc_attr($button_2['title']); ?>">
                                    <?php echo esc_html($button_2['title']); ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    <?php if (!empty($bg_url)): ?>
        #hero {
            background: url(<?php echo $bg_url; ?>) center center no-repeat;
            background-size: cover;
        }
    <?php else: ?>
        #hero {
            background: <?php echo $bg_color; ?>;
        }
    <?php endif; ?>

    body #hero h2 {
        color: <?php echo $t_color; ?>;
    }
    body #hero p {
        color: <?php echo $d_color; ?>;
    }
</style>
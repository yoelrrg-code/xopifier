<?php 
$title  = get_field('title');
$poster = get_field('poster');
$video  = get_field('video');
$button = get_field('button');

$poster_url = isset($poster['url']) ? esc_url($poster['url']) : '';
$video_url  = isset($video['url']) ? esc_url($video['url']) : '';
?>

<div id="howto" data-aos="fade-up" class="container-fluid px-0">
    <div class="row">
        <div class="col-12">
            <div class="container px-md-0 px-sm-auto px-auto">
                <div class="row">
                    <?php if (!empty($title)): ?>
                        <div data-aos="fade-right" class="col-12 d-flex align-items-center justify-content-center">
                            <h2><?php echo esc_html($title); ?></h2>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($video_url)): ?>
                        <div data-aos="fade-left" class="col-12 d-flex flex-column justify-content-center align-items-start">
                            <div class="video-wrapper">
                                <video controls preload="auto" class="videot" id="videot" poster="<?php echo $poster_url; ?>">
                                    <source src="<?php echo $video_url; ?>" type="video/mp4">
                                    <object data="<?php echo $video_url; ?>">
                                        <param name="wmode" value="transparent">
                                        <param name="autoplay" value="false">
                                        <param name="loop" value="false">
                                    </object>
                                </video>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($button && !empty($button['url'])): ?>
                        <div data-aos="fade-right" class="col-12 d-flex align-items-center justify-content-center">
                            <a class="btn btn-primary" href="<?php echo esc_url($button['url']); ?>" target="<?php echo !empty($button['target']) ? esc_attr($button['target']) : '_self'; ?>" title="<?php echo esc_attr($button['title']); ?>">
                                <?php echo esc_html($button['title']); ?>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
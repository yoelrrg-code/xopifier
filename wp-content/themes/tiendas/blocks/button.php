<?php 
$button     = get_field('button');
$alignment  = get_field('alignment');
$type       = get_field('type');
$custom_cls = get_field('custom_class');

if ($button && !empty($button['url'])):
    $btn_url    = esc_url($button['url']);
    $btn_target = !empty($button['target']) ? esc_attr($button['target']) : '_self';
    $btn_title  = !empty($button['title']) ? esc_html($button['title']) : '';
    $align_cls  = !empty($alignment) ? esc_attr($alignment) : 'start';
    $type_cls   = !empty($type) ? esc_attr($type) : 'primary';
    $extra_cls  = !empty($custom_cls) ? esc_attr($custom_cls) : '';
?>
    <div data-aos="fade-up" class="d-flex align-items-center justify-content-<?php echo $align_cls; ?>">
        <a class="btn btn-<?php echo $type_cls; ?> <?php echo $extra_cls; ?>" href="<?php echo $btn_url; ?>" target="<?php echo $btn_target; ?>" title="<?php echo $btn_title; ?>">
            <?php echo $btn_title; ?>
        </a>
    </div>
<?php endif; ?>
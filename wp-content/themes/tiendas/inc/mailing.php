<?php
function send_mail($type, $to, $image, $title, $description, $subtitle = '', $button_url = '', $button_text = ''){
    $headers = array('Content-Type: text/html; charset=UTF-8');
    $theme_dir = get_template_directory();
    $theme_uri = get_template_directory_uri();

    $to = sanitize_email($to);
    if (!is_email($to)) {
        return false;
    }

    if(is_array($title)){
        $subject = isset($title['text']) ? $title['text'] : '';
        $title   = isset($title['html']) ? $title['html'] : '';
    }else{
        $subject = $title;
    }
    
    $html = '';
    $template_file = '';

    if($type == 'generic'){
        $template_file = $theme_dir . '/emails/generic.html';
    }elseif($type == 'design'){
        $template_file = $theme_dir . '/emails/ready-designs.html';
    }elseif($type == 'generic-2'){
        $template_file = $theme_dir . '/emails/generic-step-2.html';
    }

    if (!empty($template_file) && file_exists($template_file)) {
        $html = file_get_contents($template_file);
    } else {
        return false;
    }

    $html = str_replace('{{mainimage}}', esc_url($image), $html);
    $html = str_replace('{{title}}', $title, $html);
    $html = str_replace('{{description}}', $description, $html);

    if ($type == 'design') {
        $html = str_replace('{{subtitle}}', $subtitle, $html);
        $html = str_replace('{{button_url}}', esc_url($button_url), $html);
        $html = str_replace('{{button_text}}', esc_html($button_text), $html);
    } elseif ($type == 'generic-2') {
        $html = str_replace('{{description_2}}', $subtitle, $html);
        $html = str_replace('{{button_url}}', esc_url($button_url), $html);
        $html = str_replace('{{button_text}}', esc_html($button_text), $html);
    }

    $html = str_replace('{{logoheader}}', $theme_uri . '/emails/img/logo.svg', $html);
    $html = str_replace('{{logofooter}}', $theme_uri . '/emails/img/logo-footer.svg', $html);
    $html = str_replace('{{gradient}}', $theme_uri . '/emails/img/gradient.png', $html);

    // social media
    $socialmedia = get_field('social_media', 'option');
    if (is_array($socialmedia)) {
        foreach($socialmedia as $media){
            if (isset($media['link']['title'], $media['link']['url'])) {
                $platform = strtolower(sanitize_key($media['link']['title']));
                $html = str_replace('{{'.$platform.'image}}', $theme_uri . '/emails/img/'.$platform.'.svg', $html);
                $html = str_replace('{{'.$platform.'}}', esc_url($media['link']['url']), $html);
            }
        }
    }

    return wp_mail($to, $subject, $html, $headers);
}
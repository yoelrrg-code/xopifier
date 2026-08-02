<?php
function step_4_welcome($form) {
    if (!is_array($form)) {
        return '';
    }

    $img_url     = isset($form['image']['url']) ? esc_url($form['image']['url']) : '';
    $title       = isset($form['title']) ? esc_html($form['title']) : '';
    $description = isset($form['description']) ? $form['description'] : '';
    $btn_url     = isset($form['button']['url']) ? esc_url($form['button']['url']) : '';
    $btn_title   = isset($form['button']['title']) ? esc_html($form['button']['title']) : '';
    $btn_target  = isset($form['button']['target']) ? esc_attr($form['button']['target']) : '_self';

    return '
        <div class="sub-step welcome-step-4">
            <div class="row">
                <div class="col-12">
                    <div class="row box">
                        <div class="text-center col-12">
                            '.(!empty($img_url) ? '<img src="'.$img_url.'" alt="main image" class="step-main-image" />' : '').'
                            '.(!empty($title) ? '<h3 class="step-title">'.$title.'</h3>' : '').'
                            '.(!empty($description) ? '<div class="step-description">'.$description.'</div>' : '').'
                            '.(!empty($btn_title) ? '<a class="btn btn-primary" href="'.$btn_url.'" target="'.$btn_target.'" title="'.$btn_title.'">'.$btn_title.'</a>' : '').'
                        </div>
                    </div>
                </div>
            </div>
        </div>
    ';
}

function step_4_shortcode(){
    global $current_user, $designId;

    $form_html = '';

    if (!is_user_logged_in() || (isset($current_user->roles) && in_array('administrator', $current_user->roles))) {
        // Formulario de login si no está autenticado
        $form_html = step_2_generate_login_form($designId);
    } else {
        $form = get_field('message_step_4_1', 'option');
        if (empty($form)) {
            $form = get_field('message_step_3_1', 'option');
        }

        if (isset($designId) && !empty($designId)) {            
            $form_html = step_4_welcome($form);
        }
    }

    $html = '
        <div id="steps" class="step-4 container-fluid px-0">
            <div class="row">
                <div class="col-12">
                    <div class="container">
                        <div class="step">
                            '.$form_html.'
                        </div>
                    </div>
                </div>
            </div>
        </div>
    ';

    return $html;
}
add_shortcode("step4", "step_4_shortcode");
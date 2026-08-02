<?php

// File Security Check
defined('ABSPATH') or die('No script kiddies please!');

function _customize_register($wp_customize) {

        // main logo setting
    // $wp_customize->add_setting('logo_main', array(
    //     'default' => '',
    //     'transport' => 'refresh',
    // ));
    // main logo control
    // $wp_customize->add_control(
    //         new WP_Customize_Image_Control(
    //         $wp_customize, 'logo_main', array(
    //     'label' => __('Suba el logo principal', 'xopifier'),
    //     'section' => 'title_tagline',
    //     'settings' => 'logo_main',
    //         )
    //     )
    // );

    // // main logo footer setting
    // $wp_customize->add_setting('logo_footer_main', array(
    //     'default' => '',
    //     'transport' => 'refresh',
    // ));
    // // main logo control
    // $wp_customize->add_control(
    //         new WP_Customize_Image_Control(
    //         $wp_customize, 'logo_footer_main', array(
    //     'label' => __('Suba el logo del footer', 'xopifier'),
    //     'section' => 'title_tagline',
    //     'settings' => 'logo_footer_main',
    //         )
    //     )
    // );

    // $wp_customize->add_panel( 'panel_theme_options', array(
    //     'priority' => 10,
    //     'capability' => 'edit_theme_options',
    //     'theme_supports' => '',
    //     'title' => __( 'Opciones del tema', 'xopifier' ),
    // ) );    
    
    //     $wp_customize->add_section('general_options', array(
    //         'title' => __('Opciones Generales', 'xopifier'),
    //         'priority' => 116,
    //         'panel' => 'panel_theme_options'
    //     ));

    //         $wp_customize->add_setting('copy_setting', array(
    //             'default' => '',
    //             'transport' => 'refresh',
    //         ));

    //         $wp_customize->add_control( 'copy_setting', array(
    //             'type' => 'textarea',
    //             'section' => 'general_options', // // Add a default or your own section
    //             'settings' => 'copy_setting',
    //             'label' => __( 'Copyright' ),
    //             'description' => __( 'Copyright for site footer section.' ),
    //         ));

    //         $wp_customize->add_setting('text_reserva_setting', array(
    //             'default' => '',
    //             'transport' => 'refresh',
    //         ));
    //         // main logo control
    //         $wp_customize->add_control(
    //                 new WP_Customize_Control(
    //                 $wp_customize, 'text_reserva_setting', array(
    //             'label' => esc_html__( 'Texto Reserva', 'xopifier' ),
    //             'section' => 'general_options',
    //             'settings' => 'text_reserva_setting',
    //                 )
    //             )
    //         );  

    //         $wp_customize->add_setting('link_reserva_setting', array(
    //             'default' => '',
    //             'transport' => 'refresh',
    //         ));
    //         // main logo control
    //         $wp_customize->add_control(
    //                 new WP_Customize_Control(
    //                 $wp_customize, 'link_reserva_setting', array(
    //             'label' => esc_html__( 'Enlace Reserva', 'xopifier' ),
    //             'section' => 'general_options',
    //             'settings' => 'link_reserva_setting',
    //                 )
    //             )
    //         );

        // $wp_customize->add_section('contact_options', array(
        //     'title' => __('Opciones de Contacto', 'xopifier'),
        //     'priority' => 116,
        //     'panel' => 'panel_theme_options'
        // ));

        //     $wp_customize->add_setting('address_setting', array(
        //         'default' => '',
        //         'transport' => 'refresh',
        //     ));
        //     // main logo control
        //     $wp_customize->add_control(
        //             new WP_Customize_Control(
        //             $wp_customize, 'address_setting', array(
        //         'label' => esc_html__( 'Direccion', 'xopifier' ),
        //         'section' => 'contact_options',
        //         'settings' => 'address_setting',
        //             )
        //         )
        //     );

        //     $wp_customize->add_setting('phone_setting', array(
        //         'default' => '',
        //         'transport' => 'refresh',
        //     ));
        //     // main logo control
        //     $wp_customize->add_control(
        //             new WP_Customize_Control(
        //             $wp_customize, 'phone_setting', array(
        //         'label' => esc_html__( 'Telefono', 'xopifier' ),
        //         'section' => 'contact_options',
        //         'settings' => 'phone_setting',
        //             )
        //         )
        //     );

        //     $wp_customize->add_setting('email_setting', array(
        //         'default' => '',
        //         'transport' => 'refresh',
        //     ));
        //     // main logo control
        //     $wp_customize->add_control(
        //             new WP_Customize_Control(
        //             $wp_customize, 'email_setting', array(
        //         'label' => esc_html__( 'Email', 'xopifier' ),
        //         'section' => 'contact_options',
        //         'settings' => 'email_setting',
        //             )
        //         )
        //     );

        // $wp_customize->add_section('socialmedia_options', array(
        //     'title' => __('Opciones de las Redes Sociales', 'xopifier'),
        //     'priority' => 116,
        //     'panel' => 'panel_theme_options'
        // ));

        //     Kirki::add_field( 'socialmedia_setting', [
        //         'type'        => 'repeater',
        //         'label'       => esc_html__( 'Redes Sociales', 'xopifier' ),
        //         'section'     => 'socialmedia_options',
        //         'priority'    => 10,
        //         'row_label' => [
        //             'type'  => 'text',
        //             'value' => esc_html__( 'Redes Sociales', 'xopifier' ),
        //         ],
        //         'button_label' => esc_html__('Agregar Red Social', 'xopifier' ),
        //         'settings'     => 'socialmedia_setting',
        //         'fields' => array(
        //             'social_url' => array(
        //                 'type'        => 'text',
        //                 'label'       => esc_attr__( 'URL de la Red', 'xopifier' ),
        //                 'default'     => '',
        //             ),
        //             'social_type' => array(
        //                 'type'		=> 'select',
        //                 'choices' 	=> array(
        //                     'facebook'	=> 'Facebook',
        //                     'twitter'   => 'Twitter',
        //                     'linkedin'  => 'Linkedin',
        //                     'instagram'  => 'Instagram',
        //                     'youtube'  => 'Youtube',
        //                 ),
        //                 'default' => 'facebook',
        //             ),
        //         ),
        //     ] );
}

add_action('customize_register', '_customize_register');

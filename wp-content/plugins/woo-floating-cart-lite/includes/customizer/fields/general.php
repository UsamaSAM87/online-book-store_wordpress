<?php

Xirki::add_field( self::$config_id, array(
    'settings'    => self::field_id( 'ajax_init' ),
    'section'     => self::section_id( 'general' ),
    'label'       => esc_html__( 'Force Ajax Initialization', 'woo-floating-cart' ),
    'description' => esc_html__( 'Enable only if encountering caching issues / conflicts with your theme', 'woo-floating-cart' ),
    'type'        => 'radio-buttonset',
    'choices'     => array(
    '0' => esc_html__( 'No', 'woo-floating-cart' ),
    '1' => esc_html__( 'Yes', 'woo-floating-cart' ),
),
    'default'     => '0',
    'priority'    => 10,
    'transport'   => 'postMessage',
) );
Xirki::add_field( self::$config_id, array(
    'settings' => self::field_id( 'general_features' ),
    'section'  => self::section_id( 'general' ),
    'type'     => 'xt-premium',
    'default'  => array(
    'type'  => 'image',
    'value' => self::$parent->plugin_url() . 'includes/customizer/assets/images/general.png',
    'link'  => self::$parent->fs()->get_upgrade_url(),
),
) );
<?php

Xirki::add_field( self::$config_id, array(
    'settings' => self::field_id( 'visibility_features' ),
    'section'  => self::section_id( 'visibility' ),
    'type'     => 'xt-premium',
    'default'  => array(
    'type'  => 'image',
    'value' => self::$parent->plugin_url() . 'includes/customizer/assets/images/visibility.png',
    'link'  => self::$parent->fs()->get_upgrade_url(),
),
) );
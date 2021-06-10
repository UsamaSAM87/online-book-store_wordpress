<?php

Xirki::add_field( self::$config_id, array(
    'settings' => self::field_id( 'list_features' ),
    'section'  => self::section_id( 'body' ),
    'type'     => 'xt-premium',
    'default'  => array(
    'type'  => 'image',
    'value' => self::$parent->plugin_url() . 'includes/customizer/assets/images/list.png',
    'link'  => self::$parent->fs()->get_upgrade_url(),
),
) );
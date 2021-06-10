<?php

Xirki::add_field( self::$config_id, array(
    'settings' => self::field_id( 'typography_features' ),
    'section'  => self::section_id( 'typography' ),
    'type'     => 'xt-premium',
    'default'  => array(
    'type'  => 'image',
    'value' => self::$parent->plugin_url() . 'includes/customizer/assets/images/typography.png',
    'link'  => self::$parent->fs()->get_upgrade_url(),
),
) );
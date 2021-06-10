<?php
if (!class_exists('XT_Plugins_Tab')) {

    class XT_Plugins_Tab {

        function __construct() {

            add_filter( 'plugins_api_result', array($this, 'plugin_results'), 1, 3);
        }

        function plugin_results($res, $action, $args) {

            if($action !== 'query_plugins') {
                return $res;
            }

            $args = (array) $args;

            unset($args['browse']);

            if(!empty($args['xt_plugin_query']) || !empty($args['search'])) {
                return $res;
            }

            $args['search'] = 'xplodedthemes';
            $args['xt_plugin_query'] = true;

            $api = plugins_api( 'query_plugins', $args );

            if ( is_wp_error( $api ) ) {
                return $res;
            }

            $below_plugins = array_splice($res->plugins, 8);
            $top_plugins = $res->plugins;

            $top_plugins = array_merge($api->plugins, $top_plugins);
            shuffle($top_plugins);

            $res->plugins = array_merge($top_plugins, $below_plugins);

            return $res;
        }

    }

    new XT_Plugins_Tab;
}
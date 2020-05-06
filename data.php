<?php
/**
 * Plugin Name: Data Visualizations
 * Description: A plugin that holds all of the Jacket's data visualizations.
 * Author: Jerome Paulos
 * Author URI: https://jeromepaulos.com
 */


add_filter( 'plugin_action_links', 'disable_plugin_deactivation', 10, 4 );
function disable_plugin_deactivation( $actions, $plugin_file, $plugin_data, $context ) {
 
    if ( array_key_exists( 'deactivate', $actions ) && in_array( $plugin_file, array(
        'data/data.php'
    )))
        unset( $actions['deactivate'] );
    return $actions;
}
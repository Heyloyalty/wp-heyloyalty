<?php
/*
Plugin Name: wp-heyloyalty
Version: 1.1.4
Plugin URI: https://heyloyalty.com/plugins/wordpress
Description: Integrates with heyloyalty email platform
Author: René Skou Jensen
Text Domain: wp-heyloyalty
Domain Path: /languages/
License: GPL v3
*/
/**
 * Main file
 */
if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}
/**
 * @return Heyloyalty\\Plugin
 */
function wp_heyloyalty() {
	static $instance;
	if( is_null( $instance ) ) {
		$classname =  'Heyloyalty\\Plugin';
		$id = 0;
		$file = __FILE__;
		$dir = dirname( __FILE__ );
		$name = 'Wp Heyloyalty';
		$version = '1.1.4';
        $slug = 'wp-heyloylty';
		$instance = new $classname(
			$id,
			$name,
			$version,
			$file,
			$dir,
            $slug
		);
	}
	return $instance;
}
// wrapper function to move out of global namespace
function __load_wp_heyloyalty() {
	// load autoloader & init plugin
	require dirname( __FILE__ ) . '/vendor/autoload.php';
	// fetch instance and store in global
	$GLOBALS['wp_heyloyalty'] = wp_heyloyalty();
	// register activation hook
	register_activation_hook( __FILE__, "__activate_wp_heyloyalty_plugin" );

}
function __load_wp_heyloyalty_fallback() {
    //todo
}
function __activate_wp_heyloyalty_plugin() {
    //function to access under the plugin activation fase.
}
if( version_compare( PHP_VERSION, '5.3', '>=' ) ) {
	__load_wp_heyloyalty();
} else {
	//todo
}

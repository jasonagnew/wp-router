<?php
/**
 * @wordpress-plugin
 * Plugin Name:       WP Router
 * Plugin URI:        http://wp-router.org/
 * Description:       A router for WordPress.
 * Version:           1.0.0
 * Author:            Jason Agnew
 * Author URI:        https://bigbitecreative.com/
 * License:           GPL2+
 */


require_once __DIR__ . '/src/class-wp-http.php';
require_once __DIR__ . '/src/class-wp-router.php';

global $wp_router, $wp_http;

$wp_http 	= new WP_Http;
$wp_router 	= new WP_Router;

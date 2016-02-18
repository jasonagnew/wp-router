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

function dd()
{
    array_map(function($x) { var_dump($x); }, func_get_args());
    die;
}

require_once __DIR__ . '/src/class-wp-request.php';
require_once __DIR__ . '/src/class-wp-middleware.php';
require_once __DIR__ . '/src/class-wp-response.php';
require_once __DIR__ . '/src/class-wp-router.php';

require_once __DIR__ . '/src/responses/class-wp-json-response.php';
require_once __DIR__ . '/src/responses/class-wp-template-response.php';
require_once __DIR__ . '/src/responses/class-wp-redirect-response.php';

global $wp_router;

$wp_router = new WP_Router;

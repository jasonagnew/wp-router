<?php

/**
 * A number of helper functions.
 */

function get_wp_router()
{
	global $wp_router;

	return $wp_router;
}

function response( $data, $status = 200, $headers = array() )
{
	$response = new WP_Response();

	return $response->basic( $data, $status, $headers );
}

function json_response( $data, $status = 200, $headers = array() )
{
	$response = new WP_Response();

	return $response->json( $data, $status, $headers );
}

function template_response( $file, $varibales = array(), $status = 200, $headers = array() )
{
	$response = new WP_Response();

	return $response->template( $file, $varibales, $status, $headers );
}

function redirect_response( $url, $status = 302, $headers = array() )
{
	$response = new WP_Response();

	return $response->redirect( $url, $status, $headers );
}

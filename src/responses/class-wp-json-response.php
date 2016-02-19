<?php

class WP_JSON_Response extends WP_Response {

	/**
	 * Builds a json response.
	 *
	 * @param mixed
	 * @param integer
	 * @param array
	 */
    public function __construct( $data = null, $status = 200, $headers = array() )
    {
    	$headers[] = 'Content-Type: application/json';
    	$data = json_encode( $data );

    	return parent::__construct( $data, $status, $headers );
    }
}

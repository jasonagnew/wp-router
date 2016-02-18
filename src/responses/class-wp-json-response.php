<?php

class WP_JSON_Response extends WP_Response {

    public function __construct( $data = null, $status = 200, $headers = array() )
    {
    	$headers[] = 'Content-Type: application/json';
    	$data = json_encode( $data );

    	return parent::__construct( $data, $status, $headers );
    }
}

<?php

class WP_Redirect_Response extends WP_Response {

    public function __construct( $url, $status = 302, $headers = array() )
    {
    	$headers[] = 'Location: ' . $url;

    	return parent::__construct( null, $status, $headers );
    }
}

<?php

class WP_Router_UnitTestCase extends WP_UnitTestCase {

	public function setUp()
	{
		$GLOBALS['wp_router'] = new WP_Router( true );
        parent::setUp();
    }

    public function tearDown()
    {
        parent::tearDown();
    }

    public function visit( $uri, $method = 'get', $request = array() )
    {
    	$GLOBALS['wp'] 			   = new WP();
		$GLOBALS['wp_the_query']   = new WP_Query();
		$GLOBALS['wp_query'] 	   = $GLOBALS['wp_the_query'];
		$_SERVER['REQUEST_URI']    = $uri;
		$_SERVER['REQUEST_METHOD'] = strtoupper( $method );

		unset($_SERVER['X-HTTP-METHOD-OVERRIDE']);

		if ( !in_array( $method, array('get', 'post') ) )
		{
			$_SERVER['REQUEST_METHOD'] 		   = 'POST';
			$_SERVER['X-HTTP-METHOD-OVERRIDE'] = strtoupper( $method );
		}

		$GLOBALS['wp_rewrite']->set_permalink_structure( '/%postname%/' );
		$GLOBALS['wp']->init();
		$GLOBALS['wp_router']->boot();

		$_REQUEST = $request;

		$GLOBALS['wp']->parse_request( '' );

		return $GLOBALS['wp_router']->parse_request( $GLOBALS['wp'], true );
    }

}

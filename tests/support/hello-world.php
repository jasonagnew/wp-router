<?php

// class Hello_World extends WP_Router_UnitTestCase {

// 	public function testSimpleRoute()
// 	{
// 		global $wp_router;

//         $wp_router->put( array(
//             'as'   => 'getUser',
//             'uri'  => '/user/{id}',
//             'uses' => array( $this, 'displayUser' )
//         ) );

//         $response = $this->visit( '/user/2', 'put', array( 'test' => 'new' ) );

//         //$response->get()
//         //$this->assertEquals( 'new', $response->get() );
// 	}

// 	public function displayUser($id, WP_Request $request)
// 	{
// 		$this->assertEquals( 'new', $request->get('test') );
// 		//return new WP_Response($id);
// 		return new WP_Response( $request->get('test', 'no') );
// 	}
// }

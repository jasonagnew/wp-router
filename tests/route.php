<?php

class Route extends WP_Router_UnitTestCase {

	/**
	 * Check GET route.
	 */
	public function testGet()
	{
		global $wp_router;

        $wp_router->get( array(
            'as'   => 'get',
            'uri'  => '/test/route',
            'uses' => array( $this, 'getMethod' )
        ) );

        $response = $this->visit( '/test/route', 'get' );

        $this->assertEquals( 'GET', $response );
	}

	/**
	 * Check POST route.
	 */
	public function testPost()
	{
		global $wp_router;

        $wp_router->post( array(
            'as'   => 'post',
            'uri'  => '/test/route',
            'uses' => array( $this, 'postMethod' )
        ) );

        $response = $this->visit( '/test/route', 'post' );

        $this->assertEquals( 'POST', $response );
	}

	/**
	 * Check PUT route.
	 */
	public function testPut()
	{
		global $wp_router;

        $wp_router->put( array(
            'as'   => 'put',
            'uri'  => '/test/route',
            'uses' => array( $this, 'putMethod' )
        ) );

        $response = $this->visit( '/test/route', 'put' );

        $this->assertEquals( 'PUT', $response );
	}

	/**
	 * Check PATCH route.
	 */
	public function testPatch()
	{
		global $wp_router;

        $wp_router->patch( array(
            'as'   => 'patch',
            'uri'  => '/test/route',
            'uses' => array( $this, 'patchMethod' )
        ) );

        $response = $this->visit( '/test/route', 'patch' );

        $this->assertEquals( 'PATCH', $response );
	}

	/**
	 * Check DELETE route.
	 */
	public function testDelete()
	{
		global $wp_router;

        $wp_router->delete( array(
            'as'   => 'delete',
            'uri'  => '/test/route',
            'uses' => array( $this, 'deleteMethod' )
        ) );

        $response = $this->visit( '/test/route', 'delete' );

        $this->assertEquals( 'DELETE', $response );
	}

	/**
	 * Check all methods together.
	 */
	public function testAllMethods()
	{
		global $wp_router;

		$wp_router->get( array(
            'as'   => 'get',
            'uri'  => '/test/route',
            'uses' => array( $this, 'getMethod' )
        ) );

        $wp_router->post( array(
            'as'   => 'post',
            'uri'  => '/test/route',
            'uses' => array( $this, 'postMethod' )
        ) );

        $wp_router->put( array(
            'as'   => 'put',
            'uri'  => '/test/route',
            'uses' => array( $this, 'putMethod' )
        ) );

        $wp_router->patch( array(
            'as'   => 'patch',
            'uri'  => '/test/route',
            'uses' => array( $this, 'patchMethod' )
        ) );

        $wp_router->delete( array(
            'as'   => 'delete',
            'uri'  => '/test/route',
            'uses' => array( $this, 'deleteMethod' )
        ) );

        $this->assertEquals( 'GET'   , $this->visit( '/test/route', 'get'    ) );
        $this->assertEquals( 'POST'  , $this->visit( '/test/route', 'post'   ) );
        $this->assertEquals( 'PUT'   , $this->visit( '/test/route', 'put'    ) );
        $this->assertEquals( 'PATCH' , $this->visit( '/test/route', 'patch'  ) );
        $this->assertEquals( 'DELETE', $this->visit( '/test/route', 'delete' ) );
	}

	public function getMethod()
	{
		return 'GET';
	}

	public function postMethod()
	{
		return 'POST';
	}

	public function putMethod()
	{
		return 'PUT';
	}

	public function patchMethod()
	{
		return 'PATCH';
	}

	public function deleteMethod()
	{
		return 'DELETE';
	}
}

<?php

abstract class WP_Middleware {

	protected $router;
	protected $store;

	public function run( WP_Request $request, WP_Router $router, $store )
	{
		$this->router = $router;
		$this->store = $store;

		return $this->handle( $request );
	}

	public function next( WP_Request $request )
	{
		$this->router->next( $request, $this->router, $this->store );
	}

	abstract function handle( WP_Request $request );
}

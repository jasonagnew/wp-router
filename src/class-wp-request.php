<?php

class WP_Request {

	protected $parameters = array();

	public function __construct()
	{
		$this->parameters = $this->request();
	}

	/**
	 * Gets request info
	 *
	 */
	public function request()
	{
		$request = $_REQUEST;
		$server  = $_SERVER;

		if ( isset( $server['CONTENT_TYPE'] ) && $server['CONTENT_TYPE'] == 'application/json' )
		{
			$json = json_decode( file_get_contents( 'php://input' ), true );

			if ( $json )
			{
				$request = array_merge( $request, $json );
			}
		}

		return $request;
	}

	/**
	 * Gets a request parameter.
	 *
	 * @param        $var
	 * @param string $default
	 * @return string
	 */
	public function get( $var, $default = '' )
	{
		if ( !isset( $this->parameters[$var] ) || empty( $this->parameters[$var] ) )
		{
			return $default;
		}

		return $this->parameters[$var];
	}

	/**
	 * Check if a request parameter exists.
	 *
	 * @param $var
	 * @return bool
	 */
	public function has( $var )
	{
		return $this->get( $var, null ) !== null;
	}

	/**
	 * Return all the request parameters.
	 *
	 * @return mixed
	 */
	public function all()
	{
		return $this->parameters;
	}

	public function merge( $data )
	{
		$this->parameters = array_merge( $this->parameters, $data );
	}

	/**
	 * Gets method used, supporting _method
	 *
	 * @return mixed
	 */
	public function method()
	{
		$method = $_SERVER['REQUEST_METHOD'];

		if ( $method === 'POST' )
		{
			if ( isset( $_SERVER['X-HTTP-METHOD-OVERRIDE'] ) )
			{
				$method = $_SERVER['X-HTTP-METHOD-OVERRIDE'];
			}
			elseif ( $this->has( '_method' ) )
			{
				$method = $this->get( '_method' );
			}
		}

		return strtoupper($method);
	}

}

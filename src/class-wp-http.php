<?php

class WP_Http {

	public function __construct()
	{
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
		$res = $this->all();

		if ( !isset( $res[$var] ) || empty( $res[$var] ) )
		{
			return $default;
		}

		return $res[$var];
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
	 * Gets all the request parameters.
	 *
	 * @return mixed
	 */
	public function all()
	{
		return $_SERVER['REQUEST_METHOD'] === 'POST' ? $_POST : $_GET;
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
			elseif ( isset( $_REQUEST['_method'] ) )
			{
				$method = $_REQUEST['_method'];
			}
		}

		return strtoupper($method);
	}

}

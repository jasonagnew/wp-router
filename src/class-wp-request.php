<?php

class WP_Request {

	/**
	* $_Request & php://input
	*
	* @var array
	*/
	protected $parameters;

	/**
	* $_GET
	*
	* @var array
	*/
	public $query;

	/**
	* $_POST
	*
	* @var array
	*/
	public $post;

    /**
     * $_SERVER
     *
     * @var array
     */
    public $server;

    /**
     * $_FILES
     *
     * @var array
     */
    public $files;

    /**
     * $_COOKIE
     *
     * @var array
     */
    public $cookies;

    /**
     * request headers
     *
     * @var array
     */
    public $headers;

    /**
     * List of available types.
     *
     * @var array
     */
   	public $types = array(
   		'parameters',
   		'query',
   		'post',
   		'cookies',
   		'files',
   		'server',
   		'headers',
   	);

   	/**
   	 * Build request.
   	 *
   	 * @param array
   	 * @param array
   	 * @param array
   	 * @param array
   	 * @param array
   	 * @param array
   	 */
	public function __construct( $parameters = array(), $query = array(), $request = array(), $cookies = array(), $files = array(), $server = array() )
	{
		$this->parameters = $this->request();
		$this->query   	  = $_GET;
		$this->post       = $_POST;
		$this->cookies    = $_COOKIE;
		$this->files      = $_FILES;
		$this->server     = $_SERVER;
		$this->headers    = getallheaders();
	}


	/**
	 * Gets $_Request & php://input
	 *
	 * @return array
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
	 * Valids a given type.
	 *
	 * @param  string
	 * @return void
	 */
	protected function check_type( $type )
	{
		if ( !in_array( $type, $this->types ) )
		{
			throw new \UnexpectedValueException( 'Expected a valid type on request method' );
		}
	}

	/**
	 * Gets a request parameter.
	 *
	 * @param  string
	 * @param  string
	 * @param  string
	 * @return string
	 */
	public function get( $var, $default = '', $type = 'parameters' )
	{
		$this->check_type( $type );

		if ( !isset( $this->{$type}[$var] ) || empty( $this->{$type}[$var] ) )
		{
			return $default;
		}

		return $this->{$type}[$var];
	}

	/**
	 * Check if a request parameter exists.
	 *
	 * @param  string
	 * @param  string
	 * @return bool
	 */
	public function has( $var, $type = 'parameters' )
	{
		return $this->get( $var, null, $type ) !== null;
	}

	/**
	 * Return all the request parameters.
	 *
	 * @param  string
	 * @return mixed
	 */
	public function all( $type = 'parameters' )
	{
		$this->check_type( $type );

		return $this->$type;
	}

	/**
	 * Merges an array into the request parameters.
	 *
	 * @param  array
	 * @param  string
	 * @return void
	 */
	public function merge( $data, $type = 'parameters' )
	{
		$this->check_type( $type );

		$this->{$type} = array_merge( $this->{$type}, $data );
	}

	/**
	 * Returns only the values specified by $keys
	 *
	 * @param  array
	 * @param  string
	 * @return array
	 */
	public function only( $keys, $type = 'parameters' )
	{
		$keys = is_array($keys) ? $keys : array($keys);

        $results = [];

        foreach ($keys as $key)
        {
        	if ( $this->has( $key, $type ) )
        	{
        		$results[ $key ] = $this->get( $key, $type );
        	}
        }

        return $results;
	}

	/**
	 * Return all the request parameters execept values specified by $keys
	 *
	 * @param  array
	 * @param  string
	 * @return array
	 */
	public function except( $keys, $type = 'parameters' )
	{
		$keys = is_array($keys) ? $keys : array($keys);

        $results = $this->all( $type );

        foreach ($keys as $key)
        {
        	if ( isset( $results[$key] ) )
        	{
        		unset( $results[$key] );
        	}
        }

        return $results;
	}

	/**
	 * Gets method used, supporting _method
	 *
	 * @return string
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

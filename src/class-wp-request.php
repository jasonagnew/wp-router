<?php

class WP_Request {


	/**
	* $_Request & php://input
	*
	* @var
	*/
	protected $parameters;

	/**
	* $_GET
	*
	* @var
	*/
	public $query;

	/**
	* $_POST
	*
	* @var
	*/
	public $request;

    /**
     * $_SERVER
     *
     * @var
     */
    public $server;

    /**
     * $_FILES
     *
     * @var
     */
    public $files;

    /**
     * $_COOKIE
     *
     * @var
     */
    public $cookies;

    /**
     * Headers (taken from the $_SERVER).
     *
     * @var
     */
    public $headers;


   	public $types = array(
   		'parameters',
   		'query',
   		'request',
   		'cookies',
   		'files',
   		'server'
   	);



	public function __construct( $parameters = array(), $query = array(), $request = array(), $cookies = array(), $files = array(), $server = array() )
	{
		$this->parameters = $this->request();
		$this->query   	  = $_GET;
		$this->request    = $_POST;
		$this->cookies    = $_COOKIE;
		$this->files      = $_FILES;
		$this->server     = $_SERVER;
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
	 * @param        $var
	 * @param string $default
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
	 * @param $var
	 * @return bool
	 */
	public function has( $var, $type = 'parameters'  )
	{
		return $this->get( $var, null, $type ) !== null;
	}

	/**
	 * Return all the request parameters.
	 *
	 * @return mixed
	 */
	public function all( $type = 'parameters' )
	{
		$this->check_type( $type );

		return $this->$type;
	}

	public function merge( $data, $type = 'parameters' )
	{
		$this->check_type( $type );

		$this->{$type} = array_merge( $this->{$type}, $data );
	}

	public function only( $keys, $type = 'parameters' )
	{
		$keys = is_array($keys) ? $keys : array($keys);

        $results = [];

        foreach ($keys as $key)
        {
        	if ( $this->has( $key, $type ) )
        	{
        		$results[] = $this->get( $key, $type );
        	}
        }

        return $results;
	}

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

<?php

class WP_Router {

    /**
     * Store any created routes.
 	 *
     * @var array
     */
    protected $routes = array(
        'GET' 	 => array(),
		'HEAD'   => array(),
        'POST' 	 => array(),
        'PUT' 	 => array(),
        'PATCH'  => array(),
        'DELETE' => array(),
        'named'  => array()
    );

    /**
     * @var boolean
     */
    protected $testing;

    /**
     * @var WP Request
     */
    protected $request;

    /**
     * @var string
     */
    protected $rewrite_prefix = 'wp_router';

    /**
     * @var string
     */
    protected $parameter_pattern = '/{([\w\d]+)}/';

    /**
     * @var string
     */
    protected $value_pattern = '(?P<$1>[^\/]+)';

    /**
     * @var string
     */
    protected $value_pattern_replace = '([^\/]+)';

    /**
     * @var array
     */
    protected $prefix = '';

    /**
     * @var array
     */
    protected $middlewares = array();

    /**
     * Adds the action hooks for WordPress.
     */
    public function __construct( $testing = false )
    {
    	$this->testing = $testing;
    	$this->request = new WP_Request;

        add_action('wp_loaded', 	array( $this, 'flush' ) );
        add_action('init', 			array( $this, 'boot' ) );
        add_action('parse_request', array( $this, 'parse_request' ) );
    }

    /**
     * Boot the router.
     *
     * @return void
     */
    public function boot()
    {
        add_rewrite_tag("%{$this->rewrite_prefix}_route%", '(.+)');

        $method = $this->request->method();

        foreach ( $this->routes[$method] as $id => $route )
        {
            $this->add_route( $route, $id, $method );
        }
    }


    /**
     * Adds route to the Router.
	 *
     * @param $method
     * @param $attrs
     * @return bool
     */
	public function add( $method, $attrs )
	{
		foreach ( array( 'uri', 'uses' ) as $key )
        {
            if ( !isset( $attrs[$key] ) )
            {
                 throw new InvalidArgumentException( "Missing {$key} definition for route" );
            }
        }

        $attrs['uri'] = ltrim( $attrs['uri'] , '/' );

        if ( isset( $attrs['prefix'] ) )
        {
        	$attrs['prefix'] = $this->prefix . $attrs['prefix'];
        }
        else
        {
        	$attrs['prefix'] = $this->prefix;
        }

        $attrs['prefix'] = ltrim( $attrs['prefix'] , '/' );

        if ( isset( $attrs['middlewares'] ) )
        {
        	$middlewares = $attrs['middlewares'];

        	if ( is_string( $middlewares ) )
			{
				$middlewares = array( $middlewares );
			}

			$attrs['middlewares'] = array_merge($this->middlewares, $middlewares);
        }
        else
        {
        	$attrs['middlewares'] = $this->middlewares;
        }

        $route = apply_filters( 'wp_router_create_route', $attrs, $method );

        $this->routes[$method][] = $route;

        if ( isset( $route['as'] ) )
        {
            $this->routes['named'][$method . '::' . $route['as']] = $route;
        }

        return true;
	}

	/**
	 *	Starts a new router group.
	 *
	 * @param  $attrs
	 * @return void
	 */
	public function group( $attrs )
	{
		if ( isset( $attrs['middlewares'] ) )
		{
			if ( is_string( $attrs['middlewares'] ) )
			{
				$this->middlewares[] = $attrs['middlewares'];
			}
			else
			{
				$this->middlewares = $attrs['middlewares'];
			}
		}

		if ( isset( $attrs['prefix'] ) )
		{
			$this->prefix = $attrs['prefix'];
		}

		$this->fetch( $attrs['uses'], array() );

		$this->middlewares = array();
		$this->prefix = '';
	}

    /**
     * Adds the route to WordPress.
     *
     * @param $route
     * @param $id
     * @param $method
     */
    protected function add_route( $route, $id, $method )
    {
        $params = array(
            'id' => $id,
            'parameters' => array()
        );

        $uri = $route['uri'];

        if( !empty( $route['prefix'] ) )
        {
        	$uri = $route['prefix'] . '/' . $route['uri'];
        }

        $uri = '^' . preg_replace(
            $this->parameter_pattern,
            $this->value_pattern_replace,
            str_replace( '/', '\\/', $uri )
        );

        $url = 'index.php?';

        $matches = [];
        if ( preg_match_all( $this->parameter_pattern, $route['uri'], $matches ) )
        {
            foreach ( $matches[1] as $id => $param )
            {
            	$param_referance = "{$this->rewrite_prefix}_param_{$param}";
        		$url 			.= "{$param_referance}=\$matches[" . ($id + 1) . ']&';

                add_rewrite_tag("%{$param_referance}%", '(.+)');

                $params['parameters'][$param] = null;
            }
        }

        add_rewrite_rule($uri . '$', "{$url}{$this->rewrite_prefix}_route=" . urlencode( json_encode( $params ) ), 'top');
    }

     /**
     * Catches requests and checks if they contain 'wp_router_route'
     * before passing them to 'process_request'
     *
     * @param $direct
     * @param $wp
     */
    public function parse_request( $wp, $direct = false )
    {
    	if ( $this->testing && !$direct )
    	{
    		return;
    	}

    	$route_key = "{$this->rewrite_prefix}_route";

        if ( !array_key_exists( $route_key, $wp->query_vars ) )
        {
            return;
        }

        $data  		= @json_decode( $wp->query_vars[$route_key], true );
        $route 		= null;
        $id 		= null;
        $name 		= null;
        $parameters = null;

        foreach( array('id', 'name', 'parameters' ) as $key )
        {
			if ( isset( $data[$key] ) )
			{
				$$key= $data[$key];
			}
        }

		$method = $this->request->method();

        if ( isset( $this->routes[$method][$id] ) )
        {
            $route = $this->routes[$method][$id];
        }
        elseif ( isset( $this->routes['named'][$name] ) )
        {
            $route = $this->routes['named'][$name];
        }

        if ( !isset( $route ) )
        {
            return;
        }

        foreach ($parameters as $key => $val)
        {
        	$reference = "{$this->rewrite_prefix}_param_{$key}";

            if ( !isset( $wp->query_vars[$reference] ) )
            {
                return;
            }
            $parameters[$key] = $wp->query_vars[$reference];
        }

        $response = $this->process_request( $route, $parameters );

        if ( $this->testing )
    	{
    		return $response;
    	}

       	die;
    }

    /**
     * Handles the response of the route.
     *
     * @param  $route
     * @param  $args
     */
    public function process_request( $route, $args = array() )
    {
    	$request = new WP_Request;
    	$request->merge( $args );

		$store = array(
			'middlewares' => $route['middlewares'],
			'route' => $route,
			'args' => $args
		);

    	return $this->next( $request, $this, $store, true );
    }

    public function next( $request, $router, $store, $first = false )
    {
    	if ( (isset( $store['middlewares'][0] ) && $first) || isset( $store['middlewares'][1] ) )
    	{
	    	if ( !$first )
	    	{
	    		array_shift( $store['middlewares'] );
	    	}

	    	$response = $this->fetch( $store['middlewares'][0] .'@run', array(
	    		$request,
	    		$this,
	    		$store
			) );
    	}
    	else
    	{
    		$store['args']['request'] = $request;
    		$response = $this->fetch( $store['route']['uses'], $store['args'] );
    	}

    	if ( $this->testing )
    	{
    		return $response;
    	}

    	echo $response;
    }

    /**
     * Fetches a controller or callbacks response.
     *
     * @param $callback
     * @param array $args
     * @return mixed
     */
    public function fetch( $callback, $args = array() )
    {
        if ( is_string( $callback ) )
        {
            list( $class, $method ) = explode( '@', $callback, 2 );

            $controller = new $class;

            return call_user_func_array( array( $controller, $method ), $args );
        }

        return call_user_func_array( $callback, $args );
    }

    /**
     * Flushes WordPress's rewrite rules.
     *
     * @return void
     */
    public function flush()
    {
        flush_rewrite_rules();
    }

    /**
     * Returns the route by name.
     */
	public function name( $name )
	{
		$methods = array_keys( $this->routes );

		foreach ( $methods as $method )
		{
			if ( isset( $this->routes['named'][$method .'::'. $name] ) )
			{
				return array_merge( $this->routes['named'][$method .'::'. $name], array( 'method' => $method ) );
			}
		}

		return false;
	}

    /**
     * Returns the uri of named route.
     */
	public function uri( $name )
	{
		$route = $this->name( $name );

		if ( $route )
		{
			if( !empty( $route['prefix'] ) )
	        {
	        	return $route['prefix'] . '/' . $route['uri'];
	        }

			return $route['uri'];
		}

		return false;
	}

	/**
     * Returns the url of named route.
     */
	public function url( $name )
	{
		$uri = $this->uri( $name );
		$uri = ltrim( $uri, '/' );

		if ( $uri )
		{
			return get_bloginfo( 'url' ) . '/' . $uri;
		}

		return false;
	}

	/**
     * Returns the method of named route.
     */
	public function method( $name )
	{
		$route = $this->name( $name );

		if ( $route )
		{
			return $route['method'];
		}

		return false;
	}

    /**
     * Helper method for adding route.
     */
	public function get( $attrs )
	{
		return $this->add( 'GET', $attrs );
	}

    /**
     * Helper method for adding route.
     */
	public function post( $attrs )
	{
		return $this->add( 'POST', $attrs );
	}

    /**
     * Helper method for adding route.
     */
	public function put( $attrs )
	{
		return $this->add( 'PUT', $attrs );
	}

    /**
     * Helper method for adding route.
     */
	public function patch( $attrs )
	{
		return $this->add( 'PATCH', $attrs );
	}

    /**
     * Helper method for adding route.
     */
	public function delete( $attrs )
	{
		return $this->add( 'DELETE', $attrs );
	}
}

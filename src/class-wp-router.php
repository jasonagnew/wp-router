<?php

class WP_Router {

    /**
     * Store any created routes.
 	 *
     * @var array
     */
    protected $routes = array(
        'GET' 	 => array(),
        'POST' 	 => array(),
        'PUT' 	 => array(),
        'PATCH'  => array(),
        'DELETE' => array(),
        'named'  => array()
    );

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
     * Adds the action hooks for WordPress.
     */
    public function __construct()
    {
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

        $method = 'GET';

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
                return new WP_Error( 'missing-attr', "Missing {$key} definition for route" );
            }
        }

        $attrs['uri'] = ltrim( $attrs['uri'] , '/' );

        $route = apply_filters( 'wp_router_create_route', $attrs, $method );

        $this->routes[$method][] = $route;

        if ( isset( $route['as'] ) )
        {
            $this->routes['named'][$method . '::' . $route['as']] = $route;
        }

        return true;
	}

    /**
     * Adds the route to WordPress.
     *
     * @param $route
     * @param $id
     * @param $method
     */
    protected function add_route($route, $id, $method)
    {
        $params = array(
            'id' => $id,
            'parameters' => array()
        );

        $uri = '^' . preg_replace(
            $this->parameter_pattern,
            $this->value_pattern_replace,
            str_replace( '/', '\\/', $route['uri'] )
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
     * @param $wp
     */
    public function parse_request($wp)
    {
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

		$method = 'GET';

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

        $this->process_request($route, $parameters);

       	die;
    }

     /**
     * Handles the response of the route.
     *
     * @param $wp
     */
    public function process_request( $route, $args )
    {
    	 $response = $this->fetch( $route['uses'], $args );

    	 //if json etc.

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

            if ( !empty( $args ) )
            {
                return call_user_func_array( array( $controller, $method ), $args);
            }

            return $controller->$method();
        }

        if ( !empty( $args ) )
        {
            return call_user_func_array( $callback, $args );
        }

        return $callback();
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

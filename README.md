# WP-Router

### Example

```
<?php

class Middleware_One extends WP_Middleware {

    public function handle(WP_Request $request)
    {
        if ($request->get('id') == 1)
        {
            return 'No';
        }

        return $this->next($request);
    }
}

class Route_Test {

    protected $router;

    public function __construct()
    {
        global $wp_router;

        $this->router  = $wp_router;

        $this->register_routes();
    }

    public function register_protected_routes()
    {
        $this->router->get( array(
            'as'   => 'getTest',
            'uri'  => 'test/{id}',
            'uses' => array( $this, 'get' )
        ) );
    }

    protected function register_routes()
    {
        $this->router->group( array(
            'prefix' => '/protected',
            'middlewares' => array( 'Middleware_One' ),
            'uses' => array( $this, 'register_protected_routes' )
        ) );

        $this->router->post( array(
            'as'   => 'postTest',
            'uri'  => '/test/{id}',
            'uses' => array( $this, 'post' ),
            'prefix' => ''
        ) );

        $this->router->put( array(
            'as'   => 'putTest',
            'uri'  => '/test/{id}',
            'uses' => array( $this, 'put' )
        ) );
    }

    public function get($id, WP_Request $request)
    {
        $all = $request->all();

        return new WP_JSON_Response($all);
    }

    public function post($id)
    {
        return 'POST: The ID is ' . $id;
    }

    public function put($id)
    {
        return 'PUT: The ID is ' . $id;
    }

}

new Route_Test();
```

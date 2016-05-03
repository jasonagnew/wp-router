# WP-Router

You can acccess the router at any point using the $wp_router global

``` php
global $wp_router
```

Defining a simple route is simple, routes consist of a Name, URI and a Closure callback. The URI will be appended to the site url, for example: `http://example.com/simple`

#### Basic GET Route

``` php
$wp_router->get( array(
	'as'   => 'simpleRoute',
	'uri'  => '/simple',
	'uses' => function()
	{
		return 'Hello World';
	}
) );
```

#### Basic POST Route

``` php
$wp_router->post( array(
	'as'   => 'simpleRoute',
	'uri'  => '/simple',
	'uses' => function()
	{
		return 'Hello World';
	}
) );
```

#### Support for PUT, DELETE and PATCH

```
$wp_router->put();
$wp_router->delete();
$wp_router->patch();
```

#### Using functions, class methods or controllers

``` php
$wp_router->get( array(
	'as'   => 'simpleRoute',
	'uri'  => '/simple',
	'uses' => 'my_function'
) );
```

``` php
$wp_router->get( array(
	'as'   => 'simpleRoute',
	'uri'  => '/simple',
	'uses' => array( $this, 'method' )
) ;
```

``` php
$wp_router->get( array(
	'as'   => 'simpleRoute',
	'uri'  => '/simple',
	'uses' => __NAMESPACE__ . '\Controllers\SampleController@method'
) ;
```

## Route Parameters

You can set route parameters in your URI by defining as `{param}`. These parameters then be accessed by your Closure or Controller as `$param`

``` php
$wp_router->get( array(
	'as'   => 'userProfile',
	'uri'  => '/user/{id}',
	'uses' => function($id)
	{
		return "User: {$id}";
	}
) );
```

## Route Middleware

You set up middleware which runs when your route is accessed before the Closure is run. You can run more than one and are passed as an array.

``` php
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
```
``` php
$wp_router->get( array(
	'as'   => 'simpleRoute',
	'uri'  => '/simple',
	'middlewares' => array( 'Middleware_One' ),
	'uses' => __NAMESPACE__ . '\Controllers\SampleController@method'
) );
```

## Overall Example

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

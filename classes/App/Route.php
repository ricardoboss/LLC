<?php

namespace App;

use App\Controllers\Controller;
use Exceptions\LeveledException;
use Exceptions\RoutingException;

class Route {
	const TYPE_GET = "GET";
	const TYPE_POST = "POST";
	const TYPE_PUT = "PUT";
	const TYPE_HEAD = "HEAD";

	private static $routes = array(
		Route::TYPE_GET => array(
			'routes' => array(
			)
		),
	    Route::TYPE_POST => array(),
	    Route::TYPE_PUT => array(),
	    Route::TYPE_HEAD => array()
	);
	/** @var callable|null A callback method for 404 errors. */
	private static $callback404 = null;

	/**
	 * @return array All routes that have been registered.
	 */
	public static function getRoutes() {
		return static::$routes;
	}

	/**
	 * @param callable $callback A callback method to be called when a page cannot be found.
	 *
	 * @throws \Exceptions\LeveledException If the callback is not callable.
	 */
	public static function set404(callable $callback) {
		if (!is_callable($callback))
			throw new LeveledException("Callback is not callable!", LeveledException::LEVEL_INFO);

		static::$callback404 = $callback;
	}

	/** @noinspection PhpUnusedPrivateMethodInspection
	 *
	 * @param string $uri The called uri.
	 */
	private static function error404(string $uri) {
		$callback = static::$callback404;

		$callback($uri);
	}

	/**
	 * @param \App\Route $route The route to be added.
	 *
	 * @throws \Exceptions\RoutingException If the route has already been specified.
	 */
	private static function addRoute(Route $route) {
		if (array_key_exists($route->route, static::$routes[$route->type])) {
			throw new RoutingException(
				"Route is already specified!",
				RoutingException::ALREADY_SPECIFIED
			);
		}

		static::$routes[$route->type][$route->route] = $route;
	}

	/**
	 * @param \App\Route $route   The route to specify aliases for.
	 * @param array      $aliases An array of aliases.
	 *
	 * @throws \Exceptions\RoutingException
	 */
	private static function addAliases(Route $route, array $aliases) {
		foreach ($aliases as $alias) {
			if (array_key_exists($alias, static::$routes[$route->type])) {
				throw new RoutingException(
					"Route is already specified!",
					RoutingException::ALREADY_SPECIFIED
				);
			}

			static::$routes[$route->type][$alias] = $route->route;
		}
	}

	/**
	 * Register a route for a get request.
	 *
	 * @param string $route                The route for the GET request.
	 * @param string $controllerOrTemplate The controller and the method to be used to handle the request:
	 *                                     'class{at}staticMethod'. Alternatively, you can specify a template path:
	 *                                     'app.master'.
	 * @param array $aliases               An optional array which contains aliases for a route (i.e. routes will be
	 *                                     redirected to the route specified).
	 */
	public static function get(string $route, string $controllerOrTemplate, array $aliases = array()) {
		new Route($controllerOrTemplate, $route, Route::TYPE_GET, $aliases);
	}

	/**
	 * Register a route for a post request.
	 *
	 * @param string $route                The route for the POST request.
	 * @param string $controllerOrTemplate The controller and the method to be used to handle the request:
	 *                                     'class{at}staticMethod'. Alternatively, you can specify a template path:
	 *                                     'app.master'.
	 * @param array $aliases               An optional array which contains aliases for a route (i.e. routes will be
	 *                                     redirected to the route specified).
	 *
	 * @throws \Exceptions\RoutingException If the controller is invalid.
	 */
	public static function post(string $route, string $controllerOrTemplate, array $aliases = array()) {
		new Route($controllerOrTemplate, $route, Route::TYPE_POST, $aliases);
	}

	/**
	 * Register a route for a put request.
	 *
	 * @param string $route                The route for the PUT request.
	 * @param string $controllerOrTemplate The controller and the method to be used to handle the request:
	 *                                     'class{at}staticMethod'. Alternatively, you can specify a template path:
	 *                                     'app.master'.
	 * @param array $aliases               An optional array which contains aliases for a route (i.e. routes will be
	 *                                     redirected to the route specified).
	 */
	public static function put(string $route, string $controllerOrTemplate, array $aliases = array()) {
		new Route($controllerOrTemplate, $route, Route::TYPE_PUT, $aliases);
	}

	/**
	 * Register a route for a head request.
	 *
	 * @param string $route                The route for the HEAD request.
	 * @param string $controllerOrTemplate The controller and the method to be used to handle the request:
	 *                                     'class{at}staticMethod'. Alternatively, you can specify a template path:
	 *                                     'app.master'.
	 * @param array $aliases               An optional array which contains aliases for a route (i.e. routes will be
	 *                                     redirected to the route specified).
	 */
	public static function head(string $route, string $controllerOrTemplate, array $aliases = array()) {
		new Route($controllerOrTemplate, $route, Route::TYPE_HEAD, $aliases);
	}

	/**
	 * Handle the current request. Call this after registering all routes.
	 */
	public static function handle() {
		$uri = $_SERVER['REQUEST_URI'];

		$file = $_SERVER['DOCUMENT_ROOT'] . substr($uri, 1);
		if (
			file_exists($file) &&
			!is_dir($file) &&
			!in_array(
				pathinfo($file, PATHINFO_EXTENSION),
				array(
					"php",
				    "phtml"
				)
			)
		)
			die(file_get_contents($file));

		$method = strtoupper($_SERVER['REQUEST_METHOD']);
		$routes = static::$routes[$method];

		if (array_key_exists($uri, $routes)) {
			$route = $routes[$uri];

			// check if route is a Route object which can be processed or an alias for a route (string).
			if (!is_string($route) && get_class($route) == Route::class) {
				/** @noinspection PhpUndefinedMethodInspection */
				$callback = $route->getCallback();

				if (get_parent_class($callback['class']) == Controller::class) {
					if ($method == Route::TYPE_GET)
						$callback['arguments'] = array($_GET);
					else if ($method == Route::TYPE_POST)
						$callback['arguments'] = array($_POST);
				}
			} else {
				assert(is_string($route), "Invalid type for route");

				header("Location: " . $route);

				die;
			}
		} else {
			http_response_code(404);

			if (isset(static::$callback404) && static::$callback404 !== null)
				$callback = array(
					'class' => Route::class,
				    'method' => "error404",
				    'arguments' => array(
				        $uri
				    )
				);
			else
				die("File \"" . $uri . "\" not found on this server");
		}

		call_user_func_array(
			array(
				$callback['class'],
				$callback['method']
			),
			$callback['arguments']
		);

		die;
	}

	/**
	 * @param string $uri The uri to redirect to.
	 */
	public static function redirect(string $uri) {
		header("Location: " . $uri);
	}

	private $callback;
	private $route;
	private $type;

	/**
	 * Route constructor.
	 *
	 * @param string $controllerOrTemplate The controller and the method to be used to handle the request:
	 *                                     'class{at}staticMethod'. Alternatively, you can specify a template path:
	 *                                     'app.master'.
	 * @param string $route                The route to be bound to this controller.
	 * @param string $type                 The type of this route (either 'GET', 'POST', 'PUT' or 'HEAD').
	 *
	 * @param array  $aliases              An optional array to specify aliases for this route.
	 *
	 * @throws \Exceptions\RoutingException If the controller is invalid.
	 */
	private function __construct(string $controllerOrTemplate, string $route, string $type, array $aliases = array()) {
		if ($type !== Route::TYPE_POST && $type !== Route::TYPE_GET)
			throw new RoutingException(
				"Invalid type specified!",
				RoutingException::INVALID_TYPE
			);

		// Check if a template or a controller should be used:
		// Search for '@' in controller string (if none is found, the string must be a template)
		// Next, search for the template
		if (strpos($controllerOrTemplate, "@") === false || Template::exists($controllerOrTemplate)) {
			$callback = array(
				'class' => Template::class,
			    'method' => 'display', // \Template::display();
			    'arguments' => array(
			    	$controllerOrTemplate
			    )
			);
		} else if (strpos($controllerOrTemplate, "@") !== false) {
			$methodPath = explode("@", $controllerOrTemplate);

			assert(count($methodPath) == 2, "Invalid controller format: must be like: \"controller@method\"");

			$callback = array(
				'class' => "\\App\\Controllers\\" . $methodPath[0],
			    'method' => $methodPath[1],
			    'arguments' => array()
			);

			if (
				!is_callable(
					array(
						$callback['class'],
						$callback['method']
					)
				)
			)
				throw new RoutingException(
					"Invalid controller string: method '{$callback['class']}::{$callback['method']}' is not callable!",
					RoutingException::INVALID_CONTROLLER
				);
		} else
			throw new RoutingException(
				"Invalid controller specified: is neither a template nor a valid controller method!",
				RoutingException::INVALID_CONTROLLER
			);

		$this->callback = $callback;
		$this->route = $route;
		$this->type = $type;

		static::addRoute($this);
		static::addAliases($this, $aliases);
	}

	/**
	 * @return array An array containing all the information to use when calling the method for this route.
	 */
	public function getCallback(): array {
		return $this->callback;
	}

	/**
	 * @return string The route string.
	 */
	public function getRoute(): string {
		return $this->route;
	}

	/**
	 * @return string The type of this route.
	 */
	public function getType(): string {
		return $this->type;
	}
}
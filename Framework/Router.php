<?php

namespace Framework;

use App\Controllers\ErrorController;

class Router
{
    private const URI_PARAM_PATTERN = '/\{(.+?)\}/';
    protected $routes = [];

    // todo: maybe this should be private??
    public function registerRoute($method, $uri, $action)
    {
        list($controller, $controllerMethod) = explode('@', $action);
        $this->routes[] = [
            'method' => $method,
            'uri' => $uri,
            'controller' => $controller,
            'controllerMethod' =>  $controllerMethod
        ];
    }

    /**
     * Add a new GET route
     *
     * @param string $uri
     * @param string $controller
     * @return void
     */
    public function get($uri, $controller)
    {
        $this->registerRoute('GET', $uri, $controller);
    }

    /**
     * Add a new POST route
     *
     * @param string $uri
     * @param string $controller
     * @return void
     */
    public function post($uri, $controller)
    {
        $this->registerRoute('POST', $uri, $controller);
    }

    /**
     * Add a new PUT route
     *
     * @param string $uri
     * @param string $controller
     * @return void
     */
    public function put($uri, $controller)
    {
        $this->registerRoute('PUT', $uri, $controller);
    }

    /**
     * Add a new DELETE route
     *
     * @param string $uri
     * @param string $controller
     * @return void
     */
    public function delete($uri, $controller)
    {
        $this->registerRoute('DELETE', $uri, $controller);
    }

    /**
     * Match the request to the route defined by $this->routes.
     * If no match is found, show 404.
     *
     * @param string $uri
     * @param string $method
     * @return void
     */
    public function route($uri = '')
    {
        $requestMethod = self::getRequestMethod();

        // Split this current URI into segments
        $uriSegments = explode('/', trim($uri, '/'));

        foreach ($this->routes as $route) {

            // Split the route URI into segments
            $routeSegments = explode('/', trim($route['uri'], '/'));

            $match = true;

            // If the number of segments in the URI and the route don't match, skip this route
            if (count($uriSegments) !== count($routeSegments)) {
                $match = false;
                continue;
            }

            // If the request method doesn't match, skip this route
            if (strtoupper($route['method']) !== $requestMethod) {
                $match = false;
                continue;
            }

            $params = [];

            for ($i = 0; $i < count($uriSegments); $i++) {

                // Break if the segments do not match and there is no {} param
                if ($routeSegments[$i] !== $uriSegments[$i] && !preg_match(self::URI_PARAM_PATTERN, $routeSegments[$i], $paramMatches)) {
                    $match = false;
                    break;
                }

                // If the router is using params, set the value
                if ($paramMatches ?? false) {
                    $params[$paramMatches[1]] = $uriSegments[$i];
                }
            }

            if ($match) {
                $controller = 'App\\Controllers\\' . $route['controller'];
                $controllerMethod = $route['controllerMethod'];

                // For example, (new ListingController())->index()
                (new $controller())->$controllerMethod($params);
                return;
            }
        }

        ErrorController::notFound();
    }

    /**
     * Get the request method and check for a method override
     *
     * @return string
     */
    public static function getRequestMethod()
    {
        $requestMethod = $_SERVER['REQUEST_METHOD'];

        // Check if the request method is being overridden, commonly used for DELETE and PUT requests
        if ($requestMethod === 'POST' && isset($_POST['_method'])) {
            $requestMethod = strtoupper($_POST['_method']);
        }

        return $requestMethod;
    }
}

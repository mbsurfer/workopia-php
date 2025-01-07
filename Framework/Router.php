<?php

namespace Framework;

use App\Controllers\ErrorController;
use Framework\Middleware\Authorize;

class Router
{
    private const URI_PARAM_PATTERN = '/\{(.+?)\}/';
    protected $routes = [];

    // todo: maybe this should be private??
    /**
     * Add a new route to the $this->routes array
     *
     * @param string $method
     * @param string $uri
     * @param string $action
     * @param array $middeleware
     * @return void
     */
    public function registerRoute($method, $uri, $action, $middeleware = [])
    {
        list($controller, $controllerMethod) = explode('@', $action);
        $this->routes[] = [
            'method' => $method,
            'uri' => $uri,
            'controller' => $controller,
            'controllerMethod' =>  $controllerMethod,
            'middleware' => $middeleware
        ];
    }

    /**
     * Add a new GET route
     *
     * @param string $uri
     * @param string $controller
     * @param array $middeleware
     * @return void
     */
    public function get($uri, $controller, $middeleware = [])
    {
        $this->registerRoute('GET', $uri, $controller, $middeleware);
    }

    /**
     * Add a new POST route
     *
     * @param string $uri
     * @param string $controller
     * @param array $middeleware
     * @return void
     */
    public function post($uri, $controller, $middeleware = [])
    {
        $this->registerRoute('POST', $uri, $controller, $middeleware);
    }

    /**
     * Add a new PUT route
     *
     * @param string $uri
     * @param string $controller
     * @param array $middeleware
     * @return void
     */
    public function put($uri, $controller, $middeleware = [])
    {
        $this->registerRoute('PUT', $uri, $controller, $middeleware);
    }

    /**
     * Add a new DELETE route
     *
     * @param string $uri
     * @param string $controller
     * @param array $middeleware
     * @return void
     */
    public function delete($uri, $controller, $middeleware = [])
    {
        $this->registerRoute('DELETE', $uri, $controller, $middeleware);
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

                // Check if the route has a role(s) associated with it
                foreach ($route['middleware'] as $role) {
                    (new Authorize())->handle($role);
                }

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

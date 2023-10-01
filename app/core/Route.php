<?php
namespace App\Core;

class Route
{
    /**
     * Current request URI
     * @var string
     */
    private string $requestUri;

    /**
     * Current request method
     * @var string
     */
    private string $requestMethod;

    /**
     * Current request parameters
     * @var array
     */
    public array $requestParameters;

    /**
     * Routes contianer
     * @var array
     */
    public array $routes = [];

    /**
     * Regular expression to get dynamic variables
     * @var string
     */
    private const DYNAMIC_VAR_FORMAT = '/\{([a-zA-Z0-9_]+)\}/';

    /**
     * Placeholder of the dynamic variable
     * @var string
     */
    private const DYNAMIC_VAR_PLACEHOLDER = '([^/]+)';

    /**
     * Assign properties
     */
    public function __construct() {
        $this->requestUri = $_SERVER['REQUEST_URI'];
        $this->requestMethod = $_SERVER['REQUEST_METHOD'];
        $this->requestParameters = $_REQUEST;
    }

    /**
     * Set GET request route
     * 
     * @param string $path
     * @param mixed $callback
     * @return void
     */
    public function get(string $path, $callback) : void {
        $this->setRoute($path, "GET", $callback);
    }
    
    /**
     * Set POST request route
     * 
     * @param string $path
     * @param mixed $callback
     * @return void
     */
    public function post(string $path, $callback) : void {
        $this->setRoute($path, "POST", $callback);
    }
    
    /**
     * Run all routes 
     * 
     * @return void
     */
    public function dispatch(): void
    {
        foreach ($this->routes as $route) {
            extract($route);
            if ($this->isRouteMatched($path) && $this->requestMethod === $method) {                
                $dynamicVariables = $this->getDynamicVariables($path);
                
                // Pass dynamic variables and request parameters into the callback
                array_unshift($dynamicVariables, $this->requestParameters);
                call_user_func_array($callback, $dynamicVariables);
                
                break;
            }
            
        }

        // If no route matches, return a 404 response
        http_response_code(404);
    }

    /**
     * ------------------------------------------------------------
     * | Helper functions
     * ------------------------------------------------------------
     */
    
    /**
     * Set routes data
     * 
     * @param string $path
     * @param string $method
     * @param mixed $callback
     * @return void
     */
    private function setRoute(string $path, string $method, $callback) : void {
        $this->routes[] = [
            'path' => $path,
            'method' => $method,
            'callback' => $callback,
        ];
    }

    /**
     * Return raw format of the route
     * 
     * @param string $path
     * @return string
     */
    private function getSearchPattern(string $path) : string {
        $routeRawFormat = preg_replace(self::DYNAMIC_VAR_FORMAT, self::DYNAMIC_VAR_PLACEHOLDER, $path);
        $searchPattern = "#^$routeRawFormat$#";

        return $searchPattern;
    }

    /**
     * Sanitize special characters from the array values
     * 
     * @param array $dynamicVariables
     * @return void
     */
    private function sanitizeValues(array &$dynamicVariables) : void {
        array_walk($dynamicVariables, function (&$variable) {
            $variable = htmlspecialchars($variable, ENT_QUOTES, 'UTF-8');
        });
    }

    /**
     * Check given route is matched to server uri
     * 
     * @param string $route
     * @return bool
     */
    private function isRouteMatched(string $route) : bool {
        $routeFormat = $this->getSearchPattern($route);
        $extractedRoute = explode("?", $this->requestUri)[0];

        return preg_match($routeFormat, $extractedRoute);
    }

    /**
     * Return dynamic variable values from the route
     * 
     * @param string $route
     * @return array
     */
    private function getDynamicVariables(string $route) : array {
        $dynamicVariables = [];

        if ($this->isRouteMatched($route)) {
            $routeFormat = $this->getSearchPattern($route);
            preg_match($routeFormat, $this->requestUri, $dynamicVariables);
            array_shift($dynamicVariables);
            $this->sanitizeValues($dynamicVariables);
        }
        
        return $dynamicVariables;
    }

}

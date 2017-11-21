<?php
namespace Core;
/**
 * Class Router
 */
class Router
{
    protected $routes = [];
    protected $params = [];

    /**
     * Adds a route to the route table. The route strings are converted into regex for later route matching.
     * @param $route string
     * @param array $params
     */
    public function add($route, $params = [])
    {
        // Escape forward slashes in the route to covert into a regular expression
        $route = preg_replace('/\//', '\\/', $route);
        // Convert words surrounded in {} into capture groups
        $route = preg_replace('/\{([a-z]+)\}/', '(?P<\1>[a-z-]+)', $route);
        // Convert routes that contains a custom regular expression
        $route = preg_replace('/\{([a-z]+):([^\}]+)\}/', '(?P<\1>\2)', $route);
        // Add case-insensitive, start and end delimiters;
        $route = '/^' . $route . '$/i';

        $this->routes[$route] = $params;
    }

    /**
     * Matches the current route against the regex in the route table and pull out the params.
     * @param $url
     * @return bool
     */
    public function match($url)
    {
        //$reg_exp = "/^(?P<controller>[a-z-]+)\/(?P<action>[a-z-]+)$/";
        // loop through the routes table
        foreach($this->routes as $route => $params)
        {
            // test url against the route as regular expression match
            if(preg_match($route, $url, $matches))
            {
                foreach ($matches as $key => $match)
                {
                    if(is_string($key))
                    {
                        $params[$key] = $match;
                    }
                }
                $this->params = $params;
                return true;
            }
        }
        return false;
    }

    /**
     * Get the parameters
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * get the route table
     * @return array
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * Dispatch a url. Checks to see if the url matches a route, if so, creates the matching controller, and executes the
     * action.
     * @param $url
     * @throws \Exception
     */
    public function dispatch($url)
    {
        // clean the query variables
        $url = $this->removeQueryStringVariables($url);

        // if the url matches
        if($this->match($url))
        {
            // get the controller parameter and convert to studley format
            $controller = $this->params['controller'];
            $controller = $this->convertToStudleyCaps($controller);
            $controller = $this->getNamespace() . $controller;

            if(class_exists($controller))
            {
                $controller_object = new $controller($this->params);
                // default action is index if none is defined
                $action = (!array_key_exists('action', $this->params)) ? 'index' : $this->params['action'];
                $action = $this->convertToCamelCase($action);
                // security check to see if they are trying to access our actions without filtering
                if(preg_match('/action$/i', $action) == 0)
                {
                    $controller_object->$action();
                } else {
                    throw new \Exception("Method $action in controller cannot be called directly");
                }
            } else {
                throw new \Exception("Controller class $controller not found", 404);
            }
        } else {
            throw new \Exception("no route matched", 404);
        }

    }

    /**
     * converts a string to StudleyCaps: this-is-a-route -> ThisIsARoute
     * @param $string
     * @return mixed
     */
    protected function convertToStudleyCaps($string)
    {
        $separatedWords = str_replace('-', ' ', $string);
        $uppercaseFirstLetter = ucwords($separatedWords);
        $studleyCaps = str_replace(' ', '', $uppercaseFirstLetter);
        return $studleyCaps;
    }

    /**
     * converts a string to camel case: ThisIsARoute -> thisISARoute
     * @param $string
     * @return string
     */
    protected function convertToCamelCase($string)
    {
        return lcfirst($this->convertToStudleyCaps($string));
    }

    /**
     * Takes the url and removes the query string variables from the end to give a clean route
     * @param $url
     * @return string
     */
    protected function removeQueryStringVariables($url)
    {
        if($url != '')
        {
            $parts = explode('&', $url, 2);
            if(strpos($parts[0], '=') === false)
            {
                $url = $parts[0];
            } else {
                $url = '';
            }
        }

        return $url;
    }

    /**
     * Gets the namespace
     * @return string
     */
    protected function getNamespace()
    {
        // Default namespace
        $namespace = 'App\Controllers\\';

        if(array_key_exists('namespace', $this->params))
        {
            $namespace .= $this->params['namespace'] . '\\';
        }
        return $namespace;
    }

}
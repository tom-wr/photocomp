<?php

namespace Core;

use Core\Auth;

abstract class Controller
{
    protected $route_params = [];

    /**
     * Controller constructor.
     * @param $route_params The route parameters
     */
    public function __construct($route_params)
    {
        $this->route_params = $route_params;
    }

    /**
     * Magic method to pick up any none existent method calls on the controller.
     * Appends the name of the method called with 'Action' to see if there is a legitimate
     * controller action. All actions called from a route must therefore be named as *Action.
     * @param $name string the name of the method being called
     * @param $args [] the arguments accompanying the call
     * @throws \Exception
     */
    public function __call($name, $args)
    {
        $method = $name . 'Action';

        if(method_exists($this, $method))
        {
            // call the preprocess method
            if($this->before() !== false)
            {
                // call the method with given arguments
                call_user_func_array([$this, $method], $args);
                // call the post process method
                $this->after();
            }
        } else {
            throw new \Exception("Method $method not found in controller " . get_class($this));
        }
    }

    /**
     * Preprocessor function is called before the main action
     */
    protected function before()
    {

    }

    /**
     * post processor function is called after the main action
     */
    protected function after()
    {

    }

    /**
     * Initiates a redirect to a given url
     * @param $url string
     */
    protected function redirect($url)
    {
        header('Location: http://' . $_SERVER['HTTP_HOST'] . $url, true, 303);
        exit;
    }

    /**
     * Encodes and sends json
     * @param $data
     */
    protected function json($data)
    {
        header('Content-Type: application/json');
        echo json_encode($data);
    }

    /**
     * Assign current action a requiring a login. Checks to see if the user is logged in else redirects to the login page.
     */
    protected function requireLogin()
    {
        if(!Auth::getUser())
        {
            Flasher::addMessage('Please login for access', Flasher::INFO);
            Auth::rememberRequestedPage();
            $this->redirect('/login');
        }
    }
}
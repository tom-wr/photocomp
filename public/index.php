<?php
/**
 * FRONT CONTROLLER
 */

//composer autoloading
require '../vendor/autoload.php';

use Core\Router;

// Set error reporting
error_reporting(E_ALL);
set_error_handler('Core\Error::errorHandler');
set_exception_handler('Core\Error::exceptionHandler');

// Start the session
session_start();

// Start router and initialise routes

$router = new Router();

$router->add('', ['controller' => 'Pages', 'action' => 'home']);
$router->add('login', ['controller' => 'Users', 'action' => 'login']);
$router->add('logout', ['controller' => 'Users', 'action' => 'logout']);
$router->add('signup', ['controller' => 'Users', 'action' => 'signup']);
$router->add('submit', ['controller' => 'Photos', 'action' => 'submit']);
$router->add('{controller}');
$router->add('{controller}/{action}');
$router->add('photos/{id:\d+}/show', ['controller' => 'Photos', 'action' => 'show']);

$router->dispatch($_SERVER['QUERY_STRING']);/*


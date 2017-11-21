<?php

namespace Core;


use Core\Auth;

class View
{
    /**
     * Renders a twig template view
     * @param $view
     * @param array $args
     */
    public static function render($view, $args = [])
    {
        static $twig = null;

        if($twig === null)
        {
            $loader = new \Twig_Loader_Filesystem('../App/Views');
            $twig = new \Twig_Environment($loader);

            // Set twig globals
            $twig->addGlobal('current_user', Auth::getUser());
            $twig->addGlobal('flash', Flasher::getMessages());
        }

        echo $twig->render($view, $args);

    }

}
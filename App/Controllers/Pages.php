<?php

namespace App\Controllers;

use Core\Controller;
use Core\View;

/**
 * Class Pages
 * The pages controller
 * @package App\Controllers
 */
class Pages extends Controller
{

    /**
     * Renders the hoe page template
     */
    public function homeAction()
    {
        View::render('Pages/home.html.twig');
    }
}
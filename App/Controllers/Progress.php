<?php
/**
 * Created by PhpStorm.
 * User: tomto
 * Date: 22/11/17
 * Time: 22:51
 */

namespace App\Controllers;

use Core\Controller;

class Progress extends Controller
{
    public function __construct($route_params)
    {
        parent::__construct($route_params);
    }

    public function progressAction()
    {
        $key = ini_get("session.upload_progress.prefix") . "myForm";
        if (!empty($_SESSION[$key])) {
            $current = $_SESSION[$key]["bytes_processed"];
            $total = $_SESSION[$key]["content_length"];
            echo $current < $total ? ceil($current / $total * 100) : 100;
            die;
        }
        else {
            echo 100;
        }
    }

}
<?php

namespace App\Controllers;

use Core\Auth;
use App\Models\User;
use Core\Controller;
use Core\Flasher;
use Core\View;
use Facebook\Facebook;

/**
 * Class Users
 * The users controller
 * @package App\Controllers
 */
class Users extends Controller
{
    /**
     * Render the signup form
     */
    public function signupAction()
    {
        View::render('Users/signup.html.twig');
    }

    /**
     * Render the login form
     */
    public function loginAction()
    {
        /*$fb = new Facebook([
            'app_id' => '156026275012289',
            'app_secret' => '9de92b9e29f5ac3f785c2d96b60b386a',
            'default_graph_version' => 'v2.2',
        ]);
        $helper = $fb->getRedirectLoginHelper();
        $permissions = ['email'];
        $loginUrl = $helper->getLoginUrl('http://localhost/facebook-login', $permissions);*/
        View::render('Users/login.html.twig');//, ['facebook_login' => $loginUrl]);
    }

    /**
     * Create a new user from the post data
     */
    public function createAction()
    {
        // create a new user from post data
        $user = new User($_POST);

        if($user->save()) {
            // authenticate user
            $user = User::authenticate($user->email, $user->password);
            if($user) {
                // login and redirect
                Auth::login($user);
                $this->redirect('/');
            }
        } else {
            View::render('Users/signup.html.twig', ['user' => $user]);
        }
    }

    /**
     * Check if email already exists
     */
    public function validateEmailAction()
    {
        $is_valid = !User::emailExists($_GET['email']);
        $this->json($is_valid);
    }

    /**
     * Authenticate and login user
     */
    public function enterAction()
    {
        $user = User::authenticate($_POST['email'], $_POST['password']);
        // log in and redirect
        if($user) {
            Auth::login($user);
            $this->redirect(Auth::getRequestedPage());
        } else {
            // or try again if failed
            Flasher::addMessage('Login unsuccessful, please try again.', Flasher::WARNING);
            View::render('Users/login.html.twig', ['email' => $_POST['email']]);
        }
    }

    /**
     * Log a user out of the site
     */
    public function logout()
    {
        Auth::logout();
        $this->redirect('/');
    }

    public function facebookCallbackAction()
    {

    }

}
<?php

namespace App\Controllers;

use App\Config;
use Core\Auth;
use App\Models\User;
use Core\Controller;
use Core\Error;
use Core\FacebookLogin;
use Core\Flasher;
use Core\View;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook;

/**
 * Class Users
 * The users controller
 * @package App\Controllers
 */
class Users extends Controller
{
    /**
     * @var FacebookLogin
     */
    protected $facebookLogin;

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
        $this->facebookLogin = new FacebookLogin();
        $loginUrl = $this->facebookLogin->getLoginUrl();
        View::render('Users/login.html.twig', ['facebook_login' => $loginUrl]);
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
        $accessToken = '';
        // Get access token
        try {
            $accessToken = $this->facebookLogin->getAccessToken();
        } catch (FacebookResponseException $e) {
            Error::exceptionHandler($e);
        } catch (FacebookSDKException $e) {
            Error::exceptionHandler($e);
        }
        // Redirect if access token is not set
        if($accessToken === '') {
            $this->redirect('/login');
        }

        // get long lived access token
        $oAuth2Client = $this->facebookLogin->getOAuth2Client();
        if(!$accessToken->isLongLived()) {
            $accessToken = $oAuth2Client->getLongLivedAccessToken();
        }

        // get the data from the response graph node
        $response = $this->facebookLogin->getFb()->get('me?fields=id, first_name, last_name, email', $accessToken);
        $userData = $response->getGraphNode()->asArray();

        // set the session data
        $_SESSION['userData'] = $userData;
        $_SESSION['access_token'] = $accessToken;



    }

}
<?php
/**
 * Created by PhpStorm.
 * User: tomto
 * Date: 23/11/17
 * Time: 11:49
 */

namespace Core;


use Facebook\Facebook;

class FacebookLogin
{
    private $fb;
    private $helper;
    private $loginUrl;


    public function __construct()
    {
        $this->fb = new Facebook([
            'app_id' => Config::FACEBOOK_APP_ID,
            'app_secret' => Config::FACEBOOK_APP_SECRET,
            'default_graph_version' => Config::FACEBOOK_GRAPH_VERSION,
        ]);
        $this->helper = $this->fb->getRedirectLoginHelper();
        $permissions = ['email', 'name'];
        $this->loginUrl = $this->helper->getLoginUrl('http://localhost/facebook-login', $permissions);
    }

    /**
     * @return Facebook
     */
    public function getFb()
    {
        return $this->fb;
    }

    /**
     * @return mixed
     */
    public function getHelper()
    {
        return $this->helper;
    }

    /**
     * @return string
     */
    public function getLoginUrl(): string
    {
        return $this->loginUrl;
    }

    public function getAccessToken() {
        return $this->helper->getAccessToken();
    }

    public function getOAuth2Client()
    {
        return $this->fb->getOAuth2Client();
    }

}
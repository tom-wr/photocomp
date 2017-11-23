<?php
namespace Core;


use App\Models\User;

/**
 * Class Auth
 * Handles authentication of users and session data
 * @package Core
 */
class Auth
{
    /**
     * Log user in by setting the user_id on the session.
     * @param $user User object
     */
    public static function login($user)
    {
        // regenerate for safety
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user->id;
    }

    /**
     * http://php.net/manual/en/function.session-destroy.php
     * Logs a user out of the session
     */
    public static function logout()
    {
        // Unset all of the session variables.
        $_SESSION = array();

        // If it's desired to kill the session, also delete the session cookie.
        // Note: This will destroy the session, and not just the session data!
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        // Finally, destroy the session.
        session_destroy();
    }

    /**
     * Checks to see if a user is logged in.
     * @return bool Whether the user is is set or not
     */
    public static function isLoggedIn()
    {
        return isset($_SESSION['user_id']);
    }

    /**
     * Set the requested page in the session
     */
    public static function rememberRequestedPage()
    {
        $_SESSION['return_to'] = $_SERVER['REQUEST_URI'];
    }

    /**
     * Get the remembered page request from the session
     * @return string The saved page request
     */
    public static function getRequestedPage()
    {
        return $_SESSION['return_to'] ?? '/';
    }

    /**
     * Get the current user based from the user id in the session
     * @return mixed The user object if successful, null otherwise
     */
    public static function getUser()
    {
        if(isset($_SESSION['user_id'])) {
            return User::findById($_SESSION['user_id']);
        }
    }

    /**
     * Create a hash of the user's email address.
     * @return string the hash of the users email
     */
    public static function getUserEmailHash()
    {
        $user = static::getUser();
        if($user) {
            $email_hash = hash('sha256', $user->email);
            return $email_hash;
        }
    }

    /**
     * Validates a captcha response from given response key.
     * @param $post_response
     * @return mixed
     */
    public static function validateCaptcha($responseKey) {

        $secretKey = '6Lfj-zkUAAAAAIxPhuOc1G3LikmUGgJ8YEVc97Q9';
        $userIp = $_SERVER['REMOTE_ADDR'];
        $url = "https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$responseKey&remoteip=$userIp";
        $validation = file_get_contents($url);
        return json_decode($validation, true);

    }

}
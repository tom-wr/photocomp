<?php
/**
 * Created by PhpStorm.
 * User: tomto
 * Date: 20/11/17
 * Time: 09:23
 */

namespace Core;

/**
 * Class Flasher
 * Handles setting and getting flash messages from the session
 * @package Core
 */
class Flasher
{
    // set default notice levels
    const SUCCESS = 'success';
    const INFO = 'info';
    const WARNING = 'warning';

    /**
     * Adds a message to the flash
     * @param $message string
     * @param string $type
     */
    public static function addMessage($message, $type = 'success')
    {
        // initialise flash if not set
        if(!isset($_SESSION['flash'])) {
            $_SESSION['flash'] = [];
        }
        // add message to the flash array
        $_SESSION['flash'][] = [
            'message' => $message,
            'type' => $type
        ];
    }

    /**
     * Get a message from the flash array and clears the flash
     * @return mixed
     */
    public static function getMessages()
    {
        // if there's a flash
        if(isset($_SESSION['flash']))
        {
            // get messages from flash
            $messages = $_SESSION['flash'];
            // clear flash
            unset($_SESSION['flash']);
            return $messages;
        }
    }

}
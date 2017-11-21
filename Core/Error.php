<?php

namespace Core;

use App\Config;
use Twig\Node\Expression\ArrayExpression;

/**
 * Class Error
 * Handles error reporting
 * @package Core
 */
class Error
{
    /**
     * Turns error into an exception
     * @param $level
     * @param $message
     * @param $file
     * @param $line
     * @throws \ErrorException
     */
    public static function errorHandler($level, $message, $file, $line)
    {
        if (error_reporting() !== 0)
        {
            throw new \ErrorException($message, 0, $level, $file, $line);
        }
    }

    /**
     * Handles exceptions. Sets the http response code and renders a error message either to the log
     * or to the webpage depending on the error config settings.
     * @param $exception
     */
    public static function exceptionHandler($exception)
    {
        // Determine and set the error response code
        $code = $exception->getCode();
        if($code != 404) {
            $code = 500;
        }
        http_response_code($code);

        // set up the error array
        $error = [
            'class' => get_class($exception),
            'message' => $exception->getMessage(),
            'stack_trace' => $exception->getTraceAsString(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'code' => $code
        ];
        // render to the page or to logs depending on server settings.
        if(Config::SHOW_ERRORS)
        {
            View::render('Errors/dev_error.html.twig', ['error' => $error]);
        } else {
            self::logError($error);
        }
    }

    /**
     * Output a formatted error message to the error log
     * @param array $error
     */
    protected static function logError(Array $error)
    {
        $log = dirname(__DIR__) . '/logs/' . date('Y-m-d') . '.txt';
        ini_set('error_log', $log);
        $log_message = "Uncaught Exception:" . $error['class'] . PHP_EOL
            . "Message:" . $error['message'] . PHP_EOL
            . "Stack trace:" . $error['stack_trace'] . PHP_EOL
            . "File:" . $error['file'] . PHP_EOL
            . "Line:" . $error['line'] . PHP_EOL;
        error_log($log_message);
        if($error['code'] == 404) {
            $view_message = '404: Page not found';
        } else {
            $view_message = '500: Server error';
        }
        View::render('Errors/error.html.twig', ['message' => $view_message]);
    }
}
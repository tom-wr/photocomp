<?php

namespace Core;
use PDO;
use App\Config;

/**
 * Class Model
 * Abstract class for the model
 * @package Core
 */
abstract class Model
{
    /**
     * Error array for validations
     * @var array
     */
    public $errors = [];

    /**
     * Model constructor.
     * Initialises and sets properties on the object from a given array
     * @param array $data
     */
    public function __construct($data = [])
    {
        foreach($data as $key => $value)
        {
            $this->$key = $value;
        }
    }

    /**
     * Get the database connection.
     * @return null|PDO
     */
    protected static function getDB()
    {
        static $db = null;
        if($db === null) {
            try {
                $dsn =  'mysql:host=' . Config::DB_HOST .
                        ';dbname=' . Config::DB_NAME . ';charset=utf8';
                // get new connection using config information
                $db = new PDO($dsn, Config::DB_USER, Config::DB_PASSWORD);
                // Throw exceptions when errors occur
                $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (\PDOException $e) {
                echo $e->getMessage();
            }
        }
        return $db;
    }
}
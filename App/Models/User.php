<?php

namespace App\Models;

use Core\Model;
use PDO;

class User extends Model
{
    /**
     * @var array The list of validation errors
     */
    public $errors = [];

    /**
     * User constructor.
     * @param array $data the parameters to be set on the user
     */
    public function __construct($data = [])
    {
        parent::__construct($data);
    }

    /**
     * Save a user to the database
     * @return bool success of the operation
     */
    public function save()
    {
        // validate the properties
        $this->validate();
        // if not errors
        if(empty($this->errors)) {
            // create a password hash
            $password_hash = password_hash($this->password, PASSWORD_DEFAULT);

            $sql = 'INSERT INTO users(username, email, password)
                VALUES (:username, :email, :password)';
            // get the db connection
            $db = static::getDB();
            // prepare the statement
            $stmt = $db->prepare($sql);
            $outcome = $stmt->execute([
                ':username' => $this->username,
                ':email' => $this->email,
                ':password' => $password_hash,
            ]);
            return $outcome;
        }
        return false;
    }

    /**
     * Validate the user properties
     */
    public function validate()
    {
        // Username must not be empty
        if($this->username == '')
        {
            $this->errors[] = 'Name is required';
        }

        // the email must not be invalid
        if(filter_var($this->email, FILTER_VALIDATE_EMAIL) === false)
        {
            $this->errors[] = 'Invalid email';
        }

        // The email must not already exist
        if(static::emailExists($this->email))
        {
            $this->errors[] = 'email already taken';
        }

        // The password must be longer than 6 letters
        if(strlen($this->password) < 6)
        {
            $this->errors[] = 'Please enter at least 6 characters for the password';
        }

        // The password must contain at least one letter
        if(preg_match('/.*[a-z]+.*/i', $this->password) == 0)
        {
            $this->errors[] = 'Password needs at least one letter';
        }

        // The password must contain at least one number
        if(preg_match('/.*\d+.*/i', $this->password) == 0)
        {
            $this->errors[] = 'Password needs at least one number';
        }
    }

    /**
     * Check if an email exists in the db
     * @param $email String the email to check
     * @return bool
     */
    public static function emailExists($email)
    {
        // return true if results found, false otherwise.
        return static::findByEmail($email) !== false;
    }

    /**
     * Find and email by a given email string
     * @param $email String
     * @return mixed
     */
    public static function findByEmail($email)
    {
        $sql = 'SELECT * FROM users WHERE email = :email';
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt->execute([':email' => $email]);

        return $stmt->fetch();
    }

    /**
     * Authenticate a user by checking that the given password verifies against the given email
     * @param $email
     * @param $password
     * @return bool|mixed returns a Users object if succesful and false if not
     */
    public static function authenticate($email, $password)
    {
        $user = static::findByEmail($email);
        if($user) {
            if(password_verify($password, $user->password)) {
                return $user;
            }
        }
        return false;
    }

    /**
     * Find a user in the db by a given id
     * @param $id
     * @return mixed
     */
    public static function findById($id)
    {
        $sql = 'SELECT * FROM users WHERE id = :id';
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt->execute([':id' => $id]);

        return $stmt->fetch();
    }



}
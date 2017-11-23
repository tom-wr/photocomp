<?php

namespace App;

/**
 * Class Config
 * Configuration variables for the app
 * @package App
 */
class Config
{
    // database host name
    const DB_HOST = '127.0.0.1';
    // database name
    const DB_NAME = 'machineh_db';
    // database username
    const DB_USER = 'root';
    // database password
    const DB_PASSWORD = 'root';
    // show development error messages
    const SHOW_ERRORS = true;
    // hash key
    const HASH_KEY = 'akImeo2wfz3uN66jAakMFtUU329Pwf0R';

    const SMTP_DEBUG = 2;
    const SMTP_HOST = 'mail3.gridhost.co.uk';
    const SMTP_AUTH = true;
    const SMTP_USERNAME = 'support@machineheart.xyz';
    const SMTP_PASSWORD = 'mantaray12';
    const SMTP_SECURE = 'ssl';
    const SMTP_PORT = 465;



}
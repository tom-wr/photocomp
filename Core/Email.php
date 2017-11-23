<?php

namespace Core;


use App\Config;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class Email
{
    protected $mail = null;

    public function __construct()
    {
        $this->mail = new PHPMailer();
        try {
            $this->mail->SMTPDebug = Config::SMTP_DEBUG;                                 // Enable verbose debug output
            $this->mail->isSMTP();                                      // Set mailer to use SMTP
            $this->mail->Host = Config::SMTP_HOST;  // Specify main and backup SMTP servers
            $this->mail->SMTPAuth = Config::SMTP_AUTH;                               // Enable SMTP authentication
            $this->mail->Username = Config::SMTP_USERNAME;                 // SMTP username
            $this->mail->Password = Config::SMTP_PASSWORD;                           // SMTP password
            $this->mail->SMTPSecure = Config::SMTP_SECURE;                            // Enable TLS encryption, `ssl` also accepted
            $this->mail->Port = Config::SMTP_PORT;
        } catch(Exception $e) {
            Error::exceptionHandler($e);
        }
    }

    public function send($from, $message)
    {
        try {
            $this->mail->setFrom($from);
            $this->mail->Subject = 'Contact form message';
            $this->mail->Body = $message;
            $this->mail->addAddress(Config::SMTP_USERNAME);
            $this->mail->send();

        } catch (Exception $e) {
            Error::exceptionHandler($e);
        }
    }


}
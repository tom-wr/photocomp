<?php

namespace App\Controllers;

use Core\Auth;
use Core\Controller;
use Core\Email;
use Core\Flasher;
use Core\View;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

/**
 * Class Pages
 * The pages controller
 * @package App\Controllers
 */
class Pages extends Controller
{

    /**
     * Renders the hoe page template
     */
    public function homeAction()
    {
        View::render('Pages/home.html.twig');
    }

    public function contactAction()
    {
        View::render('Pages/contact.html.twig');
    }

    public function contactSendAction()
    {
        $errors = $this->validateContact($_POST);
        if(empty($errors))
        {
            $mail = new Email();
            $mail->send($_POST['email'], $_POST['message']);
            Flasher::addMessage('Message sent');
            $this->redirect('/contact');
        } else {
            View::render('Pages/contact.html.twig', ['errors' => $errors]);
        }

    }

    protected function validateContact($post)
    {
        $errors = [];

        $captchaValidation = Auth::validateCaptcha($_POST['g-recaptcha-response']);

        if($captchaValidation['success'] === false) {
            $errors[] = 'Invalid captcha';
        }

        // the email must not be invalid
        if(filter_var($post['email'], FILTER_VALIDATE_EMAIL) === false)
        {
            $errors[] = 'Invalid email';
        }

        if(isset($post['number'])) {
            if (filter_var($post['number'], FILTER_VALIDATE_INT) === false) {
                $errors[] = 'Phone number must be only digits';
            }
        }

        // The password must contain at least one number
        if(strlen($post['message']) == 0)
        {
            $errors[] = 'Please enter a message';
        }

        return $errors;
    }

    protected function sendEmail($post)
    {
        $mail = new PHPMailer();

        try {
            $mail->SMTPDebug = 2;                                 // Enable verbose debug output
            $mail->isSMTP();                                      // Set mailer to use SMTP
            $mail->Host = 'mail3.gridhost.co.uk';  // Specify main and backup SMTP servers
            $mail->SMTPAuth = true;                               // Enable SMTP authentication
            $mail->Username = 'support@machineheart.xyz';                 // SMTP username
            $mail->Password = 'mantaray12';                           // SMTP password
            $mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
            $mail->Port = 465;


        } catch (Exception $e) {
            echo 'Mailer error: ' . $mail->ErrorInfo;
        }

    }


}
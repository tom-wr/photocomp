<?php

namespace App\Controllers;
use Core\Auth;
use Core\Controller;
use App\Models\Photo;
use Core\Flasher;
use Core\View;

/**
 * Class Photos
 * The photo controller
 * @package App\Controllers
 */
class Photos extends Controller
{
    /**
     * Lists the photos
     */
    public function indexAction()
    {
        $photos = Photo::getAll();
        View::render('Photos/gallery.html.twig', ['photos' => $photos]);
    }

    /**
     * Searches the photos in the database using the get params. Rerenders the gallery as a results page.
     */
    public function search()
    {
        $options = [];
        $options['search'] = $_GET['search'] ?? '';
        $options['date-start'] = $_GET['date-start'] ?? '';
        $options['date-end'] = $_GET['date-end'] ?? '';
        $results = Photo::search($options);

        View::render('Photos/gallery.html.twig', ['photos' => $results]);
    }

    /**
     * Render the photo submit page.
     */
    public function submitAction()
    {
        $this->requireLogin();
        View::render('Photos/submit.html.twig');
    }

    /**
     * Handle the file upload
     */
    public function createAction()
    {
        // make sure request is post
        if($_SERVER["REQUEST_METHOD"] == "POST") {
            // make sure that the photo file exists and has no errors
            if (isset($_FILES["photo"]) && $_FILES["photo"]["error"] == 0) {

                // create a unique filename
                $email_hash = Auth::getUserEmailHash();
                $upload_dir = 'images/';
                $upload_file = $upload_dir . $email_hash . basename($_FILES['photo']['name']);
                // move the file to the server directory
                $moved_file = move_uploaded_file($_FILES['photo']['tmp_name'], $upload_file);

                if ($moved_file) {
                    // save the file to the database
                    $photo = new Photo([
                         'uid' => Auth::getUser()->id,
                         'filename' => $upload_file,
                         'caption' => $_POST['caption']
                    ]);
                    // if successfully saved to database redirect to the gallery
                    if($photo->save()) {
                        $this->redirect('/photos');
                    } else {
                        Flasher::addMessage('Upload failed', Flasher::WARNING);
                        $this->redirect('/submit');
                    }
                }
            }
        }
    }

    /**
     * Show a single photo
     */
    public function showAction()
    {
        $photo = Photo::findById($this->route_params['id']);
        View::render('Photos/show.html.twig', ['photo' => $photo]);
    }

}
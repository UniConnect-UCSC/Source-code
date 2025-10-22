<?php
class Users extends Controller
{
    public function index()
    {
        redirect('/');
    }


    public function show($identifier = null)
    {
        if (!$identifier) {
            redirect('/');
        }

        require_once __DIR__ . "/../models/User.php";
        require_once __DIR__ . "/../models/GlobalPost.php";

        $userModel = new User();
        $profileUser = $userModel->first(['id' => $identifier]);


        if (!$profileUser) {
            http_response_code(404);
            $this->view('404');
            return;
        }

        $this->view('userSlug', [
            'title' => 'University Feed | UniConnect',
            'head' => '
        
            <link rel="stylesheet" href="/assets/css/components/navbar.css">
            <link rel="stylesheet" href="/assets/css/components/navPanel.css">
            ',
            'profileUser' => $profileUser
        ]);
    }
}
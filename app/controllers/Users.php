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
            <link rel="stylesheet" href="/assets/css/pages/profile.css">
            <link rel="stylesheet" href="/assets/css/components/profileContent.css">
            <link rel="stylesheet" href="/assets/css/components/profileSidebar.css">       
            <link rel="stylesheet" href="/assets/css/components/profileBanner.css">  
            <link rel="stylesheet" href="/assets/css/components/createPost.css"> 
            <link rel="stylesheet" href="/assets/css/components/achievementsWidget.css">
            <link rel="stylesheet" href="/assets/css/components/feedType.css">
            <link rel="stylesheet" href="/assets/css/components/profileFeed.css">
            <link rel="stylesheet" href="/assets/css/components/post.css">
            <link rel="stylesheet" href="/assets/css/components/friendsWidget.css">
            <link rel="stylesheet" href="/assets/css/components/photosWidget.css">
            ',
            'profileUser' => $profileUser
        ]);
    }
}

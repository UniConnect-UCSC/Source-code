<?php

require_once(__DIR__ . "/../models/GlobalPost.php");

class Profile extends Controller
{
    public function index()
    {
        //Edit Post Handling
        // if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['edit_post_id'])) {
        //     $postId = $_POST['edit_post_id'];
        //     $newContent = $_POST['new_content'] ?? '';
        //     $postModel = new Post();
        // }


        //Delete Post Handling
        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['delete_post_id'])) {
            $postId = $_POST['delete_post_id'];
            $postModel = new Post();

            $postModel->delete($postId);
            exit;
        }

        $this->view('profile', [
            'title' => 'Profile | UniConnect',
            'head' => '
            <link rel="stylesheet" href="/assets/css/pages/profile.css">
            <link rel="stylesheet" href="/assets/css/components/navbar.css">
            <link rel="stylesheet" href="/assets/css/components/navPanel.css">
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
            '
        ]);
    }
}

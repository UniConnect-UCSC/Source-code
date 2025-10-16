<?php

class Profile extends Controller
{
    public function index()
    {
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
            '
        ]);
    }
}

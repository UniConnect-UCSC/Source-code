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
            '
        ]);
    }
}
<?php
class Friends extends Controller
{
    public function index()
    {
        $this->view('friends', [
            'title' => 'Friends | UniConnect',
            'head' => '
            <link rel="stylesheet" href="/assets/css/pages/friends.css">
            <link rel="stylesheet" href="/assets/css/components/navbar.css">
            <link rel="stylesheet" href="/assets/css/components/navPanel.css">
            <link rel="stylesheet" href="/assets/css/components/friends.css">
            <link rel="stylesheet" href="/assets/css/components/friendRequests.css">
            '
        ]);
    }
}
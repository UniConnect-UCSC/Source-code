<?php

class Home extends Controller
{
    public function index()
    {
        $this->view('home', [
            'title' => 'UniConnect',
            'head' => '
            <link rel="stylesheet" href="/assets/css/pages/home.css">
            <link rel="stylesheet" href="/assets/css/components/navbar.css">
            <link rel="stylesheet" href="/assets/css/components/navPanel.css">
            <link rel="stylesheet" href="/assets/css/components/feed.css">
            <link rel="stylesheet" href="/assets/css/components/widgetPanel.css">
            <link rel="stylesheet" href="/assets/css/components/eventsWidget.css">
            '
        ]);
    }
}

<?php

class Settings extends Controller
{
    public function index()
    {
        $this->view('settings', [
            'title' => 'Settings | UniConnect',
            'head' => '
            <link rel="stylesheet" href="/assets/css/pages/settings.css">
            <link rel="stylesheet" href="/assets/css/components/navbar.css">
            <link rel="stylesheet" href="/assets/css/components/navPanel.css">
            ',
        ]);
    }
}

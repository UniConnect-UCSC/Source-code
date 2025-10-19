<?php
class Photos extends Controller
{
    public function index()
    {
        $this->view('photos', [
            'title' => 'Photos | UniConnect',
            'head' => '
            
            <link rel="stylesheet" href="/assets/css/pages/photos.css">
            <link rel="stylesheet" href="/assets/css/components/navbar.css">
            <link rel="stylesheet" href="/assets/css/components/navPanel.css">
            '
        ]);
    }
}
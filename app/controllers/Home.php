<?php

class Home extends Controller
{
    public function index()
    {
        $this->view('home', [
            'title' => 'UniConnect',
            'head' => '<link rel="stylesheet" href="/assets/css/components/navbar.css">'
        ]);
    }
}
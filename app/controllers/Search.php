<?php
class Search extends Controller
{
    public function index()
    {
        $this->view('search', [
            'title' => 'Search | UniConnect',
            'head' => '
            <link rel="stylesheet" href="/assets/css/pages/search.css">
            <link rel="stylesheet" href="/assets/css/components/navbar.css">
            <link rel="stylesheet" href="/assets/css/components/navPanel.css">
            <link rel="stylesheet" href="/assets/css/components/friendRequests.css">
            <link rel="stylesheet" href="/assets/css/components/searchResults.css">
            <link rel="stylesheet" href="/assets/css/components/searchFilters.css">
            
            '
        ]);
    }
}
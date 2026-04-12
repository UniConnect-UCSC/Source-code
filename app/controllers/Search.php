<?php
require_once __DIR__ . '/../models/User.php';

class Search extends Controller
{
    public function index()
    {
        $query = trim($_GET['q'] ?? '');
        $results = [];

        if ($query !== '' && !empty($_SESSION['user_id'])) {
            $userModel = new User();
            $users = $userModel->searchUsers($query, $_SESSION['user_id']);

            foreach ($users as $u) {
                $results[] = [
                    'type' => 'user',
                    'data' => $u,
                ];
            }
        }

        $this->view('search', [
            'title' => 'Search | UniConnect',
            'head' => '
            <link rel="stylesheet" href="/assets/css/pages/search.css">
            <link rel="stylesheet" href="/assets/css/components/navbar.css">
            <link rel="stylesheet" href="/assets/css/components/navPanel.css">
            <link rel="stylesheet" href="/assets/css/components/friendRequests.css">
            <link rel="stylesheet" href="/assets/css/components/searchResults.css">
            <link rel="stylesheet" href="/assets/css/components/searchFilters.css">
            <link rel="stylesheet" href="/assets/css/components/userCard.css">
            ',
            'results' => $results,
            'query' => $query,
        ]);
    }
}

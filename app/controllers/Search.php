<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Friendship.php';

class Search extends Controller
{

    public function index()
    {
        $query = trim($_GET['q'] ?? '');
        $results = [];

        if ($query !== '' && !empty($_SESSION['user_id'])) {
            $currentUserId = $_SESSION['user_id'];
            $userModel = new User();
            $friendshipModel = new Friendship(); // create once, not inside loop

            $users = $userModel->searchUsers($query, $currentUserId);

            foreach ($users as $u) {
                $outgoingStatus = $friendshipModel->getFriendshipStatus($currentUserId, $u->id); // me -> them
                $incomingStatus = $friendshipModel->getFriendshipStatus($u->id, $currentUserId); // them -> me

                $relationshipState = 'none';

                if ($outgoingStatus === 'accepted' || $incomingStatus === 'accepted') {
                    $relationshipState = 'friends';
                } elseif ($outgoingStatus === 'pending') {
                    $relationshipState = 'outgoing_pending';
                } elseif ($incomingStatus === 'pending') {
                    $relationshipState = 'incoming_pending';
                }

                $results[] = [
                    'type' => 'user',
                    'data' => $u,
                    'relationshipState' => $relationshipState,
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

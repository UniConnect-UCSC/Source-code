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
            $userModel = new User();
            $users = $userModel->searchUsers($query, $_SESSION['user_id']);


            foreach ($users as $u) {
                $friendshipModel = new Friendship();
                $friendshipStatus = $friendshipModel->getFriendshipStatus($_SESSION['user_id'], $u->id);

                $results[] = [
                    'type' => 'user',
                    'data' => $u,
                    'friendshipStatus' => $friendshipStatus,
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



    public function sendFriendRequest()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $targetUserId = $_POST['user_id'] ?? null;
            $currentUserId = $_SESSION['user_id'] ?? null;

            if ($targetUserId && $currentUserId) {
                $friendshipModel = new Friendship();
                $result = $friendshipModel->sendFriendRequest($currentUserId, $targetUserId);

                if ($result) {
                    echo json_encode(['success' => true, 'message' => 'Friend request sent.']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to send friend request.']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid user ID.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
        }
    }
}

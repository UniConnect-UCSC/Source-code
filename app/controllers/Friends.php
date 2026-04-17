<?php
require_once __DIR__ . '/../models/Friendship.php';

class Friends extends Controller
{
    public function index()
    {
        $friendshipModel = new Friendship();
        $friends = $friendshipModel->getFriends($_SESSION['user_id']);

        $friendRequests = $friendshipModel->getFriendRequests($_SESSION['user_id']);
        $friends = $friendshipModel->getFriends($_SESSION['user_id']);

        $this->view('friends', [
            'title' => 'Friends | UniConnect',
            'head' => '
            <link rel="stylesheet" href="/assets/css/pages/friends.css">
            <link rel="stylesheet" href="/assets/css/components/navbar.css">
            <link rel="stylesheet" href="/assets/css/components/navPanel.css">
            <link rel="stylesheet" href="/assets/css/components/friends.css">
            <link rel="stylesheet" href="/assets/css/components/friendRequests.css">
            <link rel="stylesheet" href="/assets/css/components/friendSuggestions.css">
            
            ',
            "friendRequests" => $friendRequests,
            "friends" => $friends,
        ]);
    }

    public function sendFriendRequest()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
            exit;
        }

        $currentUserId = $_SESSION['user_id'] ?? null;

        $body = json_decode(file_get_contents('php://input'), true);
        $targetUserId = $body['user_id'] ?? null;

        if (!$currentUserId) {
            header('Location: /login');
            exit;
        }

        if (!$targetUserId || $targetUserId == $currentUserId) {
            header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/search'));
            exit;
        }

        $friendshipModel = new Friendship();
        $friendshipModel->sendFriendRequest($currentUserId, $targetUserId);

        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
        exit;
    }

    public function cancelFriendRequest()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
            exit;
        }

        $currentUserId = $_SESSION['user_id'] ?? null;

        $body = json_decode(file_get_contents('php://input'), true);
        $targetUserId = $body['user_id'] ?? null;

        if (!$currentUserId) {
            header('Location: /login');
            exit;
        }

        if (!$targetUserId || $targetUserId == $currentUserId) {
            header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/search'));
            exit;
        }

        $friendshipModel = new Friendship();
        $friendshipModel->removeFriendRequest($currentUserId, $targetUserId);

        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
        exit;
    }

    public function acceptFriendRequest()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
            exit;
        }

        $currentUserId = $_SESSION['user_id'] ?? null;

        $body = json_decode(file_get_contents('php://input'), true);
        $targetUserId = $body['user_id'] ?? null;

        if (!$currentUserId) {
            header('Location: /login');
            exit;
        }

        if (!$targetUserId || $targetUserId == $currentUserId) {
            header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/search'));
            exit;
        }

        $friendshipModel = new Friendship();
        $friendshipModel->acceptFriendRequest($currentUserId, $targetUserId);

        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
        exit;
    }

    public function declineFriendRequest()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
            exit;
        }

        $currentUserId = $_SESSION['user_id'] ?? null;

        $body = json_decode(file_get_contents('php://input'), true);
        $targetUserId = $body['user_id'] ?? null;

        if (!$currentUserId) {
            header('Location: /login');
            exit;
        }

        if (!$targetUserId || $targetUserId == $currentUserId) {
            header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/search'));
            exit;
        }

        $friendshipModel = new Friendship();
        $friendshipModel->removeFriendRequest($currentUserId, $targetUserId);

        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
        exit;
    }

    public function unfriend()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
            exit;
        }

        $currentUserId = $_SESSION['user_id'] ?? null;

        $body = json_decode(file_get_contents('php://input'), true);
        $targetUserId = $body['user_id'] ?? null;

        if (!$currentUserId) {
            header('Location: /login');
            exit;
        }

        if (!$targetUserId || $targetUserId == $currentUserId) {
            header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/search'));
            exit;
        }

        $friendshipModel = new Friendship();
        $friendshipModel->removeFriendship($currentUserId, $targetUserId);

        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
        exit;
    }
}

<?php
require_once __DIR__ . '/../models/Reaction.php';

class Posts extends Controller
{
    public function index()
    {
        redirect('/');
    }


    public function reactOnPost()
    {
        ob_start(); // Buffer any accidental output (notices, warnings)
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            ob_end_clean();
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
            exit;
        }

        $currentUserId = $_SESSION['user_id'] ?? null;
        if (!$currentUserId) {
            ob_end_clean();
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthenticated.']);
            exit;
        }

        $body = json_decode(file_get_contents('php://input'), true);
        $postId = $body['post_id'] ?? null;
        $postType = $body['post_type'] ?? 'global';

        if (!$postId) {
            ob_end_clean();
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Post ID is required.']);
            exit;
        }

        $reactionModel = new Reaction();
        $existingReaction = $reactionModel->getReaction($currentUserId, $postId);
        if ($existingReaction) {
            $reactionModel->removeReaction($currentUserId, $postId);
            $action = 'removed';
        } else {
            $reactionModel->addReaction($currentUserId, $postId, $postType);
            $action = 'added';
        }

        $reactionCount = $reactionModel->countReactions($postId);

        ob_end_clean(); // Discard any notices/warnings that snuck in
        echo json_encode([
            'success' => true,
            'action' => $action,
            'reaction_count' => $reactionCount
        ]);
        exit;
    }
}

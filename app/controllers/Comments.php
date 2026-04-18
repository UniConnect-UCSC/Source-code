<?php
require_once __DIR__ . '/../models/PostComment.php';

class Comments extends Controller
{
    public function index()
    {
        redirect('/');
    }

    public function addComment()
    {
        ob_start();
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
        $commentText = trim($body['comment_text'] ?? '');

        if (!$postId || $commentText === '') {
            ob_end_clean();
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Post ID and comment text are required.']);
            exit;
        }

        $commentModel = new PostComment();
        $commentId = $commentModel->addComment($currentUserId, $postId, $commentText);

        ob_end_clean();
        echo json_encode([
            'success' => true,
            'comment_id' => $commentId,
            'message' => 'Comment added successfully.'
        ]);
    }

    public function getComments()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
            exit;
        }

        $postId = $_GET['post_id'] ?? null;
        if (!$postId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Post ID is required.']);
            exit;
        }

        $commentModel = new PostComment();
        $comments = $commentModel->getCommentsForPost($postId) ?: [];

        echo json_encode([
            'success' => true,
            'comments' => $comments
        ]);
    }
}

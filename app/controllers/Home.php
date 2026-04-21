<?php
require_once(__DIR__ . "/../models/GlobalPost.php");
require_once __DIR__ . '/../core/functions.php';
class Home extends Controller
{
    public function index()
    {

        //delete post
        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['delete_post_id'])) {
            $postId = $_POST['delete_post_id'];
            $postModel = new GlobalPost();

            $postModel->delete($postId, true);
            exit;
        }

        //edit post
        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['edit_post_id'])) {
            $postId = $_POST['edit_post_id'];
            $caption = $_POST['caption'] ?? '';
            $updatedAt = date('Y-m-d H:i:s');
            $isAnonymous = isset($_POST['is_anonymous']) ? (int)$_POST['is_anonymous'] : 0;

            // Handle file upload if present
            $mediaUrl = uploadImageToCloudinary($_FILES['media'] ?? null, 'uniconnect_posts');


            $updateData = [
                'caption' => $caption,
                'is_anonymous' => $isAnonymous,
                'updated_at' => $updatedAt
            ];
            if ($mediaUrl) {
                $updateData['media_url'] = $mediaUrl;
            }

            $postModel = new GlobalPost();
            $postModel->update($postId, $updateData);

            // Respond for AJAX (no layout)
            http_response_code(200);
            exit;
        }

        //create post
        if ($_SERVER["REQUEST_METHOD"] === "POST" && !isset($_POST['delete_post_id'])) {
            // respond with JSON for AJAX
            header('Content-Type: application/json');

            $caption = $_POST['caption'] ?? '';
            $isAnonymous = (isset($_POST['isAnonymous']) && ($_POST['isAnonymous'] === '1' || $_POST['isAnonymous'] === 'true')) ? 1 : 0;

            // handle media upload
            $mediaURL = uploadImageToCloudinary($_FILES['media'] ?? null, 'uniconnect_post');

            try {
                $postModel = new GlobalPost();
                $insertData = [
                    'user_id' => $_SESSION['user_id'],
                    'caption'   => $caption,
                    'is_anonymous' => $isAnonymous,
                    'media_url' => $mediaURL,
                ];

                $insertId = $postModel->insert(array_keys($insertData), [array_values($insertData)]);

                echo json_encode([
                    'success' => true,
                    'post_id' => $insertId ?? null,
                    'media_url' => $mediaURL,
                ]);
            } catch (Exception $e) {
                error_log("Home::create post error: " . $e->getMessage());
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Server error']);
            }
            exit;
        }

        $this->view('home', [
            'title' => 'UniConnect',
            'head' => '
            <link rel="stylesheet" href="/assets/css/pages/home.css">
            <link rel="stylesheet" href="/assets/css/components/navbar.css">
            <link rel="stylesheet" href="/assets/css/components/navPanel.css">
            <link rel="stylesheet" href="/assets/css/components/feed.css">
            <link rel="stylesheet" href="/assets/css/components/widgetPanel.css">
            <link rel="stylesheet" href="/assets/css/components/eventsWidget.css">
            <link rel="stylesheet" href="/assets/css/components/createPost.css">
            <link rel="stylesheet" href="/assets/css/components/post.css">
            '
        ]);
    }
}

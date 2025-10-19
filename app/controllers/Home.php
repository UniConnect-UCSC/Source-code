<?php
require_once(__DIR__ . "/../models/GlobalPost.php");
require_once __DIR__ . '/../core/functions.php';
class Home extends Controller
{
    public function index()
    {
        //create post
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            // respond with JSON for AJAX
            header('Content-Type: application/json');

            $caption = $_POST['caption'] ?? '';
            $isAnonymous = (isset($_POST['isAnonymous']) && ($_POST['isAnonymous'] === '1' || $_POST['isAnonymous'] === 'true')) ? 1 : 0;
            $mediaURL = null;

            // handle media upload
            if (!empty($_FILES['media']) && $_FILES['media']['error'] === UPLOAD_ERR_OK) {
                $tmpPath = $_FILES['media']['tmp_name'];
                $uploadedUrl = uploadImageToCloudinary($tmpPath, 'uniconnect_posts');
                if ($uploadedUrl) {
                    $mediaURL = $uploadedUrl;
                } else {
                    // log but continue (or return error)
                    error_log('Cloudinary upload failed for post by user ' . ($_SESSION['user_id'] ?? 'unknown'));
                }
            }

            try {
                $postModel = new GlobalPost();
                $insertId = $postModel->insert([
                    'user_id' => $_SESSION['user_id'],
                    'caption'   => $caption,
                    'is_anonymous' => $isAnonymous,
                    'media_url' => $mediaURL,
                ]);

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

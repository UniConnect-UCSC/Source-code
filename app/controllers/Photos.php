<?php
class Photos extends Controller
{
    public function index()
    {
        $this->view('photos', [
            'title' => 'Photos | UniConnect',
            'head' => '
            
            <link rel="stylesheet" href="/assets/css/pages/photos.css">
            <link rel="stylesheet" href="/assets/css/components/navbar.css">
            <link rel="stylesheet" href="/assets/css/components/navPanel.css">
            '
        ]);
    }

    public function show($userId = null)
    {
        if (!$userId) {
            redirect('/');
        }

        require_once __DIR__ . "/../models/User.php";
        require_once __DIR__ . "/../models/GlobalPost.php";

        $userModel = new User();
        $profileUser = $userModel->first(['id' => $userId]);

        if (!$profileUser) {
            http_response_code(404);
            $this->view('404');
            return;
        }

        // Fetch recent posts with media for the user
        $globalPostModel = new GlobalPost();
        $allPosts = $globalPostModel->where(
            [
                ["user_id", '=', $userId]
            ],
            6,  // limit to 6 for testing
            null,
            ['created_at' => 'DESC']
        ) ?? [];

        $photos = [];
        $isOwnProfile = isset($_SESSION['user_id']) && $_SESSION['user_id'] == $profileUser->id;

        foreach ($allPosts as $post) {
            if ($post->media_url) {
                $photos[] = [
                    'id' => $post->id,
                    'media_url' => $post->media_url,
                    'created_at' => $post->created_at,
                    'is_own_post' => $isOwnProfile
                ];
            }
        }

        $this->view('photos', [
            'title' => 'Photos | UniConnect',
            'head' => '
            
            <link rel="stylesheet" href="/assets/css/pages/photos.css">
            <link rel="stylesheet" href="/assets/css/components/navbar.css">
            <link rel="stylesheet" href="/assets/css/components/navPanel.css">
            ',
            'profileUser' => $profileUser,
            'photos' => $photos
        ]);
    }
}

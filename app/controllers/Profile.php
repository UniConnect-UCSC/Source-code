<?php

require_once(__DIR__ . "/../models/GlobalPost.php");
require_once(__DIR__ . "/../models/UniversityPost.php");

class Profile extends Controller
{
    public function index()
    {
        //Edit Post Handling
        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['edit_post_id'])) {
            require_once(__DIR__ . "/../models/GlobalPost.php");
            $postId = $_POST['edit_post_id'];
            $caption = $_POST['caption'] ?? '';
            $updatedAt = date('Y-m-d H:i:s');
            $isAnonymous = isset($_POST['is_anonymous']) ? (int)$_POST['is_anonymous'] : 0;

            // Handle file upload if present
            $mediaUrl = uploadImageToCloudinary($tmpPath, 'uniconnect_posts');

            $updateData = [
                'caption' => $caption,
                'is_anonymous' => $isAnonymous,
                'updated_at' => $updatedAt
            ];
            if ($mediaUrl) {
                $updateData['media_url'] = $mediaUrl;
            }


            if (isset($_POST['post_type']) && $_POST['post_type'] == 'global') {
                $postModel = new GlobalPost();
                $postModel->update($postId, $updateData);
            } else {
                $postModel = new UniversityPost();
                $postModel->update($postId, $updateData);
            }


            // Respond for AJAX (no layout)
            http_response_code(200);
            exit;
        }


        //Delete Post Handling
        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['delete_post_id'])) {
            $postId = $_POST['delete_post_id'];
            $postType = $_POST['post_type'];

            if ($postType === 'global') {
                $postModel = new GlobalPost();
            } else {
                $postModel = new UniversityPost();
            }

            $postModel->delete($postId);
            exit;
        }

        $this->view('profile', [
            'title' => 'Profile | UniConnect',
            'head' => '
            <link rel="stylesheet" href="/assets/css/pages/profile.css">
            <link rel="stylesheet" href="/assets/css/components/navbar.css">
            <link rel="stylesheet" href="/assets/css/components/navPanel.css">
            <link rel="stylesheet" href="/assets/css/components/profileContent.css">
            <link rel="stylesheet" href="/assets/css/components/profileSidebar.css">       
            <link rel="stylesheet" href="/assets/css/components/profileBanner.css">  
            <link rel="stylesheet" href="/assets/css/components/createPost.css"> 
            <link rel="stylesheet" href="/assets/css/components/achievementsWidget.css">
            <link rel="stylesheet" href="/assets/css/components/feedType.css">
            <link rel="stylesheet" href="/assets/css/components/profileFeed.css">
            <link rel="stylesheet" href="/assets/css/components/post.css">
            <link rel="stylesheet" href="/assets/css/components/friendsWidget.css">
            <link rel="stylesheet" href="/assets/css/components/photosWidget.css">
            '
        ]);
    }
}
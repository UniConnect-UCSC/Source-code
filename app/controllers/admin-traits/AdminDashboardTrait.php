<?php
trait AdminDashboardTrait
{
    public function dashboard()
    {
        if (empty($_SESSION['is_admin'])) {
            header("Location: /admin");
            exit;
        }

        $this->view('admindashboard', [
            'title' => 'Admin Dashboard | UniConnect',
            'head' => '
            <link rel="stylesheet" href="/assets/css/pages/admin.css">
            <link rel="stylesheet" href="/assets/css/pages/admindashboard.css">
            <link rel="stylesheet" href="/assets/css/components/navbar.css">
            <link rel="stylesheet" href="/assets/css/components/navPanel.css">
            <link rel="stylesheet" href="/assets/css/components/statcard.css">
            ',
        ]);
    }

    public function dashboardStats()
    {
        ob_start();
        header('Content-Type: application/json');

        if (empty($_SESSION['is_admin'])) {
            ob_end_clean();
            http_response_code(403);
            echo json_encode([
                'success' => false,
                'message' => 'Unauthorized',
            ]);
            exit;
        }

        $userModel = new User();
        $globalPostModel = new GlobalPost();
        $universityPostModel = new UniversityPost();
        $commentModel = new PostComment();
        $universityMode = new University();

        $totalUsers = $userModel->getTotalUsers();
        $totalPosts = $globalPostModel->getTotalPosts();
        $universityPosts = $universityPostModel->getTotalPosts();
        $totalComments = $commentModel->getTotalComments();
        $totalUniversities = $universityMode->getTotalUniversitiees();


        ob_end_clean();
        echo json_encode([
            'success' => true,
            'data' => [
                'totalUsers' => $totalUsers,
                'totalGlobalPosts' => $totalPosts,
                'totalUniversityPosts' => $universityPosts,
                'totalComments' => $totalComments,
                'totalUniversities' => $totalUniversities,
            ],
        ]);
        exit;
    }
}

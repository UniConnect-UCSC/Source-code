<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/GlobalPost.php';
require_once __DIR__ . '/../models/UniversityPost.php';
require_once __DIR__ . '/../models/PostComment.php';

class Admin extends Controller
{
    public function index()
    {
        $this->view('adminlogin', [
            'title' => 'Admin Dashboard | UniConnect',
            'head' => '
            <link rel="stylesheet" href="/assets/css/pages/admin.css">
            <link rel="stylesheet" href="/assets/css/components/navbar.css">
            <link rel="stylesheet" href="/assets/css/components/navPanel.css">
            <link rel="stylesheet" href="/assets/css/pages/login.css">
            ',
        ]);
    }

    //Login 
    public function loginAdmin()
    {
        ob_start();
        header('Content-Type: application/json');

        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            ob_end_clean();
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
            exit;
        }

        $body = json_decode(file_get_contents('php://input'), true) ?? [];
        $email    = trim($body['email'] ?? '');
        $password = trim($body['password'] ?? '');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            ob_end_clean();
            echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
            exit;
        }

        if ($password === '') {
            ob_end_clean();
            echo json_encode(['success' => false, 'message' => 'Password is required.']);
            exit;
        }

        $adminEmail    = 'admin@uniconnect.com';
        $adminPassword = 'admin123';

        if ($email === $adminEmail && $password === $adminPassword) {
            session_regenerate_id(true);

            $_SESSION['is_admin'] = true;
            $_SESSION['admin_email'] = $email;

            session_write_close();

            ob_end_clean();
            echo json_encode(['success' => true, 'redirect' => '/admin/dashboard']);
            exit;
        }

        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Invalid email or password.']);
        exit;
    }

    public function logoutAdmin()
    {
        unset($_SESSION['is_admin']);
        header("Location: /admin");
        exit;
    }

    //Dashboard
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

        $totalUsers = $userModel->getTotalUsers();
        $totalPosts = $globalPostModel->getTotalPosts();
        $universityPosts = $universityPostModel->getTotalPosts();
        $totalComments = $commentModel->getTotalComments();

        error_log("University posts count: " . $universityPosts);

        ob_end_clean();
        echo json_encode([
            'success' => true,
            'data' => [
                'totalUsers' => $totalUsers,
                'totalGlobalPosts' => $totalPosts,
                'totalUniversityPosts' => $universityPosts,
                'totalComments' => $totalComments,
            ],
        ]);
        exit;
    }

    //Users
    public function users()
    {
        if (empty($_SESSION['is_admin'])) {
            header("Location: /admin");
            exit;
        }

        $userModel = new User();
        $users = $userModel->where(
            [],
            15,
            0,
            ['account_created_at' => 'DESC'],
            [],
            ['id', 'f_name', 'l_name', 'email', 'profile_picture', 'account_created_at', 'deleted_at'],

        );

        $this->view('adminUsers', [
            'title' => 'Admin Dashboard | UniConnect',
            'head' => '
            <link rel="stylesheet" href="/assets/css/pages/admin.css">
            <link rel="stylesheet" href="/assets/css/pages/adminUsers.css">
            <link rel="stylesheet" href="/assets/css/components/navbar.css">
            <link rel="stylesheet" href="/assets/css/components/navPanel.css">
            <link rel="stylesheet" href="/assets/css/components/usersTable.css">
            ',
            'users' => $users,
        ]);
    }

    public function usersScrollable()
    {
        ob_start();
        header('Content-Type: application/json');

        if (empty($_SESSION['is_admin'])) {
            ob_end_clean();
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $body = json_decode(file_get_contents('php://input'), true) ?? [];
        $offset = (int)($body['offset'] ?? 0);
        $limit = (int)($body['limit'] ?? 10);
        $search = trim($body['context']['search'] ?? '');

        $userModel = new User();

        if ($search !== '') {
            $users = $userModel->where(
                [
                    ['f_name', 'ILIKE', '%' . $search . '%', 'OR'],
                    ['l_name', 'ILIKE', '%' . $search . '%', 'OR'],
                    ['email', 'ILIKE', '%' . $search . '%'],
                ],
                $limit,
                $offset,
                ['account_created_at' => 'DESC'],
                [],
                ['id', 'f_name', 'l_name', 'email', 'profile_picture', 'account_created_at']
            );
        } else {
            $users = $userModel->where(
                [],
                $limit,
                $offset,
                ['account_created_at' => 'DESC'],
                [],
                ['id', 'f_name', 'l_name', 'email', 'profile_picture', 'account_created_at', 'deleted_at']
            );
        }

        ob_end_clean();
        echo json_encode($users ?: []);
        exit;
    }
    public function banUser()

    {
        ob_start();
        header('Content-Type: application/json');
        if (empty($_SESSION['is_admin'])) {
            ob_end_clean();
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            ob_end_clean();
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }

        $body = json_decode(file_get_contents('php://input'), true) ?? [];
        $userId = trim($body['user_id'] ?? '');

        if ($userId === '') {
            ob_end_clean();
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'user_id is required']);
            exit;
        }

        $userModel = new User();
        $ok = $userModel->delete([['id', '=', $userId]], true); // soft delete

        ob_end_clean();
        echo json_encode(['success' => (bool)$ok]);
        exit;
    }

    public function unbanUser()
    {
        ob_start();
        header('Content-Type: application/json');

        if (empty($_SESSION['is_admin'])) {
            ob_end_clean();
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            ob_end_clean();
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }

        $body = json_decode(file_get_contents('php://input'), true) ?? [];
        $userId = trim($body['user_id'] ?? '');

        if ($userId === '') {
            ob_end_clean();
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'user_id is required']);
            exit;
        }

        $userModel = new User();
        $ok = $userModel->update($userId, ['deleted_at' => null]);

        ob_end_clean();
        echo json_encode(['success' => (bool)$ok]);
        exit;
    }
}

<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/GlobalPost.php';
require_once __DIR__ . '/../models/UniversityPost.php';
require_once __DIR__ . '/../models/University.php';
require_once __DIR__ . '/../models/PostComment.php';
require_once __DIR__ . '/../models/RepresentativeRequest.php';
require_once __DIR__ . '/../models/Representative.php';

require_once __DIR__ . '/admin-traits/AdminUsersTrait.php';
require_once __DIR__ . '/admin-traits/AdminDashboardTrait.php';
require_once __DIR__ . '/admin-traits/AdminPostsTrait.php';
require_once __DIR__ . '/admin-traits/AdminUniRepTrait.php';
require_once __DIR__ . '/admin-traits/AdminEventTrait.php';





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

    use AdminDashboardTrait;
    use AdminUsersTrait;
    use AdminPostsTrait;
    use AdminUniRepTrait;
    use AdminEventTrait;
}

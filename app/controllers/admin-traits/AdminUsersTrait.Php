<?php
trait AdminUsersTrait
{
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
            'title' => 'Users | Admin | UniConnect',
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

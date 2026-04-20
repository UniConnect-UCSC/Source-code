<?php

trait AdminPostsTrait
{
    public function posts()
    {
        if (empty($_SESSION['is_admin'])) {
            header("Location: /admin");
            exit;
        }

        $globalPostModel = new GlobalPost();

        $globalPosts = $globalPostModel->where(
            [],
            15,
            0,
            ['created_at' => 'DESC'],
            [],
            ['id', 'user_id', 'caption', 'is_anonymous', 'reported', 'deleted_at', 'created_at']
        ) ?: [];

        $this->view('adminPosts', [
            'title' => 'Admin Dashboard | UniConnect',
            'head' => '
            <link rel="stylesheet" href="/assets/css/pages/admin.css">
            <link rel="stylesheet" href="/assets/css/pages/adminPosts.css">
            <link rel="stylesheet" href="/assets/css/components/navbar.css">
            <link rel="stylesheet" href="/assets/css/components/navPanel.css">
            <link rel="stylesheet" href="/assets/css/components/postsTable.css">
            ',
            'globalPosts' => $globalPosts,
        ]);
    }

    public function postsScrollable()
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
        $type = trim($body['context']['type'] ?? 'global');
        $search = trim($body['context']['search'] ?? '');

        if ($type === 'university') {
            $model = new UniversityPost();

            $selected = [
                'm.id',
                'm.user_id',
                'm.university_id',
                'u.name AS university_name',
                'm.caption',
                'm.is_anonymous',
                'm.reported',
                'm.deleted_at',
                'm.created_at',
            ];

            $join = [
                ['universities', 'm.university_id = u.id', 'LEFT', 'u'],
            ];

            if ($search !== '') {
                $rows = $model->where(
                    [
                        ['m.caption', 'ILIKE', '%' . $search . '%', 'OR'],
                        ['CAST(m.id AS TEXT)', 'ILIKE', '%' . $search . '%', 'OR'],
                        ['CAST(m.user_id AS TEXT)', 'ILIKE', '%' . $search . '%', 'OR'],
                        ['u.name', 'ILIKE', '%' . $search . '%'],
                    ],
                    $limit,
                    $offset,
                    ['m.created_at' => 'DESC'],
                    $join,
                    $selected
                ) ?: [];
            } else {
                $rows = $model->where(
                    [],
                    $limit,
                    $offset,
                    ['m.created_at' => 'DESC'],
                    $join,
                    $selected
                ) ?: [];
            }
        } else {
            $model = new GlobalPost();
            $selected = ['id', 'user_id', 'caption', 'is_anonymous', 'reported', 'deleted_at', 'created_at'];

            if ($search !== '') {
                $rows = $model->where(
                    [
                        ['caption', 'ILIKE', '%' . $search . '%', 'OR'],
                        ['CAST(id AS TEXT)', 'ILIKE', '%' . $search . '%', 'OR'],
                        ['CAST(user_id AS TEXT)', 'ILIKE', '%' . $search . '%'],
                    ],
                    $limit,
                    $offset,
                    ['created_at' => 'DESC'],
                    [],
                    $selected
                ) ?: [];
            } else {
                $rows = $model->where(
                    [],
                    $limit,
                    $offset,
                    ['created_at' => 'DESC'],
                    [],
                    $selected
                ) ?: [];
            }

            foreach ($rows as $row) {
                $row->university_id = null;
            }
        }

        ob_end_clean();
        echo json_encode($rows);
        exit;
    }

    public function deletePost()
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
        $postId = (int)($body['post_id'] ?? 0);
        $postType = trim($body['post_type'] ?? '');

        if ($postId <= 0) {
            ob_end_clean();
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'post_id is required']);
            exit;
        }

        if (!in_array($postType, ['global', 'university'], true)) {
            ob_end_clean();
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid post_type']);
            exit;
        }

        $model = $postType === 'global' ? new GlobalPost() : new UniversityPost();
        $ok = $model->delete([['id', '=', $postId]]);

        ob_end_clean();
        echo json_encode(['success' => (bool)$ok]);
        exit;
    }
}

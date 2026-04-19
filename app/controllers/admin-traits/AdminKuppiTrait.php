<?php

trait AdminKuppiTrait
{
    public function kuppi()
    {
        if (empty($_SESSION['is_admin'])) {
            header("Location: /admin");
            exit;
        }

        $kuppiModel = new KuppiModel();

        $kuppi = $kuppiModel->where(
            [],
            15,
            0,
            ['m.kuppi_date_time' => 'DESC'],
            [
                ['users', 'm.host_id = h.id', 'LEFT', 'h'],
                ['universities', 'm.university_id = u.id', 'LEFT', 'u'],
                ['kuppi_categories', 'm.category_id = c.id', 'LEFT', 'c'],
            ],
            [
                'm.id',
                'm.topic',
                'm.status',
                'm.kuppi_date_time',
                'm.participants',
                'm.host_id',
                'u.name AS university_name',
                'h.f_name AS host_f_name',
                'h.l_name AS host_l_name',
                'c.category_name AS category_name',
            ]
        ) ?: [];

        $this->view('adminKuppi', [
            'title' => 'Admin Dashboard | UniConnect',
            'head' => '
            <link rel="stylesheet" href="/assets/css/pages/admin.css">
            <link rel="stylesheet" href="/assets/css/pages/adminPosts.css">
            <link rel="stylesheet" href="/assets/css/components/navbar.css">
            <link rel="stylesheet" href="/assets/css/components/navPanel.css">
            <link rel="stylesheet" href="/assets/css/components/kuppiTable.css">
            ',
            'kuppi' => $kuppi,
        ]);
    }

    public function kuppiScrollable()
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

        $kuppiModel = new KuppiModel();

        $conditions = [];
        if ($search !== '') {
            $conditions = [
                ['m.topic', 'ILIKE', '%' . $search . '%', 'OR'],
                ['m.status', 'ILIKE', '%' . $search . '%', 'OR'],
                ['u.name', 'ILIKE', '%' . $search . '%', 'OR'],
                ['h.f_name', 'ILIKE', '%' . $search . '%', 'OR'],
                ['h.l_name', 'ILIKE', '%' . $search . '%', 'OR'],
                ['c.category_name', 'ILIKE', '%' . $search . '%', 'OR'],
                ['CAST(m.id AS TEXT)', 'ILIKE', '%' . $search . '%'],
            ];
        }

        $kuppi = $kuppiModel->where(
            $conditions,
            $limit,
            $offset,
            ['m.kuppi_date_time' => 'DESC'],
            [
                ['users', 'm.host_id = h.id', 'LEFT', 'h'],
                ['universities', 'm.university_id = u.id', 'LEFT', 'u'],
                ['kuppi_categories', 'm.category_id = c.id', 'LEFT', 'c'],
            ],
            [
                'm.id',
                'm.topic',
                'm.status',
                'm.kuppi_date_time',
                'm.participants',
                'm.host_id',
                'u.name AS university_name',
                'h.f_name AS host_f_name',
                'h.l_name AS host_l_name',
                'c.category_name AS category_name',
            ]
        ) ?: [];

        ob_end_clean();
        echo json_encode($kuppi);
        exit;
    }

    public function approveKuppiRequest()
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
        $kuppiId = trim($body['kuppi_id'] ?? '');

        if (empty($kuppiId)) {
            ob_end_clean();
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'kuppi_id is required']);
            exit;
        }

        $kuppiModel = new KuppiModel();
        $ok = $kuppiModel->update($kuppiId, ['status' => 'Upcoming']);

        ob_end_clean();
        echo json_encode(['success' => (bool)$ok]);
        exit;
    }
}

<?php

trait AdminUniRepTrait
{
    public function unirep()
    {
        if (empty($_SESSION['is_admin'])) {
            header("Location: /admin");
            exit;
        }

        $representativeRequestModel = new RepresentativeRequest();
        $representativeModel = new UniversityRepresentative();

        $repRequests = $representativeRequestModel->where(
            [
                ['status', '=', 'pending'],
            ],
            15,
            0,
            ['requested_at' => 'DESC'],
            [
                ['users', 'm.student_id = s.id', 'LEFT', 's'],
                ['universities', 'm.university_id = u.id', 'LEFT', 'u'],
            ],
            [
                'm.id',
                'm.student_id',
                'm.university_id',
                's.f_name AS student_f_name',
                's.l_name AS student_l_name',
                'u.name AS university_name',
                'm.requested_at',
                'm.proof_url',
                'm.status',
                'm.reviewed_by',
                'm.reviewed_at',
                'm.review_notes',
            ]
        ) ?: [];

        $uniReps = $representativeModel->where(
            [],
            15,
            0,
            ['registered_at' => 'DESC'],
            [
                ['users', 'm.id = s.id', 'LEFT', 's'],
                ['universities', 'm.university_id = u.id', 'LEFT', 'u'],
            ],
            [
                'm.id',
                'm.university_id',
                's.f_name AS rep_f_name',
                's.l_name AS rep_l_name',
                'u.name AS university_name',
                'm.registered_at',
                'm.proof_url',
            ]
        ) ?: [];

        $this->view('adminUniRep', [
            'title' => 'Admin Dashboard | UniConnect',
            'head' => '
            <link rel="stylesheet" href="/assets/css/pages/admin.css">
            <link rel="stylesheet" href="/assets/css/pages/adminPosts.css">
            <link rel="stylesheet" href="/assets/css/components/navbar.css">
            <link rel="stylesheet" href="/assets/css/components/navPanel.css">
            <link rel="stylesheet" href="/assets/css/components/uniRepTable.css">
            ',
            'repRequests' => $repRequests,
            'uniReps' => $uniReps,
        ]);
    }

    public function uniRepScrollable()
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
        $type = trim($body['context']['type'] ?? 'pending');

        if ($type === 'reps') {
            $model = new UniversityRepresentative();

            $rows = $model->where(
                [],
                $limit,
                $offset,
                ['m.registered_at' => 'DESC'],
                [
                    ['users', 'm.id = s.id', 'LEFT', 's'],
                    ['universities', 'm.university_id = u.id', 'LEFT', 'u'],
                ],
                [
                    'm.id',
                    'm.university_id',
                    's.f_name AS rep_f_name',
                    's.l_name AS rep_l_name',
                    'u.name AS university_name',
                    'm.registered_at',
                    'm.proof_url',
                ]
            ) ?: [];

            ob_end_clean();
            echo json_encode($rows);
            exit;
        }

        $model = new RepresentativeRequest();

        $rows = $model->where(
            [
                ['m.status', '=', 'pending'],
            ],
            $limit,
            $offset,
            ['m.requested_at' => 'DESC'],
            [
                ['users', 'm.student_id = s.id', 'LEFT', 's'],
                ['universities', 'm.university_id = u.id', 'LEFT', 'u'],
            ],
            [
                'm.id',
                'm.student_id',
                'm.university_id',
                's.f_name AS student_f_name',
                's.l_name AS student_l_name',
                'u.name AS university_name',
                'm.requested_at',
                'm.proof_url',
                'm.status',
                'm.reviewed_by',
                'm.reviewed_at',
                'm.review_notes',
            ]
        ) ?: [];

        ob_end_clean();
        echo json_encode($rows);
        exit;
    }

    public function approveRepRequest()
    {
        ob_start();
        header('Content-Type: application/json');

        try {
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
            $requestId = (int)($body['request_id'] ?? 0);

            if ($requestId <= 0) {
                ob_end_clean();
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'request_id is required']);
                exit;
            }

            $requestModel = new RepresentativeRequest();
            $repModel = new UniversityRepresentative();

            $request = $requestModel->first(['id' => $requestId]);

            if (!$request) {
                ob_end_clean();
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Request not found']);
                exit;
            }

            if (($request->status ?? '') !== 'pending') {
                ob_end_clean();
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Request is not pending']);
                exit;
            }

            $existingRepId = $repModel->getRep($request->university_id);
            if (!empty($existingRepId)) {
                $repModel->stepDown($existingRepId);
            }

            if ($repModel->isRep($request->student_id)) {
                $repModel->stepDown($request->student_id);
            }

            $inserted = $repModel->insert(
                ['id', 'university_id', 'proof_url'],
                [$request->student_id, $request->university_id, $request->proof_url]
            );

            if (!$inserted) {
                throw new Exception('Failed to create representative');
            }

            $updateData = [
                'status' => 'approved',
                'reviewed_at' => date('Y-m-d H:i:s'),
            ];

            // if (!empty($_SESSION['user_id'])) {
            //     $updateData['reviewed_by'] = $_SESSION['user_id'];
            // }

            $updated = $requestModel->update($requestId, $updateData);
            if (!$updated) {
                throw new Exception('Failed to update request status');
            }

            ob_end_clean();
            echo json_encode(['success' => true]);
            exit;
        } catch (Throwable $e) {
            error_log('approveRepRequest error: ' . $e->getMessage());
            ob_end_clean();
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Server error while approving request',
            ]);
            exit;
        }
    }

    public function removeUniRep()
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
        $repId = trim($body['rep_id'] ?? '');

        if ($repId === '') {
            ob_end_clean();
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'rep_id is required']);
            exit;
        }

        $repModel = new UniversityRepresentative();
        $ok = $repModel->stepDown($repId);

        ob_end_clean();
        echo json_encode(['success' => (bool)$ok]);
        exit;
    }
}

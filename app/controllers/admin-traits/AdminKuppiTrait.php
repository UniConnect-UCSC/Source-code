<?php

trait AdminKuppiTrait
{
    public function kuppi()
    {
        if (empty($_SESSION['is_admin'])) {
            header("Location: /admin");
            exit;
        }

        $reportModel = new KuppiReportModel();
        $kuppi = $reportModel->getAllKuppiReports(0, 15);

        $this->view('adminKuppi', [
            'title' => 'Admin Dashboard | UniConnect',
            'head' => '
            <link rel="stylesheet" href="/assets/css/pages/admin.css">
            <link rel="stylesheet" href="/assets/css/pages/adminPosts.css">
            <link rel="stylesheet" href="/assets/css/components/navbar.css">
            <link rel="stylesheet" href="/assets/css/components/navPanel.css">
            <link rel="stylesheet" href="/assets/css/components/kuppiTable.css">
            ',
            'kuppi' => $kuppi ?: [],
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
        $reportStatus = trim($body['context']['reportStatus'] ?? '');

        $reportModel = new KuppiReportModel();
        $rows = $reportModel->getAllKuppiReports(
            $offset,
            $limit,
            $reportStatus !== '' ? $reportStatus : null,
            $search
        );

        ob_end_clean();
        echo json_encode($rows);
        exit;
    }

    public function resolveKuppiReport()
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
        $action = trim($body['action'] ?? '');

        if ($kuppiId === '' || !in_array($action, ['approve', 'reject'], true)) {
            ob_end_clean();
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid input']);
            exit;
        }

        $reportModel = new KuppiReportModel();
        $kuppiModel = new KuppiModel();

        if (!$reportModel->hasPendingReports($kuppiId)) {
            ob_end_clean();
            echo json_encode(['success' => false, 'message' => 'No pending reports for this kuppi']);
            exit;
        }

        $decision = $action === 'approve' ? 'Kuppi Approved' : 'Kuppi Rejected';
        $resolved = $reportModel->resolveReportsForKuppi($kuppiId, $decision);

        if (!$resolved) {
            ob_end_clean();
            echo json_encode(['success' => false, 'message' => 'Failed to resolve reports']);
            exit;
        }

        if ($action === 'reject') {
            $kuppiModel->update($kuppiId, ['status' => 'Rejected']);
        }

        ob_end_clean();
        echo json_encode([
            'success' => true,
            'report_status' => 'Resolved',
            'decision' => $decision,
            'kuppi_status' => $action === 'reject' ? 'Rejected' : null
        ]);
        exit;
    }
}

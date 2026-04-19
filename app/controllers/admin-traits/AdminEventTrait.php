<?php

trait AdminEventTrait
{
    public function events()
    {
        if (empty($_SESSION['is_admin'])) {
            header("Location: /admin");
            exit;
        }

        $eventModel = new EventModel();

        $events = $eventModel->where(
            [],
            15,
            0,
            ['m.event_timestamp' => 'DESC'],
            [
                ['universities', 'm.university_id = u.id', 'LEFT', 'u'],
            ],
            [
                'm.id',
                'm.title',
                'm.description',
                'm.event_timestamp',
                'm.held_at',
                'm.participant_count',
                'm.university_id',
                'u.name AS university_name',
            ],
            false
        ) ?: [];

        $this->view('adminEvents', [
            'title' => 'Admin Dashboard | UniConnect',
            'head' => '
            <link rel="stylesheet" href="/assets/css/pages/admin.css">
            <link rel="stylesheet" href="/assets/css/pages/adminPosts.css">
            <link rel="stylesheet" href="/assets/css/components/navbar.css">
            <link rel="stylesheet" href="/assets/css/components/navPanel.css">
            <link rel="stylesheet" href="/assets/css/components/eventsTable.css">
            ',
            'events' => $events,
        ]);
    }

    public function eventsScrollable()
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

        $eventModel = new EventModel();

        $conditions = [];
        if ($search !== '') {
            $conditions = [
                ['m.title', 'ILIKE', '%' . $search . '%', 'OR'],
                ['m.description', 'ILIKE', '%' . $search . '%', 'OR'],
                ['u.name', 'ILIKE', '%' . $search . '%'],
            ];
        }

        $events = $eventModel->where(
            $conditions,
            $limit,
            $offset,
            ['m.event_timestamp' => 'DESC'],
            [
                ['universities', 'm.university_id = u.id', 'LEFT', 'u'],
            ],
            [
                'm.id',
                'm.title',
                'm.description',
                'm.event_timestamp',
                'm.held_at',
                'm.participant_count',
                'm.university_id',
                'u.name AS university_name',
            ],
            false
        ) ?: [];

        ob_end_clean();
        echo json_encode($events);
        exit;
    }

    public function removeEvent()
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
        $eventId = trim($body['event_id'] ?? '');

        if (empty($eventId)) {
            ob_end_clean();
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'event_id is required']);
            exit;
        }

        try {
            // Delete category mappings first
            require_once __DIR__ . '/../../models/eventCategoryMapping.php';
            $mappingModel = new EventCategoryMappingModel();
            $mappingModel->deleteMappingsForEvent($eventId);

            // Delete participations
            require_once __DIR__ . '/../../models/eventParticipation.php';
            $participationModel = new EventParticipationModel();
            $participationModel->removeAllParticipatorsForEvent($eventId);

            require_once __DIR__ . '/../../models/eventFavorites.php';
            $favoriteModel = new EventFavoritesModel();
            $favoriteModel->removeAllFavoritesForEvent($eventId);

            $eventModel = new EventModel();
            $ok = $eventModel->delete([['id', '=', $eventId]]);

            ob_end_clean();
            echo json_encode(['success' => (bool)$ok]);
            exit;
        } catch (Exception $e) {
            error_log('removeEvent error: ' . $e->getMessage());
            ob_end_clean();
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to delete event.']);
            exit;
        }
    }
}

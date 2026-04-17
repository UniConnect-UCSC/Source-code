#!/usr/bin/env php
<?php

chdir(__DIR__);

$logFileLocation = __DIR__ . '/../logs/update_total_view_count.log';

ini_set('log_errors', 1);
ini_set('error_log', $logFileLocation);
error_reporting(E_ALL);

register_shutdown_function(function () {
    $error = error_get_last();
    if ($error !== null) {
        error_log("FATAL: " . print_r($error, true));
    }
});

require_once __DIR__ . '/../../app/core/config.php';
require_once __DIR__ . '/../../app/core/Database.php';
require_once __DIR__ . '/../../app/core/Model.php';

// This should be implemented where badges are fetched in batches and processed as this causes way too much load
class AllocateBadges{
    use Model;
    protected string $table = '';

    public function allocateBadges() {
        $nextBadges = $this->fetchNextBadges();

        foreach ($nextBadges as $badge) {
            switch($badge->type) {
                case 'view_based':
                    $this->handleViewBasedBadge($badge->id, $badge->count_threshold);
                    break;
                
                case 'count_based':
                    $this->handleCountBasedBadge($badge->id, $badge->count_threshold);
                    break;
                
                default:
                    error_log("Unknown badge type: " . $badge->type);
                    break;
            }
        }
    }

    private function handleCountBasedBadge($badgeId, $countThreshold) {
        $this->table = 'study_material_volunteers';

        $volunteersToAward = $this->where(
            selected: ['m.id'],
            conditions: [
                ['m.total_material_count', '>=', $countThreshold],
                ['sb.badge_id', 'IS', null]
            ],
            join: [
                ["student_badges", ["sb.student_id = m.id", ["sb.badge_id", "=", $badgeId]], "LEFT", 'sb']
            ]
        );

        $this->assignBadges(array_column($volunteersToAward, 'id'), $badgeId);
    }

    private function handleViewBasedBadge($badgeId, $viewThreshold) {
        $this->table = 'study_material_volunteers';


        $volunteersToAward = $this->where(
            selected: ['m.id'],
            conditions: [
                ['m.total_view', '>=', $viewThreshold],
                ['sb.badge_id', 'IS', null]
            ],
            join: [
                ["student_badges", ["sb.student_id = m.id", ["sb.badge_id", "=", $badgeId]], "LEFT", 'sb']
            ]
        );

        $this->assignBadges(array_column($volunteersToAward, 'id'), $badgeId);
    }


    private function fetchNextBadges(){
        $this->table = 'study_material_badges';

        $nextBadges = $this->where(
            selected: ['id', 'count_threshold', 'type'],
            conditions: [],
        );

        return $nextBadges;
    }


    private function assignBadges($userIds, $badgeId) {
        $this->table = 'student_badges';

        error_log("Assigning badge '$badgeId' to user IDs: " . implode(', ', $userIds));

        $data = [];
        foreach ($userIds as $userId) {
            $data[] = [
                'student_id' => $userId,
                'badge_id' => $badgeId
            ];
        }

        $this->insert(
            ['student_id', 'badge_id'],
            $data
        );
    }

}

class UpdateTotalViewCount {
    use Model;

    protected string $table = '';

    public function updateCounts() {
        $offset = 0;
        $limit = 100;

        while (true) {
            $volunteers = $this->getVolunteers($limit, $offset);

            if (empty($volunteers)) {
                break;
            }

            foreach ($volunteers as $volunteer) {
                $totalViews = $this->calculateTotalViews($volunteer->id);
                $this->updateVolunteerTotalViews($volunteer->id, $totalViews);
            }

            $offset += $limit;
        }
    }

    private function getVolunteers($limit, $offset) {
        
        $selected = ['id'];
        $orderBy = ['user_id' => 'ASC'];
        $this->table = 'study_material_volunteers';

        return $this->where(
            selected: $selected,
            conditions: [],
            orderBy: $orderBy,
            limit: $limit,
            offset: $offset
        );
    }

    private function calculateTotalViews($volunteerId) {
        
        $selected = [['SUM(view_count)', 'total_views']];
        $conditions = ['user_id' => $volunteerId];
        $this->table = 'study_materials';

        return $this->where(
            selected: $selected,
            conditions: $conditions
        )[0]->total_views ?? 0;
    }

    private function updateVolunteerTotalViews($userId, $totalViews) {
        $this->table = 'study_material_volunteers';

        $this->update(
            id: $userId,
            data: ['total_view' => $totalViews],
            id_column: 'id'
        );
    }
}

$logMessage = "[" . date('Y-m-d H:i:s') . "] Starting sm updater\n";
error_log($logMessage);

try {
    $updater = new UpdateTotalViewCount();
    $updater->updateCounts();
    $successMessage = "[" . date('Y-m-d H:i:s') . "] View count update completed successfully\n";
    error_log($successMessage);
    
    $badgeAllocator = new AllocateBadges();
    $badgeAllocator->allocateBadges();
    $badgeSuccessMessage = "[" . date('Y-m-d H:i:s') . "] Badge allocation completed successfully\n";
    error_log($badgeSuccessMessage);
   
    exit(0);
    
} catch (Exception $e) {
    $errorMessage = "[" . date('Y-m-d H:i:s') . "] ERROR: " . $e->getMessage() . "\n";
    $errorMessage .= "Stack trace: " . $e->getTraceAsString() . "\n";
    error_log($errorMessage);
    
    exit(1);
}

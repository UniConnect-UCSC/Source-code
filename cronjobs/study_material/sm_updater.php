#!/usr/bin/env php
<?php

chdir(__DIR__);

$logFileLocation = __DIR__ . '/../logs/sm_updater.log';

ini_set('log_errors', 1);
ini_set('error_log', $logFileLocation);
error_reporting(E_ALL);

register_shutdown_function(function () {
    $error = error_get_last();
    if ($error !== null) {
        error_log("FATAL: " . print_r($error, true));
    }
});

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../app/core/config.php';
require_once __DIR__ . '/../../app/core/Database.php';
require_once __DIR__ . '/../../app/core/Model.php';

// This should be implemented where badges are fetched in batches and processed as this causes way too much load
class AllocateBadges{
    use Model;
    protected string $table = '';

    public function allocateBadges() {
        error_log("[allocateBadges] Starting badge allocation process");
        $nextBadges = $this->fetchNextBadges();
        error_log("[allocateBadges] Fetched " . count($nextBadges) . " badges to process");

        foreach ($nextBadges as $badge) {
            error_log("[allocateBadges] Processing badge ID: " . $badge->id . ", type: " . $badge->type);
            switch($badge->type) {
                case 'view_based':
                    error_log("[allocateBadges] Handling view_based badge (ID: " . $badge->id . ", threshold: " . $badge->count_threshold . ")");
                    $this->handleViewBasedBadge($badge->id, $badge->count_threshold);
                    break;
                
                case 'count_based':
                    error_log("[allocateBadges] Handling count_based badge (ID: " . $badge->id . ", threshold: " . $badge->count_threshold . ")");
                    $this->handleCountBasedBadge($badge->id, $badge->count_threshold);
                    break;
                
                default:
                    error_log("[allocateBadges] Unknown badge type: " . $badge->type);
                    break;
            }
        }
        error_log("[allocateBadges] Badge allocation process completed");
    }

    private function handleCountBasedBadge($badgeId, $countThreshold) {
        error_log("[handleCountBasedBadge] Starting for badge ID: " . $badgeId . ", threshold: " . $countThreshold);
        $this->table = 'study_material_volunteers';

        $volunteersToAward = $this->where(
            selected: ['m.id'],
            conditions: [
                ['m.total_material_count', '>=', $countThreshold],
                ['sb.badge_id', 'IS', 'NULL']
            ],
            join: [
                ["student_badges", ["sb.student_id = m.id", ["sb.badge_id", "=", $badgeId]], "LEFT", 'sb']
            ]
        );

        if(empty($volunteersToAward)) {
            error_log("[handleCountBasedBadge] No volunteers found for badge ID: " . $badgeId);
            return;
        }

        error_log("[handleCountBasedBadge] Found " . count($volunteersToAward) . " volunteers to award badge ID: " . $badgeId);
        $this->assignBadges(array_column($volunteersToAward, 'id'), $badgeId);
        error_log("[handleCountBasedBadge] Completed for badge ID: " . $badgeId);
    }

    private function handleViewBasedBadge($badgeId, $viewThreshold) {
        error_log("[handleViewBasedBadge] Starting for badge ID: " . $badgeId . ", threshold: " . $viewThreshold);
        $this->table = 'study_material_volunteers';


        $volunteersToAward = $this->where(
            selected: ['m.id'],
            conditions: [
                ['m.total_views', '>=', $viewThreshold],
                ['sb.badge_id', 'IS', 'NULL']
            ],
            join: [
                ["student_badges", ["sb.student_id = m.id", ["sb.badge_id", "=", $badgeId]], "LEFT", 'sb']
            ]
        );

        if(empty($volunteersToAward)) {
            error_log("[handleViewBasedBadge] No volunteers found for badge ID: " . $badgeId);
            return;
        }

        error_log("[handleViewBasedBadge] Found " . count($volunteersToAward) . " volunteers to award badge ID: " . $badgeId);
        $this->assignBadges(array_column($volunteersToAward, 'id'), $badgeId);
        error_log("[handleViewBasedBadge] Completed for badge ID: " . $badgeId);
    }


    private function fetchNextBadges(){
        error_log("[fetchNextBadges] Starting to fetch badges");
        $this->table = 'study_material_badges';

        $nextBadges = $this->where(
            selected: ['id', 'count_threshold', 'type'],
            conditions: [],
        );

        error_log("[fetchNextBadges] Successfully fetched badges from database");
        return $nextBadges ? $nextBadges : [];
    }


    private function assignBadges($userIds, $badgeId) {
        error_log("[assignBadges] Starting badge assignment for badge ID: " . $badgeId . " to " . count($userIds) . " users");
        $this->table = 'student_badges';

        error_log("[assignBadges] User IDs: " . implode(', ', $userIds));

        $data = [];
        foreach ($userIds as $userId) {
            $data[] = [
                $userId,
                $badgeId
            ];
        }

        $this->insert(
            ['student_id', 'badge_id'],
            $data
        );
        error_log("[assignBadges] Successfully assigned badge ID: " . $badgeId . " to " . count($userIds) . " users");
    }

}

class UpdateTotalViewCount {
    use Model;

    protected string $table = '';

    public function updateCounts() {
        error_log("[updateCounts] Starting to update view counts");
        $offset = 0;
        $limit = 100;
        $processedCount = 0;

        while (true) {
            error_log("[updateCounts] Fetching volunteers with offset: " . $offset . ", limit: " . $limit);
            $volunteers = $this->getVolunteers($limit, $offset);

            if (empty($volunteers)) {
                error_log("[updateCounts] No more volunteers to process. Total processed: " . $processedCount);
                break;
            }

            error_log("[updateCounts] Processing " . count($volunteers) . " volunteers");
            foreach ($volunteers as $volunteer) {
                error_log("[updateCounts] Calculating total views for volunteer ID: " . $volunteer->id);
                $totalViews = $this->calculateTotalViews($volunteer->id);
                error_log("[updateCounts] Volunteer ID " . $volunteer->id . ' has ' . $totalViews . ' total views, updating...');
                $this->updateVolunteerTotalViews($volunteer->id, $totalViews);
                $processedCount++;
            }

            $offset += $limit;
        }
        error_log("[updateCounts] Update counts completed. Processed " . $processedCount . ' volunteers total');
    }

    private function getVolunteers($limit, $offset) {
        error_log("[getVolunteers] Fetching volunteers with limit: " . $limit . ", offset: " . $offset);
        $selected = ['id'];
        $this->table = 'study_material_volunteers';

        $result = $this->where(
            selected: $selected,
            conditions: [],
            limit: $limit,
            offset: $offset
        );
        
        return $result ? $result : [];
    }

    private function calculateTotalViews($volunteerId) {
        error_log("[calculateTotalViews] Calculating total views for volunteer ID: " . $volunteerId);
        $selected = [['SUM(view_count)', 'total_views']];
        $conditions = [['volunteer_id', '=', $volunteerId]];
        $this->table = 'study_materials';

        $result = $this->where(
            selected: $selected,
            conditions: $conditions
        )[0]->total_views ?? 0;
        error_log("[calculateTotalViews] Total views for volunteer ID " . $volunteerId . ': ' . $result);
        return $result;
    }

    private function updateVolunteerTotalViews($userId, $totalViews) {
        error_log("[updateVolunteerTotalViews] Updating volunteer ID: " . $userId . ' with total views: ' . $totalViews);
        $this->table = 'study_material_volunteers';

        $this->update(
            id: $userId,
            data: ['total_views' => $totalViews],
            id_column: 'id'
        );
        error_log("[updateVolunteerTotalViews] Successfully updated volunteer ID: " . $userId);
    }
}

$logMessage = "[" . date('Y-m-d H:i:s') . "] Starting sm updater\n";
error_log($logMessage);

try {
    error_log("[MAIN] Initializing UpdateTotalViewCount...");
    $updater = new UpdateTotalViewCount();
    
    error_log("[MAIN] Starting view count updates...");
    $updater->updateCounts();
    $successMessage = "[" . date('Y-m-d H:i:s') . "] View count update completed successfully\n";
    error_log($successMessage);
    
    error_log("[MAIN] Initializing AllocateBadges...");
    $badgeAllocator = new AllocateBadges();
    
    error_log("[MAIN] Starting badge allocation...");
    $badgeAllocator->allocateBadges();
    $badgeSuccessMessage = "[" . date('Y-m-d H:i:s') . "] Badge allocation completed successfully\n";
    error_log($badgeSuccessMessage);
   
    error_log("[MAIN] All tasks completed successfully");
    exit(0);
    
} catch (Exception $e) {
    $errorMessage = "[" . date('Y-m-d H:i:s') . "] ERROR: " . $e->getMessage() . "\n";
    $errorMessage .= "Stack trace: " . $e->getTraceAsString() . "\n";
    error_log($errorMessage);
    
    exit(1);
}

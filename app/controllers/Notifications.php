<?php

require_once __DIR__ . '/../core/functions.php';
require_once __DIR__ . '/../models/InAppUserNotifications.php';

class Notifications extends Controller
{
    private function getNextNotifications($limit, $offset) {
        $notificationModel = new NotificationModel();
        $data = $notificationModel->getNextNotifications($limit, $offset);
        
        // Handled on client side
        //$data->metadata = json_decode($data->metadata, true);

        return $data;

    }

    private function markAllNotificationAsRead($userId) {
        $notificationModel = new NotificationModel();
        return $notificationModel->markAllAsRead($userId);
    }

    private function markNotificationAsRead($notificationId) {
        $notificationModel = new NotificationModel();
        return $notificationModel->markAsRead($notificationId);
    }

    private function hasNewNotifications($userId, $lastCheckTimestamp) {
        $notificationModel = new NotificationModel();
        return $notificationModel->hasNewNotifications($userId, $lastCheckTimestamp);
    }

    public function scrollable() {
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
        
            $data = parseRequestData();

            header('Content-Type: application/json');

            switch($data['scrollIdentifier']) {
                case 'getNotifications':
                    $response = $this->getNextNotifications($data['limit'], $data['offset']);
                    echo json_encode($response);
                    break;
                default:
                    echo json_encode(['error' => 'Invalid action']);
                    break;
            }
        }
    }

    // Return values wouldn't be received due to the fire and forget nature of the request
    public function markAsRead() {
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
        
            $data = parseRequestData();
            header('Content-Type: application/json');

            $notificationId = $data ?? null;

             if($notificationId && $this->markNotificationAsRead($notificationId)) {
                 echo json_encode(['success' => true, 'message' => "Notification $notificationId marked as read."]);
             } else {
                 echo json_encode(['error' => 'Notification ID is required']);
             }
        }
    }

    public function markAllAsRead() {
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
        
            header('Content-Type: application/json');

             if($this->markAllNotificationAsRead($_SESSION['user_id'])) {
                 echo json_encode(['success' => true, 'message' => "All notification marked as read."]);
             } else {
                 echo json_encode(['error' => 'Notification ID is required']);
             }
        }
    }

    public function checkNew() {
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
        
            header('Content-Type: application/json');
            $data = parseRequestData();

            if(!isset($data['lastCheckTimestamp'])) {
                echo json_encode(['error' => 'lastCheckTimestamp is required']);
                return;
            }

            $hasNewNotification = $this->hasNewNotifications($_SESSION['user_id'], $data['lastCheckTimestamp']);

            echo json_encode([
                'hasNewNotifications' => $hasNewNotification,
            ]);
        }
    }

}
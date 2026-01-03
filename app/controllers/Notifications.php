<?php

require_once __DIR__ . '/../core/functions.php';
require_once __DIR__ . '/../models/InAppUserNotifications.php';

class Notifications extends Controller
{
    public function index(){
        $this->view('notifications', [
            'title' => 'Notification Management',
            'head' => 
                '<link rel="stylesheet" href="/assets/css/pages/notifications.css">'
        ]);
    }

    private function parseAjaxData(){
        $json = file_get_contents('php://input');
        return json_decode($json, true);
    }

    private function getNextNotifications($limit, $offset, $notificationFilter) {
        $notificationModel = new NotificationModel();

        switch($notificationFilter) {
            case 'all':
                $data = $notificationModel->getNextAllNotifications($limit, $offset);
                break;
            case 'unread':
                $data = $notificationModel->getNextUnreadNotifications($limit, $offset);
                break;
            default:
                throw new Exception("Invalid notification filter: $notificationFilter");
        }

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

    private function getUnreadCount($userId) {
        $notificationModel = new NotificationModel();
        return $notificationModel->getUnreadCount($userId);
    }

    public function scrollable() {
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
        
            $data = parseRequestData();

            header('Content-Type: application/json');

            switch($data['scrollIdentifier']) {
                case 'getNotifications':
                    $response = $this->getNextNotifications($data['limit'], $data['offset'], $data['context']);
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

    public function getUnreadNotificationCount() {
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
        
            header('Content-Type: application/json');

            $unreadCount = $this->getUnreadCount($_SESSION['user_id']);

            echo json_encode([
                'unreadNotificationCount' => $unreadCount,
            ]);
        }
    }

    public function sendCustomNotification(){
        header('Content-Type: application/json');

        if($_SERVER['REQUEST_METHOD'] !== 'POST'){
            http_response_code(405);
            echo json_encode(['error' => 'Invalid request method']);
            return;
        }

        $data = $this->parseAjaxData();

        // Validate required fields
        if(empty($data['type']) || empty($data['title']) || empty($data['message'])){
            http_response_code(400);
            echo json_encode(['error' => 'Type, title, and message are required']);
            return;
        }

        global $notificationService;

        // Get user_id from data or use session user
        $userId = !empty($data['user_id']) ? $data['user_id'] : $_SESSION['user_id'];

        // Create metadata from URL if provided
        $metadata = [];
        if(!empty($data['url'])){
            $metadata['url'] = $data['url'];
        }

        // Create notification object
        $notification = new Notification(
            $data['type'],
            $data['title'],
            $data['message'],
            $metadata
        );

        require_once(__DIR__ . '/../notifications/recipientProviders/singleUserProvider.php');
        $singleUserProvider = new SingleUserProvider($userId);

        try {
            $notificationService->notify($notification, $singleUserProvider, ["in_app"]);
            echo json_encode([
                'status' => 'success',
                'message' => 'Custom notification sent successfully',
                'user_id' => $userId
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to send notification: ' . $e->getMessage()]);
        }
    }

    public function sendDummyNotification(){
        header('Content-Type: application/json');

        if($_SERVER['REQUEST_METHOD'] !== 'POST'){
            http_response_code(405);
            echo json_encode(['error' => 'Invalid request method']);
            return;
        }

        $data = $this->parseAjaxData();
        global $notificationService;

        // Get user_id from data or use session user
        $userId = !empty($data['user_id']) ? $data['user_id'] : $_SESSION['user_id'];

        // List of dummy notifications (expanded)
        $dummyNotifications = [
            [ 'type' => 'event_info', 'title' => 'Event Reminder', 'message' => 'Don\'t forget the Hackathon tomorrow!', 'metadata' => ['url' => '/events/hackathon'] ],
            [ 'type' => 'event_info', 'title' => 'Profile Update', 'message' => 'Your profile was updated successfully.', 'metadata' => ['url' => '/profile'] ],
            [ 'type' => 'event_info', 'title' => 'New Event Created', 'message' => 'Tech Talk: Cloud Computing is happening next week.', 'metadata' => ['url' => '/events/cloud-computing'] ],
            [ 'type' => 'event_info', 'title' => 'Event Cancelled', 'message' => 'The workshop on AI has been cancelled.', 'metadata' => ['url' => '/events'] ],
            [ 'type' => 'event_info', 'title' => 'Event Registration Confirmed', 'message' => 'You are registered for the Career Fair 2024.', 'metadata' => ['url' => '/events/career-fair'] ],
            [ 'type' => 'event_info', 'title' => 'Venue Changed', 'message' => 'The venue for the Robotics Seminar has changed to Hall B.', 'metadata' => ['url' => '/events/robotics'] ],
            [ 'type' => 'event_info', 'title' => 'Event Postponed', 'message' => 'The Data Science Workshop has been postponed to next month.', 'metadata' => ['url' => '/events/data-science'] ],
            [ 'type' => 'event_info', 'title' => 'Speaker Announced', 'message' => 'Dr. Smith will be the keynote speaker at the AI Conference.', 'metadata' => ['url' => '/events/ai-conference'] ],
            [ 'type' => 'event_info', 'title' => 'Event Full', 'message' => 'Registration for the Startup Bootcamp is now full.', 'metadata' => ['url' => '/events/startup-bootcamp'] ],
            [ 'type' => 'event_info', 'title' => 'Event Feedback', 'message' => 'Please provide feedback for the recently concluded Coding Marathon.', 'metadata' => ['url' => '/events/coding-marathon/feedback'] ],
            [ 'type' => 'event_info', 'title' => 'Event Reminder', 'message' => 'The Sports Day starts in 1 hour!', 'metadata' => ['url' => '/events/sports-day'] ],
            [ 'type' => 'event_info', 'title' => 'Event Materials Uploaded', 'message' => 'Presentation slides for the Blockchain Seminar are now available.', 'metadata' => ['url' => '/events/blockchain/materials'] ],
            [ 'type' => 'event_info', 'title' => 'Event Invitation', 'message' => 'You are invited to the Alumni Meet 2025.', 'metadata' => ['url' => '/events/alumni-meet'] ],
            [ 'type' => 'event_info', 'title' => 'Event Registration Open', 'message' => 'Registration for the Summer Internship Fair is now open.', 'metadata' => ['url' => '/events/internship-fair'] ],
            [ 'type' => 'event_info', 'title' => 'Event Registration Closed', 'message' => 'Registration for the Art Exhibition is now closed.', 'metadata' => ['url' => '/events/art-exhibition'] ],
            [ 'type' => 'event_info', 'title' => 'Event Reminder', 'message' => 'Don\'t miss the Guest Lecture on Quantum Computing tomorrow.', 'metadata' => ['url' => '/events/quantum-lecture'] ],
            [ 'type' => 'event_info', 'title' => 'Event Cancelled', 'message' => 'The Outdoor Movie Night has been cancelled due to weather.', 'metadata' => ['url' => '/events/movie-night'] ],
            [ 'type' => 'event_info', 'title' => 'Event Updated', 'message' => 'The schedule for the Career Fair has been updated.', 'metadata' => ['url' => '/events/career-fair'] ],
            [ 'type' => 'event_info', 'title' => 'Event Reminder', 'message' => 'The Music Festival starts at 6 PM today!', 'metadata' => ['url' => '/events/music-festival'] ],
            [ 'type' => 'event_info', 'title' => 'Event Registration Confirmed', 'message' => 'You are confirmed for the Leadership Workshop.', 'metadata' => ['url' => '/events/leadership-workshop'] ],
            [ 'type' => 'system_info', 'title' => 'New Message', 'message' => 'You have a new message from John Doe.', 'metadata' => ['url' => '/messages'] ],
            [ 'type' => 'system_info', 'title' => 'Friend Request', 'message' => 'Jane Smith sent you a friend request.', 'metadata' => ['url' => '/friends'] ],
            [ 'type' => 'marketplace_info', 'title' => 'Item Sold', 'message' => 'Your item "Laptop" has been sold!', 'metadata' => ['url' => '/marketplace/items'] ],
            [ 'type' => 'kuppi_info', 'title' => 'Kuppi Scheduled', 'message' => 'A new study session has been scheduled for Mathematics.', 'metadata' => ['url' => '/kuppi'] ],
            [ 'type' => 'system_info', 'title' => 'System Update', 'message' => 'UniConnect will be under maintenance tonight from 11 PM to 2 AM.', 'metadata' => ['url' => '/'] ],
        ];

        // Pick a random notification
        $randomIndex = array_rand($dummyNotifications);
        $selected = $dummyNotifications[$randomIndex];

        $notification = new Notification(
            $selected['type'],
            $selected['title'],
            $selected['message'],
            $selected['metadata']
        );

        require_once(__DIR__ . '/../notifications/recipientProviders/singleUserProvider.php');
        $singleUserProvider = new SingleUserProvider($userId);

        try {
            $notificationService->notify($notification, $singleUserProvider, ["in_app"]);
            echo json_encode([
                'status' => 'success',
                'message' => 'Dummy notification sent successfully',
                'notification' => $selected,
                'user_id' => $userId
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to send notification: ' . $e->getMessage()]);
        }
    }
}

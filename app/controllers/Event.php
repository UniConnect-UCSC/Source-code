<?php

require_once(__DIR__ . "/../models/Event.php");
require_once __DIR__ . '/../core/functions.php';
require_once(__DIR__ . "/../models/University.php");

class Event extends Controller
{
    private function getEventData($limit, $offset, $categories = [])
    {

        $eventModel = new EventModel();
        $response = $eventModel->getEvents($limit, $offset);
        $universityModel = new University();

        foreach ($response as $event) {

            $event->university_name = $universityModel->getUniversityName($event->university_id);
        }
        return $response;
    }

    private function checkIfUniRep($userId){
        require_once(__DIR__ . "/../models/Representative.php");
        $repModel = new UniversityRepresentative();
        return $repModel->isRep($userId);
    }
    
    private function getUniRepUniversity($userId){
        require_once(__DIR__ . "/../models/Representative.php");
        $repModel = new UniversityRepresentative();
        $repDetails = $repModel->getRepDetails($userId);
        return $repDetails ? $repDetails->university_id : null;
    }

    private function getEventCategories($searchTerm, $excludeIds, $limit, $offset){
        require_once(__DIR__ . "/../models/eventCategory.php");
        $categoryModel = new EventCategoryModel();
        return $categoryModel->getAllCategories($searchTerm, $excludeIds, $limit, $offset) ?? null;
    }
    
    private function uploadEventImage($file){

        if(!empty($file) && $file['error'] === UPLOAD_ERR_OK){
            $uploadedUrl = uploadImageToCloudinary($file['tmp_name'], 'uniconnect_events');
            if($uploadedUrl){
                return $uploadedUrl;
            }else{
                error_log('Cloudinary upload failed for event image by user ' . ($_SESSION['user_id'] ?? 'unknown'));
                return null;
            }
        }
    }

    
    public function getRepEvents($limit, $offset){

        if(!$this->checkIfUniRep($_SESSION['user_id'])){
            return null;
        }

        $eventModel = new EventModel();
        $universityId = $this->getUniRepUniversity($_SESSION['user_id']);

        if ($universityId) {
            $events = $eventModel->getUniUpcomingEvents($universityId, $limit, $offset);
            return $events;
        } else {
            return null;
        }
    }

    // Fix redundant verification code
    public function deleteEvent(){

        header('Content-Type: application/json');
        $data = parseRequestData();

        if($_SERVER['REQUEST_METHOD'] !== 'POST'){
            http_response_code(405);
            echo json_encode(['error' => 'Invalid request method']);
            return;
        }

        $eventModel = new EventModel();

        // Validation of user access
        if(!isset($_SESSION['user_id']) || !$this->checkIfUniRep($_SESSION['user_id'])){
            http_response_code(403);
            echo json_encode(['error' => 'Unauthorized access']);
            return;
        }

        //Fetch user's university for verification
        $universityId = $this->getUniRepUniversity($_SESSION['user_id']);
        $eventUniId = $eventModel->getEventUni($data['event_id']);

        if($eventUniId != $universityId){
            http_response_code(403);
            echo json_encode(['error' => 'Unauthorized to modify this event']);
            return;
        }

        $result = $eventModel->deleteEvent($data['event_id']);

        if($result){
            echo json_encode(['success' => 'Event deleted successfully']);
        }else{
            http_response_code(500);
            echo json_encode(['error' => 'Failed to delete event']);
        }

    }

    public function updateEvent(){

        header('Content-Type: application/json');

        if($_SERVER['REQUEST_METHOD'] !== 'POST'){
            http_response_code(405);
            echo json_encode(['error' => 'Invalid request method']);
            return;
        }

        $data = parseRequestData();

        $eventModel = new EventModel();

        // Validation of user access
        if(!isset($_SESSION['user_id']) || !$this->checkIfUniRep($_SESSION['user_id'])){
            http_response_code(403);
            echo json_encode(['error' => 'Unauthorized access']);
            return;
        }

        //Fetch user's university for verification
        $universityId = $this->getUniRepUniversity($_SESSION['user_id']);
        $eventUniId = $eventModel->getEventUni($data['event_id']);

        if($eventUniId != $universityId){
            http_response_code(403);
            echo json_encode(['error' => 'Unauthorized to modify this event']);
            return;
        }

        // Data from view to model conversion
        $eventData = [
            'id' => $data['event_id'], // unset and used as condition in model method
            'posted_by' => $_SESSION['user_id'],
            'title' => trim($data['title']),
            'description' => trim($data['description']),
            'event_timestamp' => trim($data['event_timestamp']),
            'held_at' => trim($data['held_at']),
            'updated_at' => date('Y-m-d H:i:s', time())
        ];

        $result = $eventModel->update($data['event_id'], $eventData);

        if($result){
            echo json_encode(['status' => 'success']);
        }else{
            http_response_code(500);
            echo json_encode(['error' => 'Failed to update event']);
        }
    }
    
    public function createNewEvent(){
        header('Content-Type: application/json');

        $data = parseRequestData();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['error' => 'Invalid request method']); 
            return;
        }

        // Validation of user
        if (!isset($_SESSION['user_id']) || !$this->checkIfUniRep($_SESSION['user_id'])){
            echo json_encode(['error' => 'Unauthorized access']);
            return;
        }

        $mediaUrl = uploadImageToCloudinary($data['FILES']['event_image'] ?? null, 'uniconnect_events');

        // Data from view to model conversion
        $eventData = [
            'university_id' => $_SESSION['user_universityID'],
            'posted_by' => $_SESSION['user_id'],
            'title' => trim($data['title']),
            'description' => trim($data['description']),
            'event_timestamp' => trim($data['event_timestamp']),
            'held_at' => trim($data['held_at']),
            'media_url' => $mediaUrl
        ];

        $eventModel = new EventModel();
        $result = $eventModel->createEvent($eventData);

        if ($result) {
            echo json_encode(["status" => "success"]);
        } else {
            http_response_code(500);
            echo json_encode($error);

        }
    }

    public function scrollable(){

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $data = parseRequestData();
            error_log("data received for scrollable: " . print_r($data, true));

            header('Content-Type: application/json');

            switch($data['scrollIdentifier']){
                case 'getEvents':
                    $response = $this->getEventData($data["limit"], $data['offset'], $data['context']['categories'] ?? []) ?? [];
                    echo json_encode($response);
                    break;

                case 'getRepEvents':
                    $response = $this->getRepEvents($data["limit"], $data['offset']) ?? [];
                    echo json_encode($response);
                    break;

                case 'getCategories':
                    $response = $this->getEventCategories($data['context']['searchTerm'] ?? '', $data['context']['excludeIds'] ?? [], $data["limit"], $data['offset']) ?? [];
                    echo json_encode($response);
                    break;

                default:
                    http_response_code(400);
                    echo json_encode(['error' => 'Invalid scroll identifier']);
                    exit;
            }
        }
    }

    public function index(){

        $this->view('event', [
            'title' => 'Event Page',
            'head' => 
                '<link rel="stylesheet" href="/assets/css/components/event.css">
                <link rel="stylesheet" href="/assets/css/components/eventControls.css">
                <link rel="stylesheet" href="/assets/css/components/modal.css">
                <link rel="stylesheet" href="/assets/css/components/viewEventsModal.css">
                <link rel="stylesheet" href="/assets/css/components/feed.css"> 
                <link rel="stylesheet" href="/assets/css/components/navbar.css"> 
                <link rel="stylesheet" href="/assets/css/components/navPanel.css">
                <link rel="stylesheet" href="/assets/css/components/widgetPanel.css">
                <link rel="stylesheet" href="/assets/css/components/eventsWidget.css">
                <link rel="stylesheet" href="/assets/css/pages/home.css">
                ',

        ]);
    }

}

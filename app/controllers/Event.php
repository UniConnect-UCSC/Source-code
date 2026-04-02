<?php

require_once(__DIR__ . "/../models/Event.php");
require_once __DIR__ . '/../core/functions.php';
require_once(__DIR__ . "/../models/University.php");

class Event extends Controller
{
    private function getEventData($limit, $offset, $categories = [])
    {

        $eventModel = new EventModel();
        $response = $eventModel->getUpcomingEvents($limit, $offset, $categories);
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

    private function addCategoryMappings($eventId, $categories){
        require_once(__DIR__ . "/../models/eventCategoryMapping.php");
        $mappingModel = new EventCategoryMappingModel();
        $mappingModel->mapEventToCategory($eventId, $categories);
    }

    private function toggleEventFavorite($eventId, $userId, $currentStatus){
        require_once(__DIR__ . "/../models/eventFavorites.php");
        $favoriteModel = new EventFavoritesModel();

        if($currentStatus){
            $favoriteModel->removeFavorite($userId, $eventId);
            return false;
        }else{
            $favoriteModel->addFavorite($userId, $eventId);
            return true;
        }
    }

    private function toggleEventParticipation($eventId, $userId, $currentStatus){
        require_once(__DIR__ . "/../models/eventParticipation.php");
        $participationModel = new EventParticipationModel();
        $response = [];

        if($currentStatus){
            $response['participantCount'] = $participationModel->removeParticipation($userId, $eventId);
            $response['newStatus'] = false;
        }else{
            $response['participantCount'] = $participationModel->addParticipation($userId, $eventId);
            $response['newStatus'] = true;
        }

        return $response;
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
        $data["eventCategories"] = json_decode($data['eventCategories'], true); // Decode JSON string to array

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
        $eventId = $eventModel->createEvent($eventData);

        if (!$eventId) {
            http_response_code(500);
        } 

        // Handle event categories
        if (!empty($data["eventCategories"])){
            $this->addCategoryMappings($eventId, $data["eventCategories"]);
        }

        echo json_encode(["status" => "success"]);
    }

    public function toggle(){
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $data = parseRequestData();
            header('Content-Type: application/json');

            error_log("Toggle request data: " . print_r($data, true));

            switch($data['action']){
                case 'favorite':
                    $response = $this->toggleEventFavorite($data['event_id'], $_SESSION['user_id'], $data['current_status']);
                    echo json_encode(['newStatus' => $response]);
                    break;

                case 'participate':
                    $response = $this->toggleEventParticipation($data['event_id'], $_SESSION['user_id'], $data['current_status']);
                    echo json_encode(['newStatus' => $response['newStatus'], 'participantCount' => $response['participantCount']]);
                    break;

                default:
                    http_response_code(400);
                    echo json_encode(['error' => 'Invalid action']);
                    exit;
            }
            
        }
    }

    public function scrollable(){

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $data = parseRequestData();
            error_log("data received for scrollable: " . print_r($data, true));

            header('Content-Type: application/json');

            switch($data['scrollIdentifier']){
                case 'getEvents':
                    $filterCats = $data['context']['filterCategories'] ?? $data['filterCategories'] ?? [];
                    $response = $this->getEventData($data["limit"], $data['offset'], $filterCats) ?? [];
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

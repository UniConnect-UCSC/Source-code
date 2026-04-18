<?php

require_once(__DIR__ . "/../models/Event.php");
require_once __DIR__ . '/../core/functions.php';
require_once(__DIR__ . "/../models/University.php");

class Event extends Controller
{
    private function getEventData($limit, $offset, $categories = [], $searchTerm = '', $onlyFavorites = false, $filterType = "for-you"){

        $eventModel = new EventModel();
        $response = $eventModel->getUpcomingEvents($limit, $offset, $categories, $searchTerm, $onlyFavorites, $filterType);
        return $response;
    }

    private function getEventSuggestions($limit, $offset, $searchTerm){
        $eventModel = new EventModel();
        $response = $eventModel->getEventSuggestions($limit, $offset, $searchTerm);
        return $response;
    }

    private function checkIfUniRep($userId){
        if(!$userId){return false;}

        require_once(__DIR__ . "/../models/Representative.php");
        $repModel = new UniversityRepresentative();
        return $repModel->isRep($userId);
    }
    
    private function getUniRepUniversity(){
        return $_SESSION['user_universityID'];
    }

    private function getEventCategories($searchTerm, $excludeIds, $limit, $offset){
        require_once(__DIR__ . "/../models/eventCategory.php");
        $categoryModel = new EventCategoryModel();
        return $categoryModel->getAllCategories($searchTerm, $excludeIds, $limit, $offset) ?? null;
    }

    private function getEventTitle($eventId){
        $eventModel = new EventModel();
        return $eventModel->getEventTitle($eventId);
    }

    private function getNotifyingUsersForDeletion($eventId){
        require_once(__DIR__ . "/../models/eventParticipation.php");
        $participationModel = new EventParticipationModel();
        $participatingUserIds = $participationModel->getParticipatorsForEvent($eventId);

        require_once(__DIR__ . "/../models/eventFavorites.php");
        $favoriteModel = new EventFavoritesModel();
        $favoriteUserIds = $favoriteModel->getUsersForEvent($eventId);

        $userIds = [];

        foreach ($participatingUserIds as $row) {
            $userIds[$row->user_id] = true;
        }

        foreach ($favoriteUserIds as $row) {
            $userIds[$row->user_id] = true;
            }

        return array_keys($userIds);
    }

    //Done this instead of using a cascade is for a easier transition into soft deletion
    //Can break if exited in middle of the process
    //Implement a transaction like feature in the future
    private function deleteEventOrchestrator($eventId){

        global $notificationService;
        require_once(__DIR__ . "/../notifications/recipientProviders/deterministicMultiUserProvider.php");

        $userIds = $this->getNotifyingUsersForDeletion($eventId);
       
        $notification = new Notification(
            type: "event_deleted",
            title: "Event Cancellation Notice" ,
            message: "We regret to inform you that an event - " . $this->getEventTitle($eventId) . " has been cancelled. Sorry for the inconvenience.",
            metadata: [
                'url' => '/event'
            ]
        );

        $provider = new deterministicMultiUserProvider($userIds);

        $notificationService->notify($notification, $provider, ['in_app']);

        //Deletion of category mappings for the event
        require_once(__DIR__ . "/../models/eventCategoryMapping.php");
        $mappingModel = new EventCategoryMappingModel();
        $status = $mappingModel->deleteMappingsForEvent($eventId);
        if(!$status){return false;}

        //Deletion of participators for the event
        require_once(__DIR__ . "/../models/eventParticipation.php");
        $participationModel = new EventParticipationModel();
        $status = $participationModel->removeAllParticipatorsForEvent($eventId);
        if(!$status){return false;}

        //Deletion of favorites for the event
        require_once(__DIR__ . "/../models/eventFavorites.php");
        $favoriteModel = new EventFavoritesModel();
        $status = $favoriteModel->removeAllFavoritesForEvent($eventId);
        if(!$status){return false;}

        // Removal of the event
        $eventModel = new EventModel();
        $status = $eventModel->deleteEvent($eventId); 
        if(!$status){return false;}

        return true;
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

    private function updateCategoryMappings($eventId, $categories){
        require_once(__DIR__ . "/../models/eventCategoryMapping.php");
        $mappingModel = new EventCategoryMappingModel();
        $mappingModel->deleteMappingsForEvent($eventId);
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
        $universityId = $this->getUniRepUniversity();

        $events = $eventModel->getUniUpcomingEvents($universityId, $limit, $offset);
        return $events;
    }

    public function getAllCategoriesForEvent(){
        header('Content-Type: application/json');

        if($_SERVER['REQUEST_METHOD'] !== 'POST'){
            http_response_code(405);
            echo json_encode(['error' => 'Invalid request method']);
            return;
        }

        $data = parseRequestData();
        $eventId = $data['event_id'];

        require_once(__DIR__ . "/../models/eventCategoryMapping.php");
        $mappingModel = new EventCategoryMappingModel();
        $categories = $mappingModel->getCategoriesForEvent($eventId);

        echo json_encode(['success' => true, 'categories' => $categories]);
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

        // Validation of user access
        if(!isset($_SESSION['user_id']) || !$this->checkIfUniRep($_SESSION['user_id'])){
            http_response_code(403);
            echo json_encode(['error' => 'Unauthorized access']);
            return;
        }


        $model = new EventModel();
        $eventUni = $model->getEventUni($data['event_id']);
        if($eventUni != $this->getUniRepUniversity()){
            http_response_code(403);
            echo json_encode(['error' => 'Unauthorized to delete this event']);
            return;
        }

        $result = $this->deleteEventOrchestrator($data['event_id']);

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
        $universityId = $this->getUniRepUniversity();
        $eventUniId = $eventModel->getEventUni($data['event_id']);

        if($eventUniId != $universityId){
            http_response_code(403);
            echo json_encode(['error' => 'Unauthorized to modify this event']);
            return;
        }

        $mediaUrl = uploadImageToCloudinary($data['FILES']['event_image'] ?? null, 'uniconnect_events');

        // Data from view to model conversion
        $eventData = [
            'title' => trim($data['title']),
            'description' => trim($data['description']),
            'event_timestamp' => trim($data['event_timestamp']),
            'held_at' => trim($data['held_at']),
            'updated_at' => date('Y-m-d H:i:s', time())
        ];

        if($mediaUrl){
            $eventData['media_url'] = $mediaUrl;
        }

        $result = $eventModel->updateEvent($data['event_id'], $eventData);
        if($result && isset($data["eventCategories"])){
            $this->updateCategoryMappings($data['event_id'], json_decode($data["eventCategories"], true));
        }

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

        // Validate the event time
        if($data['event_timestamp'] < date('Y-m-d H:i:s')){
            echo json_encode(['error' => 'Event time must be in the future']);
            return;
        }

        $mediaUrl = uploadImageToCloudinary($data['FILES']['event_image'] ?? null, 'uniconnect_events');

        // Data from view to model conversion
        $eventData = [
            'university_id' => $_SESSION['user_universityID'],
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
            echo json_encode(['error' => 'Failed to create event']);
            return;
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
                    $filterCats = $data['context']['filterCategories'] ?? [];
                    $searchTerm = $data['context']['searchTerm'] ?? '';
                    $onlyFavorites = $data['context']['onlyFavorites']  ?? false;
                    $filterType = $data['context']['filterButton'] ?? "for-you";

                    $response = $this->getEventData($data["limit"], $data['offset'], $filterCats, $searchTerm, $onlyFavorites, $filterType) ?? [];
                    echo json_encode($response);
                    break;

                case 'getEventSuggestions':
                    $searchTerm = $data['context']['searchTerm'] ?? '';
                    $response = $this->getEventSuggestions($data["limit"], $data['offset'], $searchTerm) ?? [];
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
        $isUniRep = $this->checkIfUniRep($_SESSION['user_id'] ?? null);

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
            'isUniRep' => $isUniRep

        ]);
    }

}

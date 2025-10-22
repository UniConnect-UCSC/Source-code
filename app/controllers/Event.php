<?php

require_once(__DIR__ . "/../models/Event.php");

class Event extends Controller
{
    private function parseAjaxData(){
        $json = file_get_contents('php://input');
        return json_decode($json, true);
    }

    private function getEventData($limit, $offset)
    {
        error_log("Fetching event data with offset: " . $offset);
        $eventModel = new EventModel();
        return $eventModel->getEvents($limit, $offset);
    }

    private function checkIfUniRep($userId){
        error_log("Checking if user $userId is a university representative");
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
        $data = $this->parseAjaxData();

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

        $data = $this->parseAjaxData();

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

        $data = $this->parseAjaxData();

        $error = [];

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $error['method_invalid']++;
            echo json_encode($error); 
            return;
        }

        // Validation of user
        if (!isset($_SESSION['user_id']) || !$this->checkIfUniRep($_SESSION['user_id'])) {
            $error['unauthorized_access']++;
            echo json_encode($error);
            return;
        }

        //Fetching required data for submission
        //In the future make the db handle it through a join
        $universityId = $this->getUniRepUniversity($_SESSION['user_id']);
        
        // Data from view to model conversion
        $eventData = [
            'university_id' => $universityId,
            'posted_by' => $_SESSION['user_id'],
            'title' => trim($data['title']),
            'description' => trim($data['description']),
            'event_timestamp' => trim($data['event_timestamp']),
            'held_at' => trim($data['held_at'])
        ];

        $eventModel = new EventModel();
        $result = $eventModel->createEvent($eventData);

        header('Content-Type: application/json');
        if ($result) {
            echo json_encode(["status" => "success"]);
        } else {
            http_response_code(500);
            echo json_encode($error);

        }
    }

    public function scrollable(){

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $data = $this->parseAjaxData();

            header('Content-Type: application/json');
            if($data['scrollIdentifier'] === 'getEvents'){
                $response = $this->getEventData($data["limit"], $data['offset']) ?? [];
                echo json_encode($response);
                exit;

            }else if($data['scrollIdentifier'] === 'getRepEvents'){
                $response = $this->getRepEvents($data["limit"], $data['offset']) ?? [];
                error_log("Scrollable Rep Events Response: " . print_r($response, true));
                echo json_encode($response);
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
                <!--<link rel="stylesheet" href="/assets/css/components/eventsWidget.css">-->
                <link rel="stylesheet" href="/assets/css/pages/home.css">
                ',

        ]);
    }

}

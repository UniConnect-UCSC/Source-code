<?php

require_once(__DIR__ . "/../models/Event.php");

class Event extends Controller
{
    private function getEventData($offset)
    {
        error_log("Fetching event data with offset: " . $offset);
        $eventModel = new EventModel();
        return $eventModel->getEvents($offset);
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

    
    public function getRepEvents(){
        $eventModel = new EventModel();
        $universityId = $this->getUniRepUniversity($_SESSION['user_id']);

        if ($universityId) {
            $events = $eventModel->getUniUpcomingEvents($universityId);
            return $events;
            //echo json_encode($events);
        } else {
            http_response_code(403);
            echo json_encode(['error' => 'User is not a university representative']);
        }
    }

    // Fix redundent verification code
    public function deleteEvent(){

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
        $eventUniId = $eventModel->getEventUni($_POST['event_id']);

        if($eventUniId != $universityId){
            http_response_code(403);
            echo json_encode(['error' => 'Unauthorized to modify this event']);
            return;
        }

        $result = $eventModel->deleteEvent($_POST['event_id']);

        if($result){
            echo json_encode(['success' => 'Event deleted successfully']);
        }else{
            http_response_code(500);
            echo json_encode(['error' => 'Failed to delete event']);
        }

    }

    public function updateEvent(){

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
        $eventUniId = $eventModel->getEventUni($_POST['event_id']);

        if($eventUniId != $universityId){
            http_response_code(403);
            echo json_encode(['error' => 'Unauthorized to modify this event']);
            return;
        }

        // Data from view to model conversion
        $eventData = [
            'id' => $_POST['event_id'], // unset and used as condition in model method
            'posted_by' => $_SESSION['user_id'],
            'title' => trim($_POST['title']),
            'description' => trim($_POST['description']),
            'event_timestamp' => trim($_POST['event_timestamp']),
            'held_at' => trim($_POST['held_at']),
            'updated_at' => date('Y-m-d H:i:s', time())
        ];

        $result = $eventModel->update($_POST['event_id'], $eventData);

        if($result){
            echo json_encode(['success' => 'Event updated successfully']);
        }else{
            http_response_code(500);
            echo json_encode(['error' => 'Failed to update event']);
        }
    }
    
    public function createNewEvent(){

        $error = [];

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $error['method_invalid']++;
        }

        // Validation of user
        if (!isset($_SESSION['user_id']) || !$this->checkIfUniRep($_SESSION['user_id'])) {

        }

        //Fetching required data for submission
        //In the future make the db handle it through a join
        $universityId = $this->getUniRepUniversity($_SESSION['user_id']);
        
        // Data from view to model conversion
        $eventData = [
            'university_id' => $universityId,
            'posted_by' => $_SESSION['user_id'],
            'title' => trim($_POST['title']),
            'description' => trim($_POST['description']),
            'event_timestamp' => trim($_POST['event_timestamp']),
            'held_at' => trim($_POST['held_at'])
        ];

        $eventModel = new EventModel();
        $result = $eventModel->createEvent($eventData);

        if ($result) {
            echo json_encode(["success" => "Event created successfully"]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Failed to create event"]);
        }
    }

    public function index(){

        error_log(print_r($this->getEventData(0), true));

        $this->view('event', [
            'title' => 'Event Page',
            'events' => $this->getEventData(0),
            'head' => '',
            'university_events' => $this->getRepEvents()
        ]);
    }

}

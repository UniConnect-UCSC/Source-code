<?php
require_once(__DIR__."/../models/Kuppi.php");
require_once(__DIR__."/../models/KuppiCategory.php");
require_once(__DIR__."/../models/User.php");
require_once(__DIR__."/../models/University.php");
require_once(__DIR__."/../models/KuppiVolunteer.php");
require_once(__DIR__."/../core/functions.php");
require_once(__DIR__ . "/../models/kuppiVolunteerReview.php");
require_once(__DIR__ . "/../models/kuppiParticipation.php");
require_once(__DIR__ . "/../models/kuppiFavorites.php");

class Kuppi extends Controller
{

    private function initiateVolunteer($studentId) {
        $volunteerModel = new KuppiVolunteerModel();
        $existing = $volunteerModel->getByStudentId($studentId);
        if ($existing) {
            return $existing;
        }

        $data = [
            'student_id' => $studentId,
            'conducted_kuppi_count' => 0,
            'avg_rating' => 0,
            'last_updated' => date('Y-m-d H:i:s')
        ];

        return $volunteerModel->insertAndFetch($data);
    }


    private function updateVolunteerKuppiCount($studentId) {
        $volunteerModel = new KuppiVolunteerModel();
        $volunteer = $volunteerModel->getByStudentId($studentId);
        if (!$volunteer) {
            return false;
        }

        return $volunteerModel->update($volunteer->id, [
            'conducted_kuppi_count' => $volunteer->conducted_kuppi_count + 1,
            'last_updated' => date('Y-m-d H:i:s')
        ]);
    }

    private function updateVolunteerRating($studentId) {
        $volunteerModel = new KuppiVolunteerModel();
        $volunteer = $volunteerModel->getByStudentId($studentId);
        if (!$volunteer) {
            return false;
        }

        $reviewModel = new KuppiVolunteerReviewModel();
        $reviews = $reviewModel->getReviewsByVolunteerId($volunteer->id);

        if (empty($reviews)) {
            return false;
        }

        $totalRating = 0;
        $count = 0;
        foreach ($reviews as $review) {
            $totalRating += $review->rating;
            $count++;
        }

        $avgRating = round($totalRating / $count, 2);

        return $volunteerModel->update($volunteer->id, [
            'avg_rating' => $avgRating,
            'last_updated' => date('Y-m-d H:i:s')
        ]);
    }

    private function tabulateVolunteer($context, $studentId) {
        switch($context) {
            case 'volunteered'    : $this->initiateVolunteer($studentId);          break;
            case 'kuppiCompleted' : $this->updateVolunteerKuppiCount($studentId);  break;
            case 'reviewed'       : $this->updateVolunteerRating($studentId);      break;
            default : break;
        }
    }

    public  function create(){
        $user = new User();
        $university_id = $_SESSION['user_universityID'] ?? null;

        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $topic = $_POST['topic'] ?? '';
            $date = $_POST['date'] ?? '';
            $time = $_POST['time'] ?? '';
            $platform = $_POST['platform'] ?? '';
            $category_id = $_POST['category_id'] ?? '';
            $link = $_POST['link'] ?? '';
            
            $kuppiDateTime = $date . ' ' . $time;
            
            $kuppiModel = new KuppiModel();
            
            $data = [
                'topic' => $topic,
                'kuppi_date_time' => $kuppiDateTime,
                'platform' => $platform,
                'image_url' => 'assets/images/ml-banner.jpg', 
                'host_id' => $_SESSION['user_id'],
                'category_id' => $category_id,
                'university_id' => $_SESSION['user_universityID'],
                'status' => 'In Progress',
                'kuppi_url' => $link,
                
            ];
            
            $insertedId = $kuppiModel->insertAndFetch($data);
            
            if ($insertedId) {
                $this->tabulateVolunteer('volunteered', $_SESSION['user_id']);
                echo "Kuppi session created successfully!";
                header('Location: /kuppi');
                exit();
            } else {
                echo "Error creating Kuppi session.";
            }
        } else {
            header('Location: /kuppi');
            exit();
        }
    }
    
    public function request_kuppi(){
        $kuppiModel = new KuppiModel();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $topic = $_POST['topic'];
            $requester_id = $_SESSION['user_id'];
            $category_id = $_POST['category_id'] ?? '';
            
            $data = [
                'topic' => $topic,
                'category_id' => $category_id,
                'requester_id' => $requester_id,    
                'host_id' => null, 
                'university_id' => $_SESSION['user_universityID'],
                'status' => 'Requested' 
            ];
            
            $insertedId = $kuppiModel->insertAndFetch($data);
            
            if ($insertedId) {
                echo "Kuppi request submitted successfully!";
                header('Location: /kuppi');
                exit();
            } else {
                echo "Error submitting Kuppi request.";
            }
        } else {
            header('Location: /kuppi');
            exit();
        }
    }
    
    public function edit_kuppi($id){
        $kuppiModel = new KuppiModel();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $topic = $_POST['topic'] ?? '';
            $date = $_POST['date'] ?? '';
            $time = $_POST['time'] ?? '';
            $platform = $_POST['platform'] ?? '';
            $category_id = $_POST['category_id'] ?? '';
            $link = $_POST['link'] ?? '';
            
            $kuppiDateTime = $date . ' ' . $time;
            
            $data = [
                'topic' => $topic,
                'kuppi_date_time' => $kuppiDateTime,
                'platform' => $platform,
                'kuppi_url' => $link
            ];
            
            if (!empty($category_id)) {
                $data['category_id'] = (int)$category_id;
            }
            
            $updated = $kuppiModel->update($id, $data);
            
            if ($updated) {
                header('Location: /kuppi');
                exit();
            } else {
                echo "Error updating Kuppi session.";
            }
        } else {
            header('Location: /kuppi');
            exit();
        }
    }

    public function delete_kuppi($id){
        $kuppiModel = new KuppiModel();
        $deleted = $kuppiModel->delete($id);
        
        if ($deleted) {
            header('Location: /kuppi');
            exit();
        } else {
            echo "Error deleting Kuppi session.";
        }
    }

    public function approve_request($id){
        $kuppiModel = new KuppiModel();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $platform = $_POST['platform'] ?? '';
            $date_time = $_POST['date'].' '.$_POST['time'] ?? '';
            $data = [
                'status' => 'In Progress',
                'university_id' => $_SESSION['user_universityID'],
                'platform' => $platform,
                'host_id' => $_SESSION['user_id'],
                'kuppi_date_time' => $date_time
            ];
        }
        $updated = $kuppiModel->update($id, $data);
        
        if ($updated) {
            header('Location: /kuppi');
            exit();
        } else {
            echo "Error approving Kuppi request.";
        }
    }

    public function reportKuppi(){
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }

        try {
            $data = parseRequestData();
            $id = $data['id'] ?? null;

            if (!$id) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Missing or invalid kuppi id']);
                exit;
            }

            $kuppiModel = new KuppiModel();

            $existing = $kuppiModel->first(['id' => $id]);
            if (!$existing) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Kuppi not found']);
                exit;
            }

            $kuppiModel->update($id, ['is_reported' => true]);

            $updated = $kuppiModel->first(['id' => $id]);
            if ($updated && (bool)$updated->is_reported === true) {
                echo json_encode(['success' => true, 'message' => 'Kuppi reported']);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to update report flag']);
            }
        } catch (Throwable $e) {
            error_log('reportKuppi error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Server error']);
        }
    }
    
    public function editKuppiRequest(){
        $kuppiModel = new KuppiModel();
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $id = $_POST['id'] ?? '';
            $topic = $_POST['topic'] ?? '';
            $category_id = $_POST['category_id'] ?? '';
            
            $data = [
                'topic' => $topic,
                'category_id' => $category_id
            ];
            
            $updated = $kuppiModel->update($id, $data);
            
            if($updated){
                header('Location: /kuppi/myKuppis');
                exit();
            } else {
                echo "Error updating Kuppi request.";
            }
        } else {
            header('Location: /kuppi/myKuppis');
            exit();
        }
        
        
        
    }

    public function changeStatus() {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            return;
        }

        try {
            $data = parseRequestData();
            $kuppiId   = $data['id']     ?? null;
            $currentStatus = $data['currentStatus'] ?? null;
            $newStatus = $data['newStatus'] ?? null;

            if (!$kuppiId || !$newStatus || !$currentStatus) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Missing id or status']);
                return;
            }

            if($currentStatus == 'Completed') {
                echo json_encode(['success' => false ,'message' => 'Cannot change the status if kuppi was completed']);
                return ;
            }

            $kuppiModel = new KuppiModel();
            $updated = $kuppiModel->update($kuppiId, ['status' => $newStatus]);

            if ($updated) {
                if($newStatus == 'Completed') {
                    $kuppiModel2 = new KuppiModel();
                    $kuppiData = $kuppiModel2->getKuppiById($kuppiId);
                    if ($kuppiData && isset($kuppiData->host_id)) {
                        $this->tabulateVolunteer('kuppiCompleted', $kuppiData->host_id);
                    }
                }
                echo json_encode([
                    'success'   => true,
                    'newStatus' => $newStatus,
                    'message'   => 'Changed status successfully',
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to update status']);
            }
        } catch (Throwable $e) {
            error_log('changeStatus error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Server error']);
        }
    }

    public function review() {
        header('Content-Type: application/json');


        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            return;
        }

        try {

            $data = parseRequestData();
            $kuppiId   = $data['kuppi_id']  ?? null;
            $host_id   = $data['host_id']   ?? null;
            $rating    = $data['rating']    ?? null;
            $comment   = $data['comment']   ?? null;

            if (!$kuppiId || !$rating ) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Missing id or rating']);
                return;
            }

            if ($host_id === $_SESSION['user_id']) {
                echo json_encode(['success' => false, 'message' => 'Host cant review their own kuppi']);
                return ;
            }

            $kuppiReviewModel = new KuppiVolunteerReviewModel ();

            // Look up the volunteer record by host_id (student_id) to get the volunteer's PK
            $volunteerModel = new KuppiVolunteerModel();
            $volunteer = $volunteerModel->getByStudentId($host_id);
            if (!$volunteer) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Volunteer record not found for this host']);
                return;
            }

            $data = [
                'volunteer_id' => $volunteer->id,
                'reviewer_id'  => $_SESSION['user_id'],
                'review_text'  => $comment,
                'rating'       => $rating,
                'kuppi_id'     => $kuppiId,
            ];

            $insertedId = $kuppiReviewModel->insertAndFetch($data);
            if ($insertedId) {
                // Recalculate the volunteer's avg_rating after new review
                $this->tabulateVolunteer('reviewed', $host_id);
                echo json_encode([
                    'success'   => true,
                    'message'   => 'Review inserted to DB successfully',
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed t0 insert review']);
            }
        } catch (Throwable $e) {
            error_log('review error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
        }        
    }

    public function getReviews() {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            return;
        }

        try {
            $data = parseRequestData();
            $kuppiId = $data['kuppi_id'] ?? null;

            if (!$kuppiId) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Missing kuppi_id']);
                return;
            }

            $reviewModel = new KuppiVolunteerReviewModel();
            $reviews = $reviewModel->getReviewsByKuppiId($kuppiId);

            echo json_encode([
                'success' => true,
                'reviews' => $reviews
            ]);
        } catch (Throwable $e) {
            error_log('getReviews error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
        }
    }
    
    private function toggleKuppiFavourite($kuppiId, $userId, $currentStatus) {
        $favoritesModel = new KuppiFavoriteModel();

        // Handle true/false, "true"/"false", 1/0 safely
        $isFavorite = filter_var($currentStatus, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        if ($isFavorite === null) {
            $isFavorite = ((int)$currentStatus === 1);
        }

        if ($isFavorite) {
            $result = $favoritesModel->unmarkFavorite($kuppiId, $userId);
            return [
                'newStatus' => false,
                'result' => $result // true or null
            ];
        }

        $result = $favoritesModel->markFavorite($kuppiId, $userId);
        return [
            'newStatus' => true,
            'result' => $result // true or null
        ];
    }

    private function toggleKuppiParticipation ($kuppiId, $userId , $currentStatus) {

        $participationModel = new KuppiParticipationModel();
        $response = [];
        
        if($currentStatus){
            $response['participantCount'] = $participationModel->removeParticipation($kuppiId, $userId);
            $response['newStatus'] = false;
        }else{
            $response['participantCount'] = $participationModel->addParticipation($kuppiId, $userId);
            $response['newStatus'] = true;
        }

        return $response;
    }

    public function toggle(){
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $data = parseRequestData();
            header('Content-Type: application/json');

            error_log("Toggle request data: " . print_r($data, true));

            switch($data['action']){
                case 'favorite':
                    $response = $this->toggleKuppiFavourite($data['kuppi_id'], $_SESSION['user_id'], $data['current_status']);
                    echo json_encode([
                        'newStatus' => $response['newStatus'],
                        'result' => $response['result']
                    ]);
                    break;

                case 'participate':
                    $response = $this->toggleKuppiParticipation($data['kuppi_id'], $_SESSION['user_id'], $data['current_status']);
                    echo json_encode(['newStatus' => $response['newStatus'], 'participantCount' => $response['participantCount']]);
                    break;

                default:
                    http_response_code(400);
                    echo json_encode(['error' => 'Invalid action']);
                    exit;
            }
            
        }
    }

    private function fetchKuppis($offset, $limit){
        
        
        $kuppiModel = new KuppiModel();
        $kuppies = $kuppiModel->getKuppi($offset, $limit) ?? [];

        foreach ($kuppies as $item){
            $item->host_name = $item->host_f_name." ".$item->host_l_name;
            $item->requester_name = $item->requester_f_name." ".$item->requester_l_name;
        }

        return $kuppies;
    }




    private function fetchMyHostKuppies($offset, $limit ,  $status = null){
        
    
        $kuppiModel = new KuppiModel();
        $myKuppies = $kuppiModel->getMyHosts($offset ,$limit , $status) ?? [];

        foreach ($myKuppies as $item){
            $item->host_name = $item->host_f_name." ".$item->host_l_name;

            if(isset($item->requester_f_name) || !empty($item->requester_f_name)){
                $item->requester_name = $item->requester_f_name." ".$item->requester_l_name;
            }
            $item->university = $item->host_university;
            if (!isset($item->image_url) || empty($item->image_url)) {
                 $item->image_url = 'assets/images/ml-banner.jpg';
            }
        }
        return $myKuppies;
    }

    private function fetchMyRequests($offset , $limit , $status = null){
        $kuppiModel = new KuppiModel();
        $myRequests = $kuppiModel->getMyRequests($offset, $limit ,$status) ?? [];

        foreach ($myRequests as $item){
            $item->requester_name = $item->requester_f_name." ".$item->requester_l_name;

            if(isset($item->host_f_name) || !empty($item->host_f_name)){
                $item->host_name = $item->host_f_name." ".$item->host_l_name;
            }
            $item->university = $item->host_university ?? $item->requester_university;
            if (!isset($item->image_url) || empty($item->image_url)) {
                 $item->image_url = 'assets/images/ml-banner.jpg';
            }
        }
        return $myRequests;
    }

    private function fetchMyParticipations($offset, $limit, $status = null){
        $kuppiModel = new KuppiModel();
        $participations = $kuppiModel->getMyParticipations($offset, $limit, $status) ?? [];

        foreach ($participations as $item){
            $item->host_name = $item->host_f_name . ' ' . $item->host_l_name;

            if (isset($item->requester_f_name) && !empty($item->requester_f_name)){
                $item->requester_name = $item->requester_f_name . ' ' . $item->requester_l_name;
            }
            $item->university = $item->university ?? '';
            if (!isset($item->image_url) || empty($item->image_url)) {
                $item->image_url = 'assets/images/ml-banner.jpg';
            }
        }
        return $participations;
    }

    private function fetchKuppiRequests($offset, $limit){

        
        $kuppiModel = new KuppiModel();
        $kuppiRequests = $kuppiModel->getKuppiRequests($offset, $limit );

        foreach ($kuppiRequests as $item) {
            $item->requester_name = $item->requester_f_name.' '.$item->requester_l_name;

            if (!isset($item->image_url) || empty($item->image_url)) {
                 $item->image_url = 'assets/images/ml-banner.jpg';
            }
        }
        return $kuppiRequests;
    }
    


    public function scrollable(){
        $data = parseRequestData();
        header('Content-Type: application/json');
    
        switch ($data['scrollIdentifier']) {
            case 'getAllKuppies':
                $response = $this->fetchKuppis($data['offset'], $data['limit']);
                echo json_encode($response);
                break;
            case 'getMyHostKuppies':
                $status = $data['context']['status'] ?? null;
                $response = $this->fetchMyHostKuppies($data['offset'], $data['limit'], $status);
                echo json_encode($response);
                break;
            case 'getKuppiRequests':
                $response = $this->fetchKuppiRequests($data['offset'], $data['limit'] );
                echo json_encode($response);
                break;
            case 'getMyRequests':
                $status = $data['context']['status'] ?? null;
                $response = $this->fetchMyRequests($data['offset'], $data['limit'] , $status);
                echo json_encode($response);
                break;
            case 'getMyParticipations':
                $status = $data['context']['status'] ?? null;
                $response = $this->fetchMyParticipations($data['offset'], $data['limit'], $status);
                echo json_encode($response);
                break;
            default:
                # code...
                break;
        }
    }


    public function index(){
        $KuppiCategorymodel = new KuppiCategoryModel();
        $KuppiCategories = $KuppiCategorymodel->getAllKuppiCategories();
        $this->view('kuppi', [
            'title' => 'UniConnect',
            'head' => '
            <link rel="stylesheet" href="/assets/css/pages/kuppi.css">
            <link rel="stylesheet" href="/assets/css/pages/home.css">
            <link rel="stylesheet" href="/assets/css/components/navbar.css">
            <link rel="stylesheet" href="/assets/css/components/navPanel.css">
            <link rel="stylesheet" href="/assets/css/components/feed.css">
            <link rel="stylesheet" href="/assets/css/components/widgetPanel.css">
            <link rel="stylesheet" href="/assets/css/components/kuppi/kuppiPost.css">
            <link rel="stylesheet" href="/assets/css/components/kuppi/validation.css">
            <link rel="stylesheet" href="/assets/css/components/eventsWidget.css">
            ',
            'kuppiCategories' => $KuppiCategories
        ]);
    }

}
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
require_once(__DIR__ . "/KuppiNotificationHandler.php");
require_once(__DIR__ . "/../models/userKuppiFavoriteCategories.php");
require_once(__DIR__ . "/../models/kuppiReport.php");

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
            $topic = trim($_POST['topic'] ?? '');
            $date = trim($_POST['date'] ?? '');
            $time = trim($_POST['time'] ?? '');
            $platform = trim($_POST['platform'] ?? '');
            $category_id = $_POST['category_id'] ?? '';
            $link = trim($_POST['link'] ?? '');

            $uploadedImageUrl = uploadImageToCloudinary($_FILES['kuppi_image'] ?? null, 'uniconnect_kuppi');
            $imageUrl = $uploadedImageUrl ?: 'assets/images/ml-banner.jpg';
            
            $kuppiDateTime = $date . ' ' . $time;
            
            $kuppiModel = new KuppiModel();
            
            $data = [
                'topic' => $topic,
                'kuppi_date_time' => $kuppiDateTime,
                'platform' => $platform,
                'image_url' => $imageUrl,
                'host_id' => $_SESSION['user_id'],
                'category_id' => $category_id,
                'university_id' => $_SESSION['user_universityID'],
                'status' => 'Upcoming ',
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
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /kuppi');
            exit();
        }

        $kuppiModel = new KuppiModel();
        $kuppi = $kuppiModel->getKuppiById($id);

        if (!$kuppi) {
            echo "Kuppi session not found.";
            return;
        }

        if ((int)($kuppi->host_id ?? 0) !== (int)($_SESSION['user_id'] ?? 0)) {
            echo "You are not allowed to edit this session.";
            return;
        }

        if (($kuppi->status ?? '') === 'Completed') {
            echo "Can't edit already ended session";
            return;
        }

        $topic = trim($_POST['topic'] ?? '');
        $date = trim($_POST['date'] ?? '');
        $time = trim($_POST['time'] ?? '');
        $platform = trim($_POST['platform'] ?? '');
        $category_id = $_POST['category_id'] ?? '';
        $link = trim($_POST['link'] ?? '');

        if ($topic === '' || $date === '' || $time === '' || $platform === '') {
            http_response_code(422);
            echo "Topic, date, time and platform are required.";
            return;
        }

        $kuppiDateTime = $date . ' ' . $time;

        $data = [
            'topic' => $topic,
            'kuppi_date_time' => $kuppiDateTime,
            'platform' => $platform,
            'kuppi_url' => $link
        ];

        $uploadedImageUrl = uploadImageToCloudinary($_FILES['kuppi_image'] ?? null, 'uniconnect_kuppi');
        if (!empty($uploadedImageUrl)) {
            $data['image_url'] = $uploadedImageUrl;
        }

        if (!empty($category_id)) {
            $data['category_id'] = (int)$category_id;
        }

        $updated = $kuppiModel->update($id, $data);

        if ($updated) {
            header('Location: /kuppi');
            exit();
        }

        echo "Error updating Kuppi session.";
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
                'status' => 'Upcoming',
                'university_id' => $_SESSION['user_universityID'],
                'platform' => $platform,
                'host_id' => $_SESSION['user_id'],
                'kuppi_date_time' => $date_time
            ];
        }
        $updated = $kuppiModel->update($id, $data);
        $kuppi = $kuppiModel->getKuppiById($id);
        if ($updated) {
            $this->tabulateVolunteer('volunteered', $_SESSION['user_id']);
            
            $this->notify(
                'volunteered' ,
                $kuppi,
                null
            );
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
            $id = $data['kuppi_id'];

            if (!$id) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Missing or invalid kuppi id']);
                exit;
            }

            $kuppiModel = new KuppiModel();
            $kuppiReportModel = new KuppiReportModel();



            $existing = $kuppiModel->first(['id' => $id]);
            if (!$existing) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Kuppi not found']);
                exit;
            }
            $data = [
                'is_reported' => true,
            ];
            $rData = [
                'kuppi_id' => $id,
                'reporter_id' => $_SESSION['user_id'],
                'created_at' => date('Y-m-d H:i:s')
            ];

            $kuppiModel->update($id, $data);
            $response = $kuppiReportModel->insertAndFetch($rData) ;

            $updated = $kuppiModel->first(['id' => $id]);
            
            if ($updated && (bool)$updated->is_reported === true && $response) {
                echo json_encode(['success' => true, 'message' => 'Kuppi reported', 'response' => $response]);
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

    public function notify($context, $kuppi, $data) {
        switch ($context) {
            case 'volunteered':
                return $this->notifyVolunteered($context, $kuppi);
            case 'statusChanged':
                return $this->notifyStatusChange($context, $kuppi, $data);

            default:
                throw new InvalidArgumentException('Invalid notification context');
        }
    }

    private function notifyVolunteered($context, $kuppi ) {
        $userModel = new User;
        $topic = $kuppi->topic ;
        $hostId = $kuppi->host_id;
        $hostName = $userModel->getUserNameById($hostId);

        $baseMeta = [
            'context' => $context,
        ];

        $requesterPayload = [
            'type' => 'kuppi_volunteered_request',
            'title' => '"'. $hostName .'"  Volunteered for your Request!',
            'message' => '"'. $hostName .'"  has volunteered to host your requested kuppi "' . $topic . '".',
            'metadata' => $baseMeta,
        ];

        $hostPayload = [
            'type' => 'kuppi_volunteered_host',
            'title' => 'Successfully Volunteered!',
            'message' => 'You have successfully volunteered to host the kuppi "' . $topic . '".',
            'metadata' => $baseMeta,
        ];

        $handler = new KuppiNotificationHandler($kuppi);
        $details = [];

        if (!empty($kuppi->requester_id)) {
            $details['requester'] = $handler->notifyRequester(
                $requesterPayload['type'],
                $requesterPayload['title'],
                $requesterPayload['message'],
                $requesterPayload['metadata']
            );
        }

        if (!empty($kuppi->host_id)) {
            $details['host'] = $handler->notifyHost(
                $hostPayload['type'],
                $hostPayload['title'],
                $hostPayload['message'],
                $hostPayload['metadata']
            );
        }

        return [
            'success' => true,
            'details' => $details,
        ];
    }

    private function notifyStatusChange($context, $kuppi, $newStatus): array {

        $topic = $kuppi->topic ?? 'Kuppi';
        $baseMeta = [
            'context' => $context,
            'status' => $newStatus,
        ];
        
        if (!empty($kuppi->requester_id)) {
            $requesterPayload = [
                'type' => 'kuppi_request_status_changed',
                'title' => 'Kuppi Request Status Updated',
                'message' => 'Your requested kuppi "' . $topic . '" changed status to ' . $newStatus . '.',
                'metadata' => $baseMeta,
            ];
        }

        $hostPayload = [
            'type' => 'kuppi_host_status_changed',
            'title' => 'Kuppi Hosting Status Updated',
            'message' => 'Your hosted kuppi "' . $topic . '" changed status to ' . $newStatus . '.',
            'metadata' => $baseMeta,
        ];

        $participantsPayload = [
            'type' => 'kuppi_participant_status_changed',
            'title' => 'Kuppi You Joined Has Updated',
            'message' => 'A kuppi you joined, "' . $topic . '", changed status to ' . $newStatus . '.',
            'metadata' => $baseMeta,
        ];

        $favoritesPayload = [
            'type' => 'kuppi_favorite_status_changed',
            'title' => 'Favorite Kuppi Status Updated',
            'message' => 'A kuppi in your favorites, "' . $topic . '", changed status to ' . $newStatus . '.',
            'metadata' => $baseMeta,
        ];

        $handler = new KuppiNotificationHandler($kuppi);

        $details = [];

        if (!empty($kuppi->requester_id)) {
            $details['requester'] = $handler->notifyRequester(
                $requesterPayload['type'],
                $requesterPayload['title'],
                $requesterPayload['message'],
                $requesterPayload['metadata']
            );
        }

        $details['host'] = $handler->notifyHost(
            $hostPayload['type'],
            $hostPayload['title'],
            $hostPayload['message'],
            $hostPayload['metadata']
        );
        $details['participants'] = $handler->notifyParticipants(
            $participantsPayload['type'],
            $participantsPayload['title'],
            $participantsPayload['message'],
            $participantsPayload['metadata']
        );
        $details['favorites'] = $handler->notifyFavorites(
            $favoritesPayload['type'],
            $favoritesPayload['title'],
            $favoritesPayload['message'],
            $favoritesPayload['metadata']
        );

        return [
            'success' => true,
            'message' => 'Status-change notifications queued',
            'context' => $context,
            'kuppi_id' => (string)$kuppi->id,
            'newStatus' => $newStatus,
            'details' => $details,
        ];
    }

    public function editKuppiRequest(){
        $kuppiModel = new KuppiModel();
        if($_SERVER['REQUEST_METHOD'] !== 'POST'){
            header('Location: /kuppi/myKuppis');
            exit();
        }

        $id = $_POST['id'] ?? '';
        $topic = trim($_POST['topic'] ?? '');
        $category_id = $_POST['category_id'] ?? '';

        if (empty($id)) {
            http_response_code(400);
            echo "Missing Kuppi request id.";
            return;
        }

        $kuppi = $kuppiModel->getKuppiById($id);
        if (!$kuppi) {
            http_response_code(404);
            echo "Kuppi request not found.";
            return;
        }

        if ((int)($kuppi->requester_id ?? 0) !== (int)($_SESSION['user_id'] ?? 0)) {
            http_response_code(403);
            echo "You are not allowed to edit this request.";
            return;
        }

        if (($kuppi->status ?? '') === 'Completed') {
            http_response_code(400);
            echo "Completed requests cannot be edited.";
            return;
        }

        if ($topic === '' || empty($category_id)) {
            http_response_code(422);
            echo "Topic and category are required.";
            return;
        }

        $data = [
            'topic' => $topic,
            'category_id' => (int)$category_id
        ];

        $updated = $kuppiModel->update($id, $data);

        if($updated){
            header('Location: /kuppi/myKuppis');
            exit();
        }

        echo "Error updating Kuppi request.";
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

            $kuppiId = $data['id'] ;
            $currentStatus = $data['currentStatus'];
            $newStatus = $data['newStatus'] ;

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
                $notification = null;
                $kuppiData = $kuppiModel->getKuppiById($kuppiId);
                try {

                    if ($kuppiData) {
                        $notification = $this->notify('statusChanged', $kuppiData, (string)$newStatus);
                    }
                } catch (Throwable $e) {
                    error_log('changeStatus notifyStatusChange error: ' . $e->getMessage());
                }

                if($newStatus == 'Completed') {
                    if ($kuppiData && isset($kuppiData->host_id)) {
                        $this->tabulateVolunteer('kuppiCompleted', $kuppiData->host_id);
                    }
                }
                echo json_encode([
                    'success'   => true,
                    'newStatus' => $newStatus,
                    'message'   => 'Changed status successfully',
                    'notification' => $notification,
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

    public function removeFavoriteCategory() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            return;
        }

        try {
            $data = parseRequestData();
            $categoryId = $data['categoryId'] ?? null;

            if (!$categoryId) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Missing categoryId']);
                return;
            }

            $userFavCategoryModel = new UserKuppiFavoriteCategoriesModel();
            
            $removed = $userFavCategoryModel->removeUserFavoriteCategory($_SESSION['user_id'], $categoryId);
            
            if ($removed) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Category removed from favorites'
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to remove category']);
            }
        } catch (Throwable $e) {
            error_log('removeFavoriteCategory error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
        }
    }
    
    private function toggleKuppiFavourite($kuppiId, $userId, $currentStatus) {
        $favoritesModel = new KuppiFavoriteModel();
        $userFavCategoryModel = new UserKuppiFavoriteCategoriesModel();
        $kuppiModel = new KuppiModel();


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
        
        $kuppi = $kuppiModel->getKuppiById($kuppiId);
        if ($kuppi && !empty($kuppi->category_id)) {
            $isAlreadyMapped = $userFavCategoryModel->isAlreadyMapped($userId, $kuppi->category_id);
            if (!$isAlreadyMapped) {
                $userFavCategoryModel->createUserFavoriteCategory($userId, $kuppi->category_id);
            }
        }

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
    private function fetchKuppis($offset, $limit, $categories = [], $searchTerm = ''){
        
        
        $kuppiModel = new KuppiModel();
        $kuppies = $kuppiModel->getKuppi($offset, $limit, $categories, $searchTerm) ?? [];

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
    
    private function fetchMyFavorites($offset, $limit, $status = null){

        $kuppiModel = new KuppiModel();
        $favorites = $kuppiModel->getMyFavorites($offset, $limit, $status) ?? [];

        foreach ($favorites as $item){
            $item->host_name = $item->host_f_name . ' ' . $item->host_l_name;

            if (isset($item->requester_f_name) && !empty($item->requester_f_name)){
                $item->requester_name = $item->requester_f_name . ' ' . $item->requester_l_name;
            }

            $item->university = $item->university ?? '';

            if (!isset($item->image_url) || empty($item->image_url)) {
                $item->image_url = 'assets/images/ml-banner.jpg';
            }
        }

        return $favorites;
    }

    private function fetchMyFavoriteCategories($offset, $limit) {
        $fvCategoryModel = new UserKuppiFavoriteCategoriesModel();
        $favorites = $fvCategoryModel->getUserFavoriteCategories($offset, $limit);

        foreach ($favorites as $item) {
            $item->category = $item->category_name;
        }

        return $favorites;
    }

    private function fetchKuppiCategories($offset, $limit) {
        $kuppiCategoryModel = new KuppiCategoryModel();
        $kuppiCategories = $kuppiCategoryModel->getKuppiCategories($offset ,$limit);

        return $kuppiCategories ?? [];
    }

    private function fetchForYouKuppiSessions($offset ,$limit) {
        $fvKuppiCategoriesModel = new UserKuppiFavoriteCategoriesModel();
        $categories = $fvKuppiCategoriesModel->getAllKuppiFavoriteCategories() ?? [];
       // $categories = (array)$categories;
        $categories = array_values(array_unique(array_filter(array_map(
    static fn($c) => (int) (is_object($c) ? ($c->category_id ?? $c->id ?? 0) : (is_array($c) ? ($c['category_id'] ?? $c['id'] ?? 0) : $c)),
    (array)$categories
), static fn($id) => $id > 0)));
        $kuppiModel = new KuppiModel();

        return $kuppiModel->getKuppi($offset, $limit, $categories);
    }

    private function fetchReviewedKuppiSessions($offset ,$limit) {
        $reviewModel = new KuppiVolunteerReviewModel();
        $reviewedKuppiSessions = $reviewModel->getReviewsWithKuppiSessions($_SESSION['user_id'] ,$offset ,$limit); 

        return $reviewedKuppiSessions;
    }

    private function fetchReportedKuppiSessions($offset ,$limit) {
        $reportedKuppiModdel = new KuppiReportModel();
        $reportedKuppiSessions = $reportedKuppiModdel->getMyReportedKuppiSessions($_SESSION['user_id'] ,$offset ,$limit);

        return $reportedKuppiSessions;
    }

    public function updateReview() {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            return;
        }

        try {
            $payload = parseRequestData();
            $reviewId = trim((string)($payload['review_id'] ?? ''));
            $hostId = trim((string)($payload['host_id'] ?? ''));
            $rating = isset($payload['rating']) ? (int)$payload['rating'] : 0;
            $comment = trim((string)($payload['comment'] ?? ''));

            if ($reviewId === '' || $rating < 1 || $rating > 5) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid review id or rating']);
                return;
            }

            $reviewModel = new KuppiVolunteerReviewModel();
            $updated = $reviewModel->updateReviewById($reviewId, (string)$_SESSION['user_id'], $rating, $comment);

            if (!$updated) {
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'Review not found or access denied']);
                return;
            }

            if ($hostId !== '') {
                $this->tabulateVolunteer('reviewed', $hostId);
            }

            echo json_encode([
                'success' => true,
                'message' => 'Review updated successfully'
            ]);
        } catch (Throwable $e) {
            error_log('updateReview error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Server error']);
        }
    }

    public function deleteReview() {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            return;
        }

        try {
            $payload = parseRequestData();
            $reviewId = trim((string)($payload['review_id'] ?? ''));
            $hostId = trim((string)($payload['host_id'] ?? ''));

            if ($reviewId === '') {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid review id']);
                return;
            }

            $reviewModel = new KuppiVolunteerReviewModel();
            $deleted = $reviewModel->deleteReviewById($reviewId, (string)$_SESSION['user_id']);

            if (!$deleted) {
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'Review not found or access denied']);
                return;
            }

            if ($hostId !== '') {
                $this->tabulateVolunteer('reviewed', $hostId);
            }

            echo json_encode([
                'success' => true,
                'message' => 'Review deleted successfully'
            ]);
        } catch (Throwable $e) {
            error_log('deleteReview error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Server error']);
        }
    }

    public function scrollable(){
        $data = parseRequestData();
        header('Content-Type: application/json');
    
        switch ($data['scrollIdentifier']) {
            case 'getAllKuppies':
                $categories = $data['context']['categories'] ?? [];
                $searchTerm = trim((string)($data['context']['searchTerm'] ?? ''));
                $response = $this->fetchKuppis($data['offset'], $data['limit'], $categories, $searchTerm);
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
            case 'getMyFavorites':
                $status = $data['context']['status'] ?? null;
                $response = $this->fetchMyFavorites($data['offset'], $data['limit'], $status);
                echo json_encode($response);
                break;
            case 'getMyFavoriteCategories':
                $response = $this->fetchMyFavoriteCategories($data['offset'], $data['limit']);
                echo json_encode($response);
                break;
            case 'getKuppiCategories':
                $response = $this->fetchKuppiCategories($data['offset'], $data['limit']);
                echo json_encode($response);
                break;
            case 'getForYouKuppies':
                $response = $this->fetchForYouKuppiSessions($data['offset'], $data['limit']);
                echo json_encode($response);
                break;
            case 'getReviewedKuppiSessions':
                $response = $this->fetchReviewedKuppiSessions($data['offset'], $data['limit']);
                echo json_encode($response);
                break;
            case 'getReportedKuppiSessions':
                $response = $this->fetchReportedKuppiSessions($data['offset'] ,$data['limit']);
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